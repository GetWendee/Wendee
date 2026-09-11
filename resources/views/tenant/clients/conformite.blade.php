<x-tenant-app-layout>
@include('tenant.clients.partials.header-tabs', ['active' => 'conformite'])

<style>
.wd-conf-hero{background:#242424;color:#fff;border-radius:14px;padding:26px 28px;border-top:3px solid var(--pink);display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:20px;}
.wd-conf-hero-left{min-width:0;}
.wd-conf-hero-left .wd-eyebrow{color:#c9c2be;}
.wd-conf-hero-left h2{margin:6px 0 0;font-size:20px;letter-spacing:-.02em;}
.wd-conf-hero-left p{margin:6px 0 0;color:#aaa29e;font-size:12px;}
.wd-conf-badge{display:inline-flex;align-items:center;gap:8px;padding:9px 18px;border-radius:999px;font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.09em;}
.wd-conf-badge.faible{background:#eef7ef;color:var(--green);}
.wd-conf-badge.standard{background:#fff6e8;color:#b77a2d;}
.wd-conf-badge.eleve{background:#fbecec;color:var(--red);}
.wd-conf-badge .dot{width:7px;height:7px;border-radius:50%;background:currentColor;}
.wd-conf-override{margin-top:6px;font-size:10.5px;color:#aaa29e;text-align:right;}

.wd-conf-grid{display:grid;grid-template-columns:1.3fr 1fr;gap:16px;margin-top:16px;}
.wd-conf-panel{background:#fff;border:1px solid var(--line);border-radius:14px;padding:24px 26px;}
.wd-conf-panel h3{margin:0 0 4px;font-size:15px;letter-spacing:-.02em;}
.wd-conf-panel .sub{margin:0 0 16px;font-size:12px;color:var(--muted);}

.wd-conf-facteur{display:flex;align-items:flex-start;gap:12px;padding:13px 0;border-top:1px solid var(--line);}
.wd-conf-facteur:first-child{border-top:0;padding-top:0;}
.wd-conf-facteur .pastille{width:9px;height:9px;border-radius:50%;margin-top:5px;flex:0 0 auto;}
.wd-conf-facteur .pastille.eleve{background:var(--red);}
.wd-conf-facteur .pastille.standard{background:#b77a2d;}
.wd-conf-facteur p{margin:0;font-size:13px;color:var(--ink);}
.wd-conf-empty{font-size:13px;color:var(--muted);}

.wd-conf-check{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:11px 0;border-top:1px solid var(--line);font-size:13px;}
.wd-conf-check:first-child{border-top:0;padding-top:0;}
.wd-conf-check .lbl{color:var(--ink);}
.wd-conf-check .val{font-size:11px;font-weight:800;padding:4px 10px;border-radius:999px;white-space:nowrap;}
.wd-conf-check .val.ok{background:#eef7ef;color:var(--green);}
.wd-conf-check .val.bad{background:#fbecec;color:var(--red);}
.wd-conf-check .val.neutre{background:var(--bg,#f3f1ee);color:var(--muted);}

.wd-conf-form{margin-top:16px;}
.wd-conf-field{margin-bottom:18px;}
.wd-conf-field label{display:block;font-size:11px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;color:var(--muted);margin-bottom:7px;}
.wd-conf-field select,.wd-conf-field textarea{width:100%;border:1px solid var(--line);border-radius:8px;padding:10px 12px;font:inherit;font-size:13px;color:var(--ink);box-sizing:border-box;}
.wd-conf-field textarea{min-height:80px;resize:vertical;}
.wd-conf-field select:focus,.wd-conf-field textarea:focus{outline:none;border-color:var(--pink);}
.wd-conf-motifs{display:grid;grid-template-columns:1fr 1fr;gap:8px 16px;}
.wd-conf-motif{display:flex;align-items:flex-start;gap:8px;font-size:12.5px;color:var(--ink);}
.wd-conf-motif input{margin-top:3px;}
.wd-conf-toggle{display:flex;align-items:center;gap:10px;font-size:13px;color:var(--ink);margin-bottom:14px;}
.wd-conf-submit{min-width:220px;height:40px;padding:0 20px;border:1px solid rgba(0,0,0,.06);border-top:2px solid var(--pink);border-radius:8px;background:#242424;color:#fff;font-size:9px;font-weight:800;letter-spacing:.10em;text-transform:uppercase;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;}
.wd-conf-flash{margin-bottom:18px;padding:10px 14px;border-radius:8px;font-size:12px;background:#eef7ef;color:var(--green);border:1px solid #cfe8d2;}

.wd-conf-tracfin{border-top-color:#b77a2d;}
.wd-conf-tracfin .wd-eyebrow{color:#e0a45c;}
.wd-conf-note{margin-top:10px;font-size:11px;color:#aaa29e;}

@media(max-width:800px){
.wd-conf-grid{grid-template-columns:1fr;}
.wd-conf-motifs{grid-template-columns:1fr;}
}
@media(max-width:600px){
.wd-conf-hero{padding:18px 18px 16px;flex-direction:column;align-items:flex-start;gap:12px;}
.wd-conf-override{text-align:left;}
.wd-conf-panel{padding:18px;}
.wd-conf-check{flex-wrap:wrap;}
.wd-conf-submit{width:100%;}
}
</style>

@php
    $motifsDisponibles = [
        'operation_atypique' => 'Opération atypique sans justification économique',
        'incoherence_patrimoniale' => 'Incohérence entre flux et profil patrimonial',
        'montants_non_expliques' => 'Montants significatifs non expliqués',
        'reticence_justifier' => 'Réticence du client à justifier une opération',
        'changement_profil' => 'Changement brutal de profil comportemental',
        'lien_ppe_pays_risque' => 'Lien avéré avec une PPE ou un pays à risque',
        'versements_tiers' => 'Versements à un tiers sans relation apparente',
        'confidentialite_inhabituelle' => 'Demande de confidentialité inhabituelle',
    ];
    $conformite = $client->conformite;
@endphp

<div class="wd-conf-hero">
    <div class="wd-conf-hero-left">
        <div class="wd-eyebrow">LCB-FT · Sapin 2</div>
        <h2>Conformité — {{ $client->prenom }} {{ $client->nom }}</h2>
        <p>Dernière revue : {{ $conformite?->date_derniere_revue?->translatedFormat('d F Y') ?? 'jamais réalisée' }}</p>
    </div>
    <div>
        <span class="wd-conf-badge {{ $evaluation['niveau_retenu'] }}">
            <span class="dot"></span>
            Risque {{ ['faible' => 'faible', 'standard' => 'standard', 'eleve' => 'élevé'][$evaluation['niveau_retenu']] }}
        </span>
        @if($evaluation['surcharge'])
            <div class="wd-conf-override">Ajusté par le conseiller (calcul auto : {{ $evaluation['niveau_calcule'] }})</div>
        @endif
    </div>
</div>

@php
    $flashLabels = [
        'conformite-mise-a-jour' => 'Revue de conformité enregistrée.',
        'screening-ok' => 'Vérification PPE / sanctions effectuée : aucune correspondance trouvée.',
        'screening-alerte' => 'Vérification PPE / sanctions effectuée : correspondance potentielle détectée, voir ci-dessous.',
        'screening-vide' => "Vérification PPE / sanctions impossible : aucune identité à contrôler (KYC incomplet).",
        'screening-erreur' => 'Vérification PPE / sanctions non aboutie (erreur technique ou clé API non configurée).',
    ];
    $flashAlerte = in_array(session('status'), ['screening-alerte', 'screening-erreur'], true);
@endphp

@if(session('status') && isset($flashLabels[session('status')]))
    <div class="wd-conf-flash" style="margin-top:16px;{{ $flashAlerte ? 'background:#fbecec;color:var(--red);border-color:#f3d3d3;' : '' }}">
        {{ $flashLabels[session('status')] }}
    </div>
@endif

<div class="wd-conf-grid">

    <div class="wd-conf-panel">
        <h3>Facteurs de risque détectés</h3>
        <p class="sub">Calculés à partir du KYC, du patrimoine et de la fiscalité du client.</p>

        @forelse($evaluation['facteurs'] as $facteur)
            <div class="wd-conf-facteur">
                <span class="pastille {{ $facteur['niveau'] }}"></span>
                <p>{{ $facteur['label'] }}</p>
            </div>
        @empty
            <p class="wd-conf-empty">Aucun facteur de risque détecté sur les données actuelles.</p>
        @endforelse
    </div>

    <div class="wd-conf-panel">
        <h3>Dossier réglementaire</h3>
        <p class="sub">Statut des éléments à jour.</p>

        @php $completion = $client->completionStatus(); @endphp

        <div class="wd-conf-check">
            <span class="lbl">KYC</span>
            <span class="val {{ $completion['items']['kyc']['done'] && ! $completion['items']['kyc']['stale'] ? 'ok' : 'bad' }}">
                {{ ! $completion['items']['kyc']['done'] ? 'Manquant' : ($completion['items']['kyc']['stale'] ? 'Périmé' : 'À jour') }}
            </span>
        </div>
        <div class="wd-conf-check">
            <span class="lbl">Patrimoine</span>
            <span class="val {{ $completion['items']['pat']['done'] && ! $completion['items']['pat']['stale'] ? 'ok' : 'bad' }}">
                {{ ! $completion['items']['pat']['done'] ? 'Manquant' : ($completion['items']['pat']['stale'] ? 'Périmé' : 'À jour') }}
            </span>
        </div>
        <div class="wd-conf-check">
            <span class="lbl">Profil investisseur</span>
            <span class="val {{ $completion['items']['inv']['done'] && ! $completion['items']['inv']['stale'] ? 'ok' : 'bad' }}">
                {{ ! $completion['items']['inv']['done'] ? 'Manquant' : ($completion['items']['inv']['stale'] ? 'Périmé' : 'À jour') }}
            </span>
        </div>
        <div class="wd-conf-check">
            <span class="lbl">Origine des fonds documentée</span>
            <span class="val {{ $conformite?->origine_fonds_documentee ? 'ok' : 'neutre' }}">
                {{ $conformite?->origine_fonds_documentee ? 'Oui' : 'À documenter' }}
            </span>
        </div>
        <div class="wd-conf-check">
            <span class="lbl">Vigilance renforcée</span>
            <span class="val {{ $conformite?->vigilance_renforcee ? 'bad' : 'neutre' }}">
                {{ $conformite?->vigilance_renforcee ? 'Activée' : 'Non activée' }}
            </span>
        </div>
    </div>

</div>

<div class="wd-conf-panel" style="margin-top:16px;">
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap;">
        <div>
            <h3>Screening PPE / sanctions</h3>
            <p class="sub">
                Vérification automatisée{{ $client->estMorale() ? " du client et des intervenants déclarés (dirigeants, actionnaires, bénéficiaires effectifs)" : " du client et de son conjoint le cas échéant" }} contre les listes internationales de sanctions et de personnes politiquement exposées.
                @if($conformite?->screening_ppe_sanctions_le)
                    Dernière vérification : {{ $conformite->screening_ppe_sanctions_le->translatedFormat('d F Y à H:i') }}.
                @else
                    Jamais lancée.
                @endif
            </p>
        </div>
        <form method="POST" action="{{ route('tenant.clients.conformite-lcbft.screening', $client) }}">
            @csrf
            <button type="submit" class="wd-conf-submit" style="min-width:0;padding:0 18px;">Lancer la vérification</button>
        </form>
    </div>

    @php $statutScreening = $conformite?->screening_ppe_sanctions_statut; @endphp

    @if($statutScreening === 'correspondance_potentielle')
        <div class="wd-conf-check" style="border-top:1px solid var(--line);margin-top:14px;padding-top:14px;">
            <span class="lbl" style="font-weight:700;color:var(--red);">Correspondance(s) potentielle(s) — à examiner avant toute entrée en relation.</span>
        </div>
        @foreach($conformite->screening_ppe_sanctions_resultats ?? [] as $ligne)
            @if(! empty($ligne['matches']))
                <div class="wd-conf-facteur">
                    <span class="pastille eleve"></span>
                    <p>
                        <strong>{{ $ligne['identite'] }}</strong> ({{ $ligne['role'] }}) —
                        @foreach($ligne['matches'] as $match)
                            {{ $match['nom'] }}{{ $match['score'] ? ' (score '.round($match['score'] * 100).'%)' : '' }}{{ ! empty($match['topics']) ? ' — '.implode(', ', $match['topics']) : '' }}@if(! $loop->last), @endif
                        @endforeach
                    </p>
                </div>
            @endif
        @endforeach
    @elseif($statutScreening === 'aucune_correspondance')
        <div class="wd-conf-check" style="border-top:1px solid var(--line);margin-top:14px;padding-top:14px;">
            <span class="lbl">Aucune correspondance trouvée</span>
            <span class="val ok">OK</span>
        </div>
    @elseif($statutScreening === 'erreur')
        <div class="wd-conf-check" style="border-top:1px solid var(--line);margin-top:14px;padding-top:14px;">
            <span class="lbl">Vérification non aboutie (erreur technique ou clé API non configurée)</span>
            <span class="val bad">Erreur</span>
        </div>
    @elseif($statutScreening === 'aucune_identite')
        <div class="wd-conf-check" style="border-top:1px solid var(--line);margin-top:14px;padding-top:14px;">
            <span class="lbl">Aucune identité à vérifier (KYC incomplet)</span>
            <span class="val neutre">—</span>
        </div>
    @endif
</div>

<div class="wd-conf-panel wd-conf-form">
    <h3>Revue de conformité</h3>
    <p class="sub">À compléter et enregistrer par le conseiller. Chaque enregistrement met à jour la date de dernière revue.</p>

    <form method="POST" action="{{ route('tenant.clients.conformite-lcbft.update', $client) }}">
        @csrf
        @method('PUT')

        <div class="wd-conf-field">
            <label for="niveau_risque_override">Niveau de risque retenu (surcharge le calcul automatique)</label>
            <select id="niveau_risque_override" name="niveau_risque_override">
                <option value="">— Suivre le calcul automatique ({{ $evaluation['niveau_calcule'] }}) —</option>
                <option value="faible" @selected(old('niveau_risque_override', $conformite?->niveau_risque_override) === 'faible')>Faible</option>
                <option value="standard" @selected(old('niveau_risque_override', $conformite?->niveau_risque_override) === 'standard')>Standard</option>
                <option value="eleve" @selected(old('niveau_risque_override', $conformite?->niveau_risque_override) === 'eleve')>Élevé</option>
            </select>
        </div>

        <div class="wd-conf-field">
            <label class="wd-conf-toggle" style="margin-bottom:0;">
                <input type="checkbox" name="origine_fonds_documentee" value="1" @checked(old('origine_fonds_documentee', $conformite?->origine_fonds_documentee))>
                Origine des fonds documentée
            </label>
        </div>

        <div class="wd-conf-field">
            <label for="origine_fonds_details">Détail de l'origine des fonds</label>
            <textarea id="origine_fonds_details" name="origine_fonds_details" placeholder="Ex : cession d'entreprise en 2024, héritage, épargne salariale...">{{ old('origine_fonds_details', $conformite?->origine_fonds_details) }}</textarea>
        </div>

        <div class="wd-conf-field">
            <label class="wd-conf-toggle" style="margin-bottom:0;">
                <input type="checkbox" name="vigilance_renforcee" value="1" @checked(old('vigilance_renforcee', $conformite?->vigilance_renforcee))>
                Activer la vigilance renforcée sur ce client
            </label>
        </div>

        <div class="wd-conf-field">
            <label>Motifs de vigilance constatés</label>
            <div class="wd-conf-motifs">
                @foreach($motifsDisponibles as $cle => $libelle)
                    <label class="wd-conf-motif">
                        <input type="checkbox" name="motifs_vigilance[]" value="{{ $cle }}" @checked(in_array($cle, old('motifs_vigilance', $conformite?->motifs_vigilance ?? [])))>
                        {{ $libelle }}
                    </label>
                @endforeach
            </div>
        </div>

        <div class="wd-conf-field">
            <label for="commentaire">Commentaire libre</label>
            <textarea id="commentaire" name="commentaire">{{ old('commentaire', $conformite?->commentaire) }}</textarea>
        </div>

        <div class="wd-conf-panel wd-conf-tracfin" style="padding:20px 22px;">
            <div class="wd-eyebrow">Tracfin</div>
            <p class="wd-conf-note">Confidentiel. Ne jamais informer le client d'une déclaration (article L.561-19 CMF).</p>

            <div class="wd-conf-field" style="margin-top:14px;">
                <label for="tracfin_statut">Statut</label>
                <select id="tracfin_statut" name="tracfin_statut" required>
                    <option value="neant" @selected(old('tracfin_statut', $conformite?->tracfin_statut ?? 'neant') === 'neant')>Néant</option>
                    <option value="en_cours" @selected(old('tracfin_statut', $conformite?->tracfin_statut) === 'en_cours')>Analyse en cours</option>
                    <option value="transmise" @selected(old('tracfin_statut', $conformite?->tracfin_statut) === 'transmise')>Déclaration transmise</option>
                </select>
            </div>

            <div class="wd-conf-field" style="margin-bottom:0;">
                <label for="tracfin_justification">Justification écrite (obligatoire même en l'absence de déclaration)</label>
                <textarea id="tracfin_justification" name="tracfin_justification">{{ old('tracfin_justification', $conformite?->tracfin_justification) }}</textarea>
            </div>
        </div>

        <div style="margin-top:20px;display:flex;justify-content:flex-end;">
            <button type="submit" class="wd-conf-submit">Enregistrer la revue</button>
        </div>
    </form>
</div>

</x-tenant-app-layout>
