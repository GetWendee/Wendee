<?php

namespace App\Console\Commands;

use App\Models\DossierEnrolement;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Console\Command;

class SeedDossierEnrolementDemo extends Command
{
    protected $signature = 'dossier:seed-demo {tenant} {user}';

    protected $description = 'Renseigne un dossier d\'enrolement conseiller mandataire avec des données fictives pour un utilisateur donné (démo/test)';

    public function handle(): int
    {
        $tenantId = $this->argument('tenant');
        $userId = $this->argument('user');

        $tenant = Tenant::find($tenantId);

        if (! $tenant) {
            $this->error("Tenant introuvable : {$tenantId}");
            return self::FAILURE;
        }

        $resultat = null;

        $tenant->run(function () use ($userId, &$resultat) {
            $user = User::find($userId);

            if (! $user) {
                $resultat = 'user_introuvable';
                return;
            }

            if ($user->role !== 'conseiller') {
                $resultat = 'pas_conseiller';
                return;
            }

            $dossier = $user->dossierEnrolement;

            if (! $dossier) {
                $dossier = DossierEnrolement::create(['user_id' => $user->id, 'statut' => 'invited']);
            }

            $dossier->update([
                'statut' => $dossier->statut === 'invited' ? 'onboarding' : $dossier->statut,

                'mode_exercice' => 'individuel',
                'civilite' => 'M.',
                'date_naissance' => '1988-04-12',
                'nationalite' => 'Française',
                'adresse' => '14 rue des Lilas',
                'code_postal' => '69003',
                'ville' => 'Lyon',
                'pays' => 'France',

                'domaines' => ['Assurance', 'Finance'],
                'statuts_reglementaires_actuels' => ['IAS', 'CIF'],

                'orias_numero' => '21004587',
                'orias_date_premiere_immatriculation' => '2021-03-15',
                'orias_date_dernier_renouvellement' => '2026-01-10',
                'orias_categories' => ['IAS', 'CIF'],
                'orias_statut_actif' => true,
                'orias_organisme_mandant' => 'W Conseils',

                'capacite_pro_fondement' => ['diplome', 'experience'],
                'capacite_pro_diplome_intitule' => 'Master Banque Finance Assurance',
                'capacite_pro_diplome_etablissement' => 'Université Lyon 3',
                'capacite_pro_diplome_annee' => '2012',
                'capacite_pro_diplome_niveau' => 'Bac+5',
                'capacite_pro_experience_employeur' => 'Generali France',
                'capacite_pro_experience_fonction' => 'Conseiller en gestion de patrimoine',
                'capacite_pro_experience_periode' => '2013-2020',
                'formation_continue_realisee' => true,
                'formation_continue_heures' => 15,
                'formation_continue_annee' => '2026',
                'formation_continue_organisme' => 'CNCEF Formation',
                'honorabilite_declaree' => true,
                'honorabilite_declaree_le' => now(),

                'rcp_assureur' => 'MMA IARD',
                'rcp_numero_police' => 'RCP-2026-778451',
                'rcp_date_debut' => '2026-01-01',
                'rcp_date_expiration' => '2027-01-01',
                'rcp_montant_garantie' => 1500000,
                'rcp_franchise' => 5000,
                'encaissement_fonds' => false,

                'mandat_zone' => 'region',
                'mandat_zone_detail' => 'Auvergne-Rhône-Alpes',
                'mandat_clientele' => ['particuliers', 'tns'],
                'mandat_missions_autorisees' => ['prospection', 'decouverte_client', 'recueil_besoins', 'presentation_solutions', 'proposition', 'aide_souscription'],
                'mandat_missions_interdites' => 'Signature de contrat sans validation prealable du cabinet pour les montants superieurs a 100 000 euros.',
                'mandat_remuneration_taux' => 40.00,
                'mandat_remuneration_frequence' => 'recurrente',
                'mandat_duree' => 'indeterminee',
                'mandat_preavis_resiliation_jours' => 60,

                'procedures_acceptees' => [
                    ['cle' => 'lcbft', 'label' => 'Procédure LCB-FT', 'accepte_le' => now()->toDateTimeString()],
                    ['cle' => 'reclamations', 'label' => 'Procédure de traitement des réclamations', 'accepte_le' => now()->toDateTimeString()],
                    ['cle' => 'rgpd', 'label' => 'Politique de protection des données personnelles (RGPD)', 'accepte_le' => now()->toDateTimeString()],
                ],
            ]);

            $resultat = 'ok';
        });

        if ($resultat === 'user_introuvable') {
            $this->error("Utilisateur introuvable (id {$userId}) dans ce tenant : {$tenantId}");
            return self::FAILURE;
        }

        if ($resultat === 'pas_conseiller') {
            $this->error("L'utilisateur id {$userId} n'a pas le role conseiller.");
            return self::FAILURE;
        }

        $this->info("Dossier d'enrolement fictif renseigne pour l'utilisateur id {$userId} dans le tenant {$tenantId}.");
        return self::SUCCESS;
    }
}
