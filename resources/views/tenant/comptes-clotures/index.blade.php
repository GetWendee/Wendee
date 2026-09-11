<x-tenant-app-layout>
    <div class="p-8 space-y-8">

        @if(session('status'))
        <div style="margin-bottom:-8px;padding:12px 16px;border-radius:8px;background:#fdf2f8;color:#a3195b;font-size:13px;border:1px solid #f6c9e1;">{{ session('status') }}</div>
        @endif

        {{-- En-tête --}}
        <section class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
            <div>
                <p class="text-xs uppercase tracking-[0.18em] text-[#ff008a] font-semibold">
                    Activité
                </p>

                <h1 class="mt-1 text-2xl font-semibold text-gray-900">
                    Comptes clôturés
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    Un dossier clôturé reste réactivable librement pendant 6 mois, puis est archivé automatiquement.
                    Une fois archivé, seul le courtier peut le réactiver (le conseiller peut en faire la demande),
                    et uniquement pendant 5 ans.
                </p>
            </div>

            <div class="relative">
                <input type="search"
                       id="wd-cc-search"
                       placeholder="Rechercher un nom..."
                       class="w-full sm:w-72 rounded-xl border-gray-200 bg-gray-50 pl-10 pr-4 py-2.5 text-sm focus:border-[#ff008a] focus:ring-[#ff008a]">

                <svg xmlns="http://www.w3.org/2000/svg"
                     class="absolute left-3 top-3 w-4 h-4 text-gray-400"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor"
                     stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m1.35-5.65a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/>
                </svg>
            </div>
        </section>

        {{-- Clôturés --}}
        <section class="bg-white rounded-3xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">
                    Clôturés
                    <span class="ml-2 align-middle inline-flex rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-semibold text-gray-500">
                        {{ $clotures->count() }}
                    </span>
                </h2>
                <p class="mt-1 text-xs text-gray-400">Réactivation libre, avant archivage automatique.</p>
            </div>

            <div class="divide-y divide-gray-100" id="wd-cc-clotures-list">
                @forelse ($clotures as $client)
                    <div class="px-6 py-4 flex flex-wrap items-center justify-between gap-4" data-cc-row data-name="{{ strtolower($client->nomAffichage()) }}">
                        <div class="min-w-0 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-[#f3f1ee] text-gray-600 flex items-center justify-center text-sm font-semibold shrink-0">
                                {{ $client->estMorale() ? mb_strtoupper(mb_substr($client->raison_sociale ?? '', 0, 2)) : mb_strtoupper(mb_substr($client->prenom, 0, 1).mb_substr($client->nom, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <a href="{{ route('tenant.clients.show', $client) }}" class="font-semibold text-gray-900 hover:text-[#ff008a] transition truncate block">
                                    {{ $client->nomAffichage() }}
                                </a>
                                <p class="text-xs text-gray-400 truncate">
                                    {{ $client->conseiller?->name ?? '-' }}
                                    · Clôturé le {{ $client->cloture_le->translatedFormat('d F Y') }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 shrink-0">
                            <span class="text-xs font-semibold {{ $client->joursAvantArchivage() <= 3 ? 'text-amber-600' : 'text-gray-400' }}">
                                Archivage dans {{ $client->joursAvantArchivage() }} j
                            </span>
                            <form method="POST" action="{{ route('tenant.comptes-clotures.reactiver', $client) }}">
                                @csrf
                                <button type="submit" class="inline-flex rounded-lg border border-[#ff008a] px-3 py-1.5 text-xs font-semibold text-[#ff008a] hover:bg-[#fff0f7] transition">
                                    Réactiver
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="px-6 py-10 text-center text-sm text-gray-400">Aucun dossier clôturé.</p>
                @endforelse
            </div>
            <p id="wd-cc-clotures-empty" style="display:none" class="px-6 py-10 text-center text-sm text-gray-400">Aucun résultat pour cette recherche.</p>
        </section>

        {{-- Archivés --}}
        <section class="bg-white rounded-3xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">
                    Archivés
                    <span class="ml-2 align-middle inline-flex rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-semibold text-gray-500">
                        {{ $archives->count() }}
                    </span>
                </h2>
                <p class="mt-1 text-xs text-gray-400">
                    @if($wdRole === 'courtier')
                        Réactivable par toi pendant 5 ans après archivage.
                    @else
                        Réactivable uniquement par le courtier, sur demande, pendant 5 ans après archivage.
                    @endif
                </p>
            </div>

            <div class="divide-y divide-gray-100" id="wd-cc-archives-list">
                @forelse ($archives as $client)
                    <div class="px-6 py-4 flex flex-wrap items-center justify-between gap-4" data-cc-row data-name="{{ strtolower($client->nomAffichage()) }}">
                        <div class="min-w-0 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-[#f3f1ee] text-gray-600 flex items-center justify-center text-sm font-semibold shrink-0">
                                {{ $client->estMorale() ? mb_strtoupper(mb_substr($client->raison_sociale ?? '', 0, 2)) : mb_strtoupper(mb_substr($client->prenom, 0, 1).mb_substr($client->nom, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <a href="{{ route('tenant.clients.show', $client) }}" class="font-semibold text-gray-900 hover:text-[#ff008a] transition truncate block">
                                    {{ $client->nomAffichage() }}
                                </a>
                                <p class="text-xs text-gray-400 truncate">
                                    {{ $client->conseiller?->name ?? '-' }}
                                    · Archivé le {{ $client->archive_le->translatedFormat('d F Y') }}
                                    @if(! $client->estEncoreReactivable())
                                        · délai de réactivation dépassé
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 shrink-0">
                            @if($client->demandeReactivationEnAttente())
                                <span class="inline-flex rounded-full bg-[#fff0f7] px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-[#ff008a]">
                                    Demande en attente
                                </span>
                            @endif

                            @if($client->estEncoreReactivable())
                                @if($wdRole === 'courtier')
                                    <form method="POST" action="{{ route('tenant.comptes-clotures.reactiver', $client) }}">
                                        @csrf
                                        <button type="submit" class="inline-flex rounded-lg border border-[#ff008a] px-3 py-1.5 text-xs font-semibold text-[#ff008a] hover:bg-[#fff0f7] transition">
                                            Réactiver
                                        </button>
                                    </form>
                                @elseif(! $client->demandeReactivationEnAttente())
                                    <form method="POST" action="{{ route('tenant.comptes-clotures.demander-reactivation', $client) }}">
                                        @csrf
                                        <button type="submit" class="inline-flex rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-600 hover:bg-gray-50 transition">
                                            Demander la réactivation
                                        </button>
                                    </form>
                                @endif
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="px-6 py-10 text-center text-sm text-gray-400">Aucun dossier archivé.</p>
                @endforelse
            </div>
            <p id="wd-cc-archives-empty" style="display:none" class="px-6 py-10 text-center text-sm text-gray-400">Aucun résultat pour cette recherche.</p>
        </section>

    </div>

    <script>
    (function () {
        var search = document.getElementById('wd-cc-search');
        if (! search) return;

        var lists = [
            { rows: document.getElementById('wd-cc-clotures-list'), empty: document.getElementById('wd-cc-clotures-empty') },
            { rows: document.getElementById('wd-cc-archives-list'), empty: document.getElementById('wd-cc-archives-empty') },
        ];

        function applyFilter() {
            var term = (search.value || '').trim().toLowerCase();

            lists.forEach(function (list) {
                if (! list.rows) return;
                var rows = Array.prototype.slice.call(list.rows.querySelectorAll('[data-cc-row]'));
                var visibleCount = 0;

                rows.forEach(function (row) {
                    var visible = ! term || row.getAttribute('data-name').indexOf(term) !== -1;
                    row.style.display = visible ? '' : 'none';
                    if (visible) visibleCount++;
                });

                if (list.empty) {
                    list.empty.style.display = (term && visibleCount === 0 && rows.length > 0) ? '' : 'none';
                }
            });
        }

        search.addEventListener('input', applyFilter);
    })();
    </script>
</x-tenant-app-layout>
