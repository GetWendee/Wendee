<p>Bonjour {{ $client->civilite ? $client->civilite . ' ' : '' }}{{ $client->prenom }} {{ $client->nom }},</p>
<p>Votre recommandation patrimoniale, établie par {{ $conseiller?->name ?? $nomCabinet }}, est disponible dans votre espace client.</p>
@if($pdfJoint)
<p>Vous en trouverez également une copie en pièce jointe.</p>
@endif
<p>Pour la valider, connectez-vous à votre espace et saisissez le code suivant :</p>
<p style="font-size:22px;font-weight:700;letter-spacing:4px;">{{ $code }}</p>
<p>N'hésitez pas à revenir vers {{ $conseiller ? 'votre conseiller' : 'nous' }} pour toute question.</p>
<p>Cordialement,<br>{{ $nomCabinet }}</p>
