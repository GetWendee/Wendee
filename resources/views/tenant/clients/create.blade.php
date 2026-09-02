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
        <form method="POST" action="{{ route('tenant.clients.store') }}">
            @csrf
            <div class="wd-cabinet-information-grid">
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
</script>
</x-tenant-app-layout>
