<p>Bonjour {{ $client->civilite ? $client->civilite . ' ' : '' }}{{ $client->prenom }} {{ $client->nom }},</p>
<p>Nous vous confirmons la bonne validation de votre recommandation patrimoniale.</p>
<p>Vous en trouverez une copie signée en pièce jointe.</p>
<p>Cordialement,<br>{{ $nomCabinet }}</p>
