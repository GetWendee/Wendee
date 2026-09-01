<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ tenant('name') ?? config('app.name', 'Laravel') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=montserrat:400,500,600,700&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
        html,body{margin:0!important;background:#f3f1ee!important}
        :root{
        --bg:#f3f1ee;--white:#fff;--ink:#151515;--muted:#817b76;--line:#ded9d4;
        --dark:#1b1716;--pink:#f40087;--red:#b94d4d;--green:#4d8760;
        }
        *{box-sizing:border-box}
        .wd-sidebar{
        position:fixed;inset:0 auto 0 0;width:232px;height:100vh;
        background:#242424;color:#fff;padding:24px 16px;
        display:flex;flex-direction:column;z-index:1000;overflow:hidden
        }
        .wd-logo{padding:0 7px 36px;font-size:23px;font-weight:800;letter-spacing:-.06em}
        .wd-logo b{color:var(--pink)}
        .wd-logo small{display:block;color:#7e7773;font-size:8px;letter-spacing:.25em;text-transform:uppercase;margin-top:5px}
        .wd-nav{display:grid;gap:3px}
        .wd-nav-section{margin:22px 10px 7px;font-size:9px;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:rgba(255,255,255,.30)}
        .wd-nav a{display:flex;align-items:center;gap:11px;padding:10px 11px;margin:2px 0;border-radius:8px;color:rgba(255,255,255,.62);font-size:12px;text-decoration:none}
        .wd-nav a svg{width:18px;height:18px;flex:0 0 18px;fill:none;stroke:currentColor;stroke-width:1.7;stroke-linecap:round;stroke-linejoin:round;color:rgba(255,255,255,.42)}
        .wd-nav a.active{background:rgba(244,0,135,.11);color:#fff}
        .wd-nav a.active svg{color:var(--pink)}
        .wd-nav a:hover{background:rgba(255,255,255,.06);color:#fff}
        .wd-nav a.disabled{color:rgba(255,255,255,.28);cursor:default;pointer-events:none}
        .wd-nav a.disabled svg{color:rgba(255,255,255,.20)}
        .wd-nav a.disabled:hover{background:transparent}
        .wd-soon{margin-left:auto;font-size:9px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:rgba(255,255,255,.30);background:rgba(255,255,255,.08);padding:2px 6px;border-radius:999px;flex:0 0 auto}
        .wd-bottom-nav{margin-top:auto;border-top:1px solid #302a28;padding-top:14px}
        .wd-bottom-nav a,.wd-bottom-nav button{display:block;width:100%;padding:9px 10px;color:#8e8681;font-size:9px;text-transform:uppercase;letter-spacing:.12em;text-align:left;background:none;border:0;cursor:pointer;font-family:inherit}
        .wd-main{margin-left:232px;min-height:100vh;padding-top:68px}
        .wd-topbar{
        position:fixed;top:0;left:232px;right:0;height:68px;z-index:900;
        background:rgba(255,255,255,.94);border-bottom:1px solid var(--line);
        display:flex;align-items:center;justify-content:space-between;padding:0 34px;
        backdrop-filter:blur(10px)
        }
        .wd-crumb{color:#96908b;font-size:11px;font-weight:800;letter-spacing:.17em;text-transform:uppercase}
        .wd-who{display:flex;gap:10px;align-items:center;text-align:right}
        .wd-who strong{font-size:14px}
        .wd-who small{display:block;color:#99918c;font-size:10px;text-transform:uppercase;letter-spacing:.1em}
        .wd-top-avatar{width:34px;height:34px;border-radius:50%;background:var(--pink);display:grid;place-items:center;color:#fff;font-size:10px;font-weight:800}
        @media(max-width:1050px){
        .wd-sidebar{width:72px}
        .wd-logo{font-size:0}.wd-logo b{font-size:22px}.wd-logo small,.wd-nav-section,.wd-bottom-nav{display:none}
        .wd-nav a{justify-content:center}
        .wd-main{margin-left:72px}
        .wd-topbar{left:72px}
        }
        </style>
    </head>
    <body class="font-sans antialiased">
        @include('tenant.layouts.navigation')
        <main class="wd-main">
            @isset($header)
                <header class="bg-white border-b border-gray-200">
                    <div class="px-8 py-5">{{ $header }}</div>
                </header>
            @endisset
            {{ $slot }}
        </main>
    </body>
</html>
