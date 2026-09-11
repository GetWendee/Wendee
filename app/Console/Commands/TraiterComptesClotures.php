<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Notifications\ArchivageImminentNotification;
use Illuminate\Console\Command;

/**
 * À exécuter une fois par jour (voir routes/console.php). Deux passes,
 * indépendantes l'une de l'autre :
 *
 * 1. Notifie (in-app + email) le conseiller d'un dossier clôturé qui sera
 *    archivé dans 3 jours, une seule fois par cycle de clôture
 *    (notification_archivage_envoyee_le évite les doublons).
 * 2. Archive les dossiers clôturés depuis plus de
 *    Client::JOURS_AVANT_ARCHIVAGE jours, faute de réactivation entre-temps.
 *
 * Ne touche jamais aux dossiers déjà archivés : leur réactivation est un
 * acte humain (courtier, ou demande du conseiller), jamais automatique.
 */
class TraiterComptesClotures extends Command
{
    protected $signature = 'clients:traiter-clotures';

    protected $description = "Notifie les comptes clôturés arrivant à échéance (J-3) et archive ceux ayant atteint 180 jours de clôture";

    public function handle(): int
    {
        $joursAvantArchivage = Client::JOURS_AVANT_ARCHIVAGE;

        $aNotifier = Client::query()
            ->whereNotNull('cloture_le')
            ->whereNull('archive_le')
            ->whereNull('notification_archivage_envoyee_le')
            ->where('cloture_le', '<=', now()->subDays($joursAvantArchivage - 3))
            ->with('conseiller')
            ->get();

        foreach ($aNotifier as $client) {
            if ($client->conseiller) {
                $client->conseiller->notify(new ArchivageImminentNotification($client));
            }

            $client->notification_archivage_envoyee_le = now();
            $client->save();
        }

        $aArchiver = Client::query()
            ->whereNotNull('cloture_le')
            ->whereNull('archive_le')
            ->where('cloture_le', '<=', now()->subDays($joursAvantArchivage))
            ->get();

        foreach ($aArchiver as $client) {
            $client->archive_le = now();
            $client->save();
        }

        $this->info(
            $aNotifier->count().' notification(s) d\'archivage imminent envoyée(s), '
            .$aArchiver->count().' dossier(s) archivé(s).'
        );

        return self::SUCCESS;
    }
}
