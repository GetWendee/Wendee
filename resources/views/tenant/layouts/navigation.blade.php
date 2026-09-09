@php
    $newAccountRoles = Auth::check() ? Auth::user()->creatableUserRoles() : [];
    $wdCrumb = match (true) {
        request()->routeIs('tenant.dashboard') => 'Portefeuille · Points à traiter',
        request()->routeIs('tenant.cabinet') => 'Cabinet · Conformité & pilotage',
        request()->routeIs('tenant.users.*') => 'Équipe · Nouveau compte',
        request()->routeIs('tenant.clients.*') => 'Portefeuille · Clients',
        request()->routeIs('tenant.portefeuille.*') => 'Portefeuille',
        request()->routeIs('tenant.performances.*') => 'Cabinet · Patrimoine sous gestion',
        request()->routeIs('tenant.revenus.*') => 'Cabinet · Revenus',
        request()->routeIs('tenant.commissions.*') => 'Cabinet · Commissions',
        request()->routeIs('tenant.rendez-vous.*') => 'Agenda · Rendez-vous',
        request()->routeIs('tenant.profil.rib.*') => 'Compte · Mon RIB',
        default => 'Wendee',
    };
@endphp
<aside class="wd-sidebar">
    <div class="wd-logo"><b>W</b>endee<small>OS du conseiller patrimonial</small></div>
    <nav class="wd-nav">
        <div class="wd-nav-section">Général</div>
        <a class="{{ request()->routeIs('tenant.dashboard') || (Auth::check() && Auth::user()->effectiveRole() === 'apporteur' && request()->routeIs('tenant.portefeuille.*')) ? 'active' : '' }}" href="{{ Auth::check() && Auth::user()->effectiveRole() === 'apporteur' ? route('tenant.portefeuille.index') : route('tenant.dashboard') }}">
            <svg viewBox="0 0 24 24"><path d="M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h6v6h-6z"/></svg>
            <span>Tableau de bord</span>
        </a>
        @if(Auth::check() && in_array(Auth::user()->effectiveRole(), ['courtier', 'conseiller'], true))
        <a class="{{ request()->routeIs('tenant.portefeuille.*') ? 'active' : '' }}" href="{{ route('tenant.portefeuille.index') }}">
            <svg viewBox="0 0 24 24"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M3 12h18"/></svg>
            <span>Portefeuille</span>
        </a>
        @endif
        @if(count($newAccountRoles) > 0)
        <a href="#" class="{{ request()->routeIs('tenant.users.*') ? 'active' : '' }}" data-new-account-trigger>
            <svg viewBox="0 0 24 24"><circle cx="9" cy="7" r="4"/><path d="M3 21v-2a6 6 0 0 1 6-6M16 11v6M13 14h6"/></svg>
            <span>Créer un utilisateur</span>
        </a>
        @endif
        @if(Auth::check() && Auth::user()->effectiveRole() === 'apporteur')
        <a class="{{ request()->routeIs('tenant.clients.create') ? 'active' : '' }}" href="{{ route('tenant.clients.create') }}">
            <svg viewBox="0 0 24 24"><circle cx="9" cy="7" r="4"/><path d="M3 21v-2a6 6 0 0 1 6-6M16 11v6M13 14h6"/></svg>
            <span>Créer un client</span>
        </a>
        @endif
        @if(Auth::check() && Auth::user()->effectiveRole() !== 'apporteur')
        <a href="{{ route('tenant.rendez-vous.index') }}" class="{{ request()->routeIs('tenant.rendez-vous.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M8 3v4M16 3v4M3 11h18"/></svg>
            <span>Rendez-vous</span>
        </a>
        @endif
        @if(Auth::check() && Auth::user()->effectiveRole() !== 'apporteur')
        <div class="wd-nav-section">Activité</div>
        @if(Auth::check() && Auth::user()->effectiveRole() === 'courtier')
        <a class="{{ request()->routeIs('tenant.performances.*') ? 'active' : '' }}" href="{{ route('tenant.performances.index') }}">
            <svg viewBox="0 0 24 24"><path d="M5 20v-6M12 20V9M19 20V4"/></svg>
            <span>Patrimoine sous gestion</span>
        </a>
        @else
        <a href="#" class="disabled" aria-disabled="true" tabindex="-1">
            <svg viewBox="0 0 24 24"><path d="M5 20v-6M12 20V9M19 20V4"/></svg>
            <span>Patrimoine sous gestion</span>
            <span class="wd-soon">Bientot</span>
        </a>
        @endif
        @if(Auth::check() && Auth::user()->effectiveRole() === 'courtier')
        <a class="{{ request()->routeIs('tenant.revenus.*') ? 'active' : '' }}" href="{{ route('tenant.revenus.index') }}">
            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M15 8.5c-.8-.9-1.8-1.5-3.2-1.5C10 7 9 8 9 9.3c0 1.5 1.4 2.1 3 2.7 1.7.6 3 1.3 3 2.8 0 1.3-1.1 2.2-3 2.2-1.4 0-2.6-.5-3.5-1.5M12 5v14"/></svg>
            <span>Revenus</span>
        </a>
        @else
        <a href="#" class="disabled" aria-disabled="true" tabindex="-1">
            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M15 8.5c-.8-.9-1.8-1.5-3.2-1.5C10 7 9 8 9 9.3c0 1.5 1.4 2.1 3 2.7 1.7.6 3 1.3 3 2.8 0 1.3-1.1 2.2-3 2.2-1.4 0-2.6-.5-3.5-1.5M12 5v14"/></svg>
            <span>Revenus</span>
            <span class="wd-soon">Bientot</span>
        </a>
        @endif
        @if(Auth::check() && Auth::user()->effectiveRole() === 'courtier')
        <a class="{{ request()->routeIs('tenant.commissions.*') ? 'active' : '' }}" href="{{ route('tenant.commissions.index') }}">
            <svg viewBox="0 0 24 24"><rect x="3" y="6" width="18" height="12" rx="2"/><path d="M7 10h.01M17 14h.01M9 12h6"/></svg>
            <span>Commissions</span>
        </a>
        @else
        <a href="#" class="disabled" aria-disabled="true" tabindex="-1">
            <svg viewBox="0 0 24 24"><rect x="3" y="6" width="18" height="12" rx="2"/><path d="M7 10h.01M17 14h.01M9 12h6"/></svg>
            <span>Commissions</span>
            <span class="wd-soon">Bientot</span>
        </a>
        @endif
        @endif
        <div class="wd-nav-section">Compte</div>
        @php
        $dossierConseiller = Auth::check() && Auth::user()->role === 'conseiller' ? Auth::user()->dossierEnrolement : null;
        $dossierNecessiteAttention = $dossierConseiller && ($dossierConseiller->statut === 'rejected' || ($dossierConseiller->statut === 'onboarding' && ! empty($dossierConseiller->notes_back_office)));
        @endphp
        @if($dossierConseiller)
        <a class="{{ request()->routeIs('tenant.dossier-enrolement.*') ? 'active' : '' }}" href="{{ route('tenant.dossier-enrolement.edit') }}">
            <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
            <span>Mon dossier</span>
            @if($dossierNecessiteAttention)
            <span class="wd-nav-dot"></span>
            @endif
        </a>
        @endif
        @if(Auth::check() && Auth::user()->effectiveRole() === 'apporteur')
        <a href="#" class="disabled" aria-disabled="true" tabindex="-1">
            <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
            <span>Mon dossier</span>
            <span class="wd-soon">Bientot</span>
        </a>
        @endif
        @if(Auth::check() && Auth::user()->effectiveRole() === 'courtier')
        <a class="{{ request()->routeIs('tenant.back-office-enrolement.*') ? 'active' : '' }}" href="{{ route('tenant.back-office-enrolement.index') }}">
            <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
            <span>Dossiers d'enrôlement</span>
        </a>
        @endif
        @if(Auth::check() && Auth::user()->effectiveRole() !== 'apporteur')
        <a class="{{ request()->routeIs('tenant.cabinet') ? 'active' : '' }}" href="{{ route('tenant.cabinet') }}">
            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M4.9 4.9l2.1 2.1M17 17l2.1 2.1M2 12h3M19 12h3M4.9 19.1 7 17M17 7l2.1-2.1"/></svg>
            <span>Paramètres</span>
        </a>
        @endif
        @if(Auth::check() && Auth::user()->effectiveRole() === 'apporteur')
        <a class="{{ request()->routeIs('tenant.profil.rib.*') ? 'active' : '' }}" href="{{ route('tenant.profil.rib.edit') }}">
            <svg viewBox="0 0 24 24"><rect x="3" y="6" width="18" height="12" rx="2"/><path d="M7 10h.01M17 14h.01M9 12h6"/></svg>
            <span>Mon RIB</span>
        </a>
        @endif
        <a class="{{ request()->routeIs('tenant.profil.*') ? 'active' : '' }}" href="{{ route('tenant.profil.edit') }}">
            <svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
            <span>Profil</span>
        </a>
    </nav>
    <div class="wd-bottom-nav">
        <form method="POST" action="{{ route('tenant.logout') }}">@csrf<button>Déconnexion</button></form>
    </div>
