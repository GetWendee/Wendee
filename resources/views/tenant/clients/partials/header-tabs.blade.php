@php
    $viewRole = Auth::user()?->effectiveRole();
    $initiales = mb_strtoupper(
        mb_substr($client->prenom ?? '', 0, 1) .
        mb_substr($client->nom ?? '', 0, 1)
    );
    $formatEuro = fn($value) =>
        number_format((float) $value, 0, ',', ' ') . ' €';
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
    $kycCompletion = 0;
    if ($client->kyc) {
        $kycData = collect($client->kyc->getAttributes())
            ->except(['id', 'client_id', 'created_at', 'updated_at']);
        $totalKycFields = $kycData->count();
        $filledKycFields = $kycData->filter(function ($value) {
            return ! is_null($value) && $value !== '' && $value !== '[]';
        })->count();
        $kycCompletion = $totalKycFields > 0
            ? (int) round(($filledKycFields / $totalKycFields) * 100)
            : 0;
    }
    $derniereSuggestion = $client->analyses()
        ->where('type', 'suggestion')
        ->where('status', 'completed')
        ->latest('completed_at')
        ->first();
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
    $analyseLocked = ! $dossierComplet;
    $analyseTooltip = 'Complétez le KYC, le Patrimoine et le Profil investisseur pour débloquer l\'Analyse.';
    $missionLocked = ! ($recommandationDisponible && $planActionDisponible);
    $missionTooltip = 'Complétez la Suggestion et la Recommandation pour débloquer la Mission.';
    $contratsMandatsTypes = [
        'mandat_assurance_vie', 'mandat_assurance_deces', 'mandat_assurance_emprunteur',
        'mandat_assurance_habitation', 'mandat_assurance_obseques', 'mandat_complementaire_sante',
        'mandat_contrat_capitalisation', 'mandat_garantie_accident_vie', 'mandat_assurance_vehicule',
        'mandat_plan_epargne_retraite',
    ];
    $contratLocked = ! $client->analyses()
        ->whereIn('type', $contratsMandatsTypes)
        ->where('status', 'completed')
        ->exists();
    $contratTooltip = 'Générez au moins un contrat pour débloquer cet onglet.';
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
.wd-wrap{max-width:1540px;margin:auto;padding:28px 34px 60px;}
.wd-hero{background:#242424;color:white;border-radius:14px;overflow:hidden;border-top:3px solid var(--pink);box-shadow:0 10px 30px rgba(27,23,22,.08);}
.wd-hero-non-conforme{background:#E67E22;}
.wd-hero-conforme{border-top-color:var(--green);}
.wd-hero-main{display:grid;grid-template-columns:1.4fr .6fr;gap:24px;padding:28px 30px 25px;}
.wd-identity{display:flex;align-items:center;gap:18px;}
.wd-avatar{width:64px;height:64px;border-radius:50%;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.08);display:grid;place-items:center;font-size:18px;font-weight:800;}
.wd-eyebrow{font-size:10px;color:var(--pink);font-weight:850;letter-spacing:.19em;text-transform:uppercase;}
.wd-hero .wd-eyebrow{color:#c9c2be}
.wd-hero-non-conforme .wd-eyebrow{color:#fff}
.wd-hero h1{margin:6px 0 4px;font-size:34px;letter-spacing:-.045em;}
.wd-hero-meta{color:#aaa29e;font-size:12px;}
.wd-hero-non-conforme .wd-hero-meta{color:#fff}
.wd-actions{display:flex;justify-content:flex-end;gap:9px;align-items:flex-start;}
.wd-btn{min-height:40px;padding:0 14px;border-radius:8px;border:1px solid rgba(255,255,255,.14);background:rgba(255,255,255,.06);color:white;display:inline-flex;align-items:center;text-decoration:none;font-size:11px;font-weight:700;}
.wd-btn.primary{background:#fff;color:#242424;}
.wd-hero-foot{display:grid;grid-template-columns:repeat(auto-fit, minmax(180px, 1fr));border-top:1px solid rgba(255,255,255,.10);background:#242424;}
.wd-hero-foot>div{padding:16px 20px;border-right:1px solid rgba(255,255,255,.10);}
.wd-hero-foot>div:last-child{border-right:0;}
.wd-hero-foot span{display:block;color:#8f8883;font-size:9px;text-transform:uppercase;letter-spacing:.11em;font-weight:800;}
.wd-hero-foot strong{display:block;margin-top:6px;font-size:13px;}
.wd-tabs{display:flex;gap:4px;margin:16px 0 24px;padding:5px;background:#ebe8e5;border-radius:10px;}
.wd-tabs a{flex:1;text-align:center;padding:10px 16px;border-radius:7px;text-decoration:none;color:#77706c;font-size:11px;}
.wd-tabs a.active{background:#fff;color:#171514;font-weight:750;}
.wd-btn-dark{
    flex:0 0 auto;
    min-width:220px;
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
.wd-btn-dark-disabled{
    flex:0 0 auto;
    min-height:40px;
    padding:0 20px;
    border:1px solid #D2D8D5;
    border-top:2px solid #C8CFCC;
    border-radius:8px;
    background:#E2E5E4;
    color:#929A97;
    font-size:9px;
    font-weight:800;
    letter-spacing:.10em;
    text-transform:uppercase;
    cursor:not-allowed;
    display:inline-flex;
    align-items:center;
    justify-content:center;
}
.wd-section{margin-top:22px;}
.wd-tabs span.locked{flex:1;text-align:center;padding:10px 16px;border-radius:7px;color:#b7b2ad;font-size:11px;cursor:not-allowed;}
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
<a class="wd-btn" href="{{ route('tenant.clients.edit', $client) }}">
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
<a href="{{ route('tenant.dashboard') }}" class="{{ ($active ?? null) === 'dashboard' ? 'active' : '' }}">
Tableau de bord
</a>
<a href="{{ route('tenant.clients.show', $client) }}" class="{{ ($active ?? null) === 'vue' ? 'active' : '' }}">
Profil
</a>
@if($analyseLocked)
<span class="locked" title="{{ $analyseTooltip }}">Analyse</span>
@else
<a href="{{ route('tenant.clients.aide-decision', $client) }}" class="{{ ($active ?? null) === 'analyse' ? 'active' : '' }}">
Analyse
</a>
@endif
@if($missionLocked)
<span class="locked" title="{{ $missionTooltip }}">Mission</span>
@else
<a href="{{ route('tenant.clients.mission', $client) }}" class="{{ ($active ?? null) === 'mission' ? 'active' : '' }}">
Mission
</a>
@endif
@if($contratLocked)
<span class="locked" title="{{ $contratTooltip }}">Contrat</span>
@else
<a href="{{ route('tenant.clients.contrats-clients', $client) }}" class="{{ ($active ?? null) === 'contrats' ? 'active' : '' }}">
Contrat
</a>
@endif
<a href="{{ route('tenant.clients.conformites-clients', $client) }}" class="{{ ($active ?? null) === 'archives' ? 'active' : '' }}">
Archives
</a>
</nav>
