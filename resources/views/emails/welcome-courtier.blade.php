<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue chez Wendee</title>
</head>

<body style="margin:0; padding:0; background:#f5f7f6; font-family:Arial, Helvetica, sans-serif; color:#24312e;">

    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f5f7f6; padding:40px 20px;">
        <tr>
            <td align="center">

                <table width="600" cellpadding="0" cellspacing="0" border="0" style="max-width:600px; width:100%; background:#ffffff; border-radius:12px; overflow:hidden;">

                    <!-- LOGO -->
                    <tr>
                        <td align="center" style="padding:35px 30px 20px;">
                            <img
                                src="https://{{ $domain }}/images/logo-wendee.png"
                                alt="Wendee"
                                style="max-width:180px; height:auto; border:0;"
                            >
                        </td>
                    </tr>

                    <!-- CONTENU -->
                    <tr>
                        <td style="padding:20px 45px 40px;">

                            <h1 style="margin:0 0 20px; font-size:28px; line-height:1.3; color:#24312e;">
                                Bienvenue chez Wendee !
                            </h1>

                            <p style="font-size:16px; line-height:1.7; margin:0 0 15px;">
                                Bonjour {{ $user->name }},
                            </p>

                            <p style="font-size:16px; line-height:1.7; margin:0 0 15px;">
                                Votre compte courtier vient d’être créé sur Wendee.
                            </p>

                            <p style="font-size:16px; line-height:1.7; margin:0 0 25px;">
                                Vous pouvez maintenant activer votre compte et définir votre mot de passe afin d’accéder à votre espace professionnel.
                            </p>

                            <!-- BOUTON -->
                            <table cellpadding="0" cellspacing="0" border="0" style="margin:30px auto;">
                                <tr>
                                    <td align="center" style="border-radius:8px; background:#80A29A;">
                                        <a
                                            href="{{ $activationUrl }}"
                                            style="display:inline-block; padding:14px 28px; font-size:16px; font-weight:bold; color:#ffffff; text-decoration:none; border-radius:8px;"
                                        >
                                            Activer mon compte
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="font-size:15px; line-height:1.7; margin:25px 0 5px;">
                                <strong>Votre espace professionnel</strong>
                            </p>

                            <p style="font-size:15px; line-height:1.7; margin:0 0 25px;">
                                <a href="https://{{ $domain }}" style="color:#80A29A; text-decoration:none;">
                                    https://{{ $domain }}
                                </a>
                            </p>

                            <p style="font-size:14px; line-height:1.7; color:#66736f; margin:0 0 15px;">
                                Ce lien d’activation est personnel et sécurisé.
                            </p>

                            <p style="font-size:15px; line-height:1.7; margin:25px 0 5px;">
                                Si le bouton « Activer mon compte » ne fonctionne pas, copiez-collez l’adresse suivante dans votre navigateur :
                            </p>

                            <p style="font-size:13px; line-height:1.6; word-break:break-all; color:#66736f;">
                                {{ $activationUrl }}
                            </p>

                            <p style="font-size:15px; line-height:1.7; margin:30px 0 0;">
                                L’équipe Wendee
                            </p>

                        </td>
                    </tr>

                    <!-- FOOTER -->
                    <tr>
                        <td align="center" style="padding:25px 30px; background:#f5f7f6;">

                            <p style="font-size:12px; line-height:1.6; color:#7a8581; margin:0;">
                                © {{ date('Y') }} Wendee. Tous droits réservés.
                            </p>

                            <p style="font-size:12px; line-height:1.6; color:#7a8581; margin:5px 0 0;">
                                La plateforme digitale de gestion patrimoniale.
                            </p>

                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>
</html>
