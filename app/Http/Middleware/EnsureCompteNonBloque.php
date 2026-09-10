<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureCompteNonBloque
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $user->bloque_le) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $route = function_exists('tenant') && tenant() ? 'tenant.login' : 'login';

            return redirect()->route($route)->with('status', 'Ce compte a été bloqué par mesure de sécurité. Contactez votre conseiller.');
        }

        return $next($request);
    }
}
