<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle connexion</title>
</head>

<body style="margin:0; padding:0; background:#f5f7f6; font-family:Arial, Helvetica, sans-serif; color:#24312e;">

    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f5f7f6; padding:40px 20px;">
        <tr>
            <td align="center">

                <table width="600" cellpadding="0" cellspacing="0" border="0" style="max-width:600px; width:100%; background:#ffffff; border-radius:12px; overflow:hidden;">

                    <tr>
                        <td style="padding:35px 45px 10px;">
                            <h1 style="margin:0 0 20px; font-size:24px; line-height:1.3; color:#24312e;">
                                Nouvelle connexion détectée
                            </h1>

                            <p style="font-size:16px; line-height:1.7; margin:0 0 15px;">
                                Une connexion à votre compte {{ $cabinetName }} vient d'être tentée depuis un appareil que nous ne reconnaissons pas :
                            </p>

                            <table cellpadding="0" cellspacing="0" border="0" style="width:100%; background:#f5f7f6; border-radius:8px; margin:0 0 25px;">
                                <tr>
                                    <td style="padding:16px 20px; font-size:14px; line-height:1.8; color:#24312e;">
                                        <strong>Appareil :</strong> {{ $appareil }}<br>
                                        <strong>Adresse IP :</strong> {{ $ip }}<br>
                                        <strong>Date :</strong> {{ $heure }}
                                    </td>
                                </tr>
                            </table>

                            <p style="font-size:16px; line-height:1.7; margin:0 0 25px;">
                                Cette connexion est bloquée en attendant votre confirmation. Est-ce bien vous ?
                            </p>

                            <table cellpadding="0" cellspacing="0" border="0" style="margin:0 auto 30px;">
                                <tr>
                                    <td style="border-radius:8px; background:#80A29A; padding:0 8px;">
                                        <a href="{{ $confirmUrl }}" style="display:inline-block; padding:14px 24px; font-size:15px; font-weight:bold; color:#ffffff; text-decoration:none;">
                                            Oui, c'est moi
                                        </a>
                                    </td>
                                    <td style="width:16px;"></td>
                                    <td style="border-radius:8px; background:#c0392b; padding:0 8px;">
                                        <a href="{{ $denyUrl }}" style="display:inline-block; padding:14px 24px; font-size:15px; font-weight:bold; color:#ffffff; text-decoration:none;">
                                            Non, bloquer
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="font-size:14px; line-height:1.7; color:#66736f; margin:0 0 25px;">
                                Si vous cliquez sur « Non, bloquer », la connexion sera refusée et vous recevrez un email pour redéfinir votre mot de passe.
                            </p>

                            <p style="font-size:13px; line-height:1.6; color:#66736f; margin:0 0 10px;">
                                Ce lien expire dans 15 minutes.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="padding:25px 30px; background:#f5f7f6;">
                            <p style="font-size:12px; line-height:1.6; color:#7a8581; margin:0;">
                                Message généré automatiquement — merci de ne pas y répondre.
                            </p>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>
</html>
