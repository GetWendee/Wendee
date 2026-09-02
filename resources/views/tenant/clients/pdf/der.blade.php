<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
@font-face { font-family: 'Montserrat'; font-weight: 400; font-style: normal; src: url('{{ $fontRegular }}'); }
@font-face { font-family: 'Montserrat'; font-weight: 500; font-style: normal; src: url('{{ $fontMedium }}'); }
@font-face { font-family: 'Montserrat'; font-weight: 600; font-style: normal; src: url('{{ $fontSemiBold }}'); }
@font-face { font-family: 'Montserrat'; font-weight: 700; font-style: normal; src: url('{{ $fontBold }}'); }
@page { margin: 20mm 20mm 34mm 20mm; }
body { font-family: 'Montserrat', sans-serif; font-size: 10pt; color: #242424; line-height: 1.55; margin: 0; }
.page { padding: 0; }
.accent-bar { width: 54px; height: 4px; background: #242424; border-radius: 2px; margin-bottom: 18px; }
.letterhead { margin-top: 20px; margin-bottom: 26px; }
.letterhead .nom-cabinet { font-weight: 700; font-size: 14pt; color: #171514; }
.letterhead .slogan { font-style: italic; color: #817a75; font-size: 9.5pt; margin-top: 2px; }
.letterhead .adresse-cabinet { font-size: 9.5pt; color: #817a75; margin-top: 4px; }
.letterhead-table { width: 100%; border-collapse: collapse; }
.letterhead-table td { vertical-align: top; }
.letterhead-logo-cell { width: 90px; text-align: right; }
.letterhead-logo-cell img { max-width: 85px; max-height: 50px; }
.divider { border-top: 1px solid #ded9d4; margin: 20px 0; }
.lieu-date { text-align: right; font-size: 9.5pt; color: #817a75; margin-bottom: 24px; }
.client-block { margin-bottom: 26px; font-size: 10pt; }
.client-block .nom-client { font-weight: 700; color: #171514; }
.titre { text-align: center; margin: 26px 0 30px; }
.titre .eyebrow { font-size: 9pt; color: #242424; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; }
.titre h1 { font-size: 17pt; font-weight: 700; margin: 6px 0 0; color: #171514; }
.section-title { font-size: 12pt; font-weight: 700; color: #171514; margin: 26px 0 10px; page-break-after: avoid; text-transform: uppercase; }
.recueil-sous-titre { font-size: 10.5pt; font-weight: 700; color: #171514; margin: 16px 0 6px; }
.corps-paragraphe { margin: 0 0 8px; text-align: left; }
.corps-liste { margin: 0 0 8px; padding-left: 18px; }
.corps-liste li { margin-bottom: 4px; }
.recueil-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
.recueil-table td { padding: 6px 8px; font-size: 9.5pt; border-bottom: 1px solid #ded9d4; vertical-align: top; }
.recueil-table td.recueil-label { width: 42%; color: #817a75; }
.signature-block { margin-top: 34px; width: 100%; }
.signature-block td { vertical-align: top; font-size: 10pt; }
.signature-block strong { color: #171514; }
.mention { font-size: 8.5pt; color: #817a75; font-style: italic; }
.pdf-footer { position: fixed; bottom: -26mm; left: 0; right: 0; border-top: 1px solid #ded9d4; padding-top: 8px; font-size: 7.5pt; color: #817a75; }
.pdf-footer table { width: 100%; border-collapse: collapse; }
.pdf-footer td { vertical-align: middle; }
.pdf-footer .footer-logo-cell { width: 70px; }
.pdf-footer .footer-logo-cell img { max-height: 26px; max-width: 65px; }
.pdf-footer .footer-text-cell { text-align: right; line-height: 1.5; }
</style>
</head>
<body>
<div class="accent-bar"></div>
<div class="page">
    <div class="letterhead">
        <table class="letterhead-table">
            <tr>
                <td>
                    <div class="nom-cabinet">{{ $cabinet->raison_sociale ?: $cabinet->nom_commercial }}</div>
                    @if($cabinet->slogan)
                        <div class="slogan">{{ $cabinet->slogan }}</div>
                    @endif
                    <div class="adresse-cabinet">{{ $cabinet->adresse }} {{ $cabinet->code_postal }} {{ $cabinet->ville }}</div>
                </td>
                <td class="letterhead-logo-cell">
                    @if($logoPath)
                    <img src="{{ $logoPath }}" alt="Logo">
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <div class="divider"></div>

    <div class="lieu-date">
        À {{ $lieuSignature }}, le {{ $dateGeneration }}
    </div>

    <div class="client-block">
        <div class="nom-client">{{ $nomClient }}</div>
        <div>{{ $client->adresse }}</div>
        <div>{{ $client->code_postal }} {{ $client->ville }}</div>
    </div>

    <div class="titre">
        <div class="eyebrow">Entrée en relation</div>
        <h1>Document d'entrée en relation</h1>
    </div>

    <div class="section-title">Objectif du document</div>
    <p class="corps-paragraphe">Le présent document d'entrée en relation a pour objet de formaliser les bases de la relation entre le professionnel et son client, en définissant le cadre dans lequel les prestations seront réalisées.</p>
    <p class="corps-paragraphe">Il permet notamment de présenter l'identité du professionnel, son statut, ses obligations réglementaires ainsi que la nature des services proposés. Ce document vise à assurer une information claire, transparente et complète, préalable à toute intervention.</p>
    <p class="corps-paragraphe">À l'instar d'un mandat, il précise les conditions dans lesquelles le professionnel pourra être amené à intervenir, sans pour autant constituer, à ce stade, un engagement contractuel sur des opérations spécifiques. Toute mission particulière fera, le cas échéant, l'objet d'un accord distinct.</p>
    <p class="corps-paragraphe">Ce document rappelle également que la qualité des conseils fournis dépend directement de l'exactitude et de l'exhaustivité des informations communiquées par le client. Il constitue ainsi un préalable indispensable à la délivrance d'un accompagnement adapté et conforme aux exigences réglementaires.</p>
    <p class="corps-paragraphe">Enfin, il permet au client de prendre connaissance de ses droits, notamment en matière de protection des données personnelles, ainsi que des modalités de traitement de ses informations.</p>

    <div class="section-title">Présentation du cabinet</div>
    <p class="corps-paragraphe">Bienvenue chez {{ $cabinet->raison_sociale ?: $cabinet->nom_commercial }}, cabinet indépendant spécialisé en gestion de patrimoine.</p>
    <p class="corps-paragraphe">{{ $cabinet->raison_sociale ?: $cabinet->nom_commercial }} accompagne les particuliers et les professionnels dans l'organisation, la valorisation et la transmission de leur patrimoine.</p>
    <p class="corps-paragraphe">Notre approche repose sur une analyse globale de votre situation patrimoniale afin de vous proposer des stratégies adaptées à vos objectifs personnels, familiaux et professionnels.</p>

    <div class="recueil-sous-titre">Notre approche</div>
    <p class="corps-paragraphe">{{ $cabinet->raison_sociale ?: $cabinet->nom_commercial }} privilégie une relation fondée sur :</p>
    <ul class="corps-liste">
        <li>l'écoute et la compréhension de votre situation</li>
        <li>l'indépendance du conseil</li>
        <li>la transparence dans les recommandations</li>
    </ul>
    <p class="corps-paragraphe">Cette approche permet de construire des solutions patrimoniales cohérentes intégrant les dimensions civiles, juridiques, financières, immobilières et fiscales.</p>

    <div class="recueil-sous-titre">Une relation dans la durée</div>
    <p class="corps-paragraphe">Notre accompagnement s'inscrit dans une logique de suivi dans le temps.</p>
    <p class="corps-paragraphe">Selon vos besoins, nous pouvons intervenir dans l'analyse de votre situation patrimoniale, la mise en œuvre de solutions d'investissement, ainsi que dans le suivi et l'adaptation de votre stratégie patrimoniale.</p>

    <div class="section-title">Statuts réglementaires et immatriculations</div>
    <p class="corps-paragraphe">{{ $cabinet->raison_sociale ?: $cabinet->nom_commercial }} est un cabinet indépendant spécialisé en conseil en gestion de patrimoine.</p>
    <p class="corps-paragraphe">Dans le cadre de son activité, le cabinet exerce plusieurs activités réglementées dans les domaines financier, bancaire, assurantiel et immobilier.</p>
    <p class="corps-paragraphe">Le cabinet est immatriculé au registre unique des intermédiaires en assurance, banque et finance (ORIAS).</p>
    <table class="recueil-table">
        <tr><td class="recueil-label">Numéro ORIAS</td><td>{{ $cabinet->numero_orias }} — consultable sur www.orias.fr</td></tr>
    </table>

    @if($statutContientCif)
    <div class="recueil-sous-titre">Conseiller en investissements financiers (CIF)</div>
    <p class="corps-paragraphe">{{ $cabinet->raison_sociale ?: $cabinet->nom_commercial }} exerce l'activité de Conseiller en Investissements Financiers (CIF) au sens des articles L.541-1 et suivants du Code monétaire et financier.</p>
    <p class="corps-paragraphe">Le cabinet est adhérent à une association professionnelle agréée par l'Autorité des Marchés Financiers (ANACOFI). L'activité de CIF est placée sous le contrôle de l'Autorité des Marchés Financiers (AMF), 17 place de la Bourse, 75002 Paris — www.amf-france.org</p>
    @endif

    @if($statutContientAssurance)
    <div class="recueil-sous-titre">Courtier en assurance ou en réassurance</div>
    <p class="corps-paragraphe">{{ $cabinet->raison_sociale ?: $cabinet->nom_commercial }} exerce l'activité de courtier en assurance ou en réassurance, conformément aux articles L.511-1 et suivants du Code des assurances. Cette activité est placée sous le contrôle de l'Autorité de Contrôle Prudentiel et de Résolution (ACPR), 4 place de Budapest, 75436 Paris Cedex 09 — acpr.banque-france.fr</p>
    @endif

    @if($statutContientIobsp)
    <div class="recueil-sous-titre">Courtier en opérations de banque et services de paiement (IOBSP)</div>
    <p class="corps-paragraphe">{{ $cabinet->raison_sociale ?: $cabinet->nom_commercial }} exerce l'activité de courtier en opérations de banque et services de paiement, conformément aux articles L.519-1 et suivants du Code monétaire et financier. Cette activité est également placée sous le contrôle de l'ACPR.</p>
    @endif

    @if($statutContientImmobilier)
    <div class="recueil-sous-titre">Agent immobilier</div>
    <p class="corps-paragraphe">{{ $cabinet->raison_sociale ?: $cabinet->nom_commercial }} exerce l'activité de transaction sur immeubles et fonds de commerce, conformément à la loi n°70-9 du 2 janvier 1970 dite loi Hoguet. Le cabinet est titulaire d'une carte professionnelle délivrée par la Chambre de Commerce et d'Industrie.</p>
    <table class="recueil-table">
        <tr><td class="recueil-label">Numéro de carte professionnelle</td><td>{{ $cabinet->immatriculation_cci }}</td></tr>
    </table>
    @endif

    <div class="recueil-sous-titre">Assurance de responsabilité civile professionnelle</div>
    <p class="corps-paragraphe">Conformément aux obligations légales applicables aux professionnels exerçant des activités réglementées dans les domaines financier, bancaire, assurantiel et immobilier, {{ $cabinet->raison_sociale ?: $cabinet->nom_commercial }} dispose d'une assurance de responsabilité civile professionnelle couvrant les conséquences pécuniaires de sa responsabilité professionnelle.</p>
    <table class="recueil-table">
        <tr><td class="recueil-label">Assureur</td><td>{{ $cabinet->assurance_compagnie }}</td></tr>
        <tr><td class="recueil-label">Adresse</td><td>{{ $cabinet->assurance_adresse }}, {{ $cabinet->assurance_code_postal }} {{ $cabinet->assurance_ville }}</td></tr>
        <tr><td class="recueil-label">Contrat</td><td>Responsabilité Civile Professionnelle « Professions réglementées »</td></tr>
        <tr><td class="recueil-label">Référence de police</td><td>{{ $cabinet->assurance_police }}</td></tr>
        <tr><td class="recueil-label">Date d'effet du contrat</td><td>{{ $cabinet->assurance_date_debut?->translatedFormat('d F Y') }}</td></tr>
        <tr><td class="recueil-label">Activités couvertes</td><td>{{ $statutOrias }}</td></tr>
    </table>

    <div class="recueil-sous-titre">Plafonds principaux de garantie</div>
    <table class="recueil-table">
        @if($cabinet->plafond_garanties_sinistre_ias || $cabinet->plafond_garanties_annee_ias)
        <tr><td class="recueil-label">Intermédiaire en assurance</td><td>{{ $cabinet->plafond_garanties_sinistre_ias }} € par sinistre — {{ $cabinet->plafond_garanties_annee_ias }} € par année d'assurance</td></tr>
        @endif
        @if($cabinet->plafond_garanties_sinistre_iobsp || $cabinet->plafond_garanties_annee_iobsp)
        <tr><td class="recueil-label">Intermédiaire en opérations de banque</td><td>{{ $cabinet->plafond_garanties_sinistre_iobsp }} € par sinistre — {{ $cabinet->plafond_garanties_annee_iobsp }} € par année d'assurance</td></tr>
        @endif
        @if($cabinet->plafond_garanties_sinistre_cif)
        <tr><td class="recueil-label">Conseiller en investissements financiers</td><td>{{ $cabinet->plafond_garanties_sinistre_cif }} € par sinistre (structure de petite taille)</td></tr>
        @endif
        @if($cabinet->responsabilite_civile_exploitation_sinistre)
        <tr><td class="recueil-label">Responsabilité civile exploitation</td><td>{{ $cabinet->responsabilite_civile_exploitation_sinistre }} € par sinistre</td></tr>
        @endif
        <tr><td class="recueil-label">Territorialité</td><td>{{ $zoneCouverture }}</td></tr>
    </table>

    <div class="section-title">Nature des activités exercées</div>
    <p class="corps-paragraphe">Dans le cadre de son activité de conseil en gestion de patrimoine, le cabinet {{ $cabinet->raison_sociale ?: $cabinet->nom_commercial }} propose un accompagnement global visant à analyser, structurer et optimiser la situation patrimoniale de ses clients.</p>
    <p class="corps-paragraphe">L'intervention du cabinet peut porter sur différentes dimensions patrimoniales, notamment civiles, financières, fiscales, juridiques et immobilières. Selon les besoins identifiés et dans le respect des réglementations applicables, le cabinet peut exercer les activités suivantes.</p>

    @if($statutContientCif)
    <div class="recueil-sous-titre">Conseil en investissements financiers</div>
    <p class="corps-paragraphe">Dans le cadre de son statut de Conseiller en Investissements Financiers (CIF), le cabinet peut fournir des recommandations personnalisées portant sur des instruments financiers, fondées sur l'analyse de la situation financière du client, de ses objectifs patrimoniaux, de son horizon d'investissement et de sa tolérance au risque.</p>
    @endif

    @if($statutContientAssurance)
    <div class="recueil-sous-titre">Conseil et intermédiation en assurance</div>
    <p class="corps-paragraphe">Dans le cadre de son activité de courtier en assurance, le cabinet peut proposer et accompagner la mise en place de solutions d'assurance adaptées à la situation du client, notamment en matière de :</p>
    <ul class="corps-liste">
        <li>assurance-vie</li>
        <li>capitalisation</li>
        <li>prévoyance</li>
        <li>assurance emprunteur</li>
        <li>assurance IARD</li>
    </ul>
    <p class="corps-paragraphe">Le cabinet intervient en qualité d'intermédiaire entre le client et les organismes assureurs.</p>
    @endif

    @if($statutContientIobsp)
    <div class="recueil-sous-titre">Intermédiation en opérations de banque et services de paiement</div>
    <p class="corps-paragraphe">Dans le cadre de son activité de courtier en opérations de banque et services de paiement, le cabinet peut accompagner ses clients dans la recherche et la mise en place de solutions de financement, notamment :</p>
    <ul class="corps-liste">
        <li>le financement immobilier</li>
        <li>le financement patrimonial</li>
        <li>la restructuration ou l'optimisation de financements existants</li>
    </ul>
    @endif

    @if($statutContientImmobilier)
    <div class="recueil-sous-titre">Transaction immobilière</div>
    <p class="corps-paragraphe">Dans le cadre de son statut d'agent immobilier, le cabinet peut intervenir dans des opérations de transaction portant sur des biens immobiliers, notamment :</p>
    <ul class="corps-liste">
        <li>l'accompagnement dans l'acquisition d'actifs immobiliers</li>
        <li>l'assistance dans des opérations d'investissement immobilier</li>
    </ul>
    @endif

    <div class="recueil-sous-titre">Conseil patrimonial global</div>
    <p class="corps-paragraphe">Au-delà de ces activités réglementées, le cabinet propose une approche globale consistant à analyser l'ensemble de la situation patrimoniale du client afin de formuler des recommandations stratégiques adaptées à ses objectifs, notamment :</p>
    <ul class="corps-liste">
        <li>l'organisation et la structuration du patrimoine</li>
        <li>l'optimisation fiscale</li>
        <li>la préparation de la retraite</li>
        <li>la transmission du patrimoine</li>
        <li>la diversification des investissements</li>
    </ul>

    <div class="section-title">Mode de rémunération</div>
    <p class="corps-paragraphe">Dans le cadre de ses activités de conseil et d'intermédiation, le cabinet {{ $cabinet->raison_sociale ?: $cabinet->nom_commercial }} peut percevoir une rémunération sous différentes formes, selon la nature des prestations réalisées.</p>

    <div class="recueil-sous-titre">Honoraires de conseil</div>
    <p class="corps-paragraphe">Le cabinet peut percevoir des honoraires de conseil en contrepartie des prestations d'analyse patrimoniale, d'élaboration de stratégies patrimoniales ou d'accompagnement dans la mise en œuvre de solutions adaptées à la situation du client. Ces honoraires sont définis préalablement à l'intervention et font l'objet d'une lettre de mission ou d'une convention spécifique.</p>

    <div class="recueil-sous-titre">Rémunérations versées par les partenaires</div>
    <p class="corps-paragraphe">Dans le cadre de certaines opérations, notamment en matière d'assurance, d'instruments financiers ou de financement, le cabinet peut percevoir des rémunérations versées par les établissements partenaires auprès desquels les opérations sont réalisées, notamment sous forme de commissions de distribution, d'apport d'affaires ou de suivi. Ces commissions sont généralement intégrées dans les frais propres aux produits ou services proposés par les établissements partenaires.</p>

    <div class="recueil-sous-titre">Coexistence des modes de rémunération</div>
    <p class="corps-paragraphe">Selon la nature des prestations réalisées, la rémunération du cabinet peut être constituée d'honoraires versés directement par le client et/ou de commissions versées par les partenaires financiers ou assureurs. Ces différentes formes de rémunération peuvent coexister lorsque la mission confiée comprend à la fois une prestation de conseil et la mise en œuvre de solutions d'investissement, d'assurance, de financement ou d'opérations immobilières.</p>

    <div class="recueil-sous-titre">Transparence de la rémunération</div>
    <p class="corps-paragraphe">{{ $cabinet->raison_sociale ?: $cabinet->nom_commercial }} s'engage à informer ses clients de manière transparente sur les modalités de rémunération applicables aux prestations fournies. Lorsque la réglementation l'exige, les informations relatives aux frais, honoraires et commissions sont communiquées préalablement à la réalisation de toute opération ou prestation de conseil.</p>

    <div class="section-title">Sélection des partenaires et étendue du marché analysé</div>
    <p class="corps-paragraphe">Dans le cadre de ses activités de conseil et d'intermédiation, {{ $cabinet->raison_sociale ?: $cabinet->nom_commercial }} s'appuie sur un ensemble d'établissements partenaires sélectionnés pour la qualité de leurs produits, leur solidité financière et la pertinence de leurs solutions au regard des besoins des clients.</p>
    <p class="corps-paragraphe">Cette sélection repose notamment sur la qualité des supports d'investissement proposés, la solidité et la réputation des établissements partenaires, la transparence des frais et des conditions contractuelles, et la pertinence des solutions au regard des objectifs patrimoniaux du client.</p>
    <p class="corps-paragraphe">Le cabinet n'effectue pas nécessairement une analyse exhaustive de l'ensemble des solutions existantes sur le marché, mais s'appuie sur une sélection d'établissements et de solutions considérées comme pertinentes au regard des objectifs poursuivis. Le cabinet veille à agir dans l'intérêt du client et à formuler des recommandations adaptées à sa situation et à ses objectifs patrimoniaux.</p>

    <div class="section-title">Traitement des réclamations</div>
    <p class="corps-paragraphe">Le cabinet s'engage à accuser réception de la réclamation dans un délai maximum de 10 jours ouvrables, sauf si la réponse est apportée dans ce délai, et à apporter une réponse au client dans un délai maximum de 2 mois à compter de la réception de la réclamation.</p>

    <div class="recueil-sous-titre">Recours à la médiation</div>
    <p class="corps-paragraphe">Si la réponse apportée par le cabinet ne satisfait pas le client ou en l'absence de réponse dans les délais indiqués, le client peut saisir gratuitement le médiateur compétent selon la nature de la prestation concernée :</p>
    <ul class="corps-liste">
        <li>Conseil en investissements financiers : Le Médiateur de l'Autorité des Marchés Financiers, 17 place de la Bourse, 75082 Paris Cedex 02 — www.amf-france.org</li>
        <li>Assurance : La Médiation de l'Assurance, TSA 50110, 75441 Paris Cedex 09 — www.mediation-assurance.org</li>
        <li>Crédit (IOBSP) : Le Médiateur de l'Autorité de Contrôle Prudentiel et de Résolution (ACPR), TSA 50120, 75436 Paris Cedex 09</li>
    </ul>

    <div class="section-title">Protection des données</div>
    <p class="corps-paragraphe">Dans le cadre de ses activités, {{ $cabinet->raison_sociale ?: $cabinet->nom_commercial }} est amené à collecter et traiter des données à caractère personnel concernant ses clients, nécessaires à l'analyse de la situation patrimoniale du client, à la fourniture des prestations de conseil et d'intermédiation, ainsi qu'au respect des obligations légales et réglementaires applicables.</p>
    <p class="corps-paragraphe">Conformément au Règlement Général sur la Protection des Données (RGPD) et à la loi Informatique et Libertés, le client dispose d'un droit d'accès, de rectification, d'effacement, de limitation, d'opposition et de portabilité de ses données.</p>
    <p class="corps-paragraphe">Ces droits peuvent être exercés en adressant une demande au cabinet {{ $cabinet->raison_sociale ?: $cabinet->nom_commercial }} :</p>
    <table class="recueil-table">
        <tr><td class="recueil-label">Par courrier</td><td>{{ $cabinet->raison_sociale ?: $cabinet->nom_commercial }}, {{ $cabinet->adresse }}, {{ $cabinet->code_postal }} {{ $cabinet->ville }}</td></tr>
        <tr><td class="recueil-label">Par email</td><td>{{ $cabinet->email }}</td></tr>
    </table>

    <div style="page-break-inside: avoid;">
    <div class="section-title">Attestation de remise du document d'entrée en relation</div>
    <p class="corps-paragraphe">Le présent document d'entrée en relation a été remis au client préalablement à toute prestation de conseil ou d'intermédiation réalisée par le cabinet {{ $cabinet->raison_sociale ?: $cabinet->nom_commercial }}, l'informant sur l'identité du cabinet, ses statuts réglementaires, la nature des activités exercées, les modalités de rémunération, les procédures applicables en matière de réclamation et les dispositions relatives à la protection des données personnelles.</p>
    <p class="corps-paragraphe">Le client reconnaît avoir pris connaissance des informations contenues dans ce document.</p>

    <div class="recueil-sous-titre">Attestation du client</div>
    <table class="recueil-table">
        <tr><td class="recueil-label">Nom et Prénom</td><td>{{ $nomClient }}</td></tr>
        <tr><td class="recueil-label">Adresse</td><td>{{ $client->adresse }}, {{ $client->code_postal }} {{ $client->ville }}</td></tr>
    </table>
    <p class="corps-paragraphe">déclare avoir reçu le document d'entrée en relation du cabinet {{ $cabinet->raison_sociale ?: $cabinet->nom_commercial }} préalablement à toute prestation de conseil.</p>
    </div>

    <table class="signature-block">
        <tr>
            <td width="100%">
                Fait à {{ $lieuSignature }}, le {{ $dateGeneration }}
            </td>
        </tr>
    </table>

</div>
<div class="pdf-footer">
    <table>
        <tr>
            <td class="footer-logo-cell">
                @if($logoPath)
                <img src="{{ $logoPath }}" alt="Logo">
                @endif
            </td>
            <td class="footer-text-cell">
                {{ $cabinet->nom_commercial ?: $cabinet->raison_sociale }}
                @if($cabinet->raison_sociale) | {{ $cabinet->raison_sociale }}@endif
                <br>
                @if($cabinet->numero_rcs) RCS {{ $cabinet->ville_rcs }} {{ $cabinet->numero_rcs }} @endif
                @if($cabinet->numero_tva) | TVA {{ $cabinet->numero_tva }} @endif
                @if($cabinet->numero_orias) | ORIAS n° {{ $cabinet->numero_orias }} @endif
            </td>
        </tr>
    </table>
</div>
</body>
</html>
