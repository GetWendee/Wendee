<?php

namespace App\Services\AI;

use App\Models\Client;
use App\Models\ClientAnalysis;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Analyse du profil investisseur pour une personne morale (société).
 * Service entièrement séparé de ProfilInvestisseurAnalysisService
 * (personne physique) : pas de questionnaire scoré côté société, des
 * champs déclaratifs directs. Voir claude/kyc-personne-morale.md.
 */
class ProfilInvestisseurAnalysisServiceMorale
{
    public const PROMPT_VERSION = 'profil-investisseur-morale-v1';

    public function analyze(Client $client): ClientAnalysis
    {
        $client->loadMissing('profilInvestisseurMorale');

        if (! $client->profilInvestisseurMorale) {
            throw new RuntimeException('Aucun profil investisseur société disponible pour ce client.');
        }

        $input = $this->buildInput($client);

        $analysis = ClientAnalysis::create([
            'client_id' => $client->id,
            'type' => 'profil_investisseur_morale',
            'status' => 'processing',
            'input_version' => '1',
            'prompt_version' => self::PROMPT_VERSION,
            'model' => config('services.openai.model', 'gpt-4.1'),
            'input_data' => $input,
            'started_at' => now(),
        ]);

        try {
            $response = Http::withToken(config('services.openai.key'))
                ->acceptJson()
                ->timeout(90)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => config('services.openai.model', 'gpt-4.1'),
                    'temperature' => 0.3,
                    'response_format' => ['type' => 'json_object'],
                    'messages' => [
                        ['role' => 'system', 'content' => $this->systemPrompt()],
                        ['role' => 'user', 'content' => json_encode($input, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)],
                    ],
                ]);

            if (! $response->successful()) {
                throw new RuntimeException('Erreur OpenAI HTTP ' . $response->status() . ' : ' . $response->body());
            }

            $payload = $response->json();
            $raw = data_get($payload, 'choices.0.message.content');

            if (! is_string($raw) || trim($raw) === '') {
                throw new RuntimeException('Réponse OpenAI vide ou invalide.');
            }

            $result = json_decode($raw, true);

            if (! is_array($result)) {
                throw new RuntimeException('La réponse OpenAI ne contient pas un JSON valide.');
            }

            $this->validateResult($result);

            $analysis->update([
                'status' => 'completed',
                'result_json' => $result,
                'raw_response' => $raw,
                'prompt_tokens' => data_get($payload, 'usage.prompt_tokens'),
                'completion_tokens' => data_get($payload, 'usage.completion_tokens'),
                'total_tokens' => data_get($payload, 'usage.total_tokens'),
                'completed_at' => now(),
                'error_message' => null,
            ]);

            return $analysis->fresh();
        } catch (\Throwable $e) {
            $analysis->update([
                'status' => 'failed',
                'completed_at' => now(),
                'error_message' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    private function buildInput(Client $client): array
    {
        $profil = $client->profilInvestisseurMorale;

        $attributes = collect($profil->getAttributes())
            ->except(['id', 'client_id', 'created_at', 'updated_at', 'signe_le', 'accepte_cgu'])
            ->all();

        return [
            'societe' => [
                'id' => $client->id,
                'raison_sociale' => $client->raison_sociale,
            ],

            'profil_investisseur' => $attributes,
        ];
    }

    private function validateResult(array $result): void
    {
        foreach (['points_forts', 'points_attention'] as $key) {
            if (! isset($result[$key]) || ! is_array($result[$key]) || count($result[$key]) !== 4) {
                throw new RuntimeException("{$key} doit contenir exactement 4 éléments.");
            }

            foreach ($result[$key] as $item) {
                if (! is_array($item) || empty($item['titre']) || empty($item['analyse'])) {
                    throw new RuntimeException("Structure invalide dans {$key}.");
                }
            }
        }
    }

    private function systemPrompt(): string
    {
        return <<<'PROMPT'
Tu es un conseiller en gestion de patrimoine expérimenté.

Tu réalises une analyse du profil investisseur d'une société (personne morale) destinée à un professionnel du conseil patrimonial.

============================================================
RÈGLE ABSOLUE : NE RIEN INVENTER
============================================================

Tu analyses exclusivement les informations transmises. Une information absente est INCONNUE, jamais nulle ou négative.

============================================================
PÉRIMÈTRE D'ANALYSE
============================================================

Analyse notamment :
- profil de risque déclaré (échelle 1 à 7) ;
- objectifs d'investissement et horizons associés ;
- intérêt pour les critères extra-financiers (ESG, taxonomie, SFDR, PAI) ;
- acceptation d'une performance potentiellement moindre au profit de critères ESG ;
- cohérence entre le profil ESG de l'investissement envisagé et celui du patrimoine global de la société ;
- indicateurs environnementaux et sociaux suivis ;
- cohérence entre objectifs, horizon et profil de risque déclaré.

============================================================
POINTS FORTS
============================================================

Produis exactement 4 points forts : éléments objectivement favorables dans la cohérence du profil (objectifs clairs, horizon cohérent, démarche ESG structurée, etc.).

============================================================
POINTS D'ATTENTION
============================================================

Produis exactement 4 points d'attention : incohérences, limites, informations manquantes qui limitent réellement l'analyse.

============================================================
INTERDICTIONS
============================================================

Aucune recommandation de produit. Aucune allocation chiffrée. Aucune projection. Aucun conseil explicite.

============================================================
FORMAT
============================================================

Retourne exclusivement un JSON valide :

{
  "points_forts": [
    { "titre": "Titre court", "analyse": "Phrase analytique." }
  ],
  "points_attention": [
    { "titre": "Titre court", "analyse": "Phrase analytique." }
  ]
}

Contraintes : exactement 4 points forts, exactement 4 points d'attention, titre de 6 mots maximum, aucun texte avant ou après le JSON.
PROMPT;
    }
}
