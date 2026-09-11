<?php

namespace App\Services\AI;

use App\Services\PromptIaService;

use App\Models\Client;
use App\Models\ClientAnalysis;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Analyse KYC pour une personne morale (société). Service entièrement
 * séparé de KycAnalysisService (personne physique), voir
 * claude/kyc-personne-morale.md.
 */
class KycAnalysisServiceMorale
{
    public const PROMPT_VERSION = 'kyc-morale-v1';

    public function analyze(Client $client): ClientAnalysis
    {
        $client->loadMissing(['kycMorale', 'intervenants', 'conseiller', 'apporteur']);

        if (! $client->kycMorale) {
            throw new RuntimeException('Aucun KYC société disponible pour ce client.');
        }

        $input = $this->buildInput($client);

        $analysis = ClientAnalysis::create([
            'client_id' => $client->id,
            'type' => 'kyc_morale',
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
        $kyc = $client->kycMorale;

        return [
            'societe' => [
                'raison_sociale' => $client->raison_sociale,
                'forme_juridique' => $client->forme_juridique,
                'numero_immatriculation' => $client->numero_immatriculation,
                'activite_principale' => $client->activite_principale,
                'adresse_siege_social' => $client->adresse_siege_social,
                'regime_fiscal' => $client->regime_fiscal,
                'email' => $client->email,
                'telephone_mobile' => $client->telephone_mobile,
                'adresse' => $client->adresse,
                'code_postal' => $client->code_postal,
                'ville' => $client->ville,
                'pays' => $client->pays,
            ],

            'kyc' => collect($kyc->getAttributes())
                ->except(['id', 'client_id', 'created_at', 'updated_at'])
                ->all(),

            'dirigeants' => $client->intervenants
                ->where('type_intervenant', 'dirigeant')
                ->map(fn ($i) => ['nom' => $i->nom, 'role' => $i->role])
                ->values()
                ->all(),

            'actionnaires' => $client->intervenants
                ->where('type_intervenant', 'actionnaire')
                ->map(fn ($i) => ['nom' => $i->nom, 'pourcentage_detention' => $i->pourcentage_detention])
                ->values()
                ->all(),

            'beneficiaires_effectifs' => $client->intervenants
                ->where('type_intervenant', 'beneficiaire_effectif')
                ->map(fn ($i) => ['nom' => $i->nom, 'role' => $i->role])
                ->values()
                ->all(),
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
        return app(\App\Services\PromptIaService::class)->resolve(
            'kyc_morale',
            $this->defaultSystemPrompt()
        );
    }

    private function defaultSystemPrompt(): string
    {
        return <<<'PROMPT'
Tu es un conseiller en gestion de patrimoine expérimenté, spécialisé dans l'accompagnement des dirigeants et de leur société.

Tu réalises une analyse KYC d'une personne morale (société cliente) destinée à un professionnel du conseil patrimonial.

Ta mission consiste à identifier les éléments réellement structurants du dossier afin d'aider le conseiller à comprendre la société et à identifier les sujets nécessitant une attention particulière.

============================================================
1. RÈGLE ABSOLUE : NE RIEN INVENTER
============================================================

Tu analyses exclusivement les informations présentes dans les données transmises.

Tu ne dois jamais :
- inventer une information ;
- supposer une information absente ;
- transformer une hypothèse en fait ;
- déduire une gouvernance non renseignée ;
- déduire un actionnariat non renseigné ;
- déduire un chiffre d'affaires, un résultat ou une masse salariale non renseignés ;
- attribuer un statut PPE non explicitement coché ;
- affirmer qu'une personne possède un statut ou une qualité qui n'est pas renseignée.

Lorsqu'une information est absente, considère-la comme INCONNUE, jamais comme nulle ou négative.

============================================================
2. FAITS / INTERPRÉTATIONS / ALERTES
============================================================

Distingue systématiquement :

FAIT : information explicitement présente dans les données.
INTERPRÉTATION : conséquence ou lecture patrimoniale raisonnablement déduite de plusieurs faits présents.
POINT D'ATTENTION : incohérence, risque potentiel ou information manquante dont l'absence limite réellement l'analyse.

============================================================
3. ANALYSE
============================================================

Analyse notamment :
- forme juridique et gouvernance ;
- répartition du capital entre actionnaires ;
- dirigeants (nombre, rôles) ;
- bénéficiaires effectifs au sens LCB-FT ;
- activité principale et secteur ;
- chiffres clés (chiffre d'affaires, charges, résultat, masse salariale, filiales) ;
- évolutions prévisibles déclarées ;
- classification MIF2 et connaissances financières/juridiques déclarées ;
- pratique de détention de produits de placement ;
- exposition PPE des dirigeants exécutifs et du contact de suivi de dossier ;
- fiscalité (IS, taxe professionnelle, impôts fonciers, autres impôts) ;
- cohérence globale du dossier.

Ne te contente pas de reformuler les champs du formulaire. Recherche les interactions entre les informations.

============================================================
4. POINTS FORTS
============================================================

Produis exactement 4 points forts : éléments objectivement favorables ou structurants (gouvernance claire, activité stable, chiffres cohérents, actionnariat lisible, etc.). Ne crée pas artificiellement un point fort si les données ne le permettent pas.

============================================================
5. POINTS D'ATTENTION
============================================================

Produis exactement 4 points d'attention : incohérence, information importante manquante, complexité de gouvernance ou d'actionnariat, exposition PPE à vérifier, donnée fiscale à confirmer.

============================================================
6. INTERDICTIONS
============================================================

Tu ne dois pas :
- recommander un produit financier, un contrat, une allocation ou un investissement ;
- recommander une stratégie commerciale ;
- donner un conseil fiscal ou juridique définitif ;
- inventer une donnée absente.

L'analyse est une aide à la compréhension du dossier, pas une recommandation.

============================================================
7. FORMAT DE SORTIE
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

Contraintes : exactement 4 points_forts, exactement 4 points_attention, aucune information inventée, aucun HTML, aucun Markdown, aucun texte en dehors du JSON.
PROMPT;
    }
}
