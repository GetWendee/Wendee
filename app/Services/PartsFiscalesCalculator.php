<?php

namespace App\Services;

use Illuminate\Support\Collection;

class PartsFiscalesCalculator
{
    /**
     * Calcule le nombre de parts fiscales (quotient familial) à partir de la situation
     * familiale et des personnes à charge du client.
     *
     * Règle appliquée (cas courants du quotient familial français) :
     * - 1 part par défaut, 2 parts si marié(e) ou pacsé(e).
     * - +0,5 part par enfant à charge pour les 2 premiers, +1 part à partir du 3e
     *   (divisé par 2 si garde alternée).
     * - +0,5 part supplémentaire pour le premier enfant à charge si le parent est isolé
     *   (célibataire, divorcé(e), séparé(e) ou veuf(ve) vivant seul avec ses enfants).
     * - +0,5 part par enfant à charge en situation d'invalidité (divisé par 2 si garde alternée).
     *
     * Non géré (données non collectées dans le KYC actuel, à ajuster manuellement si besoin) :
     * - invalidité du contribuable ou de son conjoint,
     * - conservation des parts du conjoint décédé (veuvage avec enfants issus du couple),
     * - demi-part ancien combattant.
     *
     * @param  Collection<int, \App\Models\ClientPersonneACharge>  $personnesACharge
     */
    public static function calculer(?string $situationFamiliale, Collection $personnesACharge): float
    {
        $enCouple = in_array($situationFamiliale, ['marie', 'pacse'], true);
        $isolePossible = in_array($situationFamiliale, ['celibataire', 'divorce', 'separe', 'veuf'], true);

        $parts = $enCouple ? 2.0 : 1.0;

        $enfantsACharge = $personnesACharge->filter(
            fn ($p) => $p->fiscalement_a_charge === 'oui'
        )->values();

        $isole = $isolePossible && $enfantsACharge->isNotEmpty();

        foreach ($enfantsACharge as $rangMoins1 => $enfant) {
            $rang = $rangMoins1 + 1;
            $alternee = $enfant->garde_alternee === 'oui';
            $diviseur = $alternee ? 2 : 1;

            $parts += ($rang <= 2 ? 0.5 : 1.0) / $diviseur;

            if ($rang === 1 && $isole) {
                $parts += 0.5 / $diviseur;
            }

            if ($enfant->invalidite === 'oui') {
                $parts += 0.5 / $diviseur;
            }
        }

        return $parts;
    }
}
