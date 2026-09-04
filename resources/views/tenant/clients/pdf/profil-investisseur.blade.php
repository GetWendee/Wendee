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
.recueil-table td.recueil-valeur { font-weight: 700; color: #171514; }
.recueil-sous-titre { font-size: 10pt; font-weight: 700; color: #171514; margin: 16px 0 6px; page-break-after: avoid; }
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
    $nettoyer = function ($valeur) {
        if (! $valeur) {
            return 'Non déterminé';
        }
        return trim(preg_replace('/^[🔴🟠🟢]+\s*/u', '', $valeur));
    };
    $profilFinal = $nettoyer($profil?->profil_risque_final_echelle ?? $profil?->profil_risque_final);
    $tolerance = $nettoyer($profil?->score_tolerance_risque_echelle);
    $capacite = $nettoyer($profil?->score_capacite_financiere_echelle);
    $experience = $nettoyer($profil?->score_experience_global_echelle);
    $connaissance = $nettoyer($profil?->score_connaissance_global_echelle);
    $pertes = $profil?->score_capacite_subir_pertes_echelle ? $nettoyer($profil->score_capacite_subir_pertes_echelle) : null;
    $valeurObjectifs = $profil?->reponses['profil_investisseur_objetifs'] ?? null;
    $valeursObjectifs = is_array($valeurObjectifs) ? $valeurObjectifs : (is_string($valeurObjectifs) && $valeurObjectifs !== '' ? [$valeurObjectifs] : []);
    $objectif = ! empty($valeursObjectifs)
        ? implode(', ', array_map(fn ($code) => config('patrimoine.objectifs')[$code] ?? $code, $valeursObjectifs))
        : null;
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
        <div class="eyebrow">Profil investisseur</div>
        <h1>Profil Investisseur</h1>
    </div>

    <div class="section-title">Préalable</div>
    <p class="corps-paragraphe">Ce questionnaire permet à chaque individu de déterminer son profil d'investisseur, pour le guider vers des solutions de placement adaptées, grâce à l'évaluation du niveau de connaissance et d'expérience des marchés financiers, la mesure de la sensibilité au risque, et l'identification des préférences de placement pour de futurs projets.</p>
    <p class="corps-paragraphe">La fourniture d'informations complètes et sincères est une condition nécessaire pour bénéficier d'un service de qualité.</p>
    <table class="recueil-table">
        <tr><td class="recueil-label">Date</td><td>{{ $dateGeneration }}</td></tr>
        <tr><td class="recueil-label">Acceptation des termes et conditions</td><td>{{ $profil?->accepte_cgu ? 'Accepté' : 'Non accepté' }}</td></tr>
    </table>

    @if(!empty($pointsForts) || !empty($pointsAttention))
    <div class="section-title">Analyse de votre profil</div>
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

    <div class="section-title">Profil de risque</div>
    <table class="recueil-table">
        <tr><td class="recueil-label">Profil final</td><td class="recueil-valeur">{{ $profilFinal }}</td></tr>
        <tr><td class="recueil-label">Tolérance au risque</td><td>{{ $tolerance }}</td></tr>
        <tr><td class="recueil-label">Capacité financière</td><td>{{ $capacite }}</td></tr>
        <tr><td class="recueil-label">Expérience</td><td>{{ $experience }}</td></tr>
        <tr><td class="recueil-label">Connaissance</td><td>{{ $connaissance }}</td></tr>
        @if($pertes)
        <tr><td class="recueil-label">Capacité à subir des pertes</td><td>{{ $pertes }}</td></tr>
        @endif
    </table>

    @if($profil?->engagement_extra_financier_echelle || $profil?->orientation_extra_financier_echelle || $profil?->thematiques_esg_echelle)
    <div class="section-title">Extra-financier</div>
    <table class="recueil-table">
        @if($profil?->engagement_extra_financier_echelle)
        <tr><td class="recueil-label">Engagement extra-financier</td><td>{{ $nettoyer($profil->engagement_extra_financier_echelle) }}</td></tr>
        @endif
        @if($profil?->orientation_extra_financier_echelle)
        <tr><td class="recueil-label">Orientation</td><td>{{ $nettoyer($profil->orientation_extra_financier_echelle) }}</td></tr>
        @endif
        @if($profil?->thematiques_esg_echelle)
        <tr><td class="recueil-label">Thématiques ESG</td><td>{{ $nettoyer($profil->thematiques_esg_echelle) }}</td></tr>
        @endif
    </table>
    @endif

    @if($objectif)
    <div class="section-title">Objectifs</div>
    <p class="corps-paragraphe">{{ $objectif }}</p>
    @endif

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
                @if($profil?->signe_le)
                Validé le {{ \Illuminate\Support\Carbon::parse($profil->signe_le)->translatedFormat('d F Y') }}
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
