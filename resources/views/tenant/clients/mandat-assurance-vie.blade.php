<x-tenant-app-layout>
@include('tenant.clients.partials.header-tabs', ['active' => 'mission'])
@php
    $donnees = $mandat->result_json ?? [];
    $objectifsOptions = [
        'epargne' => 'Constituer une épargne',
        'retraite' => 'Préparer la retraite',
        'transmission' => 'Transmettre un capital',
        'fiscalite' => 'Optimiser la fiscalité',
        'disponibilite' => 'Disponibilité immédiate',
        'autre' => 'Autre',
    ];
    $horizonOptions = [
        'moins_2_ans' => 'Moins de 2 ans',
        '2_5_ans' => '2 à 5 ans',
        '5_8_ans' => '5 à 8 ans',
        'plus_8_ans' => 'Plus de 8 ans',
    ];
    $periodiciteOptions = ['mensuel' => 'Mensuel', 'trimestriel' => 'Trimestriel', 'annuel' => 'Annuel'];
    $modeGestionOptions = ['libre' => 'Gestion libre', 'pilotee' => 'Gestion pilotée', 'mandat' => 'Gestion sous mandat'];
    $repartitionOptions = ['prudent' => 'Prudent', 'equilibre' => 'Équilibré', 'dynamique' => 'Dynamique'];
    $objectifsSelectionnes = $donnees['objectifs'] ?? [];
    $contratsExistants = $donnees['contrats_existants'] ?? [[]];
    $beneficiaires = $donnees['beneficiaires'] ?? [[]];
