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

        $sujetLabels = [
            'point_etape' => "Point d'étape",
            'bilan_patrimonial' => 'Bilan patrimonial',
            'signature_document' => 'Signature document',
            'suivi_portefeuille' => 'Suivi portefeuille',
            'autre' => 'Autre',
        ];
        $formatLabels = [
            'visioconference' => 'Visioconférence',
            'telephone' => 'Téléphone',
            'agence' => 'Agence',
            'domicile' => 'Domicile',
        ];

        $nomsConseillers = $conseillers->pluck('name', 'id');
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
            .wd-agenda-cal-btn.active{background:var(--dark);color:#fff;border-color:var(--dark)}

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

            .wd-agenda-legend{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:16px}
            .wd-agenda-legend-item{display:flex;align-items:center;gap:7px;font-size:12.5px;color:var(--ink);font-weight:600;background:none;border:1px solid transparent;border-radius:999px;padding:5px 10px;cursor:pointer}
            .wd-agenda-legend-item:hover{background:var(--bg)}
            .wd-agenda-legend-item.active{border-color:var(--line);background:var(--white)}
            .wd-agenda-legend.filtering .wd-agenda-legend-item:not(.active){opacity:.45}
            .wd-agenda-legend-item .dot{width:8px;height:8px;border-radius:50%;flex:0 0 auto}
            .wd-agenda-event.wd-agenda-hidden, .wd-agenda-month-pill.wd-agenda-hidden, .wd-agenda-busy.wd-agenda-hidden{display:none}

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
            .wd-agenda-hour-col .wd-agenda-hour-label:first-child{transform:translateY(0)}
            .wd-agenda-daycol{position:relative;border-left:1px solid var(--line);background-image:repeating-linear-gradient(to bottom, var(--line) 0, var(--line) 1px, transparent 1px, transparent {{ $hauteurLigne }}px)}
            .wd-agenda-daycol.today{background-color:rgba(244,0,135,.03)}
            .wd-agenda-event{position:absolute;border-radius:6px;border-left:3px solid;padding:2px 6px;font-size:11px;overflow:hidden;line-height:1.2;min-height:26px}
            .wd-agenda-event{z-index:2}
            .wd-agenda-busy{position:absolute;left:0;right:0;border-radius:6px;border-left:3px solid var(--line);background:repeating-linear-gradient(135deg, rgba(21,21,21,.04), rgba(21,21,21,.04) 6px, rgba(21,21,21,.08) 6px, rgba(21,21,21,.08) 12px);z-index:1;pointer-events:none}
            .wd-agenda-event-heure{display:block;font-weight:700;color:var(--dark);font-size:9.5px}
            .wd-agenda-event-client{display:block;color:var(--ink);font-size:10px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}

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

            .wd-agenda-event, .wd-agenda-month-pill{cursor:pointer}

            .wd-agenda-modal-overlay{position:fixed;inset:0;background:rgba(21,21,21,.45);align-items:center;justify-content:center;z-index:200;padding:20px}
            .wd-agenda-modal-overlay[hidden]{display:none}
            .wd-agenda-modal-overlay:not([hidden]){display:flex}
            .wd-agenda-modal{background:var(--white);border-radius:20px;padding:26px 28px;max-width:360px;width:100%;position:relative}
            .wd-agenda-modal-close{position:absolute;top:14px;right:14px;width:28px;height:28px;border-radius:50%;border:none;background:var(--bg);color:var(--muted);font-size:16px;cursor:pointer;line-height:1}
            .wd-agenda-modal-close:hover{color:var(--ink)}
            .wd-agenda-modal-date{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--pink);margin:0 0 6px}
            .wd-agenda-modal h3{font-size:18px;font-weight:700;color:var(--dark);margin:0 0 16px}
            .wd-agenda-modal-row{display:flex;justify-content:space-between;gap:16px;padding:10px 0;border-top:1px solid var(--line);font-size:13px}
            .wd-agenda-modal-row:first-of-type{border-top:0}
            .wd-agenda-modal-row .lbl{color:var(--muted);font-weight:600;flex:0 0 auto}
            .wd-agenda-modal-row span:last-child{color:var(--ink);font-weight:600;text-align:right}

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
                @if ($estCourtier)
                    <button type="button" class="wd-agenda-cal-btn" data-wd-mes-rdv data-mon-id="{{ auth()->id() }}">Uniquement mes RDV</button>
                @endif
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
            <div class="wd-agenda-legend" data-wd-legend>
                @foreach ($conseillers as $conseiller)
                    <button type="button" class="wd-agenda-legend-item" data-wd-legend-filter="{{ $conseiller->id }}">
                        <span class="dot" style="background: {{ $couleurs[$conseiller->id] }};"></span>
                        {{ $conseiller->name }}
                    </button>
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
                                <div class="wd-agenda-month-pill"
                                     data-wd-rdv
                                     data-conseiller-id="{{ $rdv->user_id }}"
                                     data-date="{{ $rdv->starts_at->format('d/m/Y') }} · {{ $rdv->starts_at->format('H:i') }}-{{ $rdv->ends_at->format('H:i') }}"
                                     data-client="{{ $rdv->client->prenom }} {{ $rdv->client->nom }}"
                                     data-conseiller="{{ $rdv->conseiller->name ?? '' }}"
                                     data-tel="{{ $rdv->client->telephone_mobile ?? '' }}"
                                     data-email="{{ $rdv->client->email ?? '' }}"
                                     data-sujet="{{ $sujetLabels[$rdv->sujet] ?? '' }}"
                                     data-format="{{ $formatLabels[$rdv->format] ?? '' }}">
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
                            @foreach ($jourData['indispos'] as $indispo)
                                @php $couleurIndispo = $couleurs[$indispo['conseiller_id']] ?? '#151515'; @endphp
                                <div class="wd-agenda-busy"
                                     style="top: {{ $indispo['top_pct'] }}%; height: {{ $indispo['height_pct'] }}%; border-left-color: {{ $couleurIndispo }}88;"
                                     data-conseiller-id="{{ $indispo['conseiller_id'] }}"
                                     title="Indisponible{{ $estCourtier ? ' · '.($nomsConseillers[$indispo['conseiller_id']] ?? '') : '' }}"></div>
                            @endforeach
                            @foreach ($jourData['evenements'] as $ev)
                                @php $rdv = $ev['rdv']; $couleur = $couleurs[$rdv->user_id] ?? '#f40087'; @endphp
                                <div class="wd-agenda-event"
                                     style="top: {{ $ev['top_pct'] }}%; height: {{ $ev['height_pct'] }}%; left: {{ $ev['left_pct'] }}%; width: calc({{ $ev['width_pct'] }}% - 3px); background: {{ $couleur }}22; border-left-color: {{ $couleur }};"
                                     data-wd-rdv
                                     data-conseiller-id="{{ $rdv->user_id }}"
                                     data-start-min="{{ $ev['debut_minutes'] }}"
                                     data-end-min="{{ $ev['fin_minutes'] }}"
                                     data-date="{{ $rdv->starts_at->format('d/m/Y') }} · {{ $rdv->starts_at->format('H:i') }}-{{ $rdv->ends_at->format('H:i') }}"
                                     data-client="{{ $rdv->client->prenom }} {{ $rdv->client->nom }}"
                                     data-conseiller="{{ $rdv->conseiller->name ?? '' }}"
                                     data-tel="{{ $rdv->client->telephone_mobile ?? '' }}"
                                     data-email="{{ $rdv->client->email ?? '' }}"
                                     data-sujet="{{ $sujetLabels[$rdv->sujet] ?? '' }}"
                                     data-format="{{ $formatLabels[$rdv->format] ?? '' }}">
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

    <div class="wd-agenda-modal-overlay" data-wd-rdv-modal hidden>
        <div class="wd-agenda-modal">
            <button type="button" class="wd-agenda-modal-close" data-wd-rdv-modal-close>&times;</button>
            <p class="wd-agenda-modal-date" data-wd-rdv-modal-date></p>
            <h3 data-wd-rdv-modal-client></h3>
            <div class="wd-agenda-modal-row"><span class="lbl">Conseiller</span><span data-wd-rdv-modal-conseiller></span></div>
            <div class="wd-agenda-modal-row"><span class="lbl">Téléphone</span><span data-wd-rdv-modal-tel></span></div>
            <div class="wd-agenda-modal-row"><span class="lbl">Email</span><span data-wd-rdv-modal-email></span></div>
            <div class="wd-agenda-modal-row"><span class="lbl">Sujet</span><span data-wd-rdv-modal-sujet></span></div>
            <div class="wd-agenda-modal-row"><span class="lbl">Format</span><span data-wd-rdv-modal-format></span></div>
        </div>
    </div>

    <script>
        (function () {
            var bouton = document.querySelector('[data-wd-cal-popup]');

            if (bouton) {
                bouton.addEventListener('click', function () {
                    window.open("{{ route('tenant.calendrier.index') }}", 'wd-calendrier', 'width=480,height=580');
                });
            }

            function relayoutColonnes() {
                document.querySelectorAll('.wd-agenda-daycol').forEach(function (colonne) {
                    var evenements = Array.prototype.slice.call(colonne.querySelectorAll('.wd-agenda-event:not(.wd-agenda-hidden)'));

                    if (! evenements.length) { return; }

                    evenements.sort(function (a, b) {
                        return parseInt(a.getAttribute('data-start-min'), 10) - parseInt(b.getAttribute('data-start-min'), 10);
                    });

                    var clusters = [];
                    var clusterCourant = [];
                    var finMaxCluster = -1;

                    evenements.forEach(function (el) {
                        var debut = parseInt(el.getAttribute('data-start-min'), 10);
                        var fin = parseInt(el.getAttribute('data-end-min'), 10);

                        if (clusterCourant.length && debut >= finMaxCluster) {
                            clusters.push(clusterCourant);
                            clusterCourant = [];
                            finMaxCluster = -1;
                        }

                        clusterCourant.push(el);
                        finMaxCluster = Math.max(finMaxCluster, fin);
                    });

                    if (clusterCourant.length) { clusters.push(clusterCourant); }

                    clusters.forEach(function (cluster) {
                        var finsLanes = [];

                        cluster.forEach(function (el) {
                            var debut = parseInt(el.getAttribute('data-start-min'), 10);
                            var fin = parseInt(el.getAttribute('data-end-min'), 10);
                            var lane = finsLanes.findIndex(function (finLane) { return debut >= finLane; });

                            if (lane === -1) {
                                lane = finsLanes.length;
                                finsLanes.push(fin);
                            } else {
                                finsLanes[lane] = fin;
                            }

                            el.setAttribute('data-lane', lane);
                        });

                        var laneCount = finsLanes.length;
                        var largeur = 100 / laneCount;

                        cluster.forEach(function (el) {
                            var lane = parseInt(el.getAttribute('data-lane'), 10);
                            el.style.left = (lane * largeur) + '%';
                            el.style.width = 'calc(' + largeur + '% - 3px)';
                        });
                    });
                });
            }

            var legende = document.querySelector('[data-wd-legend]');
            var boutonMesRdv = document.querySelector('[data-wd-mes-rdv]');
            var monId = boutonMesRdv ? boutonMesRdv.getAttribute('data-mon-id') : null;

            function appliquerFiltre(idFiltre) {
                if (legende) {
                    legende.querySelectorAll('[data-wd-legend-filter]').forEach(function (b) {
                        b.classList.toggle('active', idFiltre !== null && b.getAttribute('data-wd-legend-filter') === idFiltre);
                    });

                    legende.classList.toggle('filtering', idFiltre !== null);
                }

                if (boutonMesRdv) {
                    boutonMesRdv.classList.toggle('active', idFiltre !== null && idFiltre === monId);
                }

                document.querySelectorAll('[data-conseiller-id]').forEach(function (el) {
                    var visible = idFiltre === null || el.getAttribute('data-conseiller-id') === idFiltre;
                    el.classList.toggle('wd-agenda-hidden', ! visible);
                });

                relayoutColonnes();
            }

            if (legende) {
                legende.querySelectorAll('[data-wd-legend-filter]').forEach(function (bouton) {
                    bouton.addEventListener('click', function () {
                        var dejaActif = bouton.classList.contains('active');
                        appliquerFiltre(dejaActif ? null : bouton.getAttribute('data-wd-legend-filter'));
                    });
                });
            }

            if (boutonMesRdv) {
                boutonMesRdv.addEventListener('click', function () {
                    var dejaActif = boutonMesRdv.classList.contains('active');
                    appliquerFiltre(dejaActif ? null : monId);
                });
            }

            var modal = document.querySelector('[data-wd-rdv-modal]');

            if (! modal) { return; }

            function texteOu(valeur, defaut) {
                return valeur && valeur.length ? valeur : defaut;
            }

            document.querySelectorAll('[data-wd-rdv]').forEach(function (el) {
                el.addEventListener('click', function () {
                    modal.querySelector('[data-wd-rdv-modal-date]').textContent = el.getAttribute('data-date') || '';
                    modal.querySelector('[data-wd-rdv-modal-client]').textContent = el.getAttribute('data-client') || '';
                    modal.querySelector('[data-wd-rdv-modal-conseiller]').textContent = texteOu(el.getAttribute('data-conseiller'), '-');
                    modal.querySelector('[data-wd-rdv-modal-tel]').textContent = texteOu(el.getAttribute('data-tel'), '-');
                    modal.querySelector('[data-wd-rdv-modal-email]').textContent = texteOu(el.getAttribute('data-email'), '-');
                    modal.querySelector('[data-wd-rdv-modal-sujet]').textContent = texteOu(el.getAttribute('data-sujet'), '-');
                    modal.querySelector('[data-wd-rdv-modal-format]').textContent = texteOu(el.getAttribute('data-format'), '-');
                    modal.hidden = false;
                });
            });

            modal.addEventListener('click', function (e) {
                if (e.target === modal) { modal.hidden = true; }
            });

            modal.querySelector('[data-wd-rdv-modal-close]').addEventListener('click', function () {
                modal.hidden = true;
            });
        })();
    </script>
</x-tenant-app-layout>
