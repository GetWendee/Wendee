<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\PatrimoineFiscalite;
use App\Models\VerificationClient;
use App\Services\VerificationCodeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PatrimoineController extends Controller
{
    private const CATEGORIES = ['actif_financier', 'actif_non_financier', 'passif', 'revenu', 'charge'];

    public function edit(Client $client, VerificationCodeService $verification): View
    {
        $client->load('patrimoineElements', 'patrimoineFiscalite', 'patrimoineObjectifs', 'kyc');

        if (! VerificationClient::where('client_id', $client->id)->where('module', 'patrimoine')->exists()) {
            $verification->enregistrerModification($client, 'patrimoine');
        }

        $verificationClient = VerificationClient::where('client_id', $client->id)
            ->where('module', 'patrimoine')
            ->first();

        $verificationRequise = ! $verificationClient?->verifie_le;

        $elements = [];
        foreach (self::CATEGORIES as $categorie) {
            $elements[$categorie] = $client->patrimoineElements
                ->where('categorie', $categorie)
                ->values()
                ->map(fn ($e) => [
                    'nature' => $e->nature,
                    'designation' => $e->designation,
                    'montant' => $e->montant,
                    'mode_detention' => $e->mode_detention,
                    'type_pret' => $e->type_pret,
                    'date_souscription' => $e->date_souscription?->format('Y-m-d'),
                    'duree' => $e->duree,
                    'taux_interet' => $e->taux_interet,
                    'taux_assurance' => $e->taux_assurance,
                    'bien' => $e->bien,
                    'quotite_detention' => $e->quotite_detention,
                ]);
        }

        $fiscalite = $client->patrimoineFiscalite;
        $objectifs = $client->patrimoineObjectifs->values()->map(fn ($o) => [
            'objectif' => $o->objectif,
            'horizon' => $o->horizon,
        ]);

        $foyerAvecConjoint = in_array($client->kyc?->situation_familiale, ['marie', 'pacse'], true);

        return view('tenant.clients.patrimoine', [
            'client' => $client,
            'elements' => $elements,
            'fiscalite' => $fiscalite,
            'objectifs' => $objectifs,
            'foyerAvecConjoint' => $foyerAvecConjoint,
            'verificationRequise' => $verificationRequise,
        ]);
    }

    public function update(
        Request $request,
        Client $client,
        VerificationCodeService $verification,
        \App\Services\AI\PatrimoineAnalysisService $patrimoineAnalysis
    ): RedirectResponse
    {
        $validated = $request->validate([
            'elements' => ['nullable', 'array'],
            'elements.*.categorie' => ['required', 'in:' . implode(',', self::CATEGORIES)],
            'elements.*.nature' => ['nullable', 'string', 'max:255'],
            'elements.*.designation' => ['nullable', 'string', 'max:255'],
            'elements.*.montant' => ['nullable', 'numeric'],
            'elements.*.mode_detention' => ['nullable', 'string', 'max:255'],
            'elements.*.type_pret' => ['nullable', 'string', 'max:255'],
            'elements.*.date_souscription' => ['nullable', 'date'],
            'elements.*.duree' => ['nullable', 'integer'],
            'elements.*.taux_interet' => ['nullable', 'numeric'],
            'elements.*.taux_assurance' => ['nullable', 'numeric'],
            'elements.*.bien' => ['nullable', 'string', 'in:propre,commun,conjoint,indivision'],
            'elements.*.quotite_detention' => ['nullable', 'integer', 'min:0', 'max:100'],

            'resident_fiscal_francais' => ['nullable', 'string', 'in:oui,non'],
            'irpp_montant' => ['nullable', 'numeric'],
            'irpp_nombre_parts' => ['nullable', 'numeric'],
            'connait_tmi_ir' => ['nullable', 'string', 'in:oui,non'],
            'tmi_ir' => ['nullable', 'string', 'in:0,11,30,41,45'],
            'reductions_credits_impots' => ['nullable', 'numeric'],
            'impot_net_a_payer' => ['nullable', 'numeric'],
            'contributions_sociales' => ['nullable', 'numeric'],

            'impose_ifi' => ['nullable', 'string', 'in:oui,non'],
            'base_imposable_ifi' => ['nullable', 'numeric'],
            'connait_tmi_ifi' => ['nullable', 'string', 'in:oui,non'],
            'tmi_ifi' => ['nullable', 'string', 'in:0,0.5,0.7,1,1.25,1.5'],
            'reductions_ifi' => ['nullable', 'numeric'],
            'ifi_net_a_payer' => ['nullable', 'numeric'],

            'us_person' => ['nullable', 'string', 'in:oui,non'],
            'us_citoyen' => ['nullable', 'string', 'in:oui,non'],
            'us_resident' => ['nullable', 'string', 'in:oui,non'],
            'us_carte_verte' => ['nullable', 'string', 'in:oui,non'],
            'us_sejour' => ['nullable', 'string', 'in:oui,non'],
            'us_entite' => ['nullable', 'string', 'in:oui,non'],
            'us_autre_raison' => ['nullable', 'string', 'in:oui,non'],
            'us_tin' => ['nullable', 'string', 'in:oui,non'],

            'objectifs' => ['nullable', 'array'],
            'objectifs.*.objectif' => ['nullable', 'string', 'max:255'],
            'objectifs.*.horizon' => ['nullable', 'integer'],
            'effort_epargne_mensuel' => ['nullable', 'numeric'],
            'montant_patrimoine_total' => ['nullable', 'numeric'],
            'montant_revenus_annuels' => ['nullable', 'numeric'],

            'code_de_verification_client' => ['nullable', 'string', 'max:10'],
        ], [], [
            'elements.*.categorie' => 'catégorie',
            'elements.*.nature' => 'nature',
            'elements.*.designation' => 'désignation',
            'elements.*.montant' => 'montant',
            'elements.*.mode_detention' => 'mode de détention',
        ]);

        $codeSaisi = $validated['code_de_verification_client'] ?? null;
        $codeValide = false;

        $verificationClient = VerificationClient::where('client_id', $client->id)
            ->where('module', 'patrimoine')
            ->first();

        $verificationRequise = ! $verificationClient?->verifie_le;

        if ($verificationRequise && empty($codeSaisi)) {
            return back()
                ->withErrors(['code_de_verification_client' => 'Le code de vérification client est obligatoire pour la première validation.'])
                ->withInput();
        }

        if (! empty($codeSaisi)) {
            if (! $verification->verifierCode($client, 'patrimoine', $codeSaisi)) {
                return back()->withErrors(['code_de_verification_client' => 'Code de vérification incorrect.'])->withInput();
            }
            $codeValide = true;
        }

        $client->patrimoineElements()->delete();

        foreach ($validated['elements'] ?? [] as $element) {
            if (empty($element['nature'])) {
                continue;
            }
            $client->patrimoineElements()->create([
                'categorie' => $element['categorie'],
                'nature' => $element['nature'],
                'designation' => $element['designation'] ?? null,
                'montant' => $element['montant'] ?? 0,
                'mode_detention' => $element['mode_detention'] ?? null,
                'type_pret' => $element['type_pret'] ?? null,
                'date_souscription' => $element['date_souscription'] ?? null,
                'duree' => $element['duree'] ?? null,
                'taux_interet' => $element['taux_interet'] ?? null,
                'taux_assurance' => $element['taux_assurance'] ?? null,
                'bien' => $element['bien'] ?? null,
                'quotite_detention' => $element['quotite_detention'] ?? null,
            ]);
        }

        PatrimoineFiscalite::updateOrCreate(
            ['client_id' => $client->id],
            [
                'resident_fiscal_francais' => $validated['resident_fiscal_francais'] ?? null,
                'irpp_montant' => $validated['irpp_montant'] ?? null,
                'irpp_nombre_parts' => $validated['irpp_nombre_parts'] ?? null,
                'connait_tmi_ir' => $validated['connait_tmi_ir'] ?? null,
                'tmi_ir' => $validated['tmi_ir'] ?? null,
                'reductions_credits_impots' => $validated['reductions_credits_impots'] ?? null,
                'impot_net_a_payer' => $validated['impot_net_a_payer'] ?? null,
                'contributions_sociales' => $validated['contributions_sociales'] ?? null,
                'impose_ifi' => $validated['impose_ifi'] ?? null,
                'base_imposable_ifi' => $validated['base_imposable_ifi'] ?? null,
                'connait_tmi_ifi' => $validated['connait_tmi_ifi'] ?? null,
                'tmi_ifi' => $validated['tmi_ifi'] ?? null,
                'reductions_ifi' => $validated['reductions_ifi'] ?? null,
                'ifi_net_a_payer' => $validated['ifi_net_a_payer'] ?? null,
                'us_person' => $validated['us_person'] ?? null,
                'us_citoyen' => $validated['us_citoyen'] ?? null,
                'us_resident' => $validated['us_resident'] ?? null,
                'us_carte_verte' => $validated['us_carte_verte'] ?? null,
                'us_sejour' => $validated['us_sejour'] ?? null,
                'us_entite' => $validated['us_entite'] ?? null,
                'us_autre_raison' => $validated['us_autre_raison'] ?? null,
                'us_tin' => $validated['us_tin'] ?? null,
                'effort_epargne_mensuel' => $validated['effort_epargne_mensuel'] ?? null,
                'montant_patrimoine_total' => $validated['montant_patrimoine_total'] ?? null,
                'montant_revenus_annuels' => $validated['montant_revenus_annuels'] ?? null,
            ]
        );

        $client->patrimoineObjectifs()->delete();
        foreach ($validated['objectifs'] ?? [] as $objectif) {
            if (empty($objectif['objectif'])) {
                continue;
            }
            $client->patrimoineObjectifs()->create([
                'objectif' => $objectif['objectif'],
                'horizon' => $objectif['horizon'] ?? null,
            ]);
        }

        /*
         * Analyse patrimoniale native Laravel / OpenAI.
         *
         * Une erreur OpenAI ne doit jamais empêcher
         * l'enregistrement du patrimoine.
         */
        try {
            $patrimoineAnalysis->analyze($client);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error(
                'Erreur analyse patrimoine OpenAI',
                [
                    'client_id' => $client->id,
                    'error' => $e->getMessage(),
                ]
            );
        }

        if ($codeValide) {
            $message = 'Patrimoine validé par le client.';
        } else {
            $verification->enregistrerModification($client, 'patrimoine');
            $message = 'Patrimoine enregistré.';
        }

        return redirect()->route('tenant.clients.patrimoine.edit', $client)->with('status', $message);
    }
}
