<?php

/*
|--------------------------------------------------------------------------
| Catalogue des prestations (page Mission)
|--------------------------------------------------------------------------
|
| Contenu repris à l'identique du CPT WordPress "prestations".
| 'route' est le nom de la route Laravel associée, null si la
| fonctionnalité n'est pas encore construite (carte "Indisponible
| pour le moment").
|
*/

return [
    [
        'titre' => 'Assurance',
        'sous_titre' => 'Comparer vos assurances',
        'icone' => '🛡️',
        'famille' => 'etude',
        'route' => null,
    ],
    [
        'titre' => 'Banque',
        'sous_titre' => 'Optimiser son financement',
        'icone' => '🏦',
        'famille' => 'etude',
        'route' => null,
    ],
    [
        'titre' => 'Épargne',
        'sous_titre' => 'Projeter pour réaliser',
        'icone' => '🐷',
        'famille' => 'simulation',
        'route' => null,
    ],
    [
        'titre' => 'Financement',
        'sous_titre' => 'Simuler pour réaliser',
        'icone' => '💵',
        'famille' => 'simulation',
        'route' => null,
    ],
    [
        'titre' => 'Financière',
        'sous_titre' => 'Personnaliser votre demande',
        'icone' => '📈',
        'famille' => 'etude',
        'route' => 'tenant.clients.recommandation-patrimoniale',
    ],
    [
        'titre' => 'Global',
        'sous_titre' => 'Connaître pour agir',
        'icone' => '🌐',
        'famille' => 'audit',
        'route' => null,
    ],
    [
        'titre' => 'Immobilière',
        'sous_titre' => 'Piloter votre patrimoine immobilier',
        'icone' => '🏠',
        'famille' => 'etude',
        'route' => null,
    ],
    [
        'titre' => 'Prévoyance',
        'sous_titre' => 'Anticiper pour protéger',
        'icone' => '🖥️',
        'famille' => 'audit',
        'route' => null,
    ],
    [
        'titre' => 'Rémunération',
        'sous_titre' => 'Sélectionner pour optimiser',
        'icone' => '💳',
        'famille' => 'etude',
        'route' => null,
    ],
    [
        'titre' => 'Retraite',
        'sous_titre' => 'Préparer pour profiter',
        'icone' => '🏊',
        'famille' => 'audit',
        'route' => null,
    ],
    [
        'titre' => 'Statut du dirigeant',
        'sous_titre' => 'Réfléchir pour projeter',
        'icone' => '🪪',
        'famille' => 'etude',
        'route' => null,
    ],
    [
        'titre' => 'Transmission',
        'sous_titre' => 'Estimer pour optimiser',
        'icone' => '✈️',
        'famille' => 'audit',
        'route' => null,
    ],
];
