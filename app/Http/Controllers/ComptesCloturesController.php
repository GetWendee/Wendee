<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use App\Notifications\DemandeReactivationNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ComptesCloturesController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $role = $user->effectiveRole();

        abort_unless(in_array($role, ['courtier', 'conseiller'], true), 403);

        $limiterAuPerimetre = $role === 'conseiller' && ! $user->voitTousLesClients();

        $clotures = Client::query()
            ->clotures()
            ->with('conseiller')
            ->when($limiterAuPerimetre, fn ($q) => $q->where('conseiller_id', $user->id))
            ->orderBy('cloture_le')
            ->get();

        $archives = Client::query()
            ->archives()
            ->with('conseiller')
            ->when($limiterAuPerimetre, fn ($q) => $q->where('conseiller_id', $user->id))
            ->orderByDesc('archive_le')
            ->get();

        return view('tenant.comptes-clotures.index', [
            'wdRole' => $role,
            'clotures' => $clotures,
            'archives' => $archives,
        ]);
    }

    /**
     * Réactivation immédiate. Autorisée pour un dossier simplement clôturé
     * (courtier ou conseiller du client), mais réservée au courtier une
     * fois le dossier archivé — voir demanderReactivation() pour le
     * conseiller dans ce cas.
     */
    public function reactiver(Request $request, Client $client): RedirectResponse
    {
        $user = $request->user();
        $role = $user->effectiveRole();

        abort_unless(in_array($role, ['courtier', 'conseiller'], true), 403);
        abort_unless(
            $client->conseiller_id === $user->id || $user->voitTousLesClients(),
            403
        );

        if ($client->estArchive()) {
            abort_unless($role === 'courtier', 403, 'Seul le courtier peut réactiver un dossier archivé.');
            abort_unless($client->estEncoreReactivable(), 403, 'Ce dossier n\'est plus réactivable (délai de 5 ans dépassé).');
        }

        $client->reactiver();

        return redirect()
            ->route('tenant.comptes-clotures.index')
            ->with('status', $client->nomAffichage().' a été réactivé.');
    }

    /**
     * Le conseiller ne peut pas réactiver lui-même un dossier archivé :
     * il envoie une demande au courtier, qui reste seul décisionnaire.
     */
    public function demanderReactivation(Request $request, Client $client): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user->effectiveRole() === 'conseiller', 403);
        abort_unless(
            $client->conseiller_id === $user->id || $user->voitTousLesClients(),
            403
        );
        abort_unless($client->estArchive(), 403);
        abort_unless($client->estEncoreReactivable(), 403, 'Ce dossier n\'est plus réactivable (délai de 5 ans dépassé).');

        if ($client->demandeReactivationEnAttente()) {
            return redirect()
                ->route('tenant.comptes-clotures.index')
                ->with('status', 'Une demande de réactivation est déjà en attente pour ce dossier.');
        }

        $client->demanderReactivation();

        User::query()
            ->where('role', 'courtier')
            ->get()
            ->each(fn (User $courtier) => $courtier->notify(
                new DemandeReactivationNotification($client, $user)
            ));

        return redirect()
            ->route('tenant.comptes-clotures.index')
            ->with('status', 'Demande de réactivation envoyée au courtier.');
    }
}
