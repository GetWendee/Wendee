<?php

/*
|--------------------------------------------------------------------------
| Catalogue des prestations (page Mission)
|--------------------------------------------------------------------------
|
| Contenu repris à l'identique du CPT WordPress "prestations".
| 'icone' contient le contenu interne (paths) d'une icône Lucide
| (https://lucide.dev, licence ISC), rendu dans un <svg> commun
| par la vue (voir mission.blade.php).
| 'route' est le nom de la route Laravel associée, null si la
| fonctionnalité n'est pas encore construite (carte "Indisponible
| pour le moment").
|
*/

return [
    [
        'titre' => 'Assurance',
        'sous_titre' => 'Comparer vos assurances',
        'icone' => '<path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z" />',
        'famille' => 'etude',
        'route' => null,
    ],
    [
        'titre' => 'Banque',
        'sous_titre' => 'Optimiser son financement',
        'icone' => '<path d="M10 18v-7" /><path d="M11.119 2.205a2 2 0 0 1 1.762 0l7.84 3.846A.5.5 0 0 1 20.5 7h-17a.5.5 0 0 1-.22-.949z" /><path d="M14 18v-7" /><path d="M18 18v-7" /><path d="M3 22h18" /><path d="M6 18v-7" />',
        'famille' => 'etude',
        'route' => null,
    ],
    [
        'titre' => 'Épargne',
        'sous_titre' => 'Projeter pour réaliser',
        'icone' => '<path d="M11 17h3v2a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-3a3.16 3.16 0 0 0 2-2h1a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1h-1a5 5 0 0 0-2-4V3a4 4 0 0 0-3.2 1.6l-.3.4H11a6 6 0 0 0-6 6v1a5 5 0 0 0 2 4v3a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1z" /><path d="M16 10h.01" /><path d="M2 8v1a2 2 0 0 0 2 2h1" />',
        'famille' => 'simulation',
        'route' => null,
    ],
    [
        'titre' => 'Financement',
        'sous_titre' => 'Simuler pour réaliser',
        'icone' => '<rect width="20" height="12" x="2" y="6" rx="2" /><circle cx="12" cy="12" r="2" /><path d="M6 12h.01M18 12h.01" />',
        'famille' => 'simulation',
        'route' => null,
    ],
    [
        'titre' => 'Financière',
        'sous_titre' => 'Personnaliser votre demande',
        'icone' => '<path d="M16 7h6v6" /><path d="m22 7-8.5 8.5-5-5L2 17" />',
        'famille' => 'etude',
        'route' => 'tenant.clients.recommandation-patrimoniale',
    ],
    [
        'titre' => 'Global',
        'sous_titre' => 'Connaître pour agir',
        'icone' => '<circle cx="12" cy="12" r="10" /><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20" /><path d="M2 12h20" />',
        'famille' => 'audit',
        'route' => null,
    ],
    [
        'titre' => 'Immobilière',
        'sous_titre' => 'Piloter votre patrimoine immobilier',
        'icone' => '<path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8" /><path d="M3 10a2 2 0 0 1 .709-1.528l7-6a2 2 0 0 1 2.582 0l7 6A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />',
        'famille' => 'etude',
        'route' => null,
    ],
    [
        'titre' => 'Prévoyance',
        'sous_titre' => 'Anticiper pour protéger',
        'icone' => '<path d="M12 13v7a2 2 0 0 0 4 0" /><path d="M12 2v2" /><path d="M20.992 13a1 1 0 0 0 .97-1.274 10.284 10.284 0 0 0-19.923 0A1 1 0 0 0 3 13z" />',
        'famille' => 'audit',
        'route' => null,
    ],
    [
        'titre' => 'Rémunération',
        'sous_titre' => 'Sélectionner pour optimiser',
        'icone' => '<path d="M19 7V4a1 1 0 0 0-1-1H5a2 2 0 0 0 0 4h15a1 1 0 0 1 1 1v4h-3a2 2 0 0 0 0 4h3a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1" /><path d="M3 5v14a2 2 0 0 0 2 2h15a1 1 0 0 0 1-1v-4" />',
        'famille' => 'etude',
        'route' => null,
    ],
    [
        'titre' => 'Retraite',
        'sous_titre' => 'Préparer pour profiter',
        'icone' => '<path d="M13 8c0-2.76-2.46-5-5.5-5S2 5.24 2 8h2l1-1 1 1h4" /><path d="M13 7.14A5.82 5.82 0 0 1 16.5 6c3.04 0 5.5 2.24 5.5 5h-3l-1-1-1 1h-3" /><path d="M5.89 9.71c-2.15 2.15-2.3 5.47-.35 7.43l4.24-4.25.7-.7.71-.71 2.12-2.12c-1.95-1.96-5.27-1.8-7.42.35" /><path d="M11 15.5c.5 2.5-.17 4.5-1 6.5h4c2-5.5-.5-12-1-14" />',
        'famille' => 'audit',
        'route' => null,
    ],
    [
        'titre' => 'Statut du dirigeant',
        'sous_titre' => 'Réfléchir pour projeter',
        'icone' => '<path d="M16 2v2" /><path d="M7 21v-2a2 2 0 012-2h6a2 2 0 012 2v2" /><path d="M8 2v2" /><circle cx="12" cy="10" r="3" /><rect x="3" y="3" width="18" height="18" rx="2" />',
        'famille' => 'etude',
        'route' => null,
    ],
    [
        'titre' => 'Transmission',
        'sous_titre' => 'Estimer pour optimiser',
        'icone' => '<path d="M14.536 21.686a.5.5 0 0 0 .937-.024l6.5-19a.496.496 0 0 0-.635-.635l-19 6.5a.5.5 0 0 0-.024.937l7.93 3.18a2 2 0 0 1 1.112 1.11z" /><path d="m21.854 2.147-10.94 10.939" />',
        'famille' => 'audit',
        'route' => null,
    ],
];
