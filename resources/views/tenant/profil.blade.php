<x-tenant-app-layout>
<div class="wd-wrap wd-profil-wrap">
    <div class="wd-head">
        <div>
            <div class="wd-eyebrow">Compte</div>
            <h1>Mon profil</h1>
            <p>Vos informations personnelles.</p>
        </div>
    </div>

    @if (session('status') === 'profil-mis-a-jour')
    <section class="wd-user-success">Profil mis à jour.</section>
    @endif

    <div class="wd-profil-form">
        <form method="POST" action="{{ route('tenant.profil.update') }}">
            @csrf
            @method('put')
            <div class="wd-cabinet-information-grid">
                <div class="wd-cabinet-field wd-c3">
                    <label for="name">Nom</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required autocomplete="name">
                    @error('name')<div class="wd-field-error">{{ $message }}</div>@enderror
                </div>
                <div class="wd-cabinet-field wd-c3">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="username">
                    @error('email')<div class="wd-field-error">{{ $message }}</div>@enderror
                </div>
            </div>
            <button type="submit" class="wd-profil-submit">Enregistrer</button>
        </form>
    </div>

    <div class="wd-profil-form">
        <h2 class="wd-profil-subhead">Mot de passe</h2>
        <form method="POST" action="{{ route('tenant.password.update') }}">
            @csrf
            @method('put')
            <div class="wd-cabinet-information-grid">
                <div class="wd-cabinet-field">
                    <label for="current_password">Mot de passe actuel</label>
                    <input type="password" id="current_password" name="current_password" autocomplete="current-password">
                    @error('current_password', 'updatePassword')<div class="wd-field-error">{{ $message }}</div>@enderror
                </div>
                <div class="wd-cabinet-field wd-c3">
                    <label for="password">Nouveau mot de passe</label>
                    <input type="password" id="password" name="password" autocomplete="new-password">
                    @error('password', 'updatePassword')<div class="wd-field-error">{{ $message }}</div>@enderror
                </div>
                <div class="wd-cabinet-field wd-c3">
                    <label for="password_confirmation">Confirmer le mot de passe</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password">
                </div>
            </div>
            <button type="submit" class="wd-profil-submit">Changer le mot de passe</button>
        </form>
    </div>
</div>
<style>
.wd-profil-wrap{max-width:760px}
.wd-profil-form{margin-top:28px;padding:23px;background:#fff;border:1px solid #ded9d4;border-radius:10px}
.wd-profil-subhead{font-size:15px;font-weight:700;margin:0 0 14px;color:#151515}
.wd-profil-submit{margin-top:18px;padding:11px 22px;border:0;border-radius:8px;background:#242424;color:#fff;font-size:12px;font-weight:700;letter-spacing:.04em;cursor:pointer}
.wd-profil-submit:hover{background:#151515}
.wd-cabinet-information-grid{display:grid;grid-template-columns:repeat(6,1fr);column-gap:20px;margin-top:0;border-top:none}
.wd-cabinet-field{grid-column:span 6;padding:16px 0 14px;border-bottom:1px solid #eeeae7}
.wd-cabinet-field.wd-c3{grid-column:span 3}
.wd-cabinet-field label{display:block;color:#9a928d;font-size:8px;font-weight:800;letter-spacing:.12em;text-transform:uppercase;margin-bottom:8px}
.wd-cabinet-field input{width:100%;border:1px solid #ded9d4;border-radius:7px;padding:9px 11px;font-size:13px;color:#242424;background:#fff;font-family:inherit}
.wd-cabinet-field input:focus{outline:none;border-color:#f40087}
.wd-field-error{margin-top:6px;color:#b94d4d;font-size:11px}
.wd-user-success{margin-top:28px;padding:14px 18px;background:#f3f9f4;border:1px solid #d7e8da;border-radius:8px;color:#4d8760;font-size:12px;font-weight:700}
@media(max-width:650px){
.wd-cabinet-field.wd-c3{grid-column:span 6}
}
</style>
</x-tenant-app-layout>
