<?php

namespace App\Services\AI;

use App\Models\CabinetProfile;
use App\Models\Client;
use App\Models\ClientAnalysis;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Lettre de mission pour une personne morale (société). Service
 * entièrement séparé de RecommandationAnalysisService (personne
 * physique). Réutilise uniquement le convertisseur Markdown -> HTML
 * partagé (utilitaire pur, sans logique métier). Voir
 * claude/kyc-personne-morale.md.
 */
class RecommandationAnalysisServiceMorale
{
    public const PROMPT_VERSION = 'recommandation-morale-v1';

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

        $suggestion = $client->analyses()
            ->where('type', 'suggestion_morale')
            ->where('status', 'completed')
            ->latest('completed_at')
            ->first();
        if (! $suggestion) {
            throw new RuntimeException('Une suggestion de prestations doit être générée au préalable.');
        }

        $cabinet = CabinetProfile::query()->first();
        $conseiller = $client->conseiller;
        $total = (float) ($formInput['total'] ?? 0);

        $input = [
            'societe' => [
                'id' => $client->id,
                'raison_sociale' => $client->raison_sociale,
                'forme_juridique' => $client->forme_juridique,
            ],
            'kyc' => $analyses->get('kyc_morale')->result_json,
            'patrimoine' => $analyses->get('patrimoine_morale')->result_json,
            'profil_investisseur' => $analyses->get('profil_investisseur_morale')->result_json,
            'suggestion' => $suggestion->result_json,
            'contexte_conseiller' => $formInput['contexte'] ?? '',
            'missions_retenues' => $formInput['missions'] ?? [],
            'total_forfait_final' => $total,
            'cabinet' => [
                'raison_sociale' => $cabinet?->raison_sociale,
                'adresse' => $cabinet?->adresse,
                'code_postal' => $cabinet?->code_postal,
                'ville' => $cabinet?->ville,
                'numero_orias' => $cabinet?->numero_orias,
                'statuts_reglementaires' => $cabinet?->statuts_reglementaires,
                'mediateur_nom' => $cabinet?->mediateur_nom,
                'mediateur_contact' => $cabinet?->mediateur_contact,
            ],
            'conseiller' => [
                'nom' => $conseiller?->name,
                'telephone' => $conseiller?->telephone_mobile,
            ],
        ];

