<x-tenant-app-layout>
    <div class="wd-perf p-8">

        <style>
            .wd-perf{max-width:1320px;margin:0 auto}
            .wd-perf-head{display:flex;flex-wrap:wrap;align-items:flex-end;justify-content:space-between;gap:20px;margin-bottom:28px}
            .wd-perf-eyebrow{color:var(--pink);font-size:11px;font-weight:700;letter-spacing:.16em;text-transform:uppercase}
            .wd-perf-title{font-size:34px;font-weight:700;color:var(--dark);margin:6px 0 0;letter-spacing:-.02em}

            .wd-perf-switch{display:inline-flex;background:var(--white);border:1px solid var(--line);border-radius:14px;padding:4px;gap:2px}
            .wd-perf-switch a{padding:9px 18px;border-radius:10px;font-size:13px;font-weight:600;color:var(--muted);text-decoration:none;transition:.15s}
            .wd-perf-switch a.active{background:var(--dark);color:#fff}
            .wd-perf-switch a:not(.active):hover{color:var(--ink)}

            .wd-perf-hero{background:var(--dark);border-radius:28px;padding:38px 42px;color:#fff;display:grid;grid-template-columns:auto 1fr;gap:56px;align-items:center;margin-bottom:18px}
            .wd-perf-hero-label{font-size:11px;text-transform:uppercase;letter-spacing:.14em;color:rgba(255,255,255,.5);font-weight:700}
            .wd-perf-hero-value{font-size:46px;font-weight:700;margin:10px 0 0;letter-spacing:-.02em;white-space:nowrap}
            .wd-perf-hero-sub{display:flex;gap:40px;border-left:1px solid rgba(255,255,255,.14);padding-left:40px}
            .wd-perf-hero-sub .lbl{font-size:11px;color:rgba(255,255,255,.5);text-transform:uppercase;letter-spacing:.1em;font-weight:600}
            .wd-perf-hero-sub .val{font-size:19px;font-weight:700;margin-top:8px}
            .wd-perf-hero-sub .val.pink{color:#ff5cb8}

            .wd-perf-tiles{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:8px}
            .wd-perf-tile{background:var(--white);border:1px solid var(--line);border-radius:20px;padding:22px 26px}
            .wd-perf-tile .lbl{font-size:11px;text-transform:uppercase;letter-spacing:.1em;color:var(--muted);font-weight:700}
            .wd-perf-tile .val{font-size:28px;font-weight:700;color:var(--dark);margin-top:10px}
            .wd-perf-tile .trend{font-size:12px;font-weight:700;margin-left:8px}
            .wd-perf-tile .trend.up{color:var(--green)}
            .wd-perf-tile .trend.down{color:var(--red)}

            .wd-perf-note{font-size:12px;color:var(--muted);padding:14px 4px 26px;text-align:center}

            .wd-perf-grid{display:grid;grid-template-columns:1.4fr 1fr;gap:16px;margin-bottom:16px}
            .wd-perf-card{background:var(--white);border:1px solid var(--line);border-radius:24px;padding:28px}
            .wd-perf-card h3{margin:0 0 6px;font-size:12px;text-transform:uppercase;letter-spacing:.1em;color:var(--muted);font-weight:700}
            .wd-perf-card .sub{font-size:12px;color:var(--muted);margin-bottom:18px}

            .wd-perf-rank-row{display:flex;align-items:center;gap:14px;padding:15px 0;border-top:1px solid var(--line)}
            .wd-perf-rank-row:first-child{border-top:0;padding-top:6px}
            .wd-perf-avatar{width:36px;height:36px;border-radius:50%;background:rgba(244,0,135,.1);color:var(--pink);display:grid;place-items:center;font-size:12px;font-weight:700;flex:0 0 auto}
            .wd-perf-rank-main{flex:1;min-width:0}
            .wd-perf-rank-main .name{font-weight:600;color:var(--dark);font-size:14px}
            .wd-perf-rank-bar{height:5px;border-radius:3px;background:var(--bg);margin-top:9px;overflow:hidden}
            .wd-perf-rank-bar span{display:block;height:100%;background:var(--pink);border-radius:3px}
            .wd-perf-rank-side{text-align:right;flex:0 0 auto}
            .wd-perf-rank-side .amount{font-weight:700;color:var(--dark);font-size:14px}
            .wd-perf-rank-side .clients{font-size:11px;color:var(--muted);margin-top:3px}

            .wd-perf-donut-wrap{display:flex;flex-direction:column;align-items:center}
            .wd-perf-donut{position:relative;width:172px;height:172px}
            .wd-perf-donut-hole{position:absolute;inset:16%;background:var(--white);border-radius:50%;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center}
            .wd-perf-donut-hole .amount{font-size:15px;font-weight:700;color:var(--dark)}
            .wd-perf-donut-hole .lbl{font-size:9px;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);margin-top:2px}
            .wd-perf-legend{width:100%;margin-top:26px;display:flex;flex-direction:column;gap:11px}
            .wd-perf-legend-row{display:flex;align-items:center;justify-content:space-between;font-size:13px}
            .wd-perf-legend-label{display:flex;align-items:center;color:var(--ink)}
            .wd-perf-legend-dot{width:8px;height:8px;border-radius:50%;margin-right:9px;flex:0 0 auto}
            .wd-perf-legend-pct{font-weight:700;color:var(--dark)}

            .wd-perf-chart-labels{display:flex;justify-content:space-between;margin-top:6px;font-size:10px;color:var(--muted)}
            .wd-perf-chart-labels span{flex:1;text-align:center}

            .wd-perf-alert-row{display:flex;align-items:center;gap:18px;padding:16px 0;border-top:1px solid var(--line)}
            .wd-perf-alert-row:first-child{border-top:0;padding-top:6px}
            .wd-perf-alert-name{flex:0 0 190px;font-weight:600;color:var(--dark);font-size:14px;display:flex;align-items:center;gap:10px}
            .wd-perf-alert-bar{flex:1;height:6px;background:var(--bg);border-radius:3px;overflow:hidden}
            .wd-perf-alert-bar span{display:block;height:100%;background:var(--red);border-radius:3px}
            .wd-perf-badges{flex:0 0 auto;display:flex;gap:8px}
            .wd-perf-badge{font-size:11px;font-weight:700;padding:5px 12px;border-radius:999px;background:var(--bg);color:var(--muted);white-space:nowrap}
            .wd-perf-badge.warn{background:rgba(185,77,77,.1);color:var(--red)}

            @media (max-width:960px){
                .wd-perf-hero{grid-template-columns:1fr}
                .wd-perf-hero-sub{border-left:0;padding-left:0;border-top:1px solid rgba(255,255,255,.14);padding-top:22px;flex-wrap:wrap}
                .wd-perf-tiles{grid-template-columns:1fr}
                .wd-perf-grid{grid-template-columns:1fr}
                .wd-perf-alert-name{flex-basis:140px}
            }
        </style>

        <div class="wd-perf-head">
            <div>
                <p class="wd-perf-eyebrow">Vue cabinet</p>
                <h1 class="wd-perf-title">Performances</h1>
            </div>

            <div class="wd-perf-switch">
                @foreach (['mois' => 'Mois', 'trimestre' => 'Trimestre', 'annee' => 'Année'] as $valeur => $label)
                    <a href="{{ route('tenant.performances.index', ['periode' => $valeur]) }}" class="{{ $periode === $valeur ? 'active' : '' }}">{{ $label }}</a>
                @endforeach
            </div>
        </div>

        <div class="wd-perf-hero">
            <div>
                <p class="wd-perf-hero-label">Solde patrimonial du cabinet</p>
                <p class="wd-perf-hero-value">{{ number_format($solde, 0, ',', ' ') }} €</p>
            </div>
            <div class="wd-perf-hero-sub">
                <div>
                    <p class="lbl">Actifs</p>
                    <p class="val">{{ number_format($actifs, 0, ',', ' ') }} €</p>
                </div>
                <div>
                    <p class="lbl">Passifs</p>
                    <p class="val">{{ number_format($passifs, 0, ',', ' ') }} €</p>
                </div>
                <div>
                    <p class="lbl">Clients actifs</p>
                    <p class="val pink">{{ $clientsActifs }}</p>
                </div>
            </div>
        </div>

        <div class="wd-perf-tiles">
            <div class="wd-perf-tile">
                <p class="lbl">Clients créés</p>
                <p class="val">
                    {{ $clientsCreesPeriode }}
                    @if (! is_null($evolutionClientsCrees))
                        <span class="trend {{ $evolutionClientsCrees >= 0 ? 'up' : 'down' }}">{{ $evolutionClientsCrees >= 0 ? '▲' : '▼' }} {{ number_format(abs($evolutionClientsCrees), 1, ',', ' ') }}%</span>
                    @endif
                </p>
            </div>
            <div class="wd-perf-tile">
                <p class="lbl">Audits réalisés</p>
                <p class="val">
                    {{ $auditsPeriode }}
                    @if (! is_null($evolutionAudits))
                        <span class="trend {{ $evolutionAudits >= 0 ? 'up' : 'down' }}">{{ $evolutionAudits >= 0 ? '▲' : '▼' }} {{ number_format(abs($evolutionAudits), 1, ',', ' ') }}%</span>
                    @endif
                </p>
            </div>
            <div class="wd-perf-tile">
                <p class="lbl">Patrimoine géré / client</p>
                <p class="val">{{ $clientsActifs > 0 ? number_format($actifs / $clientsActifs, 0, ',', ' ') : 0 }} €</p>
            </div>
        </div>

        <p class="wd-perf-note">Production, taux de conversion et contrats signés s'activeront avec le module Offres.</p>

        <div class="wd-perf-grid">

            <div class="wd-perf-card">
                <h3>Par conseiller</h3>
                <p class="sub">Classé par patrimoine géré</p>

                @php
                    $maxPatrimoineGere = $lignesConseillers->max('patrimoine_gere') ?: 1;
                @endphp

                @foreach ($lignesConseillers as $ligne)
                    @php
                        $initiales = collect(explode(' ', $ligne['conseiller']->name))->map(fn ($mot) => mb_substr($mot, 0, 1))->implode('');
                        $largeur = $maxPatrimoineGere > 0 ? max(3, ($ligne['patrimoine_gere'] / $maxPatrimoineGere) * 100) : 3;
                    @endphp
                    <div class="wd-perf-rank-row">
                        <span class="wd-perf-avatar">{{ $initiales }}</span>
                        <div class="wd-perf-rank-main">
                            <p class="name">{{ $ligne['conseiller']->name }}</p>
                            <div class="wd-perf-rank-bar"><span style="width: {{ $largeur }}%;"></span></div>
                        </div>
                        <div class="wd-perf-rank-side">
                            <p class="amount">{{ number_format($ligne['patrimoine_gere'], 0, ',', ' ') }} €</p>
                            <p class="clients">{{ $ligne['clients'] }} client{{ $ligne['clients'] > 1 ? 's' : '' }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="wd-perf-card">
                <h3>Répartition allocation</h3>
                <p class="sub">Actifs du cabinet</p>

                @if (empty($repartition))
                    <p style="font-size:13px;color:var(--muted)">Aucun actif renseigné.</p>
                @else
                    @php
                        $palette = ['#151515', '#f40087', '#5c5650', '#8f8880', '#b8b1a9', '#d8d2ca', '#ff8fc9', '#e2ddd6'];
                        $cursor = 0;
                        $segments = [];
                        foreach ($repartition as $index => $bucket) {
                            $start = $cursor;
                            $cursor += $bucket['pct'];
                            $segments[] = $palette[$index % count($palette)] . ' ' . number_format($start, 4, '.', '') . '% ' . number_format($cursor, 4, '.', '') . '%';
                        }
                        $gradient = implode(', ', $segments);
                    @endphp

                    <div class="wd-perf-donut-wrap">
                        <div class="wd-perf-donut" style="border-radius:50%;background:conic-gradient({{ $gradient }});">
                            <div class="wd-perf-donut-hole">
                                <span class="amount">{{ number_format($actifs / 1000, 0, ',', ' ') }} k€</span>
                                <span class="lbl">Total actifs</span>
                            </div>
                        </div>

                        <div class="wd-perf-legend">
                            @foreach ($repartition as $index => $bucket)
                                <div class="wd-perf-legend-row">
                                    <span class="wd-perf-legend-label">
                                        <span class="wd-perf-legend-dot" style="background: {{ $palette[$index % count($palette)] }};"></span>
                                        {{ $bucket['label'] }}
                                    </span>
                                    <span class="wd-perf-legend-pct">{{ number_format($bucket['pct'], 1, ',', ' ') }}%</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

        </div>

        <div class="wd-perf-card" style="margin-bottom:16px;">
            <h3>Évolution du patrimoine cabinet</h3>
            <p class="sub">Cumul du patrimoine actif, sur 13 mois</p>

            @php
                $max = collect($evolutionPatrimoine)->max('total') ?: 1;
                $count = count($evolutionPatrimoine);
                $w = 1000; $h = 220; $padX = 6; $padY = 14;
                $stepX = $count > 1 ? ($w - 2 * $padX) / ($count - 1) : 0;
                $points = [];
                foreach (array_values($evolutionPatrimoine) as $i => $point) {
                    $x = $padX + $i * $stepX;
                    $y = $h - $padY - ($max > 0 ? ($point['total'] / $max) : 0) * ($h - 2 * $padY);
                    $points[] = [$x, $y];
                }
                $linePath = '';
                foreach ($points as $i => $pt) {
                    $linePath .= ($i === 0 ? 'M ' : ' L ') . number_format($pt[0], 2, '.', '') . ' ' . number_format($pt[1], 2, '.', '');
                }
                $areaPath = $linePath;
                if (! empty($points)) {
                    $areaPath .= ' L ' . number_format($points[count($points) - 1][0], 2, '.', '') . ' ' . ($h - $padY);
                    $areaPath .= ' L ' . number_format($points[0][0], 2, '.', '') . ' ' . ($h - $padY) . ' Z';
                }
            @endphp

            <svg viewBox="0 0 {{ $w }} {{ $h }}" style="width:100%;height:220px;display:block;" preserveAspectRatio="none">
                <defs>
                    <linearGradient id="wdPerfArea" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#f40087" stop-opacity="0.22" />
                        <stop offset="100%" stop-color="#f40087" stop-opacity="0" />
                    </linearGradient>
                </defs>
                <path d="{{ $areaPath }}" fill="url(#wdPerfArea)" />
                <path d="{{ $linePath }}" fill="none" stroke="#f40087" stroke-width="2.5" stroke-linejoin="round" stroke-linecap="round" />
            </svg>

            <div class="wd-perf-chart-labels">
                @foreach ($evolutionPatrimoine as $point)
                    <span>{{ $point['label'] }}</span>
                @endforeach
            </div>
        </div>

        <div class="wd-perf-card">
            <h3>Alertes conformité agrégées</h3>
            <p class="sub">Classées par niveau de friction</p>

            @php
                $maxFriction = $alertesConformite->max(fn ($ligne) => $ligne['kyc_expires'] + $ligne['profils_a_renouveler']) ?: 1;
            @endphp

            @foreach ($alertesConformite as $ligne)
                @php
                    $friction = $ligne['kyc_expires'] + $ligne['profils_a_renouveler'];
                    $largeur = max(0, min(100, ($friction / $maxFriction) * 100));
                    $initiales = collect(explode(' ', $ligne['conseiller']->name))->map(fn ($mot) => mb_substr($mot, 0, 1))->implode('');
                @endphp
                <div class="wd-perf-alert-row">
                    <span class="wd-perf-alert-name">
                        <span class="wd-perf-avatar">{{ $initiales }}</span>
                        {{ $ligne['conseiller']->name }}
                    </span>
                    <div class="wd-perf-alert-bar"><span style="width: {{ $largeur }}%;"></span></div>
                    <div class="wd-perf-badges">
                        <span class="wd-perf-badge {{ $ligne['kyc_expires'] > 0 ? 'warn' : '' }}">{{ $ligne['kyc_expires'] }} KYC expirés</span>
                        <span class="wd-perf-badge {{ $ligne['profils_a_renouveler'] > 0 ? 'warn' : '' }}">{{ $ligne['profils_a_renouveler'] }} profils à renouveler</span>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</x-tenant-app-layout>
