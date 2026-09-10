<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function lire(Request $request, DatabaseNotification $notification): RedirectResponse
    {
        $user = $request->user();

        abort_unless(
            $notification->notifiable_id === $user->id
                && $notification->notifiable_type === get_class($user),
            403
        );

        $notification->markAsRead();

        $clientId = $notification->data['client_id'] ?? null;
        $client = $clientId ? Client::find($clientId) : null;

        if ($client) {
            return redirect()->route('tenant.clients.show', $client);
        }

        return redirect()->route('tenant.dashboard');
    }

    public function toutMarquerLu(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return back();
    }
}
