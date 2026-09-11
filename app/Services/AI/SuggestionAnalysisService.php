<?php

namespace App\Services\AI;

use App\Services\PromptIaService;

use App\Models\Client;
use App\Models\ClientAnalysis;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class SuggestionAnalysisService
{
    public const PROMPT_VERSION = 'suggestion-v1';

    private const MAX_ATTEMPTS = 3;

    private const ANALYSIS_TYPES = [
        'kyc',
        'patrimoine',
        'profil_investisseur',
    ];

    public function analyze(Client $client): ClientAnalysis
    {
        $analyses = $this->getRequiredAnalyses($client);

        if ($analyses->count() !== 3) {
            throw new RuntimeException(
                'Les trois analyses KYC, Patrimoine et Profil investisseur sont obligatoires.'
            );
        }

        foreach (self::ANALYSIS_TYPES as $type) {
            $analysis = $analyses->get($type);

            if (! $analysis) {
                throw new RuntimeException(
                    "Analyse {$type} absente ou non terminée."
                );
            }

            if (
                ! $analysis->completed_at ||
                $analysis->completed_at->lt(now()->subYear())
            ) {
                throw new RuntimeException(
                    "L'analyse {$type} est absente ou âgée de plus d'un an."
                );
            }
        }

        $input = [
            'client' => [
                'id' => $client->id,
                'prenom' => $client->prenom,
                'nom' => $client->nom,
            ],

            'kyc' => $analyses->get('kyc')->result_json,

            'patrimoine' => $analyses->get('patrimoine')->result_json,

            'profil_investisseur' =>
                $analyses->get('profil_investisseur')->result_json,
        ];

        $analysis = ClientAnalysis::create([
            'client_id' => $client->id,
            'type' => 'suggestion',
            'status' => 'processing',
            'input_version' => '1',
            'prompt_version' => self::PROMPT_VERSION,
            'model' => config('services.openai.model', 'gpt-4.1'),
            'input_data' => $input,
            'started_at' => now(),
        ]);

        $messages = [
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
        ];

        $lastException = null;
        $raw = null;

        try {
            for ($attempt = 1; $attempt <= self::MAX_ATTEMPTS; $attempt++) {
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

                                'messages' => $messages,
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

                    /*
                     * Identifiant stable par prestation (1 à 4), attribué par
                     * Wendee et non par l'IA : sert ensuite à référencer sans
                     * ambiguïté la prestation choisie par le conseiller,
                     * indépendamment de tout réordonnancement fait par les
                     * moteurs de présentation.
                     */
                    if (isset($result['prestations']) && is_array($result['prestations'])) {
                        foreach ($result['prestations'] as $index => &$prestationBrute) {
                            if (is_array($prestationBrute)) {
                                $prestationBrute['id'] = $index + 1;
                            }
                        }
                        unset($prestationBrute);
                    }

                    // Persistance AVANT validation, pour pouvoir diagnostiquer
                    // les échecs même si la validation rejette la réponse.
                    $analysis->update([
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
                    ]);

                    $this->validateResult($result);

                    $analysis->update([
                        'status' => 'completed',
                        'completed_at' => now(),
                        'error_message' => null,
                    ]);

                    return $analysis->fresh();

                } catch (RuntimeException $e) {
                    $lastException = $e;

                    if ($attempt >= self::MAX_ATTEMPTS) {
                        throw $e;
                    }

                    $messages[] = [
                        'role' => 'assistant',
                        'content' => $raw ?? '',
                    ];

                    $messages[] = [
                        'role' => 'user',
                        'content' =>
                            "Ta réponse précédente est invalide : {$e->getMessage()} "
                            . 'Corrige et renvoie un JSON strictement conforme au '
                            . 'format demandé, avec exactement 4 prestations.',
                    ];

                    continue;
                }
            }

            throw $lastException ?? new RuntimeException(
                'Échec de génération de la suggestion.'
            );

        } catch (\Throwable $e) {

            $analysis->update([
                'status' => 'failed',
                'completed_at' => now(),
                'error_message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    private function getRequiredAnalyses(Client $client)
    {
        return $client->analyses()
            ->where('status', 'completed')
            ->whereIn('type', self::ANALYSIS_TYPES)
            ->whereNotNull('completed_at')
            ->where('completed_at', '>=', now()->subYear())
            ->latest('completed_at')
            ->get()
            ->groupBy('type')
            ->map(fn ($items) => $items->first());
    }

    private function systemPrompt(): string
    {
        return app(\App\Services\PromptIaService::class)->resolve(
            'suggestion_physique',
            $this->defaultSystemPrompt()
        );
    }

    private function defaultSystemPrompt(): string
    {
        return <<<'PROMPT'
Tu es le moteur d'aide à la décision patrimoniale de Wendee.

Tu analyses simultanément trois analyses déjà réalisées :
1. Recueil d'informations client / KYC
2. Patrimoine
3. Profil investisseur

Ton objectif est d'identifier les besoins d'accompagnement réellement pertinents
pour ce client.

Tu ne dois pas inventer de données absentes des analyses.

Tu dois croiser les informations et faire apparaître les liens entre :
- situation personnelle et familiale ;
- situation professionnelle ;
- fiscalité ;
- patrimoine ;
- endettement ;
- liquidités ;
- capacité d'épargne ;
- profil de risque ;
- connaissances et expérience financières ;
- objectifs et horizon d'investissement.

Tu dois privilégier les besoins concrets et actionnables.

Produis exactement 4 prestations recommandées.

Pour chaque prestation, tu dois déterminer :

- categorie : exactement l'une des valeurs suivantes : "ASSURANCE",
  "IOBSP", "CIF" ;
- type_document : le document contractuel correspondant à la catégorie,
  strictement selon cette correspondance :
    ASSURANCE → "MANDAT_COURTAGE_ASSURANCE"
    IOBSP → "MANDAT_COURTAGE_IOBSP"
    CIF → "LETTRE_MISSION_CIF"
- titre : nom court et professionnel de la prestation ;
- justification : explique précisément pourquoi elle est pertinente au regard
  des informations du dossier ;
- score_pertinence : note entière de 0 à 100 évaluant la priorité commerciale
  et patrimoniale de cette prestation pour ce dossier (usage interne uniquement,
  jamais un indicateur de risque ni de certitude) ;
- motif_score : une phrase courte expliquant ce qui justifie ce niveau de score ;
- donnees_cles : entre 2 et 5 données précises et vérifiables, issues des
  analyses fournies, qui ont déclenché cette suggestion (ex : montants,
  statuts, profils) ;
- perimetre : entre 3 et 6 éléments courts décrivant les sujets qui seraient
  étudiés si la mission est retenue ;
- actions : exactement 2 actions concrètes que le conseiller pourrait proposer.

Ne recommande jamais un produit financier précis uniquement parce qu'il existe
dans le patrimoine du client.

Les recommandations doivent rester adaptées au profil investisseur et aux
informations disponibles.

Ne mélange jamais categorie et type_document : chaque type_document doit
correspondre exactement à sa categorie selon la table de correspondance
ci-dessus.

Format JSON STRICT :

{
  "prestations": [
    {
      "categorie": "CIF",
      "type_document": "LETTRE_MISSION_CIF",
      "titre": "...",
      "justification": "...",
      "score_pertinence": 92,
      "motif_score": "...",
      "donnees_cles": [
        "...",
        "..."
      ],
      "perimetre": [
        "...",
        "...",
        "..."
      ],
      "actions": [
        "...",
        "..."
      ]
    }
  ]
}

Il doit y avoir exactement 4 prestations.
PROMPT;
    }

    private function validateResult(array $result): void
    {
        if (
            ! isset($result['prestations']) ||
            ! is_array($result['prestations']) ||
            count($result['prestations']) !== 4
        ) {
            throw new RuntimeException(
                'Le résultat Suggestion doit contenir exactement 4 prestations.'
            );
        }

        $categoriesValides = [
            'ASSURANCE' => 'MANDAT_COURTAGE_ASSURANCE',
            'IOBSP' => 'MANDAT_COURTAGE_IOBSP',
            'CIF' => 'LETTRE_MISSION_CIF',
        ];

        foreach ($result['prestations'] as $prestation) {

            if (
                ! is_array($prestation) ||
                empty($prestation['titre']) ||
                empty($prestation['justification']) ||
                ! isset($prestation['actions']) ||
                ! is_array($prestation['actions']) ||
                count($prestation['actions']) !== 2
            ) {
                throw new RuntimeException(
                    'Format de prestation Suggestion invalide.'
                );
            }

            $categorie = $prestation['categorie'] ?? null;
            $typeDocument = $prestation['type_document'] ?? null;

            if (
                ! is_string($categorie) ||
                ! array_key_exists($categorie, $categoriesValides) ||
                $typeDocument !== $categoriesValides[$categorie]
            ) {
                throw new RuntimeException(
                    'Catégorie ou type de document de prestation Suggestion invalide.'
                );
            }

            $score = $prestation['score_pertinence'] ?? null;

            if (
                ! is_int($score) ||
                $score < 0 ||
                $score > 100
            ) {
                throw new RuntimeException(
                    'Score de pertinence de prestation Suggestion invalide.'
                );
            }

            if (empty($prestation['motif_score']) || ! is_string($prestation['motif_score'])) {
                throw new RuntimeException(
                    'Motif de score de prestation Suggestion invalide.'
                );
            }

            $donneesCles = $prestation['donnees_cles'] ?? null;

            if (
                ! is_array($donneesCles) ||
                count($donneesCles) < 2 ||
                count($donneesCles) > 5
            ) {
                throw new RuntimeException(
                    'Données clés de prestation Suggestion invalides.'
                );
            }

            $perimetre = $prestation['perimetre'] ?? null;

            if (
                ! is_array($perimetre) ||
                count($perimetre) < 3 ||
                count($perimetre) > 6
            ) {
                throw new RuntimeException(
                    'Périmètre de prestation Suggestion invalide.'
                );
            }
        }
    }
}
