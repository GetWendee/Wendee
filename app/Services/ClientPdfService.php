<?php

namespace App\Services;

use App\Models\CabinetProfile;
use App\Models\Client;

class ClientPdfService
{
    public function kyc(Client $client): array
    {
        $client->loadMissing(['kyc', 'personnesACharge', 'conseiller']);

        $cabinet = CabinetProfile::query()->first();
        $conseiller = $client->conseiller;

        $analyse = $client->analyses()
            ->where('type', 'kyc')
            ->where('status', 'completed')
            ->latest('created_at')
            ->first();

        $resultat = $analyse?->result_json ?? [];

        $data = $this->basePdfData($client, $cabinet, $conseiller);
        $data['kyc'] = $client->kyc;
        $data['personnesACharge'] = $client->personnesACharge;
        $data['pointsForts'] = is_array($resultat['points_forts'] ?? null) ? $resultat['points_forts'] : [];
        $data['pointsAttention'] = is_array($resultat['points_attention'] ?? null) ? $resultat['points_attention'] : [];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('tenant.clients.pdf.kyc', $data);

        return [
            'pdf' => $pdf,
            'filename' => $this->nommerFichierPdf('Recueil de connaissance client', $client),
        ];
    }

    private function basePdfData(Client $client, ?CabinetProfile $cabinet, $conseiller): array
    {
        $nomClient = trim(
            ($client->civilite ? $client->civilite . ' ' : '')
            . $client->prenom . ' ' . $client->nom
        );

        return [
            'client' => $client,
            'cabinet' => $cabinet,
            'nomClient' => $nomClient,
            'nomConseiller' => $conseiller?->name ?? (auth()->check() ? auth()->user()->name : ($cabinet?->nom_commercial ?: '')),
            'telConseiller' => $conseiller?->telephone_mobile,
            'mailConseiller' => $conseiller?->email,
            'lieuSignature' => $client->kyc?->lieu_signature ?: $cabinet?->ville,
            'dateGeneration' => now()->translatedFormat('d F Y'),
            'fontRegular' => base_path('resources/fonts/Montserrat-Regular.ttf'),
            'fontMedium' => base_path('resources/fonts/Montserrat-Medium.ttf'),
            'fontSemiBold' => base_path('resources/fonts/Montserrat-SemiBold.ttf'),
            'fontBold' => base_path('resources/fonts/Montserrat-Bold.ttf'),
            'logoPath' => $cabinet?->logo ? storage_path('app/public/' . $cabinet->logo) : null,
        ];
    }

    private function nommerFichierPdf(string $libelle, Client $client): string
    {
        $nomClient = trim($client->prenom . ' ' . $client->nom);

        return $libelle . ' - ' . $nomClient . ' - ' . now()->format('d-m-Y') . '.pdf';
    }
}
