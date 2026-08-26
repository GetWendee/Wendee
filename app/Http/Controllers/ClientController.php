<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(): View
    {
        $clients = Client::with('conseiller')->orderBy('nom')->paginate(20);

        return view('tenant.clients.index', ['clients' => $clients]);
    }

    public function create(): View
    {
        return view('tenant.clients.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'civilite' => ['nullable', 'string', 'in:M.,Mme'],
            'prenom' => ['required', 'string', 'max:255'],
            'nom' => ['required', 'string', 'max:255'],
            'date_naissance' => ['nullable', 'date'],
            'telephone_mobile' => ['nullable', 'string', 'max:30'],
            'telephone_domicile' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'adresse' => ['nullable', 'string', 'max:255'],
            'code_postal' => ['nullable', 'string', 'max:10'],
            'ville' => ['nullable', 'string', 'max:255'],
            'pays' => ['nullable', 'string', 'max:255'],
        ]);

        $client = Client::create($validated + [
            'conseiller_id' => $request->user()->id,
        ]);

        return redirect()->route('tenant.clients.show', $client)->with('status', 'Client créé.');
    }

    public function show(Client $client): View
    {
        return view('tenant.clients.show', ['client' => $client]);
    }
}
