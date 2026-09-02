<x-tenant-app-layout>
<style>
.wd-wrap{max-width:900px;margin:0 auto;padding:32px 24px;}
.wd-head{margin-bottom:28px;}
.wd-eyebrow{font-size:11px;font-weight:800;letter-spacing:.12em;text-transform:uppercase;color:#817b76;margin-bottom:6px;}
.wd-head h1{font-size:24px;font-weight:800;color:#151515;}
.wd-cal-card{background:#fff;border:1px solid #ded9d4;border-radius:14px;padding:24px;display:flex;align-items:center;justify-content:space-between;gap:20px;margin-bottom:16px;}
.wd-cal-info{display:flex;align-items:center;gap:16px;}
.wd-cal-logo{width:44px;height:44px;border-radius:12px;background:#f3f1ee;display:flex;align-items:center;justify-content:center;font-weight:800;color:#151515;flex-shrink:0;}
.wd-cal-name{font-size:15px;font-weight:700;color:#151515;}
.wd-cal-status{font-size:12.5px;color:#817b76;margin-top:2px;}
.wd-cal-status.connected{color:#4d8760;font-weight:600;}
.wd-btn{display:inline-flex;align-items:center;padding:10px 20px;border-radius:999px;font-size:13px;font-weight:700;cursor:pointer;border:none;text-decoration:none;}
.wd-btn-dark{background:#1b1716;color:#fff;}
.wd-btn-outline{background:#fff;color:#b94d4d;border:1px solid #ded9d4;}
.wd-status-msg{background:#eaf3ec;color:#316b45;border-radius:10px;padding:12px 16px;font-size:13px;margin-bottom:20px;}
</style>

<div class="wd-wrap">
    <div class="wd-head">
        <div class="wd-eyebrow">Agenda</div>
        <h1>Calendrier</h1>
    </div>

    @if (session('status'))
        <div class="wd-status-msg">{{ session('status') }}</div>
    @endif

    <div class="wd-cal-card">
        <div class="wd-cal-info">
            <div class="wd-cal-logo">G</div>
            <div>
                <div class="wd-cal-name">Google Calendar</div>
                <div class="wd-cal-status {{ isset($connexions['google']) ? 'connected' : '' }}">
                    @if(isset($connexions['google']))
                        Connecté{{ $connexions['google']->provider_email ? ' — '.$connexions['google']->provider_email : '' }}
                    @else
                        Non connecté
                    @endif
                </div>
            </div>
        </div>
        @if(isset($connexions['google']))
            <form action="{{ route('tenant.calendrier.destroy', $connexions['google']) }}" method="POST" onsubmit="return confirm('Déconnecter Google Calendar ?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="wd-btn wd-btn-outline">Déconnecter</button>
            </form>
        @else
            <a href="{{ route('tenant.calendrier.connecter', 'google') }}" class="wd-btn wd-btn-dark">Connecter</a>
        @endif
    </div>

    <div class="wd-cal-card">
        <div class="wd-cal-info">
            <div class="wd-cal-logo">O</div>
            <div>
                <div class="wd-cal-name">Outlook / Microsoft 365</div>
                <div class="wd-cal-status {{ isset($connexions['microsoft']) ? 'connected' : '' }}">
                    @if(isset($connexions['microsoft']))
                        Connecté{{ $connexions['microsoft']->provider_email ? ' — '.$connexions['microsoft']->provider_email : '' }}
                    @else
                        Non connecté
                    @endif
                </div>
            </div>
        </div>
        @if(isset($connexions['microsoft']))
            <form action="{{ route('tenant.calendrier.destroy', $connexions['microsoft']) }}" method="POST" onsubmit="return confirm('Déconnecter Outlook ?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="wd-btn wd-btn-outline">Déconnecter</button>
            </form>
        @else
            <a href="{{ route('tenant.calendrier.connecter', 'microsoft') }}" class="wd-btn wd-btn-dark">Connecter</a>
        @endif
    </div>

    <p style="font-size:12.5px;color:#817b76;margin-top:20px;">
        Une fois un calendrier connecté, vos créneaux libres y sont automatiquement pris en compte lors de la prise de rendez-vous depuis une fiche client.
    </p>
</div>
</x-tenant-app-layout>
