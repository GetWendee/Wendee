<p>Bonjour {{ $client->civilite ? $client->civilite . ' ' : '' }}{{ $client->prenom }} {{ $client->nom }},</p>
<p>Veuillez trouver ci-joint votre recommandation patrimoniale, établie par {{ $conseiller?->name ?? $nomCabinet }}.</p>
<p>N'hésitez pas à revenir vers {{ $conseiller ? 'votre conseiller' : 'nous' }} pour toute question.</p>
<p>Cordialement,<br>{{ $nomCabinet }}</p>
