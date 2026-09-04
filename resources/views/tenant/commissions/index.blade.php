<x-tenant-app-layout>
    <div class="wd-perf p-8">

        <style>
            .wd-perf{max-width:1320px;margin:0 auto}
            .wd-perf-head{display:flex;flex-wrap:wrap;align-items:flex-end;justify-content:space-between;gap:20px;margin-bottom:28px}
            .wd-perf-eyebrow{color:var(--pink);font-size:11px;font-weight:700;letter-spacing:.16em;text-transform:uppercase}
            .wd-perf-title{font-size:34px;font-weight:700;color:var(--dark);margin:6px 0 0;letter-spacing:-.02em}

            .wd-perf-card{background:var(--white);border:1px solid var(--line);border-radius:24px;padding:28px;margin-bottom:16px}
            .wd-perf-card h3{margin:0 0 6px;font-size:12px;text-transform:uppercase;letter-spacing:.1em;color:var(--muted);font-weight:700}
            .wd-perf-card .sub{font-size:12px;color:var(--muted);margin-bottom:18px}

            .wd-perf-avatar{width:34px;height:34px;border-radius:50%;background:rgba(244,0,135,.1);color:var(--pink);display:grid;place-items:center;font-size:11px;font-weight:700;flex:0 0 auto}

            .wd-rev-table{width:100%;border-collapse:collapse;font-size:13px}
            .wd-rev-table thead th{text-align:left;font-size:10px;text-transform:uppercase;letter-spacing:.1em;color:var(--muted);font-weight:700;padding:0 12px 12px 0}
            .wd-rev-table tbody td{padding:13px 12px 13px 0;border-top:1px solid var(--line)}
            .wd-rev-table tbody tr:first-child td{border-top:0}
            .wd-rev-table .name-cell{display:flex;align-items:center;gap:10px;font-weight:600;color:var(--dark)}
            .wd-rev-table .amount{font-weight:700;color:var(--dark)}
            .wd-rev-table .muted{color:var(--muted)}
            .wd-rev-table tbody tr.wd-com-row-blocked{opacity:.5}

            .wd-com-empty{font-size:13px;color:var(--muted)}

            .wd-com-btn{margin-top:18px;background:var(--pink);color:#fff;border:none;border-radius:12px;padding:11px 22px;font-size:13px;font-weight:700;cursor:pointer}
            .wd-com-btn:disabled{opacity:.35;cursor:not-allowed}

            .wd-com-badge{font-size:10px;font-weight:700;padding:4px 10px;border-radius:999px;background:var(--bg);color:var(--muted);white-space:nowrap}
            .wd-com-badge.warn{background:rgba(185,77,77,.1);color:var(--red)}
            .wd-com-badge.ok{background:rgba(77,135,96,.12);color:var(--green)}

            .wd-com-statut{font-size:11px;font-weight:700;padding:5px 12px;border-radius:999px;background:rgba(77,135,96,.12);color:var(--green);white-space:nowrap}
        </style>

        <div class="wd-perf-head">
            <div>
                <p class="wd-perf-eyebrow">Vue cabinet</p>
                <h1 class="wd-perf-title">Commissions</h1>
            </div>
        </div>

        <div class="wd-perf-card">
            <h3>Fonds à recevoir</h3>
            <p class="sub">Missions signées par le client, en attente d'encaissement par le cabinet</p>

            @if ($aRecevoir->isEmpty())
                <p class="wd-com-empty">Aucune commission en attente de réception.</p>
            @else
                <form method="POST" action="{{ route('tenant.commissions.confirmer-fonds-recus') }}">
                    @csrf
                    <table class="wd-rev-table">
                        <thead>
                            <tr>
                                <th><input type="checkbox" data-wd-select-all="a-recevoir"></th>
                                <th>Apporteur</th>
                                <th>Client</th>
                                <th>Mission</th>
                                <th>Tarif</th>
                                <th>Commission</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($aRecevoir as $commission)
                                <tr>
                                    <td><input type="checkbox" name="commissions[]" value="{{ $commission->id }}" data-wd-group="a-recevoir"></td>
                                    <td>
                                        <span class="name-cell">
                                            <span class="wd-perf-avatar">{{ collect(explode(' ', $commission->apporteur->name))->map(fn ($mot) => mb_substr($mot, 0, 1))->implode('') }}</span>
                                            {{ $commission->apporteur->name }}
                                        </span>
                                    </td>
                                    <td class="muted">{{ $commission->client ? $commission->client->prenom.' '.$commission->client->nom : '-' }}</td>
                                    <td class="muted">{{ $commission->libelle_mission }}</td>
                                    <td class="muted">{{ number_format($commission->montant_tarif, 0, ',', ' ') }} €</td>
                                    <td class="amount">{{ number_format($commission->montant_commission, 0, ',', ' ') }} €</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <button type="submit" class="wd-com-btn" data-wd-submit="a-recevoir" disabled>Confirmer la réception des fonds</button>
                </form>
            @endif
        </div>

        <div class="wd-perf-card">
            <h3>Virements à faire</h3>
            <p class="sub">Fonds reçus par le cabinet, en attente de virement à l'apporteur</p>

            @if ($virementsAFaire->isEmpty())
                <p class="wd-com-empty">Aucun virement en attente.</p>
            @else
                <form method="POST" action="{{ route('tenant.commissions.valider-virements') }}">
                    @csrf
                    <table class="wd-rev-table">
                        <thead>
                            <tr>
                                <th><input type="checkbox" data-wd-select-all="virements"></th>
                                <th>Apporteur</th>
                                <th>Client</th>
                                <th>Mission</th>
                                <th>Commission</th>
                                <th>RIB</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($virementsAFaire as $commission)
                                @php $ribValide = $commission->apporteur && $commission->apporteur->rib_valide; @endphp
                                <tr class="{{ $ribValide ? '' : 'wd-com-row-blocked' }}">
                                    <td><input type="checkbox" name="commissions[]" value="{{ $commission->id }}" data-wd-group="virements" {{ $ribValide ? '' : 'disabled' }}></td>
                                    <td>
                                        <span class="name-cell">
                                            <span class="wd-perf-avatar">{{ collect(explode(' ', $commission->apporteur->name))->map(fn ($mot) => mb_substr($mot, 0, 1))->implode('') }}</span>
                                            {{ $commission->apporteur->name }}
                                        </span>
                                    </td>
                                    <td class="muted">{{ $commission->client ? $commission->client->prenom.' '.$commission->client->nom : '-' }}</td>
                                    <td class="muted">{{ $commission->libelle_mission }}</td>
                                    <td class="amount">{{ number_format($commission->montant_commission, 0, ',', ' ') }} €</td>
                                    <td><span class="wd-com-badge {{ $ribValide ? 'ok' : 'warn' }}">{{ $ribValide ? 'Validé' : 'Non validé' }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <button type="submit" class="wd-com-btn" data-wd-submit="virements" disabled>Valider les virements sélectionnés</button>
                    <p class="wd-com-empty" style="margin-top:10px;">Les lignes grisées ont un RIB non validé : elles seront ignorées tant que le RIB de l'apporteur n'est pas validé.</p>
                </form>
            @endif
        </div>

        <div class="wd-perf-card">
            <h3>Derniers paiements</h3>
            <p class="sub">Virements confirmés, information uniquement</p>

            @if ($derniersPaiements->isEmpty())
                <p class="wd-com-empty">Aucun virement effectué pour le moment.</p>
            @else
                <table class="wd-rev-table">
                    <thead>
                        <tr>
                            <th>Apporteur</th>
                            <th>Client</th>
                            <th>Mission</th>
                            <th>Commission</th>
                            <th>Versé le</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($derniersPaiements as $commission)
                            <tr>
                                <td>
                                    <span class="name-cell">
                                        <span class="wd-perf-avatar">{{ collect(explode(' ', $commission->apporteur->name))->map(fn ($mot) => mb_substr($mot, 0, 1))->implode('') }}</span>
                                        {{ $commission->apporteur->name }}
                                    </span>
                                </td>
                                <td class="muted">{{ $commission->client ? $commission->client->prenom.' '.$commission->client->nom : '-' }}</td>
                                <td class="muted">{{ $commission->libelle_mission }}</td>
                                <td class="amount">{{ number_format($commission->montant_commission, 0, ',', ' ') }} €</td>
                                <td class="muted">{{ $commission->verse_le?->format('d/m/Y') }}</td>
                                <td><span class="wd-com-statut">Versé</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

    </div>

    <script>
        (function () {
            document.querySelectorAll('[data-wd-select-all]').forEach(function (selectAll) {
                var groupe = selectAll.getAttribute('data-wd-select-all');
                var cases = document.querySelectorAll('[data-wd-group="' + groupe + '"]:not(:disabled)');
                var bouton = document.querySelector('[data-wd-submit="' + groupe + '"]');

                function maj() {
                    var coche = Array.prototype.some.call(cases, function (c) { return c.checked; });
                    if (bouton) { bouton.disabled = ! coche; }
                }

                selectAll.addEventListener('change', function () {
                    cases.forEach(function (c) { c.checked = selectAll.checked; });
                    maj();
                });

                cases.forEach(function (c) { c.addEventListener('change', maj); });
            });
        })();
    </script>
</x-tenant-app-layout>
