<?php

namespace App\Services\Conformite;

use App\Models\Client;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Vérification automatisée PPE / sanctions internationales via l'API de
 * matching OpenSanctions (dataset "default" = sanctions + PEP + listes de
 * crime financier). Vient compléter le déclaratif du KYC
 * (ClientKyc::est_ppe / KycMorale::ppe_reponses), qui n'était jusqu'ici
 * jamais confronté à une source officielle.
 *
 * Une erreur d'appel API ne doit jamais bloquer le parcours conseiller :
 * le statut passe à "erreur" et la revue de conformité reste modifiable
 * manuellement, exactement comme pour les analyses OpenAI existantes.
 */
class SanctionsScreeningService
{
    public function screener(Client $client): array
    {
        $apiKey = config('services.opensanctions.key');

        if (! $apiKey) {
            return [
                'statut' => 'erreur',
                'resultats' => [],
                'erreur' => "Clé API OpenSanctions non configurée (variable d'environnement OPENSANCTIONS_API_KEY).",
            ];
        }

        $identites = $this->identitesAVerifier($client);

        if ($identites->isEmpty()) {
            return [
                'statut' => 'aucune_identite',
                'resultats' => [],
            ];
        }

        $queries = [];
        foreach ($identites as $cle => $identite) {
            $properties = ['name' => [$identite['nom_complet']]];

            if (! empty($identite['date_naissance'])) {
                $properties['birthDate'] = [$identite['date_naissance']];
            }

            $queries[$cle] = [
                'schema' => 'Person',
                'properties' => $properties,
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'ApiKey '.$apiKey,
            ])
                ->timeout(15)
                ->baseUrl(config('services.opensanctions.base_url', 'https://api.opensanctions.org'))
                ->post('/match/default', ['queries' => $queries]);

            if (! $response->successful()) {
                throw new \RuntimeException('Réponse OpenSanctions non exploitable : HTTP '.$response->status());
            }

            $reponses = $response->json('responses', []);

            $resultats = [];
            $correspondanceTrouvee = false;

            foreach ($identites as $cle => $identite) {
                $reponse = $reponses[$cle] ?? null;

                $matches = collect($reponse['results'] ?? [])
                    ->filter(fn ($r) => ($r['match'] ?? false) === true)
                    ->map(fn ($r) => [
                        'nom' => $r['caption'] ?? null,
                        'score' => $r['score'] ?? null,
                        'topics' => $r['properties']['topics'] ?? [],
                        'datasets' => $r['datasets'] ?? [],
                    ])
                    ->values()
                    ->all();

                if (! empty($matches)) {
                    $correspondanceTrouvee = true;
                }

                $resultats[] = [
                    'identite' => $identite['nom_complet'],
                    'role' => $identite['role'],
                    'matches' => $matches,
                ];
            }

            return [
                'statut' => $correspondanceTrouvee ? 'correspondance_potentielle' : 'aucune_correspondance',
                'resultats' => $resultats,
            ];
        } catch (Throwable $e) {
            Log::error('Erreur screening OpenSanctions', [
                'client_id' => $client->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'statut' => 'erreur',
                'resultats' => [],
                'erreur' => $e->getMessage(),
            ];
        }
    }

    /**
     * Identités à vérifier : pour une personne physique, le client
     * lui-même et son conjoint s'il est déclaré ; pour une personne
     * morale, tous les intervenants (dirigeants, actionnaires,
     * bénéficiaires effectifs) saisis dans le KYC société.
     */
    private function identitesAVerifier(Client $client): Collection
    {
        $identites = collect();

        if ($client->estMorale()) {
            foreach ($client->intervenants as $intervenant) {
                if (empty($intervenant->nom)) {
                    continue;
                }

                $identites->push([
                    'nom_complet' => $intervenant->nom,
                    'date_naissance' => null,
                    'role' => match ($intervenant->type_intervenant) {
                        'dirigeant' => 'Dirigeant',
                        'actionnaire' => 'Actionnaire',
                        'beneficiaire_effectif' => 'Bénéficiaire effectif',
                        default => $intervenant->type_intervenant,
                    },
                ]);
            }
        } else {
            $identites->push([
                'nom_complet' => trim($client->prenom.' '.$client->nom),
                'date_naissance' => optional($client->date_naissance)->format('Y-m-d'),
                'role' => 'Client',
            ]);

            $kyc = $client->kyc;

            if ($kyc && $kyc->a_conjoint && ! empty($kyc->conjoint_nom)) {
                $identites->push([
                    'nom_complet' => trim(($kyc->conjoint_prenom ?? '').' '.$kyc->conjoint_nom),
                    'date_naissance' => optional($kyc->conjoint_date_naissance)->format('Y-m-d'),
                    'role' => 'Conjoint',
                ]);
            }
        }

        // Clés stables (sans espaces/accents) pour retrouver chaque réponse
        // dans le batch renvoyé par OpenSanctions.
        return $identites->values()->mapWithKeys(fn ($identite, $i) => ["id_{$i}" => $identite]);
    }
}
