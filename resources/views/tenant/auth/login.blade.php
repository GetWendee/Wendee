<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Connexion | Wendee</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=montserrat:400,500,600,700,800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            min-height: 100%;
        }

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

        .wendee-login {
            width: 100%;
            max-width: 480px;
        }

        .wendee-card {
            background: #fff;
            border: 1px solid #ded9d4;
            border-radius: 12px;
            padding: 48px 48px 42px;
            box-shadow: 0 18px 45px rgba(36, 36, 36, .07);
        }

        .wendee-logo {
            display: block;
            width: 145px;
            height: auto;
            margin: 0 auto 42px;
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
            font-size: 29px;
            line-height: 1.15;
            font-weight: 700;
            letter-spacing: -.04em;
        }

        .wendee-intro {
            margin: 12px 0 30px;
            color: #817b76;
            font-size: 13px;
            line-height: 1.7;
        }

        .wendee-field {
            margin-top: 20px;
        }

        .wendee-label {
            display: block;
            margin-bottom: 8px;
            color: #242424;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .wendee-input {
            display: block;
            width: 100%;
            height: 48px;
            padding: 0 14px;
            border: 1px solid #d7d1cc;
            border-radius: 7px;
            background: #fff;
            color: #242424;
            font-family: 'Montserrat', sans-serif;
            font-size: 13px;
            outline: none;
            transition: border-color .15s, box-shadow .15s;
        }

        .wendee-input:focus {
            border-color: #80a29a;
            box-shadow: 0 0 0 3px rgba(128, 162, 154, .12);
        }

        .wendee-error {
            margin-top: 7px;
            color: #b94d4d;
            font-size: 11px;
        }

        .wendee-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 18px;
        }

        .wendee-remember {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #817b76;
            font-size: 11px;
        }

        .wendee-remember input {
            width: 16px;
            height: 16px;
            accent-color: #242424;
        }

        .wendee-forgot {
            color: #817b76;
            font-size: 11px;
            text-decoration: underline;
            text-underline-offset: 3px;
        }

        .wendee-button {
            width: 100%;
            height: 50px;
            margin-top: 28px;
            border: 0;
            border-radius: 7px;
            background: #242424;
            color: #fff;
            font-family: 'Montserrat', sans-serif;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .1em;
            text-transform: uppercase;
            cursor: pointer;
            transition: opacity .15s;
        }

        .wendee-button:hover {
            opacity: .88;
        }

        .wendee-status {
            margin-bottom: 22px;
            color: #4d8760;
            font-size: 12px;
            line-height: 1.5;
        }

        .wendee-footer {
            margin-top: 25px;
            padding-top: 22px;
            border-top: 1px solid #eeeae7;
            color: #aaa29d;
            text-align: center;
            font-size: 9px;
            line-height: 1.6;
        }

        @media (max-width: 540px) {
            .wendee-card {
                padding: 38px 26px 32px;
            }
        }
    </style>
</head>

<body>

    <main class="wendee-login">

        <section class="wendee-card">

            <img
                src="{{ asset('images/logo-wendee.png') }}"
                alt="Wendee"
                class="wendee-logo"
            >

            @if (session('status'))
                <div class="wendee-status">
                    {{ session('status') }}
                </div>
            @endif

            <div class="wendee-eyebrow">
                Espace professionnel
            </div>

            <h1 class="wendee-title">
                Bienvenue chez Wendee
            </h1>

            <p class="wendee-intro">
                Connectez-vous à votre espace professionnel pour
                piloter votre activité patrimoniale.
            </p>

            <form method="POST" action="{{ route('tenant.login') }}">
                @csrf

                <div class="wendee-field">
                    <label for="email" class="wendee-label">
                        Adresse e-mail
                    </label>

                    <input
                        id="email"
                        class="wendee-input"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="username"
                    >

                    @error('email')
                        <div class="wendee-error">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="wendee-field">
                    <label for="password" class="wendee-label">
                        Mot de passe
                    </label>

                    <input
                        id="password"
                        class="wendee-input"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                    >

                    @error('password')
                        <div class="wendee-error">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="wendee-options">

                    <label class="wendee-remember">
                        <input
                            id="remember"
                            type="checkbox"
                            name="remember"
                        >

                        <span>Se souvenir de moi</span>
                    </label>

                    @if (Route::has('tenant.password.request'))
                        <a
                            href="{{ route('tenant.password.request') }}"
                            class="wendee-forgot"
                        >
                            Mot de passe oublié ?
                        </a>
                    @endif

                </div>

                <button type="submit" class="wendee-button">
                    Se connecter
                </button>

            </form>

            <div class="wendee-footer">
                © {{ date('Y') }} Wendee. Tous droits réservés.
            </div>

        </section>

    </main>

</body>
</html>
