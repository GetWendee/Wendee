<?php

namespace App\Services\AI;

use App\Models\CabinetProfile;
use App\Models\Client;
use App\Models\ClientAnalysis;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class RecommandationAnalysisService
{
    public const PROMPT_VERSION = 'recommandation-v1';

    private const ANALYSIS_TYPES = [
        'kyc',
        'patrimoine',
        'profil_investisseur',
    ];

    public function analyze(Client $client, array $formInput): ClientAnalysis
    {
        $analyses = $this->getRequiredAnalyses($client);
        if ($analyses->count() !== 3) {
            throw new RuntimeException(
                'Les trois analyses KYC, Patrimoine et Profil investisseur sont obligatoires.'
            );
        }
        foreach (self::ANALYSIS_TYPES as $type) {
            $analysis = $analyses->get($type);
            if (
                ! $analysis
                || ! $analysis->completed_at
                || $analysis->completed_at->lt(now()->subYear())
            ) {
                throw new RuntimeException(
                    "L'analyse {$type} est absente ou âgée de plus d'un an."
                );
            }
        }

        $suggestion = $client->analyses()
            ->where('type', 'suggestion')
            ->where('status', 'completed')
            ->latest('completed_at')
            ->first();
        if (! $suggestion) {
            throw new RuntimeException(
                'Une suggestion de prestations doit être générée au préalable.'
            );
        }

        $cabinet = CabinetProfile::query()->first();
        $conseiller = $client->conseiller;
        $total = (float) ($formInput['total'] ?? 0);

        $input = [
            'client' => [
                'id' => $client->id,
                'prenom' => $client->prenom,
                'nom' => $client->nom,
            ],
            'kyc' => $analyses->get('kyc')->result_json,
            'patrimoine' => $analyses->get('patrimoine')->result_json,
            'profil_investisseur' => $analyses->get('profil_investisseur')->result_json,
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
            'type' => 'recommandation',
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
                ->post(
                    'https://api.openai.com/v1/chat/completions',
                    [
                        'model' => config('services.openai.model', 'gpt-4.1'),
                        'temperature' => 0.3,
                        'max_tokens' => 4096,
                        'messages' => [
                            [
                                'role' => 'system',
                                'content' => $this->systemPrompt($total),
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
            $raw = data_get($payload, 'choices.0.message.content');
            if (! is_string($raw) || trim($raw) === '') {
                throw new RuntimeException('Réponse OpenAI vide ou invalide.');
            }

            $analysis->update([
                'status' => 'completed',
                'result_json' => ['lettre_mission' => $raw, 'lettre_mission_html' => self::convertirMarkdownEnHtml($raw)],
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

    public static function convertirMarkdownEnHtml(string $texteBrut): string
    {
        $texteBrut = trim($texteBrut);
        $blocs = preg_split('/\n\s*\n/', $texteBrut);
        $html = '';
        foreach ($blocs as $bloc) {
            $bloc = trim($bloc);
            if ($bloc === '') {
                continue;
            }
            if (
                preg_match('/^\*{0,2}\s*(\d{1,2})\.\s*(.+?)\s*\*{0,2}$/u', $bloc, $m)
                && ! str_contains($bloc, "\n")
                && mb_strlen($m[2]) <= 120
            ) {
                $html .= '<h2 class="section-title">'
                    . e($m[1]) . '. ' . e($m[2]) . '</h2>';
                continue;
            }
            $ligne = e($bloc);
            $ligne = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $ligne);
            $ligne = nl2br($ligne);
            $html .= '<p class="corps-paragraphe">' . $ligne . '</p>';
        }
        return $html;
    }

    public static function enrichirTitresPourPdf(string $html): string
    {
        return preg_replace_callback(
            '/<h2([^>]*)>\s*(\d{1,2})\.\s*(.+?)\s*<\/h2>/u',
            function (array $m) {
                return '<h2 class="section-title"><span class="section-number">'
                    . $m[2] . '</span>' . $m[3] . '</h2>';
            },
            $html
        );
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
Tu dois rédiger le corps d'une lettre de mission, encadrant la relation commerciale entre le cabinet et son client.
Rédige exclusivement en français, quelle que soit la langue des données fournies.

=== PÉRIMÈTRE DE TA RÉDACTION ===

L'identification des parties (cabinet et client), l'en-tête, le pied de page et le bloc de signature sont générés automatiquement par le système. Tu ne les rédiges pas.

Tu rédiges uniquement le corps du document, à partir de la section 1 ci-dessous.

=== DONNÉES CLIENT ===

Tu disposes, au format JSON dans le message utilisateur :
- des données KYC
- des données patrimoniales
- du profil investisseur
- de la suggestion de prestations déjà établie pour ce client
- des missions retenues par le conseiller pour cette lettre de mission
- du contexte rédigé par le conseiller suite à l'échange avec son client

Le contexte conseiller, lorsqu'il est renseigné, constitue la grille de lecture principale de cette lettre de mission. Il prime sur toute interprétation générique et doit orienter concrètement la rédaction de l'objet, du périmètre et des livrables. En l'absence de contexte, la lettre s'appuie exclusivement sur les données structurées.

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
- le droit de rétractation si applicable

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
12. Droit de rétractation

=== DÉTAIL DES SECTIONS ===

**1. Statut et cadre réglementaire du conseiller**

Rappeler les habilitations réglementaires exercées dans le cadre de cette mission selon les missions retenues. Préciser que le conseiller agit en toute indépendance, sans lien capitalistique avec les établissements dont les solutions pourraient être évoquées.

**2. Objet de la mission**

Formuler l'objet précis de la mission à partir des données client et du contexte conseiller. L'objet doit refléter la situation réelle du client : projet identifié, besoins exprimés, axes de travail prioritaires. La formulation est précise et sans ambiguïté sur l'étendue des engagements.

**3. Périmètre de la mission**

Décrire les axes d'intervention couverts par la mission, structurés selon la nature des missions retenues :
- analyse de la situation civile et familiale
- analyse de la situation professionnelle si pertinent
- analyse patrimoniale et fiscale
- étude comparative des structures ou solutions envisagées le cas échéant

S'appuyer sur la suggestion de prestations et le contexte conseiller pour adapter le périmètre aux besoins identifiés. Préciser les exclusions explicites (gestion sous mandat, rédaction d'actes juridiques, expertise comptable ou fiscale réservée).

**4. Nature de l'intervention**

Préciser que la mission constitue une prestation d'analyse et d'assistance à la réflexion. Lister ce qu'elle n'emporte pas (gestion sous mandat, exécution d'opérations, rédaction d'actes, représentation auprès d'établissements). Mentionner la possibilité de recommander d'autres professionnels habilités si nécessaire.

**5. Livrables**

Décrire les documents remis à l'issue de la mission selon les missions retenues : synthèse patrimoniale, analyse comparative, préconisations argumentées, axes de réflexion. Préciser que le rapport ne constitue pas un acte juridique ou fiscal opposable.

**6. Missions complémentaires éventuelles**

Indiquer que toute mission complémentaire fera l'objet d'une lettre de mission distincte ou d'un avenant. Lister les cas typiques : accompagnement à la création de structure, coordination partenaires, suivi patrimonial, stratégie de transmission, missions réglementées.

**7. Honoraires**

Préciser le mode de rémunération applicable selon les missions retenues :
- honoraires forfaitaires ou au temps passé pour les missions de conseil
- commissions ou rétrocessions pour les missions d'intermédiation
- honoraires de transaction pour les missions immobilières

Le montant total à indiquer est : {$totalFormatted}.

**8. Responsabilité**

Rappeler que le conseiller est tenu à une obligation de moyens. Préciser les limites de responsabilité : informations inexactes transmises par le client, décisions prises par le client, évolutions réglementaires ou fiscales postérieures à la remise du rapport.

**9. Durée de la mission**

Pour une mission ponctuelle : préciser que la mission prend effet à la signature et s'achève à la remise du livrable.
Pour une mission continue : durée indéterminée avec tacite reconduction, résiliation par lettre recommandée avec préavis de 30 jours.

**10. Confidentialité et RGPD**

Rappeler la confidentialité des données transmises, la conformité au RGPD et à la loi Informatique et Libertés. Mentionner les droits du client sur ses données personnelles.

**11. Réclamations, médiation et litiges**

Indiquer la procédure de réclamation auprès du cabinet (délai de réponse : 2 mois maximum). Préciser les médiateurs compétents selon les missions exercées dans la mission.

**12. Droit de rétractation**

Si la mission résulte d'un acte de démarchage, mentionner le délai légal de rétractation de 14 jours calendaires à compter de la signature. Sinon, omettre cette section.

=== RÈGLES FONDAMENTALES ===

- Le document est un acte contractuel : chaque clause est précise et sans ambiguïté.
- Les informations issues des données client servent à personnaliser l'objet et le périmètre de la mission.
- Le montant des honoraires est {$totalFormatted}, jamais inventé ni modifié.
- Le document doit pouvoir être intégré tel quel dans une lettre de mission après complétion des seuls champs manquants.

=== STYLE ===

- Ton juridique et professionnel, niveau cabinet réglementé.
- Phrases claires, formulations contractuelles sans jargon excessif.
- Toujours désigner le client par « le Client » et le cabinet par « le Cabinet » ou « le Conseiller ».
- Sections numérotées avec titres apparents.

Rédige maintenant le corps de la lettre de mission en respectant strictement ces règles.
PROMPT;
    }
}
