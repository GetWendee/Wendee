@php
    $libellesAnalyses = [
        'kyc_morale' => 'KYC société',
        'patrimoine_morale' => 'Patrimoine société',
        'profil_investisseur_morale' => 'Profil investisseur société',
    ];
    $liensFormulaires = [
        'kyc_morale' => route('tenant.clients.kyc-morale.edit', $client),
        'patrimoine_morale' => route('tenant.clients.patrimoine.edit', $client),
        'profil_investisseur_morale' => route('tenant.clients.profil-investisseur-morale.edit', $client),
    ];
@endphp
<x-tenant-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Pilotage société - {{ $client->nomAffichage() }}
            </h2>
            <a href="{{ route('tenant.clients.index') }}" class="text-sm text-gray-600 underline">
                {{ __('Retour au portefeuille clients') }}
            </a>
        </div>
    </x-slot>

<style>
.wd-wrap{max-width:1000px;margin:auto;padding:30px 34px 60px}
.wd-pilot-flash{margin-bottom:18px;padding:10px 14px;border-radius:8px;font-size:12px}
.wd-pilot-flash-success{background:#eef7ef;color:#2f6b45;border:1px solid #cfe8d2}
.wd-pilot-flash-error{background:#fbecec;color:#b94d4d;border:1px solid #f0c9c9}
.wd-section-title{margin:26px 0 10px;font-size:11px;font-weight:800;letter-spacing:.1em;text-transform:uppercase;color:#9a928d}
.wd-section-title:first-of-type{margin-top:0}
.wd-pilot-grille{display:grid;grid-template-columns:repeat(3,1fr);gap:16px}
@media (max-width:800px){.wd-pilot-grille{grid-template-columns:1fr}}
.wd-pilot-carte{background:#fff;border:1px solid #ded9d4;border-radius:12px;padding:18px}
.wd-pilot-carte-titre{font-weight:800;font-size:13px;color:#242424;margin-bottom:6px}
.wd-pilot-carte-statut{font-size:11px;margin-bottom:10px}
.wd-pilot-statut-ok{color:#2f6b45}
.wd-pilot-statut-ko{color:#b94d4d}
.wd-pilot-carte-lien{font-size:11px;font-weight:700;color:#f40087;text-decoration:none}
.wd-pilot-points{margin-top:10px}
.wd-pilot-point{font-size:11px;color:#242424;padding:6px 0;border-top:1px solid #eeeae7}
.wd-pilot-point strong{display:block;font-size:11px;margin-bottom:2px}
.wd-pilot-cta{background:#242424;color:#fff;border-radius:14px;padding:22px 26px;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;border-top:3px solid #f40087}
.wd-pilot-cta h3{margin:0;font-size:15px}
.wd-pilot-cta p{margin:4px 0 0;color:#c9c2be;font-size:12px}
.wd-pilot-btn{border:1px solid rgba(255,255,255,.10);border-top:2px solid #f40087;border-radius:8px;background:#242424;color:#fff;font-size:9px;font-weight:800;letter-spacing:.10em;text-transform:uppercase;cursor:pointer;padding:0 18px;height:38px;display:inline-flex;align-items:center;text-decoration:none}
.wd-pilot-btn[disabled]{opacity:.4;cursor:not-allowed}
.wd-pilot-prestations{display:grid;grid-template-columns:repeat(2,1fr);gap:14px;margin-top:16px}
@media (max-width:800px){.wd-pilot-prestations{grid-template-columns:1fr}}
.wd-pilot-prestation{background:#fff;border:1px solid #ded9d4;border-radius:12px;padding:16px}
.wd-pilot-prestation-titre{font-weight:800;font-size:13px;color:#242424;margin-bottom:6px}
.wd-pilot-prestation-justif{font-size:12px;color:#817b76;margin-bottom:8px}
.wd-pilot-prestation-action{font-size:11px;color:#242424;padding:4px 0}
.wd-pilot-links{display:flex;gap:12px;margin-top:20px;flex-wrap:wrap}
</style>

<div class="wd-wrap">
    @if(session('status'))
        <div class="wd-pilot-flash wd-pilot-flash-success">{{ session('status') }}</div>
    @endif
    @if(session('error'))
        <div class="wd-pilot-flash wd-pilot-flash-error">{{ session('error') }}</div>
    @endif

    <div class="wd-section-title">Analyses du dossier</div>
    <div class="wd-pilot-grille">
        @foreach($libellesAnalyses as $type => $label)
            @php $analyse = $analysesDossier->get($type); @endphp
            <div class="wd-pilot-carte">
                <div class="wd-pilot-carte-titre">{{ $label }}</div>
                @if($analyse)
                    <div class="wd-pilot-carte-statut wd-pilot-statut-ok">Analysé le {{ $analyse->completed_at?->translatedFormat('d F Y') }}</div>
                    <div class="wd-pilot-points">
                        @foreach(($analyse->result_json['points_forts'] ?? []) as $point)
                            <div class="wd-pilot-point"><strong>✓ {{ $point['titre'] }}</strong>{{ $point['analyse'] }}</div>
                        @endforeach
                        @foreach(($analyse->result_json['points_attention'] ?? []) as $point)
                            <div class="wd-pilot-point"><strong>⚠ {{ $point['titre'] }}</strong>{{ $point['analyse'] }}</div>
                        @endforeach
                    </div>
                @else
                    <div class="wd-pilot-carte-statut wd-pilot-statut-ko">Non analysé</div>
                @endif
                <a href="{{ $liensFormulaires[$type] }}" class="wd-pilot-carte-lien">Ouvrir le formulaire →</a>
            </div>
        @endforeach
    </div>

    <div class="wd-section-title">Suggestion de prestations</div>
    <div class="wd-pilot-cta">
        <div>
            <h3>Générer la suggestion de prestations</h3>
            <p>Nécessite les 3 analyses du dossier, réalisées depuis moins d'un an</p>
        </div>
        <form method="POST" action="{{ route('tenant.clients.pilotage-morale.suggestion', $client) }}">
            @csrf
            <button type="submit" class="wd-pilot-btn" {{ $analysesDossier->count() === 3 ? '' : 'disabled' }}>Générer</button>
        </form>
    </div>

    @if($suggestion && $suggestion->status === 'completed')
        <div class="wd-pilot-prestations">
            @foreach(($suggestion->result_json['prestations'] ?? []) as $prestation)
                <div class="wd-pilot-prestation">
                    <div class="wd-pilot-prestation-titre">{{ $prestation['titre'] }}</div>
                    <div class="wd-pilot-prestation-justif">{{ $prestation['justification'] }}</div>
                    @foreach(($prestation['actions'] ?? []) as $action)
                        <div class="wd-pilot-prestation-action">- {{ $action }}</div>
                    @endforeach
                </div>
            @endforeach
        </div>
    @endif

    <div class="wd-pilot-links">
        <a href="{{ route('tenant.clients.recommandation-morale', $client) }}" class="wd-pilot-btn">Recommandation patrimoniale</a>
        <a href="{{ route('tenant.clients.plan-action-morale', $client) }}" class="wd-pilot-btn">Plan d'action</a>
    </div>
</div>
</x-tenant-app-layout>
