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
.section-title { font-size: 11pt; font-weight: 700; color: #171514; margin: 22px 0 8px; page-break-after: avoid; page-break-inside: avoid; }
.corps-paragraphe { margin: 0 0 8px; text-align: left; }
.recueil-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
.recueil-table td { padding: 6px 8px; font-size: 9.5pt; border-bottom: 1px solid #ded9d4; vertical-align: top; }
.recueil-table td.recueil-label { width: 42%; color: #817a75; }
.recueil-sous-titre { font-size: 10pt; font-weight: 700; color: #171514; margin: 16px 0 6px; page-break-after: avoid; }
.analyse-bloc { margin: 4px 0 14px; page-break-inside: avoid; }
.analyse-bloc-titre { font-size: 9pt; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; color: #817a75; margin-bottom: 8px; }
.analyse-point { margin-bottom: 8px; }
.analyse-point-titre { font-weight: 700; color: #171514; font-size: 9.5pt; }
.analyse-point-texte { font-size: 9.5pt; color: #242424; margin: 1px 0 0; }
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
@php
    $lister = fn (?string $cle, $valeur) => ($cle && $valeur !== null && $valeur !== '') ? (config("listes.$cle")[$valeur] ?? $valeur) : '-';
    $texte = fn ($valeur) => ($valeur !== null && $valeur !== '') ? $valeur : '-';
    $dateFr = fn ($valeur) => $valeur ? \Illuminate\Support\Carbon::parse($valeur)->translatedFormat('d F Y') : '-';
@endphp
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
        <div class="eyebrow">Recueil d'informations client</div>
        <h1>Recueil d'informations</h1>
    </div>

    <div class="section-title">Préalable</div>
    <p class="corps-paragraphe">Ce questionnaire a pour finalité de permettre au professionnel de fournir un conseil adapté à votre situation. Si vous ne communiquez pas les informations requises, votre conseiller ne pourra pas poursuivre sa mission et devra s'abstenir de vous recommander les opérations, instruments et services relevant de son activité.</p>
    <p class="corps-paragraphe">La fourniture d'informations complètes et sincères est une condition nécessaire pour bénéficier d'un service de qualité.</p>
    <table class="recueil-table">
        <tr><td class="recueil-label">Date</td><td>{{ $dateGeneration }}</td></tr>
        <tr><td class="recueil-label">Acceptation des termes et conditions</td><td>{{ $kyc?->accepte_cgu ? 'Accepté' : 'Non accepté' }}</td></tr>
    </table>

    @if(!empty($pointsForts) || !empty($pointsAttention))
    <div class="section-title">Analyse de votre situation</div>
    @if(!empty($pointsForts))
    <div class="analyse-bloc">
        <div class="analyse-bloc-titre">Points forts</div>
        @foreach($pointsForts as $point)
        <div class="analyse-point">
            <div class="analyse-point-titre">{{ $point['titre'] ?? '' }}</div>
            <p class="analyse-point-texte">{{ $point['analyse'] ?? '' }}</p>
        </div>
        @endforeach
    </div>
    @endif
    @if(!empty($pointsAttention))
    <div class="analyse-bloc">
        <div class="analyse-bloc-titre">Points d'attention</div>
        @foreach($pointsAttention as $point)
        <div class="analyse-point">
            <div class="analyse-point-titre">{{ $point['titre'] ?? '' }}</div>
            <p class="analyse-point-texte">{{ $point['analyse'] ?? '' }}</p>
        </div>
        @endforeach
    </div>
    @endif
    @endif

    <div class="section-title">Informations civiles</div>
    <table class="recueil-table">
        <tr><td class="recueil-label">Civilité</td><td>{{ $texte($client->civilite) }}</td></tr>
        <tr><td class="recueil-label">Nom complet</td><td>{{ $texte(trim($client->prenom . ' ' . $client->nom)) }}</td></tr>
        <tr><td class="recueil-label">Nom de naissance</td><td>{{ $texte($client->nom_jeune_fille) }}</td></tr>
        <tr><td class="recueil-label">Date de naissance</td><td>{{ $dateFr($client->date_naissance) }}</td></tr>
        <tr><td class="recueil-label">Commune de naissance</td><td>{{ $texte($kyc?->commune_naissance) }}</td></tr>
        <tr><td class="recueil-label">Code postal de naissance</td><td>{{ $texte($kyc?->code_postal_naissance) }}</td></tr>
        <tr><td class="recueil-label">Né(e) en France métropolitaine</td><td>{{ $lister('oui_non', $kyc?->ne_en_france) }}</td></tr>
        <tr><td class="recueil-label">Nationalité française</td><td>{{ $lister('oui_non', $kyc?->francais) }}</td></tr>
        <tr><td class="recueil-label">Autre nationalité</td><td>{{ $lister('nationalites', $kyc?->autre_nationalite) }}</td></tr>
        <tr><td class="recueil-label">Classification client MIF</td><td>{{ $lister('classification_mif', $kyc?->classification_mif) }}</td></tr>
        <tr><td class="recueil-label">Capacité juridique</td><td>{{ $lister('capacite_juridique', $kyc?->capacite_juridique) }}</td></tr>
    </table>

    <div class="section-title">Informations familiales</div>
    <table class="recueil-table">
        <tr><td class="recueil-label">Situation familiale</td><td>{{ $lister('situation_familiale', $kyc?->situation_familiale) }}</td></tr>
        <tr><td class="recueil-label">Date du mariage</td><td>{{ $dateFr($kyc?->date_mariage) }}</td></tr>
        <tr><td class="recueil-label">Lieu du mariage</td><td>{{ $texte($kyc?->lieu_mariage) }}</td></tr>
        <tr><td class="recueil-label">Régime matrimonial</td><td>{{ $lister('regime_matrimonial', $kyc?->regime_matrimonial) }}</td></tr>
        <tr><td class="recueil-label">Donation au dernier vivant à votre profit</td><td>{{ $lister('oui_non_nsp', $kyc?->donation_dernier_vivant_profit) }}</td></tr>
        <tr><td class="recueil-label">Donation au dernier vivant au profit du conjoint</td><td>{{ $lister('oui_non_nsp', $kyc?->donation_dernier_vivant_conjoint) }}</td></tr>
        <tr><td class="recueil-label">Date du PACS</td><td>{{ $dateFr($kyc?->date_pacs) }}</td></tr>
        <tr><td class="recueil-label">Lieu du PACS</td><td>{{ $texte($kyc?->lieu_pacs) }}</td></tr>
        <tr><td class="recueil-label">Convention de PACS</td><td>{{ $lister('convention_pacs', $kyc?->convention_pacs) }}</td></tr>
    </table>

    @if($kyc?->a_conjoint)
    <div class="recueil-sous-titre">Votre conjoint, partenaire</div>
    <table class="recueil-table">
        <tr><td class="recueil-label">Civilité</td><td>{{ $lister('civilite_conjoint', $kyc->conjoint_civilite) }}</td></tr>
        <tr><td class="recueil-label">Nom</td><td>{{ $texte($kyc->conjoint_nom) }}</td></tr>
        <tr><td class="recueil-label">Nom de naissance</td><td>{{ $texte($kyc->conjoint_nom_naissance) }}</td></tr>
        <tr><td class="recueil-label">Prénom</td><td>{{ $texte($kyc->conjoint_prenom) }}</td></tr>
        <tr><td class="recueil-label">Date de naissance</td><td>{{ $dateFr($kyc->conjoint_date_naissance) }}</td></tr>
    </table>
    @endif

    <div class="recueil-sous-titre">{{ $personnesACharge->count() }} personne(s) à charge</div>
    @if($personnesACharge->isNotEmpty())
    <table class="recueil-table">
        @foreach($personnesACharge as $personne)
        <tr><td class="recueil-label">{{ $texte(trim(($personne->civilite ?? '') . ' ' . $personne->prenom . ' ' . $personne->nom)) }}</td><td>{{ $dateFr($personne->date_naissance) }}</td></tr>
        @endforeach
    </table>
    @endif

    <div class="section-title">Informations professionnelles</div>
    <div class="recueil-sous-titre">Vous</div>
    <table class="recueil-table">
        <tr><td class="recueil-label">Statut</td><td>{{ $lister('statut_professionnel', $kyc?->statut_professionnel) }}</td></tr>
        <tr><td class="recueil-label">Profession</td><td>{{ $texte($kyc?->profession_libelle) }}</td></tr>
        <tr><td class="recueil-label">Profession actuelle (CSP)</td><td>{{ $lister('csp', $kyc?->csp) }}</td></tr>
        <tr><td class="recueil-label">Société, employeur</td><td>{{ $texte($kyc?->societe_employeur) }}</td></tr>
        <tr><td class="recueil-label">Code NAF</td><td>{{ $lister('code_naf', $kyc?->code_naf) }}</td></tr>
        <tr><td class="recueil-label">SIRET</td><td>{{ $texte($kyc?->siret_employeur) }}</td></tr>
        <tr><td class="recueil-label">Dans l'entreprise depuis le</td><td>{{ $dateFr($kyc?->date_entree_entreprise) }}</td></tr>
        <tr><td class="recueil-label">Départ en retraite prévu à l'âge de</td><td>{{ $texte($kyc?->age_depart_retraite) }}</td></tr>
    </table>

    @if($kyc?->conjoint_ajouter_profession)
    <div class="recueil-sous-titre">Votre conjoint, partenaire</div>
    <table class="recueil-table">
        <tr><td class="recueil-label">Statut</td><td>{{ $lister('statut_professionnel', $kyc->conjoint_statut_professionnel) }}</td></tr>
        <tr><td class="recueil-label">Profession</td><td>{{ $texte($kyc->conjoint_profession_libelle) }}</td></tr>
        <tr><td class="recueil-label">Profession actuelle (CSP)</td><td>{{ $lister('csp', $kyc->conjoint_csp) }}</td></tr>
        <tr><td class="recueil-label">Société, employeur</td><td>{{ $texte($kyc->conjoint_societe_employeur) }}</td></tr>
        <tr><td class="recueil-label">Code NAF</td><td>{{ $lister('code_naf', $kyc->conjoint_code_naf) }}</td></tr>
        <tr><td class="recueil-label">SIRET</td><td>{{ $texte($kyc->conjoint_siret_employeur) }}</td></tr>
        <tr><td class="recueil-label">Dans l'entreprise depuis le</td><td>{{ $dateFr($kyc->conjoint_date_entree_entreprise) }}</td></tr>
        <tr><td class="recueil-label">Départ en retraite prévu à l'âge de</td><td>{{ $texte($kyc->conjoint_age_depart_retraite) }}</td></tr>
    </table>
    @endif

    <div class="section-title">Coordonnées</div>
    <table class="recueil-table">
        <tr><td class="recueil-label">Téléphone mobile</td><td>{{ $texte($client->telephone_mobile) }}</td></tr>
        <tr><td class="recueil-label">Téléphone domicile</td><td>{{ $texte($client->telephone_domicile) }}</td></tr>
        <tr><td class="recueil-label">E-mail</td><td>{{ $texte($client->email) }}</td></tr>
        <tr><td class="recueil-label">Adresse</td><td>{{ $texte($client->adresse) }}</td></tr>
        <tr><td class="recueil-label">Code postal</td><td>{{ $texte($client->code_postal) }}</td></tr>
        <tr><td class="recueil-label">Ville</td><td>{{ $texte($client->ville) }}</td></tr>
        <tr><td class="recueil-label">Pays</td><td>{{ $lister('pays', $client->pays) }}</td></tr>
        <tr><td class="recueil-label">Résidence fiscale identique à l'adresse principale</td><td>{{ $lister('oui_non', $kyc?->residence_fiscale_identique) }}</td></tr>
        <tr><td class="recueil-label">Autre pays de résidence fiscale</td><td>{{ $lister('pays_hors_france', $kyc?->autre_pays_residence_fiscale) }}</td></tr>
        <tr><td class="recueil-label">Hébergé(e) par une tierce personne</td><td>{{ $lister('oui_non', $kyc?->heberge_par_tiers) }}</td></tr>
    </table>

    <div class="section-title">Compléments</div>
    <table class="recueil-table">
        <tr><td class="recueil-label">Personne Politiquement Exposée (PPE)</td><td>{{ $lister('oui_non', $kyc?->est_ppe) }}</td></tr>
        @if($kyc?->est_ppe === 'oui')
        <tr><td class="recueil-label">Fonction exercée</td><td>{{ $texte($kyc?->ppe_fonction) }}</td></tr>
        <tr><td class="recueil-label">Période</td><td>{{ $dateFr($kyc?->ppe_date_debut) }} — {{ $dateFr($kyc?->ppe_date_fin) }}</td></tr>
        <tr><td class="recueil-label">Pays</td><td>{{ $lister('pays', $kyc?->ppe_pays) }}</td></tr>
        @endif
        <tr><td class="recueil-label">Proche d'une Personne Politiquement Exposée</td><td>{{ $lister('oui_non', $kyc?->proche_ppe) }}</td></tr>
        @if($kyc?->proche_ppe === 'oui')
        <tr><td class="recueil-label">Fonction exercée</td><td>{{ $texte($kyc?->proche_ppe_fonction) }}</td></tr>
        <tr><td class="recueil-label">Personne liée</td><td>{{ $texte(trim(($kyc?->proche_ppe_prenom ?? '') . ' ' . ($kyc?->proche_ppe_nom ?? ''))) }} ({{ $lister('nature_du_lien', $kyc?->proche_ppe_nature_lien) }})</td></tr>
        <tr><td class="recueil-label">Période</td><td>{{ $dateFr($kyc?->proche_ppe_date_debut) }} — {{ $dateFr($kyc?->proche_ppe_date_fin) }}</td></tr>
        <tr><td class="recueil-label">Pays</td><td>{{ $lister('pays', $kyc?->proche_ppe_pays) }}</td></tr>
        @endif
    </table>

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
                @if($kyc?->signe_le)
                Validé le {{ $dateFr($kyc->signe_le) }}
                @endif
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