</aside>
@if(count($newAccountRoles) > 0)
<div class="wd-newaccount-overlay" data-new-account-modal hidden>
    <div class="wd-newaccount-modal">
        <div class="wd-newaccount-head">
            <div>
                <div class="wd-eyebrow">Nouveau compte</div>
                <h3>Qui souhaitez-vous créer ?</h3>
            </div>
            <button type="button" class="wd-newaccount-close" data-new-account-close aria-label="Fermer">&times;</button>
        </div>
        <div class="wd-newaccount-choices">
            @if(in_array('conseiller', $newAccountRoles))
            <a href="{{ route('tenant.users.create', ['role' => 'conseiller']) }}" class="wd-newaccount-choice">
                <span class="wd-newaccount-choice-title">Conseiller</span>
                <span class="wd-newaccount-choice-desc">Ajouter un conseiller à votre cabinet.</span>
            </a>
            @endif
            <a href="{{ route('tenant.clients.create') }}" class="wd-newaccount-choice">
                <span class="wd-newaccount-choice-title">Client</span>
                <span class="wd-newaccount-choice-desc">Créer une nouvelle fiche client.</span>
            </a>
            @if(in_array('apporteur', $newAccountRoles))
            <a href="{{ route('tenant.users.create', ['role' => 'apporteur']) }}" class="wd-newaccount-choice">
                <span class="wd-newaccount-choice-title">Apporteur</span>
                <span class="wd-newaccount-choice-desc">Ajouter un apporteur d'affaires.</span>
            </a>
            @endif
        </div>
    </div>
