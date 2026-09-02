<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document d'entrée en relation</title>
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
                                src="{{ $logoUrl }}"
                                alt="{{ $cabinetName }}"
                                style="max-width:180px; max-height:70px; height:auto; border:0;"
                            >
                        </td>
                    </tr>

                    <!-- CONTENU -->
                    <tr>
                        <td style="padding:20px 45px 40px;">

                            <h1 style="margin:0 0 20px; font-size:24px; line-height:1.3; color:#24312e;">
                                Document d'entrée en relation
                            </h1>

                            <p style="font-size:16px; line-height:1.7; margin:0 0 15px;">
                                Bonjour {{ $client->prenom }},
                            </p>

                            <p style="font-size:16px; line-height:1.7; margin:0 0 15px;">
                                Dans le cadre de l'entrée en relation avec {{ $cabinetName }}, vous trouverez ci-joint notre <strong>Document d'Entrée en Relation</strong>, qui précise notamment :
                            </p>

                            <ul style="font-size:15px; line-height:1.8; margin:0 0 20px; padding-left:20px; color:#333;">
                                <li>Notre statut réglementaire et nos habilitations</li>
                                <li>La nature des services proposés</li>
                                <li>Les modalités de rémunération et conditions d'intervention</li>
                                <li>Les garanties légales et voies de réclamation</li>
                            </ul>

                            <p style="font-size:16px; line-height:1.7; margin:0 0 15px;">
                                Ce document constitue le socle contractuel et réglementaire de notre accompagnement. Sa lecture attentive est recommandée avant toute phase d'analyse ou de recommandation.
                            </p>

                            <p style="font-size:15px; line-height:1.7; margin:0 0 25px; font-style:italic; color:#66736f;">
                                Cette étape marque le début formel de notre relation professionnelle.
                            </p>

                            <p style="font-size:15px; line-height:1.7; margin:25px 0 5px;">
                                <strong>Prochaine étape</strong>
                            </p>

                            <p style="font-size:15px; line-height:1.7; margin:0 0 25px;">
                                Votre conseiller prendra contact avec vous afin d'initier le parcours de connaissance client et d'organiser les premières étapes de travail.
                            </p>

                            <p style="font-size:15px; line-height:1.7; margin:30px 0 0;">
                                L'équipe {{ $cabinetName }}
                            </p>

                        </td>
                    </tr>

                    <!-- FOOTER -->
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