        $analysis = ClientAnalysis::create([
            'client_id' => $client->id,
            'type' => 'recommandation_morale',
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
                        ['role' => 'system', 'content' => $this->systemPrompt($total)],
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
                    'lettre_mission' => $raw,
                    'lettre_mission_html' => RecommandationAnalysisService::convertirMarkdownEnHtml($raw),
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

    private function systemPrompt(float $total): string
    {
        $totalFormatted = number_format($total, 2, ',', ' ') . ' €';

        return <<<PROMPT
Tu es juriste spécialisé en droit des services financiers et immobiliers, au sein d'un cabinet de conseil en gestion de patrimoine.
Tu interviens dans un cadre réglementaire strict (AMF, ACPR, DDA, MIF2, ORIAS, loi Hoguet).
Tu dois rédiger le corps d'une lettre de mission, encadrant la relation commerciale entre le cabinet et une société cliente (personne morale), représentée par son dirigeant ou son représentant légal.
Rédige exclusivement en français, quelle que soit la langue des données fournies.

=== PÉRIMÈTRE DE TA RÉDACTION ===

L'identification des parties (cabinet et société cliente), l'en-tête, le pied de page et le bloc de signature sont générés automatiquement par le système. Tu ne les rédiges pas.

Tu rédiges uniquement le corps du document, à partir de la section 1 ci-dessous.

=== DONNÉES CLIENT ===

Tu disposes, au format JSON dans le message utilisateur :
- des données KYC de la société
- des données patrimoniales de la société
- du profil investisseur de la société
- de la suggestion de prestations déjà établie
- des missions retenues par le conseiller pour cette lettre de mission
- du contexte rédigé par le conseiller suite à l'échange avec le dirigeant

Le contexte conseiller, lorsqu'il est renseigné, constitue la grille de lecture principale de cette lettre de mission. Il prime sur toute interprétation générique. En l'absence de contexte, la lettre s'appuie exclusivement sur les données structurées.

Les missions retenues déterminent la nature juridique du document. Elles peuvent être :
- Mandat de courtage, assurance banque
- Conseils en investissement financier
- Conseils en investissement immobilier

Si une donnée est absente, considère-la comme non fournie et rédige la clause de façon générale, sans inventer d'information.

=== OBJECTIF DU DOCUMENT ===

Rédiger le corps d'une lettre de mission professionnelle, claire et conforme aux exigences réglementaires, formalisant :
- le statut et cadre réglementaire du conseiller
- l'objet précis de la mission
- le périmètre et les axes d'intervention
- la nature de l'intervention et ses limites
- les livrables attendus
- les missions complémentaires éventuelles
- les honoraires
- la responsabilité
- la durée
- la confidentialité et le RGPD
- les réclamations, médiation et litiges

Longueur cible : 800 à 1200 mots.

=== STRUCTURE OBLIGATOIRE ===

1. Statut et cadre réglementaire du conseiller
2. Objet de la mission
3. Périmètre de la mission
4. Nature de l'intervention
5. Livrables
6. Missions complémentaires éventuelles
7. Honoraires
8. Responsabilité
9. Durée de la mission
10. Confidentialité et RGPD
11. Réclamations, médiation et litiges

=== DÉTAIL DES SECTIONS ===

**1. Statut et cadre réglementaire du conseiller**

Rappeler les habilitations réglementaires exercées dans le cadre de cette mission selon les missions retenues. Préciser que le conseiller agit en toute indépendance, sans lien capitalistique avec les établissements dont les solutions pourraient être évoquées.

**2. Objet de la mission**

Formuler l'objet précis de la mission à partir des données de la société et du contexte conseiller. L'objet doit refléter la situation réelle de la société : activité, projet identifié, besoins exprimés, axes de travail prioritaires.

**3. Périmètre de la mission**

Décrire les axes d'intervention couverts par la mission, structurés selon la nature des missions retenues :
- analyse de la situation juridique et de gouvernance de la société
- analyse de l'activité et des chiffres clés si pertinent
- analyse patrimoniale et fiscale de la société
- étude comparative des structures ou solutions envisagées le cas échéant

S'appuyer sur la suggestion de prestations et le contexte conseiller pour adapter le périmètre aux besoins identifiés. Préciser les exclusions explicites (gestion sous mandat, rédaction d'actes juridiques, expertise comptable ou fiscale réservée).

**4. Nature de l'intervention**

Préciser que la mission constitue une prestation d'analyse et d'assistance à la réflexion. Lister ce qu'elle n'emporte pas (gestion sous mandat, exécution d'opérations, rédaction d'actes, représentation auprès d'établissements).

**5. Livrables**

Décrire les documents remis à l'issue de la mission selon les missions retenues : synthèse patrimoniale de la société, analyse comparative, préconisations argumentées, axes de réflexion.

**6. Missions complémentaires éventuelles**

Indiquer que toute mission complémentaire fera l'objet d'une lettre de mission distincte ou d'un avenant.

**7. Honoraires**

Préciser le mode de rémunération applicable selon les missions retenues. Le montant total à indiquer est : {$totalFormatted}.

**8. Responsabilité**

Rappeler que le conseiller est tenu à une obligation de moyens. Préciser les limites de responsabilité : informations inexactes transmises par la société, décisions prises par ses dirigeants, évolutions réglementaires ou fiscales postérieures.

**9. Durée de la mission**

Pour une mission ponctuelle : préciser que la mission prend effet à la signature et s'achève à la remise du livrable.
Pour une mission continue : durée indéterminée avec tacite reconduction, résiliation par lettre recommandée avec préavis de 30 jours.

**10. Confidentialité et RGPD**

Rappeler la confidentialité des données transmises, la conformité au RGPD et à la loi Informatique et Libertés.

**11. Réclamations, médiation et litiges**

Indiquer la procédure de réclamation auprès du cabinet (délai de réponse : 2 mois maximum). Préciser les médiateurs compétents selon les missions exercées.

=== RÈGLES FONDAMENTALES ===

- Le document est un acte contractuel : chaque clause est précise et sans ambiguïté.
- Le montant des honoraires est {$totalFormatted}, jamais inventé ni modifié.
- Le document doit pouvoir être intégré tel quel dans une lettre de mission après complétion des seuls champs manquants.

=== STYLE ===

- Ton juridique et professionnel, niveau cabinet réglementé.
- Toujours désigner la société cliente par « le Client » ou « la Société » et le cabinet par « le Cabinet » ou « le Conseiller ».
- Sections numérotées avec titres apparents.

Rédige maintenant le corps de la lettre de mission en respectant strictement ces règles.
PROMPT;
    }
}
