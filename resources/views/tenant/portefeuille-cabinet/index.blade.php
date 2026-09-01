<x-tenant-app-layout>
    <div class="p-8 space-y-8">

        {{-- En-tête --}}
        <section class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
            <div>
                <p class="text-xs uppercase tracking-[0.18em] text-[#ff008a] font-semibold">
                    Portefeuille cabinet
                </p>

                <h1 class="mt-2 text-3xl font-semibold text-gray-900">
                    Votre réseau
                </h1>

                <p class="mt-2 text-sm text-gray-500 max-w-2xl">
                    Retrouvez les conseillers, apporteurs et clients rattachés à votre portefeuille.
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                @if (in_array($user->role, ['courtier', 'conseiller'], true))
                    <button type="button"
                            class="inline-flex items-center gap-2 rounded-xl bg-[#232323] px-5 py-3 text-sm font-semibold text-white hover:bg-black transition">
                        <span class="text-lg leading-none">+</span>
                        Ajouter un apporteur
                    </button>
                @endif

                @if (in_array($user->role, ['courtier', 'conseiller', 'apporteur'], true))
                    <a href="{{ route('tenant.clients.create') }}"
                       class="inline-flex items-center gap-2 rounded-xl bg-[#ff008a] px-5 py-3 text-sm font-semibold text-white hover:opacity-90 transition">
                        <span class="text-lg leading-none">+</span>
                        Ajouter un client
                    </a>
                @endif
            </div>
        </section>

        {{-- Indicateurs --}}
        <section class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div class="bg-white rounded-3xl border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <p class="text-xs uppercase tracking-[0.18em] text-gray-400 font-semibold">
                        Conseillers
                    </p>

                    <div class="w-10 h-10 rounded-xl bg-[#fff0f7] text-[#ff008a] flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm13 10v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </div>
                </div>

                <p class="mt-5 text-3xl font-semibold text-gray-900">
                    {{ $conseillers->count() }}
                </p>

                <p class="mt-1 text-sm text-gray-500">
                    dans votre périmètre
                </p>
            </div>

            <div class="bg-white rounded-3xl border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <p class="text-xs uppercase tracking-[0.18em] text-gray-400 font-semibold">
                        Apporteurs
                    </p>

                    <div class="w-10 h-10 rounded-xl bg-[#fff0f7] text-[#ff008a] flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 20a6 6 0 0 0-12 0M12 14a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm7-1a3 3 0 1 0-2.83-4M5 13a3 3 0 1 1 2.83-4"/>
                        </svg>
                    </div>
                </div>

                <p class="mt-5 text-3xl font-semibold text-gray-900">
                    {{ $apporteurs->count() }}
                </p>

                <p class="mt-1 text-sm text-gray-500">
                    dans votre réseau
                </p>
            </div>

            <div class="bg-white rounded-3xl border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <p class="text-xs uppercase tracking-[0.18em] text-gray-400 font-semibold">
                        Clients
                    </p>

                    <div class="w-10 h-10 rounded-xl bg-[#fff0f7] text-[#ff008a] flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0-8 0 4 4 0 0 0 8 0Zm13 10v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </div>
                </div>

                <p class="mt-5 text-3xl font-semibold text-gray-900">
                    {{ $clients->count() }}
                </p>

                <p class="mt-1 text-sm text-gray-500">
                    dans votre portefeuille
                </p>
            </div>
        </section>

        {{-- Portefeuille --}}
        <section class="bg-white rounded-3xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.18em] text-[#ff008a] font-semibold">
                        Réseau
                    </p>

                    <h2 class="mt-1 text-xl font-semibold text-gray-900">
                        Portefeuille
                        <span class="ml-2 align-middle inline-flex rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-semibold text-gray-500">
                            {{ $conseillers->count() + $apporteurs->count() + $clients->count() }}
                        </span>
                    </h2>
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="relative">
                        <input type="search"
                               id="wd-portfolio-search"
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

                    <select id="wd-portfolio-role"
                            class="rounded-xl border-gray-200 bg-gray-50 py-2.5 text-sm focus:border-[#ff008a] focus:ring-[#ff008a]">
                        <option value="">Tous les profils</option>
                        <option value="conseiller">Conseillers</option>
                        <option value="apporteur">Apporteurs</option>
                        <option value="client">Clients</option>
                    </select>
                </div>
            </div>

            <div class="p-6">
                @if ($conseillers->isEmpty() && $apporteurs->isEmpty() && $clients->isEmpty())
                    <div class="py-16 text-center">
                        <p class="text-sm font-medium text-gray-900">
                            Aucun élément dans votre portefeuille
                        </p>

                        <p class="mt-1 text-sm text-gray-400">
                            Les personnes que vous créez apparaîtront ici.
                        </p>
                    </div>
                @else
                    <div id="wd-portfolio-grid" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
                        @foreach ($conseillers as $conseiller)
                            <a href="{{ route('tenant.users.show', $conseiller) }}"
                               data-portfolio-card
                               data-role="conseiller"
                               data-name="{{ strtolower($conseiller->name) }}"
                               class="group relative block rounded-3xl border border-gray-100 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-12 h-12 rounded-2xl bg-[#232323] text-white flex items-center justify-center text-base font-semibold shrink-0">
                                            {{ strtoupper(substr($conseiller->name, 0, 1)) }}
                                        </div>

                                        <div class="min-w-0">
                                            <p class="font-semibold text-gray-900 truncate group-hover:text-[#ff008a] transition">
                                                {{ $conseiller->name }}
                                            </p>
                                            <span class="mt-1 inline-flex rounded-full bg-[#fff0f7] px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-[#ff008a]">
                                                {{ $conseiller->role === 'courtier' ? 'Courtier' : 'Conseiller' }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="w-9 h-9 rounded-xl bg-[#fff0f7] text-[#ff008a] flex items-center justify-center shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm13 10v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                                        </svg>
                                    </div>
                                </div>

                                <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between gap-3">
                                    <p class="truncate text-sm text-gray-500">{{ $conseiller->email }}</p>
                                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-gray-300 group-hover:text-[#ff008a] transition shrink-0">
                                        Voir la fiche
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 6l6 6-6 6"/>
                                        </svg>
                                    </span>
                                </div>
                            </a>
                        @endforeach

                        @foreach ($apporteurs as $apporteur)
                            <div data-portfolio-card
                                 data-role="apporteur"
                                 data-name="{{ strtolower($apporteur->name) }}"
                                 class="relative rounded-3xl border border-gray-100 bg-white p-6 shadow-sm">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-12 h-12 rounded-2xl bg-[#fff0f7] text-[#ff008a] flex items-center justify-center text-base font-semibold shrink-0">
                                            {{ strtoupper(substr($apporteur->name, 0, 1)) }}
                                        </div>

                                        <div class="min-w-0">
                                            <p class="font-semibold text-gray-900 truncate">{{ $apporteur->name }}</p>
                                            <span class="mt-1 inline-flex rounded-full bg-gray-100 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-gray-600">
                                                Apporteur
                                            </span>
                                        </div>
                                    </div>

                                    <div class="w-9 h-9 rounded-xl bg-gray-100 text-gray-500 flex items-center justify-center shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 20a6 6 0 0 0-12 0M12 14a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm7-1a3 3 0 1 0-2.83-4M5 13a3 3 0 1 1 2.83-4"/>
                                        </svg>
                                    </div>
                                </div>

                                <div class="mt-4 pt-4 border-t border-gray-100 space-y-1">
                                    @if ($apporteur->parent)
                                        <p class="truncate text-xs text-gray-400">Rattaché à {{ $apporteur->parent->name }}</p>
                                    @endif
                                    <p class="truncate text-sm text-gray-500">{{ $apporteur->email }}</p>
                                </div>
                            </div>
                        @endforeach

                        @foreach ($clients as $client)
                            @php $status = $client->completionStatus(); @endphp
                            <a href="{{ route('tenant.clients.show', $client) }}"
                               data-portfolio-card
                               data-role="client"
                               data-name="{{ strtolower($client->prenom.' '.$client->nom) }}"
                               class="group relative block rounded-3xl border border-gray-100 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-12 h-12 rounded-2xl bg-gray-100 text-gray-600 flex items-center justify-center text-base font-semibold shrink-0">
                                            {{ strtoupper(substr($client->prenom, 0, 1).substr($client->nom, 0, 1)) }}
                                        </div>

                                        <div class="min-w-0">
                                            <p class="font-semibold text-gray-900 truncate group-hover:text-[#ff008a] transition">
                                                {{ $client->prenom }} {{ $client->nom }}
                                            </p>
                                            <span class="mt-1 inline-flex rounded-full bg-gray-100 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-gray-600">
                                                Client
                                            </span>
                                        </div>
                                    </div>

                                    <div class="w-9 h-9 rounded-xl bg-gray-100 text-gray-500 flex items-center justify-center shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0-8 0 4 4 0 0 0 8 0Zm13 10v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                                        </svg>
                                    </div>
                                </div>

                                <div class="mt-4 space-y-1 text-sm text-gray-500">
                                    <p class="truncate">{{ $client->conseiller?->name ?? '-' }}</p>
                                    @if ($client->apporteur)
                                        <p class="truncate text-xs text-gray-400">Apporteur : {{ $client->apporteur->name }}</p>
                                    @endif
                                    <p class="truncate text-xs text-gray-400">{{ $client->email }}</p>
                                </div>

                                <div class="mt-4 pt-4 border-t border-gray-100">
                                    @if ($status['a_jour'])
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Informations à jour
                                        </span>
                                    @else
                                        <div class="flex items-center gap-1.5">
                                            @foreach (['kyc' => 'KYC', 'pat' => 'PAT', 'inv' => 'INV'] as $key => $label)
                                                @php $item = $status['items'][$key]; @endphp
                                                <span @class([
                                                    'text-[11px] font-bold px-2.5 py-1.5 rounded-lg',
                                                    'text-gray-300 line-through bg-gray-50' => ! $item['done'],
                                                    'text-amber-600 bg-amber-50' => $item['done'] && $item['stale'],
                                                    'text-[#ff008a] bg-[#fff0f7]' => $item['done'] && ! $item['stale'],
                                                ])>{{ $label }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <p id="wd-portfolio-empty" style="display:none" class="py-16 text-center text-sm text-gray-400">
                        Aucun résultat pour cette recherche.
                    </p>
                @endif
            </div>
        </section>

        <script>
        (function () {
            var search = document.getElementById('wd-portfolio-search');
            var roleSelect = document.getElementById('wd-portfolio-role');
            var grid = document.getElementById('wd-portfolio-grid');
            var empty = document.getElementById('wd-portfolio-empty');
            if (! grid || ! search || ! roleSelect) return;

            var cards = Array.prototype.slice.call(grid.querySelectorAll('[data-portfolio-card]'));

            function applyFilter() {
                var term = (search.value || '').trim().toLowerCase();
                var role = roleSelect.value;
                var visibleCount = 0;

                cards.forEach(function (card) {
                    var matchesRole = ! role || card.getAttribute('data-role') === role;
                    var matchesTerm = ! term || card.getAttribute('data-name').indexOf(term) !== -1;
                    var visible = matchesRole && matchesTerm;
                    card.style.display = visible ? '' : 'none';
                    if (visible) visibleCount++;
                });

                if (empty) {
                    empty.style.display = visibleCount === 0 ? '' : 'none';
                }
            }

            search.addEventListener('input', applyFilter);
            roleSelect.addEventListener('change', applyFilter);
        })();
        </script>

    </div>
</x-tenant-app-layout>
