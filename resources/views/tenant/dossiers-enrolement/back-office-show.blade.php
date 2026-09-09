<x-tenant-app-layout>
<style>
body > div > nav,
body > div > header{display:none!important}
html,body{margin:0!important;background:#f3f1ee!important}
:root{--pink:#f40087;--muted:#817b76;--line:#ded9d4;--red:#b94d4d;--green:#4d8760}
*{box-sizing:border-box}
.wd-wrap{max-width:1000px;margin:auto;padding:30px 34px 60px}
.wd-head{display:flex;justify-content:space-between;align-items:end}
.wd-eyebrow{font-size:12px;color:var(--pink);font-weight:850;letter-spacing:.2em;text-transform:uppercase}
.wd-head h1{font-size:38px;line-height:1;margin:8px 0 0;letter-spacing:-.05em;font-weight:650}
.wd-nav-link{color:#817b76;font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.08em;text-decoration:none}
.wd-badge{display:inline-block;padding:5px 11px;border-radius:20px;font-size:10px;font-weight:800;letter-spacing:.06em;text-transform:uppercase}
.wd-badge-invited{background:#f3f1ee;color:#817b76}
.wd-badge-onboarding{background:#fdf3e2;color:#b8860b}
.wd-badge-pending_validation{background:#eaf1fb;color:#2f5fa8}
.wd-badge-contract_pending{background:#f3e9fb;color:#7a3fb0}
.wd-badge-active{background:#f3f9f4;color:#4d8760}
.wd-badge-rejected{background:#fbeceb;color:#b94d4d}
.wd-user-success{margin-top:28px;padding:14px 18px;background:#f3f9f4;border:1px solid #d7e8da;border-radius:8px;color:#4d8760;font-size:12px;font-weight:700}
.wd-block{margin-top:28px;padding:23px;background:#fff;border:1px solid #ded9d4;border-radius:10px}
.wd-section-title{font-size:15px;font-weight:800;color:#151515;margin:0 0 14px}
.wd-check-row{display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid #eeeae7;font-size:13px}
.wd-check-row:last-child{border-bottom:0}
.wd-check-pastille{display:inline-block;width:8px;height:8px;border-radius:50%;margin-right:10px}
.wd-check-ok .wd-check-pastille{background:#4d8760}
.wd-check-attention .wd-check-pastille{background:#b8860b}
.wd-check-bloquant .wd-check-pastille{background:#b94d4d}
.wd-check-na .wd-check-pastille{background:#c9c3be}
.wd-actions{display:flex;gap:10px;flex-wrap:wrap;margin-top:16px}
.wd-btn{display:inline-flex;align-items:center;gap:8px;padding:11px 18px;border-radius:7px;border:0;font-size:11px;font-weight:800;letter-spacing:.02em;cursor:pointer;font-family:inherit;text-decoration:none}
.wd-btn-dark{background:#242424;color:#fff}
.wd-btn-dark:hover{background:#171717}
.wd-btn-red{background:#fbeceb;color:#b94d4d}
.wd-btn-outline{background:#fff;color:#242424;border:1px solid #ded9d4}
.wd-field{margin-top:10px}
.wd-field label{display:block;color:#9a928d;font-size:8px;font-weight:800;letter-spacing:.12em;text-transform:uppercase;margin-bottom:6px}
.wd-field textarea{width:100%;border:1px solid #ded9d4;border-radius:7px;padding:9px 11px;font-size:13px;color:#242424;font-family:inherit;min-height:70px}
.wd-just-row{display:flex;justify-content:space-between;padding:9px 0;border-bottom:1px solid #eeeae7;font-size:12px}
.wd-just-row:last-child{border-bottom:0}
.wd-modal-overlay{position:fixed;inset:0;background:rgba(21,21,21,.5);display:flex;align-items:center;justify-content:center;z-index:1000;padding:20px}
.wd-modal{background:#fff;border-radius:10px;max-width:480px;width:100%;max-height:80vh;overflow-y:auto;padding:22px}
.wd-modal-title{font-size:14px;font-weight:800;color:#151515;margin-bottom:14px}
.wd-modal-check{display:flex;align-items:center;gap:9px;padding:9px 0;border-bottom:1px solid #eeeae7;font-size:13px;cursor:pointer}
.wd-modal-check:last-child{border-bottom:0}
.wd-pastille{display:inline-block;width:8px;height:8px;border-radius:50%;flex:0 0 8px}
.wd-pastille-ok{background:#4d8760}
.wd-pastille-attention{background:#b8860b}
.wd-pastille-bloquant{background:#b94d4d}
.wd-pastille-na{background:#c9c3be}
</style>
@php
$badgeLabels = ['invited' => 'Invitation envoyée', 'onboarding' => 'Dossier en cours', 'pending_validation' => 'En validation', 'contract_pending' => 'Convention à signer', 'active' => 'Actif', 'rejected' => 'Non validé pour le moment'];
$typesJustificatifs = ['identite' => "Pièce d'identité", 'orias' => 'Attestation ORIAS', 'diplome' => 'Diplôme', 'rcp' => 'Attestation RCP', 'garantie_financiere' => 'Attestation de garantie financière', 'kbis' => 'Extrait Kbis'];
@endphp
<div class="wd-wrap">
    <section class="wd-head">
        <div>
            <div class="wd-eyebrow">Enrôlement</div>
            <h1>{{ $dossier->user->name }}</h1>
            <div style="margin-top:14px;"><span class="wd-badge wd-badge-{{ $dossier->statut }}">{{ $badgeLabels[$dossier->statut] ?? $dossier->statut }}</span></div>
        </div>
        <div><a href="{{ route('tenant.back-office-enrolement.index') }}" class="wd-nav-link">Retour aux dossiers</a></div>
    </section>

    @if(session('status'))
    <section class="wd-user-success">{{ session('status') }}</section>
    @endif

    @if($dossier->checks_conformite)
    <section class="wd-block">
        <div class="wd-section-title">Conformité réglementaire ({{ $dossier->conformite_reglementaire === 'conforme' ? 'conforme' : 'non conforme' }})</div>
        @foreach($dossier->checks_conformite as $check)
        <div class="wd-check-row wd-check-{{ $check['statut'] }}">
            <span><span class="wd-check-pastille"></span>{{ $check['label'] }}</span>
        </div>
        @endforeach
    </section>
    @endif

    <section class="wd-block">
        <div class="wd-section-title">Justificatifs</div>
        @forelse($dossier->justificatifs as $justificatif)
        <div class="wd-just-row">
            <span>{{ $typesJustificatifs[$justificatif->type] ?? $justificatif->type }} v{{ $justificatif->version }}</span>
            <span style="color:#817b76;">{{ $justificatif->nom_original }}</span>
        </div>
        @empty
        <p style="color:#817b76;font-size:12px;">Aucun justificatif déposé.</p>
        @endforelse
    </section>

    @if(in_array($dossier->statut, ['pending_validation', 'onboarding']))
    <section class="wd-block">
        <div class="wd-section-title">Décision</div>
        <div class="wd-actions">
            <form method="POST" action="{{ route('tenant.back-office-enrolement.valider', $dossier) }}">
                @csrf
                <button type="submit" class="wd-btn wd-btn-dark">Valider l'enrôlement</button>
            </form>
        </div>

        <div class="wd-field">
            <form method="POST" action="{{ route('tenant.back-office-enrolement.demander-piece', $dossier) }}">
                @csrf
                <label>Demander une pièce complémentaire</label>
                <textarea name="notes_back_office" placeholder="Précisez la pièce ou l'information manquante" required></textarea>
                <div class="wd-actions">
                    <button type="submit" class="wd-btn wd-btn-outline">Envoyer la demande</button>
                </div>
            </form>
        </div>

        <div class="wd-field" x-data="{ showModal: false, selected: @js(collect($dossier->checks_conformite ?? [])->filter(fn ($c) => ($c['statut'] ?? 'ok') !== 'ok')->pluck('label')->values()) }">
            <form method="POST" action="{{ route('tenant.back-office-enrolement.refuser', $dossier) }}" @submit="
                if (selected.length) {
                    const liste = selected.map(s => '- ' + s).join('\n');
                    const libre = $refs.motifTextarea.value.trim();
                    $refs.motifTextarea.value = liste + (libre ? '\n\n' + libre : '');
                }
            ">
                @csrf
                <label>Non valider pour le moment</label>

                @if($dossier->checks_conformite)
                <button type="button" @click="showModal = true" class="wd-btn wd-btn-outline" style="margin-bottom:10px;">
                    Choisir les points à corriger<span x-show="selected.length" x-text="' (' + selected.length + ')'"></span>
                </button>
                @endif

                <textarea name="refuse_motif" x-ref="motifTextarea" placeholder="Expliquez au conseiller ce qui doit être corrigé" required></textarea>
                <div style="color:#817b76;font-size:11px;margin-top:4px;">Le motif sera visible par le conseiller sur son dossier. Il pourra corriger les informations et soumettre à nouveau.</div>
                <div class="wd-actions">
                    <button type="submit" class="wd-btn wd-btn-red">Non valider</button>
                </div>

                @if($dossier->checks_conformite)
                <div x-show="showModal" x-cloak class="wd-modal-overlay" @click.self="showModal = false">
                    <div class="wd-modal">
                        <div class="wd-modal-title">Points à corriger</div>
                        @foreach($dossier->checks_conformite as $check)
                        <label class="wd-modal-check">
                            <input type="checkbox" value="{{ $check['label'] }}" x-model="selected">
                            <span class="wd-pastille wd-pastille-{{ $check['statut'] }}"></span>
                            <span>{{ $check['label'] }}</span>
                        </label>
                        @endforeach
                        <div class="wd-actions">
                            <button type="button" @click="showModal = false" class="wd-btn wd-btn-dark">Valider la sélection</button>
                        </div>
                    </div>
                </div>
                @endif
            </form>
        </div>
    </section>
    @endif

    @if($dossier->convention_statut === 'generee')
    <section class="wd-block">
        <div class="wd-section-title">Convention de mandat</div>
        <div class="wd-actions">
            <a href="{{ route('tenant.back-office-enrolement.convention-pdf', $dossier) }}" class="wd-btn wd-btn-outline">Télécharger la convention</a>
            <form method="POST" action="{{ route('tenant.back-office-enrolement.marquer-signe', $dossier) }}">
                @csrf
                <button type="submit" class="wd-btn wd-btn-dark">Marquer comme signée</button>
            </form>
        </div>
    </section>
    @endif

    @if($dossier->convention_statut === 'signee')
    <section class="wd-block">
        <div class="wd-section-title">Convention signée</div>
        <div class="wd-actions">
            <a href="{{ route('tenant.back-office-enrolement.convention-pdf', $dossier) }}" class="wd-btn wd-btn-outline">Télécharger la convention</a>
        </div>
    </section>
    @endif

    @if($dossier->statut === 'rejected')
    <section class="wd-block">
        <div class="wd-section-title">Non validé pour le moment</div>
        <p style="font-size:13px;color:#242424;">{{ $dossier->refuse_motif }}</p>
    </section>
    @endif
</div>
</x-tenant-app-layout>
