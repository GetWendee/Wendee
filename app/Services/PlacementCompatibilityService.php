<?php

namespace App\Services;

use App\Models\ProfilInvestisseur;

class PlacementCompatibilityService
{
    public function evaluate(?ProfilInvestisseur $profil): array
    {
        if (! $profil) {
            return [];
        }

        $risque = (float) ($profil->profil_risque_final ?? 0);
        $experience = (float) ($profil->score_experience_global ?? 0);
        $connaissance = (float) ($profil->score_connaissance_global ?? 0);
        $complexe = (float) ($profil->score_complexe_resultat ?? 0);
        $pertes = (float) ($profil->score_capacite_subir_pertes ?? 0);

        $blocageComplexes = (int) ($profil->blocage_produits_complexes_interdits ?? 0);
        $alerteFragile = (int) ($profil->alerte_client_fragile ?? 0);
        $alerteHorizon = (int) ($profil->alerte_horizon_incompatible ?? 0);

        return [

            'fonds_euros' => [
                'label' => 'Fonds euros',
                'detail' => 'Capital garanti par l’assureur',
                'niveau' => 'compatible',
                'motif' => 'Compatible avec un profil prudent ou conservateur.',
            ],

            'monetaire' => [
                'label' => 'Monétaire',
                'detail' => 'Faible volatilité et horizon court',
                'niveau' => 'compatible',
                'motif' => 'Compatible avec une faible tolérance au risque.',
            ],

            'obligations' => [
                'label' => 'Obligations',
                'detail' => 'Risque de taux et risque de crédit',
                'niveau' => $pertes <= 2 ? 'vigilance' : 'compatible',
                'motif' => $pertes <= 2
                    ? 'Capacité à subir des pertes très faible.'
                    : 'Niveau de risque compatible avec le profil.',
            ],

            'actions' => [
                'label' => 'Actions',
                'detail' => 'Volatilité et risque de perte en capital',
                'niveau' => ($risque <= 2 || $pertes <= 2) ? 'non_adapte' : ($risque <= 4 ? 'vigilance' : 'compatible'),
                'motif' => $risque <= 2
                    ? 'Profil de risque conservateur.'
                    : ($pertes <= 2
                        ? 'Capacité à subir des pertes insuffisante.'
                        : 'Exposition à calibrer selon le profil.'),
            ],

            'immobilier' => [
                'label' => 'Immobilier / SCPI',
                'detail' => 'Risque immobilier, liquidité et horizon',
                'niveau' => $alerteHorizon >= 1 ? 'vigilance' : 'compatible',
                'motif' => $alerteHorizon >= 1
                    ? 'Horizon d’investissement à vérifier.'
                    : 'Compatibilité à confirmer selon la liquidité recherchée.',
            ],

            'private_equity' => [
                'label' => 'Private equity',
                'detail' => 'Illiquidité, durée longue et risque élevé',
                'niveau' => ($risque <= 4 || $experience < 5) ? 'non_adapte' : 'vigilance',
                'motif' => $experience < 5
                    ? 'Expérience insuffisante pour ce type de placement.'
                    : 'Produit réservé à une poche de diversification.',
            ],

            'structures' => [
                'label' => 'Produits structurés',
                'detail' => 'Complexité et risque de perte en capital',
                'niveau' => $blocageComplexes === 2
                    ? 'non_adapte'
                    : (($complexe < 5 || $experience < 5) ? 'vigilance' : 'compatible'),
                'motif' => $blocageComplexes === 2
                    ? 'Produits complexes incompatibles avec le niveau actuel.'
                    : (($complexe < 5 || $experience < 5)
                        ? 'Connaissance ou expérience insuffisante.'
                        : 'Compatibilité sous réserve des caractéristiques du produit.'),
            ],

            'levier' => [
                'label' => 'Produits à effet de levier',
                'detail' => 'CFD, futures, options, turbos',
                'niveau' => ($blocageComplexes >= 1 || $risque < 6 || $experience < 6)
                    ? 'non_adapte'
                    : 'vigilance',
                'motif' => $blocageComplexes >= 1
                    ? 'Blocage ou vigilance sur les produits complexes.'
                    : 'Risque élevé nécessitant expérience et tolérance suffisantes.',
            ],

        ];
    }
}
