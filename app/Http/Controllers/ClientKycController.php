<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientPersonneACharge;
use App\Models\VerificationClient;
use App\Services\VerificationCodeService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ClientKycController extends Controller
{
    public function edit(Client $client, VerificationCodeService $verification): View
    {
        $client->load('kyc', 'personnesACharge');

        if (! VerificationClient::where('client_id', $client->id)->where('module', 'kyc')->exists()) {
            $verification->enregistrerModification($client, 'kyc');
        }

        $verificationClient = VerificationClient::where('client_id', $client->id)
            ->where('module', 'kyc')
            ->first();

        $verificationRequise = ! $verificationClient?->verifie_le;

        return view('tenant.clients.kyc', [
            'client' => $client,
            'listes' => config('listes'),
            'verificationRequise' => $verificationRequise,
        ]);
    }

    public function update(
        Request $request,
        Client $client,
        VerificationCodeService $verification,
        \App\Services\AI\KycAnalysisService $kycAnalysis
    ): RedirectResponse
    {
        $validated = $request->validate([
            'ne_en_france' => ['nullable', 'string'],
            'commune_naissance' => ['nullable', 'string', 'max:255'],
            'code_postal_naissance' => ['nullable', 'string', 'max:10'],
            'pays_naissance' => ['nullable', 'string'],
            'francais' => ['nullable', 'string'],
            'autre_nationalite' => ['nullable', 'string'],

            'classification_mif' => ['nullable', 'string'],
            'capacite_juridique' => ['nullable', 'string'],

            'situation_familiale' => ['nullable', 'string'],
            'date_mariage' => ['nullable', 'date'],
            'lieu_mariage' => ['nullable', 'string', 'max:255'],
            'regime_matrimonial' => ['nullable', 'string'],
            'donation_dernier_vivant_profit' => ['nullable', 'string'],
            'donation_dernier_vivant_conjoint' => ['nullable', 'string'],
            'date_pacs' => ['nullable', 'date'],
            'lieu_pacs' => ['nullable', 'string', 'max:255'],
            'convention_pacs' => ['nullable', 'string'],
            'a_conjoint' => ['nullable', 'boolean'],
            'a_personnes_a_charge' => ['nullable', 'boolean'],

            'conjoint_civilite' => ['nullable', 'string'],
            'conjoint_nom' => ['nullable', 'string', 'max:255'],
            'conjoint_nom_naissance' => ['nullable', 'string', 'max:255'],
            'conjoint_prenom' => ['nullable', 'string', 'max:255'],
            'conjoint_date_naissance' => ['nullable', 'date'],

            'statut_professionnel' => ['nullable', 'string'],
            'societe_employeur' => ['nullable', 'string', 'max:255'],
            'date_entree_entreprise' => ['nullable', 'date'],
            'profession_libelle' => ['nullable', 'string', 'max:255'],
            'code_naf' => ['nullable', 'string'],
            'age_depart_retraite' => ['nullable', 'integer', 'min:0', 'max:120'],
            'csp' => ['nullable', 'string'],
            'siret_employeur' => ['nullable', 'string', 'max:20'],

            'conjoint_ajouter_profession' => ['nullable', 'boolean'],
            'conjoint_statut_professionnel' => ['nullable', 'string'],
            'conjoint_societe_employeur' => ['nullable', 'string', 'max:255'],
            'conjoint_date_entree_entreprise' => ['nullable', 'date'],
            'conjoint_profession_libelle' => ['nullable', 'string', 'max:255'],
            'conjoint_code_naf' => ['nullable', 'string'],
            'conjoint_age_depart_retraite' => ['nullable', 'integer', 'min:0', 'max:120'],
            'conjoint_csp' => ['nullable', 'string'],
            'conjoint_siret_employeur' => ['nullable', 'string', 'max:20'],

            'residence_fiscale_identique' => ['nullable', 'string'],
            'autre_pays_residence_fiscale' => ['nullable', 'string'],
            'heberge_par_tiers' => ['nullable', 'string'],

            'est_ppe' => ['nullable', 'string'],
            'ppe_exercice_12_mois' => ['nullable', 'string'],
            'ppe_fonction' => ['nullable', 'string'],
            'ppe_date_debut' => ['nullable', 'date'],
            'ppe_date_fin' => ['nullable', 'date'],
            'ppe_pays' => ['nullable', 'string'],

            'proche_ppe' => ['nullable', 'string'],
            'proche_ppe_exercice_12_mois' => ['nullable', 'string'],
            'proche_ppe_fonction' => ['nullable', 'string'],
            'proche_ppe_nom' => ['nullable', 'string', 'max:255'],
            'proche_ppe_prenom' => ['nullable', 'string', 'max:255'],
            'proche_ppe_nature_lien' => ['nullable', 'string'],
            'proche_ppe_date_debut' => ['nullable', 'date'],
            'proche_ppe_date_fin' => ['nullable', 'date'],
            'proche_ppe_pays' => ['nullable', 'string'],

            'lieu_signature' => ['nullable', 'string', 'max:255'],
            'code_de_verification_client' => ['nullable', 'string', 'max:10'],
            'accepte_cgu' => ['nullable', 'boolean'],

            'personnes_a_charge' => ['nullable', 'array'],
            'personnes_a_charge.*.civilite' => ['nullable', 'string'],
            'personnes_a_charge.*.prenom' => ['required_with:personnes_a_charge.*.nom', 'nullable', 'string', 'max:255'],
            'personnes_a_charge.*.nom' => ['required_with:personnes_a_charge.*.prenom', 'nullable', 'string', 'max:255'],
            'personnes_a_charge.*.date_naissance' => ['nullable', 'date'],
            'personnes_a_charge.*.enfant_de' => ['nullable', 'string'],
            'personnes_a_charge.*.fiscalement_a_charge' => ['nullable', 'string'],
            'personnes_a_charge.*.garde_alternee' => ['nullable', 'string', 'in:oui,non'],
            'personnes_a_charge.*.invalidite' => ['nullable', 'string', 'in:oui,non'],
        ]);

        $validated['a_conjoint'] = $request->boolean('a_conjoint');
        $validated['a_personnes_a_charge'] = $request->boolean('a_personnes_a_charge');
        $validated['conjoint_ajouter_profession'] = $request->boolean('conjoint_ajouter_profession');
        $validated['accepte_cgu'] = $request->boolean('accepte_cgu');

        if ($validated['accepte_cgu'] && !$client->kyc?->signe_le) {
            $validated['signe_le'] = now();
        }

        $personnes = $validated['personnes_a_charge'] ?? [];
        unset($validated['personnes_a_charge']);

        $codeSaisi = $validated['code_de_verification_client'] ?? null;
        unset($validated['code_de_verification_client']);
        $codeValide = false;

        $verificationClient = VerificationClient::where('client_id', $client->id)
            ->where('module', 'kyc')
            ->first();

        $verificationRequise = ! $verificationClient?->verifie_le;

        if ($verificationRequise && empty($codeSaisi)) {
            return back()
                ->withErrors(['code_de_verification_client' => 'Le code de vérification client est obligatoire pour la première validation.'])
                ->withInput();
        }

        if (! empty($codeSaisi)) {
            if (! $verification->verifierCode($client, 'kyc', $codeSaisi)) {
                return back()->withErrors(['code_de_verification_client' => 'Code de vérification incorrect.'])->withInput();
            }
            $codeValide = true;
        }

        $client->kyc()->updateOrCreate(['client_id' => $client->id], $validated);

        $client->personnesACharge()->delete();
        foreach ($personnes as $personne) {
            if (empty($personne['prenom']) || empty($personne['nom'])) {
                continue;
            }
            $client->personnesACharge()->create($personne);
        }

        /*
         * Analyse KYC native Laravel / OpenAI.
         *
         * L'enregistrement du KYC reste prioritaire :
         * une erreur OpenAI ne doit jamais empêcher
         * la sauvegarde du dossier.
         */
        try {
            $kycAnalysis->analyze($client);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error(
                'Erreur analyse KYC OpenAI',
                [
                    'client_id' => $client->id,
                    'error' => $e->getMessage(),
                ]
            );
        }

        if ($codeValide) {
            $message = 'Recueil KYC validé par le client.';
        } else {
            $verification->enregistrerModification($client, 'kyc');
            $message = 'Recueil KYC enregistré.';
        }

        return redirect()->route('tenant.clients.kyc.edit', $client)->with('status', $message);
    }
}
