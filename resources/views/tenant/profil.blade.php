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
    @elseif (session('status') === 'facture-supprimee')
    <section class="wd-user-success">Facture supprimée.</section>
    @elseif (session('status'))
    <section class="wd-user-success">{{ session('status') }}</section>
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

    @if($user->effectiveRole() === 'apporteur')
    <div class="wd-profil-form">
        <h2 class="wd-profil-subhead">Mes coordonnées bancaires</h2>
        <p class="wd-profil-hint">Nécessaires pour recevoir le virement de vos commissions. Toute modification doit être revalidée par votre courtier.</p>

        @if($user->rib_iban)
        <span class="wd-rib-status {{ $user->rib_valide ? 'wd-rib-status-ok' : 'wd-rib-status-pending' }}">
            {{ $user->rib_valide ? 'RIB validé par le courtier' : 'RIB en attente de validation' }}
        </span>
        @endif

        <form method="POST" action="{{ route('tenant.profil.rib.update') }}">
            @csrf
            @method('put')
            <div class="wd-cabinet-information-grid">
                <div class="wd-cabinet-field wd-c3">
                    <label for="rib_titulaire">Titulaire du compte</label>
                    <input type="text" id="rib_titulaire" name="rib_titulaire" value="{{ old('rib_titulaire', $user->rib_titulaire) }}">
                    @error('rib_titulaire')<div class="wd-field-error">{{ $message }}</div>@enderror
                </div>
                <div class="wd-cabinet-field wd-c3">
                    <label for="rib_iban">IBAN</label>
                    <input type="text" id="rib_iban" name="rib_iban" value="{{ old('rib_iban', $user->rib_iban) }}">
                    @error('rib_iban')<div class="wd-field-error">{{ $message }}</div>@enderror
                </div>
                <div class="wd-cabinet-field wd-c3">
                    <label for="rib_bic">BIC</label>
                    <input type="text" id="rib_bic" name="rib_bic" value="{{ old('rib_bic', $user->rib_bic) }}">
                    @error('rib_bic')<div class="wd-field-error">{{ $message }}</div>@enderror
                </div>
            </div>
            <button type="submit" class="wd-profil-submit">Enregistrer le RIB</button>
        </form>
    </div>
    @endif

    @if($factures !== null)
    <div class="wd-profil-form">
        <h2 class="wd-profil-subhead">Mes factures</h2>

        @if($factures->isEmpty())
        <p class="wd-profil-empty">Aucune facture envoyée pour le moment.</p>
        @else
        <div class="wd-facture-list">
            @foreach($factures as $facture)
            <div class="wd-facture-row">
                <div class="wd-facture-row-main">
                    <span class="wd-facture-row-name">{{ $facture->nom_original ?? 'Facture' }}</span>
                    <span class="wd-facture-row-meta">
                        {{ $facture->created_at->format('d/m/Y') }}
                        @if($facture->client)
                        &middot; {{ $facture->client->prenom }} {{ $facture->client->nom }}
                        @endif
                    </span>
                </div>
                <div class="wd-facture-row-actions">
                    <a href="{{ route('tenant.factures.show', $facture) }}" target="_blank" class="wd-facture-row-link">Voir</a>
                    <form method="POST" action="{{ route('tenant.factures.destroy', $facture) }}" onsubmit="return confirm('Supprimer cette facture ?');">
                        @csrf
                        @method('delete')
                        <button type="submit" class="wd-facture-row-delete" aria-label="Supprimer">&times;</button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
    @endif

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
.wd-wrap{max-width:1540px;margin:auto;padding:30px 34px 60px}
.wd-head{display:flex;justify-content:space-between;align-items:end}
.wd-eyebrow{font-size:12px;color:var(--pink);font-weight:850;letter-spacing:.2em;text-transform:uppercase}
.wd-head h1{font-size:38px;line-height:1;margin:8px 0 0;letter-spacing:-.05em;font-weight:650}
.wd-head p{color:var(--muted);margin:10px 0 0;font-size:15px}
.wd-profil-wrap{max-width:760px}
.wd-profil-form{margin-top:28px;padding:23px;background:#fff;border:1px solid #ded9d4;border-radius:10px}
.wd-profil-subhead{font-size:15px;font-weight:700;margin:0 0 6px;color:#151515}
.wd-profil-hint{margin:0 0 16px;color:#817b76;font-size:12px;line-height:1.5}
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
.wd-rib-status{display:inline-block;margin-bottom:16px;padding:5px 12px;border-radius:999px;font-size:11px;font-weight:700}
.wd-rib-status-ok{background:#f3f9f4;color:#4d8760}
.wd-rib-status-pending{background:#fdf6e8;color:#a3720f}
.wd-profil-empty{color:#817b76;font-size:13px}
.wd-facture-list{display:grid;gap:8px}
.wd-facture-row{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:12px 14px;border:1px solid #eeeae7;border-radius:8px}
.wd-facture-row-main{display:flex;flex-direction:column;gap:2px;min-width:0}
.wd-facture-row-name{font-size:13px;font-weight:600;color:#242424;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.wd-facture-row-meta{font-size:11px;color:#9a928d}
.wd-facture-row-actions{display:flex;align-items:center;gap:10px;flex:0 0 auto}
.wd-facture-row-link{font-size:12px;font-weight:700;color:#f40087;text-decoration:none}
.wd-facture-row-link:hover{text-decoration:underline}
.wd-facture-row-delete{background:none;border:0;font-size:16px;color:#817b76;cursor:pointer;line-height:1}
.wd-facture-row-delete:hover{color:#b94d4d}
@media(max-width:650px){
.wd-wrap{padding:22px 14px 50px}
.wd-head{flex-direction:column;align-items:flex-start;gap:15px}
.wd-cabinet-field.wd-c3{grid-column:span 6}
}
</style>
</x-tenant-app-layout>
