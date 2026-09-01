<?php

namespace App\Services\AI;

use App\Models\Client;
use App\Models\ClientAnalysis;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class ProfilInvestisseurAnalysisService
{
    public const PROMPT_VERSION = 'profil-investisseur-v1';

    public function analyze(Client $client): ClientAnalysis
    {
        \Illuminate\Support\Facades\Log::info(
            'PROFIL AI SERVICE : ENTRÉE',
            [
                'client_id' => $client->id,
            ]
        );

        $client->loadMissing('profilInvestisseur');

        \Illuminate\Support\Facades\Log::info(
            'PROFIL AI SERVICE : PROFIL CHARGÉ',
            [
                'client_id' => $client->id,
                'profil_id' => $client->profilInvestisseur?->id,
            ]
        );

        if (! $client->profilInvestisseur) {
            throw new RuntimeException(
                'Aucun profil investisseur disponible pour ce client.'
            );
        }

        $input = $this->buildInput($client);

        \Illuminate\Support\Facades\Log::info(
            'PROFIL AI SERVICE : CRÉATION ANALYSE',
            [
                'client_id' => $client->id,
                'type' => 'profil_investisseur',
                'prompt_version' => self::PROMPT_VERSION,
            ]
        );

        $analysis = ClientAnalysis::create([
            'client_id' => $client->id,
            'type' => 'profil_investisseur',
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
                ->post(
                    'https://api.openai.com/v1/chat/completions',
                    [
                        'model' => config(
                            'services.openai.model',
                            'gpt-4.1'
                        ),

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
                    ]
                );

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
        $profil = $client->profilInvestisseur;

        $attributes = collect($profil->getAttributes())
            ->except([
                'id',
                'client_id',
                'created_at',
                'updated_at',
                'signe_le',
                'accepte_cgu',
            ])
            ->all();

        $reponses = is_array($profil->reponses)
            ? $profil->reponses
            : [];

        return [
            'client' => [
                'id' => $client->id,
                'prenom' => $client->prenom,
                'nom' => $client->nom,
            ],

            'profil_investisseur' => [
                'resultats_calcules' => $attributes,
                'reponses' => $reponses,
            ],
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
                    "{$key} doit contenir exactement 4 éléments."
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

                if (
                    count(
                        preg_split(
                            '/\s+/u',
                            trim((string) $item['titre'])
                        )
                    ) > 6
                ) {
                    throw new RuntimeException(
                        "Un titre de {$key} dépasse 6 mots."
                    );
                }

                if (
                    mb_strlen(
                        trim((string) $item['analyse'])
                    ) > 160
                ) {
                    throw new RuntimeException(
                        "Une analyse de {$key} dépasse 160 caractères."
                    );
                }
            }
        }
    }

    private function systemPrompt(): string
    {
        return <<<'PROMPT'
Tu es un conseiller en gestion de patrimoine expérimenté.

Tu réalises une analyse du profil investisseur destinée à un professionnel du conseil patrimonial.

============================================================
RÈGLE ABSOLUE : NE RIEN INVENTER
============================================================

Tu analyses exclusivement les informations transmises.

Une information absente est INCONNUE.
Elle ne doit jamais être considérée comme nulle ou négative.

Ne jamais :
- inventer une information ;
- supposer une information absente ;
- transformer une hypothèse en fait ;
- inventer une expérience ;
- inventer un patrimoine ;
- inventer une capacité d'épargne ;
- inventer une tolérance au risque.

============================================================
PÉRIMÈTRE D'ANALYSE
============================================================

Analyse notamment :

- connaissances financières ;
- expérience d'investissement ;
- produits connus et détenus ;
- compréhension des produits complexes ;
- objectifs ;
- horizon de placement ;
- comportement face aux pertes ;
- tolérance au risque ;
- capacité à subir des pertes ;
- profil de risque calculé ;
- patrimoine financier ;
- montant investi ;
- épargne mensuelle ;
- cohérence entre expérience, connaissance et risque ;
- cohérence entre objectif et horizon ;
- cohérence entre comportement et profil de risque ;
- préférences extra-financières lorsqu'elles sont renseignées.

============================================================
ANALYSE CROISÉE
============================================================

Ne fais pas une simple restitution des scores.

Chaque point doit résulter du croisement d'informations.

Exemples :

- connaissance élevée + expérience limitée ;
- expérience élevée + faible tolérance aux pertes ;
- patrimoine financier + montant investi ;
- objectif + horizon ;
- produits détenus + connaissances déclarées ;
- profil de risque calculé + comportement face aux pertes ;
- capacité financière + niveau d'engagement.

============================================================
POINTS FORTS
============================================================

Produis exactement 4 points forts.

Ils doivent identifier des éléments objectivement favorables
dans la cohérence du profil investisseur.

============================================================
POINTS D'ATTENTION
============================================================

Produis exactement 4 points d'attention.

Ils doivent identifier des incohérences, limites ou zones nécessitant
une vigilance professionnelle.

Une donnée manquante n'est un point d'attention que si son absence
limite réellement l'analyse.

============================================================
INTERDICTIONS
============================================================

Aucune recommandation de produit.

Aucune allocation chiffrée.

Aucune projection.

Aucun conseil explicite.

Aucune conclusion commerciale.

============================================================
FORMAT
============================================================

Retourne exclusivement un JSON valide :

{
  "points_forts": [
    {
      "titre": "Titre court",
      "analyse": "Phrase analytique."
    }
  ],
  "points_attention": [
    {
      "titre": "Titre court",
      "analyse": "Phrase analytique."
    }
  ]
}

Contraintes :

- exactement 4 points forts ;
- exactement 4 points d'attention ;
- titre de 6 mots maximum ;
- analyse de 160 caractères maximum ;
- une seule phrase par analyse ;
- aucun texte avant ou après le JSON.
PROMPT;
    }
}
