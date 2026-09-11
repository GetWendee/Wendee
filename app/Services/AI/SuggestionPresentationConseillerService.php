<?php

namespace App\Services\AI;

use App\Services\PromptIaService;

use App\Models\Client;
use App\Models\ClientAnalysis;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Moteur de présentation professionnelle (conseiller / courtier) des
 * prestations suggérées par SuggestionAnalysisService (moteur IA 1).
 *
 * Ne refait aucune analyse patrimoniale : reformate les prestations brutes
 * en cartes de lecture rapide pour l'interface conseiller, en conservant le
 * score de pertinence interne (jamais montré au client).
 */
class SuggestionPresentationConseillerService
{
    public const PROMPT_VERSION = 'suggestion-presentation-conseiller-v1';

    private const MAX_ATTEMPTS = 3;

    public function presenter(ClientAnalysis $suggestion): ClientAnalysis
    {
        $prestations = $suggestion->result_json['prestations'] ?? null;

        if (! is_array($prestations) || count($prestations) === 0) {
            throw new RuntimeException(
                'Aucune prestation à présenter (analyse Suggestion vide ou invalide).'
            );
        }

        $client = $suggestion->client;

        $input = [
            'prestations' => $prestations,
        ];

        $analysis = ClientAnalysis::create([
            'client_id' => $client->id,
            'type' => 'suggestion_presentation_conseiller',
            'status' => 'processing',
            'input_version' => '1',
            'prompt_version' => self::PROMPT_VERSION,
            'model' => config('services.openai.model', 'gpt-4.1'),
            'input_data' => $input,
            'started_at' => now(),
        ]);

        $nombrePrestationsAttendu = count($prestations);

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

                    $this->validateResult($result, $nombrePrestationsAttendu);

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
                            . "format demandé, avec exactement {$nombrePrestationsAttendu} prestation(s).",
                    ];

