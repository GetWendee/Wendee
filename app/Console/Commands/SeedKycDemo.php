<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Models\Tenant;
use Illuminate\Console\Command;

class SeedKycDemo extends Command
{
    protected $signature = 'kyc:seed-demo {tenant} {client}';

    protected $description = 'Renseigne un KYC personne physique avec des données fictives pour un client donné (démo/test)';

    public function handle(): int
    {
        $tenantId = $this->argument('tenant');
        $clientId = $this->argument('client');

        $tenant = Tenant::find($tenantId);

        if (! $tenant) {
            $this->error("Tenant introuvable : {$tenantId}");
            return self::FAILURE;
        }

        $resultat = null;

        $tenant->run(function () use ($clientId, &$resultat) {
            $client = Client::find($clientId);

            if (! $client) {
                $resultat = 'client_introuvable';
                return;
            }

            $client->kyc()->updateOrCreate([], [
                'ne_en_france' => 'oui',
                'commune_naissance' => 'Lyon',
                'code_postal_naissance' => '69000',
                'francais' => 'oui',

                'classification_mif' => 'non_professionnel_kyc',
                'capacite_juridique' => 'majeur_capable',

                'situation_familiale' => 'marie',
                'date_mariage' => '2018-06-16',
                'lieu_mariage' => 'Lyon',
                'regime_matrimonial' => 'regime_legal',
                'donation_dernier_vivant_profit' => 'oui_ddv',
                'donation_dernier_vivant_conjoint' => 'oui_ddv',

                'a_conjoint' => true,
                'a_personnes_a_charge' => false,

                'conjoint_civilite' => 'madame_conjoint_kyc',
                'conjoint_nom' => 'Dupont',
                'conjoint_nom_naissance' => 'Martin',
                'conjoint_prenom' => 'Claire',
                'conjoint_date_naissance' => '1991-03-22',

                'statut_professionnel' => 'salarie_cdi',
                'societe_employeur' => 'Acme Consulting SAS',
                'date_entree_entreprise' => '2015-09-01',
                'profession_libelle' => 'Ingénieur logiciel',
                'code_naf' => 'information_et_communication',
                'age_depart_retraite' => 64,
                'csp' => 'cadre_dentreprise',
                'siret_employeur' => '80123456700015',

                'conjoint_ajouter_profession' => true,
                'conjoint_statut_professionnel' => 'salarie_cdi',
                'conjoint_societe_employeur' => 'Hopital Edouard Herriot',
                'conjoint_date_entree_entreprise' => '2016-01-10',
                'conjoint_profession_libelle' => 'Infirmière',
                'conjoint_code_naf' => 'sante_humaine_et_action_sociale',
                'conjoint_age_depart_retraite' => 64,
                'conjoint_csp' => 'employe',

                'residence_fiscale_identique' => 'oui',
                'heberge_par_tiers' => 'non_heberge_tiers',

                'est_ppe' => 'non_ppe',
                'proche_ppe' => 'non_proche_ppe',

                'lieu_signature' => 'Lyon',
                'accepte_cgu' => true,
                'signe_le' => now(),
            ]);

            $resultat = 'ok';
        });

        if ($resultat === 'client_introuvable') {
            $this->error("Client introuvable (id {$clientId}) dans ce tenant : {$tenantId}");
            return self::FAILURE;
        }

        $this->info("KYC fictif renseigné pour le client id {$clientId} dans le tenant {$tenantId}.");
        return self::SUCCESS;
    }
}
