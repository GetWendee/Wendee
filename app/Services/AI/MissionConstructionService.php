<?php

namespace App\Services\AI;

use App\Services\PromptIaService;

use App\Models\Client;
use App\Models\ClientAnalysis;
use App\Models\Mission;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * IA 2A — Construction de la mission.
 *
 * Prend UNE prestation déjà sélectionnée par le conseiller (Bloc A, produite
 * par SuggestionAnalysisService / moteur IA 1) et la transforme en définition
 * contractuelle personnalisée de la mission : objet, contexte, périmètre
 * détaillé, exclusions, travaux, livrables, pièces à collecter.
 *
 * Ne rédige AUCUNE clause réglementaire, ne fixe pas les honoraires, et ne
 * choisit pas le modèle contractuel (Bloc C/D et moteur contractuel
 * déterministe : hors scope de ce moteur).
 */
class MissionConstructionService
{
    public const PROMPT_VERSION = 'mission-construction-v1';

    private const MAX_ATTEMPTS = 3;

    private const ANALYSIS_TYPES = [
        'kyc',
        'patrimoine',
        'profil_investisseur',
    ];

    public function construire(
        Client $client,
        ClientAnalysis $suggestion,
        array $prestation
    ): Mission {

        $analyses = $client->analyses()
            ->where('status', 'completed')
            ->whereIn('type', self::ANALYSIS_TYPES)
            ->whereNotNull('completed_at')
            ->where('completed_at', '>=', now()->subYear())
            ->latest('completed_at')
            ->get()
            ->groupBy('type')
            ->map(fn ($items) => $items->first());

        $input = [
            'client' => [
                'id' => $client->id,
                'prenom' => $client->prenom,
                'nom' => $client->nom,
            ],

            'prestation_selectionnee' => $prestation,

            'kyc' => optional($analyses->get('kyc'))->result_json,
            'patrimoine' => optional($analyses->get('patrimoine'))->result_json,
            'profil_investisseur' => optional($analyses->get('profil_investisseur'))->result_json,
        ];

        $mission = Mission::create([
            'client_id' => $client->id,
            'suggestion_id' => $suggestion->id,
            'prestation_id' => (int) ($prestation['id'] ?? 0),
            'prestation_snapshot' => $prestation,
            'categorie' => $prestation['categorie'] ?? '',
            'type_document' => $prestation['type_document'] ?? '',
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

                    // Persistance AVANT validation, pour pouvoir diagnostiquer
                    // les échecs même si la validation rejette la réponse.
                    $mission->update([
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

                    $mission->update([
                        'status' => 'completed',
                        'completed_at' => now(),
                        'error_message' => null,
                    ]);

                    return $mission->fresh();

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
                            . 'format demandé.',
                    ];

                    continue;
                }
            }

            throw $lastException ?? new RuntimeException(
                'Échec de construction de la mission.'
            );

        } catch (\Throwable $e) {

            $mission->update([
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
            'mission_construction',
            $this->defaultSystemPrompt()
        );
    }

    private function defaultSystemPrompt(): string
    {
        return <<<'PROMPT'
RÔLE

Tu es le moteur de construction de mission de Wendee (IA 2A).

Tu interviens APRÈS qu'un conseiller a sélectionné UNE prestation parmi
celles détectées par le moteur de suggestion (IA 1). Cette prestation
sélectionnée t'est transmise dans le champ prestation_selectionnee : elle
contient notamment categorie, type_document, titre, justification,
perimetre, actions, donnees_cles, score_pertinence, motif_score, id.

Tu reçois également les données source du dossier (kyc, patrimoine,
profil_investisseur) qui justifient cette prestation.

Ton rôle N'EST PAS de rédiger un contrat. Tu ne rédiges aucune clause
réglementaire, aucune mention statutaire, aucune référence légale. Tu ne
fixes ni honoraires, ni conditions commerciales : cela est géré séparément
par Wendee.

Ton rôle est de transformer la prestation sélectionnée en définition
contractuelle personnalisée de la mission : ce que le client confie et ce
que le cabinet va réaliser, avant que Wendee applique le modèle contractuel
réglementaire correspondant à la categorie.

Tu ne dois JAMAIS :

- inventer une donnée absente du dossier ;
- recommander un produit ou un support financier précis ;
- formuler une conclusion ou une préconisation (la mission n'a pas encore
  été réalisée) ;
- changer la nature de la prestation sélectionnée (categorie, type_document) ;
- écrire une phrase du type « nous recommandons de souscrire... ».


==================================================
1. INTITULÉ DE LA MISSION
==================================================

Reprends le titre de la prestation sélectionnée. Tu peux l'affiner
légèrement pour qu'il soit plus précis et plus professionnel, mais sans
changer la nature de la mission.


==================================================
2. CONTEXTE
==================================================

Rédige un paragraphe de contexte (3 à 6 phrases) qui explique la situation
du client justifiant cette mission, en t'appuyant sur les données du
dossier (kyc, patrimoine, profil_investisseur) et sur les donnees_cles de
la prestation sélectionnée.

Ce n'est pas encore une recommandation : c'est un constat factuel qui
justifie que la mission soit menée.


==================================================
3. OBJET DE LA MISSION
==================================================

Rédige l'objet de la mission (2 à 4 phrases) : ce que le client confie
précisément au cabinet. Doit être clair juridiquement et commercialement :
le client doit comprendre ce qu'il achète.

N'écris jamais de préconisation de produit ou de solution précise à ce
stade.


==================================================
4. PÉRIMÈTRE CONTRACTUEL
==================================================

Développe le perimetre de la prestation sélectionnée en un périmètre
contractuel précis : entre 4 et 8 éléments courts (une ligne chacun).

Ajoute ensuite un bloc hors_perimetre : entre 2 et 6 éléments qui ne sont
PAS couverts par cette mission (ex : liquidation effective de droits,
rédaction d'actes juridiques, consultation fiscale ou juridique réservée,
gestion sous mandat, mise en œuvre opérationnelle d'une solution non
prévue au contrat). Adapte cette liste à la nature de la mission.


==================================================
5. TRAVAUX À RÉALISER
==================================================

Produis entre 3 et 7 étapes de travaux, dans un ordre logique
d'exécution. Chaque étape doit être concrète et vérifiable.


==================================================
6. LIVRABLES
==================================================

Produis entre 2 et 6 livrables concrets et contractuels (ex : diagnostic,
synthèse, simulations comparatives, rapport de préconisations, restitution
au client).


==================================================
7. PIÈCES À COLLECTER
==================================================

Identifie les documents indispensables à la bonne exécution de la mission
et qui ne sont pas déjà disponibles dans le dossier. Peut être une liste
vide si aucune pièce complémentaire n'est nécessaire.

Pour chaque pièce : document (nom clair), necessaire (true si
indispensable, false si simplement utile), raison (une phrase expliquant
pourquoi).


==================================================
8. FORMAT JSON STRICT
==================================================

Retourne exclusivement un JSON valide, structure strictement comme suit,
aucun texte avant ou après :

{
  "intitule_mission": "...",
  "contexte": "...",
  "objet": "...",
  "perimetre": [
    "...",
    "...",
    "...",
    "..."
  ],
  "hors_perimetre": [
    "...",
    "..."
  ],
  "travaux": [
    "...",
    "...",
    "..."
  ],
  "livrables": [
    "...",
    "..."
  ],
  "pieces_a_collecter": [
    {
      "document": "...",
      "necessaire": true,
      "raison": "..."
    }
  ]
}

Contraintes :

- perimetre : entre 4 et 8 éléments ;
- hors_perimetre : entre 2 et 6 éléments ;
- travaux : entre 3 et 7 éléments ;
- livrables : entre 2 et 6 éléments ;
- pieces_a_collecter : tableau, peut être vide ([]) ;
- aucune donnée inventée ;
- aucun texte avant ou après le JSON.
PROMPT;
    }

    private function validateResult(array $result): void
    {
        if (
            empty($result['intitule_mission']) ||
            ! is_string($result['intitule_mission'])
        ) {
            throw new RuntimeException(
                'Intitulé de mission manquant ou invalide.'
            );
        }

        if (
            empty($result['contexte']) ||
            ! is_string($result['contexte'])
        ) {
            throw new RuntimeException(
                'Contexte de mission manquant ou invalide.'
            );
        }

        if (
            empty($result['objet']) ||
            ! is_string($result['objet'])
        ) {
            throw new RuntimeException(
                'Objet de mission manquant ou invalide.'
            );
        }

        $this->validerListe($result, 'perimetre', 4, 8);
        $this->validerListe($result, 'hors_perimetre', 2, 6);
        $this->validerListe($result, 'travaux', 3, 7);
        $this->validerListe($result, 'livrables', 2, 6);

        if (
            ! isset($result['pieces_a_collecter']) ||
            ! is_array($result['pieces_a_collecter'])
        ) {
            throw new RuntimeException(
                'Liste des pièces à collecter manquante ou invalide.'
            );
        }

        foreach ($result['pieces_a_collecter'] as $piece) {
            if (
                ! is_array($piece) ||
                empty($piece['document']) ||
                ! is_string($piece['document']) ||
                ! isset($piece['necessaire']) ||
                ! is_bool($piece['necessaire']) ||
                empty($piece['raison']) ||
                ! is_string($piece['raison'])
            ) {
                throw new RuntimeException(
                    'Format de pièce à collecter invalide.'
                );
            }
        }
    }

    private function validerListe(array $result, string $cle, int $min, int $max): void
    {
        if (
            ! isset($result[$cle]) ||
            ! is_array($result[$cle]) ||
            count($result[$cle]) < $min ||
            count($result[$cle]) > $max
        ) {
            throw new RuntimeException(
                "Liste {$cle} manquante ou invalide (attendu entre {$min} et {$max} éléments)."
            );
        }

        foreach ($result[$cle] as $item) {
            if (! is_string($item) || trim($item) === '') {
                throw new RuntimeException(
                    "Élément invalide dans la liste {$cle}."
                );
            }
        }
    }
}