                    continue;
                }
            }

            throw $lastException ?? new RuntimeException(
                'Échec de génération de la présentation conseiller.'
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

    private function systemPrompt(): string
    {
        return app(PromptIaService::class)->resolve(
            'suggestion_presentation_conseiller',
            $this->defaultSystemPrompt()
        );
    }

    private function defaultSystemPrompt(): string
    {
        return <<<'PROMPT'
RÔLE

Tu es le moteur de présentation des prestations suggérées dans l'interface
professionnelle de Wendee destinée aux conseillers en gestion de patrimoine
et aux courtiers.

Tu reçois en entrée une ou plusieurs prestations détectées par le moteur IA 1.

Chaque prestation contient notamment :

- categorie ;
- type_document ;
- titre ;
- justification ;
- perimetre ;
- actions ;
- donnees_cles ;
- score_pertinence ;
- motif_score.

Ton objectif est de transformer ces données en cartes de lecture rapide,
utiles à la décision du conseiller.

L'interface professionnelle doit permettre au conseiller de comprendre :

1. pourquoi la prestation a été suggérée ;
2. quelles données du dossier ont déclenché cette suggestion ;
3. quel est son niveau de pertinence ;
4. dans quel cadre elle sera contractualisée ;
5. ce que la mission couvrira ;
6. quels travaux seront réalisés ;
7. s'il souhaite sélectionner cette prestation pour générer le document
   commercial correspondant.


==================================================
1. PRINCIPES D'AFFICHAGE
==================================================

Le ton doit être :

- professionnel ;
- direct ;
- synthétique ;
- factuel ;
- compréhensible rapidement ;
- sans langage commercial excessif.

Ne reformule jamais une hypothèse comme une certitude.

Ne supprime pas les nuances présentes dans la suggestion initiale.

Ne transforme pas la prestation en recommandation finale.

Ne propose pas encore de produit ou de solution précise.

La carte doit rester une aide à la décision du professionnel.


==================================================
2. HIÉRARCHISATION DES INFORMATIONS
==================================================

Pour chaque prestation, présente les informations dans cet ordre :

1. numéro ou rang de la suggestion ;
2. catégorie ;
3. type de document ;
4. titre ;
5. justification ;
6. score de pertinence ;
7. motif du score ;
8. données clés ;
9. périmètre de la mission ;
10. actions proposées ;
11. action de sélection.


==================================================
3. AFFICHAGE DE LA CATÉGORIE
==================================================

Utilise un libellé lisible :

ASSURANCE
→ "Courtage en assurance"

IOBSP
→ "Courtage bancaire / financement"

CIF
→ "Conseil en investissements financiers"

Affiche également le type de document associé :

MANDAT_COURTAGE_ASSURANCE
→ "Mandat de courtage en assurance"

MANDAT_COURTAGE_IOBSP
→ "Mandat de courtage en opérations de banque"

LETTRE_MISSION_CIF
→ "Lettre de mission CIF"


==================================================
4. TITRE
==================================================

Reprends le titre de la prestation.

Le titre doit être mis en avant visuellement.

Ne le rallonge pas inutilement.

Exemple :

"Audit retraite et stratégie de revenus futurs"


==================================================
5. JUSTIFICATION
==================================================

Présente la justification sous un bloc :

"Pourquoi cette prestation ?"

Reprends la justification de manière fidèle.

Tu peux légèrement simplifier la formulation si nécessaire pour la lecture,
mais tu ne dois pas modifier le sens ni introduire de nouvelle donnée.


==================================================
6. SCORE DE PERTINENCE
==================================================

Affiche :

"Pertinence : XX/100"

Puis affiche le motif_score sous une formulation courte :

"Pourquoi ce score : ..."

Le score est réservé à l'interface professionnelle.

Ne le transforme jamais en probabilité de réussite.

Ne prétends pas qu'un score élevé signifie que le client doit accepter
la prestation.

Le score sert uniquement à prioriser les opportunités de mission.


==================================================
7. DONNÉES CLÉS
==================================================

Présente les donnees_cles sous le titre :

"Données ayant déclenché la suggestion"

Affiche chaque donnée sous forme de puce courte.

Ne reformule pas fortement.

Ne crée jamais de donnée complémentaire.

Exemple :

- Patrimoine immobilier : 1 250 000 €
- Actifs financiers : 180 000 €
- Revenus fonciers : 36 000 €/an
- Profil de risque : prudent


==================================================
8. PÉRIMÈTRE
==================================================

Présente le perimetre sous le titre :

"Périmètre envisagé"

Affiche entre 3 et 6 éléments sous forme de puces.

Ces éléments doivent permettre au conseiller de comprendre immédiatement
les sujets qui seront analysés si la mission est retenue.


==================================================
9. ACTIONS
==================================================

Présente les actions sous le titre :

"Travaux proposés"

Affiche exactement les 2 actions issues de la suggestion.

Ne transforme pas les actions en promesses de résultat.


==================================================
10. ACTION DE SÉLECTION
==================================================

Ajoute un libellé d'action destiné à l'interface :

"Sélectionner cette prestation"

Cette action signifie :

- le conseiller valide l'intérêt de la mission ;
- la prestation sélectionnée est transmise individuellement au moteur IA 2 ;
- le moteur IA 2 génère le document commercial correspondant.

Ne mentionne pas ce fonctionnement technique au client final.


==================================================
11. CAS DE PLUSIEURS PRESTATIONS
==================================================

Classe les prestations par score_pertinence décroissant.

Le rang d'affichage correspond à cet ordre.

Exemple :

01
02
03

Ne crée pas de rang pour une prestation qui n'existe pas.

Ne force pas quatre cartes.


==================================================
12. FORMAT JSON STRICT
==================================================

Retourne exclusivement un JSON valide.

Structure :

{
  "prestations": [
    {
      "rang": 1,
      "categorie_affichee": "Conseil en investissements financiers",
      "type_document_affiche": "Lettre de mission CIF",
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
      ],
      "cta": "Sélectionner cette prestation"
    }
  ]
}

Contraintes :

- conserver le même nombre de prestations que dans l'entrée ;
- classer par score décroissant ;
- ne supprimer aucune donnée utile ;
- ne créer aucune donnée nouvelle ;
- exactement 2 actions ;
- JSON valide ;
- aucun texte avant ou après.
PROMPT;
    }

    private function validateResult(array $result, int $nombrePrestationsAttendu): void
    {
        if (
            ! isset($result['prestations']) ||
            ! is_array($result['prestations']) ||
            count($result['prestations']) !== $nombrePrestationsAttendu
        ) {
            throw new RuntimeException(
                'Le résultat de présentation conseiller doit contenir le même nombre de prestations que l\'entrée.'
            );
        }

        foreach ($result['prestations'] as $prestation) {

            if (
                ! is_array($prestation) ||
                ! isset($prestation['rang']) ||
                ! is_int($prestation['rang']) ||
                empty($prestation['categorie_affichee']) ||
                empty($prestation['type_document_affiche']) ||
                empty($prestation['titre']) ||
                empty($prestation['justification']) ||
                ! isset($prestation['score_pertinence']) ||
                ! is_int($prestation['score_pertinence']) ||
                $prestation['score_pertinence'] < 0 ||
                $prestation['score_pertinence'] > 100 ||
                empty($prestation['motif_score']) ||
                ! isset($prestation['donnees_cles']) ||
                ! is_array($prestation['donnees_cles']) ||
                count($prestation['donnees_cles']) === 0 ||
                ! isset($prestation['perimetre']) ||
                ! is_array($prestation['perimetre']) ||
                count($prestation['perimetre']) < 3 ||
                count($prestation['perimetre']) > 6 ||
                ! isset($prestation['actions']) ||
                ! is_array($prestation['actions']) ||
                count($prestation['actions']) !== 2 ||
                empty($prestation['cta'])
            ) {
                throw new RuntimeException(
                    'Format de présentation conseiller invalide.'
                );
            }
        }
    }
}