</div>
@endif
<style>
.wd-newaccount-overlay{position:fixed;inset:0;background:rgba(20,17,15,.55);z-index:2000;display:flex;align-items:center;justify-content:center;padding:20px}
.wd-newaccount-overlay[hidden]{display:none}
.wd-newaccount-modal{background:#fff;border-radius:14px;max-width:460px;width:100%;padding:26px;box-shadow:0 20px 60px rgba(0,0,0,.25)}
.wd-newaccount-head{display:flex;justify-content:space-between;align-items:flex-start;gap:16px}
.wd-newaccount-head h3{margin:6px 0 0;font-size:22px;letter-spacing:-.03em}
.wd-newaccount-close{background:none;border:0;font-size:22px;line-height:1;color:#918984;cursor:pointer;padding:0 4px}
.wd-newaccount-close:hover{color:#151515}
.wd-newaccount-choices{margin-top:22px;display:grid;gap:10px}
.wd-newaccount-choice{display:block;padding:15px 16px;border:1px solid #ded9d4;border-radius:9px;text-decoration:none;color:inherit;transition:border-color .15s ease,background .15s ease}
.wd-newaccount-choice:hover{border-color:#f40087;background:#fdf2f8}
.wd-newaccount-choice-title{display:block;font-size:14px;font-weight:800;color:#151515}
.wd-newaccount-choice-desc{display:block;margin-top:3px;font-size:12px;color:#817b76}
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var overlay = document.querySelector('[data-new-account-modal]');
    var triggers = document.querySelectorAll('[data-new-account-trigger]');
    var closeBtn = document.querySelector('[data-new-account-close]');
    if (!overlay || !triggers.length) { return; }
    var mobileMenuOverlay = document.querySelector('[data-mobile-menu-overlay]');
    triggers.forEach(function (trigger) {
        trigger.addEventListener('click', function (e) {
            e.preventDefault();
            if (mobileMenuOverlay) { mobileMenuOverlay.hidden = true; }
            overlay.hidden = false;
        });
    });
    if (closeBtn) { closeBtn.addEventListener('click', function () { overlay.hidden = true; }); }
    overlay.addEventListener('click', function (e) { if (e.target === overlay) { overlay.hidden = true; } });
});
</script>
<header class="wd-topbar">
    <div class="wd-crumb">{{ $wdCrumb }}</div>
    <div class="wd-who">
        <div><strong>{{ Auth::user()->name }}</strong><small>{{ ucfirst(Auth::user()->role) }}</small></div>
        <div class="wd-top-avatar">{{ strtoupper(substr(Auth::user()->name,0,1)) }}</div>
    </div>
</header>
@php
    $mobileRole = Auth::check() ? Auth::user()->effectiveRole() : null;
@endphp
<header class="wd-mobile-topbar" data-mobile-topbar>
    <button type="button" class="wd-mobile-btn" data-mobile-menu-trigger>
        <svg viewBox="0 0 24 24"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
        <span>Menu</span>
    </button>
    <div class="wd-mobile-logo"><b>W</b>endee</div>
    @if(count($newAccountRoles) > 0)
        <button type="button" class="wd-mobile-add-btn" data-new-account-trigger aria-label="Nouveau compte" title="Nouveau compte">
            <svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
        </button>
    @elseif($mobileRole === 'apporteur')
        <a href="{{ route('tenant.clients.create') }}" class="wd-mobile-add-btn" aria-label="Nouveau compte" title="Nouveau compte">
            <svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
        </a>
    @else
        <span class="wd-mobile-add-spacer" aria-hidden="true"></span>
    @endif
</header>
<div class="wd-mobile-menu-overlay" data-mobile-menu-overlay hidden>
    <div class="wd-mobile-menu-panel">
        <div class="wd-mobile-menu-head">
            <div class="wd-mobile-menu-logo"><b>W</b>endee</div>
            <button type="button" class="wd-mobile-menu-close" data-mobile-menu-close aria-label="Fermer">&times;</button>
        </div>
        <nav class="wd-mobile-menu-nav">
            <div class="wd-mobile-menu-section">Général</div>
            <a class="{{ request()->routeIs('tenant.dashboard') || ($mobileRole === 'apporteur' && request()->routeIs('tenant.portefeuille.*')) ? 'active' : '' }}" href="{{ $mobileRole === 'apporteur' ? route('tenant.portefeuille.index') : route('tenant.dashboard') }}">
                <svg viewBox="0 0 24 24"><path d="M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h6v6h-6z"/></svg>
                <span>Tableau de bord</span>
            </a>
            @if($mobileRole === 'courtier' || $mobileRole === 'conseiller')
            <a class="{{ request()->routeIs('tenant.portefeuille.*') ? 'active' : '' }}" href="{{ route('tenant.portefeuille.index') }}">
                <svg viewBox="0 0 24 24"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M3 12h18"/></svg>
                <span>Portefeuille</span>
            </a>
            @endif
            @if($mobileRole === 'courtier' || $mobileRole === 'conseiller')
            <a href="#" class="{{ request()->routeIs('tenant.users.*') ? 'active' : '' }}" data-new-account-trigger>
                <svg viewBox="0 0 24 24"><circle cx="9" cy="7" r="4"/><path d="M3 21v-2a6 6 0 0 1 6-6M16 11v6M13 14h6"/></svg>
                <span>Créer un utilisateur</span>
            </a>
            @elseif($mobileRole === 'apporteur')
            <a class="{{ request()->routeIs('tenant.clients.create') ? 'active' : '' }}" href="{{ route('tenant.clients.create') }}">
                <svg viewBox="0 0 24 24"><circle cx="9" cy="7" r="4"/><path d="M3 21v-2a6 6 0 0 1 6-6M16 11v6M13 14h6"/></svg>
                <span>Créer un client</span>
            </a>
            @endif
            @if($mobileRole === 'courtier' || $mobileRole === 'conseiller')
            <a href="{{ route('tenant.rendez-vous.index') }}" class="{{ request()->routeIs('tenant.rendez-vous.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M8 3v4M16 3v4M3 11h18"/></svg>
                <span>Rendez-vous</span>
            </a>
            @endif
            @if($mobileRole === 'courtier')
            <div class="wd-mobile-menu-section">Activité</div>
            <a class="{{ request()->routeIs('tenant.performances.*') ? 'active' : '' }}" href="{{ route('tenant.performances.index') }}">
                <svg viewBox="0 0 24 24"><path d="M5 20v-6M12 20V9M19 20V4"/></svg>
                <span>Patrimoine sous gestion</span>
            </a>
            <a class="{{ request()->routeIs('tenant.revenus.*') ? 'active' : '' }}" href="{{ route('tenant.revenus.index') }}">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M15 8.5c-.8-.9-1.8-1.5-3.2-1.5C10 7 9 8 9 9.3c0 1.5 1.4 2.1 3 2.7 1.7.6 3 1.3 3 2.8 0 1.3-1.1 2.2-3 2.2-1.4 0-2.6-.5-3.5-1.5M12 5v14"/></svg>
                <span>Revenus</span>
            </a>
            <a class="{{ request()->routeIs('tenant.commissions.*') ? 'active' : '' }}" href="{{ route('tenant.commissions.index') }}">
                <svg viewBox="0 0 24 24"><rect x="3" y="6" width="18" height="12" rx="2"/><path d="M7 10h.01M17 14h.01M9 12h6"/></svg>
                <span>Commissions</span>
            </a>
            @endif
            <div class="wd-mobile-menu-section">Compte</div>
            @if($mobileRole === 'courtier')
            <a class="{{ request()->routeIs('tenant.cabinet') ? 'active' : '' }}" href="{{ route('tenant.cabinet') }}">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M4.9 4.9l2.1 2.1M17 17l2.1 2.1M2 12h3M19 12h3M4.9 19.1 7 17M17 7l2.1-2.1"/></svg>
                <span>Paramètres</span>
            </a>
            @endif
            <a class="{{ request()->routeIs('tenant.profil.*') ? 'active' : '' }}" href="{{ route('tenant.profil.edit') }}">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                <span>Profil</span>
            </a>
            <div class="wd-mobile-menu-sep"></div>
            <form method="POST" action="{{ route('tenant.logout') }}">
                @csrf
                <button type="submit">
                    <svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg>
                    <span>Déconnexion</span>
                </button>
            </form>
        </nav>
    </div>
</div>
<style>
.wd-mobile-topbar{display:none}
@media(max-width:768px){
.wd-sidebar{display:none!important}
.wd-topbar{display:none!important}
.wd-main{margin-left:0!important;padding-top:0!important;padding-bottom:76px!important}
.wd-mobile-topbar{
    display:flex;align-items:center;justify-content:space-between;
    position:fixed;bottom:0;left:0;right:0;height:56px;z-index:1100;
    background:#242424;color:#fff;padding:0 12px;
    transition:transform .3s ease;transform:translateY(0)
}
.wd-mobile-topbar.wd-mobile-topbar--hidden{transform:translateY(100%)}
.wd-mobile-logo{font-size:15px;font-weight:800;letter-spacing:-.05em}
.wd-mobile-logo b{color:var(--pink)}
.wd-mobile-btn{display:flex;align-items:center;gap:6px;background:none;border:0;color:#fff;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;padding:8px 6px;cursor:pointer;font-family:inherit}
.wd-mobile-btn svg{width:16px;height:16px;flex:0 0 16px;fill:none;stroke:currentColor;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}
.wd-mobile-add-btn{display:flex;align-items:center;justify-content:center;width:29px;height:29px;flex:0 0 29px;border-radius:50%;background:var(--pink);color:#fff;border:0;cursor:pointer;text-decoration:none;transition:transform .15s ease}
.wd-mobile-add-btn:hover,.wd-mobile-add-btn:active{transform:scale(1.06)}
.wd-mobile-add-btn svg{width:14px;height:14px;fill:none;stroke:currentColor;stroke-width:2.4;stroke-linecap:round;stroke-linejoin:round}
.wd-mobile-add-spacer{width:29px;height:29px;flex:0 0 29px}
.wd-new-client{display:none!important}
.wd-mobile-menu-logo{font-size:18px;font-weight:800;letter-spacing:-.05em;color:#151515}
.wd-mobile-menu-logo b{color:var(--pink)}
.wd-mobile-menu-section{margin:16px 4px 6px;font-size:9px;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:#a89f99}
.wd-mobile-menu-section:first-child{margin-top:0}
.wd-mobile-menu-overlay{position:fixed;inset:0;background:rgba(20,17,15,.55);z-index:1900;display:flex;align-items:center;justify-content:center;padding:20px}
.wd-mobile-menu-overlay[hidden]{display:none}
.wd-mobile-menu-panel{background:#fff;color:#151515;width:100%;max-width:360px;max-height:78vh;overflow-y:auto;border-radius:14px;padding:22px;box-shadow:0 20px 60px rgba(0,0,0,.25)}
.wd-mobile-menu-head{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:18px}
.wd-mobile-menu-head .wd-logo{padding:0}
.wd-mobile-menu-head .wd-logo small{color:#96908b}
.wd-mobile-menu-close{background:none;border:0;font-size:24px;line-height:1;color:#918984;cursor:pointer;padding:0 4px}
.wd-mobile-menu-close:hover{color:#151515}
.wd-mobile-menu-nav{display:grid;gap:3px}
.wd-mobile-menu-nav a,.wd-mobile-menu-nav button{display:flex;align-items:center;gap:11px;width:100%;padding:11px;margin:0;border-radius:8px;color:#4a4542;font-size:13px;text-decoration:none;background:none;border:0;text-align:left;font-family:inherit;cursor:pointer}
.wd-mobile-menu-nav a svg,.wd-mobile-menu-nav button svg{width:18px;height:18px;flex:0 0 18px;fill:none;stroke:currentColor;stroke-width:1.7;stroke-linecap:round;stroke-linejoin:round;color:#9a928d}
.wd-mobile-menu-nav a.active{background:rgba(244,0,135,.10);color:#151515}
.wd-mobile-menu-nav a.active svg{color:var(--pink)}
.wd-mobile-menu-nav a:hover,.wd-mobile-menu-nav button:hover{background:#f3f1ee;color:#151515}
.wd-mobile-menu-sep{margin:10px 4px;border-top:1px solid #eeeae7}
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var mobileTopbar = document.querySelector('[data-mobile-topbar]');
    if (mobileTopbar) {
        var lastY = Math.max(0, window.scrollY);
        var ticking = false;
        window.addEventListener('scroll', function () {
            if (ticking) { return; }
            ticking = true;
            window.requestAnimationFrame(function () {
                var y = Math.max(0, window.scrollY);
                var maxY = document.documentElement.scrollHeight - window.innerHeight;
                var delta = y - lastY;
                if (Math.abs(delta) > 6) {
                    if (delta > 0 && y > 80 && y < maxY - 4) {
                        mobileTopbar.classList.add('wd-mobile-topbar--hidden');
                    } else {
                        mobileTopbar.classList.remove('wd-mobile-topbar--hidden');
                    }
                    lastY = y;
                }
                ticking = false;
            });
        }, { passive: true });
    }

    var mobileMenuOverlay = document.querySelector('[data-mobile-menu-overlay]');
    var mobileMenuTrigger = document.querySelector('[data-mobile-menu-trigger]');
    var mobileMenuClose = document.querySelector('[data-mobile-menu-close]');
    if (mobileMenuOverlay && mobileMenuTrigger) {
        mobileMenuTrigger.addEventListener('click', function () { mobileMenuOverlay.hidden = false; });
        if (mobileMenuClose) { mobileMenuClose.addEventListener('click', function () { mobileMenuOverlay.hidden = true; }); }
        mobileMenuOverlay.addEventListener('click', function (e) { if (e.target === mobileMenuOverlay) { mobileMenuOverlay.hidden = true; } });
    }
});
</script>
