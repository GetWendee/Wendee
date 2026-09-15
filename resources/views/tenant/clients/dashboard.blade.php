<x-tenant-app-layout>
@include('tenant.clients.partials.header-tabs', ['active' => 'dashboard'])
@php
    $items = $dossierStatus['items'];
    $kycJoursRestants = $kycEcheance ? (int) now()->startOfDay()->diffInDays($kycEcheance->copy()->startOfDay(), false) : null;
@endphp
<style>
.wd-tdb-grid{display:grid;grid-template-columns:2fr 1fr;gap:22px;align-items:start;max-width:1540px;margin:0 auto;padding:0 34px 60px;}
@media(max-width:900px){.wd-tdb-grid{grid-template-columns:1fr;}}
.wd-panel{background:#fff;border:1px solid var(--line);border-radius:12px;overflow:hidden;margin-bottom:22px;}
.wd-panel-head{padding:18px 20px 0;}
.wd-panel-body{padding:18px 20px 20px;}
.wd-tdb-kpis{display:grid;grid-template-columns:repeat(4,1fr);}
@media(max-width:640px){.wd-tdb-kpis{grid-template-columns:1fr 1fr;}}
.wd-tdb-kpi{padding:18px 16px;border-right:1px solid var(--line);border-top:1px solid var(--line);}
.wd-tdb-kpi:nth-child(4n){border-right:0;}
@media(max-width:640px){.wd-tdb-kpi:nth-child(2n){border-right:0;}.wd-tdb-kpi:nth-child(4n){border-right:1px solid var(--line);}}
.wd-tdb-kpi span{display:block;color:var(--muted);font-size:9px;text-transform:uppercase;letter-spacing:.1em;font-weight:800;}
.wd-tdb-kpi strong{display:block;margin-top:8px;font-size:15px;letter-spacing:-.02em;}
.wd-tdb-kpi strong.ok{color:var(--green);}
.wd-tdb-kpi strong.warn{color:var(--amber);}
.wd-tdb-kpi strong.ko{color:var(--red);}
.wd-tdb-journal{list-style:none;margin:0;padding:0;}
.wd-tdb-journal li{padding:12px 0;border-top:1px solid var(--line);font-size:12.5px;color:var(--ink);display:flex;justify-content:space-between;align-items:flex-start;gap:12px;}
.wd-tdb-journal li:first-child{border-top:0;}
.wd-tdb-journal time{display:block;color:var(--muted);font-size:10px;text-transform:uppercase;letter-spacing:.07em;font-weight:700;margin-bottom:4px;}
.wd-tdb-journal-texte{flex:1;}
.wd-tdb-journal-lu{flex:0 0 auto;background:none;border:1px solid var(--line);border-radius:7px;padding:5px 10px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);cursor:pointer;font-family:inherit;}
.wd-tdb-journal-lu:hover{border-color:var(--pink);color:var(--pink);}
.wd-tdb-empty{color:var(--muted);font-size:12.5px;padding:6px 0;}
.wd-tdb-rdv{display:grid;gap:10px;}
.wd-tdb-rdv-tile{border:1px solid var(--line);border-radius:9px;padding:12px 14px;display:flex;justify-content:space-between;align-items:center;gap:12px;}
.wd-tdb-rdv-date{font-size:13px;font-weight:800;}
.wd-tdb-rdv-sujet{font-size:11.5px;color:var(--muted);margin-top:2px;}
.wd-tdb-msg-form textarea{width:100%;min-height:120px;border:1px solid var(--line);border-radius:9px;padding:12px 14px;font:inherit;font-size:12.5px;resize:vertical;box-sizing:border-box;}
.wd-tdb-msg-form button{margin-top:12px;}
.wd-tdb-msg-status{margin-top:10px;font-size:11.5px;color:var(--green);font-weight:700;}
.wd-tdb-dropzone{margin-top:10px;border:1.5px dashed var(--line);border-radius:9px;padding:16px 14px;text-align:center;cursor:pointer;transition:border-color .15s ease,background .15s ease;}
.wd-tdb-dropzone:hover,.wd-tdb-dropzone.wd-tdb-dropzone-actif{border-color:var(--pink);background:#fdf2f8;}
.wd-tdb-dropzone-texte{margin:0;font-size:11.5px;color:var(--muted);}
.wd-tdb-dropzone-texte span{color:var(--pink);font-weight:700;text-decoration:underline;}
</style>
<div class="wd-tdb-grid">
<div>

<div class="wd-panel">
    <div class="wd-panel-head">
        <div class="wd-eyebrow">Général</div>
        <h1 style="margin:6px 0 14px;font-size:22px;letter-spacing:-.03em;">Tableau de bord</h1>
    </div>
    <div class="wd-tdb-kpis">
        <div class="wd-tdb-kpi">
            <span>KYC</span>
            <strong class="{{ ! $items['kyc']['done'] ? 'ko' : ($items['kyc']['stale'] ? 'warn' : 'ok') }}">
                {{ ! $items['kyc']['done'] ? 'À compléter' : ($items['kyc']['stale'] ? 'À revalider' : 'À jour') }}
            </strong>
        </div>
        <div class="wd-tdb-kpi">
            <span>Patrimoine</span>
            <strong class="{{ ! $items['pat']['done'] ? 'ko' : ($items['pat']['stale'] ? 'warn' : 'ok') }}">
                {{ ! $items['pat']['done'] ? 'À compléter' : ($items['pat']['stale'] ? 'À revalider' : 'À jour') }}
            </strong>
        </div>
        <div class="wd-tdb-kpi">
            <span>Profil investisseur</span>
            <strong class="{{ ! $items['inv']['done'] ? 'ko' : ($items['inv']['stale'] ? 'warn' : 'ok') }}">
                {{ ! $items['inv']['done'] ? 'À compléter' : ($items['inv']['stale'] ? 'À revalider' : 'À jour') }}
            </strong>
        </div>
        <div class="wd-tdb-kpi">
            <span>Échéance KYC</span>
            @if($kycJoursRestants === null)
            <strong>-</strong>
            @elseif($kycJoursRestants < 0)
            <strong class="ko">Expirée</strong>
            @else
            <strong class="{{ $kycJoursRestants <= 30 ? 'warn' : 'ok' }}">{{ $kycJoursRestants }} j</strong>
            @endif
        </div>
    </div>
</div>

<div class="wd-panel">
    <div class="wd-panel-head">
        <div class="wd-eyebrow">Actualité</div>
        <h2 style="margin:6px 0 0;font-size:16px;">Notifications</h2>
    </div>
    <div class="wd-panel-body">
        @if($journal->isEmpty())
        <p class="wd-tdb-empty">Aucune actualité pour le moment.</p>
        @else
        <ul class="wd-tdb-journal">
            @foreach($journal as $entree)
            <li>
                <div class="wd-tdb-journal-texte">
                    <time>{{ $entree['date']->translatedFormat('d F Y à H:i') }}</time>
                    {{ $entree['texte'] }}
                </div>
                <form method="POST" action="{{ route('tenant.clients.dashboard.journal.lu', $client) }}">
                    @csrf
                    <input type="hidden" name="cle" value="{{ $entree['cle'] }}">
                    <button type="submit" class="wd-tdb-journal-lu">Lu</button>
                </form>
            </li>
            @endforeach
        </ul>
        @endif
    </div>
</div>

</div>
<div>

<div class="wd-panel">
    <div class="wd-panel-head">
        <div class="wd-eyebrow">Contact</div>
        <h2 style="margin:6px 0 0;font-size:16px;">Message à mon conseiller</h2>
    </div>
    <div class="wd-panel-body">
        @if(session('status') === 'message-envoye')
        <p class="wd-tdb-msg-status">Message envoyé à votre conseiller.</p>
        @endif
        <form class="wd-tdb-msg-form" method="POST" action="{{ route('tenant.clients.dashboard.message', $client) }}" enctype="multipart/form-data">
            @csrf
            <textarea name="message" placeholder="Écrivez votre message ici..." required>{{ old('message') }}</textarea>
            @error('message')
            <p style="color:var(--red);font-size:11.5px;margin-top:6px;">{{ $message }}</p>
            @enderror
            <div class="wd-tdb-dropzone" data-dropzone>
                <input type="file" name="piece_jointe" id="wd-tdb-piece-jointe" data-dropzone-input hidden>
                <p class="wd-tdb-dropzone-texte" data-dropzone-texte>Glissez-déposez une pièce jointe ici, ou <span>parcourir</span></p>
            </div>
            @error('piece_jointe')
            <p style="color:var(--red);font-size:11.5px;margin-top:6px;">{{ $message }}</p>
            @enderror
            <button type="submit" class="wd-btn-dark">Envoyer</button>
        </form>
    </div>
</div>

<div class="wd-panel">
    <div class="wd-panel-head">
        <div class="wd-eyebrow">Agenda</div>
        <h2 style="margin:6px 0 0;font-size:16px;">Rendez-vous</h2>
    </div>
    <div class="wd-panel-body">
        @if($rendezVousAVenir->isEmpty())
        <p class="wd-tdb-empty">Aucun rendez-vous pour le moment.</p>
        @else
        <div class="wd-tdb-rdv">
            @foreach($rendezVousAVenir as $rdv)
            <div class="wd-tdb-rdv-tile">
                <div>
                    <div class="wd-tdb-rdv-date">{{ $rdv->starts_at->translatedFormat('d F Y · H:i') }}</div>
                    @if($rdv->sujet)
                    <div class="wd-tdb-rdv-sujet">{{ $rdv->sujet }}</div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif
        <a class="wd-btn-dark" style="margin-top:14px;" href="{{ route('tenant.rendez-vous.index') }}">Voir mes rendez-vous</a>
    </div>
</div>

</div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var zone = document.querySelector('[data-dropzone]');
    if (!zone) { return; }
    var input = zone.querySelector('[data-dropzone-input]');
    var texte = zone.querySelector('[data-dropzone-texte]');
    var texteDefaut = texte.innerHTML;

    function afficherFichier(fichier) {
        texte.textContent = fichier ? fichier.name : '';
        if (!fichier) { texte.innerHTML = texteDefaut; }
    }

    zone.addEventListener('click', function () { input.click(); });

    input.addEventListener('change', function () {
        afficherFichier(input.files[0] || null);
    });

    ['dragenter', 'dragover'].forEach(function (evenement) {
        zone.addEventListener(evenement, function (e) {
            e.preventDefault();
            e.stopPropagation();
            zone.classList.add('wd-tdb-dropzone-actif');
        });
    });

    ['dragleave', 'drop'].forEach(function (evenement) {
        zone.addEventListener(evenement, function (e) {
            e.preventDefault();
            e.stopPropagation();
            zone.classList.remove('wd-tdb-dropzone-actif');
        });
    });

    zone.addEventListener('drop', function (e) {
        var fichiers = e.dataTransfer.files;
        if (fichiers.length > 0) {
            input.files = fichiers;
            afficherFichier(fichiers[0]);
        }
    });
});
</script>
</x-tenant-app-layout>
