<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Connexion en attente | Wendee</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=montserrat:400,500,600,700,800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * { box-sizing: border-box; }

        html, body { margin: 0; min-height: 100%; }

        body {
            min-height: 100vh;
            background: #f3f1ee;
            color: #242424;
            font-family: 'Montserrat', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 20px;
        }

        .wendee-login { width: 100%; max-width: 480px; }

        .wendee-card {
            background: #fff;
            border: 1px solid #ded9d4;
            border-radius: 12px;
            padding: 48px 48px 42px;
            box-shadow: 0 18px 45px rgba(36, 36, 36, .07);
            text-align: center;
        }

        .wendee-eyebrow {
            margin-bottom: 9px;
            color: #f40087;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .18em;
            text-transform: uppercase;
        }

        .wendee-title {
            margin: 0;
            color: #242424;
            font-size: 24px;
            line-height: 1.25;
            font-weight: 700;
            letter-spacing: -.03em;
        }

        .wendee-intro {
            margin: 14px 0 0;
            color: #817b76;
            font-size: 13.5px;
            line-height: 1.7;
        }

        .wendee-spinner {
            width: 34px;
            height: 34px;
            margin: 26px auto 4px;
            border: 3px solid #ded9d4;
            border-top-color: #80a29a;
            border-radius: 50%;
            animation: wendee-spin 0.8s linear infinite;
        }

        @keyframes wendee-spin {
            to { transform: rotate(360deg); }
        }

        .wendee-retour {
            display: inline-block;
            margin-top: 22px;
            color: #80a29a;
            font-size: 12.5px;
            font-weight: 700;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="wendee-login"
        x-data="deviceLoginWaiting(
            '{{ route('tenant.device-login.status') }}',
            '{{ route('tenant.device-login.complete') }}',
            '{{ route('tenant.login') }}'
        )"
    >
        <div class="wendee-card">
            <div x-show="state === 'pending'" x-cloak>
                <div class="wendee-eyebrow">Sécurité</div>
                <h1 class="wendee-title">Nouvel appareil détecté</h1>
                <p class="wendee-intro">
                    Nous avons envoyé un email pour confirmer que c'est bien vous.
                    Vous pouvez aussi confirmer depuis un appareil déjà connecté.
                </p>
                <div class="wendee-spinner"></div>
                <p class="wendee-intro">En attente de confirmation…</p>
            </div>

            <div x-show="state === 'denied'" x-cloak>
                <div class="wendee-eyebrow">Sécurité</div>
                <h1 class="wendee-title">Connexion refusée</h1>
                <p class="wendee-intro">
                    Cette connexion a été bloquée. Un email vous a été envoyé pour redéfinir votre mot de passe.
                </p>
                <a class="wendee-retour" href="{{ route('tenant.login') }}">Retour à la connexion</a>
            </div>

            <div x-show="state === 'expired'" x-cloak>
                <div class="wendee-eyebrow">Sécurité</div>
                <h1 class="wendee-title">Délai dépassé</h1>
                <p class="wendee-intro">
                    La demande de confirmation n'a pas été validée à temps. Merci de vous reconnecter.
                </p>
                <a class="wendee-retour" href="{{ route('tenant.login') }}">Retour à la connexion</a>
            </div>

            <div x-show="state === 'error'" x-cloak>
                <div class="wendee-eyebrow">Sécurité</div>
                <h1 class="wendee-title">Une erreur est survenue</h1>
                <p class="wendee-intro">Merci de réessayer de vous connecter.</p>
                <a class="wendee-retour" href="{{ route('tenant.login') }}">Retour à la connexion</a>
            </div>
        </div>
    </div>
</body>

</html>
