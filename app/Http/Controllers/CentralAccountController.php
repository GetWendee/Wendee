<?php
declare(strict_types=1);
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;
class CentralAccountController extends Controller
{
    public function create(): View
    {
        return view('comptes.create');
    }
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
        ]);
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Str::random(40),
            'activation_pending' => true,
        ]);
        Password::sendResetLink(['email' => $user->email]);
        return redirect()->route('dashboard')->with(
            'status_simple',
            "Compte créé pour {$user->name}. Un e-mail pour définir son mot de passe lui a été envoyé."
        );
    }
}
