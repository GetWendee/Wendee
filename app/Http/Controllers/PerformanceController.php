<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientAnalysis;
use App\Models\PatrimoineElement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PerformanceController extends Controller
{
    /**
     * Nature (actif financier) -> famille d'allocation affichée.
     */
    private const BUCKETS_FINANCIER = [
        'compte_courant' => 'liquidites',
        'compte_sur_livret_csl' => 'liquidites',
        'livret_de_developpement_durable_et_solidaire' => 'liquidites',
        'livret_a' => 'liquidites',
        'livret_depargne_populaire_lep' => 'liquidites',
        'livrets_jeune' => 'liquidites',
        'cel' => 'liquidites',
        'compte_a_terme' => 'liquidites',
        'compte_courant_dassocies' => 'liquidites',
        'pel' => 'liquidites',
        'pep_bancaire' => 'liquidites',
        'autres_depots' => 'liquidites',
        'compte_titres' => 'pea_ct',
        'pea' => 'pea_ct',
        'peapme' => 'pea_ct',
        'parts_de_sofica' => 'pe_scpi',
        'parts_de_fcpi' => 'pe_scpi',
        'parts_de_fcpr' => 'pe_scpi',
        'parts_de_fip' => 'pe_scpi',
        'girardin_industrielle' => 'pe_scpi',
        'parts_de_holding' => 'professionnel',
        'autres_droits_sociaux' => 'professionnel',
        'contrat_dassurancevie_multisupports' => 'assurance_vie',
        'pep_assurance_vie_multisupports' => 'assurance_vie',
        'bons_contrats_de_capitalisation' => 'assurance_vie',
        'per' => 'epargne_retraite',
        'peepei' => 'epargne_retraite',
        'percopercoi' => 'epargne_retraite',
        'perp' => 'epargne_retraite',
        'contrat_loi_madelin' => 'epargne_retraite',
        'contrat_article_83' => 'epargne_retraite',
        'contrat_article_82' => 'epargne_retraite',
        'contrat_prefonretraite' => 'epargne_retraite',
        'autres_valeurs_mobilieres' => 'autres',
    ];

    /**
     * Nature (actif non financier) -> famille d'allocation affichée.
     */
    private const BUCKETS_NON_FINANCIER = [
        'bien_dusage' => 'immobilier',
        'residence_principale' => 'immobilier',
        'residence_secondaire' => 'immobilier',
        'terrain' => 'immobilier',
        'autre_bien_dusage' => 'immobilier',
        'immobilier_locatif_location_en_meublee_et_parts_de_sci' => 'immobilier',
        'immobilier_locatif_locatif' => 'immobilier',
        'location_meublee_professionnelle_lmp' => 'immobilier',
        'location_meublee_non_professionnelle_lmnp' => 'immobilier',
        'location_meublee_non_professionnelle_lmnp_loi_bouvard' => 'immobilier',
        'par_de_sci' => 'immobilier',
        'scpi' => 'pe_scpi',
        'parts_de_scpi' => 'pe_scpi',
        'bien_professionnels' => 'professionnel',
        'droits_sociaux' => 'professionnel',
        'entreprise_individuelle' => 'professionnel',
        'fonds_de_commerce_clienteles' => 'professionnel',
        'autres_biens_professionnels' => 'professionnel',
        'placement_foncier_et_divers' => 'autres',
        'part_de_groupements_forestiers' => 'autres',
        'bois_et_foret' => 'autres',
        'bien_ruraux_loues_a_long_terme' => 'autres',
        'parts_de_gfa_gaf_gfv_et_gfr' => 'autres',
        'parts_de_societe_depargne_forestiere' => 'autres',
        'objet_dart_et_dantiquites' => 'autres',
        'or_dematerialise_compte_titres' => 'autres',
        'or_physique_lingots_pieces_etc' => 'autres',
        'autres_placements_divers' => 'autres',
    ];

    private const LABELS_BUCKETS = [
        'immobilier' => 'Immobilier',
        'assurance_vie' => 'Assurance vie',
        'epargne_retraite' => 'Épargne retraite',
        'pea_ct' => 'PEA / Compte titres',
        'pe_scpi' => 'Private equity / SCPI',
        'professionnel' => 'Professionnel',
        'liquidites' => 'Liquidités',
        'autres' => 'Autres',
    ];

    public function index(Request $request): View
    {
        $user = $request->user();

        abort_unless($user && $user->effectiveRole() === 'courtier', 403);

        $periode = in_array($request->query('periode'), ['mois', 'trimestre', 'annee'], true)
            ? $request->query('periode')
            : 'trimestre';

        [$debut, $fin, $debutPrecedent, $finPrecedent] = $this->bornesPeriode($periode);

        $clients = Client::query()
            ->with(['kyc', 'profilInvestisseur', 'patrimoineElements', 'conseiller'])
            ->get();

        $clientIds = $clients->pluck('id');

        $patrimoine = PatrimoineElement::query()->whereIn('client_id', $clientIds)->get();

        $actifs = (float) $patrimoine->whereIn('categorie', ['actif_financier', 'actif_non_financier'])->sum('montant');
        $passifs = (float) $patrimoine->where('categorie', 'passif')->sum('montant');
        $solde = $actifs - $passifs;

        $clientsActifs = $clients->count();

        $clientsCreesPeriode = Client::query()->whereBetween('created_at', [$debut, $fin])->count();
        $clientsCreesPeriodePrecedente = Client::query()->whereBetween('created_at', [$debutPrecedent, $finPrecedent])->count();

        $evolutionClientsCrees = $clientsCreesPeriodePrecedente > 0
            ? round((($clientsCreesPeriode - $clientsCreesPeriodePrecedente) / $clientsCreesPeriodePrecedente) * 100, 1)
            : null;

        $auditsPeriode = ClientAnalysis::query()
            ->whereIn('client_id', $clientIds)
            ->where('type', 'recommandation')
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$debut, $fin])
            ->count();

        $auditsPeriodePrecedente = ClientAnalysis::query()
            ->whereIn('client_id', $clientIds)
            ->where('type', 'recommandation')
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$debutPrecedent, $finPrecedent])
            ->count();

        $evolutionAudits = $auditsPeriodePrecedente > 0
            ? round((($auditsPeriode - $auditsPeriodePrecedente) / $auditsPeriodePrecedente) * 100, 1)
            : null;

        $repartition = $this->repartitionAllocation($patrimoine);

        $conseillers = collect([$user])->merge(
            User::query()->where('role', 'conseiller')->where('parent_id', $user->id)->orderBy('name')->get()
        );

        $lignesConseillers = $conseillers->map(function (User $conseiller) use ($clients): array {
            $clientsConseiller = $clients->where('conseiller_id', $conseiller->id);
            $patrimoineConseiller = $clientsConseiller->flatMap->patrimoineElements;

            return [
                'conseiller' => $conseiller,
                'patrimoine_gere' => (float) $patrimoineConseiller
                    ->whereIn('categorie', ['actif_financier', 'actif_non_financier'])
                    ->sum('montant'),
                'clients' => $clientsConseiller->count(),
            ];
        })->sortByDesc('patrimoine_gere')->values();

        $limiteConformite = now()->subYear();

        $alertesConformite = $conseillers->map(function (User $conseiller) use ($clients, $limiteConformite): array {
            $clientsConseiller = $clients->where('conseiller_id', $conseiller->id);

            $kycExpires = $clientsConseiller->filter(function (Client $client) use ($limiteConformite) {
                $date = $client->kyc?->signe_le;

                return empty($date) || $date->lt($limiteConformite);
            })->count();

            $profilsARenouveler = $clientsConseiller->filter(function (Client $client) use ($limiteConformite) {
                $date = $client->profilInvestisseur?->signe_le;

                return empty($date) || $date->lt($limiteConformite);
            })->count();

            return [
                'conseiller' => $conseiller,
                'kyc_expires' => $kycExpires,
                'profils_a_renouveler' => $profilsARenouveler,
            ];
        })->sortByDesc(fn (array $ligne) => $ligne['kyc_expires'] + $ligne['profils_a_renouveler'])->values();

        return view('tenant.performances.index', [
            'periode' => $periode,
            'actifs' => $actifs,
            'passifs' => $passifs,
            'solde' => $solde,
            'clientsActifs' => $clientsActifs,
            'clientsCreesPeriode' => $clientsCreesPeriode,
            'evolutionClientsCrees' => $evolutionClientsCrees,
            'auditsPeriode' => $auditsPeriode,
            'evolutionAudits' => $evolutionAudits,
            'repartition' => $repartition,
            'lignesConseillers' => $lignesConseillers,
            'alertesConformite' => $alertesConformite,
        ]);
    }

    /**
     * Bornes [début, fin, début période précédente, fin période précédente]
     * pour le filtre mois / trimestre / année.
     */
    private function bornesPeriode(string $periode): array
    {
        $now = now();

        return match ($periode) {
            'mois' => [
                $now->copy()->startOfMonth(),
                $now->copy()->endOfMonth(),
                $now->copy()->subMonthNoOverflow()->startOfMonth(),
                $now->copy()->subMonthNoOverflow()->endOfMonth(),
            ],
            'annee' => [
                $now->copy()->startOfYear(),
                $now->copy()->endOfYear(),
                $now->copy()->subYear()->startOfYear(),
                $now->copy()->subYear()->endOfYear(),
            ],
            default => [
                $now->copy()->startOfQuarter(),
                $now->copy()->endOfQuarter(),
                $now->copy()->subQuarterNoOverflow()->startOfQuarter(),
                $now->copy()->subQuarterNoOverflow()->endOfQuarter(),
            ],
        };
    }

    /**
     * Répartition des actifs du cabinet par famille (Immobilier, Assurance
     * vie, Épargne retraite, PEA / Compte titres, Private equity / SCPI,
     * Professionnel, Liquidités, Autres), triée par montant décroissant.
     */
    private function repartitionAllocation($patrimoine): array
    {
        $actifs = $patrimoine->whereIn('categorie', ['actif_financier', 'actif_non_financier']);
        $total = (float) $actifs->sum('montant');

        $parBucket = $actifs->groupBy(function (PatrimoineElement $element) {
            $map = $element->categorie === 'actif_financier'
                ? self::BUCKETS_FINANCIER
                : self::BUCKETS_NON_FINANCIER;

            return $map[$element->nature] ?? 'autres';
        })->map(fn ($items) => (float) $items->sum('montant'));

        return collect(self::LABELS_BUCKETS)
            ->map(function (string $label, string $cle) use ($parBucket, $total): array {
                $montant = (float) ($parBucket[$cle] ?? 0);

                return [
                    'label' => $label,
                    'montant' => $montant,
                    'pct' => $total > 0 ? ($montant / $total) * 100 : 0,
                ];
            })
            ->filter(fn (array $bucket) => $bucket['montant'] > 0)
            ->sortByDesc('montant')
            ->values()
            ->all();
    }
}
