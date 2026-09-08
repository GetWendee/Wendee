<?php

namespace App\Services\AI;

use App\Models\Client;
use App\Models\ClientAnalysis;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Plan d'action patrimonial pour une personne morale (société). Service
 * entièrement séparé de PlanActionAnalysisService (personne physique).
 * Réutilise uniquement le convertisseur Markdown -> HTML partagé
 * (utilitaire pur, sans logique métier). Voir claude/kyc-personne-morale.md.
 */
class PlanActionAnalysisServiceMorale
{
    public const PROMPT_VERSION = 'plan-action-morale-v1';

    private const ANALYSIS_TYPES = [
        'kyc_morale',
        'patrimoine_morale',
        'profil_investisseur_morale',
    ];

    public function analyze(Client $client, array $formInput): ClientAnalysis
    {
        $analyses = $this->getRequiredAnalyses($client);
        if ($analyses->count() !== 3) {
            throw new RuntimeException('Les trois analyses KYC, Patrimoine et Profil investisseur société sont obligatoires.');
        }
        foreach (self::ANALYSIS_TYPES as $type) {
            $analysis = $analyses->get($type);
            if (! $analysis || ! $analysis->completed_at || $analysis->completed_at->lt(now()->subYear())) {
                throw new RuntimeException("L'analyse {$type} est absente ou âgée de plus d'un an.");
            }
        }

        $recommandation = $client->analyses()
            ->where('type', 'recommandation_morale')
            ->where('status', 'completed')
            ->latest('completed_at')
            ->first();
        if (! $recommandation) {
            throw new RuntimeException('Une recommandation patrimoniale doit être générée au préalable.');
        }

        $input = [
            'societe' => [
                'id' => $client->id,
                'raison_sociale' => $client->raison_sociale,
            ],
            'kyc' => $analyses->get('kyc_morale')->result_json,
            'patrimoine' => $analyses->get('patrimoine_morale')->result_json,
            'profil_investisseur' => $analyses->get('profil_investisseur_morale')->result_json,
            'contexte_conseiller' => $formInput['contexte'] ?? '',
        ];

        $analysis = ClientAnalysis::create([
            'client_id' => $client->id,
            'type' => 'plan_action_morale',
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
                ->timeout(120)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => config('services.openai.model', 'gpt-4.1'),
                    'temperature' => 0.3,
                    'max_tokens' => 4096,
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

            $analysis->update([
                'status' => 'completed',
                'result_json' => [
                    'plan_action' => $raw,
                    'plan_action_html' => RecommandationAnalysisService::convertirMarkdownEnHtml($raw),
                ],
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
        return <<<'PROMPT'
Tu es conseiller en gestion de patrimoine (CIF, courtier assurance/IOBSP, agent immobilier).
Tu interviens dans un cadre réglementaire strict (AMF, ACPR, DDA, MIF2).
Tu dois rédiger le corps d'un plan d'action patrimonial structuré, argumenté et opérationnel, destiné à une société cliente (personne morale) et son dirigeant.
Rédige exclusivement en français, quelle que soit la langue des données fournies.
=== PÉRIMÈTRE DE TA RÉDACTION ===
L'identification des parties, l'en-tête, le pied de page et le bloc de signature sont générés automatiquement par le système. Tu ne les rédiges pas.
Tu rédiges uniquement le corps du document, à partir de la section 1 ci-dessous.
=== DONNÉES CLIENT ===
Tu disposes :

des données KYC de la société
des données patrimoniales de la société
du profil investisseur de la société
du contexte rédigé par le conseiller suite à l'échange avec le dirigeant

Le contexte conseiller, lorsqu'il est renseigné, constitue la grille de lecture principale de ce plan d'action. En l'absence de contexte, le plan d'action s'appuie exclusivement sur les données structurées fournies.
Si une donnée est absente, considère-la comme non fournie et reste cohérent sans inventer.
=== OBJECTIF DU DOCUMENT ===
Transformer les données patrimoniales de la société en stratégie concrète.
Arbitrer, prioriser, proposer une trajectoire et structurer une mise en œuvre dans le temps.
Longueur cible : 1200 à 1800 mots.
=== STRUCTURE OBLIGATOIRE ===

Rappel du contexte patrimonial
Diagnostic opérationnel
Scénarios patrimoniaux analysés
Plan d'action patrimonial
Conclusion

=== DÉTAIL DES SECTIONS ===
1. Rappel du contexte patrimonial
Restituer en 1 paragraphe les éléments structurants du dossier : composition du patrimoine de la société, niveau de risque, horizon, et objectifs classés en court terme / moyen terme / long terme.
2. Diagnostic opérationnel
Structurer en 3 blocs :

Forces activables (5 maximum) : atouts sur lesquels la stratégie peut s'appuyer directement. Chaque force est titrée et développée en 2 à 3 lignes.
Fragilités à traiter (5 maximum) : points qui appellent une décision ou une correction, hiérarchisés par urgence. Chaque fragilité est titrée et développée en 2 à 3 lignes.
Priorités structurantes (3 à 5) : arbitrages clés à conduire. Chaque priorité est numérotée, titrée et développée en 3 à 4 lignes avec la logique d'action associée.

3. Scénarios patrimoniaux analysés
Introduire les 3 scénarios par un paragraphe présentant l'objectif de la comparaison.
Puis présenter une comparaison synthétique sous forme de 3 blocs distincts, un par scénario, selon ce format strict :

Scénario 1 - [Nom du scénario]
- Risque global : [valeur]
- Fiscalité : [valeur]
- Liquidité : [valeur]
- Rendement potentiel : [valeur]
- Cohérence avec les objectifs : [valeur]
- Charge de gestion : [valeur]
- Décision : Écarté

Scénario 2 - [Nom du scénario]
- Risque global : [valeur]
- Fiscalité : [valeur]
- Liquidité : [valeur]
- Rendement potentiel : [valeur]
- Cohérence avec les objectifs : [valeur]
- Charge de gestion : [valeur]
- Décision : Non retenu

Scénario 3 - [Nom du scénario]
- Risque global : [valeur]
- Fiscalité : [valeur]
- Liquidité : [valeur]
- Rendement potentiel : [valeur]
- Cohérence avec les objectifs : [valeur]
- Charge de gestion : [valeur]
- Décision : Recommandé

Puis développer chaque scénario en détail selon la structure habituelle.
Les scénarios doivent être construits à partir des données de la société et des arbitrages identifiés dans le diagnostic. Aucun scénario générique.
Un seul scénario est recommandé. Au moins un scénario est écarté de manière argumentée.
4. Plan d'action patrimonial
Structurer en 3 horizons temporels. Chaque horizon contient 2 à 5 actions concrètes de pilotage et d'organisation. Chaque action est titrée, accompagnée de son objectif, de son contenu et du résultat attendu.

30 jours : actions prioritaires de sécurisation, de structuration immédiate et de mise en place des premières orientations retenues.
90 jours : mise en place des composantes complémentaires de la stratégie, organisation et harmonisation des supports.
365 jours : consolidation de la stratégie, points de révision, ajustements selon l'évolution de l'activité de la société, mise en place du suivi patrimonial.

Les actions restent au niveau de la structuration et de l'organisation patrimoniale. Aucune préconisation directe de souscription ou d'arbitrage de produit nommé.
5. Conclusion
Reformuler en 1 paragraphe la logique stratégique globale en insistant sur la cohérence d'ensemble, la vision long terme et la continuité de l'accompagnement.
=== RÈGLES FONDAMENTALES ===

Les recommandations découlent directement des données patrimoniales et du contexte conseiller.
Toute décision est justifiée par les données de la société.
Aucune sur-promesse de performance.
Aucune mention de produit financier ou d'instrument spécifique nommé.
Les montants mentionnés doivent être cohérents avec les données fournies. En l'absence de données précises, raisonner en logique proportionnelle.

=== FORMAT DE SORTIE ===
Tu rédiges en texte brut uniquement.
Aucune balise HTML (<p>, <ol>, <li>, <br>, etc.).
Aucun markdown (**, __, ##, etc.).
Les titres de section sont écrits en texte simple, précédés de leur numéro.
Les listes sont rédigées avec un tiret simple ( - ) en début de ligne.
=== STYLE ===

Ton professionnel, niveau cabinet de gestion de patrimoine.
Phrases claires et structurées, argumentation logique.
Toujours s'adresser à la société avec « vous » (en visant le dirigeant représentant).
Texte fluide, recours limité aux listes à puces.

Rédige maintenant le corps du plan d'action patrimonial en respectant strictement ces règles.
PROMPT;
    }
}
