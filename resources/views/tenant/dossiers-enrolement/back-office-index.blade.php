<x-tenant-app-layout>
<style>
body > div > nav,
body > div > header{display:none!important}
html,body{margin:0!important;background:#f3f1ee!important}
:root{--pink:#f40087;--muted:#817b76;--line:#ded9d4}
*{box-sizing:border-box}
.wd-wrap{max-width:1000px;margin:auto;padding:30px 34px 60px}
.wd-eyebrow{font-size:12px;color:var(--pink);font-weight:850;letter-spacing:.2em;text-transform:uppercase}
.wd-head h1{font-size:38px;line-height:1;margin:8px 0 0;letter-spacing:-.05em;font-weight:650}
.wd-head p{color:var(--muted);margin:10px 0 0;font-size:15px}
.wd-list{margin-top:28px;background:#fff;border:1px solid #ded9d4;border-radius:10px;overflow:hidden}
.wd-row{display:flex;justify-content:space-between;align-items:center;padding:16px 20px;border-bottom:1px solid #eeeae7;text-decoration:none;color:inherit}
.wd-row:last-child{border-bottom:0}
.wd-row:hover{background:#f9f8f7}
.wd-row-nom{font-weight:700;font-size:14px;color:#151515}
.wd-row-meta{color:#817b76;font-size:11px;margin-top:3px}
.wd-badge{display:inline-block;padding:5px 11px;border-radius:20px;font-size:10px;font-weight:800;letter-spacing:.06em;text-transform:uppercase}
.wd-badge-invited{background:#f3f1ee;color:#817b76}
.wd-badge-onboarding{background:#fdf3e2;color:#b8860b}
.wd-badge-pending_validation{background:#eaf1fb;color:#2f5fa8}
.wd-badge-contract_pending{background:#f3e9fb;color:#7a3fb0}
.wd-badge-active{background:#f3f9f4;color:#4d8760}
.wd-badge-rejected{background:#fbeceb;color:#b94d4d}
.wd-empty{padding:40px 20px;text-align:center;color:#817b76;font-size:13px}
</style>
@php
$badgeLabels = ['invited' => 'Invitation envoyée', 'onboarding' => 'Dossier en cours', 'pending_validation' => 'En validation', 'contract_pending' => 'Convention à signer', 'active' => 'Actif', 'rejected' => 'Refusé'];
@endphp
<div class="wd-wrap">
    <section class="wd-head">
        <div class="wd-eyebrow">Équipe</div>
        <h1>Dossiers d'enrôlement.</h1>
        <p>Suivi des dossiers d'enrôlement de vos conseillers mandataires.</p>
    </section>

    <div class="wd-list">
        @forelse($dossiers as $dossier)
        <a href="{{ route('tenant.back-office-enrolement.show', $dossier) }}" class="wd-row">
            <div>
                <div class="wd-row-nom">{{ $dossier->user->name }}</div>
                <div class="wd-row-meta">{{ $dossier->statut_demande ? implode(', ', $dossier->statut_demande) : 'Statut non précisé' }}</div>
            </div>
            <span class="wd-badge wd-badge-{{ $dossier->statut }}">{{ $badgeLabels[$dossier->statut] ?? $dossier->statut }}</span>
        </a>
        @empty
        <div class="wd-empty">Aucun dossier d'enrôlement pour le moment.</div>
        @endforelse
    </div>
</div>
</x-tenant-app-layout>
