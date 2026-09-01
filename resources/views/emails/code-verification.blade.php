@php
    $libelles = [
        'kyc' => 'connaissance client',
        'patrimoine' => 'patrimoine',
        'profil_investisseur' => 'profil investisseur',
    ];
@endphp
<p>Bonjour {{ $client->prenom }},</p>

<p>
    Votre conseiller a complété votre dossier {{ $libelles[$module] ?? $module }}.
    Voici votre code de vérification :
</p>

<p style="font-size: 24px; font-weight: bold; letter-spacing: 4px;">{{ $code }}</p>

<p>Communiquez ce code à votre conseiller pour valider ce dossier.</p>
