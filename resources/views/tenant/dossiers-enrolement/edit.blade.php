<x-tenant-app-layout>
<style>
body > div > nav,
body > div > header{display:none!important}
html,body{margin:0!important;background:#f3f1ee!important}
:root{
--bg:#f3f1ee;--white:#fff;--ink:#151515;--muted:#817b76;--line:#ded9d4;
--dark:#1b1716;--pink:#f40087;--red:#b94d4d;--green:#4d8760;
}
*{box-sizing:border-box}
.wd-wrap{max-width:1000px;margin:auto;padding:30px 34px 60px}
.wd-head{display:flex;justify-content:space-between;align-items:end}
.wd-eyebrow{font-size:12px;color:var(--pink);font-weight:850;letter-spacing:.2em;text-transform:uppercase}
.wd-head h1{font-size:38px;line-height:1;margin:8px 0 0;letter-spacing:-.05em;font-weight:650}
.wd-head p{color:var(--muted);margin:10px 0 0;font-size:15px}
@media(max-width:650px){
.wd-wrap{padding:22px 14px 50px}
.wd-head{flex-direction:column;align-items:flex-start;gap:15px}
}
.wd-badge{display:inline-block;padding:5px 11px;border-radius:20px;font-size:10px;font-weight:800;letter-spacing:.06em;text-transform:uppercase}
.wd-badge-invited{background:#f3f1ee;color:#817b76}
.wd-badge-onboarding{background:#fdf3e2;color:#b8860b}
.wd-badge-pending_validation{background:#eaf1fb;color:#2f5fa8}
.wd-badge-contract_pending{background:#f3e9fb;color:#7a3fb0}
.wd-badge-active{background:#f3f9f4;color:#4d8760}
.wd-badge-rejected{background:#fbeceb;color:#b94d4d}
.wd-user-success{margin-top:28px;padding:14px 18px;background:#f3f9f4;border:1px solid #d7e8da;border-radius:8px;color:#4d8760;font-size:12px;font-weight:700}
.wd-user-form{margin-top:28px;padding:23px;background:#fff;border:1px solid #ded9d4;border-radius:10px}
.wd-section-title{font-size:15px;font-weight:800;color:#151515;margin:0 0 4px}
.wd-section-sub{font-size:12px;color:#817b76;margin:0 0 6px}
.wd-cabinet-information-grid{display:grid;grid-template-columns:repeat(6, 1fr);column-gap:20px;margin-top:6px;border-top:1px solid #eeeae7}
.wd-cabinet-field{grid-column:span 6;padding:16px 0 14px 0;border-bottom:1px solid #eeeae7}
.wd-cabinet-field.wd-c3{grid-column:span 3}
.wd-cabinet-field.wd-c2{grid-column:span 2}
.wd-cabinet-field label{display:block;color:#9a928d;font-size:8px;font-weight:800;letter-spacing:.12em;text-transform:uppercase;margin-bottom:8px}
.wd-cabinet-field input[type=text],.wd-cabinet-field input[type=email],.wd-cabinet-field input[type=number],.wd-cabinet-field input[type=file]{width:100%;border:1px solid #ded9d4;border-radius:7px;padding:9px 11px;font-size:13px;color:#242424;background:#fff;font-family:inherit}
.wd-cabinet-field input:focus{outline:none;border-color:#f40087}
.wd-cabinet-field .wd-field-error{margin-top:6px;color:#b94d4d;font-size:11px}
.wd-cabinet-radio-group{display:flex;gap:8px}
.wd-cabinet-radio{flex:1;display:flex;align-items:center;justify-content:center;padding:9px 8px;border:1px solid #ded9d4;border-radius:7px;font-size:11px;font-weight:700;color:#817b76;cursor:pointer}
.wd-cabinet-radio input{display:none}
.wd-cabinet-radio:has(input:checked){border-color:#242424;color:#242424;background:#f3f1ee}
.wd-cabinet-save{margin-top:20px;display:inline-flex;align-items:center;gap:10px;padding:11px 18px;border-radius:7px;background:#242424;color:#fff;border:0;font-size:11px;font-weight:800;letter-spacing:.02em;cursor:pointer;font-family:inherit}
.wd-cabinet-save:hover{background:#171717}
.wd-cabinet-save:disabled{background:#ded9d4;cursor:not-allowed}
.wd-cabinet-checkbox-group{display:grid;grid-template-columns:repeat(auto-fill, minmax(210px, 1fr));gap:8px}
.wd-cabinet-checkbox-group-wrap{grid-template-columns:1fr}
.wd-cabinet-checkbox{display:flex;align-items:center;gap:8px;padding:8px 10px;border:1px solid #ded9d4;border-radius:7px;font-size:12px;color:#817b76;cursor:pointer}
.wd-cabinet-checkbox input{display:none}
.wd-cabinet-checkbox:has(input:checked){border-color:#242424;color:#242424;background:#f3f1ee}
.wd-cabinet-field select,.wd-cabinet-field input[type=date]{width:100%;border:1px solid #ded9d4;border-radius:7px;padding:9px 11px;font-size:13px;color:#242424;background:#fff;font-family:inherit}
.wd-cabinet-field select:focus,.wd-cabinet-field input[type=date]:focus{outline:none;border-color:#f40087}
.wd-just-row{display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid #eeeae7;font-size:12px}
.wd-just-row .wd-just-nom{color:#151515;font-weight:700}
.wd-just-row .wd-just-meta{color:#817b76}
.wd-nav-link{color:#817b76;font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.08em;text-decoration:none}
</style>
@php
$modeExercice = old('mode_exercice', $dossier->mode_exercice);
$badgeLabels = ['invited' => 'Invitation envoyée', 'onboarding' => 'Dossier en cours', 'pending_validation' => 'En validation', 'contract_pending' => 'Convention à signer', 'active' => 'Actif', 'rejected' => 'Refusé'];
$readonly = in_array($dossier->statut, ['pending_validation', 'contract_pending', 'active']);
@endphp
<div class="wd-wrap">
    <section class="wd-head">
        <div>
            <div class="wd-eyebrow">Mon dossier</div>
            <h1>Dossier d'enrôlement.</h1>
            <p>Complétez votre dossier de conseiller mandataire pour être validé par le cabinet.</p>
            <div style="margin-top:14px;"><span class="wd-badge wd-badge-{{ $dossier->statut }}">{{ $badgeLabels[$dossier->statut] ?? $dossier->statut }}</span></div>
        </div>
        <div><a href="{{ route('tenant.dashboard') }}" class="wd-nav-link">Retour au tableau de bord</a></div>
    </section>

    @if(session('status'))
    <section class="wd-user-success">{{ session('status') }}</section>
    @endif

    @if($readonly)
    <section class="wd-user-success" style="background:#eaf1fb;border-color:#d3e2f5;color:#2f5fa8;">
        Ce dossier est en cours de validation par le cabinet, il n'est plus modifiable.
    </section>
    @endif

    <form method="POST" action="{{ route('tenant.dossier-enrolement.update') }}">
        @csrf
        @method('PUT')
        <fieldset @if($readonly) disabled @endif style="border:0;padding:0;margin:0;">

        <section class="wd-user-form">
            <div class="wd-section-title">1. Identité</div>
            <div class="wd-section-sub">Exercice individuel ou en société.</div>
            <div class="wd-cabinet-information-grid">
                <div class="wd-cabinet-field">
                    <label>Mode d'exercice</label>
                    <div class="wd-cabinet-radio-group">
                        <label class="wd-cabinet-radio"><input type="radio" name="mode_exercice" value="individuel" {{ $modeExercice === 'individuel' ? 'checked' : '' }}><span>Individuel</span></label>
                        <label class="wd-cabinet-radio"><input type="radio" name="mode_exercice" value="societe" {{ $modeExercice === 'societe' ? 'checked' : '' }}><span>Société</span></label>
                    </div>
                </div>
                <div class="wd-cabinet-field wd-c3">
                    <label>Civilité</label>
                    <select name="civilite">
                        <option value="">-</option>
                        <option value="M." {{ old('civilite', $dossier->civilite) === 'M.' ? 'selected' : '' }}>M.</option>
                        <option value="Mme" {{ old('civilite', $dossier->civilite) === 'Mme' ? 'selected' : '' }}>Mme</option>
                    </select>
                </div>
                <div class="wd-cabinet-field wd-c3">
                    <label>Date de naissance</label>
                    <input type="date" name="date_naissance" value="{{ old('date_naissance', optional($dossier->date_naissance)->format('Y-m-d')) }}">
                </div>
                <div class="wd-cabinet-field wd-c3">
                    <label>Nationalité</label>
                    <input type="text" name="nationalite" value="{{ old('nationalite', $dossier->nationalite) }}">
                </div>
                <div class="wd-cabinet-field wd-c3">
                    <label>Pays</label>
                    <input type="text" name="pays" value="{{ old('pays', $dossier->pays) }}">
                </div>
                <div class="wd-cabinet-field wd-c3">
                    <label>Adresse</label>
                    <input type="text" name="adresse" value="{{ old('adresse', $dossier->adresse) }}">
                </div>
                <div class="wd-cabinet-field wd-c3">
                    <label>Code postal</label>
                    <input type="text" name="code_postal" value="{{ old('code_postal', $dossier->code_postal) }}">
                </div>
                <div class="wd-cabinet-field wd-c3">
                    <label>Ville</label>
                    <input type="text" name="ville" value="{{ old('ville', $dossier->ville) }}">
                </div>
                <div class="wd-cabinet-field wd-c3">
                    <label>Dénomination société</label>
                    <input type="text" name="societe_denomination" value="{{ old('societe_denomination', $dossier->societe_denomination) }}">
                </div>
                <div class="wd-cabinet-field wd-c3">
                    <label>Forme juridique</label>
                    <input type="text" name="societe_forme_juridique" value="{{ old('societe_forme_juridique', $dossier->societe_forme_juridique) }}">
                </div>
                <div class="wd-cabinet-field wd-c3">
                    <label>SIREN</label>
                    <input type="text" name="societe_siren" value="{{ old('societe_siren', $dossier->societe_siren) }}">
                </div>
                <div class="wd-cabinet-field wd-c3">
                    <label>SIRET</label>
                    <input type="text" name="societe_siret" value="{{ old('societe_siret', $dossier->societe_siret) }}">
                </div>
                <div class="wd-cabinet-field wd-c3">
                    <label>RCS (numéro)</label>
                    <input type="text" name="societe_rcs" value="{{ old('societe_rcs', $dossier->societe_rcs) }}">
                </div>
                <div class="wd-cabinet-field wd-c3">
                    <label>RCS (ville)</label>
                    <input type="text" name="societe_ville_rcs" value="{{ old('societe_ville_rcs', $dossier->societe_ville_rcs) }}">
                </div>
                <div class="wd-cabinet-field wd-c3">
                    <label>Capital social</label>
                    <input type="number" step="0.01" name="societe_capital_social" value="{{ old('societe_capital_social', $dossier->societe_capital_social) }}">
                </div>
                <div class="wd-cabinet-field wd-c3">
                    <label>Adresse du siège</label>
                    <input type="text" name="societe_adresse_siege" value="{{ old('societe_adresse_siege', $dossier->societe_adresse_siege) }}">
                </div>
                <div class="wd-cabinet-field wd-c3">
                    <label>Représentant (nom)</label>
                    <input type="text" name="representant_nom" value="{{ old('representant_nom', $dossier->representant_nom) }}">
                </div>
                <div class="wd-cabinet-field wd-c3">
                    <label>Représentant (prénom)</label>
                    <input type="text" name="representant_prenom" value="{{ old('representant_prenom', $dossier->representant_prenom) }}">
                </div>
                <div class="wd-cabinet-field">
                    <label>Représentant (fonction)</label>
                    <input type="text" name="representant_fonction" value="{{ old('representant_fonction', $dossier->representant_fonction) }}">
                </div>
            </div>
        </section>

        <section class="wd-user-form">
            <div class="wd-section-title">2. Statut et périmètre professionnel</div>
            <div class="wd-cabinet-information-grid">
                <div class="wd-cabinet-field">
                    <label>Domaines d'intervention</label>
                    <div class="wd-cabinet-checkbox-group">
                        @foreach(['Assurance', 'Banque', 'Finance', 'Immobilier'] as $domaine)
                        <label class="wd-cabinet-checkbox">
                            <input type="checkbox" name="domaines[]" value="{{ $domaine }}" {{ in_array($domaine, old('domaines', $dossier->domaines ?? [])) ? 'checked' : '' }}>
                            <span>{{ $domaine }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                <div class="wd-cabinet-field">
                    <label>Statuts réglementaires actuels</label>
                    <div class="wd-cabinet-checkbox-group wd-cabinet-checkbox-group-wrap">
                        @foreach(['IAS', 'MIA', 'COA', 'IOBSP', 'CIF', 'Agent immobilier'] as $statutRegl)
                        <label class="wd-cabinet-checkbox">
                            <input type="checkbox" name="statuts_reglementaires_actuels[]" value="{{ $statutRegl }}" {{ in_array($statutRegl, old('statuts_reglementaires_actuels', $dossier->statuts_reglementaires_actuels ?? [])) ? 'checked' : '' }}>
                            <span>{{ $statutRegl }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <section class="wd-user-form">
            <div class="wd-section-title">3. Immatriculation ORIAS</div>
            <div class="wd-cabinet-information-grid">
                <div class="wd-cabinet-field wd-c3">
                    <label>Numéro ORIAS</label>
                    <input type="text" name="orias_numero" value="{{ old('orias_numero', $dossier->orias_numero) }}">
                </div>
                <div class="wd-cabinet-field wd-c3">
                    <label>Organisme mandant déclaré</label>
                    <input type="text" name="orias_organisme_mandant" value="{{ old('orias_organisme_mandant', $dossier->orias_organisme_mandant) }}">
                </div>
                <div class="wd-cabinet-field wd-c3">
                    <label>Première immatriculation</label>
                    <input type="date" name="orias_date_premiere_immatriculation" value="{{ old('orias_date_premiere_immatriculation', optional($dossier->orias_date_premiere_immatriculation)->format('Y-m-d')) }}">
                </div>
                <div class="wd-cabinet-field wd-c3">
                    <label>Dernier renouvellement</label>
                    <input type="date" name="orias_date_dernier_renouvellement" value="{{ old('orias_date_dernier_renouvellement', optional($dossier->orias_date_dernier_renouvellement)->format('Y-m-d')) }}">
                </div>
                <div class="wd-cabinet-field">
                    <label>Catégories ORIAS</label>
                    <div class="wd-cabinet-checkbox-group">
                        @foreach(['COA', 'IAS', 'MIA', 'COBSP', 'IOBSP', 'CIF'] as $cat)
                        <label class="wd-cabinet-checkbox">
                            <input type="checkbox" name="orias_categories[]" value="{{ $cat }}" {{ in_array($cat, old('orias_categories', $dossier->orias_categories ?? [])) ? 'checked' : '' }}>
                            <span>{{ $cat }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                <div class="wd-cabinet-field">
                    <label>Immatriculation active</label>
                    <div class="wd-cabinet-radio-group">
                        <label class="wd-cabinet-radio"><input type="radio" name="orias_statut_actif" value="1" {{ old('orias_statut_actif', $dossier->orias_statut_actif) == 1 ? 'checked' : '' }}><span>Oui</span></label>
                        <label class="wd-cabinet-radio"><input type="radio" name="orias_statut_actif" value="0" {{ old('orias_statut_actif', $dossier->orias_statut_actif) === false || old('orias_statut_actif', $dossier->orias_statut_actif) === '0' ? 'checked' : '' }}><span>Non</span></label>
                    </div>
                </div>
            </div>
        </section>

        <section class="wd-user-form">
            <div class="wd-section-title">4. Capacité professionnelle et honorabilité</div>
            <div class="wd-cabinet-information-grid">
                <div class="wd-cabinet-field">
                    <label>Fondement de la capacité professionnelle</label>
                    <div class="wd-cabinet-checkbox-group">
                        @foreach(['diplome' => 'Diplôme', 'experience' => 'Expérience', 'formation' => 'Formation'] as $key => $label)
                        <label class="wd-cabinet-checkbox">
                            <input type="checkbox" name="capacite_pro_fondement[]" value="{{ $key }}" {{ in_array($key, old('capacite_pro_fondement', $dossier->capacite_pro_fondement ?? [])) ? 'checked' : '' }}>
                            <span>{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                <div class="wd-cabinet-field wd-c3">
                    <label>Diplôme (intitulé)</label>
                    <input type="text" name="capacite_pro_diplome_intitule" value="{{ old('capacite_pro_diplome_intitule', $dossier->capacite_pro_diplome_intitule) }}">
                </div>
                <div class="wd-cabinet-field wd-c3">
                    <label>Diplôme (établissement)</label>
                    <input type="text" name="capacite_pro_diplome_etablissement" value="{{ old('capacite_pro_diplome_etablissement', $dossier->capacite_pro_diplome_etablissement) }}">
                </div>
                <div class="wd-cabinet-field wd-c3">
                    <label>Diplôme (année)</label>
                    <input type="text" name="capacite_pro_diplome_annee" value="{{ old('capacite_pro_diplome_annee', $dossier->capacite_pro_diplome_annee) }}">
                </div>
                <div class="wd-cabinet-field wd-c3">
                    <label>Diplôme (niveau)</label>
                    <input type="text" name="capacite_pro_diplome_niveau" value="{{ old('capacite_pro_diplome_niveau', $dossier->capacite_pro_diplome_niveau) }}">
                </div>
                <div class="wd-cabinet-field wd-c3">
                    <label>Expérience (employeur)</label>
                    <input type="text" name="capacite_pro_experience_employeur" value="{{ old('capacite_pro_experience_employeur', $dossier->capacite_pro_experience_employeur) }}">
                </div>
                <div class="wd-cabinet-field wd-c3">
                    <label>Expérience (fonction)</label>
                    <input type="text" name="capacite_pro_experience_fonction" value="{{ old('capacite_pro_experience_fonction', $dossier->capacite_pro_experience_fonction) }}">
                </div>
                <div class="wd-cabinet-field">
                    <label>Expérience (période)</label>
                    <input type="text" name="capacite_pro_experience_periode" value="{{ old('capacite_pro_experience_periode', $dossier->capacite_pro_experience_periode) }}">
                </div>
                <div class="wd-cabinet-field wd-c3">
                    <label>Formation (organisme)</label>
                    <input type="text" name="capacite_pro_formation_organisme" value="{{ old('capacite_pro_formation_organisme', $dossier->capacite_pro_formation_organisme) }}">
                </div>
                <div class="wd-cabinet-field wd-c3">
                    <label>Formation (intitulé)</label>
                    <input type="text" name="capacite_pro_formation_intitule" value="{{ old('capacite_pro_formation_intitule', $dossier->capacite_pro_formation_intitule) }}">
                </div>
                <div class="wd-cabinet-field wd-c3">
                    <label>Formation (heures)</label>
                    <input type="number" name="capacite_pro_formation_heures" value="{{ old('capacite_pro_formation_heures', $dossier->capacite_pro_formation_heures) }}">
                </div>
                <div class="wd-cabinet-field wd-c3">
                    <label>Formation (date d'obtention)</label>
                    <input type="date" name="capacite_pro_formation_date_obtention" value="{{ old('capacite_pro_formation_date_obtention', optional($dossier->capacite_pro_formation_date_obtention)->format('Y-m-d')) }}">
                </div>
                <div class="wd-cabinet-field wd-c3">
                    <label>Formation continue réalisée cette année</label>
                    <div class="wd-cabinet-radio-group">
                        <label class="wd-cabinet-radio"><input type="radio" name="formation_continue_realisee" value="1" {{ old('formation_continue_realisee', $dossier->formation_continue_realisee) == 1 ? 'checked' : '' }}><span>Oui</span></label>
                        <label class="wd-cabinet-radio"><input type="radio" name="formation_continue_realisee" value="0" {{ old('formation_continue_realisee', $dossier->formation_continue_realisee) === false || old('formation_continue_realisee', $dossier->formation_continue_realisee) === '0' ? 'checked' : '' }}><span>Non</span></label>
                    </div>
                </div>
                <div class="wd-cabinet-field wd-c3">
                    <label>Formation continue (heures)</label>
                    <input type="number" name="formation_continue_heures" value="{{ old('formation_continue_heures', $dossier->formation_continue_heures) }}">
                </div>
                <div class="wd-cabinet-field wd-c3">
                    <label>Formation continue (année)</label>
                    <input type="text" name="formation_continue_annee" value="{{ old('formation_continue_annee', $dossier->formation_continue_annee) }}">
                </div>
                <div class="wd-cabinet-field wd-c3">
                    <label>Formation continue (organisme)</label>
                    <input type="text" name="formation_continue_organisme" value="{{ old('formation_continue_organisme', $dossier->formation_continue_organisme) }}">
                </div>
                <div class="wd-cabinet-field">
                    <label class="wd-cabinet-checkbox" style="display:inline-flex;">
                        <input type="checkbox" name="honorabilite_declaree" value="1" {{ old('honorabilite_declaree', $dossier->honorabilite_declaree) ? 'checked' : '' }}>
                        <span>Je déclare sur l'honneur remplir les conditions d'honorabilité requises pour l'exercice de mon activité.</span>
                    </label>
                </div>
            </div>
        </section>

        <section class="wd-user-form">
            <div class="wd-section-title">5. RCP et garantie financière</div>
            <div class="wd-cabinet-information-grid">
                <div class="wd-cabinet-field wd-c3">
                    <label>Assureur RCP</label>
                    <input type="text" name="rcp_assureur" value="{{ old('rcp_assureur', $dossier->rcp_assureur) }}">
                </div>
                <div class="wd-cabinet-field wd-c3">
                    <label>Numéro de police</label>
                    <input type="text" name="rcp_numero_police" value="{{ old('rcp_numero_police', $dossier->rcp_numero_police) }}">
                </div>
                <div class="wd-cabinet-field wd-c3">
                    <label>Date de début</label>
                    <input type="date" name="rcp_date_debut" value="{{ old('rcp_date_debut', optional($dossier->rcp_date_debut)->format('Y-m-d')) }}">
                </div>
                <div class="wd-cabinet-field wd-c3">
                    <label>Date d'expiration</label>
                    <input type="date" name="rcp_date_expiration" value="{{ old('rcp_date_expiration', optional($dossier->rcp_date_expiration)->format('Y-m-d')) }}">
                </div>
                <div class="wd-cabinet-field wd-c3">
                    <label>Montant de garantie</label>
                    <input type="number" step="0.01" name="rcp_montant_garantie" value="{{ old('rcp_montant_garantie', $dossier->rcp_montant_garantie) }}">
                </div>
                <div class="wd-cabinet-field wd-c3">
                    <label>Franchise</label>
                    <input type="number" step="0.01" name="rcp_franchise" value="{{ old('rcp_franchise', $dossier->rcp_franchise) }}">
                </div>
                <div class="wd-cabinet-field">
                    <label class="wd-cabinet-checkbox" style="display:inline-flex;">
                        <input type="checkbox" name="encaissement_fonds" value="1" {{ old('encaissement_fonds', $dossier->encaissement_fonds) ? 'checked' : '' }}>
                        <span>Je suis autorisé à encaisser des fonds pour le compte du cabinet.</span>
                    </label>
                </div>
                <div class="wd-cabinet-field wd-c3">
                    <label>Garantie financière (organisme)</label>
                    <input type="text" name="garantie_financiere_organisme" value="{{ old('garantie_financiere_organisme', $dossier->garantie_financiere_organisme) }}">
                </div>
                <div class="wd-cabinet-field wd-c3">
                    <label>Garantie financière (numéro)</label>
                    <input type="text" name="garantie_financiere_numero" value="{{ old('garantie_financiere_numero', $dossier->garantie_financiere_numero) }}">
                </div>
                <div class="wd-cabinet-field wd-c3">
                    <label>Garantie financière (montant)</label>
                    <input type="number" step="0.01" name="garantie_financiere_montant" value="{{ old('garantie_financiere_montant', $dossier->garantie_financiere_montant) }}">
                </div>
                <div class="wd-cabinet-field wd-c3">
                    <label>Garantie financière (échéance)</label>
                    <input type="date" name="garantie_financiere_date_echeance" value="{{ old('garantie_financiere_date_echeance', optional($dossier->garantie_financiere_date_echeance)->format('Y-m-d')) }}">
                </div>
            </div>
        </section>

        <section class="wd-user-form">
            <div class="wd-section-title">6. Périmètre du mandat</div>
            <div class="wd-cabinet-information-grid">
                <div class="wd-cabinet-field wd-c3">
                    <label>Zone géographique</label>
                    <select name="mandat_zone">
                        <option value="">-</option>
                        @foreach(['france_entiere' => 'France entière', 'region' => 'Région', 'departements' => 'Départements', 'autre' => 'Autre'] as $key => $label)
                        <option value="{{ $key }}" {{ old('mandat_zone', $dossier->mandat_zone) === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="wd-cabinet-field wd-c3">
                    <label>Précision de zone</label>
                    <input type="text" name="mandat_zone_detail" value="{{ old('mandat_zone_detail', $dossier->mandat_zone_detail) }}">
                </div>
                <div class="wd-cabinet-field">
                    <label>Clientèle</label>
                    <div class="wd-cabinet-checkbox-group">
                        @foreach(['particuliers' => 'Particuliers', 'professionnels' => 'Professionnels', 'tns' => 'TNS', 'dirigeants' => 'Dirigeants', 'personnes_morales' => 'Personnes morales'] as $key => $label)
                        <label class="wd-cabinet-checkbox">
                            <input type="checkbox" name="mandat_clientele[]" value="{{ $key }}" {{ in_array($key, old('mandat_clientele', $dossier->mandat_clientele ?? [])) ? 'checked' : '' }}>
                            <span>{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                <div class="wd-cabinet-field">
                    <label>Missions autorisées</label>
                    <div class="wd-cabinet-checkbox-group wd-cabinet-checkbox-group-wrap">
                        @foreach(['prospection' => 'Prospection', 'decouverte_client' => 'Découverte client', 'recueil_besoins' => 'Recueil des besoins et exigences', 'presentation_solutions' => 'Présentation des solutions', 'proposition' => 'Proposition', 'aide_souscription' => 'Aide à la souscription', 'signature_contrat' => 'Signature du contrat', 'suivi_relation' => 'Suivi de la relation client'] as $key => $label)
                        <label class="wd-cabinet-checkbox">
                            <input type="checkbox" name="mandat_missions_autorisees[]" value="{{ $key }}" {{ in_array($key, old('mandat_missions_autorisees', $dossier->mandat_missions_autorisees ?? [])) ? 'checked' : '' }}>
                            <span>{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                <div class="wd-cabinet-field">
                    <label>Missions interdites ou limitées</label>
                    <input type="text" name="mandat_missions_interdites" value="{{ old('mandat_missions_interdites', $dossier->mandat_missions_interdites) }}">
                </div>
            </div>
        </section>

        <section class="wd-user-form">
            <div class="wd-section-title">7. Procédures et engagements</div>
            <div class="wd-cabinet-information-grid">
                <div class="wd-cabinet-field">
                    <label>Procédures acceptées</label>
                    <div class="wd-cabinet-checkbox-group wd-cabinet-checkbox-group-wrap">
                        @foreach($procedures as $cle => $label)
                        @php
                        $dejaAcceptee = collect($dossier->procedures_acceptees ?? [])->contains(fn ($p) => ($p['cle'] ?? null) === $cle);
                        @endphp
                        <label class="wd-cabinet-checkbox">
                            <input type="checkbox" name="procedures[]" value="{{ $cle }}" {{ $dejaAcceptee || in_array($cle, old('procedures', [])) ? 'checked' : '' }}>
                            <span>{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <button type="submit" class="wd-cabinet-save">Enregistrer le dossier</button>
        </fieldset>
    </form>

    <section class="wd-user-form">
        <div class="wd-section-title">Justificatifs</div>
        <div class="wd-section-sub">Pièce d'identité, attestation ORIAS, diplôme, RCP, garantie financière, Kbis.</div>
        @forelse($dossier->justificatifs as $justificatif)
        <div class="wd-just-row">
            <div>
                <div class="wd-just-nom">{{ $typesJustificatifs[$justificatif->type] ?? $justificatif->type }} <span style="color:#817b76;font-weight:400;">v{{ $justificatif->version }}</span></div>
                <div class="wd-just-meta">{{ $justificatif->nom_original }} · déposé le {{ optional($justificatif->uploaded_at)->format('d/m/Y') }}</div>
            </div>
            <div class="wd-just-meta">{{ ucfirst(str_replace('_', ' ', $justificatif->statut)) }}</div>
        </div>
        @empty
        <p style="color:#817b76;font-size:12px;">Aucun justificatif téléversé.</p>
        @endforelse

        @unless($readonly)
        <form method="POST" action="{{ route('tenant.dossier-enrolement.justificatifs.store') }}" enctype="multipart/form-data" style="margin-top:18px;">
            @csrf
            <div class="wd-cabinet-information-grid">
                <div class="wd-cabinet-field wd-c3">
                    <label>Type de document</label>
                    <select name="type">
                        @foreach($typesJustificatifs as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="wd-cabinet-field wd-c3">
                    <label>Fichier (PDF ou image, 10 Mo max)</label>
                    <input type="file" name="fichier" required>
                </div>
                <div class="wd-cabinet-field wd-c2">
                    <label>Date d'émission</label>
                    <input type="date" name="date_emission">
                </div>
                <div class="wd-cabinet-field wd-c2">
                    <label>Début de validité</label>
                    <input type="date" name="date_debut_validite">
                </div>
                <div class="wd-cabinet-field wd-c2">
                    <label>Date d'expiration</label>
                    <input type="date" name="date_expiration">
                </div>
            </div>
            <button type="submit" class="wd-cabinet-save">Téléverser</button>
        </form>
        @endunless
    </section>

    @unless($readonly)
    <section class="wd-user-form">
        <div class="wd-section-title">Soumettre le dossier</div>
        <div class="wd-section-sub">Une fois toutes les sections complétées, soumettez votre dossier pour validation par le cabinet.</div>
        <form method="POST" action="{{ route('tenant.dossier-enrolement.submit') }}">
            @csrf
            <button type="submit" class="wd-cabinet-save">Soumettre pour validation</button>
        </form>
    </section>
    @endunless
</div>
</x-tenant-app-layout>
