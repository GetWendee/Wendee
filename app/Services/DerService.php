<?php

namespace App\Services;

use App\Models\CabinetProfile;
use App\Models\Client;
use Barryvdh\DomPDF\Facade\Pdf;

class DerService
{
    private const LABELS_STATUT = [
        'ias_courtier' => 'Courtier en assurances (IAS - courtier)',
        'ias_mandataire' => "Mandataire d'assurance (IAS - mandataire)",
        'ias_mia' => "Mandataire d'intermédiaire d'assurance (MIA)",
        'iobsp_courtier' => 'Courtier en opérations de banque et services de paiement (IOBSP - courtier)',
        'iobsp_mandataire' => 'Mandataire IOBSP',
        'iobsp_mandataire_non_exclusif' => 'Mandataire non exclusif IOBSP',
        'iobsp_mandataire_exclusif' => 'Mandataire exclusif IOBSP',
        'cif' => 'Conseiller en investissements financiers (CIF)',
        'agent_immobilier' => 'Agent immobilier',
        'mandataire_agent_immobilier' => "Mandataire d'agent immobilier",
    ];

    private const LABELS_ZONE = [
        'france' => 'France uniquement',
        'ue' => 'Union européenne',
        'europe' => 'Europe (UE + hors UE)',
        'hors_usa' => 'Monde (hors USA / Canada)',
        'monde' => 'Monde entier',
    ];

    public static function statutOriasPhrase(?CabinetProfile $cabinet): string
    {
        $statuts = $cabinet?->statuts_reglementaires ?? [];

        $labels = array_values(array_filter(array_map(
            fn (string $statut) => self::LABELS_STATUT[$statut] ?? null,
            $statuts
        )));

        return $labels ? implode(', ', $labels).'.' : '-';
    }

    public static function zoneCouvertureLabel(?string $zone): string
    {
        return self::LABELS_ZONE[$zone] ?? '-';
    }

    public static function genererPdf(Client $client)
    {
        $cabinet = CabinetProfile::query()->first();

        $statuts = $cabinet->statuts_reglementaires ?? [];

        $nomClient = trim(
            ($client->civilite ? $client->civilite.' ' : '')
            .$client->prenom.' '.$client->nom
        );

        $data = [
            'client' => $client,
            'cabinet' => $cabinet,
            'nomClient' => $nomClient,
            'lieuSignature' => $cabinet?->ville,
            'dateGeneration' => now()->translatedFormat('d F Y'),
            'statutOrias' => self::statutOriasPhrase($cabinet),
            'zoneCouverture' => self::zoneCouvertureLabel($cabinet?->assurance_zone_couverture),
            'statutContientCif' => in_array('cif', $statuts, true),
            'statutContientAssurance' => (bool) array_intersect(['ias_courtier', 'ias_mandataire', 'ias_mia'], $statuts),
            'statutContientIobsp' => (bool) array_intersect(
                ['iobsp_courtier', 'iobsp_mandataire', 'iobsp_mandataire_non_exclusif', 'iobsp_mandataire_exclusif'],
                $statuts
            ),
            'statutContientImmobilier' => (bool) array_intersect(['agent_immobilier', 'mandataire_agent_immobilier'], $statuts),
            'fontRegular' => base_path('resources/fonts/Montserrat-Regular.ttf'),
            'fontMedium' => base_path('resources/fonts/Montserrat-Medium.ttf'),
            'fontSemiBold' => base_path('resources/fonts/Montserrat-SemiBold.ttf'),
            'fontBold' => base_path('resources/fonts/Montserrat-Bold.ttf'),
            'logoPath' => $cabinet?->logo ? storage_path('app/public/'.$cabinet->logo) : null,
        ];

        return Pdf::loadView('tenant.clients.pdf.der', $data);
    }
}
