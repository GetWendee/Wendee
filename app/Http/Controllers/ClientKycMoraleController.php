<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * KYC d'une personne morale (société) : formulaire et validation séparés du
 * KYC personne physique (ClientKycController), qui reste inchangé. Voir
 * claude/kyc-personne-morale.md.
 */
class ClientKycMoraleController extends Controller
{
    public function edit(Client $client): View|RedirectResponse
    {
        if (! $client->estMorale()) {
            return redirect()->route('tenant.clients.kyc.edit', $client);
        }

        $client->load('kycMorale', 'intervenants');

        return view('tenant.clients.kyc-morale', [
            'client' => $client,
            'listes' => config('listes'),
        ]);
    }

    public function update(Request $request, Client $client): RedirectResponse
    {
        abort_unless($client->estMorale(), 404);

        $validated = $request->validate([
            'masse_salariale_min' => ['nullable', 'integer'],
            'masse_salariale_max' => ['nullable', 'integer'],
            'masse_salariale_moyenne' => ['nullable', 'integer'],
            'valeur_estimee_entreprise' => ['nullable', 'numeric'],
            'elements_statutaires_notables' => ['nullable', 'string'],
            'autres_remarques_notables' => ['nullable', 'string'],

            'classification_mif' => ['nullable', 'string'],
            'connaissances_financieres' => ['nullable', 'string'],
            'connaissances_juridiques' => ['nullable', 'string'],

            'detient_produits_actuellement' => ['nullable', 'boolean'],
            'detient_produits_actuellement_detail' => ['nullable', 'string'],
            'a_detenu_produits_passe' => ['nullable', 'boolean'],
            'a_detenu_produits_passe_detail' => ['nullable', 'string'],
            'supports_opcvm' => ['nullable', 'boolean'],
            'supports_opcvm_classe_actif' => ['nullable', 'string', 'max:255'],
            'produits_couverture' => ['nullable', 'boolean'],
            'autres_supports' => ['nullable', 'string'],

            'ppe_reponses' => ['nullable', 'array'],

            'chiffre_affaires_n1' => ['nullable', 'array'],
            'chiffre_affaires_n1.*.poste' => ['nullable', 'string', 'max:255'],
            'chiffre_affaires_n1.*.montant' => ['nullable', 'numeric'],
            'chiffre_affaires_n1.*.activite' => ['nullable', 'string', 'max:255'],
            'chiffre_affaires_n1.*.hors_france' => ['nullable', 'string', 'max:255'],
            'chiffre_affaires_n1.*.remarques' => ['nullable', 'string'],

            'charges_n1' => ['nullable', 'array'],
            'charges_n1.*.poste' => ['nullable', 'string', 'max:255'],
            'charges_n1.*.montant' => ['nullable', 'numeric'],
            'charges_n1.*.activite' => ['nullable', 'string', 'max:255'],
            'charges_n1.*.hors_france' => ['nullable', 'string', 'max:255'],
            'charges_n1.*.remarques' => ['nullable', 'string'],

            'resultat_n1' => ['nullable', 'array'],
            'resultat_n1.*.poste' => ['nullable', 'string', 'max:255'],
            'resultat_n1.*.montant' => ['nullable', 'numeric'],
            'resultat_n1.*.activite' => ['nullable', 'string', 'max:255'],
            'resultat_n1.*.hors_france' => ['nullable', 'string', 'max:255'],
            'resultat_n1.*.remarques' => ['nullable', 'string'],

            'resultats_filiales' => ['nullable', 'array'],
            'resultats_filiales.*.poste' => ['nullable', 'string', 'max:255'],
            'resultats_filiales.*.montant' => ['nullable', 'numeric'],
            'resultats_filiales.*.activite' => ['nullable', 'string', 'max:255'],
            'resultats_filiales.*.hors_france' => ['nullable', 'string', 'max:255'],
            'resultats_filiales.*.remarques' => ['nullable', 'string'],

            'evolutions_previsibles' => ['nullable', 'string'],

            'is_annee_derniere' => ['nullable', 'numeric'],
            'is_annee_moyenne' => ['nullable', 'numeric'],
            'is_evolutions_previsibles' => ['nullable', 'string'],
            'taxe_professionnelle_annee_derniere' => ['nullable', 'numeric'],
            'taxe_professionnelle_annee_moyenne' => ['nullable', 'numeric'],
            'taxe_professionnelle_evolutions_previsibles' => ['nullable', 'string'],
            'impots_fonciers' => ['nullable', 'numeric'],
            'autres_impots_acquittes' => ['nullable', 'string'],
            'remarques' => ['nullable', 'string'],

            'dirigeants' => ['nullable', 'array'],
            'dirigeants.*.nom' => ['nullable', 'string', 'max:255'],
            'dirigeants.*.role' => ['nullable', 'string', 'max:255'],

            'actionnaires' => ['nullable', 'array'],
            'actionnaires.*.nom' => ['nullable', 'string', 'max:255'],
            'actionnaires.*.pourcentage_detention' => ['nullable', 'numeric', 'min:0', 'max:100'],

            'beneficiaires_effectifs' => ['nullable', 'array'],
            'beneficiaires_effectifs.*.nom' => ['nullable', 'string', 'max:255'],
            'beneficiaires_effectifs.*.role' => ['nullable', 'string', 'max:255'],

            'accepte_cgu' => ['nullable', 'boolean'],
        ]);

        $intervenantsGroupes = [
            'dirigeant' => $validated['dirigeants'] ?? [],
            'actionnaire' => $validated['actionnaires'] ?? [],
            'beneficiaire_effectif' => $validated['beneficiaires_effectifs'] ?? [],
        ];
        unset($validated['dirigeants'], $validated['actionnaires'], $validated['beneficiaires_effectifs']);

        $validated['detient_produits_actuellement'] = $request->boolean('detient_produits_actuellement');
        $validated['a_detenu_produits_passe'] = $request->boolean('a_detenu_produits_passe');
        $validated['supports_opcvm'] = $request->boolean('supports_opcvm');
        $validated['produits_couverture'] = $request->boolean('produits_couverture');

        if ($request->boolean('accepte_cgu')) {
            $validated['accepte_cgu'] = true;
            $validated['signe_le'] = now();
        }

        $client->kycMorale()->updateOrCreate([], $validated);

        /*
         * Analyse KYC société native Laravel / OpenAI.
         *
         * L'enregistrement du KYC reste prioritaire : une erreur OpenAI ne
         * doit jamais empêcher la sauvegarde du dossier.
         */
        try {
            app(\App\Services\AI\KycAnalysisServiceMorale::class)->analyze($client);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error(
                'Erreur analyse KYC société OpenAI',
                [
                    'client_id' => $client->id,
                    'error' => $e->getMessage(),
                ]
            );
        }

        $client->intervenants()->delete();
        foreach ($intervenantsGroupes as $type => $lignes) {
            foreach ($lignes as $ligne) {
                if (empty($ligne['nom'])) {
                    continue;
                }

                $client->intervenants()->create([
                    'type_intervenant' => $type,
                    'nom' => $ligne['nom'],
                    'role' => $ligne['role'] ?? null,
                    'pourcentage_detention' => $ligne['pourcentage_detention'] ?? null,
                ]);
            }
        }

        return redirect()
            ->route('tenant.clients.kyc-morale.edit', $client)
            ->with('status', 'KYC société enregistré.');
    }
}
