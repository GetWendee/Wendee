<?php

namespace App\Http\Controllers;

use App\Models\CabinetProfile;
use App\Services\CabinetCompletionChecker;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CabinetProfileController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        abort_unless($user && $user->role === 'courtier', 403);

        $cabinet = CabinetProfile::query()->first();

        abort_unless($cabinet, 404);

        $completionStatus = CabinetCompletionChecker::status($cabinet, $user);

        return view('tenant.cabinet', [
            'cabinet' => $cabinet,
            'completionStatus' => $completionStatus,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user && $user->role === 'courtier', 403);

        $cabinet = CabinetProfile::query()->firstOrFail();

        $validated = $request->validate([
            'logo' => [
                'sometimes',
                'nullable',
                'image',
                'max:5120',
            ],

            'nom_commercial' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'raison_sociale' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'forme_juridique' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'capital_social' => [
                'sometimes',
                'nullable',
                'numeric',
            ],

            'numero_rcs' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'ville_rcs' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'numero_orias' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'immatriculation_cci' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'date_orias' => [
                'sometimes',
                'nullable',
                'date',
            ],

            'statuts_reglementaires' => [
                'sometimes',
                'nullable',
                'array',
            ],
            'statuts_reglementaires.*' => [
                'string',
                'in:ias_courtier,ias_mandataire,ias_mia,iobsp_courtier,iobsp_mandataire,iobsp_mandataire_non_exclusif,iobsp_mandataire_exclusif,cif,agent_immobilier,mandataire_agent_immobilier',
            ],

            'numero_tva' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'association_professionnelle' => [
                'sometimes',
                'nullable',
                'array',
            ],
            'association_professionnelle.*' => [
                'string',
                'in:acpr,amf,cci',
            ],

            'numero_association' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'assurance_compagnie' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'assurance_adresse' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'assurance_code_postal' => [
                'sometimes',
                'nullable',
                'string',
                'max:20',
            ],

            'assurance_ville' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'plafond_garanties_sinistre_ias' => [
                'sometimes',
                'nullable',
                'numeric',
            ],

            'plafond_garanties_annee_ias' => [
                'sometimes',
                'nullable',
                'numeric',
            ],

            'plafond_garanties_sinistre_iobsp' => [
                'sometimes',
                'nullable',
                'numeric',
            ],

            'plafond_garanties_annee_iobsp' => [
                'sometimes',
                'nullable',
                'numeric',
            ],

            'plafond_garanties_sinistre_cif' => [
                'sometimes',
                'nullable',
                'numeric',
            ],

            'plafond_garanties_annee_cif' => [
                'sometimes',
                'nullable',
                'numeric',
            ],

            'responsabilite_civile_exploitation_sinistre' => [
                'sometimes',
                'nullable',
                'numeric',
            ],

            'assurance_police' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'assurance_date_debut' => [
                'sometimes',
                'nullable',
                'date',
            ],

            'assurance_date_fin' => [
                'sometimes',
                'nullable',
                'date',
            ],

            'assurance_zone_couverture' => [
                'sometimes',
                'nullable',
                'in:france,ue,europe,hors_usa,monde',
            ],

            'garantie_financiere_iobsp' => [
                'sometimes',
                'nullable',
                'in:oui,non',
            ],

            'garantie_financiere_iobsp_assureur' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'garantie_financiere_iobsp_numero' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'garantie_financiere_iobsp_montant' => [
                'sometimes',
                'nullable',
                'numeric',
            ],

            'garantie_financiere_iobsp_date_fin' => [
                'sometimes',
                'nullable',
                'date',
            ],

            'garantie_financiere_immo' => [
                'sometimes',
                'nullable',
                'in:oui,non',
            ],

            'garantie_financiere_immo_assureur' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'garantie_financiere_immo_numero' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'garantie_financiere_immo_montant' => [
                'sometimes',
                'nullable',
                'numeric',
            ],

            'garantie_financiere_immo_date_fin' => [
                'sometimes',
                'nullable',
                'date',
            ],

            'dirigeant_nom' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'dirigeant_prenom' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'dirigeant_fonction' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'responsable_conformite' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'mail_responsable_conformite' => [
                'sometimes',
                'nullable',
                'email',
                'max:255',
            ],

            'lcbft_responsable_nom' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'mediateur_nom' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'mediateur_contact' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'adresse' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'code_postal' => [
                'sometimes',
                'nullable',
                'string',
                'max:20',
            ],

            'ville' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'telephone' => [
                'sometimes',
                'nullable',
                'string',
                'max:50',
            ],

            'email' => [
                'sometimes',
                'nullable',
                'email',
                'max:255',
            ],

            'site_internet' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'mode_remuneration' => [
                'sometimes',
                'nullable',
                'in:honoraires,commissions,honoraires_commissions',
            ],

            'conflits_interets_existe' => [
                'sometimes',
                'nullable',
                'in:oui,non',
            ],

            'conflits_interets_description' => [
                'sometimes',
                'nullable',
                'string',
            ],

            'partenaires' => [
                'sometimes',
                'nullable',
                'array',
            ],
            'partenaires.*.nom' => [
                'nullable',
                'string',
                'max:255',
            ],
            'partenaires.*.type' => [
                'nullable',
                'in:assureur,banque,societe_gestion,promoteur_immobilier,plateforme,autre',
            ],
            'partenaires.*.mode_relation' => [
                'nullable',
                'in:courtier,mandataire,apporteur,partenaire_reference',
            ],
            'partenaires.*.identifiant' => [
                'nullable',
                'string',
                'max:255',
            ],
            'partenaires.*.url' => [
                'nullable',
                'string',
                'max:255',
            ],
            'partenaires.*.notes' => [
                'nullable',
                'string',
            ],

            'prestations' => [
                'sometimes',
                'nullable',
                'array',
            ],
            'prestations.*.mode' => [
                'nullable',
                'in:forfait,pourcentage',
            ],
            'prestations.*.forfait' => [
                'nullable',
                'numeric',
            ],
            'prestations.*.pourcentage' => [
                'nullable',
                'numeric',
            ],

            'objectifs' => [
                'sometimes',
                'nullable',
                'array',
            ],
            'objectifs.client_semaine' => [
                'nullable',
                'integer',
            ],
            'objectifs.rdv_semaine' => [
                'nullable',
                'integer',
            ],
            'objectifs.collectes_semaine' => [
                'nullable',
                'integer',
            ],
            'objectifs.taux_transformation' => [
                'nullable',
                'numeric',
            ],
            'objectifs.revenu_mensuel' => [
                'nullable',
                'numeric',
            ],
            'objectifs.revenu_annuel' => [
                'nullable',
                'numeric',
            ],
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        } else {
            unset($validated['logo']);
        }

        if ($request->has('objectifs')) {
            $user->update(['objectifs' => $validated['objectifs'] ?? null]);
        }

        unset($validated['objectifs']);

        $cabinet->update($validated);

        return redirect()
            ->route('tenant.cabinet')
            ->with('cabinet_saved', true);
    }
}
