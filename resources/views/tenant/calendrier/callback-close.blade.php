<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Calendrier connecté</title>
    <style>
        *{box-sizing:border-box}
        html,body{margin:0;height:100%;background:#f3f1ee;font-family:'Montserrat',Arial,sans-serif;display:flex;align-items:center;justify-content:center;text-align:center}
        p{color:#151515;font-size:14px;padding:0 24px}
    </style>
</head>
<body>
    <p>{{ ucfirst($provider) }} connecté. Cette fenêtre va se fermer...</p>

    <script>
        if (window.opener) {
            try { window.opener.location.reload(); } catch (e) {}
        }
        window.close();
    </script>
</body>
</html>
