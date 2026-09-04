<x-tenant-app-layout>
    @php
        $joursSemaineNoms = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
        $moisNoms = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
        $hauteurLigne = 56;

        if ($vue === 'jour') {
            $libellePeriode = $joursSemaineNoms[$dateRef->dayOfWeekIso - 1].' '.$dateRef->day.' '.$moisNoms[$dateRef->month - 1].' '.$dateRef->year;
        } elseif ($vue === 'mois') {
            $libellePeriode = $moisNoms[$dateRef->month - 1].' '.$dateRef->year;
        } else {
            $libellePeriode = $debutAffichage->month === $finAffichage->month
                ? $debutAffichage->day.' - '.$finAffichage->day.' '.$moisNoms[$finAffichage->month - 1].' '.$finAffichage->year
                : $debutAffichage->day.' '.$moisNoms[$debutAffichage->month - 1].' - '.$finAffichage->day.' '.$moisNoms[$finAffichage->month - 1].' '.$finAffichage->year;
        }

        $totalPeriode = $joursGrille->sum('total');
    @endphp

    <div class="wd-agenda p-8">

        <style>
            .wd-agenda{max-width:1320px;margin:0 auto}
            .wd-agenda-head{display:flex;flex-wrap:wrap;align-items:flex-end;justify-content:space-between;gap:20px;margin-bottom:22px}
            .wd-agenda-eyebrow{color:var(--pink);font-size:11px;font-weight:700;letter-spacing:.16em;text-transform:uppercase}
            .wd-agenda-title{font-size:34px;font-weight:700;color:var(--dark);margin:6px 0 0;letter-spacing:-.02em}

            .wd-agenda-cal{display:flex;align-items:center;gap:10px}
            .wd-agenda-cal-btn{background:var(--white);border:1px solid var(--line);border-radius:999px;padding:9px 18px;font-size:12.5px;font-weight:700;color:var(--ink);cursor:pointer}
            .wd-agenda-cal-btn:hover{border-color:var(--pink);color:var(--pink)}
            .wd-agenda-cal-badges{display:flex;gap:6px}
            .wd-agenda-cal-badge{font-size:11px;font-weight:700;padding:5px 10px;border-radius:999px;background:var(--bg);color:var(--muted)}
            .wd-agenda-cal-badge.ok{background:rgba(77,135,96,.12);color:var(--green)}

            .wd-agenda-toolbar{display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:16px;margin-bottom:20px}
            .wd-agenda-switch{display:inline-flex;background:var(--white);border:1px solid var(--line);border-radius:14px;padding:4px;gap:2px}
            .wd-agenda-switch a{padding:9px 18px;border-radius:10px;font-size:13px;font-weight:600;color:var(--muted);text-decoration:none;transition:.15s}
            .wd-agenda-switch a.active{background:var(--dark);color:#fff}
            .wd-agenda-switch a:not(.active):hover{color:var(--ink)}

            .wd-agenda-nav{display:flex;align-items:center;gap:14px}
            .wd-agenda-nav a{display:flex;align-items:center;justify-content:center;width:34px;height:34px;border-radius:10px;border:1px solid var(--line);background:var(--white);color:var(--ink);text-decoration:none;font-size:15px}
            .wd-agenda-nav a:hover{border-color:var(--pink);color:var(--pink)}
            .wd-agenda-nav .today{width:auto;padding:0 16px;height:34px;display:flex;align-items:center;border-radius:10px;border:1px solid var(--line);background:var(--white);color:var(--ink);text-decoration:none;font-size:12.5px;font-weight:700;white-space:nowrap}
            .wd-agenda-nav .today:hover{border-color:var(--pink);color:var(--pink)}
            .wd-agenda-period{font-size:14px;font-weight:700;color:var(--dark);text-transform:capitalize;min-width:220px}

            .wd-agenda-legend{display:flex;flex-wrap:wrap;gap:16px;margin-bottom:16px}
            .wd-agenda-legend-item{display:flex;align-items:center;gap:7px;font-size:12.5px;color:var(--ink);font-weight:600}
            .wd-agenda-legend-item .dot{width:8px;height:8px;border-radius:50%;flex:0 0 auto}

            .wd-agenda-empty{font-size:13px;color:var(--muted);text-align:center;padding:14px 0}

            .wd-agenda-card{background:var(--white);border:1px solid var(--line);border-radius:24px;overflow:hidden}

            /* Vue jour / semaine */
            .wd-agenda-timegrid-header{display:grid;border-bottom:1px solid var(--line)}
            .wd-agenda-hour-col{}
            .wd-agenda-daycol-header{padding:14px 8px;text-align:center;border-left:1px solid var(--line)}
            .wd-agenda-daycol-header .dow{display:block;font-size:10px;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);font-weight:700}
            .wd-agenda-daycol-header .num{display:inline-block;margin-top:4px;font-size:15px;font-weight:700;color:var(--dark);width:28px;height:28px;line-height:28px;border-radius:50%}
            .wd-agenda-daycol-header.today .num{background:var(--pink);color:#fff}

            .wd-agenda-timegrid-body{display:grid;position:relative}
            .wd-agenda-hour-label{font-size:10.5px;color:var(--muted);text-align:right;padding-right:10px;transform:translateY(-6px)}
            .wd-agenda-daycol{position:relative;border-left:1px solid var(--line);background-image:repeating-linear-gradient(to bottom, var(--line) 0, var(--line) 1px, transparent 1px, transparent {{ $hauteurLigne }}px)}
            .wd-agenda-daycol.today{background-color:rgba(244,0,135,.03)}
            .wd-agenda-event{position:absolute;border-radius:8px;border-left:3px solid;padding:4px 7px;font-size:11px;overflow:hidden;line-height:1.35}
            .wd-agenda-event-heure{display:block;font-weight:700;color:var(--dark);font-size:10.5px}
            .wd-agenda-event-client{display:block;color:var(--ink);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}

            /* Vue mois */
            .wd-agenda-month-header{display:grid;grid-template-columns:repeat(7,1fr);border-bottom:1px solid var(--line)}
            .wd-agenda-month-header div{padding:12px 8px;text-align:center;font-size:10px;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);font-weight:700}
            .wd-agenda-month-grid{display:grid;grid-template-columns:repeat(7,1fr)}
            .wd-agenda-month-cell{min-height:110px;border-left:1px solid var(--line);border-top:1px solid var(--line);padding:8px;display:flex;flex-direction:column;gap:3px}
            .wd-agenda-month-cell.muted{background:var(--bg)}
            .wd-agenda-month-cell.muted .num{color:var(--muted)}
            .wd-agenda-month-cell.today .num{background:var(--pink);color:#fff}
            .wd-agenda-month-cell .num{font-size:12.5px;font-weight:700;color:var(--dark);width:22px;height:22px;line-height:22px;text-align:center;border-radius:50%;margin-bottom:2px}
            .wd-agenda-month-pill{display:flex;align-items:center;gap:5px;font-size:10.5px;color:var(--ink);background:var(--bg);border-radius:6px;padding:3px 6px;overflow:hidden;white-space:nowrap;text-overflow:ellipsis}
            .wd-agenda-month-pill .dot{width:6px;height:6px;border-radius:50%;flex:0 0 auto}
            .wd-agenda-month-more{font-size:10.5px;color:var(--muted);font-weight:600;padding:2px 6px}

            @media (max-width:960px){
                .wd-agenda-daycol-header .dow, .wd-agenda-event-client{font-size:10px}
                .wd-agenda-month-cell{min-height:70px}
            }
        </style>

        <div class="wd-agenda-head">
            <div>
                <p class="wd-agenda-eyebrow">Agenda</p>
                <h1 class="wd-agenda-title">Rendez-vous</h1>
            </div>

            <div class="wd-agenda-cal">
                <button type="button" class="wd-agenda-cal-btn" data-wd-cal-popup>Connecter mon calendrier</button>
                <span class="wd-agenda-cal-badges">
                    <span class="wd-agenda-cal-badge {{ isset($connexionsCalendrier['google']) ? 'ok' : '' }}">Google{{ isset($connexionsCalendrier['google']) ? ' ✓' : '' }}</span>
                    <span class="wd-agenda-cal-badge {{ isset($connexionsCalendrier['microsoft']) ? 'ok' : '' }}">Outlook{{ isset($connexionsCalendrier['microsoft']) ? ' ✓' : '' }}</span>
                </span>
            </div>
        </div>

        <div class="wd-agenda-toolbar">
            <div class="wd-agenda-switch">
                @foreach (['jour' => 'Jour', 'semaine' => 'Semaine', 'mois' => 'Mois'] as $valeur => $label)
                    <a href="{{ route('tenant.rendez-vous.index', ['vue' => $valeur, 'date' => $dateRef->format('Y-m-d')]) }}" class="{{ $vue === $valeur ? 'active' : '' }}">{{ $label }}</a>
                @endforeach
            </div>

            <div class="wd-agenda-nav">
                <a href="{{ route('tenant.rendez-vous.index', ['vue' => $vue, 'date' => $datePrecedente->format('Y-m-d')]) }}">‹</a>
                <a href="{{ route('tenant.rendez-vous.index', ['vue' => $vue, 'date' => now()->format('Y-m-d')]) }}" class="today">Aujourd'hui</a>
                <a href="{{ route('tenant.rendez-vous.index', ['vue' => $vue, 'date' => $dateSuivante->format('Y-m-d')]) }}">›</a>
                <span class="wd-agenda-period">{{ $libellePeriode }}</span>
            </div>
        </div>

        @if ($estCourtier && $conseillers->count() > 1)
            <div class="wd-agenda-legend">
                @foreach ($conseillers as $conseiller)
                    <span class="wd-agenda-legend-item">
                        <span class="dot" style="background: {{ $couleurs[$conseiller->id] }};"></span>
                        {{ $conseiller->name }}
                    </span>
                @endforeach
            </div>
        @endif

        <div class="wd-agenda-card">
            @if ($totalPeriode === 0)
                <p class="wd-agenda-empty">Aucun rendez-vous sur cette période.</p>
            @endif

            @if ($vue === 'mois')
                <div class="wd-agenda-month-header">
                    @foreach ($joursSemaineNoms as $nom)
                        <div>{{ mb_substr($nom, 0, 3) }}</div>
                    @endforeach
                </div>
                <div class="wd-agenda-month-grid">
                    @foreach ($joursGrille as $jourData)
                        @php $horsMois = $jourData['date']->month !== $dateRef->month; @endphp
                        <div class="wd-agenda-month-cell {{ $horsMois ? 'muted' : '' }} {{ $jourData['date']->isToday() ? 'today' : '' }}">
                            <span class="num">{{ $jourData['date']->day }}</span>
                            @foreach ($jourData['evenements']->take(4) as $rdv)
                                @php $couleur = $couleurs[$rdv->user_id] ?? '#f40087'; @endphp
                                <div class="wd-agenda-month-pill" title="{{ $rdv->starts_at->format('H:i') }}-{{ $rdv->ends_at->format('H:i') }} · {{ $rdv->client->prenom }} {{ $rdv->client->nom }} · {{ $rdv->conseiller->name ?? '' }}">
                                    <span class="dot" style="background: {{ $couleur }};"></span>
                                    {{ $rdv->starts_at->format('H:i') }} {{ $rdv->client->nom }}
                                </div>
                            @endforeach
                            @if ($jourData['total'] > 4)
                                <div class="wd-agenda-month-more">+{{ $jourData['total'] - 4 }} de plus</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="wd-agenda-timegrid-header" style="grid-template-columns: 56px repeat({{ $joursGrille->count() }}, 1fr);">
                    <div class="wd-agenda-hour-col"></div>
                    @foreach ($joursGrille as $jourData)
                        <div class="wd-agenda-daycol-header {{ $jourData['date']->isToday() ? 'today' : '' }}">
                            <span class="dow">{{ $joursSemaineNoms[$jourData['date']->dayOfWeekIso - 1] }}</span>
                            <span class="num">{{ $jourData['date']->day }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="wd-agenda-timegrid-body" style="grid-template-columns: 56px repeat({{ $joursGrille->count() }}, 1fr); height: {{ ($heureFin - $heureDebut) * $hauteurLigne }}px;">
                    <div class="wd-agenda-hour-col">
                        @for ($h = $heureDebut; $h < $heureFin; $h++)
                            <div class="wd-agenda-hour-label" style="height: {{ $hauteurLigne }}px;">{{ sprintf('%02d:00', $h) }}</div>
                        @endfor
                    </div>

                    @foreach ($joursGrille as $jourData)
                        <div class="wd-agenda-daycol {{ $jourData['date']->isToday() ? 'today' : '' }}">
                            @foreach ($jourData['evenements'] as $ev)
                                @php $rdv = $ev['rdv']; $couleur = $couleurs[$rdv->user_id] ?? '#f40087'; @endphp
                                <div class="wd-agenda-event"
                                     style="top: {{ $ev['top_pct'] }}%; height: {{ $ev['height_pct'] }}%; left: {{ $ev['left_pct'] }}%; width: calc({{ $ev['width_pct'] }}% - 3px); background: {{ $couleur }}22; border-left-color: {{ $couleur }};"
                                     title="{{ $rdv->starts_at->format('H:i') }}-{{ $rdv->ends_at->format('H:i') }} · {{ $rdv->client->prenom }} {{ $rdv->client->nom }} · {{ $rdv->conseiller->name ?? '' }}{{ $rdv->sujet ? ' · '.$rdv->sujet : '' }}">
                                    <span class="wd-agenda-event-heure">{{ $rdv->starts_at->format('H:i') }}</span>
                                    <span class="wd-agenda-event-client">{{ $rdv->client->prenom }} {{ $rdv->client->nom }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    <script>
        (function () {
            var bouton = document.querySelector('[data-wd-cal-popup]');

            if (! bouton) { return; }

            bouton.addEventListener('click', function () {
                window.open("{{ route('tenant.calendrier.index') }}", 'wd-calendrier', 'width=480,height=580');
            });
        })();
    </script>
</x-tenant-app-layout>
