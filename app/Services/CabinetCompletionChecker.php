<?php

namespace App\Services;

use App\Models\CabinetProfile;
use App\Models\User;

class CabinetCompletionChecker
{
    /**
     * Retourne le détail des champs essentiels manquants, groupés par section.
     * Chaque item : ['label' => string, 'tab' => string|null, 'anchor' => string].
     */
    public static function status(CabinetProfile $cabinet, User $user): array
    {
        $identite = [];

        $identiteFields = [
            'nom_commercial' => ['Nom commercial', 'identification'],
            'raison_sociale' => ['Raison sociale', 'identification'],
            'forme_juridique' => ['Forme juridique', 'identification'],
            'numero_rcs' => ['Numéro RCS', 'identification'],
            'ville_rcs' => ['Ville RCS', 'identification'],
            'capital_social' => ['Capital social', 'identification'],
            'numero_tva' => ['N° TVA intracommunautaire', 'identification'],
            'logo' => ['Logo du cabinet', 'identification'],
            'adresse' => ['Adresse', 'coordonnees'],
            'code_postal' => ['Code postal', 'coordonnees'],
            'ville' => ['Ville', 'coordonnees'],
            'telephone' => ['Téléphone', 'coordonnees'],
            'email' => ['Email', 'coordonnees'],
            'numero_orias' => ['Numéro ORIAS', 'orias'],
        ];

        foreach ($identiteFields as $field => [$label, $tab]) {
            if (blank($cabinet->{$field})) {
                $identite[] = ['label' => $label, 'anchor' => 'informations-cabinet', 'tab' => $tab];
            }
        }

        if (empty($cabinet->statuts_reglementaires)) {
            $identite[] = ['label' => 'Statut ORIAS', 'anchor' => 'informations-cabinet', 'tab' => 'orias'];
        }

        $tarifs = [];

        if (blank($cabinet->mode_remuneration)) {
            $tarifs[] = ['label' => 'Mode de rémunération', 'anchor' => 'tarifs-cabinet', 'tab' => null];
        } elseif (in_array($cabinet->mode_remuneration, ['honoraires', 'honoraires_commissions'], true)) {
            $prestations = collect($cabinet->prestations ?? [])->filter(function ($prestation) {
                return filled($prestation['mode'] ?? null)
                    && (filled($prestation['forfait'] ?? null) || filled($prestation['pourcentage'] ?? null));
            });

            if ($prestations->isEmpty()) {
                $tarifs[] = ['label' => 'Au moins une prestation tarifée', 'anchor' => 'tarifs-cabinet', 'tab' => null];
            }
        }

        $objectifsData = $user->objectifs ?? [];

        $objectifsFields = [
            'client_semaine' => 'Clients / semaine',
            'rdv_semaine' => 'RDV / semaine',
            'collectes_semaine' => 'Collectes / semaine',
            'taux_transformation' => 'Taux de transformation',
            'revenu_mensuel' => 'Revenu mensuel',
            'revenu_annuel' => 'Revenu annuel',
        ];

        $objectifs = [];

        foreach ($objectifsFields as $field => $label) {
            if (blank($objectifsData[$field] ?? null)) {
                $objectifs[] = ['label' => $label, 'anchor' => 'objectifs-cabinet', 'tab' => null];
            }
        }

        return [
            'identite' => $identite,
            'tarifs' => $tarifs,
            'objectifs' => $objectifs,
            'complete' => empty($identite) && empty($tarifs) && empty($objectifs),
        ];
    }

    public static function isComplete(CabinetProfile $cabinet, User $user): bool
    {
        return self::status($cabinet, $user)['complete'];
    }
}
