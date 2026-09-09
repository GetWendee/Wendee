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
.wd-pj-check-list{display:grid;gap:8px;margin-top:18px;max-height:50vh;overflow-y:auto}
.wd-pj-check{display:flex;align-items:center;gap:9px;padding:10px 12px;border:1px solid #ded9d4;border-radius:8px;font-size:13px;color:#242424;cursor:pointer}
.wd-pj-check input{display:none}
.wd-pj-check:has(input:checked){border-color:#242424;background:#f3f1ee}
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

    <section class="wd-block" x-data="{ modalOpen: false, modalUrl: '', modalIsImage: false, modalLabel: '', modalDeleteUrl: '' }">
        <div class="wd-section-title">Justificatifs</div>
        @forelse($dossier->justificatifs as $justificatif)
        @php
        $ext = strtolower(pathinfo($justificatif->nom_original ?: $justificatif->fichier_path, PATHINFO_EXTENSION));
        $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
        @endphp
        <div class="wd-just-row" style="cursor:pointer;"
            @click="
                modalOpen = true;
                modalUrl = '{{ route('tenant.back-office-enrolement.justificatifs.show', $justificatif) }}';
                modalIsImage = {{ $isImage ? 'true' : 'false' }};
                modalLabel = @js(($typesJustificatifs[$justificatif->type] ?? $justificatif->type) . ' v' . $justificatif->version);
                modalDeleteUrl = '{{ route('tenant.back-office-enrolement.justificatifs.destroy', $justificatif) }}';
            "
        >
            <span>{{ $typesJustificatifs[$justificatif->type] ?? $justificatif->type }} v{{ $justificatif->version }}</span>
            <span style="color:#817b76;">{{ $justificatif->nom_original }}</span>
        </div>
        @empty
        <p style="color:#817b76;font-size:12px;">Aucun justificatif déposé.</p>
        @endforelse

        <div x-show="modalOpen" x-cloak class="wd-newaccount-overlay" @click.self="modalOpen = false">
            <div class="wd-newaccount-modal" style="max-width:640px;">
                <div class="wd-newaccount-head">
                    <div>
                        <div class="wd-eyebrow">Justificatif</div>
                        <h3 x-text="modalLabel" style="font-size:16px;"></h3>
                    </div>
                    <button type="button" class="wd-newaccount-close" @click="modalOpen = false" aria-label="Fermer">&times;</button>
                </div>
                <div style="margin-top:16px;max-height:60vh;overflow:auto;">
                    <template x-if="modalIsImage">
                        <img :src="modalUrl" style="max-width:100%;border-radius:8px;">
                    </template>
                    <template x-if="!modalIsImage">
                        <iframe :src="modalUrl" style="width:100%;height:60vh;border:1px solid #ded9d4;border-radius:8px;"></iframe>
                    </template>
                </div>
                <div class="wd-actions" style="margin-top:16px;">
                    <a :href="modalUrl" target="_blank" class="wd-btn wd-btn-outline">Ouvrir dans un nouvel onglet</a>
                    <form method="POST" :action="modalDeleteUrl" onsubmit="return confirm('Supprimer définitivement ce justificatif ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="wd-btn wd-btn-red">Supprimer</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    @if(in_array($dossier->statut, ['pending_validation', 'onboarding']))
    <section class="wd-block">
        <div class="wd-section-title">Décision</div>
        <div x-data="{ showModal: false, selected: @js(collect($dossier->checks_conformite ?? [])->filter(fn ($c) => in_array($c['statut'] ?? 'ok', ['attention', 'bloquant']))->keys()->values()) }">
            <div class="wd-field">
                <label>Si refus, précisez les points à corriger</label>

                @if($dossier->checks_conformite)
                <button type="button" @click="showModal = true" class="wd-btn wd-btn-outline" style="margin-bottom:10px;">
                    Choisir les points à corriger<span x-show="selected.length" x-text="' (' + selected.length + ')'"></span>
                </button>
                @endif

                <textarea form="refuser-form-{{ $dossier->id }}" name="refuse_motif" placeholder="Expliquez au conseiller ce qui doit être corrigé (facultatif si vous avez choisi des points ci-dessus)"></textarea>
                <div style="color:#817b76;font-size:11px;margin-top:4px;">Le motif et les points choisis seront visibles par le conseiller sur son dossier. Il pourra corriger les informations et soumettre à nouveau.</div>
            </div>

            @if($dossier->checks_conformite)
            <div x-show="showModal" x-cloak class="wd-newaccount-overlay" @click.self="showModal = false">
                <div class="wd-newaccount-modal">
                    <div class="wd-newaccount-head">
                        <div>
                            <div class="wd-eyebrow">Non validé pour le moment</div>
                            <h3>Points à corriger</h3>
                        </div>
                        <button type="button" class="wd-newaccount-close" @click="showModal = false" aria-label="Fermer">&times;</button>
                    </div>
                    <div class="wd-pj-check-list">
                        @foreach($dossier->checks_conformite as $key => $check)
                        <label class="wd-pj-check">
                            <input type="checkbox" name="refuse_points[]" value="{{ $key }}" x-model="selected" form="refuser-form-{{ $dossier->id }}">
                            <span class="wd-pastille wd-pastille-{{ $check['statut'] }}"></span>
                            <span>{{ $check['label'] }}</span>
                        </label>
                        @endforeach
                    </div>
                    <button type="button" @click="showModal = false" class="wd-btn wd-btn-dark" style="margin-top:16px;">Valider la sélection</button>
                </div>
            </div>
            @endif
        </div>

        <div class="wd-actions" style="margin-top:16px;">
            <form method="POST" action="{{ route('tenant.back-office-enrolement.valider', $dossier) }}">
                @csrf
                <button type="submit" class="wd-btn wd-btn-dark">Valider l'enrôlement</button>
            </form>
            <form method="POST" action="{{ route('tenant.back-office-enrolement.refuser', $dossier) }}" id="refuser-form-{{ $dossier->id }}">
                @csrf
                <button type="submit" class="wd-btn wd-btn-red">Refuser le dossier</button>
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
        <div class="wd-section-title">Modifications demandées</div>
        @if(!empty($dossier->refuse_points))
        <p style="font-size:13px;color:#242424;margin:0 0 10px;">
            @foreach($dossier->refuse_points as $key)
            {{ $dossier->checks_conformite[$key]['label'] ?? $key }}<br>
            @endforeach
        </p>
        @endif
        @if($dossier->refuse_motif)
        <p style="font-size:13px;color:#151515;font-weight:700;background:#f9f8f7;border:1px solid #eeeae7;border-radius:8px;padding:12px 14px;margin:0;">{{ $dossier->refuse_motif }}</p>
        @endif
    </section>
    @endif
</div>
</x-tenant-app-layout>
