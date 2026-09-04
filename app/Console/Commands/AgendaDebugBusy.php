<?php

namespace App\Console\Commands;

use App\Models\CalendarConnection;
use App\Models\Tenant;
use App\Models\User;
use App\Services\Calendar\GoogleCalendarService;
use App\Services\Calendar\MicrosoftCalendarService;
use Illuminate\Console\Command;

class AgendaDebugBusy extends Command
{
    protected $signature = 'agenda:debug-busy {tenant} {email}';

    protected $description = "Diagnostic : affiche les connexions calendrier d'un user et interroge Google/Outlook pour voir les plages occupees renvoyees";

    public function handle(): int
    {
        $tenantId = $this->argument('tenant');
        $email = $this->argument('email');

        $tenant = Tenant::find($tenantId);

        if (! $tenant) {
            $this->error("Tenant introuvable : {$tenantId}");

            return self::FAILURE;
        }

        $tenant->run(function () use ($email) {
            $user = User::where('email', $email)->first();

            if (! $user) {
                $this->error("Utilisateur introuvable : {$email}");

                return;
            }

            $this->info("Utilisateur : {$user->name} (id {$user->id}, role {$user->role})");

            $connexions = CalendarConnection::where('user_id', $user->id)->get();

            if ($connexions->isEmpty()) {
                $this->error('Aucune connexion calendrier trouvee pour cet utilisateur.');

                return;
            }

            foreach ($connexions as $connexion) {
                $this->line('---');
                $this->info("Provider : {$connexion->provider}");
                $this->line("Email connecte : {$connexion->provider_email}");
                $this->line("Calendar ID : ".($connexion->calendar_id ?: 'primary'));
                $this->line("Token expire le : {$connexion->token_expires_at}");
                $this->line("Token expire ? : ".($connexion->isExpired() ? 'OUI' : 'non'));
                $this->line("A un refresh_token ? : ".($connexion->refresh_token ? 'oui' : 'NON'));

                $from = now()->startOfWeek();
                $to = now()->endOfWeek();

                $this->line("Recherche des plages occupees entre {$from} et {$to}...");

                try {
                    $service = $connexion->provider === 'google'
                        ? app(GoogleCalendarService::class)
                        : app(MicrosoftCalendarService::class);

                    $busy = $service->getBusyPeriods($connexion, $from, $to);

                    if (empty($busy)) {
                        $this->error('Aucune plage occupee renvoyee (liste vide). Voir storage/logs/laravel.log pour un eventuel warning API juste au-dessus.');
                    } else {
                        foreach ($busy as $plage) {
                            $this->info(" -> {$plage['start']} a {$plage['end']}");
                        }
                    }
                } catch (\Throwable $e) {
                    $this->error('Exception : '.$e->getMessage());
                }
            }
        });

        return self::SUCCESS;
    }
}
