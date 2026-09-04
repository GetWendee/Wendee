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
        <div class="eyebrow">Mandat de courtage</div>
        <h1>Mandat de courtage en assurance obsèques</h1>
    </div>

    <div class="parties">
        Entre les soussignés :<br><br>
        <strong>Le Mandant</strong><br>
        Nom : {{ $nomClient }}<br>
        Adresse : {{ $client->adresse }}, {{ $client->code_postal }} {{ $client->ville }}<br>
        Téléphone : {{ $client->telephone_mobile }}<br>
        Adresse e-mail : {{ $client->email }}<br><br>
        Et<br><br>
        <strong>Le Courtier en Assurances</strong><br>
        Société : {{ $cabinet->raison_sociale ?: $cabinet->nom_commercial }}<br>
        Adresse : {{ $cabinet->adresse }} {{ $cabinet->code_postal }} {{ $cabinet->ville }}<br>
        RCS : {{ $cabinet->ville_rcs }} {{ $cabinet->numero_rcs }}<br>
        ORIAS : {{ $cabinet->numero_orias }}<br>
        Téléphone : {{ $telConseiller }}<br>
        Adresse e-mail : {{ $mailConseiller }}
    </div>

    <p class="corps-paragraphe">Il est convenu ce qui suit :</p>

    <div class="section-title">1. Objet du mandat</div>
    <p class="corps-paragraphe">Le présent mandat confie au Courtier la recherche, la négociation et le suivi d'un contrat d'assurance obsèques destiné à financer et, le cas échéant, organiser les obsèques du Mandant selon les souhaits exprimés. Le Courtier s'engage à analyser les besoins du Mandant et à lui proposer des contrats adaptés parmi les offres des compagnies d'assurance partenaires.</p>

    <div class="section-title">2. Durée du mandat</div>
    <p class="corps-paragraphe">Le présent mandat est conclu pour une durée d'un an à compter de sa signature. Il est renouvelable par tacite reconduction sauf dénonciation par l'une des parties avec un préavis d'un mois avant la date d'échéance.</p>

    <div class="section-title">3. Rémunération du Courtier</div>
    <p class="corps-paragraphe">Le Courtier est rémunéré par :</p>
    <ul class="corps-liste">
        <li>Commissions versées par les assureurs dans le cadre des contrats souscrits par le Mandant.</li>
        <li>Frais éventuels à la charge du Mandant, détaillés dans une convention spécifique si applicable.</li>
    </ul>

    <div class="section-title">4. Obligations et devoirs du Courtier</div>
    <p class="corps-paragraphe">Le Courtier s'engage à :</p>
    <ul class="corps-liste">
        <li>Agir de façon honnête, loyale, professionnelle et au mieux des intérêts du Mandant.</li>
        <li>Réaliser une analyse objective des besoins avant toute recommandation.</li>
        <li>Remettre une fiche d'information légale (statut ORIAS, liens capitalistiques, modalités de rémunération, existence d'un conseil indépendant ou non).</li>
        <li>Informer le Mandant de l'existence d'éventuels conflits d'intérêts et des solutions mises en place.</li>
        <li>Fournir des explications claires sur la nature du contrat (capital ou prestations), les exclusions et les modalités de revalorisation.</li>
        <li>Assister le Mandant dans la souscription et la transmission de ses volontés à la personne chargée d'organiser les obsèques.</li>
    </ul>

    <div class="section-title">5. Obligations du Mandant</div>
    <p class="corps-paragraphe">Le Mandant s'engage à fournir des informations exactes et complètes nécessaires à l'étude, et à informer sans délai le Courtier de toute modification de sa situation ou de ses souhaits pouvant impacter le contrat.</p>

    <div class="section-title">6. Résiliation du mandat</div>
    <p class="corps-paragraphe">Le mandat peut être résilié par l'une ou l'autre des parties par lettre recommandée avec accusé de réception, sous réserve du respect du préavis mentionné à l'article 2.</p>

    <div class="section-title">7. Protection des données personnelles</div>
    <p class="corps-paragraphe">Les données personnelles, y compris les éléments relatifs à l'état de santé et aux volontés funéraires, sont traitées conformément au RGPD et à la loi Informatique et Libertés, avec le niveau de confidentialité renforcé applicable aux données de santé. Le Mandant dispose d'un droit d'accès, de rectification, d'opposition, de suppression et de portabilité en écrivant au Courtier ou à la CNIL.</p>

    <div class="section-title">8. Réclamations, médiation et droit applicable</div>
    <p class="corps-paragraphe">En cas de réclamation, le Mandant peut s'adresser gratuitement au service réclamation du Courtier. Si aucune solution n'est trouvée, le Mandant peut saisir le Médiateur de l'Assurance (www.mediation-assurance.org)@if($cabinet->mediateur_nom), ou {{ $cabinet->mediateur_nom }}@endif. En cas de litige relatif à l'exécution du présent mandat, les parties s'efforceront de trouver une solution amiable. À défaut, les tribunaux compétents seront ceux du ressort du domicile du Mandant. Le présent mandat est soumis au droit français.</p>

    <div class="section-title">Recueil des données</div>

    <div class="recueil-sous-titre">Objectif du contrat</div>
    <table class="recueil-table">
        <tr><td class="recueil-label">Objectif exprimé</td><td>{{ $recueil['objectif_contrat_libelle'] ?? '-' }}</td></tr>
    </table>

    <div class="recueil-sous-titre">Type de contrat et capital</div>
    <table class="recueil-table">
        <tr><td class="recueil-label">Type de contrat</td><td>{{ $recueil['type_contrat_libelle'] ?? '-' }}</td></tr>
        <tr><td class="recueil-label">Capital souhaité</td><td>{{ $recueil['capital_souhaite'] ?? '-' }} €</td></tr>
    </table>

    <div class="recueil-sous-titre">Modalités de versement</div>
    <table class="recueil-table">
        <tr><td class="recueil-label">Modalité</td><td>{{ $recueil['modalite_versement_libelle'] ?? '-' }}</td></tr>
        @if(!empty($recueil['montant_versement_periodique']))
        <tr><td class="recueil-label">Montant périodique</td><td>{{ $recueil['montant_versement_periodique'] }} € ({{ $recueil['periodicite_versement_libelle'] ?? '-' }})</td></tr>
        @endif
    </table>

    <div class="recueil-sous-titre">Souhaits relatifs aux obsèques</div>
    <table class="recueil-table">
        <tr><td class="recueil-label">Type de cérémonie</td><td>{{ $recueil['type_ceremonie_libelle'] ?? '-' }}</td></tr>
        <tr><td class="recueil-label">Lieu souhaité</td><td>{{ $recueil['lieu_souhaite'] ?? '-' }}</td></tr>
        <tr><td class="recueil-label">Prestataire pressenti</td><td>{{ $recueil['prestataire_pressenti'] ?? '-' }}</td></tr>
    </table>
    @if(!empty($recueil['precisions_obseques']))
    <p class="corps-paragraphe">{{ $recueil['precisions_obseques'] }}</p>
    @endif

    @if(!empty($recueil['organisateurs']))
    <div class="recueil-sous-titre">Personne(s) chargée(s) d'organiser les obsèques</div>
    <table class="recueil-table">
        @foreach($recueil['organisateurs'] as $organisateur)
        <tr><td class="recueil-label">{{ $organisateur['nom'] ?? '-' }} ({{ $organisateur['lien'] ?? '-' }})</td><td>Téléphone : {{ $organisateur['telephone'] ?? '-' }}</td></tr>
        @endforeach
    </table>
    @endif

    <div class="recueil-sous-titre">Déclaration de santé simplifiée</div>
    <table class="recueil-table">
        <tr><td class="recueil-label">Âge de l'assuré</td><td>{{ $recueil['age_assure'] ?? '-' }}</td></tr>
        <tr><td class="recueil-label">Affection grave déclarée</td><td>{{ $recueil['affection_grave_declaree_libelle'] ?? '-' }}</td></tr>
        @if(!empty($recueil['affection_grave_precision']))
        <tr><td class="recueil-label">Précision</td><td>{{ $recueil['affection_grave_precision'] }}</td></tr>
        @endif
    </table>
    <p class="mention">Ces éléments orientent la recherche de contrat et ne remplacent pas le questionnaire médical de l'assureur, lorsqu'il est requis.</p>

    @if(!empty($recueil['commentaire_conseiller']))
    <div class="recueil-sous-titre">Commentaire du conseiller</div>
    <p class="corps-paragraphe">{{ $recueil['commentaire_conseiller'] }}</p>
    @endif

    <div style="page-break-inside: avoid;">
    <div class="section-title">Fiche d'entrée en relation</div>
    <table class="recueil-table">
        <tr><td class="recueil-label">ORIAS</td><td>N° {{ $cabinet->numero_orias }} — www.orias.fr</td></tr>
        <tr><td class="recueil-label">Statuts réglementés</td><td>{{ is_array($cabinet->statuts_reglementaires) ? implode(', ', $cabinet->statuts_reglementaires) : $cabinet->statuts_reglementaires }}</td></tr>
        <tr><td class="recueil-label">Rémunération</td><td>Commissions versées par les compagnies d'assurance et partenaires ; honoraires éventuels précisés par écrit.</td></tr>
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
                @if($cabinet->numero_orias) | ORIAS n° {{ $cabinet->numero_orias }} @endif
                @if($cabinet->mediateur_nom)
                <br>Médiateur : {{ $cabinet->mediateur_nom }}@if($cabinet->mediateur_contact) ({{ $cabinet->mediateur_contact }})@endif
                @endif
            </td>
        </tr>
    </table>
</div>
</body>
</html>
