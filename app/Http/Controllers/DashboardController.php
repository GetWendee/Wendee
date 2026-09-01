<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\PatrimoineElement;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        /*
         * Le dashboard est celui du conseiller connecté.
         *
         * Le courtier possède également la casquette de conseiller :
         * ses propres clients sont donc ceux dont conseiller_id = son id.
         */
        $clients = Client::query()
            ->where('conseiller_id', $user->id)
            ->with([
                'kyc',
                'profilInvestisseur',
                'patrimoineElements',
            ])
            ->latest('updated_at')
            ->get();

        $nombreClients = $clients->count();

        /*
         * Les éléments de patrimoine sont déjà isolés dans la base tenant.
         * On limite néanmoins le calcul aux clients du conseiller.
         */
        $clientIds = $clients->pluck('id');

        $patrimoine = PatrimoineElement::query()
            ->whereIn('client_id', $clientIds)
            ->get();

        $actifsFinanciers = (float) $patrimoine
            ->where('categorie', 'actif_financier')
            ->sum('montant');

        $actifsNonFinanciers = (float) $patrimoine
            ->where('categorie', 'actif_non_financier')
            ->sum('montant');

        $passifs = (float) $patrimoine
            ->where('categorie', 'passif')
            ->sum('montant');

        $revenus = (float) $patrimoine
            ->where('categorie', 'revenu')
            ->sum('montant');

        $charges = (float) $patrimoine
            ->where('categorie', 'charge')
            ->sum('montant');

        $actifs = $actifsFinanciers + $actifsNonFinanciers;
        $patrimoineNet = $actifs - $passifs;
        $soldeAnnuel = $revenus - $charges;

        /*
         * État d'avancement de chaque dossier.
         */
        $suiviClients = $clients->map(function (Client $client): array {
            $limite = now()->subYear();

            $kycDate = $client->kyc?->signe_le;
            $kycComplet = ! empty($kycDate) && $kycDate->gte($limite);

            $dernierPatrimoine = $client->patrimoineElements
                ->sortByDesc('updated_at')
                ->first();

            $patrimoineDate = $dernierPatrimoine?->updated_at;
            $patrimoineComplet = $dernierPatrimoine
                && $patrimoineDate
                && $patrimoineDate->gte($limite);

            $profilDate = $client->profilInvestisseur?->signe_le;
            $profilComplet = ! empty($profilDate) && $profilDate->gte($limite);

            $anomalies = collect();

            if (! $kycComplet) {
                $anomalies->push([
                    'type' => 'KYC',
                    'libelle' => empty($kycDate) ? 'KYC incomplet' : 'KYC à actualiser',
                    'detail' => empty($kycDate)
                        ? 'Le KYC n’est pas finalisé'
                        : 'Le KYC date de plus d’un an',
                    'url' => route('tenant.clients.kyc.edit', $client),
                ]);
            }

            if (! $patrimoineComplet) {
                $anomalies->push([
                    'type' => 'Patrimoine',
                    'libelle' => empty($patrimoineDate) ? 'Patrimoine non renseigné' : 'Patrimoine à actualiser',
                    'detail' => empty($patrimoineDate)
                        ? 'Aucun élément patrimonial renseigné'
                        : 'Le patrimoine date de plus d’un an',
                    'url' => route('tenant.clients.patrimoine.edit', $client),
                ]);
            }

            if (! $profilComplet) {
                $anomalies->push([
                    'type' => 'Profil investisseur',
                    'libelle' => empty($profilDate) ? 'Profil investisseur incomplet' : 'Profil investisseur à actualiser',
                    'detail' => empty($profilDate)
                        ? 'Le profil investisseur n’est pas finalisé'
                        : 'Le profil investisseur date de plus d’un an',
                    'url' => route('tenant.clients.profil.edit', $client),
                ]);
            }

            $k = $client->kyc;

            $champs = [];

            $ajouter = function ($valeur, int $poids) use (&$champs) {
                $champs[] = ['valeur' => $valeur, 'poids' => $poids];
            };

            // COORDONNÉES
            $ajouter($client->civilite, 1);
            $ajouter($client->prenom, 3);
            $ajouter($client->nom, 3);
            $ajouter($client->date_naissance, 3);
            $ajouter($client->telephone_mobile, 2);
            $ajouter($client->email, 2);
            $ajouter($client->adresse, 2);
            $ajouter($client->code_postal, 1);
            $ajouter($client->ville, 1);
            $ajouter($client->pays, 1);

            // KYC ESSENTIEL
            $ajouter($k?->ne_en_france, 2);
            $ajouter($k?->francais, 2);
            $ajouter($k?->classification_mif, 3);
            $ajouter($k?->capacite_juridique, 3);
            $ajouter($k?->situation_familiale, 2);
            $ajouter($k?->statut_professionnel, 2);
            $ajouter($k?->csp, 2);
            $ajouter($k?->residence_fiscale_identique, 2);
            $ajouter($k?->heberge_par_tiers, 1);
            $ajouter($k?->est_ppe, 3);
            $ajouter($k?->proche_ppe, 3);
            $ajouter($k?->lieu_signature, 2);
            $ajouter($k?->accepte_cgu, 3);

            if ($k?->ne_en_france === 'oui') {
                $ajouter($k->commune_naissance, 2);
                $ajouter($k->code_postal_naissance, 1);
            } elseif ($k?->ne_en_france === 'non') {
                $ajouter($k->pays_naissance, 2);
            }

            if ($k?->francais === 'non') {
                $ajouter($k->autre_nationalite, 2);
            }

            if ($k?->situation_familiale === 'marie') {
                $ajouter($k->date_mariage, 1);
                $ajouter($k->lieu_mariage, 1);
                $ajouter($k->regime_matrimonial, 2);
                $ajouter($k->donation_dernier_vivant_profit, 1);
                $ajouter($k->donation_dernier_vivant_conjoint, 1);
            }

            if ($k?->situation_familiale === 'pacse') {
                $ajouter($k->date_pacs, 1);
                $ajouter($k->lieu_pacs, 1);
                $ajouter($k->convention_pacs, 2);
            }

            if ($k?->a_conjoint) {
                $ajouter($k->conjoint_civilite, 1);
                $ajouter($k->conjoint_prenom, 2);
                $ajouter($k->conjoint_nom, 2);
                $ajouter($k->conjoint_nom_naissance, 1);
                $ajouter($k->conjoint_date_naissance, 2);
            }

            if ($k?->conjoint_ajouter_profession) {
                $ajouter($k->conjoint_statut_professionnel, 2);
                $ajouter($k->conjoint_csp, 2);
                $ajouter($k->conjoint_profession_libelle, 1);
                $ajouter($k->conjoint_societe_employeur, 1);
            }

            if ($k?->residence_fiscale_identique === 'non') {
                $ajouter($k->autre_pays_residence_fiscale, 3);
            }

            if ($k?->est_ppe === 'oui_ppe') {
                $ajouter($k->ppe_exercice_12_mois, 3);
                $ajouter($k->ppe_fonction, 3);
                $ajouter($k->ppe_date_debut, 2);
                $ajouter($k->ppe_pays, 2);
            }

            if ($k?->proche_ppe === 'oui_proche_ppe') {
                $ajouter($k->proche_ppe_exercice_12_mois, 2);
                $ajouter($k->proche_ppe_fonction, 2);
                $ajouter($k->proche_ppe_nom, 2);
                $ajouter($k->proche_ppe_prenom, 2);
                $ajouter($k->proche_ppe_nature_lien, 3);
                $ajouter($k->proche_ppe_pays, 2);
            }

            $poidsTotal = collect($champs)->sum('poids');

            $poidsRempli = collect($champs)
                ->filter(fn ($champ) =>
                    $champ['valeur'] !== null &&
                    $champ['valeur'] !== ''
                )
                ->sum('poids');

            $completion = $poidsTotal > 0
                ? (int) round(($poidsRempli / $poidsTotal) * 100)
                : 0;

            $modulesComplets = $completion === 100 ? 3 : 0;

            return [
                'client' => $client,
                'kyc_complet' => $kycComplet,
                'patrimoine_complet' => $patrimoineComplet,
                'profil_complet' => $profilComplet,
                'completion' => $completion,
                'dossier_complet' => $modulesComplets === 3,
                'anomalies' => $anomalies,
            ];
        });

        /*
         * Dossiers nécessitant une intervention.
         */


        $clientsASuivre = $suiviClients
            ->filter(fn (array $ligne): bool =>
                collect($ligne['anomalies'] ?? [])->isNotEmpty()
            )
            ->values();

        $clientsNonConformes = $clientsASuivre->count();

        $dossiersEnFinalisation = $clientsASuivre
            ->filter(fn (array $ligne): bool =>
                $ligne['completion'] > 0
            )
            ->sortByDesc('completion')
            ->take(5)
            ->values();

        $dossiersComplets = $suiviClients
            ->where('dossier_complet', true)
            ->count();

        $dossiersIncomplets = $nombreClients - $dossiersComplets;

        $tauxCompletionMoyen = $nombreClients > 0
            ? (int) round($suiviClients->avg('completion'))
            : 0;

        $kycComplets = $suiviClients
            ->where('kyc_complet', true)
            ->count();

        $patrimoinesComplets = $suiviClients
            ->where('patrimoine_complet', true)
            ->count();

        $profilsComplets = $suiviClients
            ->where('profil_complet', true)
            ->count();

        $tauxQualite = $nombreClients > 0
            ? (int) round(
                (
                    $kycComplets
                    + $patrimoinesComplets
                    + $profilsComplets
                ) / ($nombreClients * 3) * 100
            )
            : 0;

        return view('tenant.dashboard', compact(
            'nombreClients',
            'actifs',
            'actifsFinanciers',
            'actifsNonFinanciers',
            'passifs',
            'patrimoineNet',
            'revenus',
            'charges',
            'soldeAnnuel',
            'suiviClients',
            'clientsASuivre',
            'clientsNonConformes',
            'dossiersEnFinalisation',
            'dossiersComplets',
            'dossiersIncomplets',
            'tauxCompletionMoyen',
            'kycComplets',
            'patrimoinesComplets',
            'profilsComplets',
            'tauxQualite',
        ));
    }
}
