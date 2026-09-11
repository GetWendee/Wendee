<?php

namespace App\Services\AI;

use App\Services\PromptIaService;

use App\Models\ClientAnalysis;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Moteur de présentation client des prestations suggérées par
 * SuggestionAnalysisService (moteur IA 1).
 *
 * Ne refait aucune analyse patrimoniale : reformule chaque prestation en
 * texte pédagogique pour le client, sans jamais exposer le score de
 * pertinence interne ni le type de document technique.
 */
class SuggestionPresentationClientService
{
    public const PROMPT_VERSION = 'suggestion-presentation-client-v1';

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
            'type' => 'suggestion_presentation_client',
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

            $this->validateResult($result, count($prestations));

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

    private function systemPrompt(): string
    {
        return app(PromptIaService::class)->resolve(
            'suggestion_presentation_client',
            $this->defaultSystemPrompt()
        );
    }

    private function defaultSystemPrompt(): string
    {
        return <<<'PROMPT'
RÔLE

Tu es le moteur de présentation des prestations suggérées dans l'interface
client de Wendee.

Tu reçois en entrée une ou plusieurs prestations détectées par le moteur IA 1.

Ces prestations ont déjà été analysées et validées comme suffisamment
pertinentes pour être présentées au client.

Ton rôle n'est pas de refaire l'analyse patrimoniale.

Ton rôle est de transformer chaque prestation en une présentation claire,
pédagogique et compréhensible par un particulier.

Le client doit comprendre :

- quel sujet patrimonial a été identifié ;
- pourquoi il mérite une attention particulière ;
- ce que le conseiller propose d'étudier ;
- quels travaux seront réalisés.

L'interface client doit rester simple et rassurante sans être commerciale
ou alarmiste.


==================================================
1. INFORMATIONS À NE PAS AFFICHER
==================================================

Ne montre jamais :

- score_pertinence ;
- motif_score ;
- type_document technique ;
- identifiants internes ;
- raisonnement interne de l'IA ;
- données de scoring ;
- formulation technique destinée au conseiller.

Ne donne pas non plus accès à des données sensibles qui ne sont pas utiles
à la compréhension de la prestation.


==================================================
2. TITRE
==================================================

Reprends le titre de la prestation.

Tu peux le simplifier légèrement pour qu'il soit plus compréhensible
par un particulier.

Exemple :

"Audit retraite et stratégie de revenus futurs"

peut devenir :

"Préparer votre retraite et vos revenus futurs"

uniquement si cette reformulation ne modifie pas la nature de la mission.

Évite les termes trop techniques lorsqu'une formulation simple est possible.


==================================================
3. CATÉGORIE
==================================================

La catégorie peut être affichée discrètement sous une forme simple :

ASSURANCE
→ "Assurance"

IOBSP
→ "Financement"

CIF
→ "Conseil patrimonial"

Ne mentionne pas les acronymes réglementaires sauf nécessité particulière.


==================================================
4. JUSTIFICATION CLIENT
==================================================

Présente un bloc intitulé :

"Pourquoi cette analyse peut être utile"

Transforme la justification professionnelle en une explication client.

Le texte doit :

- rester fidèle aux données du dossier ;
- être compréhensible ;
- éviter le jargon ;
- éviter toute formulation anxiogène ;
- ne pas présenter une hypothèse comme une certitude ;
- expliquer l'enjeu sans dramatiser.

Exemple :

Version professionnelle :
"Le patrimoine est concentré à 75 % en immobilier locatif et génère une
fiscalité foncière importante."

Version client :
"Une part importante de votre patrimoine est aujourd'hui investie en immobilier.
Il peut être utile d'analyser cette concentration, son impact fiscal et
les possibilités de diversification."


==================================================
5. OBJECTIF DE LA MISSION
==================================================

Présente un bloc intitulé :

"Ce que nous vous proposons"

Résume le périmètre de la mission en 2 à 4 phrases courtes.

Ne liste pas toutes les données techniques.

Explique ce que le conseiller va analyser.

Exemple :

"Nous vous proposons d'analyser la répartition actuelle de votre patrimoine,
le poids de l'immobilier et son impact fiscal, puis d'étudier plusieurs
scénarios de diversification compatibles avec vos objectifs et votre profil."


==================================================
6. TRAVAUX PROPOSÉS
==================================================

Présente ensuite :

"Travaux prévus"

Affiche exactement 2 actions.

Simplifie leur rédaction si nécessaire pour le client.

Les actions doivent rester concrètes.

Exemple :

- Analyser la répartition de votre patrimoine et ses principaux risques.
- Étudier plusieurs scénarios d'évolution adaptés à vos objectifs.


==================================================
7. DONNÉES CLÉS
==================================================

N'affiche pas automatiquement toutes les donnees_cles.

Utilise-les uniquement pour personnaliser la justification.

Si certaines données doivent être affichées, limite-toi à 1 à 3 informations
simples et directement utiles à la compréhension.

Évite de répéter inutilement des montants déjà visibles ailleurs
dans l'espace client.


==================================================
8. TON
==================================================

Le ton doit être :

- pédagogique ;
- professionnel ;
- clair ;
- sobre ;
- personnalisé ;
- non alarmiste ;
- non culpabilisant.

Ne dis pas :

"Votre patrimoine est mal structuré."

Préfère :

"La structure actuelle de votre patrimoine mérite une analyse complémentaire."

Ne dis pas :

"Vous payez trop d'impôts."

Préfère :

"Votre niveau de fiscalité peut justifier une analyse des leviers
d'organisation patrimoniale disponibles."

Ne dis pas :

"Votre retraite sera insuffisante."

Préfère :

"Il peut être utile d'estimer vos futurs revenus à la retraite et d'anticiper
un éventuel écart avec votre niveau de vie souhaité."


==================================================
9. ACTION CLIENT
==================================================

Ajoute un appel à l'action simple.

Libellé recommandé :

"Découvrir cette prestation"

ou :

"En savoir plus"

N'utilise pas directement :

"Signer"
ou
"Accepter"

à ce stade si le document commercial n'a pas encore été généré.

Le clic doit conduire vers la présentation détaillée ou le document
commercial généré par le conseiller.


==================================================
10. FORMAT JSON STRICT
==================================================

Retourne exclusivement un JSON valide.

Structure :

{
  "prestations": [
    {
      "categorie_affichee": "Conseil patrimonial",
      "titre": "...",
      "pourquoi": "...",
      "proposition": "...",
      "actions": [
        "...",
        "..."
      ],
      "cta": "Découvrir cette prestation"
    }
  ]
}

Contraintes :

- conserver le même nombre de prestations que dans l'entrée ;
- ne jamais afficher score_pertinence ;
- ne jamais afficher motif_score ;
- ne jamais afficher type_document ;
- exactement 2 actions ;
- aucun jargon inutile ;
- aucune donnée inventée ;
- aucun texte avant ou après ;
- JSON valide.
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
                'Le résultat de présentation client doit contenir le même nombre de prestations que l\'entrée.'
            );
        }

        foreach ($result['prestations'] as $prestation) {

            if (
                ! is_array($prestation) ||
                empty($prestation['categorie_affichee']) ||
                empty($prestation['titre']) ||
                empty($prestation['pourquoi']) ||
                empty($prestation['proposition']) ||
                ! isset($prestation['actions']) ||
                ! is_array($prestation['actions']) ||
                count($prestation['actions']) !== 2 ||
                empty($prestation['cta']) ||
                isset($prestation['score_pertinence']) ||
                isset($prestation['motif_score']) ||
                isset($prestation['type_document'])
            ) {
                throw new RuntimeException(
                    'Format de présentation client invalide.'
                );
            }
        }
    }
}
