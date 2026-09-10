<x-tenant-app-layout>
@include('tenant.clients.partials.header-tabs', ['active' => 'plan-action'])
@php
    $viewRole = Auth::user()?->effectiveRole();
@endphp
<style>
.wd-reco-body{background:#fff;border:1px solid var(--line);border-radius:14px;padding:26px 28px;margin-top:18px;}
.wd-analysis-intro{margin-bottom:22px;}
.wd-analysis-kicker{margin:0 0 7px;color:#80A29A;font-size:10px;line-height:1;font-weight:800;letter-spacing:.16em;text-transform:uppercase;}
.wd-analysis-title{margin:0;color:#252D2A;font-size:27px;line-height:1.18;font-weight:700;letter-spacing:-.035em;}
.wd-analysis-subtitle{max-width:900px;margin:8px 0 0;color:#7A8581;font-size:12px;line-height:1.6;}
.wd-recommandation-button{flex:0 0 auto;min-width:190px;height:40px;padding:0 20px;border:1px solid rgba(255,255,255,.10);border-top:2px solid #FF3399;border-radius:8px;background:#242424;color:#ffffff;font-size:9px;font-weight:800;letter-spacing:.10em;text-transform:uppercase;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;transition:background .18s ease,border-color .18s ease,transform .18s ease,box-shadow .18s ease;}
.wd-recommandation-button:hover{box-shadow:0 0 0 2px rgba(255,51,153,.10);transform:translateY(-1px);}
.wd-recommandation-button-disabled{flex:0 0 auto;min-width:190px;height:40px;padding:0 20px;border:1px solid #D2D8D5;border-top:2px solid #C8CFCC;border-radius:8px;background:#E2E5E4;color:#929A97;font-size:9px;font-weight:800;letter-spacing:.10em;text-transform:uppercase;cursor:not-allowed;display:inline-flex;align-items:center;justify-content:center;}
.wd-reco-date{color:var(--muted);font-size:12px;margin:0 0 20px;}
.wd-reco-question{font-size:13px;font-weight:700;color:var(--ink);margin:0 0 10px;}
.wd-reco-textarea{width:100%;min-height:110px;border:1px solid var(--line);border-radius:10px;padding:14px;font:inherit;font-size:13px;color:var(--ink);resize:vertical;}
.wd-reco-textarea:focus{outline:none;border-color:var(--pink);}
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
@media(max-width:600px){
.wd-reco-body,.wd-reco-result{padding:18px;}
.wd-reco-result-head{flex-wrap:wrap;gap:10px;align-items:flex-start;}
.wd-modal-card{width:100%;max-width:92vw;}
}
</style>
<div style="display:flex;gap:12px;justify-content:flex-start;margin:22px 0 0;">
    <a href="{{ route('tenant.clients.aide-decision', $client) }}" class="wd-recommandation-button">
        Retour analyse
    </a>
    <a href="{{ route('tenant.clients.recommandation-patrimoniale', $client) }}" class="wd-recommandation-button">
        Retour recommandation
    </a>
</div>
<section class="wd-section wd-analysis-content">
    <div class="wd-analysis-intro">
        <p class="wd-analysis-kicker">Aide à la décision</p>
        <h2 class="wd-analysis-title">Générer le plan d'action</h2>
        <p class="wd-analysis-subtitle">Basé sur le KYC, le patrimoine, le profil investisseur et la recommandation patrimoniale du client</p>
    </div>
    <div class="wd-reco-body">
        @if(session('status'))
            <div class="wd-reco-flash wd-reco-flash-success">{{ session('status') }}</div>
        @endif
        @if(session('error'))
            <div class="wd-reco-flash wd-reco-flash-error">{{ session('error') }}</div>
        @endif
        <p class="wd-reco-date">
            Date du plan d'action : {{ now()->translatedFormat('d F Y') }}
        </p>
        @if($viewRole !== 'client')
        <form method="POST" action="{{ route('tenant.clients.plan-action.generer', $client) }}">
            @csrf
            <p class="wd-reco-question">
                Contexte complémentaire
            </p>
            <textarea
                name="contexte"
                class="wd-reco-textarea"
                placeholder="Facultatif mais recommandé"
            ></textarea>

            <div class="wd-reco-actions">
                <button type="submit" class="wd-reco-submit">
                    Plan d'action
                </button>
            </div>
        </form>
        @else
        <p class="wd-reco-date">Aucune action disponible dans cet espace.</p>
        @endif
    </div>
</section>
@if($planAction && $planAction->status === 'completed')
    @php
        $htmlContenu = $planAction->result_json['plan_action_html']
            ?? \App\Services\AI\RecommandationAnalysisService::convertirMarkdownEnHtml(
                $planAction->result_json['plan_action'] ?? $planAction->raw_response ?? ''
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
                <span class="wd-reco-result-eyebrow">Dernier plan d'action généré</span>
                <span class="wd-reco-result-date">{{ $planAction->completed_at?->translatedFormat('d F Y à H:i') }}</span>
                @if($viewRole !== 'client')
                <button type="button" id="wd-plan-pdf-btn" class="wd-reco-submit" data-pdf-url="{{ route('tenant.clients.plan-action.pdf', $client) }}" data-lieu-defaut="{{ $client->kyc?->lieu_signature ?: $cabinet?->ville }}">
                    Télécharger en PDF
                </button>
                @endif
            </div>
            <div id="wd-plan-editor" class="wd-reco-editor">{!! $htmlContenu !!}</div>
            @if($viewRole !== 'client')
            <form method="POST" action="{{ route('tenant.clients.plan-action.modifier', ['client' => $client, 'analysis' => $planAction->id]) }}" class="wd-reco-editor-form">
                @csrf
                @method('PUT')
                <input type="hidden" name="contenu_html" id="wd-plan-hidden">
                <button type="submit" class="wd-reco-submit wd-reco-save">Enregistrer les modifications</button>
            </form>
            @endif
        </div>
    </section>
    <div id="wd-modal-lieu" class="wd-modal-overlay" style="display:none;">
    <div class="wd-modal-card">
        <div class="wd-modal-accent"></div>
        <div class="wd-modal-eyebrow">Plan d'action</div>
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
        var btn = document.getElementById('wd-plan-pdf-btn');
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
            var editorEl = document.getElementById('wd-plan-editor');
            if (! editorEl) { return; }
            var editor = new Quill('#wd-plan-editor', { theme: 'snow' });
            var form = document.querySelector('.wd-reco-editor-form');
            var hidden = document.getElementById('wd-plan-hidden');
            if (form && hidden) {
                form.addEventListener('submit', function () {
                    hidden.value = editor.root.innerHTML;
                });
            } else {
                editor.enable(false);
            }
        })();
    </script>
@elseif($planAction && $planAction->status === 'failed')
    <section class="wd-section">
        <div class="wd-reco-flash wd-reco-flash-error">
            La dernière génération a échoué : {{ $planAction->error_message }}
        </div>
    </section>
@endif
</x-tenant-app-layout>
