<x-tenant-app-layout>

@php
    $viewRole = Auth::user()?->effectiveRole();
    $profil = $client->profilInvestisseur;

    $initiales = mb_strtoupper(
        mb_substr($client->prenom ?? '', 0, 1) .
        mb_substr($client->nom ?? '', 0, 1)
    );

    $formatEuro = fn($value) =>
        number_format((float) $value, 0, ',', ' ') . ' €';

    $profilFinal = $profil?->profil_risque_final_echelle
        ?? $profil?->profil_risque_final
        ?? 'Non déterminé';

    $tolerance = $profil?->score_tolerance_risque_echelle
        ?? 'Non déterminée';

    $capacite = $profil?->score_capacite_financiere_echelle
        ?? 'Non déterminée';

    $experience = $profil?->score_experience_global_echelle
        ?? 'Non déterminée';

    $connaissance = $profil?->score_connaissance_global_echelle
        ?? 'Non déterminée';


    /*
    |--------------------------------------------------------------------------
    | Complétion KYC
    |--------------------------------------------------------------------------
    */

    $kycCompletion = 0;

    if ($client->kyc) {

        $kycData = collect($client->kyc->getAttributes())
            ->except([
                'id',
                'client_id',
                'created_at',
                'updated_at',
            ]);

        $totalKycFields = $kycData->count();

        $filledKycFields = $kycData->filter(function ($value) {
            return ! is_null($value)
                && $value !== ''
                && $value !== '[]';
        })->count();

        $kycCompletion = $totalKycFields > 0
            ? (int) round(($filledKycFields / $totalKycFields) * 100)
            : 0;
    }


    /*
    |--------------------------------------------------------------------------
    | Téléphone
    |--------------------------------------------------------------------------
    */

    $formatTelephone = function ($telephone) {

        if (! $telephone) {
            return '-';
        }

        $digits = preg_replace('/\D+/', '', $telephone);

        if (strlen($digits) === 10) {
            return trim(chunk_split($digits, 2, ' '));
        }

        return $telephone;
    };


    $cleanScoreLabel = function ($value) {
        if (! $value) {
            return 'Non déterminé';
        }

        return trim(preg_replace('/^[🔴🟠🟢]+\s*/u', '', $value));
    };

    $profilFinal = $cleanScoreLabel($profilFinal);
    $tolerance = $cleanScoreLabel($tolerance);
    $capacite = $cleanScoreLabel($capacite);
    $experience = $cleanScoreLabel($experience);
    $connaissance = $cleanScoreLabel($connaissance);

    $scoreProfil = (float) ($profil?->profil_risque_final ?? 0);
    $scoreConnaissance = (float) ($profil?->score_connaissance_global ?? 0);
    $scoreExperience = (float) ($profil?->score_experience_global ?? 0);
    $scoreCapacite = (float) ($profil?->score_capacite_financiere ?? 0);
    $scoreTolerance = (float) ($profil?->score_tolerance_risque ?? 0);
    $scorePertes = (float) ($profil?->score_capacite_subir_pertes ?? 0);
    $scoreEsg = (float) ($profil?->score_esg ?? 0);
@endphp


@php

    $typesSuggestion = [
        'kyc',
        'patrimoine',
        'profil_investisseur',
    ];

    $analysesSuggestion = $client->analyses()
        ->where('status', 'completed')
        ->whereIn('type', $typesSuggestion)
        ->whereNotNull('completed_at')
        ->where('completed_at', '>=', now()->subYear())
        ->latest('completed_at')
        ->get()
        ->groupBy('type')
        ->map(fn ($items) => $items->first());

    $suggestionDisponible = true;

    foreach ($typesSuggestion as $type) {
        if (! $analysesSuggestion->has($type)) {
            $suggestionDisponible = false;
            break;
        }
    }

    $derniereSuggestion = $client->analyses()
        ->where('type', 'suggestion')
        ->where('status', 'completed')
        ->latest('completed_at')
        ->first();

    /*
    |--------------------------------------------------------------------------
    | Disponibilité de la recommandation patrimoniale
    |--------------------------------------------------------------------------
    |
    | Une recommandation est accessible dès qu'une suggestion
    | de prestations a été effectivement générée.
    |
    */

    $recommandationDisponible = (bool) $derniereSuggestion;

    $derniereRecommandation = $client->analyses()
        ->where('type', 'recommandation')
        ->where('status', 'completed')
        ->latest('completed_at')
        ->first();

    $planActionDisponible = (bool) $derniereRecommandation;

    $dossierStatus = $client->completionStatus();
    $dossierComplet = $dossierStatus['items']['kyc']['done']
        && $dossierStatus['items']['pat']['done']
        && $dossierStatus['items']['inv']['done'];

@endphp

<style>
body > div > nav,
body > div > header{display:none!important}

html,body{
    margin:0!important;
    background:#f3f1ee!important;
}

:root{
    --bg:#f3f1ee;
    --white:#fff;
    --soft:#faf9f7;
    --ink:#171514;
    --muted:#817a75;
    --line:#ded9d4;
    --dark:#242424;
    --pink:#f40087;
    --green:#4d8760;
    --amber:#b77a2d;
    --red:#b94d4d;
}

*{box-sizing:border-box}

.wd-sidebar{
    position:fixed;
    inset:0 auto 0 0;
    width:232px;
    height:100vh;
    background:#242424;
    color:#fff;
    padding:22px 14px;
    display:flex;
    flex-direction:column;
    z-index:1000;
}

.wd-logo{
    padding:0 8px 26px;
    font-size:23px;
    font-weight:800;
    letter-spacing:-.05em;
}
.wd-logo b{color:var(--pink)}
.wd-logo small{
    display:block;
    color:#8d8580;
    font-size:8px;
    letter-spacing:.22em;
    text-transform:uppercase;
    margin-top:5px;
}

.wd-nav-section{
    margin:20px 10px 7px;
    color:rgba(255,255,255,.28);
    font-size:9px;
    font-weight:800;
    letter-spacing:.14em;
    text-transform:uppercase;
}

.wd-nav a{
    display:flex;
    align-items:center;
    gap:11px;
    padding:10px 11px;
    margin:2px 0;
    border-radius:8px;
    color:rgba(255,255,255,.62);
    text-decoration:none;
    font-size:12px;
}
.wd-nav a.active{
    background:rgba(244,0,135,.11);
    color:#fff;
}
.wd-nav i{
    width:19px;
    font-style:normal;
    color:#777;
}
.wd-nav a.active i{color:var(--pink)}

.wd-logout{
    margin-top:auto;
    border-top:1px solid rgba(255,255,255,.08);
    padding-top:14px;
}
.wd-logout button{
    border:0;
    background:none;
    color:#938b86;
    font-size:9px;
    text-transform:uppercase;
    letter-spacing:.12em;
}

.wd-main{
    margin-left:232px;
    min-height:100vh;
    padding-top:68px;
}

.wd-topbar{
    position:fixed;
    top:0;
    left:232px;
    right:0;
    height:68px;
    z-index:900;
    background:rgba(255,255,255,.95);
    border-bottom:1px solid var(--line);
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:0 34px;
}

.wd-crumb{
    font-size:10px;
    font-weight:800;
    letter-spacing:.15em;
    text-transform:uppercase;
    color:#96908b;
}

.wd-user{
    text-align:right;
    font-size:12px;
    font-weight:700;
}
.wd-user small{
    display:block;
    color:#a19a95;
    font-size:9px;
    margin-top:2px;
}

