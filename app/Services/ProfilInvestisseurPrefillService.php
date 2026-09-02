<?php

namespace App\Services;

use App\Models\Client;
use Illuminate\Support\Carbon;

class ProfilInvestisseurPrefillService
{
    /**
     * Calcule les réponses "risque_5" à "risque_11" (radios Revenus/Épargne/Patrimoine)
     * à partir des données déjà saisies dans le module Patrimoine du client.
     *
     * @return array<string, string|null>
     */
    public static function calculer(Client $client): array
    {
        $elements = $client->patrimoineElements;

        $normaliserAnnuel = fn ($els) => (float) $els->sum(
            fn ($e) => (float) $e->montant * ($e->periodicite === 'mensuel' ? 12 : 1)
        );
        $normaliserMensuel = fn ($els) => (float) $els->sum(
            fn ($e) => (float) $e->montant * ($e->periodicite === 'mensuel' ? 1 : (1 / 12))
        );

        $revenus = $elements->where('categorie', 'revenu');
        $charges = $elements->where('categorie', 'charge');
        $chargesMensualitesCredits = $charges->where('nature', 'mensualites_credits');
        $chargesHorsCredits = $charges->where('nature', '!=', 'mensualites_credits');

        $totalRevenusAnnuel = $normaliserAnnuel($revenus);
        $totalChargesAnnuel = $normaliserAnnuel($charges);
        $totalActifsFinanciers = (float) $elements->where('categorie', 'actif_financier')->sum('montant');
        $totalActifsNonFinanciers = (float) $elements->where('categorie', 'actif_non_financier')->sum('montant');

        return [
            'risque_5_profil_investisseur' => self::bucket($totalRevenusAnnuel, [
                [25000, 'inferieur_a_25000_euros'],
                [50000, 'entre_25000_euros_et_50000_euros'],
                [75000, 'entre_50000_euros_et_75000_euros'],
                [100000, 'entre_75000_euros_et_100000_euros'],
                [150000, 'entre_100000_euros_et_150000_euros'],
                [300000, 'entre_150000_euros_et_300000_euros'],
                [null, 'plus_de_300000_euros'],
            ]),
            'risque_6_profil_investisseur' => self::bucket(
                round(($totalRevenusAnnuel - $totalChargesAnnuel) / 12, 2),
                [
                    [0, 'je_n_epargne_pas'],
                    [500, 'entre_0_et_500_euros'],
                    [1000, 'entre_500_et_1000_euros'],
                    [2000, 'entre_1000_et_2000_euros'],
                    [null, 'plus_de_2000_euros'],
                ]
            ),
            'risque_7_profil_investisseur' => self::bucket($totalActifsNonFinanciers, [
                [0, 'je_n_ai_pas_de_patrimoine_immobilier'],
                [100000, 'moins_de_100000_euros'],
                [300000, 'entre_100000_et_300000_euros'],
                [500000, 'entre_300000_et_500000_euros'],
                [1000000, 'entre_500000_et_1000000_euros'],
                [null, 'plus_de_1000000_euros'],
            ]),
            'risque_8_profil_investisseur' => self::bucket($totalActifsFinanciers, [
                [20000, 'estim_patrimoine_financier_20'],
                [50000, 'estim_patrimoine_financier_50'],
                [200000, 'estim_patrimoine_financier_200'],
                [null, 'estim_patrimoine_financier_plus200'],
            ]),
            'risque_9_profil_investisseur' => self::bucket($normaliserMensuel($chargesMensualitesCredits), [
                [0, 'je_ne_suis_pas_endette'],
                [500, 'moins_de_500_euros'],
                [1000, 'entre_500_et_1000_euros'],
                [2000, 'entre_1000_et_2000_euros'],
                [null, 'plus_de_2000_euros'],
            ]),
            'risque_10_profil_investisseur' => self::bucket($normaliserMensuel($chargesHorsCredits), [
                [1000, 'moins_de_1000_euros'],
                [2000, 'entre_1000_et_2000_euros'],
                [5000, 'entre_2000_et_5000_euros'],
                [null, 'plus_de_5000_euros'],
            ]),
            'risque_11_profil_investisseur' => self::logementPrincipal($elements),
        ];
    }

    /**
     * @param array<int, array{0: int|float|null, 1: string}> $paliers Liste ordonnée croissante,
     *        chaque palier étant [seuil_max_inclus, valeur]. Le dernier palier doit avoir un seuil null.
     */
    private static function bucket(float $montant, array $paliers): string
    {
        foreach ($paliers as [$seuil, $valeur]) {
            if ($seuil === null || $montant <= $seuil) {
                return $valeur;
            }
        }

        return end($paliers)[1];
    }

    private static function logementPrincipal($elements): ?string
    {
        $residencePrincipale = $elements
            ->where('categorie', 'actif_non_financier')
            ->firstWhere('nature', 'residence_principale');

        if (! $residencePrincipale) {
            return 'locataire_heberge_a_titre_gratuit';
        }

        $emprunt = $elements
            ->where('categorie', 'passif')
            ->firstWhere('nature', 'emprunt_sur_residence_principale');

        if (! $emprunt || ! $emprunt->duree || ! $emprunt->date_souscription) {
            return 'proprietaire_sans_remboursement_d_emprunt';
        }

        $finEmprunt = $emprunt->date_souscription->copy()->addYears((int) $emprunt->duree);

        if ($finEmprunt->isPast()) {
            return 'proprietaire_sans_remboursement_d_emprunt';
        }

        $anneesRestantes = Carbon::now()->diffInYears($finEmprunt);

        return $anneesRestantes >= 5
            ? 'proprietaire_et_mon_emprunt_finit_dans_plus_de_5_ans'
            : 'proprietaire_et_mon_emprunt_finit_dans_moins_de_5_ans';
    }
}
