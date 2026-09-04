<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PortefeuilleCabinetController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $conseillers = collect();
        $apporteurs = collect();
        $clients = collect();

        if ($user->effectiveRole() === 'courtier') {
            /*
             * Le courtier porte également la casquette de conseiller.
             * Il apparaît donc en premier dans les conseillers.
             */
            $conseillers = collect([$user])->merge(
                User::query()
                    ->where('role', 'conseiller')
                    ->where('parent_id', $user->id)
                    ->orderBy('name')
                    ->get()
            );

            $conseillerIds = $conseillers
                ->pluck('id')
                ->filter(fn ($id) => $id !== $user->id)
                ->values();

            $apporteurs = User::query()
                ->where('role', 'apporteur')
                ->where(function ($query) use ($user, $conseillerIds) {
                    $query->where('parent_id', $user->id);

                    if ($conseillerIds->isNotEmpty()) {
                        $query->orWhereIn('parent_id', $conseillerIds);
                    }
                })
                ->with('parent')
                ->orderBy('name')
                ->get();

            $clients = Client::query()
                ->with(['conseiller', 'apporteur', 'kyc', 'profilInvestisseur', 'patrimoineElements'])
                ->orderBy('nom')
                ->orderBy('prenom')
                ->get();
        }

        elseif ($user->effectiveRole() === 'conseiller') {
            $conseillers = collect([$user]);

            $apporteurs = User::query()
                ->where('role', 'apporteur')
                ->where('parent_id', $user->id)
                ->with('parent')
                ->orderBy('name')
                ->get();

            $clientsQuery = Client::query()
                ->with(['conseiller', 'apporteur', 'kyc', 'profilInvestisseur', 'patrimoineElements'])
                ->orderBy('nom')
                ->orderBy('prenom');

            /*
             * Un courtier peut accorder à un conseiller le droit de voir
             * tous les clients du cabinet. Ce droit ne concerne que les
             * clients, jamais la liste des apporteurs ci-dessus.
             */
            if (! $user->voitTousLesClients()) {
                $clientsQuery->where('conseiller_id', $user->id);
            }

            $clients = $clientsQuery->get();
        }

        elseif ($user->effectiveRole() === 'apporteur') {
            $apporteurs = collect([$user]);

            $clients = Client::query()
                ->with(['conseiller', 'apporteur', 'kyc', 'profilInvestisseur', 'patrimoineElements'])
                ->where('apporteur_id', $user->id)
                ->orderBy('nom')
                ->orderBy('prenom')
                ->get();
        }

        return view('tenant.portefeuille-cabinet.index', [
            'user' => $user,
            'conseillers' => $conseillers,
            'apporteurs' => $apporteurs,
            'clients' => $clients,
        ]);
    }
}
