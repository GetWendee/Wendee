<?php

namespace App\Http\Controllers;

use App\Models\Client;
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
        $client->load('patrimoineElements');

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
                ]);
        }

        return view('tenant.clients.patrimoine', [
            'client' => $client,
            'elements' => $elements,
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
