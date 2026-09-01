<?php

namespace App\Services\Sirene;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

class SireneService
{
    /**
     * Recherche un établissement à partir de son SIRET.
     */
    public function findBySiret(string $siret): ?array
    {
        $siret = $this->normalizeSiret($siret);

        if (! $this->isValidSiret($siret)) {
            throw new RuntimeException(
                'Le SIRET doit contenir exactement 14 chiffres.'
            );
        }

        $url = rtrim(
            config('services.sirene.url'),
            '/'
        );

        $token = config('services.sirene.token');

        if (! $token) {
            throw new RuntimeException(
                'Le service SIRENE n’est pas configuré : token INSEE manquant.'
            );
        }

        try {

            $response = Http::acceptJson()
                ->withHeaders([
                    'X-INSEE-Api-Key-Integration' => $token,
                ])
                ->timeout(15)
                ->get(
                    $url . '/siret/' . $siret
                );

            if ($response->status() === 404) {
                return null;
            }

            if ($response->failed()) {

                Log::error(
                    'Erreur API SIRENE',
                    [
                        'siret' => $siret,
                        'status' => $response->status(),
                        'response' => $response->body(),
                    ]
                );

                throw new RuntimeException(
                    'Le service SIRENE est momentanément indisponible.'
                );
            }

            return $this->normalizeResponse(
                $response->json()
            );

        } catch (\Illuminate\Http\Client\ConnectionException $e) {

            Log::error(
                'Connexion API SIRENE impossible',
                [
                    'siret' => $siret,
                    'error' => $e->getMessage(),
                ]
            );

            throw new RuntimeException(
                'Impossible de contacter le service SIRENE.'
            );
        }
    }

    /**
     * Nettoie un SIRET saisi par l'utilisateur.
     */
    private function normalizeSiret(string $siret): string
    {
        return preg_replace(
            '/\D+/',
            '',
            $siret
        );
    }

    /**
     * Vérifie le format du SIRET.
     */
    private function isValidSiret(string $siret): bool
    {
        return strlen($siret) === 14
            && ctype_digit($siret);
    }

    /**
     * Transforme la réponse INSEE en structure Wendee.
     */
    private function normalizeResponse(array $data): array
    {
        $etablissement = $data['etablissement'] ?? [];

        $uniteLegale = $etablissement['uniteLegale'] ?? [];

        $adresse = $etablissement['adresseEtablissement'] ?? [];

        /*
        |--------------------------------------------------------------------------
        | PÉRIODE ÉTABLISSEMENT
        |--------------------------------------------------------------------------
        |
        | Les informations d'activité, d'état administratif et d'enseigne
        | sont portées par periodesEtablissement.
        |
        | On privilégie la période active puis, à défaut, la période
        | la plus récente.
        |
        */

        $periodes = $etablissement['periodesEtablissement'] ?? [];

        $periode = null;

        if (is_array($periodes)) {

            foreach ($periodes as $candidate) {

                if (
                    is_array($candidate) &&
                    ($candidate['etatAdministratifEtablissement'] ?? null) === 'A'
                ) {
                    $periode = $candidate;
                    break;
                }
            }

            if (! $periode && count($periodes)) {

                usort(
                    $periodes,
                    function ($a, $b) {
                        return strcmp(
                            (string) ($b['dateDebut'] ?? ''),
                            (string) ($a['dateDebut'] ?? '')
                        );
                    }
                );

                $periode = $periodes[0];
            }
        }

        $periode = is_array($periode)
            ? $periode
            : [];

        /*
        |--------------------------------------------------------------------------
        | IDENTIFIANTS
        |--------------------------------------------------------------------------
        */

        $siren = $etablissement['siren'] ?? null;

        $siret = $etablissement['siret'] ?? null;

        /*
        |--------------------------------------------------------------------------
        | ACTIVITÉS
        |--------------------------------------------------------------------------
        */

        $codeApe = $periode['activitePrincipaleEtablissement']
            ?? null;

        $nomenclatureApe = $periode['nomenclatureActivitePrincipaleEtablissement']
            ?? null;

        $codeApe2025 = $etablissement['activitePrincipaleNAF25Etablissement']
            ?? null;

        $nomenclatureApe2025 = $codeApe2025
            ? 'NAF2025'
            : null;

        /*
        |--------------------------------------------------------------------------
        | ÉTAT / CRÉATION / ENSEIGNE
        |--------------------------------------------------------------------------
        */

        $etatAdministratif = $periode['etatAdministratifEtablissement']
            ?? null;

        $dateCreation = $etablissement['dateCreationEtablissement']
            ?? null;

        $enseigne = $periode['enseigne1Etablissement']
            ?? null;

        /*
        |--------------------------------------------------------------------------
        | UNITÉ LÉGALE
        |--------------------------------------------------------------------------
        */

        $denomination = $uniteLegale['denominationUniteLegale']
            ?? null;

        $nom = $uniteLegale['nomUniteLegale']
            ?? null;

        $prenom = $uniteLegale['prenom1UniteLegale']
            ?? null;

        $formeJuridique = $uniteLegale['categorieJuridiqueUniteLegale']
            ?? null;

        $nomUniteLegale = $denomination;

        if (! $nomUniteLegale && ($nom || $prenom)) {

            $nomUniteLegale = trim(
                implode(
                    ' ',
                    array_filter([
                        $nom,
                        $prenom,
                    ])
                )
            );
        }

        /*
        |--------------------------------------------------------------------------
        | ADRESSE
        |--------------------------------------------------------------------------
        */

        $libelleVoie = $adresse['libelleVoieEtablissement']
            ?? null;

        $numeroVoie = $adresse['numeroVoieEtablissement']
            ?? null;

        $typeVoie = $adresse['typeVoieEtablissement']
            ?? null;

        $codePostal = $adresse['codePostalEtablissement']
            ?? null;

        $commune = $adresse['libelleCommuneEtablissement']
            ?? null;

        $adresseComplete = trim(
            implode(
                ' ',
                array_filter([
                    $numeroVoie,
                    $typeVoie,
                    $libelleVoie,
                ])
            )
        );

        /*
        |--------------------------------------------------------------------------
        | STRUCTURE WENDEE
        |--------------------------------------------------------------------------
        */

        return [
            'siret' => $siret,
            'siren' => $siren,

            'raison_sociale' => $nomUniteLegale,

            'forme_juridique' => $formeJuridique,

            'adresse' => $adresseComplete ?: null,
            'code_postal' => $codePostal,
            'ville' => $commune,

            'code_ape' => $codeApe,
            'libelle_ape' => null,
            'nomenclature_ape' => $nomenclatureApe,

            'code_ape_2025' => $codeApe2025,
            'nomenclature_ape_2025' => $nomenclatureApe2025,

            'activite_principale' => $codeApe,

            'etat_administratif' => $etatAdministratif,

            'date_creation' => $dateCreation,

            'enseigne' => $enseigne,

            'nom_unite_legale' => $nomUniteLegale,

            'donnees_sirene' => $data,
        ];
    }

}
