<x-tenant-app-layout>
    <div class="wd-cli p-8">

        <style>
            .wd-cli{max-width:1320px;margin:0 auto}
            .wd-cli-head{display:flex;flex-wrap:wrap;align-items:flex-end;justify-content:space-between;gap:20px;margin-bottom:22px}
            .wd-cli-eyebrow{color:var(--pink);font-size:11px;font-weight:700;letter-spacing:.16em;text-transform:uppercase}
            .wd-cli-title{font-size:34px;font-weight:700;color:var(--dark);margin:6px 0 0;letter-spacing:-.02em}
            .wd-cli-count{font-size:13px;color:var(--muted);margin-top:6px}

            .wd-cli-new{background:var(--pink);color:#fff;border:none;border-radius:12px;padding:11px 22px;font-size:13px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:8px}
            .wd-cli-new:hover{opacity:.92}

            .wd-cli-toolbar{display:flex;align-items:center;gap:12px;margin-bottom:20px}
            .wd-cli-search{flex:0 0 auto;width:320px;position:relative}
            .wd-cli-search input{width:100%;box-sizing:border-box;background:var(--white);border:1px solid var(--line);border-radius:12px;padding:10px 14px 10px 38px;font-size:13px;color:var(--ink)}
            .wd-cli-search input:focus{outline:none;border-color:var(--pink)}
            .wd-cli-search svg{position:absolute;left:13px;top:50%;transform:translateY(-50%);color:var(--muted)}
            .wd-cli-clear{font-size:12.5px;color:var(--muted);text-decoration:none;font-weight:600}
            .wd-cli-clear:hover{color:var(--pink)}

            .wd-cli-status{margin-bottom:16px;background:rgba(77,135,96,.12);color:var(--green);border-radius:12px;padding:12px 16px;font-size:13px}

            .wd-cli-card{background:var(--white);border:1px solid var(--line);border-radius:24px;overflow:hidden}

            .wd-cli-table{width:100%;border-collapse:collapse;font-size:13px}
            .wd-cli-table thead th{text-align:left;font-size:10px;text-transform:uppercase;letter-spacing:.1em;color:var(--muted);font-weight:700;padding:16px 16px;border-bottom:1px solid var(--line)}
            .wd-cli-table tbody td{padding:14px 16px;border-top:1px solid var(--line)}
            .wd-cli-table tbody tr:first-child td{border-top:0}
            .wd-cli-table tbody tr{cursor:pointer}
            .wd-cli-table tbody tr:hover{background:var(--bg)}

            .wd-cli-name-cell{display:flex;align-items:center;gap:11px}
            .wd-cli-avatar{width:36px;height:36px;border-radius:50%;background:rgba(244,0,135,.1);color:var(--pink);display:grid;place-items:center;font-size:12px;font-weight:700;flex:0 0 auto}
            .wd-cli-name{font-weight:600;color:var(--dark)}

            .wd-cli-contact{display:flex;flex-direction:column;gap:2px}
            .wd-cli-contact .email{color:var(--ink)}
            .wd-cli-contact .tel{color:var(--muted);font-size:12px}

            .wd-cli-badge{font-size:11px;font-weight:700;padding:5px 12px;border-radius:999px;background:var(--bg);color:var(--muted);white-space:nowrap}

            .wd-cli-empty{font-size:13px;color:var(--muted);text-align:center;padding:40px 0}

            .wd-cli-pagination{margin-top:18px}
            .wd-cli-pagination nav > div:first-child{display:none}

            @media (max-width:720px){
                .wd-cli-search{width:100%}
                .wd-cli-contact .tel, .wd-cli-table thead th:nth-child(4), .wd-cli-table tbody td:nth-child(4){display:none}
            }
        </style>

        <div class="wd-cli-head">
            <div>
                <p class="wd-cli-eyebrow">Portefeuille</p>
                <h1 class="wd-cli-title">Clients</h1>
                <p class="wd-cli-count">{{ $totalClients }} client{{ $totalClients > 1 ? 's' : '' }} au total</p>
            </div>

            <a href="{{ route('tenant.clients.create') }}" class="wd-cli-new">Nouveau client</a>
        </div>

        @if (session('status'))
            <div class="wd-cli-status">{{ session('status') }}</div>
        @endif

        <div class="wd-cli-toolbar">
            <form method="GET" class="wd-cli-search">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/><path d="M20 20L16.5 16.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                <input type="text" name="q" value="{{ $recherche }}" placeholder="Nom, email, ville...">
            </form>
            @if ($recherche !== '')
                <a href="{{ route('tenant.clients.index') }}" class="wd-cli-clear">Effacer</a>
            @endif
        </div>

        <div class="wd-cli-card">
            <table class="wd-cli-table">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Contact</th>
                        <th>Conseiller</th>
                        <th>Ville</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($clients as $client)
                        @php
                            $initiales = collect(explode(' ', trim($client->prenom.' '.$client->nom)))
                                ->filter()
                                ->map(fn ($mot) => mb_substr($mot, 0, 1))
                                ->take(2)
                                ->implode('');
                        @endphp
                        <tr onclick="window.location='{{ route('tenant.clients.show', $client) }}'">
                            <td>
                                <span class="wd-cli-name-cell">
                                    <span class="wd-cli-avatar">{{ mb_strtoupper($initiales) }}</span>
                                    <span class="wd-cli-name">{{ $client->civilite }} {{ $client->prenom }} {{ $client->nom }}</span>
                                </span>
                            </td>
                            <td>
                                <span class="wd-cli-contact">
                                    <span class="email">{{ $client->email ?: '-' }}</span>
                                    @if ($client->telephone_mobile)
                                        <span class="tel">{{ $client->telephone_mobile }}</span>
                                    @endif
                                </span>
                            </td>
                            <td>
                                @if ($client->conseiller)
                                    <span class="wd-cli-badge">{{ $client->conseiller->name }}</span>
                                @else
                                    <span class="wd-cli-badge">-</span>
                                @endif
                            </td>
                            <td>{{ $client->ville ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <p class="wd-cli-empty">
                                    @if ($recherche !== '')
                                        Aucun client ne correspond à "{{ $recherche }}".
                                    @else
                                        Aucun client pour l'instant.
                                    @endif
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="wd-cli-pagination">
            {{ $clients->links() }}
        </div>

    </div>
</x-tenant-app-layout>
