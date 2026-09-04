@php
    $newAccountRoles = Auth::check() ? Auth::user()->creatableUserRoles() : [];
    $wdCrumb = match (true) {
        request()->routeIs('tenant.dashboard') => 'Portefeuille · Points à traiter',
        request()->routeIs('tenant.cabinet') => 'Cabinet · Conformité & pilotage',
        request()->routeIs('tenant.users.*') => 'Équipe · Nouveau compte',
        request()->routeIs('tenant.clients.*') => 'Portefeuille · Clients',
        request()->routeIs('tenant.portefeuille-cabinet.*') => 'Cabinet · Portefeuille global',
        request()->routeIs('tenant.performances.*') => 'Cabinet · Patrimoine sous gestion',
        request()->routeIs('tenant.revenus.*') => 'Cabinet · Revenus',
        request()->routeIs('tenant.commissions.*') => 'Cabinet · Commissions',
        request()->routeIs('tenant.profil.rib.*') => 'Compte · Mon RIB',
        default => 'Wendee',
    };
@endphp
<aside class="wd-sidebar">
    <div class="wd-logo"><b>W</b>endee<small>OS du conseiller patrimonial</small></div>
    <nav class="wd-nav">
        <div class="wd-nav-section">Général</div>
        <a class="{{ request()->routeIs('tenant.dashboard') ? 'active' : '' }}" href="{{ route('tenant.dashboard') }}">
            <svg viewBox="0 0 24 24"><path d="M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h6v6h-6z"/></svg>
            <span>Tableau de bord</span>
        </a>
        <a class="{{ request()->routeIs('tenant.clients.*') ? 'active' : '' }}" href="{{ route('tenant.clients.index') }}">
            <svg viewBox="0 0 24 24"><path d="M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM3 21v-2a6 6 0 0 1 12 0v2M17 11a4 4 0 0 0 0-8M16 13a6 6 0 0 1 5 6v2"/></svg>
            <span>Mon portefeuille</span>
        </a>
        @if(Auth::check() && Auth::user()->effectiveRole() === 'courtier')
        <a class="{{ request()->routeIs('tenant.portefeuille-cabinet.*') ? 'active' : '' }}" href="{{ route('tenant.portefeuille-cabinet.index') }}">
            <svg viewBox="0 0 24 24"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M3 12h18"/></svg>
            <span>Portefeuille cabinet</span>
        </a>
        @endif
        <a href="#" class="disabled" aria-disabled="true" tabindex="-1">
            <svg viewBox="0 0 24 24"><circle cx="7" cy="12" r="3"/><circle cx="17" cy="12" r="3"/><path d="M10 12h4"/></svg>
            <span>Mes apporteurs</span>
            <span class="wd-soon">Bientot</span>
        </a>
        @if(count($newAccountRoles) > 0)
        <a href="#" class="{{ request()->routeIs('tenant.users.*') ? 'active' : '' }}" data-new-account-trigger>
            <svg viewBox="0 0 24 24"><circle cx="9" cy="7" r="4"/><path d="M3 21v-2a6 6 0 0 1 6-6M16 11v6M13 14h6"/></svg>
            <span>Créer un utilisateur</span>
        </a>
        @endif
        <a href="{{ route('tenant.calendrier.index') }}" class="{{ request()->routeIs('tenant.calendrier.*') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M8 3v4M16 3v4M3 11h18"/></svg>
            <span>Rendez-vous</span>
        </a>
        <div class="wd-nav-section">Activité</div>
        @if(Auth::check() && Auth::user()->effectiveRole() === 'courtier')
        <a class="{{ request()->routeIs('tenant.cabinet') ? 'active' : '' }}" href="{{ route('tenant.cabinet') }}">
            <svg viewBox="0 0 24 24"><path d="M12 3l7 3v6c0 5-3 8-7 9-4-1-7-4-7-9V6z"/><path d="M9 12l2 2 4-4"/></svg>
            <span>Cabinet</span>
        </a>
        @endif
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
        <div class="wd-nav-section">Compte</div>
        @if(Auth::check() && Auth::user()->effectiveRole() === 'apporteur')
        <a class="{{ request()->routeIs('tenant.profil.rib.*') ? 'active' : '' }}" href="{{ route('tenant.profil.rib.edit') }}">
            <svg viewBox="0 0 24 24"><rect x="3" y="6" width="18" height="12" rx="2"/><path d="M7 10h.01M17 14h.01M9 12h6"/></svg>
            <span>Mon RIB</span>
        </a>
        @endif
        <a class="{{ request()->routeIs('tenant.cabinet') ? 'active' : '' }}" href="{{ route('tenant.cabinet') }}">
            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M4.9 4.9l2.1 2.1M17 17l2.1 2.1M2 12h3M19 12h3M4.9 19.1 7 17M17 7l2.1-2.1"/></svg>
            <span>Paramètres</span>
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
    triggers.forEach(function (trigger) {
        trigger.addEventListener('click', function (e) {
            e.preventDefault();
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
