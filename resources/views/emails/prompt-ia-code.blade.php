<p>Bonjour {{ $auteur->name }},</p>

<p>
    Une modification du prompt « {{ $prompt->titre }} » vient d'être enregistrée sur Wendee.
    Voici votre code de confirmation :
</p>

<p style="font-size: 24px; font-weight: bold; letter-spacing: 4px;">{{ $code }}</p>

<p>
    Saisissez ce code sur la page Configuration IA pour appliquer la modification.
    Si vous n'êtes pas à l'origine de cette demande, ne saisissez pas ce code et vérifiez les accès à votre compte Wendee.
</p>
