<x-tenant-app-layout>
<style>
.wd-wrap{max-width:1000px;margin:auto;padding:30px 34px 60px}
.wd-head{display:flex;justify-content:space-between;align-items:end}
.wd-eyebrow{font-size:12px;color:var(--pink);font-weight:850;letter-spacing:.2em;text-transform:uppercase}
.wd-head h1{font-size:38px;line-height:1;margin:8px 0 0;letter-spacing:-.05em;font-weight:650}
.wd-head p{color:var(--muted);margin:10px 0 0;font-size:15px}
@media(max-width:650px){
.wd-wrap{padding:22px 14px 50px}
.wd-head{flex-direction:column;align-items:flex-start;gap:15px}
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
    grid-template-columns:repeat(6, 1fr);
    column-gap:20px;
    margin-top:6px;
    border-top:1px solid #eeeae7;
}
.wd-cabinet-field{
    position:relative;
    padding:16px 0 14px 0;
    border-bottom:1px solid #eeeae7;
    grid-column:span 6;
}
.wd-cabinet-field.wd-c2{grid-column:span 2}
.wd-cabinet-field.wd-c3{grid-column:span 3}
.wd-cabinet-field.wd-c4{grid-column:span 4}
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
.wd-cabinet-field input[type=email],
.wd-cabinet-field input[type=date],
.wd-cabinet-field select{
    width:100%;
    border:1px solid #ded9d4;
    border-radius:7px;
    padding:9px 11px;
    font-size:13px;
    color:#242424;
    background:#fff;
    font-family:inherit;
}
.wd-cabinet-field input:focus,
.wd-cabinet-field select:focus{
    outline:none;
    border-color:#f40087;
}
.wd-cabinet-field .wd-field-error{
    margin-top:6px;
    color:#b94d4d;
    font-size:11px;
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
.wd-address-results{
    position:absolute;
    z-index:10;
    background:#fff;
    border:1px solid #ded9d4;
    border-radius:7px;
    margin-top:4px;
    width:100%;
    max-height:220px;
    overflow-y:auto;
    box-shadow:0 8px 20px rgba(0,0,0,.08);
}
.wd-address-results li{
    padding:9px 11px;
    font-size:12px;
    color:#242424;
    cursor:pointer;
    list-style:none;
}
.wd-address-results li:hover{
    background:#f3f1ee;
}
.wd-mode-select{
    display:grid;
    grid-template-columns:repeat(3, 1fr);
    gap:10px;
    margin-top:6px;
}
.wd-mode-option{
    border:1px solid #ded9d4;
    border-radius:8px;
    padding:12px 14px;
    cursor:pointer;
    font-size:12px;
}
.wd-mode-option input{margin-right:8px}
.wd-mode-option strong{display:block;font-size:12px;margin-bottom:3px}
.wd-mode-option span{color:var(--muted);font-size:11px}
.wd-section-title{
    margin:26px 0 4px;
    font-size:11px;
    font-weight:800;
    letter-spacing:.1em;
    text-transform:uppercase;
    color:#9a928d;
}
</style>
<div class="wd-wrap">
    <section class="wd-head">
        <div>
            <div class="wd-eyebrow">Portefeuille</div>
            <h1>Nouveau client.</h1>
            <p>
                Créez la fiche du client. Il recevra un email pour définir son mot de passe.
            </p>
        </div>
    </section>
    <section class="wd-user-form">
        <form method="POST" action="{{ route('tenant.clients.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="wd-mode-select">
                <label class="wd-mode-option">
                    <input type="radio" name="mode" value="soi_meme" data-mode-radio {{ old('mode', 'soi_meme') === 'soi_meme' ? 'checked' : '' }}>
                    <strong>En son nom propre</strong>
                    <span>Le client agit pour lui-même.</span>
                </label>
                <label class="wd-mode-option">
                    <input type="radio" name="mode" value="represente_physique" data-mode-radio {{ old('mode') === 'represente_physique' ? 'checked' : '' }}>
                    <strong>Représente un mineur / majeur protégé</strong>
                    <span>Parent, tuteur, curateur, mandataire...</span>
                </label>
                <label class="wd-mode-option">
                    <input type="radio" name="mode" value="represente_morale" data-mode-radio {{ old('mode') === 'represente_morale' ? 'checked' : '' }}>
                    <strong>Représente une société</strong>
                    <span>Gérant, président, mandataire social...</span>
                </label>
            </div>
            <div class="wd-cabinet-information-grid" data-mode-bloc="represente_physique,represente_morale" hidden>
                <div class="wd-cabinet-field">
                    <label>Représentant déjà existant (optionnel)</label>
                    <select name="representant_existant_id" data-representant-existant>
                        <option value="">Nouveau représentant</option>
                        @foreach ($representantsExistants as $r)
                            <option value="{{ $r->user_id }}" {{ (string) old('representant_existant_id') === (string) $r->user_id ? 'selected' : '' }}>
                                {{ trim($r->prenom.' '.$r->nom) }} ({{ $r->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('representant_existant_id')
                    <div class="wd-field-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="wd-section-title" data-titre-representant>Le client</div>
            <div class="wd-cabinet-information-grid" data-representant-identite-grid>
                <div class="wd-cabinet-field wd-c2">
                    <label>Civilité</label>
                    <select name="civilite" data-civilite>
                        <option value="">-</option>
                        <option value="M." {{ old('civilite') === 'M.' ? 'selected' : '' }}>M.</option>
                        <option value="Mme" {{ old('civilite') === 'Mme' ? 'selected' : '' }}>Mme</option>
                    </select>
                    @error('civilite')
                    <div class="wd-field-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="wd-cabinet-field wd-c4">
                    <label>Date de naissance</label>
                    <input type="date" name="date_naissance" value="{{ old('date_naissance') }}" max="{{ now()->subYears(18)->format('Y-m-d') }}">
                    @error('date_naissance')
                    <div class="wd-field-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="wd-cabinet-field wd-c3">
                    <label>Prénom</label>
                    <input type="text" name="prenom" value="{{ old('prenom') }}" required autofocus>
                    @error('prenom')
                    <div class="wd-field-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="wd-cabinet-field wd-c3">
                    <label>Nom</label>
                    <input type="text" name="nom" value="{{ old('nom') }}" required>
                    @error('nom')
                    <div class="wd-field-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="wd-cabinet-field" data-nom-jeune-fille-field hidden>
                    <label>Nom de jeune fille</label>
                    <input type="text" name="nom_jeune_fille" value="{{ old('nom_jeune_fille') }}">
                    @error('nom_jeune_fille')
                    <div class="wd-field-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="wd-cabinet-field wd-c3">
                    <label>Téléphone mobile</label>
                    <input type="text" name="telephone_mobile" value="{{ old('telephone_mobile') }}" maxlength="10" inputmode="numeric" pattern="[0-9]{10}">
                    @error('telephone_mobile')
                    <div class="wd-field-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="wd-cabinet-field wd-c3">
                    <label>Téléphone domicile</label>
                    <input type="text" name="telephone_domicile" value="{{ old('telephone_domicile') }}" maxlength="10" inputmode="numeric" pattern="[0-9]{10}">
                    @error('telephone_domicile')
                    <div class="wd-field-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="wd-cabinet-field">
                    <label>E-mail</label>
                    <input type="email" name="email" value="{{ old('email') }}">
                    @error('email')
                    <div class="wd-field-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="wd-cabinet-field" x-data="addressAutocomplete('housenumber', @js(old('adresse', '')))">
                    <label>Adresse</label>
                    <input type="text" name="adresse" autocomplete="off" x-model="query" @input.debounce.300ms="search()">
                    <ul class="wd-address-results" x-show="open" @click.outside="open = false">
                        <template x-for="f in results" :key="f.properties.id">
                            <li @click="select(f, (p) => {
                                    query = p.name;
                                    document.querySelector('[name=code_postal]').value = p.postcode;
                                    document.querySelector('[name=ville]').value = p.city;
                                })" x-text="f.properties.label"></li>
                        </template>
                    </ul>
                    @error('adresse')
                    <div class="wd-field-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="wd-cabinet-field wd-c2">
                    <label>Code postal</label>
                    <input type="text" name="code_postal" value="{{ old('code_postal') }}">
                    @error('code_postal')
                    <div class="wd-field-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="wd-cabinet-field wd-c4">
                    <label>Ville</label>
                    <input type="text" name="ville" value="{{ old('ville') }}">
                    @error('ville')
                    <div class="wd-field-error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="wd-cabinet-field">
                    <label>Pays</label>
                    <input type="text" name="pays" value="{{ old('pays', 'France') }}">
                    @error('pays')
                    <div class="wd-field-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div data-mode-bloc="represente_physique" hidden>
                <div class="wd-section-title">Le titulaire représenté</div>
                <div class="wd-cabinet-information-grid">
                    <div class="wd-cabinet-field wd-c3">
                        <label>Prénom du titulaire</label>
                        <input type="text" name="titulaire_prenom" value="{{ old('titulaire_prenom') }}">
                        @error('titulaire_prenom')
                        <div class="wd-field-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="wd-cabinet-field wd-c3">
                        <label>Nom du titulaire</label>
                        <input type="text" name="titulaire_nom" value="{{ old('titulaire_nom') }}">
                        @error('titulaire_nom')
                        <div class="wd-field-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="wd-cabinet-field wd-c3">
                        <label>Date de naissance du titulaire</label>
                        <input type="date" name="titulaire_date_naissance" value="{{ old('titulaire_date_naissance') }}" max="{{ now()->format('Y-m-d') }}">
                        @error('titulaire_date_naissance')
                        <div class="wd-field-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="wd-cabinet-field wd-c3">
                        <label>Relation avec le titulaire</label>
                        <select name="relation">
                            <option value="">-</option>
                            <option value="parent" {{ old('relation') === 'parent' ? 'selected' : '' }}>Parent / représentant légal</option>
                            <option value="tuteur" {{ old('relation') === 'tuteur' ? 'selected' : '' }}>Tuteur</option>
                            <option value="curateur" {{ old('relation') === 'curateur' ? 'selected' : '' }}>Curateur</option>
                            <option value="mandataire" {{ old('relation') === 'mandataire' ? 'selected' : '' }}>Mandataire de protection future</option>
                        </select>
                        @error('relation')
                        <div class="wd-field-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="wd-cabinet-field">
                        <label>Justificatif (livret de famille, jugement...)</label>
                        <input type="file" name="justificatif" accept=".pdf,.jpg,.jpeg,.png">
                        @error('justificatif')
                        <div class="wd-field-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <div data-mode-bloc="represente_morale" hidden>
                <div class="wd-section-title">La société représentée</div>
                <div class="wd-cabinet-information-grid">
                    <div class="wd-cabinet-field wd-c4">
                        <label>Raison sociale</label>
                        <input type="text" name="raison_sociale" value="{{ old('raison_sociale') }}">
                        @error('raison_sociale')
                        <div class="wd-field-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="wd-cabinet-field wd-c2">
                        <label>Forme juridique</label>
                        <input type="text" name="forme_juridique" value="{{ old('forme_juridique') }}" placeholder="SARL, SAS, SCI...">
                        @error('forme_juridique')
                        <div class="wd-field-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="wd-cabinet-field">
                        <label>Numéro d'immatriculation (SIREN)</label>
                        <input type="text" name="numero_immatriculation" value="{{ old('numero_immatriculation') }}">
                        @error('numero_immatriculation')
                        <div class="wd-field-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="wd-cabinet-field">
                        <label>Adresse du siège social</label>
                        <input type="text" name="adresse_siege_social" value="{{ old('adresse_siege_social') }}">
                        @error('adresse_siege_social')
                        <div class="wd-field-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="wd-cabinet-field wd-c3">
                        <label>Qualité du représentant</label>
                        <select name="relation">
                            <option value="">-</option>
                            <option value="gerant" {{ old('relation') === 'gerant' ? 'selected' : '' }}>Gérant</option>
                            <option value="president" {{ old('relation') === 'president' ? 'selected' : '' }}>Président</option>
                            <option value="mandataire" {{ old('relation') === 'mandataire' ? 'selected' : '' }}>Mandataire social</option>
                        </select>
                        @error('relation')
                        <div class="wd-field-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="wd-cabinet-field wd-c3">
                        <label>Justificatif (Kbis, statuts...)</label>
                        <input type="file" name="justificatif" accept=".pdf,.jpg,.jpeg,.png">
                        @error('justificatif')
                        <div class="wd-field-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <button type="submit" class="wd-cabinet-save">Créer le client</button>
        </form>
    </section>
</div>
<script>
(function () {
    var civilite = document.querySelector('[data-civilite]');
    var champ = document.querySelector('[data-nom-jeune-fille-field]');
    if (! civilite || ! champ) { return; }
    function appliquer() {
        champ.hidden = civilite.value !== 'Mme';
    }
    civilite.addEventListener('change', appliquer);
    appliquer();
})();
(function () {
    var radios = document.querySelectorAll('[data-mode-radio]');
    var blocs = document.querySelectorAll('[data-mode-bloc]');
    var titreRepresentant = document.querySelector('[data-titre-representant]');
    var selectReuse = document.querySelector('[data-representant-existant]');
    var grilleIdentite = document.querySelector('[data-representant-identite-grid]');
    if (! radios.length) { return; }
    var libellesTitre = {
        soi_meme: 'Le client',
        represente_physique: 'Le représentant (celui qui se connecte)',
        represente_morale: 'Le représentant (celui qui se connecte)'
    };
    function appliquerReuse() {
        if (! selectReuse || ! grilleIdentite) { return; }
        var reuse = ! selectReuse.disabled && selectReuse.value !== '';
        grilleIdentite.hidden = reuse;
        grilleIdentite.querySelectorAll('input, select').forEach(function (champ) {
            champ.disabled = reuse;
        });
    }
    function appliquer() {
        var mode = document.querySelector('[data-mode-radio]:checked').value;
        blocs.forEach(function (bloc) {
            var actif = bloc.getAttribute('data-mode-bloc').split(',').indexOf(mode) !== -1;
            bloc.hidden = ! actif;
            bloc.querySelectorAll('input, select').forEach(function (champ) {
                champ.disabled = ! actif;
            });
        });
        if (titreRepresentant) {
            titreRepresentant.textContent = libellesTitre[mode] || libellesTitre.soi_meme;
        }
        appliquerReuse();
    }
    if (selectReuse) {
        selectReuse.addEventListener('change', appliquerReuse);
    }
    radios.forEach(function (radio) {
        radio.addEventListener('change', appliquer);
    });
    appliquer();
})();
</script>
</x-tenant-app-layout>
