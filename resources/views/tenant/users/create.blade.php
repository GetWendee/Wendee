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
.wd-sidebar{
position:fixed;inset:0 auto 0 0;width:232px;height:100vh;
background:#242424;color:#fff;padding:24px 16px;
display:flex;flex-direction:column;z-index:1000;overflow:hidden
}
.wd-logo{padding:0 7px 36px;font-size:23px;font-weight:800;letter-spacing:-.06em}
.wd-logo b{color:var(--pink)}
.wd-logo small{display:block;color:#7e7773;font-size:8px;letter-spacing:.25em;text-transform:uppercase;margin-top:5px}
.wd-nav{display:grid;gap:3px}
.wd-nav-section{margin:22px 10px 7px;font-size:9px;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:rgba(255,255,255,.30)}
.wd-nav a{display:flex;align-items:center;gap:11px;padding:10px 11px;margin:2px 0;border-radius:8px;color:rgba(255,255,255,.62);font-size:12px;text-decoration:none}
.wd-nav a svg{width:18px;height:18px;flex:0 0 18px;fill:none;stroke:currentColor;stroke-width:1.7;stroke-linecap:round;stroke-linejoin:round;color:rgba(255,255,255,.42)}
.wd-nav a.active{background:rgba(244,0,135,.11);color:#fff}
.wd-nav a.active svg{color:var(--pink)}
.wd-nav a:hover{background:rgba(255,255,255,.06);color:#fff}
.wd-bottom-nav{margin-top:auto;border-top:1px solid #302a28;padding-top:14px}
.wd-bottom-nav a,.wd-bottom-nav button{display:block;width:100%;padding:9px 10px;color:#8e8681;font-size:9px;text-transform:uppercase;letter-spacing:.12em;text-align:left;background:none;border:0}
.wd-main{margin-left:232px;min-height:100vh;padding-top:68px}
.wd-topbar{
position:fixed;top:0;left:232px;right:0;height:68px;z-index:900;
background:rgba(255,255,255,.94);border-bottom:1px solid var(--line);
display:flex;align-items:center;justify-content:space-between;padding:0 34px;
backdrop-filter:blur(10px)
}
.wd-crumb{color:#96908b;font-size:11px;font-weight:800;letter-spacing:.17em;text-transform:uppercase}
.wd-who{display:flex;gap:10px;align-items:center;text-align:right}
.wd-who strong{font-size:14px}
.wd-who small{display:block;color:#99918c;font-size:10px;text-transform:uppercase;letter-spacing:.1em}
.wd-top-avatar{width:34px;height:34px;border-radius:50%;background:var(--pink);display:grid;place-items:center;color:#fff;font-size:10px;font-weight:800}
.wd-wrap{max-width:1000px;margin:auto;padding:30px 34px 60px}
.wd-head{display:flex;justify-content:space-between;align-items:end}
.wd-eyebrow{font-size:12px;color:var(--pink);font-weight:850;letter-spacing:.2em;text-transform:uppercase}
.wd-head h1{font-size:38px;line-height:1;margin:8px 0 0;letter-spacing:-.05em;font-weight:650}
.wd-head p{color:var(--muted);margin:10px 0 0;font-size:15px}
@media(max-width:1050px){
.wd-sidebar{width:72px}
.wd-logo{font-size:0}.wd-logo b{font-size:22px}.wd-logo small,.wd-nav-section,.wd-bottom-nav{display:none}
.wd-nav a{justify-content:center}
.wd-main{margin-left:72px}
.wd-topbar{left:72px}
}
@media(max-width:650px){
.wd-wrap{padding:22px 14px 50px}
.wd-head{flex-direction:column;align-items:flex-start;gap:15px}
}
.wd-user-success{
    margin-top:28px;
    padding:14px 18px;
    background:#f3f9f4;
    border:1px solid #d7e8da;
    border-radius:8px;
    color:#4d8760;
    font-size:12px;
    font-weight:700;
}
.wd-user-form{
    margin-top:28px;
    padding:23px;
    background:#fff;
    border:1px solid #ded9d4;
    border-radius:10px;
}
.wd-cabinet-information-grid{
    display:grid;
    grid-template-columns:1fr;
    gap:0;
    margin-top:6px;
    border-top:1px solid #eeeae7;
}
.wd-cabinet-field{
    padding:16px 0 14px 0;
    border-bottom:1px solid #eeeae7;
}
.wd-cabinet-information-grid{grid-template-columns:repeat(6, 1fr);column-gap:20px;}
.wd-cabinet-field{grid-column:span 6;}
.wd-cabinet-field.wd-c3{grid-column:span 3}
.wd-cabinet-field label{
    display:block;
    color:#9a928d;
    font-size:8px;
    font-weight:800;
    letter-spacing:.12em;
    text-transform:uppercase;
    margin-bottom:8px;
}
.wd-cabinet-field input[type=text],
.wd-cabinet-field input[type=email]{
    width:100%;
    border:1px solid #ded9d4;
    border-radius:7px;
    padding:9px 11px;
    font-size:13px;
    color:#242424;
    background:#fff;
    font-family:inherit;
}
.wd-cabinet-field input:focus{
    outline:none;
    border-color:#f40087;
}
.wd-cabinet-field .wd-field-error{
    margin-top:6px;
    color:#b94d4d;
    font-size:11px;
}
.wd-cabinet-radio-group{
    display:flex;
    gap:8px;
}
.wd-cabinet-radio{
    flex:1;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:9px 8px;
    border:1px solid #ded9d4;
    border-radius:7px;
    font-size:11px;
    font-weight:700;
    color:#817b76;
    cursor:pointer;
}
.wd-cabinet-radio input{
    display:none;
}
.wd-cabinet-radio:has(input:checked){
    border-color:#242424;
    color:#242424;
    background:#f3f1ee;
}
.wd-cabinet-save{
    margin-top:20px;
    display:inline-flex;
    align-items:center;
    gap:10px;
    padding:11px 18px;
    border-radius:7px;
    background:#242424;
    color:#fff;
    border:0;
    font-size:11px;
    font-weight:800;
    letter-spacing:.02em;
    cursor:pointer;
    font-family:inherit;
}
.wd-cabinet-save:hover{
    background:#171717;
}
.wd-cabinet-checkbox-group{
    display:grid;
    grid-template-columns:repeat(auto-fill, minmax(210px, 1fr));
    gap:8px;
}
.wd-cabinet-checkbox-group-wrap{
    grid-template-columns:1fr;
}
.wd-cabinet-checkbox{
    display:flex;
    align-items:center;
    gap:8px;
    padding:8px 10px;
    border:1px solid #ded9d4;
    border-radius:7px;
    font-size:12px;
    color:#817b76;
    cursor:pointer;
}
.wd-cabinet-checkbox input{
    display:none;
}
.wd-cabinet-checkbox:has(input:checked){
    border-color:#242424;
    color:#242424;
    background:#f3f1ee;
}
.wd-cabinet-field select,
.wd-cabinet-field input[type=date]{
    width:100%;
    border:1px solid #ded9d4;
    border-radius:7px;
    padding:9px 11px;
    font-size:13px;
    color:#242424;
    background:#fff;
    font-family:inherit;
}
.wd-cabinet-field select:focus,
.wd-cabinet-field input[type=date]:focus{
    outline:none;
    border-color:#f40087;
}
</style>
@php
$selectedRole = old('role') ?: request('role', $roles[0] ?? null);
$roleTitres = ['conseiller' => 'Nouveau conseiller.', 'apporteur' => 'Nouvel apporteur.'];
$roleSousTitres = [
    'conseiller' => "Créez un compte conseiller. Il recevra un email pour définir son mot de passe.",
    'apporteur' => "Créez un compte apporteur. Il recevra un email pour définir son mot de passe.",
];
@endphp
<div class="wd-wrap">
    @if(session('user_created'))
    <section class="wd-user-success">
        Le compte de {{ session('user_created') }} a été créé. Un email lui a été envoyé pour définir son mot de passe.
    </section>
    @endif
    <section class="wd-head">
        <div>
            <div class="wd-eyebrow">Équipe</div>
            <h1>{{ $roleTitres[$selectedRole] ?? 'Nouvel utilisateur.' }}</h1>
            <p>
                {{ $roleSousTitres[$selectedRole] ?? "Il recevra un email pour définir son mot de passe." }}
            </p>
        </div>
    </section>
    <section class="wd-user-form">
        <form method="POST" action="{{ route('tenant.users.store') }}">
            @csrf
            <input type="hidden" name="role" value="{{ $selectedRole }}">
            <div class="wd-cabinet-information-grid">
                <div class="wd-cabinet-field wd-c3">
                    <label>Prénom</label>
                    <input type="text" name="prenom" value="{{ old('prenom') }}">
                    @error('prenom')
                    <div class="wd-field-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="wd-cabinet-field wd-c3">
                    <label>Nom</label>
                    <input type="text" name="nom" value="{{ old('nom') }}">
                    @error('nom')
                    <div class="wd-field-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="wd-cabinet-field wd-c3">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ old('email') }}">
                    @error('email')
                    <div class="wd-field-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="wd-cabinet-field wd-c3">
                    <label>Téléphone</label>
                    <input type="text" name="telephone" value="{{ old('telephone') }}" maxlength="10" inputmode="numeric" pattern="[0-9]{10}">
                    @error('telephone')
                    <div class="wd-field-error">{{ $message }}</div>
                    @enderror
                </div>
                @if($selectedRole === 'conseiller')
                @php
                $habilitationsOptions = [
                    "Courtier d'assurance (COA – courtier)",
                    "Mandataire d'assurance (IAS – mandataire)",
                    "Mandataire d'intermédiaire d'assurance (MIA)",
                    'Courtier en opérations de banque et service de paiement (COBSP - courtier)',
                    'Mandataire IOBSP',
                    'Mandataire non exclusif IOBSP',
                    'Mandataire exclusif IOBSP',
                    'Conseiller en investissements financiers (CIF)',
                    'Agent immobilier',
                    "Mandataire d'agent immobilier",
                    'Salarié',
                ];
                @endphp
                <div class="wd-cabinet-field">
                    <label>Périmètres d'intervention</label>
                    <div class="wd-cabinet-checkbox-group">
                        @foreach(['Assurance', 'Banque', 'Finance', 'Immobilier'] as $perimetre)
                        <label class="wd-cabinet-checkbox">
                            <input type="checkbox" name="perimetres[]" value="{{ $perimetre }}" {{ in_array($perimetre, old('perimetres', [])) ? 'checked' : '' }}>
                            <span>{{ $perimetre }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                <div class="wd-cabinet-field">
                    <label>Habilitations</label>
                    <div class="wd-cabinet-checkbox-group wd-cabinet-checkbox-group-wrap">
                        @foreach($habilitationsOptions as $habilitation)
                        <label class="wd-cabinet-checkbox">
                            <input type="checkbox" name="habilitations[]" value="{{ $habilitation }}" {{ in_array($habilitation, old('habilitations', [])) ? 'checked' : '' }}>
                            <span>{{ $habilitation }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                <div class="wd-cabinet-field">
                    <label>Numéro ORIAS</label>
                    <input type="text" name="numero_orias" value="{{ old('numero_orias') }}">
                    @error('numero_orias')
                    <div class="wd-field-error">{{ $message }}</div>
                    @enderror
                </div>
                @endif
                @if($selectedRole === 'apporteur')
                @php
                $formesJuridiques = [
                    'ei' => 'Entreprise individuelle',
                    'eurl' => 'EURL',
                    'sasu' => 'SASU',
                    'sas' => 'SAS',
                    'sarl' => 'SARL',
                    'sa' => 'Société Anonyme',
                    'snc' => 'SNC',
                    'scp' => 'SCP',
                ];
                $apporteurRolesOptions = [
                    'mise_relation' => 'Se limite à la mise en relation',
                    'presentation' => 'Présente des produits ou solutions',
                    'analyse' => 'Analyse les besoins clients',
                    'conseil' => 'Fournit un conseil ou une recommandation',
                ];
                $statutReglementeOptions = [
                    'iobsp' => 'IOBSP',
                    'ias' => 'IAS',
                    'cif' => 'CIF',
                    'mia' => 'MIA',
                ];
                $autoriteControleOptions = [
                    'acpr' => 'ACPR',
                    'amf' => 'AMF',
                    'autre' => 'Autre',
                ];
                @endphp
                <div class="wd-cabinet-field">
                    <label>Forme juridique</label>
                    <select name="apporteur_forme_juridique">
                        <option value="">Choisir</option>
                        @foreach($formesJuridiques as $value => $formeLabel)
                        <option value="{{ $value }}" {{ old('apporteur_forme_juridique') === $value ? 'selected' : '' }}>{{ $formeLabel }}</option>
                        @endforeach
                    </select>
                    @error('apporteur_forme_juridique')
                    <div class="wd-field-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="wd-cabinet-field">
                    <label>Dénomination sociale</label>
                    <input type="text" name="apporteur_denomination_sociale" value="{{ old('apporteur_denomination_sociale') }}">
                    @error('apporteur_denomination_sociale')
                    <div class="wd-field-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="wd-cabinet-field">
                    <label>Date de création</label>
                    <input type="date" name="apporteur_date_creation" value="{{ old('apporteur_date_creation') }}">
                    @error('apporteur_date_creation')
                    <div class="wd-field-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="wd-cabinet-field">
                    <label>Numéro SIREN</label>
                    <input type="text" name="apporteur_siren" value="{{ old('apporteur_siren') }}">
                    @error('apporteur_siren')
                    <div class="wd-field-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="wd-cabinet-field">
                    <label>Numéro SIRET</label>
                    <input type="text" name="apporteur_siret" value="{{ old('apporteur_siret') }}">
                    @error('apporteur_siret')
                    <div class="wd-field-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="wd-cabinet-field">
                    <label>Ville du RCS</label>
                    <input type="text" name="apporteur_rcs_ville" value="{{ old('apporteur_rcs_ville') }}">
                    @error('apporteur_rcs_ville')
                    <div class="wd-field-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="wd-cabinet-field">
                    <label>Numéro RCS</label>
                    <input type="text" name="apporteur_rcs_numero" value="{{ old('apporteur_rcs_numero') }}">
                    @error('apporteur_rcs_numero')
                    <div class="wd-field-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="wd-cabinet-field">
                    <label>Représentant légal</label>
                    <input type="text" name="apporteur_representant_legal" value="{{ old('apporteur_representant_legal') }}">
                    @error('apporteur_representant_legal')
                    <div class="wd-field-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="wd-cabinet-field">
                    <label>Immatriculé à l'ORIAS ?</label>
                    <div class="wd-cabinet-radio-group">
                        <label class="wd-cabinet-radio">
                            <input type="radio" name="apporteur_immatricule_orias" value="1" {{ old('apporteur_immatricule_orias') === '1' ? 'checked' : '' }}>
                            <span>Oui</span>
                        </label>
                        <label class="wd-cabinet-radio">
                            <input type="radio" name="apporteur_immatricule_orias" value="0" {{ old('apporteur_immatricule_orias') === '0' ? 'checked' : '' }}>
                            <span>Non</span>
                        </label>
                    </div>
                    @error('apporteur_immatricule_orias')
                    <div class="wd-field-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="wd-cabinet-field">
                    <label>Rôle de l'apporteur</label>
                    <div class="wd-cabinet-checkbox-group wd-cabinet-checkbox-group-wrap">
                        @foreach($apporteurRolesOptions as $value => $roleLabel)
                        <label class="wd-cabinet-checkbox">
                            <input type="checkbox" name="apporteur_roles[]" value="{{ $value }}" {{ in_array($value, old('apporteur_roles', [])) ? 'checked' : '' }}>
                            <span>{{ $roleLabel }}</span>
                        </label>
                        @endforeach
                    </div>
                    @error('apporteur_roles')
                    <div class="wd-field-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="wd-cabinet-field">
                    <label>Précisions sur le rôle</label>
                    <input type="text" name="apporteur_role_commentaire" value="{{ old('apporteur_role_commentaire') }}">
                    @error('apporteur_role_commentaire')
                    <div class="wd-field-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="wd-cabinet-field">
                    <label>Numéro ORIAS</label>
                    <input type="text" name="apporteur_orias_numero" value="{{ old('apporteur_orias_numero') }}">
                    @error('apporteur_orias_numero')
                    <div class="wd-field-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="wd-cabinet-field">
                    <label>Statut réglementé</label>
                    <div class="wd-cabinet-checkbox-group">
                        @foreach($statutReglementeOptions as $value => $statutLabel)
                        <label class="wd-cabinet-checkbox">
                            <input type="checkbox" name="apporteur_statut_reglemente[]" value="{{ $value }}" {{ in_array($value, old('apporteur_statut_reglemente', [])) ? 'checked' : '' }}>
                            <span>{{ $statutLabel }}</span>
                        </label>
                        @endforeach
                    </div>
                    @error('apporteur_statut_reglemente')
                    <div class="wd-field-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="wd-cabinet-field">
                    <label>Autorité de contrôle</label>
                    <div class="wd-cabinet-checkbox-group">
                        @foreach($autoriteControleOptions as $value => $autoriteLabel)
                        <label class="wd-cabinet-checkbox">
                            <input type="checkbox" name="apporteur_autorite_controle[]" value="{{ $value }}" {{ in_array($value, old('apporteur_autorite_controle', [])) ? 'checked' : '' }}>
                            <span>{{ $autoriteLabel }}</span>
                        </label>
                        @endforeach
                    </div>
                    @error('apporteur_autorite_controle')
                    <div class="wd-field-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="wd-cabinet-field">
                    <label>Disposez-vous d'une RCP professionnelle ?</label>
                    <div class="wd-cabinet-radio-group">
                        <label class="wd-cabinet-radio">
                            <input type="radio" name="apporteur_rcp" value="1" {{ old('apporteur_rcp') === '1' ? 'checked' : '' }}>
                            <span>Oui</span>
                        </label>
                        <label class="wd-cabinet-radio">
                            <input type="radio" name="apporteur_rcp" value="0" {{ old('apporteur_rcp') === '0' ? 'checked' : '' }}>
                            <span>Non</span>
                        </label>
                    </div>
                    @error('apporteur_rcp')
                    <div class="wd-field-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="wd-cabinet-field">
                    <label>Compagnie d'assurance RCP</label>
                    <input type="text" name="apporteur_rcp_compagnie" value="{{ old('apporteur_rcp_compagnie') }}">
                    @error('apporteur_rcp_compagnie')
                    <div class="wd-field-error">{{ $message }}</div>
                    @enderror
                </div>
                @php
                $modeAcquisitionOptions = [
                    'reseau_personnel' => 'Réseau personnel',
                    'reseau_professionnel' => 'Réseau professionnel',
                    'digital' => 'Digital',
                    'publicite' => 'Publicité',
                ];
                $typologieClientOptions = [
                    'particuliers' => 'Particuliers',
                    'professionnels' => 'Professionnels',
                ];
                $volumeMensuelOptions = [
                    '0_5' => 'De 0 à 5 clients',
                    '5_10' => 'De 5 à 10 clients',
                    '10_20' => 'De 10 à 20 clients',
                    'plus_20' => 'Plus de 20 clients',
                ];
                $zoneGeographiqueOptions = [
                    'local' => 'Local',
                    'regional' => 'Régional',
                    'national' => 'National',
                    'international' => 'International',
                ];
                $typeRemunerationOptions = [
                    'fixe' => 'Fixe',
                    'pourcentage' => 'Pourcentage',
                    'mixte' => 'Mixte',
                ];
                $declenchementOptions = [
                    'mise_en_relation' => 'À la mise en relation',
                    'signature' => 'À la signature',
                    'encaissement' => "À l'encaissement",
                ];
                @endphp
                <div class="wd-cabinet-field">
                    <label>Rattaché à un autre réseau ?</label>
                    <div class="wd-cabinet-radio-group">
                        <label class="wd-cabinet-radio">
                            <input type="radio" name="apporteur_autre_reseau" value="1" {{ old('apporteur_autre_reseau') === '1' ? 'checked' : '' }}>
                            <span>Oui</span>
                        </label>
                        <label class="wd-cabinet-radio">
                            <input type="radio" name="apporteur_autre_reseau" value="0" {{ old('apporteur_autre_reseau') === '0' ? 'checked' : '' }}>
                            <span>Non</span>
                        </label>
                    </div>
                    @error('apporteur_autre_reseau')
                    <div class="wd-field-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="wd-cabinet-field">
                    <label>Nom du réseau</label>
                    <input type="text" name="apporteur_nom_reseau" value="{{ old('apporteur_nom_reseau') }}">
                    @error('apporteur_nom_reseau')
                    <div class="wd-field-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="wd-cabinet-field">
                    <label>Mode d'acquisition des contacts</label>
                    <div class="wd-cabinet-checkbox-group">
                        @foreach($modeAcquisitionOptions as $value => $modeLabel)
                        <label class="wd-cabinet-checkbox">
                            <input type="checkbox" name="apporteur_mode_acquisition[]" value="{{ $value }}" {{ in_array($value, old('apporteur_mode_acquisition', [])) ? 'checked' : '' }}>
                            <span>{{ $modeLabel }}</span>
                        </label>
                        @endforeach
                    </div>
                    @error('apporteur_mode_acquisition')
                    <div class="wd-field-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="wd-cabinet-field">
                    <label>Typologie de clientèle</label>
                    <div class="wd-cabinet-checkbox-group">
                        @foreach($typologieClientOptions as $value => $typologieLabel)
                        <label class="wd-cabinet-checkbox">
                            <input type="checkbox" name="apporteur_typologie_client[]" value="{{ $value }}" {{ in_array($value, old('apporteur_typologie_client', [])) ? 'checked' : '' }}>
                            <span>{{ $typologieLabel }}</span>
                        </label>
                        @endforeach
                    </div>
                    @error('apporteur_typologie_client')
                    <div class="wd-field-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="wd-cabinet-field">
                    <label>Nombre de mises en relation mensuelles estimées</label>
                    <div class="wd-cabinet-radio-group">
                        @foreach($volumeMensuelOptions as $value => $volumeLabel)
                        <label class="wd-cabinet-radio">
                            <input type="radio" name="apporteur_volume_mensuel_reco" value="{{ $value }}" {{ old('apporteur_volume_mensuel_reco') === $value ? 'checked' : '' }}>
                            <span>{{ $volumeLabel }}</span>
                        </label>
                        @endforeach
                    </div>
                    @error('apporteur_volume_mensuel_reco')
                    <div class="wd-field-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="wd-cabinet-field">
                    <label>Zone géographique d'intervention</label>
                    <div class="wd-cabinet-radio-group">
                        @foreach($zoneGeographiqueOptions as $value => $zoneLabel)
                        <label class="wd-cabinet-radio">
                            <input type="radio" name="apporteur_zone_geographique" value="{{ $value }}" {{ old('apporteur_zone_geographique') === $value ? 'checked' : '' }}>
                            <span>{{ $zoneLabel }}</span>
                        </label>
                        @endforeach
                    </div>
                    @error('apporteur_zone_geographique')
                    <div class="wd-field-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="wd-cabinet-field">
                    <label>Type de rémunération</label>
                    <div class="wd-cabinet-radio-group">
                        @foreach($typeRemunerationOptions as $value => $typeLabel)
                        <label class="wd-cabinet-radio">
                            <input type="radio" name="apporteur_type_remuneration" value="{{ $value }}" {{ old('apporteur_type_remuneration') === $value ? 'checked' : '' }}>
                            <span>{{ $typeLabel }}</span>
                        </label>
                        @endforeach
                    </div>
                    @error('apporteur_type_remuneration')
                    <div class="wd-field-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="wd-cabinet-field">
                    <label>Pourcentage de rémunération</label>
                    <input type="text" name="apporteur_remuneration_pourcentage" value="{{ old('apporteur_remuneration_pourcentage') }}">
                    @error('apporteur_remuneration_pourcentage')
                    <div class="wd-field-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="wd-cabinet-field">
                    <label>Montant fixe (€)</label>
                    <input type="text" name="apporteur_remuneration_fixe" value="{{ old('apporteur_remuneration_fixe') }}">
                    @error('apporteur_remuneration_fixe')
                    <div class="wd-field-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="wd-cabinet-field">
                    <label>Moment de déclenchement</label>
                    <select name="apporteur_declenchement_remuneration">
                        <option value="">Choisir</option>
                        @foreach($declenchementOptions as $value => $declenchementLabel)
                        <option value="{{ $value }}" {{ old('apporteur_declenchement_remuneration') === $value ? 'selected' : '' }}>{{ $declenchementLabel }}</option>
                        @endforeach
                    </select>
                    @error('apporteur_declenchement_remuneration')
                    <div class="wd-field-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="wd-cabinet-field">
                    <label>Rémunération liée à un produit réglementé ?</label>
                    <div class="wd-cabinet-radio-group">
                        <label class="wd-cabinet-radio">
                            <input type="radio" name="apporteur_remuneration_produit_reglemente" value="1" {{ old('apporteur_remuneration_produit_reglemente') === '1' ? 'checked' : '' }}>
                            <span>Oui</span>
                        </label>
                        <label class="wd-cabinet-radio">
                            <input type="radio" name="apporteur_remuneration_produit_reglemente" value="0" {{ old('apporteur_remuneration_produit_reglemente') === '0' ? 'checked' : '' }}>
                            <span>Non</span>
                        </label>
                    </div>
                    @error('apporteur_remuneration_produit_reglemente')
                    <div class="wd-field-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="wd-cabinet-field">
                    <label>Engagements</label>
                    <div class="wd-cabinet-checkbox-group wd-cabinet-checkbox-group-wrap">
                        <label class="wd-cabinet-checkbox">
                            <input type="checkbox" name="apporteur_engagement_sans_conseil" value="1" {{ old('apporteur_engagement_sans_conseil') ? 'checked' : '' }}>
                            <span>Je m'engage à ne fournir aucun conseil</span>
                        </label>
                        <label class="wd-cabinet-checkbox">
                            <input type="checkbox" name="apporteur_engagement_sans_presentation" value="1" {{ old('apporteur_engagement_sans_presentation') ? 'checked' : '' }}>
                            <span>Je m'engage à ne présenter aucun produit</span>
                        </label>
                        <label class="wd-cabinet-checkbox">
                            <input type="checkbox" name="apporteur_engagement_sans_encaissement" value="1" {{ old('apporteur_engagement_sans_encaissement') ? 'checked' : '' }}>
                            <span>Je m'engage à ne collecter aucun fonds</span>
                        </label>
                        <label class="wd-cabinet-checkbox">
                            <input type="checkbox" name="apporteur_engagement_orientation" value="1" {{ old('apporteur_engagement_orientation') ? 'checked' : '' }}>
                            <span>Je m'engage à orienter vers un professionnel habilité</span>
                        </label>
                        <label class="wd-cabinet-checkbox">
                            <input type="checkbox" name="apporteur_engagement_conformite" value="1" {{ old('apporteur_engagement_conformite') ? 'checked' : '' }}>
                            <span>Je m'engage à respecter la réglementation</span>
                        </label>
                    </div>
                    @error('apporteur_engagement_sans_conseil')
                    <div class="wd-field-error">{{ $message }}</div>
                    @enderror
                </div>
                @endif
            </div>
            <button type="submit" class="wd-cabinet-save">Créer le compte</button>
        </form>
    </section>
</div>
</x-tenant-app-layout>
