<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Notifie les comptes clôturés arrivant à échéance (J-3) et archive ceux
// ayant atteint 180 jours de clôture. Nécessite que le cron Laravel soit
// configuré sur le serveur : * * * * * cd /var/www/wendee && php artisan schedule:run >> /dev/null 2>&1
Schedule::command('clients:traiter-clotures')->dailyAt('06:00');
