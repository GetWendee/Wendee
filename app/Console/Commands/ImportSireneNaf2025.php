<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use SimpleXMLElement;
use ZipArchive;

class ImportSireneNaf2025 extends Command
{
    protected $signature = 'sirene:import-naf-2025
                            {--dry-run : Analyse sans modifier la base}';

    protected $description = 'Importe le référentiel NAF 2025 officiel INSEE';

    public function handle(): int
    {
        $path = storage_path(
            'app/sirene-references/naf_2025_structure.xlsx'
        );

        $this->info('Fichier : '.$path);

        if (!is_file($path)) {
            $this->error('Fichier introuvable.');
            return self::FAILURE;
        }

        /*
        |--------------------------------------------------------------------------
        | CONNEXION CENTRALE EXPLICITE
        |--------------------------------------------------------------------------
        */

        $connection = DB::connection('mysql');

        $this->info(
            'Base utilisée : '
            . $connection->getDatabaseName()
        );

        if ($connection->getDatabaseName() !== 'wendee_central') {
            $this->error(
                'ERREUR : la connexion mysql ne pointe pas vers wendee_central.'
            );

            return self::FAILURE;
        }

        if (! $connection->getSchemaBuilder()->hasTable('sirene_naf_2025')) {
            $this->error(
                'La table sirene_naf_2025 n’existe pas dans wendee_central.'
            );

            return self::FAILURE;
        }

        /*
        |--------------------------------------------------------------------------
        | LECTURE XLSX
        |--------------------------------------------------------------------------
        */

        $zip = new ZipArchive();

        if ($zip->open($path) !== true) {
            $this->error('Impossible d’ouvrir le fichier XLSX.');
            return self::FAILURE;
        }

        $sharedStrings = $this->readSharedStrings($zip);

        $workbook = simplexml_load_string(
            $zip->getFromName('xl/workbook.xml')
        );

        $rels = simplexml_load_string(
            $zip->getFromName('xl/_rels/workbook.xml.rels')
        );

        if (! $workbook || ! $rels) {
            $zip->close();

            $this->error('Structure XLSX invalide.');

            return self::FAILURE;
        }

        $relations = [];

        foreach ($rels->Relationship as $relation) {
            $relations[(string) $relation['Id']]
                = (string) $relation['Target'];
        }

        $target = null;

        foreach ($workbook->sheets->sheet as $sheet) {

            if ((string) $sheet['name'] !== 'Sous-classes') {
                continue;
            }

            $rid = (string) $sheet->attributes(
                'http://schemas.openxmlformats.org/officeDocument/2006/relationships'
            )->id;

            $target = $relations[$rid] ?? null;

            break;
        }

        if (! $target) {
            $zip->close();

            $this->error(
                "Feuille 'Sous-classes' introuvable."
            );

            return self::FAILURE;
        }

        $target = ltrim($target, '/');

        if (! str_starts_with($target, 'xl/')) {
            $target = 'xl/'.$target;
        }

        $sheetXml = simplexml_load_string(
            $zip->getFromName($target)
        );

        if (! $sheetXml) {
            $zip->close();

            $this->error(
                'Impossible de lire la feuille Sous-classes.'
            );

            return self::FAILURE;
        }

        $rows = [];

        foreach ($sheetXml->sheetData->row as $row) {

            $values = [];

            foreach ($row->c as $cell) {

                $reference = (string) $cell['r'];

                $column = preg_replace(
                    '/[0-9]+/',
                    '',
                    $reference
                );

                $values[$column] = $this->cellValue(
                    $cell,
                    $sharedStrings
                );
            }

            $code = trim($values['A'] ?? '');
            $libelle = trim($values['B'] ?? '');

            if (
                $code === ''
                || $code === 'NAF 2025 sous-classes'
            ) {
                continue;
            }

            $rows[] = [
                'code' => $code,
                'niveau' => 'sous-classe',
                'libelle' => $libelle,
                'libelle_complet' => $libelle,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        $zip->close();

        $this->info(
            'Lignes préparées : '.count($rows)
        );

        if (count($rows) !== 748) {
            $this->error(
                'ERREUR : 748 lignes étaient attendues.'
            );

            return self::FAILURE;
        }

        $test = collect($rows)->firstWhere(
            'code',
            '70.20Y'
        );

        if (! $test) {
            $this->error(
                'ERREUR : code 70.20Y absent.'
            );

            return self::FAILURE;
        }

        $this->info(
            '70.20Y : '.$test['libelle']
        );

        /*
        |--------------------------------------------------------------------------
        | SIMULATION
        |--------------------------------------------------------------------------
        */

        if ($this->option('dry-run')) {

            $this->info(
                'Mode simulation : aucune modification.'
            );

            return self::SUCCESS;
        }

        /*
        |--------------------------------------------------------------------------
        | IMPORT CENTRAL
        |--------------------------------------------------------------------------
        */

        $this->info(
            'Suppression des anciennes données...'
        );

        $connection->table('sirene_naf_2025')->delete();

        $this->info(
            'Insertion dans wendee_central.sirene_naf_2025...'
        );

        foreach (array_chunk($rows, 100) as $index => $chunk) {

            $codes = array_column($chunk, 'code');

            $duplicates = array_keys(
                array_filter(
                    array_count_values($codes),
                    fn ($count) => $count > 1
                )
            );

            $this->info(
                'Batch '.($index + 1)
                .' : '
                .count($chunk)
                .' lignes.'
            );

            if (! empty($duplicates)) {

                $this->error(
                    'DOUBLONS DÉTECTÉS DANS LE BATCH : '
                    .implode(', ', $duplicates)
                );

                return self::FAILURE;
            }

            try {

                $connection
                    ->table('sirene_naf_2025')
                    ->insert($chunk);

            } catch (\\Throwable $e) {

                $this->error(
                    'ERREUR INSERT BATCH '.($index + 1)
                );

                $this->error(
                    'Message SQL : '.$e->getMessage()
                );

                $this->error(
                    'Codes du batch : '.implode(', ', $codes)
                );

                return self::FAILURE;
            }

            $this->info(
                'Batch '.($index + 1)
                .' : INSERT OK.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | CONTRÔLE IMMÉDIAT
        |--------------------------------------------------------------------------
        */

        $count = $connection
            ->table('sirene_naf_2025')
            ->count();

        $this->info(
            'Nombre de lignes après import : '.$count
        );

        if ($count !== 748) {

            $this->error(
                'ERREUR : le nombre final de lignes est incorrect.'
            );

            return self::FAILURE;
        }

        $row = $connection
            ->table('sirene_naf_2025')
            ->where('code', '70.20Y')
            ->first();

        if (! $row) {

            $this->error(
                'ERREUR : 70.20Y absent après import.'
            );

            return self::FAILURE;
        }

        $this->info(
            '70.20Y correctement enregistré.'
        );

        $this->info(
            'Libellé : '.$row->libelle
        );

        $this->info(
            'IMPORT NAF 2025 TERMINÉ AVEC SUCCÈS.'
        );

        return self::SUCCESS;
    }

    private function readSharedStrings(
        ZipArchive $zip
    ): array {

        $result = [];

        $content = $zip->getFromName(
            'xl/sharedStrings.xml'
        );

        if ($content === false) {
            return $result;
        }

        $xml = simplexml_load_string($content);

        if (! $xml) {
            return $result;
        }

        foreach ($xml->si as $si) {

            $text = '';

            if (isset($si->t)) {

                $text = (string) $si->t;

            } else {

                foreach ($si->r as $run) {
                    $text .= (string) ($run->t ?? '');
                }
            }

            $result[] = $text;
        }

        return $result;
    }

    private function cellValue(
        SimpleXMLElement $cell,
        array $sharedStrings
    ): string {

        $type = (string) $cell['t'];

        if (! isset($cell->v)) {
            return '';
        }

        $value = (string) $cell->v;

        if ($type === 's') {
            return $sharedStrings[(int) $value] ?? '';
        }

        return $value;
    }
}
