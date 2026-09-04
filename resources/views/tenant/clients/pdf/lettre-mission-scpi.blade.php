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
.conseiller-block { margin-top: 14px; font-size: 9.5pt; color: #242424; font-weight: 500; }
.divider { border-top: 1px solid #ded9d4; margin: 20px 0; }
.lieu-date { text-align: right; font-size: 9.5pt; color: #817a75; margin-bottom: 24px; }
.client-block { margin-bottom: 26px; font-size: 10pt; }
.client-block .nom-client { font-weight: 700; color: #171514; }
.titre { text-align: center; margin: 26px 0 30px; }
.titre .eyebrow { font-size: 9pt; color: #242424; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; }
.titre h1 { font-size: 17pt; font-weight: 700; margin: 6px 0 0; color: #171514; }
.parties { margin: 20px 0; font-size: 9.5pt; }
.parties strong { color: #171514; }
.section-title { font-size: 11pt; font-weight: 700; color: #171514; margin: 22px 0 8px; page-break-after: avoid; }
.corps-paragraphe { margin: 0 0 8px; text-align: left; }
.corps-liste { margin: 0 0 8px; padding-left: 18px; }
.corps-liste li { margin-bottom: 4px; }
.recueil-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
.recueil-table td { padding: 6px 8px; font-size: 9.5pt; border-bottom: 1px solid #ded9d4; vertical-align: top; }
.recueil-table td.recueil-label { width: 42%; color: #817a75; }
.recueil-sous-titre { font-size: 10pt; font-weight: 700; color: #171514; margin: 16px 0 6px; }
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
                    <div class="conseiller-block">
                        {{ $nomConseiller }}<br>
                        @if($telConseiller){{ $telConseiller }}<br>@endif
                        @if($mailConseiller){{ $mailConseiller }}@endif
                    </div>
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
        <div class="eyebrow">Lettre de mission — Conseil en investissement</div>
        <h1>Lettre de mission relative à un investissement en parts de SCPI</h1>
    </div>

    <div class="parties">
        Entre les soussignés :<br><br>
        <strong>Le Client</strong><br>
        Nom : {{ $nomClient }}<br>
        Adresse : {{ $client->adresse }}, {{ $client->code_postal }} {{ $client->ville }}<br>
        Téléphone : {{ $client->telephone_mobile }}<br>
        Adresse e-mail : {{ $client->email }}<br><br>
        Et<br><br>
        <strong>Le Conseiller en Investissements Financiers</strong><br>
        Société : {{ $cabinet->raison_sociale ?: $cabinet->nom_commercial }}<br>
        Adresse : {{ $cabinet->adresse }} {{ $cabinet->code_postal }} {{ $cabinet->ville }}<br>
        RCS : {{ $cabinet->ville_rcs }} {{ $cabinet->numero_rcs }}<br>
        @if($cabinet->numero_association)Immatriculation CIF : {{ $cabinet->numero_association }}@if(is_array($cabinet->association_professionnelle) && !empty($cabinet->association_professionnelle)) ({{ implode(', ', $cabinet->association_professionnelle) }})@endif<br>@endif
        @if($cabinet->numero_orias)ORIAS : {{ $cabinet->numero_orias }}<br>@endif
        Téléphone : {{ $telConseiller }}<br>
        Adresse e-mail : {{ $mailConseiller }}
    </div>

    <p class="corps-paragraphe">Il est convenu ce qui suit :</p>

    <div class="section-title">1. Objet de la mission</div>
    <p class="corps-paragraphe">La présente lettre de mission a pour objet de définir les conditions dans lesquelles le Conseiller fournit au Client une prestation de conseil en investissement portant sur des parts de Sociétés Civiles de Placement Immobilier (SCPI), en fonction de sa situation patrimoniale, de ses objectifs et de son horizon de placement. Cette prestation ne constitue ni une garantie de résultat, ni un engagement de souscription.</p>

    <div class="section-title">2. Statut réglementaire du Conseiller</div>
    <p class="corps-paragraphe">Le Conseiller exerce l'activité de Conseiller en Investissements Financiers (CIF)@if($cabinet->numero_association) et est immatriculé sous le numéro {{ $cabinet->numero_association }} auprès de l'association professionnelle mentionnée ci-dessus, agréée par l'Autorité des Marchés Financiers (AMF)@endif. Le Conseiller déclare n'entretenir aucun lien capitalistique avec les sociétés de gestion de SCPI de nature à compromettre son indépendance d'analyse, sauf mention contraire communiquée au Client par écrit.</p>

    <div class="section-title">3. Nature du conseil et rémunération</div>
    <p class="corps-paragraphe">Le conseil fourni est un conseil {{ $cabinet->mode_remuneration ?: 'précisé au Client préalablement à la présente mission' }}. Le Conseiller est rémunéré par des commissions versées par les sociétés de gestion au titre des parts souscrites par le Client et/ou par des honoraires facturés directement au Client, dont le détail est communiqué avant toute souscription. @if($cabinet->conflits_interets_existe === 'oui'){{ $cabinet->conflits_interets_description }}@else Le Conseiller informe le Client de l'existence de tout conflit d'intérêts identifié et des mesures prises pour le gérer.@endif</p>

    <div class="section-title">4. Obligations d'information et de mise en garde du Conseiller</div>
    <p class="corps-paragraphe">Le Conseiller s'engage à :</p>
    <ul class="corps-liste">
        <li>Recueillir les informations nécessaires sur la situation financière, les objectifs et l'expérience du Client avant toute recommandation.</li>
        <li>Attirer l'attention du Client sur les caractéristiques et les risques propres à l'investissement en parts de SCPI, notamment l'absence de garantie du capital, l'absence de garantie sur le niveau des revenus distribués, le risque de liquidité et l'horizon de placement recommandé à long terme.</li>
        <li>Remettre au Client, préalablement à la signature, un document d'entrée en relation précisant son statut, ses modalités de rémunération et les modalités de traitement des réclamations.</li>
        <li>Assurer un suivi de la recommandation formulée en fonction de l'évolution de la situation du Client portée à sa connaissance.</li>
    </ul>

    <div class="section-title">5. Obligations du Client</div>
    <p class="corps-paragraphe">Le Client s'engage à fournir des informations exactes et complètes sur sa situation patrimoniale et ses objectifs, et à informer sans délai le Conseiller de toute modification de sa situation susceptible d'affecter la pertinence de la recommandation. Le Client reconnaît avoir été informé des risques mentionnés à l'article 4 avant toute décision d'investissement.</p>

    <div class="section-title">6. Durée et résiliation de la mission</div>
    <p class="corps-paragraphe">La présente mission est conclue pour une durée d'un an à compter de sa signature, renouvelable par tacite reconduction, sauf dénonciation par l'une des parties par lettre recommandée avec accusé de réception, avec un préavis d'un mois avant la date d'échéance.</p>

    <div class="section-title">7. Protection des données personnelles</div>
    <p class="corps-paragraphe">Les données personnelles du Client sont traitées conformément au RGPD et à la loi Informatique et Libertés. Le Client dispose d'un droit d'accès, de rectification, d'opposition, de suppression et de portabilité en écrivant au Conseiller ou à la CNIL.</p>

    <div class="section-title">8. Réclamations, médiation et droit applicable</div>
    <p class="corps-paragraphe">En cas de réclamation, le Client peut s'adresser gratuitement au service réclamation du Conseiller. Si aucune solution n'est trouvée, le Client peut saisir le Médiateur de l'AMF (www.amf-france.org)@if($cabinet->mediateur_nom), ou {{ $cabinet->mediateur_nom }}@endif. En cas de litige relatif à l'exécution de la présente mission, les parties s'efforceront de trouver une solution amiable. À défaut, les tribunaux compétents seront ceux du ressort du domicile du Client. La présente lettre de mission est soumise au droit français.</p>

    <div class="section-title">Recueil des données</div>

    <div class="recueil-sous-titre">Objectifs</div>
    <table class="recueil-table">
        <tr><td class="recueil-label">Objectifs exprimés</td><td>{{ $recueil['objectifs_libelle'] ?? '-' }}</td></tr>
        @if(!empty($recueil['objectif_autre_precision']))
        <tr><td class="recueil-label">Précision</td><td>{{ $recueil['objectif_autre_precision'] }}</td></tr>
        @endif
        <tr><td class="recueil-label">Horizon d'investissement</td><td>{{ $recueil['horizon_investissement_libelle'] ?? '-' }}</td></tr>
        <tr><td class="recueil-label">Montant d'investissement envisagé</td><td>{{ $recueil['montant_investissement_envisage'] ?? '-' }} €</td></tr>
    </table>

    <div class="recueil-sous-titre">Financement et détention</div>
    <table class="recueil-table">
        <tr><td class="recueil-label">Mode de financement</td><td>{{ $recueil['mode_financement_libelle'] ?? '-' }}</td></tr>
        @if(($donnees['mode_financement'] ?? '') === 'credit')
        <tr><td class="recueil-label">Montant du crédit envisagé</td><td>{{ $recueil['montant_credit_envisage'] ?? '-' }} € sur {{ $recueil['duree_credit_envisagee'] ?? '-' }} ans</td></tr>
        @endif
        <tr><td class="recueil-label">Mode de détention souhaité</td><td>{{ $recueil['mode_detention_libelle'] ?? '-' }}</td></tr>
    </table>

    <div class="recueil-sous-titre">Préférences et situation existante</div>
    <table class="recueil-table">
        <tr><td class="recueil-label">Type de SCPI recherché</td><td>{{ $recueil['type_scpi_recherche_libelle'] ?? '-' }}</td></tr>
        <tr><td class="recueil-label">TMI actuelle</td><td>{{ $recueil['tmi_actuelle_libelle'] ?? '-' }}</td></tr>
    </table>

    @if(!empty($recueil['scpi_deja_detenues']))
    <div class="recueil-sous-titre">SCPI déjà détenues</div>
    <table class="recueil-table">
        @foreach($recueil['scpi_deja_detenues'] as $ligne)
        <tr><td class="recueil-label">{{ $ligne['nom_scpi'] ?? '-' }}</td><td>{{ $ligne['nombre_parts'] ?? '-' }} parts — {{ $ligne['montant_detenu'] ?? '-' }} €</td></tr>
        @endforeach
    </table>
    @endif

    <div class="recueil-sous-titre">Situation financière</div>
    <table class="recueil-table">
        <tr><td class="recueil-label">Patrimoine financier existant</td><td>{{ $recueil['patrimoine_financier_existant'] ?? '-' }} €</td></tr>
        <tr><td class="recueil-label">Revenu annuel du foyer</td><td>{{ $recueil['revenu_annuel_foyer'] ?? '-' }} €</td></tr>
        <tr><td class="recueil-label">Importance de la liquidité</td><td>{{ $recueil['attentes_liquidite_libelle'] ?? '-' }}</td></tr>
    </table>

    <div class="recueil-sous-titre">Risques dont le Client déclare avoir pris connaissance</div>
    <p class="corps-paragraphe">{{ $recueil['risques_pris_connaissance_libelle'] ?? '-' }}</p>

    @if(!empty($recueil['commentaire_conseiller']))
    <div class="recueil-sous-titre">Commentaire du conseiller</div>
    <p class="corps-paragraphe">{{ $recueil['commentaire_conseiller'] }}</p>
    @endif

    <div style="page-break-inside: avoid;">
    <div class="section-title">Fiche d'entrée en relation</div>
    <table class="recueil-table">
        @if($cabinet->numero_association)
        <tr><td class="recueil-label">Immatriculation CIF</td><td>N° {{ $cabinet->numero_association }} — Association agréée AMF</td></tr>
        @endif
        <tr><td class="recueil-label">Statuts réglementés</td><td>{{ is_array($cabinet->statuts_reglementaires) ? implode(', ', $cabinet->statuts_reglementaires) : $cabinet->statuts_reglementaires }}</td></tr>
        <tr><td class="recueil-label">Rémunération</td><td>Commissions versées par les sociétés de gestion et/ou honoraires facturés au Client, précisés par écrit avant toute souscription.</td></tr>
        <tr><td class="recueil-label">Réclamations</td><td>{{ $cabinet->adresse }}, {{ $cabinet->code_postal }} {{ $cabinet->ville }} — {{ $mailConseiller }}</td></tr>
        @if($cabinet->mediateur_nom)
        <tr><td class="recueil-label">Médiation</td><td>{{ $cabinet->mediateur_nom }}@if($cabinet->mediateur_contact) ({{ $cabinet->mediateur_contact }})@endif</td></tr>
        @endif
    </table>
    </div>

    <table class="signature-block">
        <tr>
            <td width="48%">
                <strong>{{ $cabinet->raison_sociale ?: $cabinet->nom_commercial }}</strong><br>
                {{ $nomConseiller }}
            </td>
            <td width="4%"></td>
            <td width="48%" style="text-align:right;">
                Le Client<br>
                <strong>{{ $nomClient }}</strong><br>
                Signature Client<br>
                <span class="mention">(Signature précédée de la mention « Lu et approuvé »)</span>
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
                @if($cabinet->forme_juridique) ({{ $cabinet->forme_juridique }}@if($cabinet->capital_social), capital {{ number_format((float) $cabinet->capital_social, 0, ',', ' ') }} €@endif)@endif
                <br>
                @if($cabinet->numero_rcs) RCS {{ $cabinet->ville_rcs }} {{ $cabinet->numero_rcs }} @endif
                @if($cabinet->numero_tva) | TVA {{ $cabinet->numero_tva }} @endif
                @if($cabinet->numero_association) | CIF n° {{ $cabinet->numero_association }} @endif
                @if($cabinet->mediateur_nom)
                <br>Médiateur : {{ $cabinet->mediateur_nom }}@if($cabinet->mediateur_contact) ({{ $cabinet->mediateur_contact }})@endif
                @endif
            </td>
        </tr>
    </table>
</div>
</body>
</html>
