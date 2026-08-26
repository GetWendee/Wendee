<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CabinetController extends Controller
{
    public function index(): View
    {
        $cabinets = Tenant::with('domains')->orderByDesc('created_at')->get();

        return view('cabinets.index', ['cabinets' => $cabinets]);
    }

    public function create(): View
    {
        return view('cabinets.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required', 'string', 'max:63', 'alpha_dash', 'lowercase',
                'unique:tenants,id',
            ],
            'courtier_name' => ['required', 'string', 'max:255'],
            'courtier_email' => ['required', 'string', 'email', 'max:255'],
        ]);

        $tenant = Tenant::create([
            'id' => $validated['slug'],
            'name' => $validated['name'],
        ]);

        $tenant->domains()->create([
            'domain' => $validated['slug'].'.wendee.fr',
        ]);

        $tempPassword = Str::password(12);

        $tenant->run(function () use ($validated, $tempPassword) {
            User::create([
                'name' => $validated['courtier_name'],
                'email' => $validated['courtier_email'],
                'password' => Hash::make($tempPassword),
            ]);
        });

        return redirect()->route('cabinets.index')->with('status', [
            'cabinet' => $validated['name'],
            'domain' => $validated['slug'].'.wendee.fr',
            'courtier_email' => $validated['courtier_email'],
            'temp_password' => $tempPassword,
        ]);
    }
}
