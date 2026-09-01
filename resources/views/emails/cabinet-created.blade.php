<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau cabinet créé</title>
</head>

<body style="margin:0; padding:0; background:#f5f7f6; font-family:Arial, Helvetica, sans-serif; color:#24312e;">

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f5f7f6; padding:40px 20px;">
    <tr>
        <td align="center">

            <table width="600" cellpadding="0" cellspacing="0" border="0"
                   style="max-width:600px; width:100%; background:#ffffff; border-radius:12px; overflow:hidden;">

                <tr>
                    <td align="center" style="padding:35px 30px 20px;">
                        <img
                            src="https://{{ $domain }}/images/logo-wendee.png"
                            alt="Wendee"
                            style="max-width:180px; height:auto; border:0;"
                        >
                    </td>
                </tr>

                <tr>
                    <td style="padding:20px 45px 40px;">

                        <h1 style="margin:0 0 20px; font-size:26px; color:#24312e;">
                            Nouveau cabinet créé
                        </h1>

                        <p style="font-size:16px; line-height:1.7;">
                            Un nouveau cabinet vient d’être créé sur Wendee.
                        </p>

                        <table width="100%" cellpadding="0" cellspacing="0" border="0"
                               style="margin:25px 0; border-collapse:collapse;">

                            <tr>
                                <td style="padding:10px 0; border-bottom:1px solid #e5eae8;">
                                    <strong>Cabinet</strong>
                                </td>
                                <td style="padding:10px 0; border-bottom:1px solid #e5eae8;">
                                    {{ $cabinetName }}
                                </td>
                            </tr>

                            @if($raisonSociale)
                            <tr>
                                <td style="padding:10px 0; border-bottom:1px solid #e5eae8;">
                                    <strong>Raison sociale</strong>
                                </td>
                                <td style="padding:10px 0; border-bottom:1px solid #e5eae8;">
                                    {{ $raisonSociale }}
                                </td>
                            </tr>
                            @endif

                            @if($siren)
                            <tr>
                                <td style="padding:10px 0; border-bottom:1px solid #e5eae8;">
                                    <strong>SIREN</strong>
                                </td>
                                <td style="padding:10px 0; border-bottom:1px solid #e5eae8;">
                                    {{ $siren }}
                                </td>
                            </tr>
                            @endif

                            <tr>
                                <td style="padding:10px 0; border-bottom:1px solid #e5eae8;">
                                    <strong>SIRET</strong>
                                </td>
                                <td style="padding:10px 0; border-bottom:1px solid #e5eae8;">
                                    {{ $siret }}
                                </td>
                            </tr>

                            @if($codeApe)
                            <tr>
                                <td style="padding:10px 0; border-bottom:1px solid #e5eae8;">
                                    <strong>Code APE</strong>
                                </td>
                                <td style="padding:10px 0; border-bottom:1px solid #e5eae8;">
                                    {{ $codeApe }}
                                </td>
                            </tr>
                            @endif

                            <tr>
                                <td style="padding:10px 0; border-bottom:1px solid #e5eae8;">
                                    <strong>Courtier</strong>
                                </td>
                                <td style="padding:10px 0; border-bottom:1px solid #e5eae8;">
                                    {{ $courtierName }}
                                </td>
                            </tr>

                            <tr>
                                <td style="padding:10px 0; border-bottom:1px solid #e5eae8;">
                                    <strong>Email</strong>
                                </td>
                                <td style="padding:10px 0; border-bottom:1px solid #e5eae8;">
                                    {{ $courtierEmail }}
                                </td>
                            </tr>

                        </table>

                        <p style="font-size:15px; line-height:1.7;">
                            <strong>Espace professionnel :</strong><br>
                            <a
                                href="https://{{ $domain }}"
                                style="color:#80A29A; text-decoration:none;"
                            >
                                https://{{ $domain }}
                            </a>
                        </p>

                        <p style="font-size:15px; line-height:1.7; margin-top:25px;">
                            Le compte d’activation a été envoyé au courtier.
                        </p>

                        <p style="font-size:15px; line-height:1.7; margin-top:30px;">
                            L’équipe Wendee
                        </p>

                    </td>
                </tr>

                <tr>
                    <td align="center" style="padding:25px 30px; background:#f5f7f6;">

                        <p style="font-size:12px; line-height:1.6; color:#7a8581; margin:0;">
                            © {{ date('Y') }} Wendee - Tous droits réservés.
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
