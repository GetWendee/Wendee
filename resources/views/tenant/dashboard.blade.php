<x-tenant-app-layout>

<style>
body > div > nav,
body > div > header{display:none!important}
html,body{margin:0!important;background:#f3f1ee!important}

:root{
--bg:#f3f1ee;--white:#fff;--ink:#151515;--muted:#817b76;--line:#ded9d4;
--dark:#1b1716;--pink:#f40087;--red:#b94d4d;--green:#4d8760;
}

*{box-sizing:border-box}

.wd-sidebar{
position:fixed;inset:0 auto 0 0;width:232px;height:100vh;
background:#171211;color:#fff;padding:24px 16px;
display:flex;flex-direction:column;z-index:1000;overflow:hidden
}
.wd-logo{padding:0 7px 36px;font-size:23px;font-weight:800;letter-spacing:-.06em}
.wd-logo b{color:var(--pink)}
.wd-logo small{display:block;color:#7e7773;font-size:8px;letter-spacing:.25em;text-transform:uppercase;margin-top:5px}
.wd-nav{display:grid;gap:3px}
.wd-nav a{padding:12px 10px;border-radius:8px;color:#aaa19d;display:flex;gap:13px;align-items:center;font-size:13px;text-decoration:none}
.wd-nav a i{width:19px;color:#69615d;font-size:9px;font-style:normal}
.wd-nav a.active{background:#292321;color:#fff}
.wd-nav a.active i{color:var(--pink)}
.wd-bottom-nav{margin-top:auto;border-top:1px solid #302a28;padding-top:14px}
.wd-bottom-nav a,.wd-bottom-nav button{display:block;width:100%;padding:9px 10px;color:#8e8681;font-size:9px;text-transform:uppercase;letter-spacing:.12em;text-align:left;background:none;border:0}

.wd-main{margin-left:232px;min-height:100vh;padding-top:68px}

.wd-topbar{
position:fixed;top:0;left:232px;right:0;height:68px;z-index:900;
background:rgba(255,255,255,.94);border-bottom:1px solid var(--line);
display:flex;align-items:center;justify-content:space-between;padding:0 34px;
backdrop-filter:blur(10px)
}
.wd-crumb{color:#96908b;font-size:11px;font-weight:800;letter-spacing:.17em;text-transform:uppercase}
.wd-who{display:flex;gap:10px;align-items:center;text-align:right}
.wd-who strong{font-size:14px}
.wd-who small{display:block;color:#99918c;font-size:10px;text-transform:uppercase;letter-spacing:.1em}
.wd-top-avatar{width:34px;height:34px;border-radius:50%;background:var(--pink);display:grid;place-items:center;color:#fff;font-size:10px;font-weight:800}

.wd-wrap{max-width:1540px;margin:auto;padding:30px 34px 60px}
.wd-head{display:flex;justify-content:space-between;align-items:end}
.wd-eyebrow{font-size:12px;color:var(--pink);font-weight:850;letter-spacing:.2em;text-transform:uppercase}
.wd-head h1{font-size:38px;line-height:1;margin:8px 0 0;letter-spacing:-.05em;font-weight:650}
.wd-head p{color:var(--muted);margin:10px 0 0;font-size:15px}
.wd-date{font-size:12px;color:#8e8782;text-transform:uppercase;letter-spacing:.12em}

.wd-summary{margin-top:28px;background:var(--dark);color:#fff;border-top:3px solid var(--pink);border-radius:10px;display:grid;grid-template-columns:1.2fr 1fr 1fr 1fr;overflow:hidden}
.wd-sum{padding:22px;border-right:1px solid #383230}
.wd-sum:last-child{border:0}
.wd-sum label{color:#aaa29e;font-size:11px;font-weight:800;letter-spacing:.15em;text-transform:uppercase}
.wd-sum strong{display:block;margin-top:9px;font-size:30px;letter-spacing:-.04em}
.wd-sum small{display:block;margin-top:5px;color:#aaa29e;font-size:12px}
.wd-sum:first-child strong{color:var(--pink)}

.wd-section{margin-top:30px}
.wd-section-title{margin-bottom:14px}
.wd-section-title h2{margin:4px 0 0;font-size:24px;letter-spacing:-.03em}

.wd-panel{background:#fff;border:1px solid var(--line);border-radius:11px;overflow:hidden}
.wd-searchbar{padding:14px 18px;border-bottom:1px solid var(--line);background:#fcfbfa}
.wd-search{width:300px;padding:12px 14px;border:1px solid var(--line);border-radius:8px;background:#fff;font-size:13px}

.wd-table{width:100%;border-collapse:collapse}
.wd-table th{padding:13px 18px;background:#faf9f7;color:#918984;text-align:left;font-size:11px;letter-spacing:.14em;text-transform:uppercase}
.wd-table td{padding:16px 18px;border-top:1px solid #eeeae7;font-size:13px;vertical-align:middle}
.wd-client{display:flex;align-items:center;gap:11px}
.wd-avatar{width:34px;height:34px;border-radius:50%;background:#f1efed;display:grid;place-items:center;color:#66605c;font-size:10px;font-weight:800}
.wd-client strong{font-size:14px}
.wd-client small{display:block;margin-top:3px;color:#918984;font-size:12px}

.wd-status{display:flex;align-items:center;justify-content:space-between;gap:10px;padding:11px 12px;border-radius:8px;font-weight:700;font-size:12px;text-decoration:none}
.wd-status.bad{background:#fff7f7;border:1px solid #f1d1d1;color:#c34242}
.wd-status.ok{background:#f3f9f4;border:1px solid #d7e8da;color:var(--green)}
.wd-action{color:var(--pink);font-weight:800;text-decoration:none;white-space:nowrap}

.wd-finances{display:grid;grid-template-columns:1fr 1fr;gap:22px;margin-top:30px}
.wd-fin-card{background:#fff;border:1px solid #d9d4cf;border-radius:12px;overflow:hidden}
.wd-fin-head{background:#1b1716;color:#fff;padding:20px 22px 18px;border-bottom:3px solid var(--pink)}
.wd-fin-head strong{display:block;font-size:20px;letter-spacing:-.02em}
.wd-fin-head span{display:block;margin-top:5px;color:#aaa19d;font-size:12px}
.wd-fin-body{padding:6px 22px 14px}
.wd-fin-body>div{display:flex;justify-content:space-between;align-items:center;padding:15px 0;border-bottom:1px solid #eeeae7}
.wd-fin-body>div:last-child{border:0;border-top:1px solid #ded9d4}
.wd-fin-body span{font-size:13px;color:#817b76;font-weight:600}
.wd-fin-body strong{font-size:18px;color:#151515}
.wd-fin-body>div:last-child strong{font-size:22px;color:var(--pink)}

@media(max-width:1050px){
.wd-sidebar{width:72px}
.wd-logo{font-size:0}.wd-logo b{font-size:22px}.wd-logo small,.wd-nav span,.wd-bottom-nav{display:none}
.wd-nav a{justify-content:center}
.wd-main{margin-left:72px}
.wd-topbar{left:72px}
.wd-summary{grid-template-columns:1fr 1fr}
.wd-finances{grid-template-columns:1fr}
}
@media(max-width:650px){
.wd-wrap{padding:22px 14px 50px}
.wd-head{flex-direction:column;align-items:flex-start;gap:15px}
.wd-summary{grid-template-columns:1fr}
.wd-panel{overflow-x:auto}
.wd-table{min-width:1050px}
}
</style>



<div class="wd-wrap">

<section class="wd-head">
<div>
<div class="wd-eyebrow">Contrôle du portefeuille</div>
<h1>Les points à traiter.</h1>
<p>Une vue opérationnelle des situations qui nécessitent votre intervention.</p>
</div>
<div class="wd-head-actions">
    @php
    $newAccountRoles = Auth::user()->creatableUserRoles();
    @endphp
    @if(count($newAccountRoles) === 0)
    <a href="{{ route('tenant.clients.create') }}" class="wd-new-client">
        <span class="wd-new-client-plus">+</span>
        <span>Nouveau compte</span>
    </a>
    @else
    <button type="button" class="wd-new-client" data-new-account-trigger>
        <span class="wd-new-client-plus">+</span>
        <span>Nouveau compte</span>
    </button>
    @endif

    <div class="wd-date">{{ now()->translatedFormat('l d F Y') }}</div>
</div>


</section>

@php
    $nbNonConformites = collect($clientsASuivre ?? [])
        ->sum(fn($ligne) => collect($ligne['anomalies'] ?? [])->count());

    $nbDossiersConcernes = count($clientsASuivre ?? []);
@endphp

<section class="wd-summary wd-summary-premium">

    <div class="wd-sum wd-sum-primary">
        <label>Non-conformités</label>
        <strong>{{ str_pad($nbNonConformites, 2, '0', STR_PAD_LEFT) }}</strong>
        <small>situations nécessitant une intervention</small>
    </div>

    <div class="wd-sum">
        <label>Dossiers concernés</label>
        <strong>{{ str_pad($nbDossiersConcernes, 2, '0', STR_PAD_LEFT) }}</strong>
        <small>sur {{ str_pad($nombreClients ?? 0, 2, '0', STR_PAD_LEFT) }} clients suivis</small>
    </div>

    <div class="wd-sum">
        <label>À traiter aujourd'hui</label>
        <strong>{{ str_pad($nbDossiersConcernes, 2, '0', STR_PAD_LEFT) }}</strong>
        <small>actions prioritaires</small>
    </div>

    <div class="wd-sum">
        <label>Conformité portefeuille</label>
        <strong>{{ $tauxCompletionMoyen ?? 0 }} %</strong>
        <small>niveau global du portefeuille</small>
    </div>

</section>


<section class="wd-finances wd-finances-premium">

    <div class="wd-fin-card wd-fin-card-premium">
        <div class="wd-fin-topline">
            <div>
                <div class="wd-eyebrow">Situation patrimoniale</div>
                <h3>Patrimoine global</h3>
                <p>Agrégation des situations patrimoniales de vos clients.</p>
            </div>
            <div class="wd-fin-kpi">
                <span>Net conseillé</span>
                <strong>{{ number_format($patrimoineNet ?? 0,0,',',' ') }} €</strong>
            </div>
        </div>

        <div class="wd-fin-metrics">
            <div>
                <span>Actifs</span>
                <strong>{{ number_format($actifs ?? 0,0,',',' ') }} €</strong>
            </div>
            <div>
                <span>Passifs</span>
                <strong>{{ number_format($passifs ?? 0,0,',',' ') }} €</strong>
            </div>
        </div>
    </div>

    <div class="wd-fin-card wd-fin-card-premium">
        <div class="wd-fin-topline">
            <div>
                <div class="wd-eyebrow">Flux annuels</div>
                <h3>Revenus & charges</h3>
                <p>Synthèse déclarative annuelle du portefeuille.</p>
            </div>
            <div class="wd-fin-kpi">
                <span>Solde annuel</span>
                <strong>{{ number_format($soldeAnnuel ?? 0,0,',',' ') }} €</strong>
            </div>
        </div>

        <div class="wd-fin-metrics">
            <div>
                <span>Revenus</span>
                <strong>{{ number_format($revenus ?? 0,0,',',' ') }} €</strong>
            </div>
            <div>
                <span>Charges</span>
                <strong>{{ number_format($charges ?? 0,0,',',' ') }} €</strong>
            </div>
        </div>
    </div>

</section>


<section class="wd-section">

<div class="wd-section-title">
<div class="wd-eyebrow">Liste des</div>
<h2>Non Conformités</h2>
</div>

<div class="wd-panel">
<table class="wd-table">
<thead>
<tr>
<th>Client</th>
<th>KYC</th>
<th>Patrimoine</th>
<th>Profil investisseur</th>
<th>Dossier</th>
</tr>
</thead>

<tbody>
@forelse(($clientsASuivre ?? []) as $suivi)
@php
$client=$suivi['client'];
$prenom=$client->prenom ?? '';
$nom=$client->nom ?? '';
$initiales=mb_strtoupper(mb_substr($prenom,0,1).mb_substr($nom,0,1));
$anomalies=collect($suivi['anomalies'] ?? []);
$kyc=$anomalies->firstWhere('type','KYC');
$patrimoine=$anomalies->firstWhere('type','Patrimoine');
$profil=$anomalies->firstWhere('type','Profil investisseur');
@endphp

<tr>
<td>
<div class="wd-client">
<div class="wd-avatar">{{ $initiales }}</div>
<div>
<strong>{{ $prenom }} {{ $nom }}</strong>
<small>{{ $suivi['completion'] }} % complété</small>
</div>
</div>
</td>

<td>
@if($kyc)
<a class="wd-status bad" href="{{ $kyc['url'] }}"><span>{{ $kyc['libelle'] }}</span><span>→</span></a>
@else
<div class="wd-status ok"><span>Conforme</span><span>✓</span></div>
@endif
</td>

<td>
@if($patrimoine)
<a class="wd-status bad" href="{{ $patrimoine['url'] }}"><span>{{ $patrimoine['libelle'] }}</span><span>→</span></a>
@else
<div class="wd-status ok"><span>Conforme</span><span>✓</span></div>
@endif
</td>

<td>
@if($profil)
<a class="wd-status bad" href="{{ $profil['url'] }}"><span>{{ $profil['libelle'] }}</span><span>→</span></a>
@else
<div class="wd-status ok"><span>Conforme</span><span>✓</span></div>
@endif
</td>

<td><a class="wd-action" href="{{ route('tenant.clients.show', $client) }}">Voir le dossier →</a></td>
</tr>

@empty
<tr><td colspan="5" style="padding:35px;text-align:center">Aucune non-conformité.</td></tr>
@endforelse
</tbody>
</table>

</div>
</section>



</div>
</x-tenant-app-layout>

<style>
/* BANDEAU PREMIUM */
.wd-summary-premium{
    grid-template-columns:1.15fr 1fr 1fr 1fr!important;
    border-radius:12px!important;
    border-top:0!important;
    position:relative;
    box-shadow:0 12px 34px rgba(20,16,14,.08);
}

.wd-summary-premium:before{
    content:"";
    position:absolute;
    top:0;left:0;right:0;
    height:3px;
    background:#f40087;
}

.wd-summary-premium .wd-sum{
    padding:26px 24px 23px!important;
}

.wd-summary-premium .wd-sum label{
    font-size:10px!important;
    letter-spacing:.16em!important;
    color:#aaa29e!important;
}

.wd-summary-premium .wd-sum strong{
    margin-top:12px!important;
    font-size:34px!important;
    line-height:1!important;
    font-weight:650!important;
}

.wd-summary-premium .wd-sum small{
    margin-top:9px!important;
    font-size:11px!important;
    color:#aaa29e!important;
}

.wd-summary-premium .wd-sum-primary strong{
    color:#f40087!important;
}

/* CARTES FINANCIÈRES PLUS PREMIUM */
.wd-finances{
    gap:24px!important;
}

.wd-fin-card{
    border-radius:12px!important;
    border:1px solid #dcd7d2!important;
    box-shadow:0 8px 30px rgba(27,23,22,.045);
}

.wd-fin-head{
    padding:22px 24px 20px!important;
    background:#1b1716!important;
    border-bottom:0!important;
    position:relative;
}

.wd-fin-head:after{
    content:"";
    position:absolute;
    bottom:0;left:24px;
    width:42px;height:3px;
    background:#f40087;
}

.wd-fin-head strong{
    font-size:20px!important;
    font-weight:650!important;
    letter-spacing:-.025em!important;
}

.wd-fin-head span{
    margin-top:6px!important;
    font-size:11px!important;
    color:#aaa29e!important;
}

.wd-fin-body{
    padding:8px 24px 14px!important;
}

.wd-fin-body>div{
    padding:17px 0!important;
}

.wd-fin-body span{
    color:#817b76!important;
    font-size:12px!important;
    text-transform:uppercase;
    letter-spacing:.07em;
}

.wd-fin-body strong{
    font-size:18px!important;
    letter-spacing:-.02em;
}

.wd-fin-body>div:last-child strong{
    font-size:23px!important;
}
</style>

<style>
/* CONTENU PREMIUM DES 2 CARTES FINANCIÈRES */
.wd-fin-body{
    padding:0!important;
    background:#fff!important;
}

.wd-fin-body>div{
    position:relative;
    padding:19px 24px!important;
    border-bottom:1px solid #eeeae7!important;
}

.wd-fin-body>div:last-child{
    border-bottom:0!important;
    border-top:0!important;
    background:#faf9f7;
}

.wd-fin-body>div span{
    font-size:11px!important;
    font-weight:800!important;
    letter-spacing:.12em!important;
    text-transform:uppercase;
    color:#8f8883!important;
}

.wd-fin-body>div strong{
    font-size:19px!important;
    font-weight:650!important;
    color:#171312!important;
    letter-spacing:-.025em!important;
}

.wd-fin-body>div:last-child{
    padding-top:22px!important;
    padding-bottom:22px!important;
}

.wd-fin-body>div:last-child span{
    color:#171312!important;
}

.wd-fin-body>div:last-child strong{
    font-size:27px!important;
    color:#f40087!important;
    letter-spacing:-.04em!important;
}

/* petite ligne d'accent discrète sur la valeur finale */
.wd-fin-body>div:last-child:before{
    content:"";
    position:absolute;
    left:24px;
    top:0;
    width:44px;
    height:2px;
    background:#f40087;
}

/* respiration + effet reporting premium */
.wd-fin-card{
    overflow:hidden;
    background:#fff;
}

.wd-fin-card:hover{
    box-shadow:0 12px 34px rgba(27,23,22,.06);
}
</style>

<style>
.wd-finances-premium{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:22px;
}

.wd-fin-card-premium{
    background:#fff;
    border:1px solid #ded9d4;
    border-radius:12px;
    overflow:hidden;
    box-shadow:0 8px 28px rgba(27,23,22,.04);
}

.wd-fin-topline{
    display:flex;
    justify-content:space-between;
    gap:24px;
    align-items:flex-start;
    padding:24px;
    border-bottom:1px solid #eeeae7;
}

.wd-fin-topline h3{
    margin:5px 0 0;
    font-size:22px;
    font-weight:700;
    letter-spacing:-.03em;
}

.wd-fin-topline p{
    margin:7px 0 0;
    font-size:12px;
    color:#817b76;
}

.wd-fin-kpi{
    min-width:190px;
    text-align:right;
}

.wd-fin-kpi span{
    display:block;
    font-size:10px;
    font-weight:800;
    letter-spacing:.12em;
    text-transform:uppercase;
    color:#918984;
}

.wd-fin-kpi strong{
    display:block;
    margin-top:7px;
    font-size:28px;
    line-height:1;
    letter-spacing:-.04em;
    color:#151515;
}

.wd-fin-kpi strong:after{
    content:"";
    display:block;
    width:36px;
    height:3px;
    margin:10px 0 0 auto;
    background:#f40087;
    border-radius:3px;
}

.wd-fin-metrics{
    display:grid;
    grid-template-columns:1fr 1fr;
}

.wd-fin-metrics>div{
    padding:20px 24px;
}

.wd-fin-metrics>div+div{
    border-left:1px solid #eeeae7;
}

.wd-fin-metrics span{
    display:block;
    font-size:11px;
    color:#918984;
    text-transform:uppercase;
    letter-spacing:.1em;
    font-weight:800;
}

.wd-fin-metrics strong{
    display:block;
    margin-top:7px;
    font-size:18px;
    color:#151515;
}

@media(max-width:1050px){
    .wd-finances-premium{grid-template-columns:1fr}
}

@media(max-width:650px){
    .wd-fin-topline{flex-direction:column}
    .wd-fin-kpi{text-align:left;min-width:0}
    .wd-fin-kpi strong:after{margin-left:0}
}
</style>

<style>
.wd-fin-card-premium{
    background:#FAF9F7 !important;
}

.wd-fin-topline{
    background:#FAF9F7 !important;
}

.wd-fin-metrics{
    background:#FAF9F7 !important;
}
</style>

<style>
/* Fond uniquement sur les zones titre des 2 cartes */
.wd-fin-card-premium{
    background:#fff !important;
}

.wd-fin-topline{
    background:#FAF9F7 !important;
}

.wd-fin-metrics{
    background:#fff !important;
}
</style>

<style>
.wd-sidebar,
.wd-summary{
    background:#242424 !important;
}

.wd-nav-section{
    margin:22px 10px 7px;
    font-size:9px;
    font-weight:700;
    letter-spacing:.14em;
    text-transform:uppercase;
    color:rgba(255,255,255,.30);
}

.wd-nav a{
    display:flex !important;
    align-items:center !important;
    gap:11px !important;
    padding:10px 11px !important;
    margin:2px 0;
    border-radius:8px !important;
    color:rgba(255,255,255,.62) !important;
    font-size:12px !important;
}

.wd-nav a svg{
    width:18px;
    height:18px;
    flex:0 0 18px;
    fill:none;
    stroke:currentColor;
    stroke-width:1.7;
    stroke-linecap:round;
    stroke-linejoin:round;
    color:rgba(255,255,255,.42);
}

.wd-nav a.active{
    background:rgba(244,0,135,.11) !important;
    color:#fff !important;
}

.wd-nav a.active svg{
    color:#f40087;
}

.wd-nav a:hover{
    background:rgba(255,255,255,.06) !important;
    color:#fff !important;
}
</style>

<style>
/* ACTION PRINCIPALE DU DASHBOARD */
.wd-head-actions{
    display:flex;
    flex-direction:column;
    align-items:flex-end;
    gap:13px;
}

.wd-new-client{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:10px;

    min-height:42px;
    padding:0 17px;

    background:#242424;
    border:1px solid #242424;
    border-radius:7px;

    color:#fff !important;
    text-decoration:none !important;

    font-size:12px;
    font-weight:650;
    letter-spacing:-.01em;

    box-shadow:0 5px 16px rgba(36,36,36,.10);
    transition:
        transform .15s ease,
        box-shadow .15s ease,
        background .15s ease;
}

.wd-new-client-plus{
    display:flex;
    align-items:center;
    justify-content:center;

    width:20px;
    height:20px;

    border-radius:50%;
    background:#f40087;
    color:#fff;

    font-size:17px;
    font-weight:400;
    line-height:1;
}

.wd-new-client:hover{
    background:#171717;
    box-shadow:0 8px 20px rgba(36,36,36,.16);
    transform:translateY(-1px);
}

@media(max-width:700px){
    .wd-head-actions{
        align-items:flex-start;
    }
}
</style>




