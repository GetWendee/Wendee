@auth
<div
    x-data="deviceLoginPopup(
        '{{ route('tenant.device-login.pending-for-me') }}',
        '{{ url('device-login') }}/__TOKEN__/confirmer',
        '{{ url('device-login') }}/__TOKEN__/refuser'
    )"
    x-cloak
>
    <div
        x-show="challenge"
        style="position:fixed; inset:0; background:rgba(36,36,36,.45); z-index:9999; display:flex; align-items:center; justify-content:center; padding:20px;"
    >
        <div style="background:#fff; border-radius:12px; padding:32px; max-width:420px; width:100%; font-family:'Montserrat',sans-serif; box-shadow:0 18px 45px rgba(36,36,36,.2);">
            <div style="color:#f40087; font-size:11px; font-weight:800; letter-spacing:.16em; text-transform:uppercase; margin-bottom:8px;">
                Sécurité
            </div>
            <h2 style="margin:0 0 12px; font-size:19px; font-weight:700; color:#242424;">
                Nouvelle connexion détectée
            </h2>
            <p style="margin:0 0 20px; font-size:13.5px; line-height:1.7; color:#5b5651;" x-show="challenge">
                Quelqu'un essaie de se connecter à votre compte depuis
                <strong x-text="challenge && challenge.appareil"></strong>
                (IP <span x-text="challenge && challenge.ip"></span>)
                le <span x-text="challenge && challenge.heure"></span>.
                Est-ce vous ?
            </p>
            <div style="display:flex; gap:12px;">
                <button
                    type="button"
                    @click="repondre(true)"
                    style="flex:1; background:#80a29a; color:#fff; border:none; border-radius:8px; padding:12px; font-family:'Montserrat',sans-serif; font-weight:700; font-size:13px; cursor:pointer;"
                >
                    Oui, c'est moi
                </button>
                <button
                    type="button"
                    @click="repondre(false)"
                    style="flex:1; background:#c0392b; color:#fff; border:none; border-radius:8px; padding:12px; font-family:'Montserrat',sans-serif; font-weight:700; font-size:13px; cursor:pointer;"
                >
                    Non, bloquer
                </button>
            </div>
        </div>
    </div>

    <div
        x-show="reponse"
        style="position:fixed; bottom:20px; right:20px; background:#242424; color:#fff; padding:14px 20px; border-radius:8px; font-family:'Montserrat',sans-serif; font-size:13px; z-index:9999;"
        x-text="reponse === 'confirme' ? 'Connexion autorisée.' : 'Connexion bloquée. Un email de réinitialisation a été envoyé.'"
    ></div>
</div>
@endauth
