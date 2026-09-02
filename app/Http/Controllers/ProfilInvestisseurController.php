<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Services\ProfilInvestisseurScoringService;
use App\Models\VerificationClient;
use App\Services\VerificationCodeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfilInvestisseurController extends Controller
{
    public function edit(Client $client, VerificationCodeService $verification): View
    {
        $client->load('profilInvestisseur');

        $reponses = $client->profilInvestisseur->reponses ?? [];

        if (empty($reponses['risque_1_profil_investisseur']) && $client->date_naissance) {
            $reponses['risque_1_profil_investisseur'] = $client->date_naissance->format('Y-m-d');
        }

        $client->loadMissing('kyc', 'personnesACharge', 'patrimoineObjectifs');
        $estCouple = in_array($client->kyc?->situation_familiale, ['marie', 'pacse'], true);
        $nbEnfants = $client->personnesACharge->count();

        if (empty($reponses['risque_2_profil_investisseur'])) {
            $reponses['risque_2_profil_investisseur'] = 1 + ($estCouple ? 1 : 0) + $nbEnfants;
        }

        if (empty($reponses['risque_3_profil_investisseur'])) {
            $partsBase = $estCouple ? 2.0 : 1.0;
            $partsEnfants = min($nbEnfants, 2) * 0.5 + max($nbEnfants - 2, 0) * 1.0;
            $reponses['risque_3_profil_investisseur'] = $partsBase + $partsEnfants;
        }

        if (! VerificationClient::where('client_id', $client->id)->where('module', 'profil_investisseur')->exists()) {
            $verification->enregistrerModification($client, 'profil_investisseur');
        }

        $verificationClient = VerificationClient::where('client_id', $client->id)
            ->where('module', 'profil_investisseur')
            ->first();

        $verificationRequise = ! $verificationClient?->verifie_le;

        return view('tenant.clients.profil-investisseur', [
            'client' => $client,
            'reponses' => $reponses,
            'verificationRequise' => $verificationRequise,
        ]);
    }

    public function update(
        Request $request,
        Client $client,
        ProfilInvestisseurScoringService $scoring,
        VerificationCodeService $verification,
        \App\Services\AI\ProfilInvestisseurAnalysisService $profilAnalysis
    ): RedirectResponse
    {
        $validated = $request->validate([
            'reponses' => ['required', 'array'],
            'signe_le' => ['nullable', 'date'],
            'accepte_cgu' => ['nullable', 'boolean'],
        ]);

        $codeSaisi = $validated['reponses']['code_de_verification_client'] ?? null;
        $codeValide = false;

        $verificationClient = VerificationClient::where('client_id', $client->id)
            ->where('module', 'profil_investisseur')
            ->first();

        $verificationRequise = ! $verificationClient?->verifie_le;

        if ($verificationRequise && empty($codeSaisi)) {
            return back()
                ->withErrors(['code_de_verification_client' => 'Le code de vérification client est obligatoire pour la première validation.'])
                ->withInput();
        }

        if (! empty($codeSaisi)) {
            if (! $verification->verifierCode($client, 'profil_investisseur', $codeSaisi)) {
                return back()->withErrors(['code_de_verification_client' => 'Code de vérification incorrect.'])->withInput();
            }
            $codeValide = true;
        }

        unset($validated['reponses']['code_de_verification_client']);

        $resultats = $scoring->score($validated['reponses']);

        $client->profilInvestisseur()->updateOrCreate(
            ['client_id' => $client->id],
            array_merge($resultats, [
                'reponses' => $validated['reponses'],
                'signe_le' => now(),
                'accepte_cgu' => $request->boolean('accepte_cgu'),
            ])
        );

        /*
         * Analyse Profil Investisseur native Laravel / OpenAI.
         *
         * Le profil est sauvegardé avant l'analyse.
         * Une erreur OpenAI ne doit jamais empêcher
         * l'enregistrement du questionnaire.
         */
        try {

            file_put_contents(
                '/tmp/wendee-profil-ai.log',
                date('Y-m-d H:i:s') . " | AVANT ANALYSE | client=" . $client->id . PHP_EOL,
                FILE_APPEND
            );

            $analysis = $profilAnalysis->analyze($client);

            file_put_contents(
                '/tmp/wendee-profil-ai.log',
                date('Y-m-d H:i:s')
                . " | APRÈS ANALYSE | client="
                . $client->id
                . " | analysis="
                . $analysis->id
                . " | status="
                . $analysis->status
                . PHP_EOL,
                FILE_APPEND
            );

        } catch (\Throwable $e) {

            file_put_contents(
                '/tmp/wendee-profil-ai.log',
                date('Y-m-d H:i:s')
                . " | ERREUR | client="
                . $client->id
                . " | "
                . $e->getMessage()
                . PHP_EOL,
                FILE_APPEND
            );
            \Illuminate\Support\Facades\Log::error(
                'Erreur analyse Profil Investisseur OpenAI',
                [
                    'client_id' => $client->id,
                    'error' => $e->getMessage(),
                ]
            );
        }

        if ($codeValide) {
            $message = 'Profil investisseur validé par le client.';
        } else {
            $verification->enregistrerModification($client, 'profil_investisseur');
            $message = 'Profil investisseur enregistré.';
        }

        return redirect()
            ->route('tenant.clients.profil.edit', $client)
            ->with('status', $message);
    }
}
