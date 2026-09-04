<?php

namespace App\Http\Middleware;

use App\Models\Client;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bloque l'accès à une route liée à un client précis ({client} dans l'URL)
 * si l'utilisateur connecté n'a pas le droit de voir ce client
 * (voir Client::estVisiblePar). Empêche de "jouer avec les URL"
 * pour accéder à la fiche d'un client qui n'est pas le sien.
 */
class EnsureClientAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $client = $request->route('client');

        if ($client instanceof Client) {
            $user = $request->user();

            abort_unless($user && $client->estVisiblePar($user), 403);
        }

        return $next($request);
    }
}
