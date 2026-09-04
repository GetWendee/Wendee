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
.donnees-table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
.donnees-table th { text-align: left; font-size: 8.5pt; text-transform: uppercase; letter-spacing: .03em; color: #817a75; padding: 6px 8px; border-bottom: 1px solid #171514; }
.donnees-table td { padding: 6px 8px; font-size: 9.5pt; border-bottom: 1px solid #ded9d4; vertical-align: top; }
.donnees-vide { font-size: 9.5pt; color: #817a75; font-style: italic; margin: 0 0 14px; }
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
    $euros = fn ($valeur) => $valeur !== null ? number_format((float) $valeur, 0, ',', ' ') . ' €' : '-';
    $pourcent = fn ($valeur) => ($valeur !== null && $valeur !== '') ? $valeur . ' %' : '-';
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
        <div class="eyebrow">Recueil patrimonial</div>
        <h1>Recueil patrimonial</h1>
    </div>

    <div class="section-title">Préalable</div>
    <p class="corps-paragraphe">Ce questionnaire permet au professionnel de comprendre l'organisation de votre patrimoine et son mode de détention. La qualité de son conseil dépend étroitement de cette connaissance : la composition de votre foyer fiscal, le mode de détention de vos actifs, et la répartition des différentes classes d'actifs.</p>
    <p class="corps-paragraphe">La fourniture d'informations complètes et sincères est une condition nécessaire pour bénéficier d'un service de qualité.</p>
    <table class="recueil-table">
        <tr><td class="recueil-label">Date</td><td>{{ $dateGeneration }}</td></tr>
        <tr><td class="recueil-label">Acceptation des termes et conditions</td><td>{{ $fiscalite?->accepte_cgu ? 'Accepté' : 'Non accepté' }}</td></tr>
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

    <div class="section-title">Actif non financier</div>
    @if($elements['actif_non_financier']->isNotEmpty())
    <table class="donnees-table">
        <tr><th>Nature</th><th>Désignation</th><th>Valeur estimée</th><th>Mode de détention</th></tr>
        @foreach($elements['actif_non_financier'] as $e)
        <tr><td>{{ $texte($e->nature) }}</td><td>{{ $texte($e->designation) }}</td><td>{{ $euros($e->montant) }}</td><td>{{ $texte($e->mode_detention) }}</td></tr>
        @endforeach
    </table>
    @else
    <p class="donnees-vide">Aucun actif non financier renseigné.</p>
    @endif

    <div class="section-title">Actif financier</div>
    @if($elements['actif_financier']->isNotEmpty())
    <table class="donnees-table">
        <tr><th>Nature</th><th>Désignation</th><th>Valeur estimée</th><th>Mode de détention</th></tr>
        @foreach($elements['actif_financier'] as $e)
        <tr><td>{{ $texte($e->nature) }}</td><td>{{ $texte($e->designation) }}</td><td>{{ $euros($e->montant) }}</td><td>{{ $texte($e->mode_detention) }}</td></tr>
        @endforeach
    </table>
    @else
    <p class="donnees-vide">Aucun actif financier renseigné.</p>
    @endif

    <div class="section-title">Passif</div>
    @if($elements['passif']->isNotEmpty())
    <table class="donnees-table">
        <tr><th>Type de prêt</th><th>Désignation</th><th>Souscrit le</th><th>Montant emprunté</th><th>Durée</th><th>Taux</th></tr>
        @foreach($elements['passif'] as $e)
        <tr><td>{{ $texte($e->type_pret) }}</td><td>{{ $texte($e->designation) }}</td><td>{{ $dateFr($e->date_souscription) }}</td><td>{{ $euros($e->montant) }}</td><td>{{ $e->duree ? $e->duree . ' ans' : '-' }}</td><td>{{ $pourcent($e->taux_interet) }}</td></tr>
        @endforeach
    </table>
    @else
    <p class="donnees-vide">Aucun passif renseigné.</p>
    @endif

    <div class="section-title">Revenus</div>
    @if($elements['revenu']->isNotEmpty())
    <table class="donnees-table">
        <tr><th>Type de revenu</th><th>Désignation</th><th>Montant</th><th>Périodicité</th></tr>
        @foreach($elements['revenu'] as $e)
        <tr><td>{{ $texte($e->nature) }}</td><td>{{ $texte($e->designation) }}</td><td>{{ $euros($e->montant) }}</td><td>{{ $e->periodicite === 'mensuel' ? 'Mensuel' : 'Annuel' }}</td></tr>
        @endforeach
    </table>
    @else
    <p class="donnees-vide">Aucun revenu renseigné.</p>
    @endif

    <div class="section-title">Charges</div>
    @if($elements['charge']->isNotEmpty())
    <table class="donnees-table">
        <tr><th>Type de charge</th><th>Désignation</th><th>Montant</th><th>Périodicité</th></tr>
        @foreach($elements['charge'] as $e)
        <tr><td>{{ $texte($e->nature) }}</td><td>{{ $texte($e->designation) }}</td><td>{{ $euros($e->montant) }}</td><td>{{ $e->periodicite === 'mensuel' ? 'Mensuel' : 'Annuel' }}</td></tr>
        @endforeach
    </table>
    @else
    <p class="donnees-vide">Aucune charge renseignée.</p>
    @endif

    <div class="section-title">Fiscalité</div>
    <div class="recueil-sous-titre">Impôt sur le revenu (IRPP)</div>
    <table class="recueil-table">
        <tr><td class="recueil-label">Résident fiscal français</td><td>{{ $lister('oui_non', $fiscalite?->resident_fiscal_francais) }}</td></tr>
        <tr><td class="recueil-label">Impôt sur le revenu (IRPP)</td><td>{{ $euros($fiscalite?->irpp_montant) }}</td></tr>
        <tr><td class="recueil-label">Nombre de parts</td><td>{{ $texte($fiscalite?->irpp_nombre_parts) }}</td></tr>
        <tr><td class="recueil-label">Connaît son TMI (IR)</td><td>{{ $lister('oui_non', $fiscalite?->connait_tmi_ir) }}</td></tr>
        <tr><td class="recueil-label">Taux Marginal d'Imposition (TMI)</td><td>{{ $pourcent($fiscalite?->tmi_ir) }}</td></tr>
        <tr><td class="recueil-label">Réductions et crédits d'impôts</td><td>{{ $euros($fiscalite?->reductions_credits_impots) }}</td></tr>
        <tr><td class="recueil-label">Impôt net à payer</td><td>{{ $euros($fiscalite?->impot_net_a_payer) }}</td></tr>
        <tr><td class="recueil-label">Contributions sociales</td><td>{{ $euros($fiscalite?->contributions_sociales) }}</td></tr>
    </table>

    <div class="recueil-sous-titre">Impôt sur la Fortune Immobilière (IFI)</div>
    <table class="recueil-table">
        <tr><td class="recueil-label">Imposé à l'IFI</td><td>{{ $lister('oui_non', $fiscalite?->impose_ifi) }}</td></tr>
        <tr><td class="recueil-label">Base imposable</td><td>{{ $euros($fiscalite?->base_imposable_ifi) }}</td></tr>
        <tr><td class="recueil-label">Connaît son TMI (IFI)</td><td>{{ $lister('oui_non', $fiscalite?->connait_tmi_ifi) }}</td></tr>
        <tr><td class="recueil-label">Taux Marginal d'Imposition (IFI)</td><td>{{ $pourcent($fiscalite?->tmi_ifi) }}</td></tr>
        <tr><td class="recueil-label">Réductions d'IFI</td><td>{{ $euros($fiscalite?->reductions_ifi) }}</td></tr>
        <tr><td class="recueil-label">IFI net à payer</td><td>{{ $euros($fiscalite?->ifi_net_a_payer) }}</td></tr>
    </table>

    <div class="section-title">Facta</div>
    <table class="recueil-table">
        <tr><td class="recueil-label">Êtes-vous une US Person</td><td>{{ $lister('oui_non', $fiscalite?->us_person) }}</td></tr>
        @if($fiscalite?->us_person === 'oui')
        <tr><td class="recueil-label">Citoyen des États-Unis</td><td>{{ $lister('oui_non', $fiscalite?->us_citoyen) }}</td></tr>
        <tr><td class="recueil-label">Résident des États-Unis</td><td>{{ $lister('oui_non', $fiscalite?->us_resident) }}</td></tr>
        <tr><td class="recueil-label">Possède une carte verte</td><td>{{ $lister('oui_non', $fiscalite?->us_carte_verte) }}</td></tr>
        <tr><td class="recueil-label">A séjourné aux États-Unis</td><td>{{ $lister('oui_non', $fiscalite?->us_sejour) }}</td></tr>
        <tr><td class="recueil-label">US Person par possession d'une entité</td><td>{{ $lister('oui_non', $fiscalite?->us_entite) }}</td></tr>
        <tr><td class="recueil-label">US Person pour d'autres raisons</td><td>{{ $lister('oui_non', $fiscalite?->us_autre_raison) }}</td></tr>
        <tr><td class="recueil-label">En possession d'un numéro fiscal (US TIN)</td><td>{{ $lister('oui_non', $fiscalite?->us_tin) }}</td></tr>
        @endif
    </table>

    <div class="section-title">Objectifs</div>
    @if($objectifs->isNotEmpty())
    <table class="donnees-table">
        <tr><th>Objectif</th><th>Horizon</th></tr>
        @foreach($objectifs as $o)
        <tr><td>{{ $texte($o->objectif) }}</td><td>{{ $o->horizon ? $o->horizon . ' ans' : '-' }}</td></tr>
        @endforeach
    </table>
    @else
    <p class="donnees-vide">Aucun objectif renseigné.</p>
    @endif
    <table class="recueil-table">
        <tr><td class="recueil-label">Effort d'épargne mensuel dédié à vos objectifs</td><td>{{ $euros($fiscalite?->effort_epargne_mensuel) }}</td></tr>
        <tr><td class="recueil-label">Montant de votre patrimoine total (si connu)</td><td>{{ $euros($fiscalite?->montant_patrimoine_total) }}</td></tr>
        <tr><td class="recueil-label">Montant de vos revenus (si connu)</td><td>{{ $euros($fiscalite?->montant_revenus_annuels) }}</td></tr>
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
                @if($fiscalite?->signe_le)
                Validé le {{ $dateFr($fiscalite->signe_le) }}
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
