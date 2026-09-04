<?php

namespace App\Http\Controllers;

use App\Mail\CommissionVerseeMail;
use App\Models\Commission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class CommissionController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        abort_unless($user && $user->effectiveRole() === 'courtier', 403);

        $aRecevoir = Commission::query()
            ->where('statut', 'a_recevoir')
            ->with(['apporteur', 'client'])
            ->orderBy('created_at')
            ->get();

        $virementsAFaire = Commission::query()
            ->where('statut', 'fonds_recus')
            ->with(['apporteur', 'client'])
            ->orderBy('fonds_recus_le')
            ->get();

        $derniersPaiements = Commission::query()
            ->where('statut', 'verse')
            ->with(['apporteur', 'client'])
            ->orderByDesc('verse_le')
            ->limit(15)
            ->get();

        return view('tenant.commissions.index', [
            'aRecevoir' => $aRecevoir,
            'virementsAFaire' => $virementsAFaire,
            'derniersPaiements' => $derniersPaiements,
        ]);
    }

    /**
     * Étape 1 : le cabinet confirme avoir reçu les fonds du client pour
     * les missions sélectionnées. Les commissions passent en "fonds_recus"
     * et deviennent éligibles au virement vers l'apporteur.
     */
    public function confirmerFondsRecus(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user && $user->effectiveRole() === 'courtier', 403);

        $validated = $request->validate([
            'commissions' => ['required', 'array', 'min:1'],
            'commissions.*' => ['integer'],
        ]);

        Commission::query()
            ->whereIn('id', $validated['commissions'])
            ->where('statut', 'a_recevoir')
            ->update(['statut' => 'fonds_recus', 'fonds_recus_le' => now()]);

        return redirect()->route('tenant.commissions.index')->with('status', 'Fonds reçus confirmés.');
    }

    /**
     * Étape 2 : le courtier valide le virement (information seulement,
     * aucun vrai virement bancaire n'est déclenché depuis Wendee). Bloqué
     * pour tout apporteur dont le RIB n'a pas été validé. Envoie un mail
     * récapitulatif à chaque apporteur concerné.
     */
    public function validerVirements(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user && $user->effectiveRole() === 'courtier', 403);

        $validated = $request->validate([
            'commissions' => ['required', 'array', 'min:1'],
            'commissions.*' => ['integer'],
        ]);

        $commissions = Commission::query()
            ->whereIn('id', $validated['commissions'])
            ->where('statut', 'fonds_recus')
            ->with('apporteur')
            ->get();

        $bloquees = 0;

        foreach ($commissions->groupBy('apporteur_id') as $lignesApporteur) {
            $apporteur = $lignesApporteur->first()->apporteur;

            if (! $apporteur || ! $apporteur->rib_valide) {
                $bloquees += $lignesApporteur->count();

                continue;
            }

            $ids = $lignesApporteur->pluck('id');

            Commission::query()->whereIn('id', $ids)->update([
                'statut' => 'verse',
                'verse_le' => now(),
            ]);

            $lignesVersees = Commission::query()->whereIn('id', $ids)->with('client')->get();

            if ($apporteur->email) {
                Mail::to($apporteur->email)->send(new CommissionVerseeMail($apporteur, $lignesVersees));
            }
        }

        $message = $bloquees > 0
            ? "Virements validés. {$bloquees} commission(s) ignorée(s) : RIB non validé pour l'apporteur concerné."
            : 'Virements validés, apporteurs notifiés par mail.';

        return redirect()->route('tenant.commissions.index')->with('status', $message);
    }
}
