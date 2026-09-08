<?php

namespace App\Services\AI;

use App\Models\Client;
use App\Models\ClientAnalysis;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Analyse patrimoniale pour une personne morale (société). Service
 * entièrement séparé de PatrimoineAnalysisService (personne physique),
 * même si la table patrimoine_elements est partagée entre les deux
 * types de client. Voir claude/kyc-personne-morale.md.
 */
class PatrimoineAnalysisServiceMorale
{
    public const PROMPT_VERSION = 'patrimoine-morale-v1';

    public function analyze(Client $client): ClientAnalysis
    {
        $client->loadMissing(['patrimoineElements']);

        $input = $this->buildInput($client);

        $analysis = ClientAnalysis::create([
            'client_id' => $client->id,
            'type' => 'patrimoine_morale',
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
        $elements = $client->patrimoineElements;

        $grouped = [];

        foreach (['actif_financier', 'actif_non_financier', 'passif', 'revenu', 'charge'] as $categorie) {
            $grouped[$categorie] = $elements
                ->where('categorie', $categorie)
                ->map(fn ($element) => [
                    'nature' => $element->nature,
                    'designation' => $element->designation,
                    'montant' => (float) $element->montant,
                    'mode_detention' => $element->mode_detention,
                ])
                ->values()
                ->all();
        }

        $actifsFinanciers = (float) $elements->where('categorie', 'actif_financier')->sum('montant');
        $actifsNonFinanciers = (float) $elements->where('categorie', 'actif_non_financier')->sum('montant');
        $passifs = (float) $elements->where('categorie', 'passif')->sum('montant');
        $revenus = (float) $elements->where('categorie', 'revenu')->sum('montant');
        $charges = (float) $elements->where('categorie', 'charge')->sum('montant');

        return [
            'societe' => [
                'id' => $client->id,
                'raison_sociale' => $client->raison_sociale,
                'forme_juridique' => $client->forme_juridique,
            ],

            'synthese' => [
                'actifs_financiers' => $actifsFinanciers,
                'actifs_non_financiers' => $actifsNonFinanciers,
                'actifs_totaux' => $actifsFinanciers + $actifsNonFinanciers,
                'passifs' => $passifs,
                'patrimoine_net' => $actifsFinanciers + $actifsNonFinanciers - $passifs,
                'revenus_annuels' => $revenus,
                'charges_annuelles' => $charges,
                'solde_annuel' => $revenus - $charges,
            ],

            'patrimoine' => $grouped,
        ];
    }

    private function validateResult(array $result): void
    {
        foreach (['points_forts', 'points_attention'] as $key) {
            if (! isset($result[$key]) || ! is_array($result[$key]) || count($result[$key]) !== 4) {
                throw new RuntimeException("Structure invalide : {$key} doit contenir exactement 4 éléments.");
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

Tu analyses exclusivement les données patrimoniales transmises pour une société (personne morale), et non pour un particulier.

OBJECTIF

Produire exactement :
- 4 points forts ;
- 4 points d'attention.

============================================================
1. NE RIEN INVENTER
============================================================

Tu utilises uniquement les données transmises. Une donnée absente signifie information inconnue, jamais nulle.

============================================================
2. ANALYSE PATRIMONIALE
============================================================

Analyse notamment :
- composition des actifs financiers et non financiers de la société ;
- concentration et diversification ;
- poids de la trésorerie et des placements financiers ;
- endettement et rapport actifs/passifs ;
- niveau de patrimoine net de la société ;
- structure des revenus et des charges ;
- solde annuel ;
- cohérence entre les différentes composantes du patrimoine professionnel.

============================================================
3. POINTS FORTS
============================================================

Produis exactement 4 points forts : trésorerie disponible, diversification, endettement maîtrisé, capacité d'épargne positive, équilibre entre catégories d'actifs.

============================================================
4. POINTS D'ATTENTION
============================================================

Produis exactement 4 points d'attention : concentration, dépendance à une catégorie d'actifs, endettement, solde annuel faible ou négatif, informations patrimoniales insuffisantes.

============================================================
5. AUCUNE RECOMMANDATION
============================================================

Ne recommande aucun produit, placement, allocation ou investissement.

============================================================
6. FORMAT
============================================================

Retourne exclusivement un objet JSON valide :

{
  "points_forts": [
    { "titre": "6 mots maximum", "analyse": "Phrase synthétique et factuelle." }
  ],
  "points_attention": [
    { "titre": "6 mots maximum", "analyse": "Phrase synthétique et factuelle." }
  ]
}

Exactement 4 objets dans chaque tableau. Aucun HTML. Aucun Markdown. Aucun texte avant ou après le JSON.
PROMPT;
    }
}