@endphp
<style>
.wd-mandat-card{background:#242424;color:#fff;border-radius:14px;padding:22px 26px;border-top:3px solid var(--pink);}
.wd-mandat-card h2{margin:0;font-size:16px;}
.wd-mandat-card p{margin:4px 0 0;color:#c9c2be;font-size:12px;}
.wd-mandat-body{background:#fff;border:1px solid var(--line);border-radius:14px;padding:26px 28px;margin-top:18px;}
.wd-mandat-section-title{font-size:13px;font-weight:800;color:var(--ink);margin:26px 0 12px;padding-bottom:8px;border-bottom:1px solid var(--line);}
.wd-mandat-section-title:first-child{margin-top:0;}
.wd-mandat-field{margin-bottom:16px;}
.wd-mandat-label{display:block;font-size:12px;font-weight:700;color:var(--ink);margin-bottom:6px;}
.wd-mandat-input,.wd-mandat-select,.wd-mandat-textarea{width:100%;border:1px solid var(--line);border-radius:8px;padding:9px 12px;font:inherit;font-size:13px;color:var(--ink);box-sizing:border-box;}
.wd-mandat-textarea{min-height:80px;resize:vertical;}
.wd-mandat-input:focus,.wd-mandat-select:focus,.wd-mandat-textarea:focus{outline:none;border-color:var(--pink);}
.wd-cabinet-checkbox-group{display:grid;grid-template-columns:repeat(2,1fr);gap:10px;margin-top:4px;}
.wd-cabinet-checkbox{display:flex;align-items:center;gap:10px;padding:9px 12px;border:1px solid #ded9d4;border-radius:7px;font-size:12px;font-weight:700;color:#817b76;cursor:pointer;}
.wd-cabinet-checkbox input{display:none;}
.wd-cabinet-checkbox-box{width:16px;height:16px;flex:0 0 16px;display:grid;place-items:center;border:1px solid #ded9d4;border-radius:4px;background:#fff;}
.wd-cabinet-checkbox-box svg{width:10px;height:10px;display:none;fill:none;stroke:#fff;stroke-width:3;stroke-linecap:round;stroke-linejoin:round;}
.wd-cabinet-checkbox:has(input:checked){border-color:#242424;color:#242424;background:#f3f1ee;}
.wd-cabinet-checkbox:has(input:checked) .wd-cabinet-checkbox-box{border-color:#242424;background:#242424;}
.wd-cabinet-checkbox:has(input:checked) .wd-cabinet-checkbox-box svg{display:block;}
.wd-cabinet-radio-group{display:flex;flex-wrap:wrap;gap:8px;margin-top:4px;}
.wd-cabinet-radio{flex:1 1 auto;min-width:110px;display:flex;align-items:center;justify-content:center;padding:9px 8px;border:1px solid #ded9d4;border-radius:7px;font-size:11px;font-weight:700;color:#817b76;cursor:pointer;text-align:center;}
.wd-cabinet-radio input{display:none;}
.wd-cabinet-radio:has(input:checked){border-color:#242424;color:#242424;background:#f3f1ee;}
.wd-mandat-row{display:grid;grid-template-columns:1fr;gap:12px;}
@media (min-width:720px){.wd-mandat-row-2{grid-template-columns:1fr 1fr;}.wd-mandat-row-3{grid-template-columns:1fr 1fr 1fr;}}
.wd-mandat-repeater-item{border:1px solid var(--line);border-radius:10px;padding:14px;margin-bottom:10px;position:relative;}
.wd-mandat-repeater-remove{position:absolute;top:10px;right:10px;background:none;border:none;color:var(--muted);cursor:pointer;font-size:11px;text-decoration:underline;}
.wd-mandat-repeater-add{background:none;border:1px dashed var(--line);border-radius:8px;padding:9px 14px;font-size:12px;color:var(--pink);font-weight:700;cursor:pointer;}
.wd-mandat-actions{margin-top:24px;display:flex;gap:12px;flex-wrap:wrap;align-items:center;}
.wd-mandat-submit{height:40px;padding:0 20px;border:none;border-radius:9px;background:#242424;border-top:2px solid var(--pink);color:#fff;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;cursor:pointer;}
.wd-mandat-pdf-btn{height:40px;padding:0 20px;border:1px solid var(--line);border-radius:9px;background:#fff;color:var(--ink);font-size:12px;font-weight:700;cursor:pointer;}
.wd-mandat-status{font-size:12px;color:var(--muted);}
.wd-mandat-header-status{display:flex;justify-content:space-between;align-items:flex-start;gap:16px;margin-bottom:20px;}
.wd-modal-overlay{position:fixed;inset:0;background:rgba(23,21,20,.55);z-index:9999;align-items:center;justify-content:center;backdrop-filter:blur(2px);display:none;}
.wd-modal-card{background:#fff;border-radius:14px;padding:24px 26px;width:320px;box-shadow:0 20px 50px rgba(0,0,0,.25);position:relative;overflow:hidden;animation:wd-modal-in .18s ease;}
.wd-modal-accent{position:absolute;top:0;left:0;right:0;height:4px;background:var(--pink);}
.wd-modal-eyebrow{font-size:19px;color:var(--ink);font-weight:800;margin-bottom:4px;}
.wd-modal-title{font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.08em;margin-bottom:12px;}
.wd-modal-input{width:100%;height:38px;border:1px solid var(--line);border-radius:8px;padding:0 12px;font-size:13px;box-sizing:border-box;}
.wd-modal-suggestions{margin-top:4px;max-height:150px;overflow-y:auto;}
.wd-modal-suggestion-item{padding:8px 10px;font-size:12px;color:var(--ink);cursor:pointer;border-radius:6px;}
.wd-modal-suggestion-item:hover{background:var(--soft);}
.wd-modal-actions{display:flex;justify-content:flex-end;gap:10px;margin-top:16px;}
.wd-modal-btn-cancel{height:36px;padding:0 16px;border:1px solid var(--line);border-radius:8px;background:#fff;color:var(--ink);font-size:11px;font-weight:700;cursor:pointer;}
.wd-modal-btn-confirm{height:36px;padding:0 16px;border:none;border-radius:8px;background:#242424;border-top:2px solid var(--pink);color:#fff;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;cursor:pointer;}
@keyframes wd-modal-in{from{opacity:0;transform:translateY(6px);}to{opacity:1;transform:translateY(0);}}
</style>

<section class="wd-section">
    <div class="wd-mandat-card">
        <h2>Mandat de courtage en assurance vie</h2>
        <p>Recueil des besoins avant génération du mandat</p>
    </div>

    <div class="wd-mandat-body">
        <div class="wd-mandat-header-status">
            <div>
                @if(session('status'))
                    <p class="wd-mandat-status">{{ session('status') }}</p>
                @endif
                @if($mandat)
                    <p class="wd-mandat-status">Dernier enregistrement : {{ $mandat->completed_at?->translatedFormat('d F Y à H:i') }}</p>
                @endif
            </div>
            @if($mandat)
                <button type="button" id="wd-mandat-pdf-btn" class="wd-mandat-pdf-btn" data-pdf-url="{{ route('tenant.clients.mandat-assurance-vie.pdf', $client) }}" data-lieu-defaut="{{ $client->kyc?->lieu_signature ?: $cabinet?->ville }}">
                    Télécharger le mandat en PDF
                </button>
            @endif
        </div>

        <form method="POST" action="{{ route('tenant.clients.mandat-assurance-vie.enregistrer', $client) }}">
            @csrf

            <div class="wd-mandat-section-title">1. Objectifs</div>
            <div class="wd-mandat-field">
                <div class="wd-cabinet-checkbox-group">
                    @foreach($objectifsOptions as $valeur => $label)
                        <label class="wd-cabinet-checkbox">
                            <input type="checkbox" name="objectifs[]" value="{{ $valeur }}" {{ in_array($valeur, $objectifsSelectionnes) ? 'checked' : '' }}>
                            <span class="wd-cabinet-checkbox-box">
                                <svg viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                            </span>
                            <span>{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="wd-mandat-field">
                <label class="wd-mandat-label">Précision si "Autre"</label>
                <input type="text" name="objectif_autre_precision" class="wd-mandat-input" value="{{ $donnees['objectif_autre_precision'] ?? '' }}">
            </div>

            <div class="wd-mandat-section-title">2. Horizon et versements</div>
            <div class="wd-mandat-row wd-mandat-row-2">
                <div class="wd-mandat-field">
                    <label class="wd-mandat-label">Horizon de placement</label>
                    <select name="horizon" class="wd-mandat-select">
                        <option value="">Sélectionner...</option>
                        @foreach($horizonOptions as $valeur => $label)
                            <option value="{{ $valeur }}" {{ ($donnees['horizon'] ?? '') === $valeur ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="wd-mandat-field">
                    <label class="wd-mandat-label">Versement initial envisagé (€)</label>
                    <input type="number" step="0.01" name="versement_initial" class="wd-mandat-input" value="{{ $donnees['versement_initial'] ?? '' }}">
                </div>
            </div>
            <div class="wd-mandat-row wd-mandat-row-2">
                <div class="wd-mandat-field">
                    <label class="wd-mandat-label">Versements programmés envisagés (€)</label>
                    <input type="number" step="0.01" name="versement_programme_montant" class="wd-mandat-input" value="{{ $donnees['versement_programme_montant'] ?? '' }}">
                </div>
                <div class="wd-mandat-field">
                    <label class="wd-mandat-label">Périodicité</label>
                    <select name="versement_programme_periodicite" class="wd-mandat-select">
                        <option value="">Sélectionner...</option>
                        @foreach($periodiciteOptions as $valeur => $label)
                            <option value="{{ $valeur }}" {{ ($donnees['versement_programme_periodicite'] ?? '') === $valeur ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="wd-mandat-section-title">3. Contrats d'assurance vie déjà détenus</div>
            <div id="wd-mandat-contrats-liste">
                @foreach($contratsExistants as $i => $contrat)
                    <div class="wd-mandat-repeater-item">
                        @if($i > 0)<button type="button" class="wd-mandat-repeater-remove" data-remove>Retirer</button>@endif
                        <div class="wd-mandat-row wd-mandat-row-3">
                            <div class="wd-mandat-field">
                                <label class="wd-mandat-label">Assureur / contrat</label>
                                <input type="text" name="contrats_existants[{{ $i }}][assureur]" class="wd-mandat-input" value="{{ $contrat['assureur'] ?? '' }}">
                            </div>
                            <div class="wd-mandat-field">
                                <label class="wd-mandat-label">Valeur actuelle (€)</label>
                                <input type="number" step="0.01" name="contrats_existants[{{ $i }}][valeur_actuelle]" class="wd-mandat-input" value="{{ $contrat['valeur_actuelle'] ?? '' }}">
                            </div>
                            <div class="wd-mandat-field">
                                <label class="wd-mandat-label">Date d'ouverture</label>
                                <input type="date" name="contrats_existants[{{ $i }}][date_ouverture]" class="wd-mandat-input" value="{{ $contrat['date_ouverture'] ?? '' }}">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <button type="button" class="wd-mandat-repeater-add" data-add="contrats">+ Ajouter un contrat</button>

            <div class="wd-mandat-section-title">4. Bénéficiaires souhaités en cas de décès</div>
            <div id="wd-mandat-beneficiaires-liste">
                @foreach($beneficiaires as $i => $beneficiaire)
                    <div class="wd-mandat-repeater-item">
                        @if($i > 0)<button type="button" class="wd-mandat-repeater-remove" data-remove>Retirer</button>@endif
                        <div class="wd-mandat-row wd-mandat-row-3">
                            <div class="wd-mandat-field">
                                <label class="wd-mandat-label">Nom</label>
                                <input type="text" name="beneficiaires[{{ $i }}][nom]" class="wd-mandat-input" value="{{ $beneficiaire['nom'] ?? '' }}">
                            </div>
                            <div class="wd-mandat-field">
                                <label class="wd-mandat-label">Lien avec le client</label>
                                <input type="text" name="beneficiaires[{{ $i }}][lien]" class="wd-mandat-input" value="{{ $beneficiaire['lien'] ?? '' }}">
                            </div>
                            <div class="wd-mandat-field">
                                <label class="wd-mandat-label">Quote-part</label>
                                <input type="text" name="beneficiaires[{{ $i }}][quote_part]" class="wd-mandat-input" value="{{ $beneficiaire['quote_part'] ?? '' }}">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <button type="button" class="wd-mandat-repeater-add" data-add="beneficiaires">+ Ajouter un bénéficiaire</button>

            <div class="wd-mandat-field" style="margin-top:16px;">
                <label class="wd-mandat-label">Clause bénéficiaire</label>
                <div class="wd-cabinet-radio-group">
                    <label class="wd-cabinet-radio">
                        <input type="radio" name="clause_beneficiaire_type" value="standard" {{ ($donnees['clause_beneficiaire_type'] ?? 'standard') === 'standard' ? 'checked' : '' }}>
                        <span>Clause type (conjoint, à défaut les enfants, à défaut les héritiers)</span>
                    </label>
                    <label class="wd-cabinet-radio">
                        <input type="radio" name="clause_beneficiaire_type" value="personnalisee" {{ ($donnees['clause_beneficiaire_type'] ?? '') === 'personnalisee' ? 'checked' : '' }}>
                        <span>Clause personnalisée</span>
                    </label>
                </div>
            </div>
            <div class="wd-mandat-field">
                <label class="wd-mandat-label">Texte de la clause personnalisée (si applicable)</label>
                <textarea name="clause_beneficiaire_texte" class="wd-mandat-textarea">{{ $donnees['clause_beneficiaire_texte'] ?? '' }}</textarea>
            </div>

            <div class="wd-mandat-section-title">5. Gestion et disponibilité</div>
            <div class="wd-mandat-row wd-mandat-row-3">
                <div class="wd-mandat-field">
                    <label class="wd-mandat-label">Mode de gestion souhaité</label>
                    <select name="mode_gestion" class="wd-mandat-select">
                        <option value="">Sélectionner...</option>
                        @foreach($modeGestionOptions as $valeur => $label)
                            <option value="{{ $valeur }}" {{ ($donnees['mode_gestion'] ?? '') === $valeur ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="wd-mandat-field">
                    <label class="wd-mandat-label">Répartition fonds euro / UC souhaitée</label>
                    <select name="repartition_risque" class="wd-mandat-select">
                        <option value="">Sélectionner...</option>
                        @foreach($repartitionOptions as $valeur => $label)
                            <option value="{{ $valeur }}" {{ ($donnees['repartition_risque'] ?? '') === $valeur ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="wd-mandat-field">
                    <label class="wd-mandat-label">Rachat possible à tout moment</label>
                    <select name="rachat_possible" class="wd-mandat-select">
                        <option value="">Sélectionner...</option>
                        <option value="oui" {{ ($donnees['rachat_possible'] ?? '') === 'oui' ? 'selected' : '' }}>Oui</option>
                        <option value="non" {{ ($donnees['rachat_possible'] ?? '') === 'non' ? 'selected' : '' }}>Non</option>
                    </select>
                </div>
            </div>

            <div class="wd-mandat-section-title">6. Commentaire du conseiller</div>
            <div class="wd-mandat-field">
                <textarea name="commentaire_conseiller" class="wd-mandat-textarea" placeholder="Facultatif">{{ $donnees['commentaire_conseiller'] ?? '' }}</textarea>
            </div>

            <div class="wd-mandat-actions">
                <button type="submit" class="wd-mandat-submit">Enregistrer</button>
            </div>
        </form>
    </div>
</section>

<div id="wd-modal-lieu" class="wd-modal-overlay" style="display:none;">
    <div class="wd-modal-card">
        <div class="wd-modal-accent"></div>
        <div class="wd-modal-eyebrow">Mandat assurance vie</div>
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
    function ajouterLigne(conteneurId, prefixe, champs) {
        var conteneur = document.getElementById(conteneurId);
        var index = conteneur.querySelectorAll('.wd-mandat-repeater-item').length;
        var item = document.createElement('div');
        item.className = 'wd-mandat-repeater-item';
        var html = '<button type="button" class="wd-mandat-repeater-remove" data-remove>Retirer</button><div class="wd-mandat-row wd-mandat-row-3">';
        champs.forEach(function (champ) {
            html += '<div class="wd-mandat-field"><label class="wd-mandat-label">' + champ.label + '</label>'
                + '<input type="' + champ.type + '" name="' + prefixe + '[' + index + '][' + champ.name + ']" class="wd-mandat-input"></div>';
        });
        html += '</div>';
        item.innerHTML = html;
        conteneur.appendChild(item);
    }
    document.querySelectorAll('[data-add]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (btn.dataset.add === 'contrats') {
                ajouterLigne('wd-mandat-contrats-liste', 'contrats_existants', [
                    { name: 'assureur', label: 'Assureur / contrat', type: 'text' },
                    { name: 'valeur_actuelle', label: 'Valeur actuelle (€)', type: 'number' },
                    { name: 'date_ouverture', label: "Date d'ouverture", type: 'date' },
                ]);
            } else {
                ajouterLigne('wd-mandat-beneficiaires-liste', 'beneficiaires', [
                    { name: 'nom', label: 'Nom', type: 'text' },
                    { name: 'lien', label: 'Lien avec le client', type: 'text' },
                    { name: 'quote_part', label: 'Quote-part', type: 'text' },
                ]);
            }
        });
    });
    document.body.addEventListener('click', function (e) {
        if (e.target.matches('[data-remove]')) {
            e.target.closest('.wd-mandat-repeater-item').remove();
        }
    });

    var btn = document.getElementById('wd-mandat-pdf-btn');
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
</x-tenant-app-layout>
