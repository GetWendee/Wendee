<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Activer votre compte | Wendee</title>

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
            font-family: 'Montserrat', sans-serif;
            background: #f3f1ee;
            color: #242424;
        }

        .wendee-auth {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .wendee-card {
            width: 100%;
            max-width: 480px;
            background: #ffffff;
            border: 1px solid #ded9d4;
            border-radius: 14px;
            padding: 48px;
            box-shadow: 0 18px 50px rgba(36, 36, 36, 0.07);
        }

        .wendee-logo {
            margin-bottom: 42px;
            text-align: center;
            font-size: 30px;
            line-height: 1;
            font-weight: 800;
            letter-spacing: -0.06em;
            color: #242424;
        }

        .wendee-logo span {
            color: #f50087;
        }

        .wendee-eyebrow {
            margin-bottom: 10px;
            color: #f50087;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .2em;
            text-transform: uppercase;
        }

        .wendee-title {
            margin: 0;
            color: #242424;
            font-size: 28px;
            line-height: 1.15;
            font-weight: 700;
            letter-spacing: -.04em;
        }

        .wendee-text {
            margin: 14px 0 30px;
            color: #817a75;
            font-size: 13px;
            line-height: 1.7;
        }

        .wendee-field {
            margin-bottom: 20px;
        }

        .wendee-label {
            display: block;
            margin-bottom: 8px;
            color: #45403d;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .wendee-input {
            width: 100%;
            height: 48px;
            padding: 0 14px;
            border: 1px solid #d8d3cf;
            border-radius: 7px;
            background: #ffffff;
            color: #242424;
            font-family: 'Montserrat', sans-serif;
            font-size: 13px;
            outline: none;
            transition: border-color .15s ease, box-shadow .15s ease;
        }

        .wendee-input:focus {
            border-color: #242424;
            box-shadow: 0 0 0 3px rgba(36, 36, 36, .07);
        }

        .wendee-error {
            margin-top: 7px;
            color: #b94d4d;
            font-size: 10px;
        }

        .wendee-button {
            width: 100%;
            height: 50px;
            margin-top: 8px;
            border: 0;
            border-radius: 7px;
            background: #242424;
            color: #ffffff;
            font-family: 'Montserrat', sans-serif;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .12em;
            text-transform: uppercase;
            cursor: pointer;
            transition: background .15s ease, transform .15s ease;
        }

        .wendee-button:hover {
            background: #111111;
            transform: translateY(-1px);
        }

        .wendee-security {
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid #eeeae7;
            color: #99918c;
            font-size: 9px;
            line-height: 1.6;
            text-align: center;
        }

        .wendee-footer {
            margin-top: 25px;
            color: #aaa29d;
            font-size: 8px;
            letter-spacing: .08em;
            text-align: center;
        }

        @media (max-width: 520px) {
            .wendee-card {
                padding: 34px 25px;
            }

            .wendee-title {
                font-size: 24px;
            }
        }
    </style>
</head>

<body>
    <main class="wendee-auth">
        <section class="wendee-card">

            <div class="wendee-logo">
                wendee<span>.</span>
            </div>

            <div class="wendee-eyebrow">
                Activation du compte
            </div>

            <h1 class="wendee-title">
                Bienvenue chez Wendee
            </h1>

            <p class="wendee-text">
                Votre compte professionnel est prêt.
                Définissez votre mot de passe pour accéder à votre espace Wendee.
            </p>

            <form method="POST" action="{{ route('tenant.password.store') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $request->route('token') }}">
                <input type="hidden" name="email" value="{{ old('email', $request->email) }}">

                <div class="wendee-field">
                    <label class="wendee-label" for="email">
                        Adresse e-mail
                    </label>

                    <input
                        id="email"
                        class="wendee-input"
                        type="email"
                        name="email"
                        value="{{ old('email', $request->email) }}"
                        required
                        autocomplete="username"
                    >

                    @error('email')
                        <div class="wendee-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="wendee-field">
                    <label class="wendee-label" for="password">
                        Nouveau mot de passe
                    </label>

                    <input
                        id="password"
                        class="wendee-input"
                        type="password"
                        name="password"
                        required
                        autocomplete="new-password"
                    >

                    @error('password')
                        <div class="wendee-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="wendee-field">
                    <label class="wendee-label" for="password_confirmation">
                        Confirmation du mot de passe
                    </label>

                    <input
                        id="password_confirmation"
                        class="wendee-input"
                        type="password"
                        name="password_confirmation"
                        required
                        autocomplete="new-password"
                    >
                </div>

                <button type="submit" class="wendee-button">
                    Activer mon compte
                </button>
            </form>

            <div class="wendee-security">
                Ce lien d'activation est personnel et sécurisé.
                Ne le transmettez pas à une autre personne.
            </div>

            <div class="wendee-footer">
                © {{ date('Y') }} Wendee. Tous droits réservés.
            </div>

        </section>
    </main>
</body>
</html>
