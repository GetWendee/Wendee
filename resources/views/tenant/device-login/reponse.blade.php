<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $titre }} | Wendee</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=montserrat:400,500,600,700,800&display=swap" rel="stylesheet">

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

        .wendee-card {
            width: 100%;
            max-width: 480px;
            background: #fff;
            border: 1px solid #ded9d4;
            border-radius: 12px;
            padding: 48px;
            box-shadow: 0 18px 45px rgba(36, 36, 36, .07);
            text-align: center;
        }

        .wendee-eyebrow {
            margin-bottom: 9px;
            color: {{ $succes ? '#80a29a' : '#f40087' }};
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
    </style>
</head>

<body>
    <div class="wendee-card">
        <div class="wendee-eyebrow">Sécurité</div>
        <h1 class="wendee-title">{{ $titre }}</h1>
        <p class="wendee-intro">{{ $message }}</p>
    </div>
</body>

</html>
