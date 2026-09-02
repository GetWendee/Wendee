<x-tenant-app-layout>
@include('tenant.clients.partials.header-tabs', ['active' => 'recommandation'])
@php
    $prestations = $cabinet->prestations ?? [];
    $missionTypes = [
        [
            'key' => 'courtage_banque',
            'label' => 'Mandat de courtage banque',
            'index' => 0,
        ],
        [
            'key' => 'courtage_assurance',
            'label' => 'Mandat de courtage assurance',
            'index' => 1,
        ],
        [
            'key' => 'conseil_investissement_financier',
            'label' => 'Conseils en investissements financiers (CIF)',
            'index' => 2,
        ],
    ];
@endphp
<style>
.wd-reco-card{background:#242424;color:#fff;border-radius:14px;padding:22px 26px;border-top:3px solid var(--pink);display:flex;align-items:center;gap:16px;}
.wd-reco-icon{width:36px;height:36px;border-radius:9px;background:rgba(255,255,255,.08);display:grid;place-items:center;flex:0 0 auto;}
.wd-reco-card h2{margin:0;font-size:16px;}
.wd-reco-card p{margin:4px 0 0;color:#c9c2be;font-size:12px;}
.wd-reco-body{background:#fff;border:1px solid var(--line);border-radius:14px;padding:26px 28px;margin-top:18px;}
.wd-reco-date{color:var(--muted);font-size:12px;margin:0 0 20px;}
.wd-reco-question{font-size:13px;font-weight:700;color:var(--ink);margin:0 0 10px;}
.wd-reco-textarea{width:100%;min-height:110px;border:1px solid var(--line);border-radius:10px;padding:14px;font:inherit;font-size:13px;color:var(--ink);resize:vertical;}
.wd-reco-textarea:focus{outline:none;border-color:var(--pink);}
.wd-reco-missions{margin:24px 0 0;display:flex;flex-direction:column;gap:12px;}
.wd-reco-mission-block{border:1px solid var(--line);border-radius:10px;padding:12px 14px;}
.wd-reco-mission-pricing{margin-top:10px;padding-top:10px;border-top:1px solid var(--line);display:flex;align-items:center;gap:14px;flex-wrap:wrap;}
.wd-reco-suggestion{font-size:12px;color:var(--muted);}
.wd-reco-suggestion-muted{font-style:italic;}
.wd-reco-amount{width:120px;border:1px solid var(--line);border-radius:7px;padding:7px 10px;font-size:13px;color:var(--ink);}
.wd-reco-amount:focus{outline:none;border-color:var(--pink);}
.wd-reco-percent-row{display:flex;align-items:center;gap:8px;}
.wd-reco-times{color:var(--muted);font-size:12px;}
.wd-reco-amount-small{width:90px;}
.wd-reco-total{margin-top:18px;font-size:13px;font-weight:700;color:var(--ink);}
.wd-reco-total-note{display:block;margin-top:4px;font-size:11px;font-weight:400;color:var(--muted);}
.wd-reco-actions{margin-top:24px;display:flex;justify-content:flex-end;}
.wd-reco-submit{min-width:190px;height:40px;padding:0 20px;border:1px solid rgba(255,255,255,.10);border-top:2px solid var(--pink);border-radius:8px;background:#242424;color:#fff;font-size:9px;font-weight:800;letter-spacing:.10em;text-transform:uppercase;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;text-align:center;text-decoration:none;}
.wd-reco-submit:hover{box-shadow:0 0 0 2px rgba(255,51,153,.10);}
.wd-reco-flash{margin-bottom:18px;padding:10px 14px;border-radius:8px;font-size:12px;}
.wd-reco-flash-success{background:#eef7ef;color:var(--green);border:1px solid #cfe8d2;}
.wd-reco-flash-error{background:#fbecec;color:var(--red);border:1px solid #f0c9c9;}
.wd-reco-result{background:#fff;border:1px solid var(--line);border-radius:14px;padding:26px 28px;margin-top:18px;}
.wd-reco-result-head{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;padding-bottom:12px;border-bottom:1px solid var(--line);}
.wd-reco-result-eyebrow{font-size:10px;color:var(--pink);font-weight:850;letter-spacing:.14em;text-transform:uppercase;}
.wd-reco-result-date{font-size:11px;color:var(--muted);}
.wd-reco-editor{min-height:220px;}
.wd-reco-editor .ql-editor{font-size:13px;line-height:1.7;color:var(--ink);}
.wd-reco-editor .ql-editor h2.section-title{font-size:14px;font-weight:800;margin:16px 0 8px;}
.wd-reco-editor .ql-editor h2.section-title .section-number{display:inline-block;min-width:20px;margin-right:8px;}
.wd-reco-editor-form{display:flex;justify-content:flex-end;margin-top:14px;}
.wd-reco-save{min-width:220px;}
.wd-modal-overlay{position:fixed;inset:0;background:rgba(23,21,20,.55);z-index:9999;align-items:center;justify-content:center;}
.wd-modal-card{background:#fff;border-radius:14px;padding:24px 26px;width:320px;box-shadow:0 20px 50px rgba(0,0,0,.25);}
.wd-modal-title{font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.08em;margin-bottom:12px;}
.wd-modal-input{width:100%;height:38px;border:1px solid var(--line);border-radius:8px;padding:0 12px;font-size:13px;box-sizing:border-box;}
.wd-modal-suggestions{margin-top:4px;max-height:150px;overflow-y:auto;}
.wd-modal-suggestion-item{padding:8px 10px;font-size:12px;color:var(--ink);cursor:pointer;border-radius:6px;}
.wd-modal-suggestion-item:hover{background:var(--soft);}
.wd-modal-actions{display:flex;justify-content:flex-end;gap:10px;margin-top:16px;}
.wd-modal-btn-cancel{height:36px;padding:0 16px;border:1px solid var(--line);border-radius:8px;background:#fff;color:var(--ink);font-size:11px;font-weight:700;cursor:pointer;}
.wd-modal-btn-confirm{height:36px;padding:0 16px;border:none;border-radius:8px;background:#242424;border-top:2px solid var(--pink);color:#fff;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;cursor:pointer;}
.wd-modal-overlay{backdrop-filter:blur(2px);}
.wd-modal-card{position:relative;overflow:hidden;animation:wd-modal-in .18s ease;}
.wd-modal-accent{position:absolute;top:0;left:0;right:0;height:4px;background:var(--pink);}
.wd-modal-eyebrow{font-size:19px;color:var(--ink);font-weight:800;letter-spacing:0;text-transform:none;margin-bottom:4px;}
.wd-modal-btn-confirm:hover{box-shadow:0 0 0 2px rgba(255,51,153,.15);}
@keyframes wd-modal-in{from{opacity:0;transform:translateY(6px);}to{opacity:1;transform:translateY(0);}}
.wd-cabinet-checkbox{display:flex;align-items:center;gap:10px;font-size:12px;font-weight:700;color:#817b76;cursor:pointer;}
.wd-cabinet-checkbox input{display:none;}
.wd-cabinet-checkbox-box{width:16px;height:16px;flex:0 0 16px;display:grid;place-items:center;border:1px solid #ded9d4;border-radius:4px;background:#fff;}
.wd-cabinet-checkbox-box svg{width:10px;height:10px;display:none;fill:none;stroke:#fff;stroke-width:3;stroke-linecap:round;stroke-linejoin:round;}
.wd-cabinet-checkbox:has(input:checked){color:#242424;}
.wd-cabinet-checkbox:has(input:checked) .wd-cabinet-checkbox-box{border-color:#242424;background:#242424;}
.wd-cabinet-checkbox:has(input:checked) .wd-cabinet-checkbox-box svg{display:block;}
</style>
<section class="wd-section">
    <div class="wd-reco-card">
        <div class="wd-reco-icon">📄</div>
        <div>
            <h2>Générer la recommandation patrimoniale</h2>
            <p>Veuillez remplir ou mettre à jour tous les formulaires de connaissance client</p>
        </div>
    </div>
    <div class="wd-reco-body">
        @if(session('status'))
            <div class="wd-reco-flash wd-reco-flash-success">{{ session('status') }}</div>
        @endif
        @if(session('error'))
            <div class="wd-reco-flash wd-reco-flash-error">{{ session('error') }}</div>
        @endif
        <p class="wd-reco-date">
            Date de la recommandation : {{ now()->translatedFormat('d F Y') }}
        </p>
        <form method="POST" action="{{ route('tenant.clients.recommandation-patrimoniale.generer', $client) }}" data-reco-form>
            @csrf
            <p class="wd-reco-question">
                1. Veuillez contextualiser cette recommandation patrimoniale.
            </p>
            <textarea
                name="contexte"
                class="wd-reco-textarea"
                placeholder="Décrivez le contexte de la mission (attentes du client, projets, contraintes…)"
            ></textarea>

            <div class="wd-reco-missions">
                @foreach($missionTypes as $mission)
                    @php $presta = $prestations[$mission['index']] ?? []; @endphp
                    <div class="wd-reco-mission-block" data-mission>
                        <label class="wd-cabinet-checkbox">
                            <input type="checkbox" name="missions[]" value="{{ $mission['key'] }}" data-mission-toggle>
                            <span class="wd-cabinet-checkbox-box">
                                <svg viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                            </span>
                            <span>{{ $mission['label'] }}</span>
                        </label>
                        <div class="wd-reco-mission-pricing" data-mission-pricing hidden>
                            @if(($presta['mode'] ?? null) === 'forfait')
                                <span class="wd-reco-suggestion">
                                    Suggestion cabinet : {{ number_format((float) ($presta['forfait'] ?? 0), 2, ',', ' ') }} €
                                </span>
                                <input
                                    type="number"
                                    step="0.01"
                                    name="montants[{{ $mission['key'] }}]"
                                    value="{{ $presta['forfait'] ?? '' }}"
                                    class="wd-reco-amount"
                                    data-mission-amount
                                    data-mode="forfait"
                                >
                            @elseif(($presta['mode'] ?? null) === 'pourcentage')
                                <span class="wd-reco-suggestion">
                                    Suggestion cabinet :
                                    @if(isset($presta['pourcentage']) && $presta['pourcentage'] !== null && $presta['pourcentage'] !== '')
                                        {{ number_format((float) $presta['pourcentage'], 2, ',', ' ') }} %
                                    @else
                                        Aucune suggestion cabinet
                                    @endif
                                </span>
                                <div class="wd-reco-percent-row">
                                    <input
                                        type="number"
                                        step="0.01"
                                        name="montants[{{ $mission['key'] }}]"
                                        placeholder="Montant"
                                        class="wd-reco-amount"
                                        data-mission-montant
                                    >
                                    <span class="wd-reco-times">×</span>
                                    <input
                                        type="number"
                                        step="0.01"
                                        name="taux[{{ $mission['key'] }}]"
                                        value="{{ $presta['pourcentage'] ?? '' }}"
                                        placeholder="Taux %"
                                        class="wd-reco-amount wd-reco-amount-small"
                                        data-mission-taux
                                    >
                                </div>
                            @else
                                <span class="wd-reco-suggestion wd-reco-suggestion-muted">
                                    Aucun tarif configuré pour cette prestation (Cabinet → Tarifs & rémunération).
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <p class="wd-reco-total">
                Total : <span data-reco-total>0,00</span> €
            </p>

            <div class="wd-reco-actions">
                <button type="submit" class="wd-reco-submit">
                    Recommandation
                </button>
            </div>
        </form>
    </div>
</section>
@if($recommandation && $recommandation->status === 'completed')
    @php
        $htmlContenu = $recommandation->result_json['lettre_mission_html']
            ?? \App\Services\AI\RecommandationAnalysisService::convertirMarkdownEnHtml(
                $recommandation->result_json['lettre_mission'] ?? $recommandation->raw_response ?? ''
            );
    @endphp
    <link href="https://cdnjs.cloudflare.com/ajax/libs/quill/1.3.6/quill.snow.min.css" rel="stylesheet">
    <style>
    .wd-reco-result .ql-picker.ql-header .ql-picker-label[data-value="1"]::before,
    .wd-reco-result .ql-picker.ql-header .ql-picker-item[data-value="1"]::before{content:'Titre 1' !important;}
    .wd-reco-result .ql-picker.ql-header .ql-picker-label[data-value="2"]::before,
    .wd-reco-result .ql-picker.ql-header .ql-picker-item[data-value="2"]::before{content:'Titre 2' !important;}
    .wd-reco-result .ql-picker.ql-header .ql-picker-label[data-value="3"]::before,
    .wd-reco-result .ql-picker.ql-header .ql-picker-item[data-value="3"]::before{content:'Titre 3' !important;}
    </style>
    <section class="wd-section">
        <div class="wd-reco-result">
            <div class="wd-reco-result-head">
                <span class="wd-reco-result-eyebrow">Dernière lettre de mission générée</span>
                <span class="wd-reco-result-date">{{ $recommandation->completed_at?->translatedFormat('d F Y à H:i') }}</span>
                <button type="button" id="wd-reco-pdf-btn" class="wd-reco-submit" data-pdf-url="{{ route('tenant.clients.recommandation-patrimoniale.pdf', $client) }}" data-lieu-defaut="{{ $client->kyc?->lieu_signature ?: $cabinet?->ville }}">
                    Télécharger en PDF
                </button>
            </div>
            <div id="wd-reco-editor" class="wd-reco-editor">{!! $htmlContenu !!}</div>
            <form method="POST" action="{{ route('tenant.clients.recommandation-patrimoniale.modifier', ['client' => $client, 'analysis' => $recommandation->id]) }}" class="wd-reco-editor-form">
                @csrf
                @method('PUT')
                <input type="hidden" name="contenu_html" id="wd-reco-hidden">
                <button type="submit" class="wd-reco-submit wd-reco-save">Enregistrer les modifications</button>
            </form>
        </div>
    </section>
    <div id="wd-modal-lieu" class="wd-modal-overlay" style="display:none;">
    <div class="wd-modal-card">
        <div class="wd-modal-accent"></div>
        <div class="wd-modal-eyebrow">Lettre de mission</div>
        <div class="wd-modal-title">Lieu de signature</div>
        <input type="text" id="wd-modal-lieu-input" class="wd-modal-input" placeholder="Ville..." autocomplete="off">
        <div id="wd-modal-lieu-suggestions" class="wd-modal-suggestions"></div>
        <div class="wd-modal-actions">
            <button type="button" id="wd-modal-lieu-cancel" class="wd-modal-btn-cancel">Annuler</button>
            <button type="button" id="wd-modal-lieu-confirm" class="wd-modal-btn-confirm">Télécharger</button>
        </div>
    </div>
</div>
<script>
    (function () {
        var btn = document.getElementById('wd-reco-pdf-btn');
        if (! btn) { return; }
        var overlay = document.getElementById('wd-modal-lieu');
        var input = document.getElementById('wd-modal-lieu-input');
        var suggestions = document.getElementById('wd-modal-lieu-suggestions');
        var cancelBtn = document.getElementById('wd-modal-lieu-cancel');
        var confirmBtn = document.getElementById('wd-modal-lieu-confirm');
        var timer = null;
        btn.addEventListener('click', function () {
            input.value = btn.dataset.lieuDefaut || '';
            suggestions.innerHTML = '';
            overlay.style.display = 'flex';
            input.focus();
        });
        cancelBtn.addEventListener('click', function () {
            overlay.style.display = 'none';
        });
        confirmBtn.addEventListener('click', function () {
            var lieu = input.value.trim();
            var url = btn.dataset.pdfUrl + (lieu ? '?lieu=' + encodeURIComponent(lieu) : '');
            window.location.href = url;
            overlay.style.display = 'none';
        });
        input.addEventListener('input', function () {
            var q = input.value.trim();
            suggestions.innerHTML = '';
            if (timer) { clearTimeout(timer); }
            if (q.length < 2) { return; }
            timer = setTimeout(function () {
                fetch('https://geo.api.gouv.fr/communes?nom=' + encodeURIComponent(q) + '&fields=nom&boost=population&limit=5')
                    .then(function (r) { return r.json(); })
                    .then(function (villes) {
                        suggestions.innerHTML = '';
                        villes.forEach(function (v) {
                            var item = document.createElement('div');
                            item.className = 'wd-modal-suggestion-item';
                            item.textContent = v.nom;
                            item.addEventListener('click', function () {
                                input.value = v.nom;
                                suggestions.innerHTML = '';
                            });
                            suggestions.appendChild(item);
                        });
                    })
                    .catch(function () {});
            }, 250);
        });
    })();
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/quill/1.3.6/quill.min.js"></script>
    <script>
        (function () {
            var editorEl = document.getElementById('wd-reco-editor');
            if (! editorEl) { return; }
            var editor = new Quill('#wd-reco-editor', { theme: 'snow' });
            var form = document.querySelector('.wd-reco-editor-form');
            var hidden = document.getElementById('wd-reco-hidden');
            form.addEventListener('submit', function () {
                hidden.value = editor.root.innerHTML;
            });
        })();
    </script>
@elseif($recommandation && $recommandation->status === 'failed')
    <section class="wd-section">
        <div class="wd-reco-flash wd-reco-flash-error">
            La dernière génération a échoué : {{ $recommandation->error_message }}
        </div>
    </section>
@endif
<script>
(function() {
    var form = document.querySelector('[data-reco-form]');
    if (!form) return;
    var totalEl = form.querySelector('[data-reco-total]');
    var blocks = form.querySelectorAll('[data-mission]');

    function recompute() {
        var total = 0;
        blocks.forEach(function(block) {
            var toggle = block.querySelector('[data-mission-toggle]');
            var pricing = block.querySelector('[data-mission-pricing]');
            if (!toggle) return;
            pricing.hidden = !toggle.checked;
            if (!toggle.checked) return;

            var forfaitInput = block.querySelector('[data-mission-amount]');
            if (forfaitInput) {
                total += parseFloat(forfaitInput.value || '0') || 0;
                return;
            }

            var montantInput = block.querySelector('[data-mission-montant]');
            var tauxInput = block.querySelector('[data-mission-taux]');
            if (montantInput && tauxInput) {
                var montant = parseFloat(montantInput.value || '0') || 0;
                var taux = parseFloat(tauxInput.value || '0') || 0;
                total += montant * taux / 100;
            }
        });
        totalEl.textContent = total.toLocaleString('fr-FR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    blocks.forEach(function(block) {
        var toggle = block.querySelector('[data-mission-toggle]');
        var forfaitInput = block.querySelector('[data-mission-amount]');
        var montantInput = block.querySelector('[data-mission-montant]');
        var tauxInput = block.querySelector('[data-mission-taux]');
        if (toggle) toggle.addEventListener('change', recompute);
        if (forfaitInput) forfaitInput.addEventListener('input', recompute);
        if (montantInput) montantInput.addEventListener('input', recompute);
        if (tauxInput) tauxInput.addEventListener('input', recompute);
    });

    recompute();
})();
</script>
</div>
</x-tenant-app-layout>
