@php
    $libelles = [
        'kyc' => 'connaissance client',
        'patrimoine' => 'patrimoine',
        'profil_investisseur' => 'profil investisseur',
    ];
@endphp
<p>Bonjour {{ $client->prenom }},</p>

<p>
    Votre conseiller vient d'apporter une modification à votre dossier {{ $libelles[$module] ?? $module }}.
</p>
