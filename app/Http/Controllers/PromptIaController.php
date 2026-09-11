<?php

namespace App\Http\Controllers;

use App\Models\PromptIa;
use App\Services\PromptIaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PromptIaController extends Controller
{
    public function index(): View
    {
        return view('prompts-ia.index', [
            'prompts' => PromptIa::orderBy('titre')->get(),
        ]);
    }

    /**
     * Propose une nouvelle version du prompt : elle est stockée en attente
     * et un code de confirmation part par email à l'auteur. Le contenu
     * actif (utilisé par le moteur IA) n'est modifié qu'à la confirmation.
     */
    public function update(Request $request, PromptIa $prompt): RedirectResponse
    {
        $validated = $request->validate([
            'contenu' => ['required', 'string'],
        ]);

        app(PromptIaService::class)->proposerModification(
            $prompt,
            $validated['contenu'],
            $request->user()
        );

        return redirect()
            ->route('prompts-ia.index')
            ->with('status', 'code-envoye')
            ->with('prompt_cle', $prompt->cle);
    }

    public function confirmer(Request $request, PromptIa $prompt): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:10'],
        ]);

        $ok = app(PromptIaService::class)->confirmer($prompt, $validated['code']);

        return redirect()
            ->route('prompts-ia.index')
            ->with('status', $ok ? 'confirme' : 'code-invalide')
            ->with('prompt_cle', $prompt->cle);
    }

    public function annuler(PromptIa $prompt): RedirectResponse
    {
        app(PromptIaService::class)->annulerModification($prompt);

        return redirect()
            ->route('prompts-ia.index')
            ->with('status', 'annule')
            ->with('prompt_cle', $prompt->cle);
    }
}
