<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Page /revenus/ : démo visuelle uniquement. Il n'existe aucun modèle de
 * tarifs de mission ni de revenu réel dans Wendee aujourd'hui (module
 * Offres / solutions jamais construit) — les montants ci-dessous sont
 * fictifs, générés de façon stable à partir des conseillers et clients
 * réels du cabinet (mêmes chiffres à chaque chargement, mais aucun lien
 * avec une vraie activité commerciale). À remplacer le jour où un vrai
 * modèle de tarifs/dossiers existe.
 */
class RevenuController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        abort_unless($user && $user->effectiveRole() === 'courtier', 403);

        $periode = in_array($request->query('periode'), ['mois', 'trimestre', 'annee'], true)
            ? $request->query('periode')
            : 'trimestre';

        $conseillers = collect([$user])->merge(
            User::query()->where('role', 'conseiller')->where('parent_id', $user->id)->orderBy('name')->get()
        );

        $clients = Client::query()
            ->whereIn('conseiller_id', $conseillers->pluck('id'))
            ->orderBy('nom')
            ->orderBy('prenom')
            ->get();

        $detailClients = $clients->map(function (Client $client) use ($conseillers) {
            $montants = $this->montantsFictifs($client->id);

            return [
                'client' => $client,
                'conseiller' => $conseillers->firstWhere('id', $client->conseiller_id),
                'mandat_courtage' => $montants['mandat_courtage'],
                'cif' => $montants['cif'],
                'cii' => $montants['cii'],
                'total' => $montants['mandat_courtage'] + $montants['cif'] + $montants['cii'],
            ];
        })->filter(fn (array $ligne) => $ligne['total'] > 0)->values();

        $revenuTotal = $detailClients->sum('total');
        $dossiers = $detailClients->count();
        $revenuMoyenDossier = $dossiers > 0 ? $revenuTotal / $dossiers : 0;

        $repartitionTypes = [
            'Mandat courtage' => $detailClients->sum('mandat_courtage'),
            'CIF' => $detailClients->sum('cif'),
            'CII' => $detailClients->sum('cii'),
        ];

        $classementConseillers = $conseillers->map(function (User $conseiller) use ($detailClients) {
            $lignes = $detailClients->filter(fn (array $l) => $l['conseiller']?->id === $conseiller->id);
            $revenu = $lignes->sum('total');
            $nbDossiers = $lignes->count();

            return [
                'conseiller' => $conseiller,
                'revenu' => $revenu,
                'dossiers' => $nbDossiers,
                'revenu_moyen' => $nbDossiers > 0 ? $revenu / $nbDossiers : 0,
            ];
        })->filter(fn (array $l) => $l['dossiers'] > 0)->sortByDesc('revenu')->values();

        $objectif = null;
        $objectifPct = null;
        $objectifsUtilisateur = $user->objectifs ?? [];

        if ($periode === 'annee' && ! empty($objectifsUtilisateur['revenu_annuel'])) {
            $objectif = (float) $objectifsUtilisateur['revenu_annuel'];
        } elseif (! empty($objectifsUtilisateur['revenu_mensuel'])) {
            $multiplicateur = $periode === 'trimestre' ? 3 : 1;
            $objectif = (float) $objectifsUtilisateur['revenu_mensuel'] * $multiplicateur;
        }

        if ($objectif !== null && $objectif > 0) {
            $objectifPct = round(($revenuTotal / $objectif) * 100, 1);
        }

        $evolution = $this->evolutionHebdomadaire($revenuTotal);

        return view('tenant.revenus.index', [
            'periode' => $periode,
            'revenuTotal' => $revenuTotal,
            'revenuMoyenDossier' => $revenuMoyenDossier,
            'objectifPct' => $objectifPct,
            'repartitionTypes' => $repartitionTypes,
            'classementConseillers' => $classementConseillers,
            'detailClients' => $detailClients,
            'evolution' => $evolution,
        ]);
    }

    /**
     * Montants fictifs stables pour un client donné (mêmes valeurs à
     * chaque chargement de page, sans dépendre d'un vrai historique).
     */
    private function montantsFictifs(int $clientId): array
    {
        $base = crc32($clientId . '-wendee-revenus-demo');

        $mandatCourtage = ($base % 4 !== 0) ? (($base % 18) * 150) + 200 : 0;
        $cif = ($base % 3 === 0) ? ((intdiv($base, 7)) % 14) * 400 : 0;
        $cii = ($base % 11 === 0) ? ((intdiv($base, 13)) % 6) * 250 : 0;

        return [
            'mandat_courtage' => (float) $mandatCourtage,
            'cif' => (float) $cif,
            'cii' => (float) $cii,
        ];
    }

    /**
     * Courbe hebdomadaire fictive sur 13 semaines, calée sur le total de
     * revenu affiché pour rester cohérente avec les autres chiffres.
     */
    private function evolutionHebdomadaire(float $total): array
    {
        $semaines = 13;
        $poids = [];

        for ($i = 0; $i < $semaines; $i++) {
            $poids[] = 0.4 + (sin(($i / ($semaines - 1)) * M_PI) * 0.9) + (($i % 3) * 0.08);
        }

        $sommePoids = array_sum($poids) ?: 1;
        $now = now();
        $points = [];

        foreach ($poids as $i => $p) {
            $semaine = $now->copy()->subWeeks($semaines - 1 - $i);

            $points[] = [
                'label' => 'S'.$semaine->isoFormat('WW'),
                'total' => round(($p / $sommePoids) * $total, 0),
            ];
        }

        return $points;
    }
}
