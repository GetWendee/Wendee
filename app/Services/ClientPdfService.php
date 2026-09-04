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

    public function patrimoine(Client $client): array
    {
        $client->loadMissing(['patrimoineElements', 'patrimoineFiscalite', 'patrimoineObjectifs', 'conseiller']);

        $cabinet = CabinetProfile::query()->first();
        $conseiller = $client->conseiller;

        $analyse = $client->analyses()
            ->where('type', 'patrimoine')
            ->where('status', 'completed')
            ->latest('created_at')
            ->first();

        $resultat = $analyse?->result_json ?? [];

        $categories = ['actif_financier', 'actif_non_financier', 'passif', 'revenu', 'charge'];
        $elements = [];
        foreach ($categories as $categorie) {
            $elements[$categorie] = $client->patrimoineElements->where('categorie', $categorie)->values();
        }

        $data = $this->basePdfData($client, $cabinet, $conseiller);
        $data['lieuSignature'] = $client->patrimoineFiscalite?->lieu_signature ?: $cabinet?->ville;
        $data['fiscalite'] = $client->patrimoineFiscalite;
        $data['elements'] = $elements;
        $data['objectifs'] = $client->patrimoineObjectifs;
        $data['pointsForts'] = is_array($resultat['points_forts'] ?? null) ? $resultat['points_forts'] : [];
        $data['pointsAttention'] = is_array($resultat['points_attention'] ?? null) ? $resultat['points_attention'] : [];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('tenant.clients.pdf.patrimoine', $data);

        return [
            'pdf' => $pdf,
            'filename' => $this->nommerFichierPdf('Recueil patrimonial', $client),
        ];
    }

    public function profilInvestisseur(Client $client): array
    {
        $client->loadMissing(['profilInvestisseur', 'conseiller']);

        $cabinet = CabinetProfile::query()->first();
        $conseiller = $client->conseiller;

        $analyse = $client->analyses()
            ->where('type', 'profil_investisseur')
            ->where('status', 'completed')
            ->latest('created_at')
            ->first();

        $resultat = $analyse?->result_json ?? [];

        $data = $this->basePdfData($client, $cabinet, $conseiller);
        $data['lieuSignature'] = $cabinet?->ville;
        $data['profil'] = $client->profilInvestisseur;
        $data['pointsForts'] = is_array($resultat['points_forts'] ?? null) ? $resultat['points_forts'] : [];
        $data['pointsAttention'] = is_array($resultat['points_attention'] ?? null) ? $resultat['points_attention'] : [];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('tenant.clients.pdf.profil-investisseur', $data);

        return [
            'pdf' => $pdf,
            'filename' => $this->nommerFichierPdf('Profil investisseur', $client),
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