.wd-wrap{
    max-width:1540px;
    margin:auto;
    padding:28px 34px 60px;
}

.wd-hero{
    background:#242424;
    color:white;
    border-radius:14px;
    overflow:hidden;
    border-top:3px solid var(--pink);
    box-shadow:0 10px 30px rgba(27,23,22,.08);
}

.wd-hero-non-conforme{
    background:#E67E22;
}
.wd-hero-conforme{border-top-color:var(--green);}

.wd-hero-main{
    display:grid;
    grid-template-columns:1.4fr .6fr;
    gap:24px;
    padding:28px 30px 25px;
}

.wd-identity{
    display:flex;
    align-items:center;
    gap:18px;
}

.wd-avatar{
    width:64px;
    height:64px;
    border-radius:50%;
    background:rgba(255,255,255,.08);
    border:1px solid rgba(255,255,255,.08);
    display:grid;
    place-items:center;
    font-size:18px;
    font-weight:800;
}

.wd-eyebrow{
    font-size:10px;
    color:var(--pink);
    font-weight:850;
    letter-spacing:.19em;
    text-transform:uppercase;
}

.wd-hero .wd-eyebrow{color:#c9c2be}

.wd-hero-non-conforme .wd-eyebrow{color:#fff}

.wd-hero h1{
    margin:6px 0 4px;
    font-size:34px;
    letter-spacing:-.045em;
}

.wd-hero-meta{
    color:#aaa29e;
    font-size:12px;
}

.wd-hero-non-conforme .wd-hero-meta{
    color:#fff;
}

.wd-actions{
    display:flex;
    justify-content:flex-end;
    gap:9px;
    align-items:flex-start;
}

.wd-btn{
    min-height:40px;
    padding:0 14px;
    border-radius:8px;
    border:1px solid rgba(255,255,255,.14);
    background:rgba(255,255,255,.06);
    color:white;
    display:inline-flex;
    align-items:center;
    text-decoration:none;
    font-size:11px;
    font-weight:700;
}

.wd-btn.primary{
    background:#fff;
    color:#242424;
}

.wd-hero-foot{
    display:grid;
    grid-template-columns:repeat(auto-fit, minmax(180px, 1fr));
    border-top:1px solid rgba(255,255,255,.10);
    background:#242424;
}

.wd-hero-foot>div{
    padding:16px 20px;
    border-right:1px solid rgba(255,255,255,.10);
}

.wd-hero-foot>div:last-child{
    border-right:0;
}

.wd-hero-foot span{
    display:block;
    color:#8f8883;
    font-size:9px;
    text-transform:uppercase;
    letter-spacing:.11em;
    font-weight:800;
}
.wd-hero-foot strong{
    display:block;
    margin-top:6px;
    font-size:13px;
}

.wd-tabs{
    display:flex;
    gap:4px;
    margin:16px 0 24px;
    padding:5px;
    background:#ebe8e5;
    border-radius:10px;
}

.wd-tabs a{
    flex:1;
    text-align:center;
    padding:10px 16px;
    border-radius:7px;
    text-decoration:none;
    color:#77706c;
    font-size:11px;
}

.wd-btn-dark{
    flex:0 0 auto;
    min-height:40px;
    padding:0 20px;
    border:1px solid rgba(255,255,255,.10);
    border-top:2px solid #FF3399;
    border-radius:8px;
    background:#242424;
    color:#ffffff;
    font-size:9px;
    font-weight:800;
    letter-spacing:.10em;
    text-transform:uppercase;
    text-decoration:none;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    cursor:pointer;
    font-family:inherit;
    transition:background .18s ease,border-color .18s ease,transform .18s ease,box-shadow .18s ease;
}
.wd-btn-dark:hover{
    background:#242424;
    border-color:rgba(255,255,255,.10);
    border-top-color:#FF3399;
    color:#ffffff;
    box-shadow:0 0 0 2px rgba(255,51,153,.10);
    transform:translateY(-1px);
}
.wd-tabs-action{margin-left:auto;}

.wd-tabs a.active{
    background:#fff;
    color:#171514;
    font-weight:750;
}

.wd-section{
    margin-top:22px;
}

.wd-section-head{
    display:flex;
    justify-content:space-between;
    align-items:end;
    margin-bottom:11px;
}

.wd-section-head h2{
    margin:4px 0 0;
    font-size:21px;
    letter-spacing:-.03em;
}

.wd-panel{
    background:white;
    border:1px solid var(--line);
    border-radius:12px;
    overflow:hidden;
}

.wd-overview{
    display:grid;
    grid-template-columns:repeat(4,1fr);
}

.wd-kpi{
    padding:22px;
    border-right:1px solid var(--line);
}
.wd-kpi:last-child{border-right:0}

.wd-kpi span{
    display:block;
    color:#918984;
    font-size:9px;
    text-transform:uppercase;
    letter-spacing:.1em;
    font-weight:800;
}

.wd-kpi strong{
    display:block;
    margin-top:8px;
    font-size:24px;
    letter-spacing:-.035em;
}

.wd-patrimoine-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:20px;
}

.wd-assets{
    padding:20px;
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:12px;
}

.wd-asset{
    border:1px solid #ebe7e4;
    border-radius:10px;
    padding:16px;
    background:#fff;
}

.wd-asset span{
    color:#918984;
    font-size:9px;
    text-transform:uppercase;
    letter-spacing:.1em;
    font-weight:800;
}

.wd-asset strong{
    display:block;
    margin-top:7px;
    font-size:18px;
}

.wd-chart{
    padding:22px;
}

.wd-bar-row{
    display:grid;
    grid-template-columns:150px 1fr 110px;
    gap:14px;
    align-items:center;
    margin:14px 0;
}

.wd-bar-label{
    font-size:11px;
    font-weight:650;
}

.wd-bar-track{
    height:8px;
    background:#eeeae7;
    border-radius:20px;
    overflow:hidden;
}

.wd-bar-fill{
    height:100%;
    background:#242424;
    border-radius:20px;
}

.wd-bar-value{
    text-align:right;
    font-size:11px;
    font-weight:700;
}

.wd-profile-grid{
    display:grid;
    grid-template-columns:.85fr 1.15fr;
    gap:20px;
}

.wd-risk{
    padding:26px;
    text-align:center;
}

.wd-risk-score{
    width:150px;
    height:150px;
    margin:auto;
    border-radius:50%;
    border:18px solid #e9e5e2;
    display:grid;
    place-items:center;
    position:relative;
}

.wd-risk-score:after{
    content:"";
    position:absolute;
    inset:-18px;
    border-radius:50%;
    border:18px solid transparent;
    border-top-color:#242424;
    border-right-color:#242424;
    transform:rotate(20deg);
}

.wd-risk-score strong{
    font-size:20px;
    position:relative;
    z-index:2;
}

.wd-risk h3{
    margin:18px 0 5px;
    font-size:22px;
}

.wd-signals{
    padding:20px;
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:12px;
}

.wd-signal{
    padding:15px;
    border:1px solid #ebe7e4;
    border-radius:10px;
}

.wd-signal span{
    font-size:9px;
    color:#918984;
    text-transform:uppercase;
    letter-spacing:.09em;
    font-weight:800;
}

.wd-signal strong{
    display:block;
    margin-top:6px;
    font-size:13px;
}

.wd-empty{
    padding:35px;
    color:#918984;
    text-align:center;
    font-size:12px;
}

@media(max-width:1050px){
    .wd-sidebar{width:72px}
    .wd-nav span,.wd-nav-section,.wd-logo small{display:none}
    .wd-main{margin-left:72px}
    .wd-topbar{left:72px}
    .wd-patrimoine-grid,.wd-profile-grid{grid-template-columns:1fr}
    .wd-overview{grid-template-columns:1fr 1fr}
}



/* ============================================================
   PROFIL INVESTISSEUR
   ============================================================ */

.wd-profile-edit{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-height:34px;
    padding:0 12px;
    border:1px solid var(--line);
    border-radius:7px;
    background:#fff;
    color:#514c48;
    text-decoration:none;
    font-size:10px;
    font-weight:750;
}

.wd-profile-edit:hover{
    border-color:#c8c2bd;
    color:#171514;
}

.wd-profile-new-grid{
    display:grid;
    grid-template-columns:.78fr 1.22fr;
    gap:16px;
    align-items:stretch;
}

.wd-profile-summary{
    background:#242424;
    color:#fff;
    border-radius:13px;
    padding:27px 28px 24px;
    border-top:3px solid var(--pink);
    box-shadow:0 8px 24px rgba(30,26,24,.06);
}

.wd-profile-summary-top{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    gap:20px;
}

.wd-profile-caption{
    color:#a49d98;
    font-size:9px;
    font-weight:800;
    letter-spacing:.14em;
    text-transform:uppercase;
}

.wd-profile-name{
    margin-top:8px;
    font-size:28px;
    line-height:1;
    font-weight:800;
    letter-spacing:-.04em;
}

.wd-profile-score{
    white-space:nowrap;
    display:flex;
    align-items:baseline;
    gap:3px;
}

.wd-profile-score strong{
    font-size:34px;
    line-height:1;
    letter-spacing:-.05em;
}

.wd-profile-score span{
    color:#8e8782;
    font-size:11px;
}

.wd-profile-scale{
    margin-top:30px;
}

.wd-profile-scale-track{
    height:5px;
    overflow:hidden;
    background:rgba(255,255,255,.10);
    border-radius:20px;
}

.wd-profile-scale-value{
    height:100%;
    min-width:4px;
    background:var(--pink);
    border-radius:20px;
}

.wd-profile-scale-labels{
    display:flex;
    justify-content:space-between;
    margin-top:8px;
    color:#837c78;
    font-size:8px;
    text-transform:uppercase;
    letter-spacing:.07em;
}

.wd-profile-reading{
    margin-top:27px;
    padding-top:20px;
    border-top:1px solid rgba(255,255,255,.08);
    color:#aaa39e;
    font-size:11px;
    line-height:1.65;
}

.wd-profile-date{
    margin-top:17px;
    color:#77716d;
    font-size:9px;
    text-transform:uppercase;
    letter-spacing:.08em;
}

.wd-profile-date strong{
    display:block;
    margin-top:4px;
    color:#c9c2be;
    font-size:10px;
    text-transform:none;
    letter-spacing:0;
}

.wd-profile-metrics{
    display:grid;
    grid-template-columns:repeat(2,minmax(0,1fr));
    gap:10px;
}

.wd-profile-metric{
    min-width:0;
    background:#fff;
    border:1px solid var(--line);
    border-radius:11px;
    padding:17px 18px 15px;
}

.wd-profile-metric-head{
    display:flex;
    justify-content:space-between;
    gap:15px;
    align-items:center;
}

.wd-profile-metric-head span{
    color:#79726d;
    font-size:9px;
    font-weight:800;
    text-transform:uppercase;
    letter-spacing:.09em;
}

.wd-profile-metric-head strong{
    color:#242424;
    font-size:17px;
    letter-spacing:-.025em;
}

.wd-profile-meter{
    height:3px;
    margin:15px 0 11px;
    overflow:hidden;
    background:#eeeae7;
    border-radius:20px;
}

.wd-profile-meter i{
    display:block;
    height:100%;
    background:#242424;
    border-radius:20px;
}

.wd-profile-metric small{
    display:block;
    color:#45403d;
    font-size:11px;
    font-weight:650;
    line-height:1.4;
}

@media(max-width:1050px){
    .wd-profile-new-grid{
        grid-template-columns:1fr;
    }
}

@media(max-width:700px){
    .wd-profile-metrics{
        grid-template-columns:1fr;
    }

    .wd-profile-summary-top{
        flex-direction:column;
    }
}



/* ============================================================
   COMPATIBILITÉ DES PLACEMENTS
   ============================================================ */

.wd-compatibility-section{
    margin-top:30px;
}

.wd-compatibility-panel{
    background:#fff;
    border:1px solid var(--line);
    border-radius:12px;
    overflow:hidden;
}

.wd-compatibility-head{
    display:grid;
    grid-template-columns:.9fr 1.1fr;
    background:#faf9f7;
    border-bottom:1px solid var(--line);
}

.wd-compatibility-head>div{
    padding:13px 20px;
}

.wd-compatibility-head span{
    color:#918984;
    font-size:9px;
    font-weight:800;
    text-transform:uppercase;
    letter-spacing:.10em;
}

.wd-compatibility-row{
    position:relative;
    display:grid;
    grid-template-columns:.9fr 1.1fr;
    border-bottom:1px solid #eeeae7;
}

.wd-compatibility-row:last-child{
    border-bottom:0;
}

.wd-compatibility-row::before{
    content:"";
    position:absolute;
    top:14px;
    bottom:14px;
    left:0;
    width:3px;
    border-radius:0 3px 3px 0;
    background:#d7d1cc;
}

.wd-compatibility-compatible::before{
    background:#7d9c88;
}

.wd-compatibility-vigilance::before{
    background:#c79a62;
}

.wd-compatibility-non_adapte::before{
    background:#9d5f66;
}

.wd-compatibility-product,
.wd-compatibility-reading{
    padding:18px 20px;
}

.wd-compatibility-reading{
    border-left:1px solid #eeeae7;
}

.wd-compatibility-title{
    color:#242424;
    font-size:13px;
    font-weight:750;
}

.wd-compatibility-detail{
    margin-top:5px;
    color:#918984;
    font-size:10px;
    line-height:1.5;
}

.wd-compatibility-status{
    color:#242424;
    font-size:12px;
    font-weight:800;
}

.wd-compatibility-compatible .wd-compatibility-status{
    color:#556e5f;
}

.wd-compatibility-vigilance .wd-compatibility-status{
    color:#8c642f;
}

.wd-compatibility-non_adapte .wd-compatibility-status{
    color:#844950;
}

.wd-compatibility-reason{
    margin-top:5px;
    color:#77706c;
    font-size:10px;
    line-height:1.55;
}

.wd-compatibility-note{
    margin-top:10px;
    padding:14px 16px;
    border:1px solid #e6e1dc;
    border-radius:10px;
    background:#faf9f7;
    color:#837c77;
    font-size:9px;
    line-height:1.6;
}

@media(max-width:800px){

    .wd-compatibility-head{
        display:none;
    }

    .wd-compatibility-row{
        grid-template-columns:1fr;
    }

    .wd-compatibility-reading{
        border-left:0;
        border-top:1px solid #eeeae7;
        padding-top:14px;
    }
}



/* ============================================================
   FILTRES COMPATIBILITÉ
   ============================================================ */

.wd-compatibility-filters{
    display:flex;
    align-items:center;
    flex-wrap:wrap;
    gap:5px;
    margin-bottom:10px;
}

.wd-compatibility-filters button{
    appearance:none;
    border:1px solid var(--line);
    background:#fff;
    color:#77706c;
    border-radius:7px;
    padding:7px 11px;
    font-family:inherit;
    font-size:9px;
    font-weight:750;
    line-height:1;
    cursor:pointer;
    transition:
        background .15s ease,
        color .15s ease,
        border-color .15s ease;
}

.wd-compatibility-filters button:hover{
    border-color:#c8c2bd;
    color:#242424;
}

.wd-compatibility-filters button.active{
    background:#242424;
    border-color:#242424;
    color:#fff;
}



/* ============================================================
   PATRIMOINE PREMIUM
   ============================================================ */

.wd-patrimoine-premium{
    margin-top:28px;
}

.wd-patrimoine-kpis{
    display:grid;
    grid-template-columns:repeat(4,minmax(0,1fr));
    background:#fff;
    border:1px solid var(--line);
    border-radius:12px;
    overflow:hidden;
}

.wd-patrimoine-kpi{
    padding:20px 22px;
    border-right:1px solid var(--line);
}

.wd-patrimoine-kpi:last-child{
    border-right:0;
}

.wd-patrimoine-kpi span{
    display:block;
    color:#918984;
    font-size:9px;
    font-weight:800;
    text-transform:uppercase;
    letter-spacing:.1em;
}

.wd-patrimoine-kpi strong{
    display:block;
    margin-top:7px;
    color:#242424;
    font-size:24px;
    letter-spacing:-.035em;
}

.wd-patrimoine-kpi small{
    display:block;
    margin-top:5px;
    color:#9b948f;
    font-size:9px;
}

.wd-patrimoine-main-grid{
    display:grid;
    grid-template-columns:1fr 1fr 1fr;
    gap:14px;
    margin-top:14px;
}

.wd-patrimoine-card{
    padding:20px;
}

.wd-patrimoine-card-head{
    display:flex;
    align-items:center;
    justify-content:space-between;
    margin-bottom:18px;
}

.wd-patrimoine-card-head h3{
    margin:0;
    color:#242424;
    font-size:13px;
    font-weight:800;
    letter-spacing:-.01em;
}

.wd-patrimoine-card-head h3::after{
    content:"";
    display:block;
    width:28px;
    height:2px;
    margin-top:8px;
    background:#80A29A;
    border-radius:10px;
}

.wd-patrimoine-bars{
    display:flex;
    flex-direction:column;
    gap:18px;
}

.wd-patrimoine-bar-top{
    display:flex;
    justify-content:space-between;
    gap:15px;
    align-items:center;
}

.wd-patrimoine-bar-top span{
    color:#45403d;
    font-size:11px;
    font-weight:650;
}

.wd-patrimoine-bar-top strong{
    color:#242424;
    font-size:11px;
}

.wd-patrimoine-bar-track{
    height:4px;
    margin-top:9px;
    overflow:hidden;
    background:#eeeae7;
    border-radius:20px;
}

.wd-patrimoine-bar-track i{
    display:block;
    height:100%;
    background:#80A29A;
    border-radius:20px;
}

.wd-patrimoine-bar-track i.is-passif{
    background:#d49a7f;
}

.wd-patrimoine-bar-item small{
    display:block;
    margin-top:5px;
    color:#aaa29d;
    font-size:8px;
    text-align:right;
}

.wd-patrimoine-structure{
    display:grid;
    grid-template-columns:170px 1fr;
    gap:24px;
    align-items:center;
}

.wd-patrimoine-donut{
    width:160px;
    height:160px;
    border-radius:50%;
    background:
        conic-gradient(
            #80A29A 0 calc(var(--fin) * 1%),
            #b9c3bd calc(var(--fin) * 1%) calc((var(--fin) + var(--nonfin)) * 1%),
            #d49a7f calc((var(--fin) + var(--nonfin)) * 1%) 100%
        );
    position:relative;
}

.wd-patrimoine-donut::after{
    content:"";
    position:absolute;
    inset:26px;
    border-radius:50%;
    background:#fff;
}

.wd-patrimoine-donut-center{
    position:absolute;
    inset:0;
    display:grid;
    place-content:center;
    text-align:center;
    z-index:2;
}

.wd-patrimoine-donut-center strong{
    font-size:13px;
    color:#242424;
}

.wd-patrimoine-donut-center span{
    margin-top:3px;
    color:#9a938e;
    font-size:8px;
}

.wd-patrimoine-legend{
    display:flex;
    flex-direction:column;
    gap:14px;
}

.wd-patrimoine-legend>div{
    display:flex;
    gap:9px;
    align-items:flex-start;
}

.wd-patrimoine-legend .dot{
    width:8px;
    height:8px;
    margin-top:4px;
    border-radius:50%;
    flex:0 0 auto;
}

.dot-fin{background:#80A29A}
.dot-nonfin{background:#b9c3bd}
.dot-passif{background:#d49a7f}

.wd-patrimoine-legend p{
    margin:0;
}

.wd-patrimoine-legend strong{
    display:block;
    color:#45403d;
    font-size:10px;
}

.wd-patrimoine-legend small{
    display:block;
    margin-top:3px;
    color:#99918c;
    font-size:9px;
}

.wd-flux-chart{
    height:220px;
    display:flex;
    justify-content:center;
    align-items:flex-end;
    gap:28px;
    padding:12px 8px 0;
}

.wd-flux-item{
    height:100%;
    width:30%;
    display:flex;
    flex-direction:column;
    justify-content:flex-end;
    align-items:center;
}

.wd-flux-value{
    margin-bottom:7px;
    color:#242424;
    font-size:10px;
    font-weight:750;
}

.wd-flux-column{
    width:54px;
    height:150px;
    display:flex;
    align-items:flex-end;
}

.wd-flux-column i{
    display:block;
    width:100%;
    min-height:3px;
    background:#80A29A;
    border-radius:5px 5px 0 0;
}

.wd-flux-column i.is-charge{
    background:#d49a7f;
}

.wd-flux-column i.is-solde{
    background:#4f7062;
}

.wd-flux-item span{
    margin-top:8px;
    color:#817a75;
    font-size:9px;
}

.wd-patrimoine-detail{
    margin-top:14px;
    padding:20px;
}

.wd-patrimoine-table-wrap{
    overflow-x:auto;
}

.wd-patrimoine-table{
    width:100%;
    border-collapse:collapse;
    min-width:900px;
}

.wd-patrimoine-table th{
    padding:11px 10px;
    border-bottom:1px solid var(--line);
    color:#918984;
    font-size:8px;
    font-weight:800;
    text-align:left;
    text-transform:uppercase;
    letter-spacing:.09em;
}

.wd-patrimoine-table td{
    padding:12px 10px;
    border-bottom:1px solid #f0ece9;
    color:#514c48;
    font-size:10px;
}

.wd-patrimoine-table tr:last-child td{
    border-bottom:0;
}

.wd-patrimoine-category{
    display:inline-flex;
    align-items:center;
    min-height:24px;
    padding:0 8px;
    border-radius:6px;
    background:#eef3f0;
    color:#526d60;
    font-size:8px;
    font-weight:750;
}

.wd-patrimoine-category.is-negative{
    background:#f7eee9;
    color:#9b6048;
}

.wd-patrimoine-amount{
    color:#242424 !important;
    font-weight:750;
    white-space:nowrap;
}

.wd-patrimoine-amount.is-negative{
    color:#b56f54 !important;
}

.wd-patrimoine-row-share{
    display:grid;
    grid-template-columns:1fr 42px;
    gap:8px;
    align-items:center;
}

.wd-patrimoine-row-track{
    height:3px;
    overflow:hidden;
    background:#eeeae7;
    border-radius:20px;
}

.wd-patrimoine-row-track i{
    display:block;
    height:100%;
    background:#80A29A;
    border-radius:20px;
}

.wd-patrimoine-row-track i.is-negative{
    background:#d49a7f;
}

.wd-patrimoine-row-share span{
    color:#99918c;
    font-size:8px;
    text-align:right;
}

@media(max-width:1100px){
    .wd-patrimoine-kpis{
        grid-template-columns:1fr 1fr;
    }

    .wd-patrimoine-kpi:nth-child(2){
        border-right:0;
    }

    .wd-patrimoine-kpi:nth-child(-n+2){
        border-bottom:1px solid var(--line);
    }

    .wd-patrimoine-main-grid{
        grid-template-columns:1fr;
    }
}

@media(max-width:650px){
    .wd-patrimoine-kpis{
        grid-template-columns:1fr;
    }

    .wd-patrimoine-kpi{
        border-right:0;
        border-bottom:1px solid var(--line);
    }

    .wd-patrimoine-kpi:last-child{
        border-bottom:0;
    }

    .wd-patrimoine-structure{
        grid-template-columns:1fr;
        justify-items:center;
    }
}



/* ============================================================
   STRUCTURE PATRIMONIALE DÉTAILLÉE
   ============================================================ */

.wd-patrimoine-main-grid{
    grid-template-columns:.88fr 1.45fr .88fr;
}

.wd-patrimoine-structure-card{
    min-width:0;
}

.wd-asset-donuts{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:24px;
}

.wd-asset-donut-block{
    min-width:0;
}

.wd-asset-donut-block + .wd-asset-donut-block{
    border-left:1px solid #eeeae7;
    padding-left:24px;
}

.wd-asset-donut-title{
    margin-bottom:16px;
    color:#242424;
    font-size:11px;
    font-weight:800;
    text-align:center;
}

.wd-asset-donut-layout{
    display:grid;
    grid-template-columns:132px minmax(0,1fr);
    gap:17px;
    align-items:center;
}

.wd-asset-donut{
    position:relative;
    width:132px;
    height:132px;
    border-radius:50%;
}

.wd-asset-donut::after{
    content:"";
    position:absolute;
    inset:25px;
    border-radius:50%;
    background:#fff;
}

.wd-asset-donut-hole{
    position:absolute;
    inset:0;
    z-index:2;
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    text-align:center;
}

.wd-asset-donut-hole strong{
    max-width:90px;
    color:#242424;
    font-size:11px;
    line-height:1.2;
}

.wd-asset-donut-hole span{
    margin-top:3px;
    color:#a09994;
    font-size:7px;
    text-transform:uppercase;
    letter-spacing:.08em;
}

.wd-asset-donut-legend{
    display:flex;
    flex-direction:column;
    gap:10px;
    max-height:180px;
    overflow-y:auto;
    padding-right:4px;
}

.wd-asset-legend-row{
    display:grid;
    grid-template-columns:8px minmax(0,1fr);
    gap:8px;
    align-items:start;
}

.wd-asset-legend-dot{
    width:7px;
    height:7px;
    margin-top:3px;
    border-radius:50%;
}

.wd-asset-legend-row strong{
    display:block;
    color:#4b4642;
    font-size:9px;
    line-height:1.25;
    font-weight:700;
}

.wd-asset-legend-row small{
    display:block;
    margin-top:2px;
    color:#9b948f;
    font-size:8px;
    line-height:1.3;
}

.wd-asset-legend-empty{
    color:#9b948f;
    font-size:9px;
}

@media(max-width:1250px){

    .wd-patrimoine-main-grid{
        grid-template-columns:1fr;
    }

    .wd-asset-donut-layout{
        grid-template-columns:150px 1fr;
    }

}

@media(max-width:750px){

    .wd-asset-donuts{
        grid-template-columns:1fr;
    }

    .wd-asset-donut-block + .wd-asset-donut-block{
        border-left:0;
        border-top:1px solid #eeeae7;
        padding-left:0;
        padding-top:22px;
    }

}



/* ============================================================
   FILTRES DÉTAIL PATRIMOINE
   ============================================================ */

.wd-patrimoine-detail-head{
    align-items:center;
    gap:20px;
}

.wd-patrimoine-filters{
    display:inline-flex;
    align-items:center;
    gap:3px;
    padding:4px;
    margin-left:auto;
    background:#f1efed;
    border-radius:8px;
}

.wd-patrimoine-filters button{
    appearance:none;
    border:0;
    background:transparent;
    padding:7px 11px;
    border-radius:6px;
    color:#817a75;
    font-family:inherit;
    font-size:9px;
    font-weight:650;
    line-height:1;
    cursor:pointer;
    white-space:nowrap;
    transition:
        background .15s ease,
        color .15s ease,
        box-shadow .15s ease;
}

.wd-patrimoine-filters button:hover{
    color:#242424;
}

.wd-patrimoine-filters button.active{
    background:#fff;
    color:#242424;
    font-weight:800;
    box-shadow:0 1px 3px rgba(36,36,36,.08);
}

.wd-patrimoine-table tr.wd-filter-hidden{
    display:none;
}

@media(max-width:750px){

    .wd-patrimoine-detail-head{
        align-items:flex-start;
        flex-direction:column;
    }

    .wd-patrimoine-filters{
        margin-left:0;
        max-width:100%;
        overflow-x:auto;
    }

}



/* ============================================================
   TOOLTIPS GRAPHIQUES PATRIMOINE
   ============================================================ */

.wd-asset-donut[data-donut-tooltip]{
    cursor:pointer;
}

.wd-flux-item[data-flux-tooltip] .wd-flux-column i{
    cursor:pointer;
    transition:
        opacity .15s ease,
        transform .15s ease;
    transform-origin:bottom;
}

.wd-flux-item[data-flux-tooltip]:hover .wd-flux-column i{
    opacity:.88;
    transform:scaleX(1.04);
}

.wd-chart-tooltip{
    position:fixed;
    z-index:99999;
    min-width:150px;
    max-width:250px;
    padding:10px 12px;
    border-radius:8px;
    background:#242424;
    color:#fff;
    box-shadow:0 8px 25px rgba(0,0,0,.18);
    pointer-events:none;
    opacity:0;
    visibility:hidden;
    transform:translateY(4px);
    transition:
        opacity .1s ease,
        transform .1s ease,
        visibility .1s ease;
}

.wd-chart-tooltip.is-visible{
    opacity:1;
    visibility:visible;
    transform:translateY(0);
}

.wd-chart-tooltip-title{
    font-size:10px;
    line-height:1.35;
    font-weight:750;
}

.wd-chart-tooltip-value{
    margin-top:5px;
    font-size:13px;
    font-weight:800;
    letter-spacing:-.02em;
}

.wd-chart-tooltip-meta{
    margin-top:3px;
    color:#b9b2ad;
    font-size:9px;
}



/* ============================================================
   DEV : SIMULATION DE RÔLE
   ============================================================ */

.wd-view-role{
    margin:0 14px 12px;
    padding:12px;
    border-top:1px solid rgba(255,255,255,.08);
}

.wd-view-role-label{
    margin-bottom:7px;
    color:#817a75;
    font-size:8px;
    font-weight:800;
    text-transform:uppercase;
    letter-spacing:.1em;
}

.wd-view-role select{
    width:100%;
    min-height:34px;
    padding:0 9px;
    border:1px solid rgba(255,255,255,.12);
    border-radius:7px;
    background:#2d2d2d;
    color:#fff;
    font-family:inherit;
    font-size:10px;
}


    
.wd-suggestion-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 24px;

    margin: 40px 0 24px;
    padding: 18px 22px;

    background: #ffffff;
    border: 1px solid #E0E7E3;
    border-radius: 14px;

    box-shadow: 0 4px 14px rgba(35, 52, 47, .045);
}

.wd-suggestion-copy {
    min-width: 0;
}

.wd-suggestion-kicker {
    margin: 0 0 5px;

    color: #80A29A;

    font-size: 9px;
    font-weight: 800;
    letter-spacing: .14em;
    text-transform: uppercase;
}

.wd-suggestion-title {
    margin: 0;

    color: #293A35;

    font-size: 14px;
    line-height: 1.35;
    font-weight: 700;
}

.wd-suggestion-description {
    margin: 5px 0 0;

    color: #7A8581;

    font-size: 14px;
    line-height: 1.65;
}

.wd-suggestion-button {
    flex: 0 0 auto;

    min-width: 126px;
    height: 40px;

    padding: 0 20px;

    border: 1px solid rgba(255,255,255,.10);
    border-top: 2px solid #FF3399;
    border-radius: 8px;

    background: #242424;
    color: #ffffff;

    font-size: 9px;
    font-weight: 800;
    letter-spacing: .11em;
    text-transform: uppercase;

    cursor: pointer;

    transition:
        background .18s ease,
        border-color .18s ease,
        transform .18s ease,
        box-shadow .18s ease;
}

.wd-suggestion-button:hover {
    background: #242424;
    border-color: rgba(255,255,255,.10);
    border-top-color: #FF3399;
    box-shadow: 0 0 0 2px rgba(255, 51, 153, .10);
    transform: translateY(-1px);
}

.wd-suggestion-button:active {
    transform: translateY(0);
}

.wd-suggestion-button:disabled {
    background: #E2E5E4;
    border-color: #D2D8D5;
    color: #929A97;
    cursor: not-allowed;
    box-shadow: none;
    transform: none;
}

.wd-suggestion-disabled {
    margin-top: 6px;

    color: #9AA39F;

    font-size: 8.5px;
    line-height: 1.4;
}

.wd-suggestion-result {
    margin: 0 0 26px;
}

.wd-suggestion-result-head {
    margin-bottom: 14px;
}

.wd-suggestion-result-title {
    margin: 0;

    color: #293A35;

    font-size: 18px;
    font-weight: 700;
}

.wd-suggestion-result-date {
    margin: 4px 0 0;

    color: #9AA39F;

    font-size: 9px;
}

.wd-suggestion-list {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 14px;
}

.wd-suggestion-item {
    padding: 18px 20px;

    background: #ffffff;

    border: 1px solid #E0E7E3;
    border-radius: 13px;

    box-shadow: 0 3px 12px rgba(35, 52, 47, .035);
}

.wd-suggestion-item-number {
    display: block;
    margin-bottom: 8px;

    color: #80A29A;

    font-size: 9px;
    font-weight: 800;
    letter-spacing: .14em;
}

.wd-suggestion-item-title {
    margin: 0 0 8px;

    color: #33433E;

    font-size: 12px;
    line-height: 1.4;
    font-weight: 700;
}

.wd-suggestion-item-text {
    margin: 0;

    color: #78847F;

    font-size: 14px;
    line-height: 1.65;
}

.wd-suggestion-actions {
    margin-top: 14px;
    padding-top: 12px;

    border-top: 1px solid #EDF1EF;
}

.wd-suggestion-actions-label {
    margin-bottom: 6px;

    color: #56635F;

    font-size: 8px;
    font-weight: 800;
    letter-spacing: .1em;
    text-transform: uppercase;
}

.wd-suggestion-actions ul {
    margin: 0;
    padding-left: 16px;

    color: #78847F;

    font-size: 12px;
    line-height: 1.6;
}

@media (max-width: 800px) {

    .wd-suggestion-bar {
        align-items: stretch;
        flex-direction: column;
    }

    .wd-suggestion-button {
        width: 100%;
    }

    .wd-suggestion-list {
        grid-template-columns: 1fr;
    }

}


    
/* ============================================================
   RECOMMANDATION PATRIMONIALE
   ============================================================ */

.wd-recommandation-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 24px;

    margin: 18px 0 0;
    padding: 18px 22px;

    background: #ffffff;
    border: 1px solid #E0E7E3;
    border-radius: 14px;

    box-shadow: 0 4px 14px rgba(35, 52, 47, .045);
}

.wd-recommandation-copy {
    min-width: 0;
}

.wd-recommandation-kicker {
    margin: 0 0 5px;

    color: #80A29A;

    font-size: 9px;
    font-weight: 800;
    letter-spacing: .14em;
    text-transform: uppercase;
}

.wd-recommandation-title {
    margin: 0;

    color: #293A35;

    font-size: 14px;
    line-height: 1.35;
    font-weight: 700;
}

.wd-recommandation-description {
    margin: 6px 0 0;

    color: #7A8581;

    font-size: 14px;
    line-height: 1.65;
}

.wd-recommandation-button {
    flex: 0 0 auto;

    min-width: 190px;
    height: 40px;

    padding: 0 20px;

    border: 1px solid rgba(255,255,255,.10);
    border-top: 2px solid #FF3399;
    border-radius: 8px;

    background: #242424;
    color: #ffffff;

    font-size: 9px;
    font-weight: 800;
    letter-spacing: .10em;
    text-transform: uppercase;

    text-decoration: none;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    transition:
        background .18s ease,
        border-color .18s ease,
        transform .18s ease,
        box-shadow .18s ease;
}

.wd-recommandation-button:hover {
    background: #242424;
    border-color: rgba(255,255,255,.10);
    border-top-color: #FF3399;

    color: #ffffff;

    box-shadow: 0 0 0 2px rgba(255,51,153,.10);

    transform: translateY(-1px);
}

.wd-recommandation-button-disabled {
    flex: 0 0 auto;

    min-width: 190px;
    height: 40px;

    padding: 0 20px;

    border: 1px solid #D2D8D5;
    border-top: 2px solid #C8CFCC;
    border-radius: 8px;

    background: #E2E5E4;
    color: #929A97;

    font-size: 9px;
    font-weight: 800;
    letter-spacing: .10em;
    text-transform: uppercase;

    cursor: not-allowed;

    display: inline-flex;
    align-items: center;
    justify-content: center;
}

@media (max-width: 800px) {

    .wd-recommandation-bar {
        align-items: stretch;
        flex-direction: column;
    }

    .wd-recommandation-button,
    .wd-recommandation-button-disabled {
        width: 100%;
    }

}

</style>

<style>

/* ============================================================
   AIDE À LA DÉCISION - ANALYSE PREMIUM
   ============================================================ */

.wd-analysis-content {
    padding-bottom: 60px;
}

.wd-analysis-intro {
    margin-bottom: 22px;
}

.wd-analysis-kicker {
    margin: 0 0 7px;
    color: #80A29A;
    font-size: 10px;
    line-height: 1;
    font-weight: 800;
    letter-spacing: .16em;
    text-transform: uppercase;
}

.wd-analysis-title {
    margin: 0;
    color: #252D2A;
    font-size: 27px;
    line-height: 1.18;
    font-weight: 700;
    letter-spacing: -.035em;
}

.wd-analysis-subtitle {
    max-width: 900px;
    margin: 8px 0 0;
    color: #7A8581;
    font-size: 12px;
    line-height: 1.6;
}


/* ============================================================
   GRILLE DES 3 MODULES
   ============================================================ */

.wd-analysis-grid {
    width: 100%;
    min-width: 0;
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 16px;
    align-items: stretch;
}

.wd-analysis-card {
    min-width: 0;
    overflow: hidden;
    display: flex;
    flex-direction: column;

    background: #fff;
    border: 1px solid #E1E7E4;
    border-radius: 15px;

    box-shadow:
        0 2px 8px rgba(36,51,47,.035),
        0 8px 22px rgba(36,51,47,.025);
}

.wd-analysis-card-head {
    padding: 19px 20px 17px;

    border-bottom: 1px solid #EDF1EF;

    background:
        linear-gradient(
            180deg,
            #FFFFFF 0%,
            #FAFCFB 100%
        );
}

.wd-analysis-number {
    display: block;
    margin-bottom: 8px;

    color: #80A29A;
    font-size: 9px;
    line-height: 1;
    font-weight: 800;
    letter-spacing: .15em;
}

.wd-analysis-card-title {
    margin: 0;

    color: #293A35;
    font-size: 14px;
    line-height: 1.35;
    font-weight: 700;
    letter-spacing: -.01em;
}

.wd-analysis-card-body {
    flex: 1;
    padding: 19px 20px 22px;
}


/* ============================================================
   SECTIONS
   ============================================================ */

.wd-analysis-section-title {
    display: flex;
    align-items: center;
    gap: 8px;

    margin-bottom: 15px;

    color: #56635F;
    font-size: 9px;
    line-height: 1;
    font-weight: 800;
    letter-spacing: .12em;
    text-transform: uppercase;
}

.wd-analysis-section-title::before {
    content: "";

    width: 5px;
    height: 5px;

    flex: 0 0 5px;

    border-radius: 50%;

    background: #80A29A;
}

.wd-analysis-section.attention
.wd-analysis-section-title::before {
    background: #B59A72;
}

.wd-analysis-section + .wd-analysis-section {
    margin-top: 23px;
    padding-top: 20px;

    border-top: 1px solid #EDF1EF;
}


/* ============================================================
   POINTS
   ============================================================ */

.wd-analysis-point {
    position: relative;
    padding-left: 29px;
}

.wd-analysis-point + .wd-analysis-point {
    margin-top: 15px;
}

.wd-analysis-point-number {
    position: absolute;
    top: 0;
    left: 0;

    width: 19px;
    height: 19px;

    display: flex;
    align-items: center;
    justify-content: center;

    box-sizing: border-box;

    border: 1px solid #DCE6E2;
    border-radius: 50%;

    color: #80A29A;

    font-size: 8px;
    line-height: 1;
    font-weight: 800;
}

.wd-analysis-point-title {
    margin: 0 0 4px;

    color: #33433E;

    font-size: 10.5px;
    line-height: 1.4;
    font-weight: 700;
}

.wd-analysis-point-text {
    margin: 0;

    color: #78847F;

    font-size: 10px;
    line-height: 1.55;
}

.wd-analysis-empty {
    margin: 0;
    padding: 7px 0;

    color: #9AA39F;

    font-size: 10px;
    font-style: italic;
}


/* ============================================================
   RESPONSIVE
   ============================================================ */

@media (max-width: 1200px) {

    .wd-analysis-grid {
        grid-template-columns: 1fr;
    }

}

@media (max-width: 700px) {

    .wd-analysis-title {
        font-size: 23px;
    }

    .wd-analysis-grid {
        grid-template-columns: 1fr;
    }

}

</style>
<div class="wd-wrap">

<section class="wd-hero {{ $dossierStatus['a_jour'] ? 'wd-hero-conforme' : 'wd-hero-non-conforme' }}">

<div class="wd-hero-main">

<div class="wd-identity">

<div class="wd-avatar">{{ $initiales }}</div>

<div>
<div class="wd-eyebrow">Client · portefeuille privé</div>
<h1>{{ $client->prenom }} {{ $client->nom }}</h1>
<div class="wd-hero-meta">
Dossier client · suivi patrimonial
</div>
</div>

</div>

<div class="wd-actions">

<a class="wd-btn"
href="{{ route('tenant.clients.edit', $client) }}">
Modifier
</a>



</div>

</div>

<div class="wd-hero-foot">

<div>
<span>Téléphone</span>
<strong>{{ $formatTelephone($client->telephone_mobile) }}</strong>
</div>

<div>
<span>Email</span>
<strong>{{ $client->email ?: '-' }}</strong>
</div>

@if($viewRole === 'courtier')

<div>
<span>Conseiller</span>
<strong>{{ $client->conseiller?->name ?: '-' }}</strong>
</div>

@elseif($viewRole === 'conseiller' && $client->apporteur)

<div>
<span>Apporteur</span>
<strong>{{ $client->apporteur->name }}</strong>
</div>

@endif

<div>
<span>Dernière mise à jour</span>
<strong>{{ $client->updated_at?->translatedFormat('d F Y') }}</strong>
</div>

</div>

</section>

<nav class="wd-tabs">

<a href="{{ route('tenant.dashboard') }}">
Tableau de bord
</a>

<a href="{{ route('tenant.clients.show', $client) }}">
Profil
</a>

<a href="{{ route('tenant.clients.aide-decision', $client) }}"
class="active">
Analyse
</a>

<a href="{{ route('tenant.clients.mission', $client) }}">
Mission
</a>

<a href="{{ route('tenant.clients.contrats-clients', $client) }}">
Contrat
</a>

<a href="{{ route('tenant.clients.conformites-clients', $client) }}">
Archives
</a>

<button type="button" class="wd-btn-dark wd-tabs-action" data-dossier-trigger>
{{ $dossierComplet ? 'Modifier les formulaires' : 'Compléter les formulaires' }}
</button>

</nav>

<div class="wd-newaccount-overlay" data-dossier-modal hidden>
<div class="wd-newaccount-modal">
<div class="wd-newaccount-head">
<div>
<div class="wd-eyebrow">Dossier client</div>
<h3>{{ $dossierComplet ? 'Modifier les formulaires' : 'Compléter les formulaires' }}</h3>
</div>
<button type="button" class="wd-newaccount-close" data-dossier-close aria-label="Fermer">&times;</button>
</div>
<div class="wd-newaccount-choices">
<a href="{{ route('tenant.clients.kyc.edit', $client) }}" class="wd-newaccount-choice">
<span class="wd-newaccount-choice-title">KYC</span>
<span class="wd-newaccount-choice-desc">Recueil de connaissance client.</span>
</a>
<a href="{{ route('tenant.clients.patrimoine.edit', $client) }}" class="wd-newaccount-choice">
<span class="wd-newaccount-choice-title">Patrimoine</span>
<span class="wd-newaccount-choice-desc">Analyse patrimoniale du client.</span>
</a>
<a href="{{ route('tenant.clients.profil.edit', $client) }}" class="wd-newaccount-choice">
<span class="wd-newaccount-choice-title">Profil investisseur</span>
<span class="wd-newaccount-choice-desc">Profil de risque et objectifs.</span>
</a>
</div>
</div>
</div>


<div style="display:flex;gap:12px;justify-content:flex-end;margin:22px 0 0;">
@if($recommandationDisponible)
<a href="{{ route('tenant.clients.recommandation-patrimoniale', $client) }}" class="wd-recommandation-button">
Recommandation
</a>
@else
<span class="wd-recommandation-button-disabled">
Recommandation
</span>
@endif
@if($planActionDisponible)
<a href="{{ route('tenant.clients.plan-action', $client) }}" class="wd-recommandation-button">
Plan d'action
</a>
@else
<span class="wd-recommandation-button-disabled">
Plan d'action
</span>
@endif
</div>

<section class="wd-section wd-analysis-content">

    <div class="wd-analysis-intro">

        <p class="wd-analysis-kicker">
            Aide à la décision
        </p>

        <h2 class="wd-analysis-title">
            Analyse du dossier
        </h2>

        <p class="wd-analysis-subtitle">
            Lecture croisée des informations recueillies afin
            d’identifier les principaux points forts et points
            d’attention de la situation patrimoniale.
        </p>

    </div>




    
    <div class="wd-analysis-grid">

        @foreach([
            'kyc' => [
                'numero' => '01',
                'titre' => 'Recueil d’informations client',
            ],
            'patrimoine' => [
                'numero' => '02',
                'titre' => 'Patrimoine',
            ],
            'profil_investisseur' => [
                'numero' => '03',
                'titre' => 'Profil d’investisseur',
            ],
        ] as $type => $module)

            @php

                $analyse = $analysesDossier->get($type);

                $resultat = $analyse?->result_json ?? [];

                $pointsForts = is_array($resultat['points_forts'] ?? null)
                    ? $resultat['points_forts']
                    : [];

                $pointsAttention = is_array($resultat['points_attention'] ?? null)
                    ? $resultat['points_attention']
                    : [];

            @endphp


            <article class="wd-analysis-card">

                <header class="wd-analysis-card-head">

                    <span class="wd-analysis-number">
                        {{ $module['numero'] }}
                    </span>

                    <h3 class="wd-analysis-card-title">
                        {{ $module['titre'] }}
                    </h3>

                </header>


                <div class="wd-analysis-card-body">

                    <section class="wd-analysis-section">

                        <div class="wd-analysis-section-title">
                            Points forts
                        </div>

                        @forelse($pointsForts as $index => $point)

                            <div class="wd-analysis-point">

                                <span class="wd-analysis-point-number">
                                    {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                                </span>

                                <p class="wd-analysis-point-title">
                                    {{ $point['titre'] ?? '' }}
                                </p>

                                <p class="wd-analysis-point-text">
                                    {{ $point['analyse'] ?? '' }}
                                </p>

                            </div>

                        @empty

                            <p class="wd-analysis-empty">
                                Analyse indisponible pour le moment.
                            </p>

                        @endforelse

                    </section>


                    <section class="wd-analysis-section attention">

                        <div class="wd-analysis-section-title">
                            Points d’attention
                        </div>

                        @forelse($pointsAttention as $index => $point)

                            <div class="wd-analysis-point">

                                <span class="wd-analysis-point-number">
                                    {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                                </span>

                                <p class="wd-analysis-point-title">
                                    {{ $point['titre'] ?? '' }}
                                </p>

                                <p class="wd-analysis-point-text">
                                    {{ $point['analyse'] ?? '' }}
                                </p>

                            </div>

                        @empty

                            <p class="wd-analysis-empty">
                                Analyse indisponible pour le moment.
                            </p>

                        @endforelse

                    </section>

                </div>

            </article>

        @endforeach

    </div>

    {{-- ======================================================
         SUGGESTION DE PRESTATIONS
         ====================================================== --}}

    <div class="wd-suggestion-bar">

        <div class="wd-suggestion-copy">

            <p class="wd-suggestion-kicker">
                Aide à la décision
            </p>

            <h3 class="wd-suggestion-title">
                Suggestion de prestations
            </h3>

            @if($suggestionDisponible)

                <p class="wd-suggestion-description">
                    Les trois analyses du dossier sont disponibles et à jour.
                    Vous pouvez générer une suggestion personnalisée.
                </p>

            @else

                <p class="wd-suggestion-description">
                    La suggestion nécessite les trois analyses du dossier,
                    chacune datant de moins d’un an.
                </p>

            @endif

        </div>


        <form
            method="POST"
            action="{{ route('tenant.clients.aide-decision.suggestion', $client) }}"
        >

            @csrf

            <button
                type="submit"
                class="wd-suggestion-button"
                @disabled(! $suggestionDisponible)
            >
                Suggérer
            </button>

        </form>

    </div>


    @if($derniereSuggestion)

        @php

            $suggestionResultat =
                $derniereSuggestion->result_json ?? [];

            $prestations =
                is_array($suggestionResultat['prestations'] ?? null)
                    ? $suggestionResultat['prestations']
                    : [];

        @endphp


        @if(count($prestations))

            <section class="wd-suggestion-result">

                <div class="wd-suggestion-result-head">

                    <h3 class="wd-suggestion-result-title">
                        Prestations suggérées
                    </h3>

                    <p class="wd-suggestion-result-date">
                        Analyse générée le
                        {{ optional($derniereSuggestion->completed_at?->copy()->setTimezone('Europe/Paris'))->translatedFormat('d F Y à H:i') }}
                    </p>

                </div>


                <div class="wd-suggestion-list">

                    @foreach($prestations as $index => $prestation)

                        <article class="wd-suggestion-item">

                            <span class="wd-suggestion-item-number">
                                {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                            </span>

                            <h4 class="wd-suggestion-item-title">
                                {{ $prestation['titre'] ?? '' }}
                            </h4>

                            <p class="wd-suggestion-item-text">
                                {{ $prestation['justification'] ?? '' }}
                            </p>

                            @if(
                                isset($prestation['actions']) &&
                                is_array($prestation['actions'])
                            )

                                <div class="wd-suggestion-actions">

                                    <div class="wd-suggestion-actions-label">
                                        Actions proposées
                                    </div>

                                    <ul>

                                        @foreach($prestation['actions'] as $action)

                                            <li>
                                                {{ $action }}
                                            </li>

                                        @endforeach

                                    </ul>

                                </div>

                            @endif

                        </article>

                    @endforeach

                </div>

            </section>

        @endif

    @endif




    {{-- ======================================================
         RECOMMANDATION PATRIMONIALE
         ====================================================== --}}

    <div class="wd-recommandation-bar">

        <div class="wd-recommandation-copy">

            <p class="wd-recommandation-kicker">
                Aide à la décision
            </p>

            <h3 class="wd-recommandation-title">
                Recommandation patrimoniale
            </h3>

            @if($recommandationDisponible)

                <p class="wd-recommandation-description">
                    Accédez à la recommandation patrimoniale construite
                    à partir de l’analyse du dossier.
                </p>

            @else

                <p class="wd-recommandation-description">
                    La recommandation patrimoniale sera disponible après
                    la génération d’une suggestion de prestations.
                </p>

            @endif

        </div>


        @if($recommandationDisponible)

            <a
                href="{{ route('tenant.clients.recommandation-patrimoniale', $client) }}"
                class="wd-recommandation-button"
            >
                Recommandation
            </a>

        @else

            <span class="wd-recommandation-button-disabled">
                Recommandation
            </span>

        @endif

    </div>

</section>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var overlay = document.querySelector('[data-dossier-modal]');
    var triggers = document.querySelectorAll('[data-dossier-trigger]');
    var closeBtn = document.querySelector('[data-dossier-close]');
    if (!overlay || !triggers.length) { return; }
    triggers.forEach(function (trigger) {
        trigger.addEventListener('click', function () { overlay.hidden = false; });
    });
    if (closeBtn) { closeBtn.addEventListener('click', function () { overlay.hidden = true; }); }
    overlay.addEventListener('click', function (e) { if (e.target === overlay) { overlay.hidden = true; } });
});
</script>
</x-tenant-app-layout>
