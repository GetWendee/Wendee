<?php

namespace App\Http\Controllers;

use App\Models\CabinetProfile;
use App\Models\DossierEnrolement;
use App\Models\DossierJustificatif;
use App\Services\DossierEnrolementComplianceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * Espace self-service du conseiller mandataire : complète son dossier
 * d'enrôlement (identité, statut, ORIAS, capacité professionnelle, RCP,
 * mandat, procédures), téléverse ses justificatifs et soumet le dossier
 * pour validation. Voir claude/enrolement-conseillers-mandataires.md.
 */
class DossierEnrolementController extends Controller
{
    private const PROCEDURES = [
        'lcbft' => 'Procédure LCB-FT',
        'reclamations' => 'Procédure de traitement des réclamations',
        'rgpd' => 'Politique de protection des données personnelles (RGPD)',
    ];

    private const TYPES_JUSTIFICATIFS = [
        'identite' => "Pièce d'identité",
        'orias' => "Attestation ORIAS",
        'diplome' => 'Diplôme',
        'rcp' => 'Attestation RCP',
        'garantie_financiere' => 'Attestation de garantie financière',
        'kbis' => 'Extrait Kbis',
    ];

    public function edit(Request $request): View
    {
        $user = $request->user();

        abort_unless($user->role === 'conseiller', 403);

        $dossier = $user->dossierEnrolement;

        abort_unless($dossier, 404);

        $dossier->load(['justificatifs' => function ($query) {
            $query->orderByDesc('version');
        }]);

        return view('tenant.dossiers-enrolement.edit', [
            'dossier' => $dossier,
            'procedures' => self::PROCEDURES,
            'typesJustificatifs' => self::TYPES_JUSTIFICATIFS,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user->role === 'conseiller', 403);

        $dossier = $user->dossierEnrolement;

        abort_unless($dossier, 404);

        abort_if(in_array($dossier->statut, ['pending_validation', 'contract_pending', 'active']), 403, 'Ce dossier est en cours de validation et ne peut plus être modifié.');

        $validated = $request->validate([
            'mode_exercice' => ['nullable', 'string', 'in:individuel,societe'],
            'civilite' => ['nullable', 'string', 'max:10'],
            'date_naissance' => ['nullable', 'date'],
            'nationalite' => ['nullable', 'string', 'max:255'],
            'adresse' => ['nullable', 'string', 'max:255'],
            'code_postal' => ['nullable', 'string', 'max:10'],
            'ville' => ['nullable', 'string', 'max:255'],
            'pays' => ['nullable', 'string', 'max:255'],
            'societe_denomination' => ['nullable', 'string', 'max:255'],
            'societe_forme_juridique' => ['nullable', 'string', 'max:255'],
            'societe_siren' => ['nullable', 'string', 'max:20'],
            'societe_siret' => ['nullable', 'string', 'max:20'],
            'societe_rcs' => ['nullable', 'string', 'max:50'],
            'societe_ville_rcs' => ['nullable', 'string', 'max:255'],
            'societe_capital_social' => ['nullable', 'numeric', 'min:0'],
            'societe_adresse_siege' => ['nullable', 'string', 'max:255'],
            'representant_nom' => ['nullable', 'string', 'max:255'],
            'representant_prenom' => ['nullable', 'string', 'max:255'],
            'representant_fonction' => ['nullable', 'string', 'max:255'],

            'domaines' => ['nullable', 'array'],
            'domaines.*' => ['string', 'in:Assurance,Banque,Finance,Immobilier'],
            'statuts_reglementaires_actuels' => ['nullable', 'array'],
            'statuts_reglementaires_actuels.*' => ['string', 'max:255'],

            'orias_numero' => ['nullable', 'string', 'max:50'],
            'orias_date_premiere_immatriculation' => ['nullable', 'date'],
            'orias_date_dernier_renouvellement' => ['nullable', 'date'],
            'orias_categories' => ['nullable', 'array'],
            'orias_categories.*' => ['string', 'max:255'],
            'orias_statut_actif' => ['nullable', 'boolean'],
            'orias_organisme_mandant' => ['nullable', 'string', 'max:255'],

            'capacite_pro_fondement' => ['nullable', 'array'],
            'capacite_pro_fondement.*' => ['string', 'in:diplome,experience,formation'],
            'capacite_pro_diplome_intitule' => ['nullable', 'string', 'max:255'],
            'capacite_pro_diplome_etablissement' => ['nullable', 'string', 'max:255'],
            'capacite_pro_diplome_annee' => ['nullable', 'string', 'max:10'],
            'capacite_pro_diplome_niveau' => ['nullable', 'string', 'max:255'],
            'capacite_pro_experience_employeur' => ['nullable', 'string', 'max:255'],
            'capacite_pro_experience_fonction' => ['nullable', 'string', 'max:255'],
            'capacite_pro_experience_periode' => ['nullable', 'string', 'max:255'],
            'capacite_pro_formation_organisme' => ['nullable', 'string', 'max:255'],
            'capacite_pro_formation_intitule' => ['nullable', 'string', 'max:255'],
            'capacite_pro_formation_heures' => ['nullable', 'integer', 'min:0'],
            'capacite_pro_formation_date_obtention' => ['nullable', 'date'],
            'formation_continue_realisee' => ['nullable', 'boolean'],
            'formation_continue_heures' => ['nullable', 'integer', 'min:0'],
            'formation_continue_annee' => ['nullable', 'string', 'max:10'],
            'formation_continue_organisme' => ['nullable', 'string', 'max:255'],
            'honorabilite_declaree' => ['nullable', 'boolean'],

            'rcp_assureur' => ['nullable', 'string', 'max:255'],
            'rcp_numero_police' => ['nullable', 'string', 'max:100'],
            'rcp_date_debut' => ['nullable', 'date'],
            'rcp_date_expiration' => ['nullable', 'date'],
            'rcp_montant_garantie' => ['nullable', 'numeric', 'min:0'],
            'rcp_franchise' => ['nullable', 'numeric', 'min:0'],
            'encaissement_fonds' => ['nullable', 'boolean'],
            'garantie_financiere_organisme' => ['nullable', 'string', 'max:255'],
            'garantie_financiere_numero' => ['nullable', 'string', 'max:100'],
            'garantie_financiere_montant' => ['nullable', 'numeric', 'min:0'],
            'garantie_financiere_date_echeance' => ['nullable', 'date'],

            'mandat_zone' => ['nullable', 'string', 'in:france_entiere,region,departements,autre'],
            'mandat_zone_detail' => ['nullable', 'string', 'max:255'],
            'mandat_clientele' => ['nullable', 'array'],
            'mandat_clientele.*' => ['string', 'in:particuliers,professionnels,tns,dirigeants,personnes_morales'],
            'mandat_missions_autorisees' => ['nullable', 'array'],
            'mandat_missions_autorisees.*' => ['string', 'in:prospection,decouverte_client,recueil_besoins,presentation_solutions,proposition,aide_souscription,signature_contrat,suivi_relation'],
            'mandat_missions_interdites' => ['nullable', 'string', 'max:2000'],

            'procedures' => ['nullable', 'array'],
            'procedures.*' => ['string'],
        ]);

        $procedures = $dossier->procedures_acceptees ?? [];

        foreach (($validated['procedures'] ?? []) as $cle) {
            if (! array_key_exists($cle, self::PROCEDURES)) {
                continue;
            }

            $dejaAcceptee = collect($procedures)->contains(fn ($p) => ($p['cle'] ?? null) === $cle);

            if (! $dejaAcceptee) {
                $procedures[] = [
                    'cle' => $cle,
                    'label' => self::PROCEDURES[$cle],
                    'accepte_le' => now()->toDateTimeString(),
                ];
            }
        }

        unset($validated['procedures']);

        $honorabiliteDejaDeclaree = $dossier->honorabilite_declaree;

        $validated['honorabilite_declaree'] = (bool) ($validated['honorabilite_declaree'] ?? false);
        $validated['procedures_acceptees'] = $procedures;

        if ($validated['honorabilite_declaree'] && ! $honorabiliteDejaDeclaree) {
            $validated['honorabilite_declaree_le'] = now();
        }

        if ($dossier->statut === 'invited') {
            $validated['statut'] = 'onboarding';
        }

        $dossier->update($validated);

        return redirect()->route('tenant.dossier-enrolement.edit')->with('status', 'Dossier mis à jour.');
    }

    public function uploadJustificatif(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user->role === 'conseiller', 403);

        $dossier = $user->dossierEnrolement;

        abort_unless($dossier, 404);

        $validated = $request->validate([
            'type' => ['required', 'string', 'in:' . implode(',', array_keys(self::TYPES_JUSTIFICATIFS))],
            'fichier' => ['required', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png'],
            'date_emission' => ['nullable', 'date'],
            'date_debut_validite' => ['nullable', 'date'],
            'date_expiration' => ['nullable', 'date'],
        ]);

        $derniereVersion = $dossier->justificatifs()
            ->where('type', $validated['type'])
            ->max('version') ?? 0;

        $path = $request->file('fichier')->store('dossiers-enrolement/' . $dossier->id, 'local');

        DossierJustificatif::create([
            'dossier_enrolement_id' => $dossier->id,
            'type' => $validated['type'],
            'fichier_path' => $path,
            'nom_original' => $request->file('fichier')->getClientOriginalName(),
            'date_emission' => $validated['date_emission'] ?? null,
            'date_debut_validite' => $validated['date_debut_validite'] ?? null,
            'date_expiration' => $validated['date_expiration'] ?? null,
            'statut' => 'a_verifier',
            'version' => $derniereVersion + 1,
            'uploaded_at' => now(),
        ]);

        return redirect()->route('tenant.dossier-enrolement.edit')->with('status', 'Justificatif téléversé.');
    }

    public function submit(Request $request, DossierEnrolementComplianceService $compliance): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user->role === 'conseiller', 403);

        $dossier = $user->dossierEnrolement;

        abort_unless($dossier, 404);

        abort_if(in_array($dossier->statut, ['pending_validation', 'contract_pending', 'active']), 403);

        $resultat = $compliance->evaluer($dossier);

        $dossier->update([
            'checks_conformite' => $resultat['checks'],
            'conformite_reglementaire' => $resultat['conformite_reglementaire'],
            'statut' => 'pending_validation',
        ]);

        return redirect()->route('tenant.dossier-enrolement.edit')->with('status', 'Dossier soumis pour validation.');
    }
}
