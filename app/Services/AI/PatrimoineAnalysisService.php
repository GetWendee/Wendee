<?php

namespace App\Services\AI;

use App\Models\Client;
use App\Models\ClientAnalysis;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class PatrimoineAnalysisService
{
    public const PROMPT_VERSION = 'patrimoine-v1';

    public function analyze(Client $client): ClientAnalysis
    {
        $client->loadMissing([
            'patrimoineElements',
        ]);

        $input = $this->buildInput($client);

        $analysis = ClientAnalysis::create([
            'client_id' => $client->id,
            'type' => 'patrimoine',
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
                    'response_format' => [
                        'type' => 'json_object',
                    ],
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => $this->systemPrompt(),
                        ],
                        [
                            'role' => 'user',
                            'content' => json_encode(
                                $input,
                                JSON_UNESCAPED_UNICODE
                                | JSON_UNESCAPED_SLASHES
                                | JSON_PRETTY_PRINT
                            ),
                        ],
                    ],
                ]);

            if (! $response->successful()) {
                throw new RuntimeException(
                    'Erreur OpenAI HTTP '
                    . $response->status()
                    . ' : '
                    . $response->body()
                );
            }

            $payload = $response->json();

            $raw = data_get(
                $payload,
                'choices.0.message.content'
            );

            if (! is_string($raw) || trim($raw) === '') {
                throw new RuntimeException(
                    'Réponse OpenAI vide ou invalide.'
                );
            }

            $result = json_decode($raw, true);

            if (! is_array($result)) {
                throw new RuntimeException(
                    'La réponse OpenAI ne contient pas un JSON valide.'
                );
            }

            $this->validateResult($result);

            $analysis->update([
                'status' => 'completed',
                'result_json' => $result,
                'raw_response' => $raw,
                'prompt_tokens' => data_get(
                    $payload,
                    'usage.prompt_tokens'
                ),
                'completion_tokens' => data_get(
                    $payload,
                    'usage.completion_tokens'
                ),
                'total_tokens' => data_get(
                    $payload,
                    'usage.total_tokens'
                ),
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

        foreach ([
            'actif_financier',
            'actif_non_financier',
            'passif',
            'revenu',
            'charge',
        ] as $categorie) {

            $grouped[$categorie] = $elements
                ->where('categorie', $categorie)
                ->map(function ($element) {
                    return [
                        'nature' => $element->nature,
                        'designation' => $element->designation,
                        'montant' => (float) $element->montant,
                        'mode_detention' => $element->mode_detention,
                    ];
                })
                ->values()
                ->all();
        }

        $actifsFinanciers = (float) $elements
            ->where('categorie', 'actif_financier')
            ->sum('montant');

        $actifsNonFinanciers = (float) $elements
            ->where('categorie', 'actif_non_financier')
            ->sum('montant');

        $passifs = (float) $elements
            ->where('categorie', 'passif')
            ->sum('montant');

        $revenus = (float) $elements
            ->where('categorie', 'revenu')
            ->sum('montant');

        $charges = (float) $elements
            ->where('categorie', 'charge')
            ->sum('montant');

        return [
            'client' => [
                'id' => $client->id,
                'prenom' => $client->prenom,
                'nom' => $client->nom,
            ],

            'synthese' => [
                'actifs_financiers' => $actifsFinanciers,
                'actifs_non_financiers' => $actifsNonFinanciers,
                'actifs_totaux' => $actifsFinanciers + $actifsNonFinanciers,
                'passifs' => $passifs,
                'patrimoine_net' =>
                    $actifsFinanciers
                    + $actifsNonFinanciers
                    - $passifs,
                'revenus_annuels' => $revenus,
                'charges_annuelles' => $charges,
                'solde_annuel' => $revenus - $charges,
            ],

            'patrimoine' => $grouped,
        ];
    }

    private function validateResult(array $result): void
    {
        foreach ([
            'points_forts',
            'points_attention',
        ] as $key) {

            if (
                ! isset($result[$key])
                || ! is_array($result[$key])
                || count($result[$key]) !== 4
            ) {
                throw new RuntimeException(
                    "Structure invalide : {$key} doit contenir exactement 4 éléments."
                );
            }

            foreach ($result[$key] as $item) {

                if (
                    ! is_array($item)
                    || empty($item['titre'])
                    || empty($item['analyse'])
                ) {
                    throw new RuntimeException(
                        "Structure invalide dans {$key}."
                    );
                }
            }
        }
    }

    private function systemPrompt(): string
    {
        return <<<'PROMPT'
Tu es un conseiller en gestion de patrimoine expérimenté.

Tu analyses exclusivement les données patrimoniales transmises.

OBJECTIF

Produire exactement :
- 4 points forts ;
- 4 points d'attention.

L'analyse doit porter exclusivement sur la structure et la situation patrimoniale.

============================================================
1. NE RIEN INVENTER
============================================================

Tu utilises uniquement les données transmises.

Tu ne dois jamais :
- inventer un actif ;
- inventer un passif ;
- inventer un revenu ou une charge ;
- supposer une valeur ;
- supposer un mode de détention ;
- supposer un objectif qui n'est pas transmis ;
- attribuer une fiscalité qui n'est pas présente dans les données ;
- considérer une donnée absente comme nulle.

Une donnée absente signifie : information inconnue.

============================================================
2. ANALYSE PATRIMONIALE
============================================================

Analyse notamment :

- composition des actifs financiers ;
- composition des actifs non financiers ;
- concentration patrimoniale ;
- diversification ;
- poids de l'immobilier ;
- poids des actifs financiers ;
- endettement ;
- rapport actifs/passifs ;
- niveau de patrimoine net ;
- structure des revenus ;
- structure des charges ;
- solde annuel ;
- liquidité lorsqu'elle peut être identifiée ;
- cohérence entre les différentes composantes du patrimoine ;
- mode de détention lorsqu'il est renseigné.

Croise les informations lorsque cela apporte une véritable valeur analytique.

Ne te contente pas de reformuler les lignes du patrimoine.

============================================================
3. POINTS FORTS
============================================================

Produis exactement 4 points forts.

Un point fort doit correspondre à un élément objectivement favorable ou structurant.

Exemples :
- diversification réelle ;
- patrimoine financier significatif ;
- patrimoine immobilier structuré ;
- endettement maîtrisé ;
- capacité d'épargne positive ;
- équilibre entre différentes catégories d'actifs.

Ne crée pas artificiellement un point fort.

============================================================
4. POINTS D'ATTENTION
============================================================

Produis exactement 4 points d'attention.

Ils peuvent porter sur :
- concentration ;
- dépendance à une catégorie d'actifs ;
- endettement ;
- solde annuel faible ou négatif ;
- manque de diversification ;
- poids excessif d'un actif ;
- charges importantes ;
- informations patrimoniales insuffisantes ;
- incohérence entre plusieurs données.

Une information manquante n'est un point d'attention que si elle limite réellement l'analyse.

============================================================
5. CALCULS
============================================================

Tu peux utiliser les montants fournis pour calculer des proportions ou ratios simples.

Tu ne dois jamais présenter un calcul comme une donnée renseignée directement.

Exemple :

Si les actifs financiers représentent 60 % des actifs totaux,
tu peux indiquer que les actifs financiers représentent environ 60 % des actifs.

============================================================
6. AUCUNE RECOMMANDATION
============================================================

Ne recommande :
- aucun produit ;
- aucun placement ;
- aucune allocation ;
- aucune assurance ;
- aucun investissement ;
- aucune stratégie commerciale.

Cette analyse constitue une aide à la lecture du patrimoine.

============================================================
7. FORMAT
============================================================

Retourne exclusivement un objet JSON valide :

{
  "points_forts": [
    {
      "titre": "6 mots maximum",
      "analyse": "Phrase synthétique et factuelle."
    }
  ],
  "points_attention": [
    {
      "titre": "6 mots maximum",
      "analyse": "Phrase synthétique et factuelle."
    }
  ]
}

Exactement 4 objets dans chaque tableau.

Aucun HTML.
Aucun Markdown.
Aucun texte avant ou après le JSON.

PROMPT;
    }
}
