<x-tenant-app-layout>
    <div class="p-8 space-y-8">

        <section class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
            <div>
                <p class="text-xs uppercase tracking-[0.18em] text-[#ff008a] font-semibold">
                    Vue cabinet
                </p>
                <h1 class="mt-2 text-3xl font-semibold text-gray-900">
                    Performances
                </h1>
            </div>

            <div class="inline-flex items-center gap-1 rounded-xl border border-gray-200 bg-white p-1">
                @foreach (['mois' => 'Mois', 'trimestre' => 'Trimestre', 'annee' => 'Année'] as $valeur => $label)
                    <a href="{{ route('tenant.performances.index', ['periode' => $valeur]) }}"
                       class="px-4 py-2 rounded-lg text-sm font-semibold transition {{ $periode === $valeur ? 'bg-[#ff008a] text-white' : 'text-gray-500 hover:text-gray-800' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </section>

        {{-- Patrimoine cabinet + KPI --}}
        <section class="grid grid-cols-1 lg:grid-cols-[260px_1fr] gap-5">

            <div class="bg-white rounded-3xl border border-gray-200 p-6 flex flex-col justify-center gap-4">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-[#ff008a] font-semibold">Actifs</span>
                    <span class="text-gray-900 font-semibold">{{ number_format($actifs, 0, ',', ' ') }} €</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-[#ff008a] font-semibold">Passifs</span>
                    <span class="text-gray-900 font-semibold">{{ number_format($passifs, 0, ',', ' ') }} €</span>
                </div>
                <div class="flex items-center justify-between text-sm border-t border-gray-100 pt-4">
                    <span class="text-[#ff008a] font-semibold">Solde</span>
                    <span class="text-gray-900 font-semibold">{{ number_format($solde, 0, ',', ' ') }} €</span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">

                <div class="bg-white rounded-3xl border border-gray-200 p-5">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-400 font-semibold">Clients actifs</p>
                    <p class="mt-3 text-3xl font-semibold text-gray-900">{{ $clientsActifs }}</p>
                </div>

                <div class="bg-white rounded-3xl border border-gray-200 p-5">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-400 font-semibold">Production période</p>
                    <p class="mt-3 text-sm text-gray-400">Historique insuffisant</p>
                </div>

                <div class="bg-white rounded-3xl border border-gray-200 p-5">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-400 font-semibold">Taux de conversion offres</p>
                    <p class="mt-3 text-sm text-gray-400">Historique insuffisant</p>
                </div>

                <div class="bg-white rounded-3xl border border-gray-200 p-5">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-400 font-semibold">Audits réalisés</p>
                    <p class="mt-3 text-3xl font-semibold text-gray-900">
                        {{ $auditsPeriode }}
                        @if (! is_null($evolutionAudits))
                            <span class="ml-1 align-middle text-xs font-semibold {{ $evolutionAudits >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $evolutionAudits >= 0 ? '▲' : '▼' }} {{ number_format(abs($evolutionAudits), 1, ',', ' ') }}%
                            </span>
                        @endif
                    </p>
                </div>

            </div>

        </section>

        {{-- Table conseillers + répartition allocation --}}
        <section class="grid grid-cols-1 xl:grid-cols-[1fr_380px] gap-5">

            <div class="bg-white rounded-3xl border border-gray-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100">
                    <p class="text-xs uppercase tracking-[0.14em] text-gray-400 font-semibold">Par conseiller</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs uppercase tracking-[0.1em] text-gray-400">
                                <th class="px-6 py-3 font-semibold">Conseiller</th>
                                <th class="px-6 py-3 font-semibold">Production</th>
                                <th class="px-6 py-3 font-semibold">Patrimoine géré</th>
                                <th class="px-6 py-3 font-semibold">Clients</th>
                                <th class="px-6 py-3 font-semibold">Conversion</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($lignesConseillers as $ligne)
                                <tr class="border-t border-gray-100">
                                    <td class="px-6 py-4 font-semibold text-gray-900">{{ $ligne['conseiller']->name }}</td>
                                    <td class="px-6 py-4 text-gray-400">-</td>
                                    <td class="px-6 py-4 text-gray-700">{{ number_format($ligne['patrimoine_gere'], 0, ',', ' ') }} €</td>
                                    <td class="px-6 py-4 text-gray-700">{{ $ligne['clients'] }}</td>
                                    <td class="px-6 py-4 text-gray-400">-</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-3xl border border-gray-200 p-6">
                <p class="text-xs uppercase tracking-[0.14em] text-gray-400 font-semibold mb-5">Répartition allocation</p>

                @if (empty($repartition))
                    <p class="text-sm text-gray-400">Aucun actif renseigné.</p>
                @else
                    @php
                        $palette = ['#242424', '#5A5653', '#918984', '#AAA39E', '#C9C3BF', '#E8E4E1', '#F50087', '#6F6965'];
                        $cursor = 0;
                        $segments = [];
                        foreach ($repartition as $index => $bucket) {
                            $start = $cursor;
                            $cursor += $bucket['pct'];
                            $segments[] = $palette[$index % count($palette)] . ' ' . number_format($start, 4, '.', '') . '% ' . number_format($cursor, 4, '.', '') . '%';
                        }
                        $gradient = implode(', ', $segments);
                    @endphp

                    <div class="flex justify-center mb-6">
                        <div class="w-36 h-36 rounded-full" style="background: conic-gradient({{ $gradient }});">
                            <div class="w-[70%] h-[70%] bg-white rounded-full relative top-[15%] left-[15%]"></div>
                        </div>
                    </div>

                    <ul class="space-y-2 text-sm">
                        @foreach ($repartition as $index => $bucket)
                            <li class="flex items-center justify-between">
                                <span class="flex items-center gap-2 text-gray-600">
                                    <span class="inline-block w-2.5 h-2.5 rounded-full" style="background: {{ $palette[$index % count($palette)] }};"></span>
                                    {{ $bucket['label'] }}
                                </span>
                                <span class="text-gray-900 font-semibold">{{ number_format($bucket['pct'], 1, ',', ' ') }}%</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

        </section>

        {{-- Évolution patrimoine + funnel --}}
        <section class="grid grid-cols-1 xl:grid-cols-[1fr_380px] gap-5">

            <div class="bg-white rounded-3xl border border-gray-200 p-6">
                <p class="text-xs uppercase tracking-[0.14em] text-gray-400 font-semibold mb-8">Évolution patrimoine cabinet</p>
                <div class="flex items-center justify-center h-40">
                    <p class="text-sm text-gray-400">Historique insuffisant</p>
                </div>
            </div>

            <div class="bg-white rounded-3xl border border-gray-200 p-6">
                <p class="text-xs uppercase tracking-[0.14em] text-gray-400 font-semibold mb-6">Funnel commercial</p>

                <div class="space-y-5 text-center">
                    <div>
                        <p class="text-[#ff008a] text-sm font-semibold">Clients créés</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $clientsCreesPeriode }}</p>
                        @if (! is_null($evolutionClientsCrees))
                            <p class="text-xs font-semibold {{ $evolutionClientsCrees >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $evolutionClientsCrees >= 0 ? '▲' : '▼' }} {{ number_format(abs($evolutionClientsCrees), 1, ',', ' ') }}%
                            </p>
                        @endif
                    </div>
                    <div>
                        <p class="text-[#ff008a] text-sm font-semibold">Audits réalisés</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $auditsPeriode }}</p>
                    </div>
                    <div>
                        <p class="text-[#ff008a] text-sm font-semibold">Offres</p>
                        <p class="text-2xl font-semibold text-gray-400">-</p>
                    </div>
                    <div>
                        <p class="text-[#ff008a] text-sm font-semibold">Contrats signés</p>
                        <p class="text-2xl font-semibold text-gray-400">-</p>
                    </div>
                </div>
            </div>

        </section>

        {{-- Alertes conformité agrégées --}}
        <section class="bg-white rounded-3xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <p class="text-xs uppercase tracking-[0.14em] text-gray-400 font-semibold">Alertes conformité agrégées</p>
                <p class="text-xs text-gray-400">Classées par niveau de friction</p>
            </div>

            @php
                $maxFriction = $alertesConformite->max(fn ($ligne) => $ligne['kyc_expires'] + $ligne['profils_a_renouveler']) ?: 1;
            @endphp

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs uppercase tracking-[0.1em] text-gray-400">
                            <th class="px-6 py-3 font-semibold">Conseiller</th>
                            <th class="px-6 py-3 font-semibold">Friction</th>
                            <th class="px-6 py-3 font-semibold">KYC expirés</th>
                            <th class="px-6 py-3 font-semibold">Profils à renouveler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($alertesConformite as $ligne)
                            @php
                                $friction = $ligne['kyc_expires'] + $ligne['profils_a_renouveler'];
                                $largeur = max(0, min(100, ($friction / $maxFriction) * 100));
                            @endphp
                            <tr class="border-t border-gray-100">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-[#ff008a]/10 text-[#ff008a] text-xs font-semibold">
                                            {{ collect(explode(' ', $ligne['conseiller']->name))->map(fn ($mot) => mb_substr($mot, 0, 1))->implode('') }}
                                        </span>
                                        <span class="font-semibold text-gray-900">{{ $ligne['conseiller']->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 w-48">
                                    <div class="h-1.5 rounded-full bg-gray-100 overflow-hidden">
                                        <div class="h-full bg-rose-500" style="width: {{ $largeur }}%;"></div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-700">{{ $ligne['kyc_expires'] }}</td>
                                <td class="px-6 py-4 text-gray-700">{{ $ligne['profils_a_renouveler'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

    </div>
</x-tenant-app-layout>
