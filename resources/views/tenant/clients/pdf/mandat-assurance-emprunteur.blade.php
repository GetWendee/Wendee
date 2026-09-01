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
        <h1>Mandat de courtage en assurance emprunteur</h1>
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
    <p class="corps-paragraphe">Le présent mandat confie au Courtier la recherche, la négociation et le suivi d'un contrat d'assurance emprunteur destiné à garantir le remboursement du prêt du Mandant, dans le cadre du droit à la délégation d'assurance prévu par le Code de la consommation. Le Courtier s'engage à analyser les besoins du Mandant et à comparer les offres du marché avec le contrat groupe proposé par l'établissement prêteur, à garanties équivalentes.</p>

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
        <li>Vérifier l'équivalence du niveau de garanties entre le contrat proposé et le contrat groupe de la banque, conformément à la grille de critères en vigueur.</li>
        <li>Remettre une fiche d'information légale (statut ORIAS, liens capitalistiques, modalités de rémunération, existence d'un conseil indépendant ou non).</li>
        <li>Informer le Mandant de l'existence d'éventuels conflits d'intérêts et des solutions mises en place.</li>
        <li>Assister le Mandant dans les démarches de délégation ou de substitution d'assurance auprès de l'établissement prêteur.</li>
    </ul>

    <div class="section-title">5. Obligations du Mandant</div>
    <p class="corps-paragraphe">Le Mandant s'engage à fournir des informations exactes et complètes nécessaires à l'étude, notamment lors de la déclaration de risque et du questionnaire de santé demandé par l'assureur, ainsi que les caractéristiques exactes du prêt concerné, et à informer sans délai le Courtier de toute modification de sa situation.</p>

    <div class="section-title">6. Résiliation du mandat</div>
    <p class="corps-paragraphe">Le mandat peut être résilié par l'une ou l'autre des parties par lettre recommandée avec accusé de réception, sous réserve du respect du préavis mentionné à l'article 2.</p>

    <div class="section-title">7. Protection des données personnelles</div>
    <p class="corps-paragraphe">Les données personnelles, y compris les données de santé recueillies dans le cadre de la déclaration de risque, sont traitées conformément au RGPD et à la loi Informatique et Libertés, avec le niveau de confidentialité renforcé applicable aux données de santé. Le Mandant dispose d'un droit d'accès, de rectification, d'opposition, de suppression et de portabilité en écrivant au Courtier ou à la CNIL.</p>

    <div class="section-title">8. Réclamations, médiation et droit applicable</div>
    <p class="corps-paragraphe">En cas de réclamation, le Mandant peut s'adresser gratuitement au service réclamation du Courtier. Si aucune solution n'est trouvée, le Mandant peut saisir le Médiateur de l'Assurance (www.mediation-assurance.org)@if($cabinet->mediateur_nom), ou {{ $cabinet->mediateur_nom }}@endif. En cas de litige relatif à l'exécution du présent mandat, les parties s'efforceront de trouver une solution amiable. À défaut, les tribunaux compétents seront ceux du ressort du domicile du Mandant. Le présent mandat est soumis au droit français.</p>

    <div class="section-title">Recueil des données</div>

    <div class="recueil-sous-titre">Prêt concerné</div>
    <table class="recueil-table">
        <tr><td class="recueil-label">Type de prêt</td><td>{{ $recueil['type_pret_libelle'] ?? '-' }}</td></tr>
        @if(!empty($recueil['type_pret_autre_precision']))
        <tr><td class="recueil-label">Précision</td><td>{{ $recueil['type_pret_autre_precision'] }}</td></tr>
        @endif
        <tr><td class="recueil-label">Montant emprunté</td><td>{{ $recueil['montant_emprunte'] ?? '-' }} €</td></tr>
        <tr><td class="recueil-label">Durée du prêt</td><td>{{ $recueil['duree_pret'] ?? '-' }} ans</td></tr>
        <tr><td class="recueil-label">Taux du prêt</td><td>{{ $recueil['taux_pret'] ?? '-' }} %</td></tr>
        <tr><td class="recueil-label">Date de l'offre de prêt</td><td>{{ $recueil['date_offre_pret'] ?? '-' }}</td></tr>
    </table>

    @if(!empty($recueil['emprunteurs']))
    <div class="recueil-sous-titre">Quotité d'assurance par emprunteur</div>
    <table class="recueil-table">
        @foreach($recueil['emprunteurs'] as $emprunteur)
        <tr><td class="recueil-label">{{ $emprunteur['nom'] ?? '-' }}</td><td>Quotité : {{ $emprunteur['quotite_pourcentage'] ?? '-' }} %</td></tr>
        @endforeach
    </table>
    @endif

    <div class="recueil-sous-titre">Garanties et couverture</div>
    <table class="recueil-table">
        <tr><td class="recueil-label">Garanties souhaitées</td><td>{{ $recueil['garanties_souhaitees_libelle'] ?? '-' }}</td></tr>
        <tr><td class="recueil-label">Type de couverture</td><td>{{ $recueil['type_couverture_libelle'] ?? '-' }}</td></tr>
    </table>

    <div class="recueil-sous-titre">Déclaration de santé simplifiée</div>
    <table class="recueil-table">
        <tr><td class="recueil-label">Fumeur</td><td>{{ $recueil['fumeur_libelle'] ?? '-' }}</td></tr>
        <tr><td class="recueil-label">Taille / Poids</td><td>{{ $recueil['taille_cm'] ?? '-' }} cm / {{ $recueil['poids_kg'] ?? '-' }} kg</td></tr>
        <tr><td class="recueil-label">Profession à risque</td><td>{{ $recueil['profession_a_risque_libelle'] ?? '-' }}</td></tr>
        <tr><td class="recueil-label">Sports à risque</td><td>{{ $recueil['sports_a_risque_libelle'] ?? '-' }}</td></tr>
        <tr><td class="recueil-label">Antécédents médicaux déclarés</td><td>{{ $recueil['antecedents_medicaux_libelle'] ?? '-' }}</td></tr>
        <tr><td class="recueil-label">Traitement en cours</td><td>{{ $recueil['traitement_en_cours_libelle'] ?? '-' }}</td></tr>
        @if(!empty($recueil['antecedents_precision']))
        <tr><td class="recueil-label">Précisions</td><td>{{ $recueil['antecedents_precision'] }}</td></tr>
        @endif
        <tr><td class="recueil-label">Arrêt de travail récent</td><td>{{ $recueil['arret_travail_recent_libelle'] ?? '-' }}</td></tr>
    </table>
    <p class="mention">Ces éléments orientent la recherche de contrat et ne remplacent pas le questionnaire médical de l'assureur.</p>

    <div class="recueil-sous-titre">Situation professionnelle</div>
    <table class="recueil-table">
        <tr><td class="recueil-label">Statut professionnel</td><td>{{ $recueil['statut_professionnel_libelle'] ?? '-' }}</td></tr>
        @if(!empty($recueil['statut_professionnel_autre_precision']))
        <tr><td class="recueil-label">Précision</td><td>{{ $recueil['statut_professionnel_autre_precision'] }}</td></tr>
        @endif
        <tr><td class="recueil-label">Ancienneté</td><td>{{ $recueil['anciennete_annees'] ?? '-' }} ans</td></tr>
        <tr><td class="recueil-label">Revenu annuel</td><td>{{ $recueil['revenu_annuel'] ?? '-' }} €</td></tr>
    </table>

    <div class="recueil-sous-titre">Contrat actuel et délégation d'assurance</div>
    <table class="recueil-table">
        <tr><td class="recueil-label">Délégation d'assurance envisagée</td><td>{{ $recueil['delegation_assurance_libelle'] ?? '-' }}</td></tr>
        @if(!empty($recueil['assureur_actuel']))
        <tr><td class="recueil-label">Assureur actuel (contrat groupe)</td><td>{{ $recueil['assureur_actuel'] }}</td></tr>
        @endif
        @if(!empty($recueil['date_echeance_actuelle']))
        <tr><td class="recueil-label">Date d'échéance pour changement</td><td>{{ $recueil['date_echeance_actuelle'] }}</td></tr>
        @endif
    </table>

    @if(!empty($recueil['commentaire_conseiller']))
    <div class="recueil-sous-titre">Commentaire du conseiller</div>
    <p class="corps-paragraphe">{{ $recueil['commentaire_conseiller'] }}</p>
    @endif

    <div style="page-break-inside: avoid;">
    <div class="section-title">Fiche d'entrée en relation</div>
    <table class="recueil-table">
        <tr><td class="recueil-label">ORIAS</td><td>N° {{ $cabinet->numero_orias }} — www.orias.fr</td></tr>
        <tr><td class="recueil-label">Statuts réglementés</td><td>{{ $cabinet->statuts_reglementaires }}</td></tr>
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
