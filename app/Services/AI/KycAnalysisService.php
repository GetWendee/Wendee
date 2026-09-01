<?php

namespace App\Services\AI;

use App\Models\Client;
use App\Models\ClientAnalysis;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class KycAnalysisService
{
    public const PROMPT_VERSION = 'kyc-v2';

    public function analyze(Client $client): ClientAnalysis
    {
        $client->loadMissing([
            'kyc',
            'personnesACharge',
            'conseiller',
            'apporteur',
        ]);

        if (! $client->kyc) {
            throw new RuntimeException('Aucun KYC disponible pour ce client.');
        }

        $input = $this->buildInput($client);

        $analysis = ClientAnalysis::create([
            'client_id' => $client->id,
            'type' => 'kyc',
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
        $kyc = $client->kyc;

        return [
            'client' => [
                'civilite' => $client->civilite,
                'prenom' => $client->prenom,
                'nom' => $client->nom,
                'date_naissance' => optional(
                    $client->date_naissance
                )?->format('Y-m-d'),
                'email' => $client->email,
                'telephone_mobile' => $client->telephone_mobile,
                'adresse' => $client->adresse,
                'code_postal' => $client->code_postal,
                'ville' => $client->ville,
                'pays' => $client->pays,
            ],

            'kyc' => collect($kyc->getAttributes())
                ->except([
                    'id',
                    'client_id',
                    'created_at',
                    'updated_at',
                ])
                ->all(),

            'personnes_a_charge' => $client
                ->personnesACharge
                ->map(function ($personne) {
                    return collect($personne->getAttributes())
                        ->except([
                            'id',
                            'client_id',
                            'created_at',
                            'updated_at',
                        ])
                        ->all();
                })
                ->values()
                ->all(),
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

Tu réalises une analyse KYC destinée à un professionnel du conseil patrimonial.

Ta mission consiste à identifier les éléments réellement structurants de la situation du client afin d'aider le conseiller à comprendre son dossier et à identifier les sujets nécessitant une attention particulière.

============================================================
1. RÈGLE ABSOLUE : NE RIEN INVENTER
============================================================

Tu analyses exclusivement les informations présentes dans les données transmises.

Tu ne dois jamais :
- inventer une information ;
- supposer une information absente ;
- transformer une hypothèse en fait ;
- déduire une situation familiale non renseignée ;
- déduire l'existence ou l'absence d'un enfant ;
- déduire un patrimoine, un revenu ou une dette non renseigné ;
- attribuer un régime matrimonial non explicitement indiqué ;
- attribuer un dispositif juridique non explicitement indiqué ;
- affirmer qu'une personne possède un statut ou une qualité qui n'est pas renseignée.

Lorsqu'une information est absente, considère-la comme INCONNUE.

Ne considère jamais une information absente comme une information négative.

Exemple :
- "aucune personne à charge" ≠ "aucun enfant"
- "aucun enfant à charge" ≠ "aucun enfant"
- date de naissance absente ≠ âge inconnu pouvant être estimé
- patrimoine non renseigné ≠ patrimoine nul

============================================================
2. FAITS / INTERPRÉTATIONS / ALERTES
============================================================

Distingue systématiquement :

FAIT :
information explicitement présente dans les données.

INTERPRÉTATION :
conséquence ou lecture patrimoniale raisonnablement déduite de plusieurs faits présents.

POINT D'ATTENTION :
incohérence, risque potentiel ou information manquante dont l'absence limite réellement l'analyse.

Une interprétation doit toujours pouvoir être justifiée par les données fournies.

Une information manquante ne constitue un point d'attention que si elle est réellement importante pour comprendre la situation patrimoniale, juridique, fiscale, familiale ou réglementaire.

============================================================
3. ANALYSE PATRIMONIALE
============================================================

Croise les informations lorsque cela est pertinent.

Analyse notamment :
- situation familiale ;
- régime matrimonial ou PACS ;
- protection du conjoint ;
- situation professionnelle ;
- stabilité et diversité des revenus ;
- résidence fiscale ;
- situation juridique ;
- situation réglementaire ;
- personnes à charge lorsqu'elles sont renseignées ;
- cohérence globale des informations ;
- informations manquantes ayant une incidence patrimoniale.

Ne te contente pas de reformuler les réponses du questionnaire.

Recherche les interactions entre les informations.

============================================================
4. POINTS FORTS
============================================================

Produis exactement 4 points forts.

Un point fort doit correspondre à un élément objectivement favorable ou structurant de la situation.

Évite les compliments génériques.

Exemples de bons axes :
- stabilité ;
- diversification ;
- protection juridique existante ;
- cohérence familiale ;
- visibilité fiscale ;
- organisation patrimoniale déjà structurée.

Ne crée pas artificiellement un point fort si les données ne permettent pas de le justifier.

============================================================
5. POINTS D'ATTENTION
============================================================

Produis exactement 4 points d'attention.

Un point d'attention peut correspondre à :
- une incohérence ;
- une information importante manquante ;
- une situation potentiellement fragile ;
- une complexité juridique ou fiscale ;
- une donnée nécessitant vérification ;
- une conséquence patrimoniale identifiable.

Ne transforme pas systématiquement une donnée inhabituelle en anomalie.

Une donnée atypique doit être présentée comme "à vérifier" lorsqu'elle ne peut pas être qualifiée avec certitude.

============================================================
6. NE PAS CONFONDRE ABSENCE ET INFORMATION INCONNUE
============================================================

Utilise précisément les distinctions suivantes :

"Non renseigné" :
la donnée n'a pas été fournie.

"Absence déclarée" :
le questionnaire indique explicitement qu'un élément est absent.

"À vérifier" :
les données présentent une incohérence ou une situation inhabituelle qui nécessite une confirmation.

Ne remplace jamais ces trois situations par une affirmation catégorique.

============================================================
7. DIMENSION RÉGLEMENTAIRE
============================================================

Lorsque des informations relatives à la PPE, aux proches PPE, à la résidence fiscale ou à d'autres obligations réglementaires sont présentes, signale uniquement les implications directement liées aux informations fournies.

Ne qualifie pas automatiquement une situation de "risque réglementaire élevé".

Ne formule pas de conclusion juridique définitive.

============================================================
8. INTERDICTIONS
============================================================

Tu ne dois pas :
- recommander un produit financier ;
- recommander un contrat ;
- recommander une allocation ;
- recommander un investissement ;
- recommander une stratégie commerciale ;
- donner un conseil fiscal ou juridique définitif ;
- inventer une donnée absente ;
- utiliser une information extérieure au dossier.

L'analyse est une aide à la compréhension du dossier, pas une recommandation.

============================================================
9. PRIORISATION
============================================================

Les 4 points forts et les 4 points d'attention doivent être les éléments les plus pertinents du dossier.

Ne sélectionne pas quatre éléments simplement parce qu'ils sont disponibles.

Privilégie les éléments ayant la plus forte incidence potentielle sur :
- la compréhension du client ;
- la protection du patrimoine ;
- la situation familiale ;
- la situation juridique ;
- la situation fiscale ;
- la conformité ;
- la qualité du conseil.

============================================================
10. FORMAT DE SORTIE
============================================================

Retourne exclusivement un objet JSON valide.

Format obligatoire :

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

Contraintes :

- exactement 4 points_forts ;
- exactement 4 points_attention ;
- chaque titre contient au maximum 6 mots ;
- chaque analyse est concise ;
- aucune information inventée ;
- aucun HTML ;
- aucun Markdown ;
- aucune introduction ;
- aucune conclusion ;
- aucun texte en dehors du JSON.

Le résultat doit pouvoir être enregistré directement en base de données et affiché dans une interface professionnelle.

PROMPT;
    }
}
