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

            .wd-perf-tiles{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:16px}
            .wd-perf-tile{background:var(--white);border:1px solid var(--line);border-radius:20px;padding:22px 26px}
            .wd-perf-tile .lbl{font-size:11px;text-transform:uppercase;letter-spacing:.1em;color:var(--muted);font-weight:700}
            .wd-perf-tile .val{font-size:30px;font-weight:700;color:var(--dark);margin-top:10px}
            .wd-perf-tile .val.muted{color:var(--muted);font-size:20px}

            .wd-perf-note{font-size:12px;color:var(--muted);padding:0 4px 20px;text-align:center}

            .wd-perf-grid{display:grid;grid-template-columns:1.4fr 1fr;gap:16px;margin-bottom:16px}
            .wd-perf-card{background:var(--white);border:1px solid var(--line);border-radius:24px;padding:28px}
            .wd-perf-card h3{margin:0 0 6px;font-size:12px;text-transform:uppercase;letter-spacing:.1em;color:var(--muted);font-weight:700}
            .wd-perf-card .sub{font-size:12px;color:var(--muted);margin-bottom:18px}

            .wd-perf-chart-labels{display:flex;justify-content:space-between;margin-top:6px;font-size:10px;color:var(--muted)}
            .wd-perf-chart-labels span{flex:1;text-align:center}

            .wd-perf-bar-row{padding:14px 0;border-top:1px solid var(--line)}
            .wd-perf-bar-row:first-child{border-top:0;padding-top:4px}
            .wd-perf-bar-row .top{display:flex;justify-content:space-between;font-size:13px;margin-bottom:9px}
            .wd-perf-bar-row .top .label{color:var(--ink);font-weight:600}
            .wd-perf-bar-row .top .amount{color:var(--dark);font-weight:700}
            .wd-perf-bar-track{height:6px;border-radius:3px;background:var(--bg);overflow:hidden}
            .wd-perf-bar-track span{display:block;height:100%;background:var(--pink);border-radius:3px}

            .wd-perf-avatar{width:34px;height:34px;border-radius:50%;background:rgba(244,0,135,.1);color:var(--pink);display:grid;place-items:center;font-size:11px;font-weight:700;flex:0 0 auto}

            .wd-rev-table{width:100%;border-collapse:collapse;font-size:13px}
            .wd-rev-table thead th{text-align:left;font-size:10px;text-transform:uppercase;letter-spacing:.1em;color:var(--muted);font-weight:700;padding:0 12px 12px 0}
            .wd-rev-table tbody td{padding:13px 12px 13px 0;border-top:1px solid var(--line)}
            .wd-rev-table tbody tr:first-child td{border-top:0}
            .wd-rev-table .name-cell{display:flex;align-items:center;gap:10px;font-weight:600;color:var(--dark)}
            .wd-rev-table .amount{font-weight:700;color:var(--dark)}
            .wd-rev-table .muted{color:var(--muted)}

            .wd-rev-search{width:100%;padding:11px 16px;border:1px solid var(--line);border-radius:12px;font-size:13px;margin-bottom:18px;background:var(--bg);color:var(--ink)}
            .wd-rev-search:focus{outline:none;border-color:var(--pink);background:var(--white)}

            @media (max-width:960px){
                .wd-perf-tiles{grid-template-columns:1fr}
                .wd-perf-grid{grid-template-columns:1fr}
            }
        </style>

        <div class="wd-perf-head">
            <div>
                <p class="wd-perf-eyebrow">Vue cabinet</p>
                <h1 class="wd-perf-title">Revenus</h1>
            </div>

            <div class="wd-perf-switch">
                @foreach (['mois' => 'Mois', 'trimestre' => 'Trimestre', 'annee' => 'Année'] as $valeur => $label)
                    <a href="{{ route('tenant.revenus.index', ['periode' => $valeur]) }}" class="{{ $periode === $valeur ? 'active' : '' }}">{{ $label }}</a>
                @endforeach
            </div>
        </div>

        <div class="wd-perf-tiles">
            <div class="wd-perf-tile">
                <p class="lbl">Revenu total période</p>
                <p class="val">{{ number_format($revenuTotal, 0, ',', ' ') }} €</p>
            </div>
            <div class="wd-perf-tile">
                <p class="lbl">Revenu moyen par dossier</p>
                <p class="val">{{ number_format($revenuMoyenDossier, 0, ',', ' ') }} €</p>
            </div>
            <div class="wd-perf-tile">
                <p class="lbl">Objectif atteint</p>
                @if (! is_null($objectifPct))
                    <p class="val">{{ number_format($objectifPct, 1, ',', ' ') }}%</p>
                @else
                    <p class="val muted">Objectif non configuré</p>
                @endif
            </div>
        </div>

        <p class="wd-perf-note">Données de démonstration : le module Offres / tarifs de mission n'est pas encore construit dans Wendee.</p>

        <div class="wd-perf-card" style="margin-bottom:16px;">
            <h3>Évolution des revenus</h3>
            <p class="sub">Agrégation hebdomadaire</p>

            @php
                $max = collect($evolution)->max('total') ?: 1;
                $count = count($evolution);
                $w = 1000; $h = 220; $padX = 6; $padY = 14;
                $stepX = $count > 1 ? ($w - 2 * $padX) / ($count - 1) : 0;
                $points = [];
                foreach (array_values($evolution) as $i => $point) {
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
                    <linearGradient id="wdRevArea" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#f40087" stop-opacity="0.22" />
                        <stop offset="100%" stop-color="#f40087" stop-opacity="0" />
                    </linearGradient>
                </defs>
                <path d="{{ $areaPath }}" fill="url(#wdRevArea)" />
                <path d="{{ $linePath }}" fill="none" stroke="#f40087" stroke-width="2.5" stroke-linejoin="round" stroke-linecap="round" />
            </svg>

            <div class="wd-perf-chart-labels">
                @foreach ($evolution as $i => $point)
                    @if ($i % 3 === 0 || $i === count($evolution) - 1)
                        <span>{{ $point['label'] }}</span>
                    @else
                        <span></span>
                    @endif
                @endforeach
            </div>
        </div>

        <div class="wd-perf-grid">

            <div class="wd-perf-card">
                <h3>Classement conseillers</h3>
                <p class="sub">Par revenu généré</p>

                @if ($classementConseillers->isEmpty())
                    <p style="font-size:13px;color:var(--muted)">Aucun dossier facturé sur la période.</p>
                @else
                    <table class="wd-rev-table">
                        <thead>
                            <tr>
                                <th>Conseiller</th>
                                <th>Revenu généré</th>
                                <th>Dossiers</th>
                                <th>Revenu moyen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($classementConseillers as $ligne)
                                @php
                                    $initiales = collect(explode(' ', $ligne['conseiller']->name))->map(fn ($mot) => mb_substr($mot, 0, 1))->implode('');
                                @endphp
                                <tr>
                                    <td>
                                        <span class="name-cell">
                                            <span class="wd-perf-avatar">{{ $initiales }}</span>
                                            {{ $ligne['conseiller']->name }}
                                        </span>
                                    </td>
                                    <td class="amount">{{ number_format($ligne['revenu'], 0, ',', ' ') }} €</td>
                                    <td>{{ $ligne['dossiers'] }}</td>
                                    <td class="amount">{{ number_format($ligne['revenu_moyen'], 0, ',', ' ') }} €</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            <div class="wd-perf-card">
                <h3>Répartition des revenus</h3>
                <p class="sub">Par type de mission</p>

                @foreach ($repartitionTypes as $label => $montant)
                    @php
                        $maxType = max($repartitionTypes) ?: 1;
                        $largeur = $maxType > 0 ? max(2, ($montant / $maxType) * 100) : 2;
                    @endphp
                    <div class="wd-perf-bar-row">
                        <div class="top">
                            <span class="label">{{ $label }}</span>
                            <span class="amount">{{ number_format($montant, 0, ',', ' ') }} €</span>
                        </div>
                        <div class="wd-perf-bar-track"><span style="width: {{ $largeur }}%;"></span></div>
                    </div>
                @endforeach
            </div>

        </div>

        <div class="wd-perf-card">
            <h3>Détail par client</h3>
            <p class="sub">Revenus générés sur la période</p>

            <input type="text" class="wd-rev-search" placeholder="Rechercher un client" data-rev-search>

            @if ($detailClients->isEmpty())
                <p style="font-size:13px;color:var(--muted)">Aucun revenu enregistré sur la période.</p>
            @else
                <table class="wd-rev-table">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Conseiller</th>
                            <th>Mandat courtage</th>
                            <th>CIF</th>
                            <th>CII</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody data-rev-rows>
                        @foreach ($detailClients as $ligne)
                            <tr data-rev-name="{{ strtolower($ligne['client']->prenom.' '.$ligne['client']->nom) }}">
                                <td class="muted">{{ $ligne['client']->prenom }} {{ $ligne['client']->nom }}</td>
                                <td class="muted">{{ $ligne['conseiller']?->name ?? '-' }}</td>
                                <td>{{ number_format($ligne['mandat_courtage'], 0, ',', ' ') }} €</td>
                                <td>{{ number_format($ligne['cif'], 0, ',', ' ') }} €</td>
                                <td>{{ number_format($ligne['cii'], 0, ',', ' ') }} €</td>
                                <td class="amount">{{ number_format($ligne['total'], 0, ',', ' ') }} €</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

    </div>

    <script>
        (function () {
            var input = document.querySelector('[data-rev-search]');
            var rows = document.querySelectorAll('[data-rev-rows] tr');

            if (! input || ! rows.length) { return; }

            input.addEventListener('input', function () {
                var terme = input.value.trim().toLowerCase();

                rows.forEach(function (row) {
                    var nom = row.getAttribute('data-rev-name') || '';
                    row.style.display = nom.indexOf(terme) !== -1 ? '' : 'none';
                });
            });
        })();
    </script>
</x-tenant-app-layout>
