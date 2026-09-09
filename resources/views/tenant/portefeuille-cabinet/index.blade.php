<x-tenant-app-layout>
    @php
        $wdRole = $user->effectiveRole();
        $showConseillersTuile = $wdRole === 'courtier';
        $showApporteursTuile = in_array($wdRole, ['courtier', 'conseiller'], true);
        $wdTuilesCount = 1 + ($showConseillersTuile ? 1 : 0) + ($showApporteursTuile ? 1 : 0) + ($mesGains ? 1 : 0);
    @endphp
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
                @php
                    $newAccountRoles = $user->creatableUserRoles();
                @endphp
                @if (count($newAccountRoles) === 0)
                    <a href="{{ route('tenant.clients.create') }}" class="wd-new-client">
                        <span class="wd-new-client-plus">+</span>
                        <span>Nouveau compte</span>
                    </a>
                @else
                    <button type="button" class="wd-new-client" data-new-account-trigger>
                        <span class="wd-new-client-plus">+</span>
                        <span>Nouveau compte</span>
                    </button>
                @endif
            </div>
        </section>

        {{-- Indicateurs --}}
        <section class="grid grid-cols-1 gap-5 @if($wdTuilesCount >= 3) md:grid-cols-3 @elseif($wdTuilesCount === 2) md:grid-cols-2 @endif">
            @if($showConseillersTuile)
            <div class="wd-kpi-card bg-white rounded-3xl border border-gray-200 p-6">
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

                <p class="wd-kpi-value mt-5 text-3xl font-semibold text-gray-900">
                    {{ $conseillers->count() }}
                </p>

                <p class="mt-1 text-sm text-gray-500">
                    dans votre périmètre
                </p>
            </div>
            @endif

            @if($showApporteursTuile)
            <div class="wd-kpi-card bg-white rounded-3xl border border-gray-200 p-6">
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

                <p class="wd-kpi-value mt-5 text-3xl font-semibold text-gray-900">
                    {{ $apporteurs->count() }}
                </p>

                <p class="mt-1 text-sm text-gray-500">
                    dans votre réseau
                </p>
            </div>
            @endif

            <div class="wd-kpi-card bg-white rounded-3xl border border-gray-200 p-6">
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

                <p class="wd-kpi-value mt-5 text-3xl font-semibold text-gray-900">
                    {{ $clients->count() }}
                </p>

                <p class="mt-1 text-sm text-gray-500">
                    dans votre portefeuille
                </p>
            </div>

            @if($mesGains)
            <div class="bg-white rounded-3xl border border-gray-200 p-6">
                <p class="text-sm font-semibold text-[#ff008a]">Mes gains</p>
                <p class="text-xs text-gray-400 mt-0.5">Total de ma rémunération</p>

                <div class="mt-4 space-y-3">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">12 derniers mois</span>
                        <span class="font-semibold text-gray-900">{{ number_format($mesGains['douze_derniers_mois'], 0, ',', ' ') }} €</span>
                    </div>
                    <div class="flex items-center justify-between text-sm border-t border-gray-100 pt-3">
                        <span class="text-gray-500">En attente</span>
                        <span class="font-semibold text-gray-900">{{ number_format($mesGains['en_attente'], 0, ',', ' ') }} €</span>
                    </div>
                    <div class="flex items-center justify-between text-sm border-t border-gray-100 pt-3">
                        <span class="text-gray-500">Paiement à venir</span>
                        <span class="inline-flex rounded-lg border border-[#ff008a] px-2.5 py-1 text-sm font-semibold text-[#ff008a]">{{ number_format($mesGains['a_venir'], 0, ',', ' ') }} €</span>
                    </div>
                </div>
            </div>
            @endif
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

                    @if($wdRole !== 'apporteur')
                    <div class="wd-role-select" data-role-select>
                        <button type="button" class="wd-role-select-trigger" data-role-select-trigger>
                            <span data-role-select-label>Tous les profils</span>
                            <svg viewBox="0 0 24 24" class="wd-role-select-chevron"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
                        </button>
                        <div class="wd-role-select-menu" data-role-select-menu hidden>
                            <button type="button" class="wd-role-select-option active" data-value="">Tous les profils</button>
                            @if($showConseillersTuile)
                            <button type="button" class="wd-role-select-option" data-value="conseiller">Conseillers</button>
                            @endif
                            @if($showApporteursTuile)
                            <button type="button" class="wd-role-select-option" data-value="apporteur">Apporteurs</button>
                            @endif
                            <button type="button" class="wd-role-select-option" data-value="client">Clients</button>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            @if($wdRole === 'apporteur')
            <div class="px-6 pb-5 flex flex-wrap gap-2" data-statut-select>
                <button type="button" class="wd-statut-pill active" data-statut-value="">Tous</button>
                <button type="button" class="wd-statut-pill" data-statut-value="prospect_cree">Prospect créé</button>
                <button type="button" class="wd-statut-pill" data-statut-value="premier_contact_qualifie">Premier contact qualifié</button>
                <button type="button" class="wd-statut-pill" data-statut-value="proposition_envoyee">Proposition envoyée</button>
                <button type="button" class="wd-statut-pill" data-statut-value="client_signe">Client signé</button>
                <button type="button" class="wd-statut-pill" data-statut-value="perdu_sans_suite">Perdu / sans suite</button>
            </div>
            @endif

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
                        @if($showConseillersTuile)
                        @foreach ($conseillers as $conseiller)
                            <a href="{{ route('tenant.users.show', $conseiller) }}"
                               data-portfolio-card
                               data-role="conseiller"
                               data-name="{{ strtolower($conseiller->name) }}"
                               class="group relative block overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg">
                                <div class="bg-[#f3f1ee] px-6 pt-6 pb-5 flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-12 h-12 rounded-2xl bg-[#232323] text-white flex items-center justify-center text-base font-semibold shrink-0">
                                            {{ mb_strtoupper(mb_substr($conseiller->name, 0, 1)) }}
                                        </div>

                                        <div class="min-w-0">
                                            <p class="wd-portfolio-name font-semibold text-gray-900 truncate group-hover:text-[#ff008a] transition">
                                                {{ $conseiller->name }}
                                            </p>
                                            <span class="mt-1 inline-flex rounded-full bg-white px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-[#ff008a]">
                                                {{ $conseiller->role === 'courtier' ? 'Courtier' : 'Conseiller' }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="w-9 h-9 rounded-xl bg-white text-[#ff008a] flex items-center justify-center shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm13 10v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                                        </svg>
                                    </div>
                                </div>

                                <div class="bg-white px-6 pt-4 pb-6 flex items-center justify-between gap-3">
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
                        @endif

                        @if($showApporteursTuile)
                        @foreach ($apporteurs as $apporteur)
                            <div data-portfolio-card
                                 data-role="apporteur"
                                 data-name="{{ strtolower($apporteur->name) }}"
                                 class="relative overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-sm">
                                <div class="bg-[#f3f1ee] px-6 pt-6 pb-5 flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-12 h-12 rounded-2xl bg-[#fff0f7] text-[#ff008a] flex items-center justify-center text-base font-semibold shrink-0">
                                            {{ mb_strtoupper(mb_substr($apporteur->name, 0, 1)) }}
                                        </div>

                                        <div class="min-w-0">
                                            <p class="wd-portfolio-name font-semibold text-gray-900 truncate">{{ $apporteur->name }}</p>
                                            <span class="mt-1 inline-flex rounded-full bg-white px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-gray-600">
                                                Apporteur
                                            </span>
                                        </div>
                                    </div>

                                    <div class="w-9 h-9 rounded-xl bg-white text-gray-500 flex items-center justify-center shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 20a6 6 0 0 0-12 0M12 14a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm7-1a3 3 0 1 0-2.83-4M5 13a3 3 0 1 1 2.83-4"/>
                                        </svg>
                                    </div>
                                </div>

                                <div class="bg-white px-6 pt-4 pb-6 space-y-1">
                                    @if ($apporteur->parent)
                                        <p class="truncate text-xs text-gray-400">Rattaché à {{ $apporteur->parent->name }}</p>
                                    @endif
                                    <p class="truncate text-sm text-gray-500">{{ $apporteur->email }}</p>
                                </div>
                            </div>
                        @endforeach
                        @endif

                        @foreach ($clients as $client)
                            @php
                                $status = $client->completionStatus();
                                $statutApporteur = $wdRole === 'apporteur' ? $client->statutApporteur() : null;
                            @endphp
                            <a href="{{ route('tenant.clients.show', $client) }}"
                               data-portfolio-card
                               data-role="client"
                               @if($statutApporteur) data-statut-apporteur="{{ $statutApporteur['key'] }}" @endif
                               data-name="{{ strtolower($client->prenom.' '.$client->nom) }}"
                               class="group relative block overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg">
                                <div class="bg-[#f3f1ee] px-6 pt-6 pb-5 flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-12 h-12 rounded-2xl bg-white text-gray-600 flex items-center justify-center text-base font-semibold shrink-0">
                                            {{ mb_strtoupper(mb_substr($client->prenom, 0, 1).mb_substr($client->nom, 0, 1)) }}
                                        </div>

                                        <div class="min-w-0">
                                            <p class="wd-portfolio-name font-semibold text-gray-900 truncate group-hover:text-[#ff008a] transition">
                                                {{ $client->prenom }} {{ $client->nom }}
                                            </p>
                                            @if($statutApporteur)
                                            <span @class([
                                                'mt-1 inline-flex rounded-full px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider',
                                                'bg-gray-100 text-gray-600' => in_array($statutApporteur['key'], ['prospect_cree', 'perdu_sans_suite']),
                                                'bg-[#fff0f7] text-[#ff008a]' => in_array($statutApporteur['key'], ['premier_contact_qualifie', 'proposition_envoyee']),
                                                'bg-[#ff008a] text-white' => $statutApporteur['key'] === 'client_signe',
                                            ])>
                                                {{ $statutApporteur['label'] }}
                                            </span>
                                            @else
                                            <span class="mt-1 inline-flex rounded-full bg-white px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-gray-600">
                                                Client
                                            </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="w-9 h-9 rounded-xl bg-white text-gray-500 flex items-center justify-center shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0-8 0 4 4 0 0 0 8 0Zm13 10v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                                        </svg>
                                    </div>
                                </div>

                                <div class="bg-white px-6 pt-4 space-y-1 text-sm text-gray-500">
                                    <p class="truncate">{{ $client->conseiller?->name ?? '-' }}</p>
                                    @if ($client->apporteur)
                                        <p class="truncate text-xs text-gray-400">Apporteur : {{ $client->apporteur->name }}</p>
                                    @endif
                                    <p class="truncate text-xs text-gray-400">{{ $client->email }}</p>
                                </div>

                                <div class="bg-white px-6 pt-4 pb-6">
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
            var roleSelectEl = document.querySelector('[data-role-select]');
            var roleTrigger = document.querySelector('[data-role-select-trigger]');
            var roleMenu = document.querySelector('[data-role-select-menu]');
            var roleLabel = document.querySelector('[data-role-select-label]');
            var statutSelectEl = document.querySelector('[data-statut-select]');
            var grid = document.getElementById('wd-portfolio-grid');
            var empty = document.getElementById('wd-portfolio-empty');
            if (! grid || ! search) return;

            var currentRole = '';
            var currentStatut = '';
            var cards = Array.prototype.slice.call(grid.querySelectorAll('[data-portfolio-card]'));

            function applyFilter() {
                var term = (search.value || '').trim().toLowerCase();
                var visibleCount = 0;

                cards.forEach(function (card) {
                    var matchesRole = ! currentRole || card.getAttribute('data-role') === currentRole;
                    var matchesStatut = ! currentStatut || card.getAttribute('data-statut-apporteur') === currentStatut;
                    var matchesTerm = ! term || card.getAttribute('data-name').indexOf(term) !== -1;
                    var visible = matchesRole && matchesStatut && matchesTerm;
                    card.style.display = visible ? '' : 'none';
                    if (visible) visibleCount++;
                });

                if (empty) {
                    empty.style.display = visibleCount === 0 ? '' : 'none';
                }
            }

            search.addEventListener('input', applyFilter);

            if (statutSelectEl) {
                statutSelectEl.querySelectorAll('[data-statut-value]').forEach(function (pill) {
                    pill.addEventListener('click', function () {
                        currentStatut = pill.getAttribute('data-statut-value');
                        statutSelectEl.querySelectorAll('[data-statut-value]').forEach(function (p) { p.classList.remove('active'); });
                        pill.classList.add('active');
                        applyFilter();
                    });
                });
            }

            if (roleTrigger && roleMenu) {
                roleTrigger.addEventListener('click', function (e) {
                    e.stopPropagation();
                    roleMenu.hidden = ! roleMenu.hidden;
                });

                roleMenu.querySelectorAll('[data-value]').forEach(function (option) {
                    option.addEventListener('click', function () {
                        currentRole = option.getAttribute('data-value');
                        roleLabel.textContent = option.textContent;
                        roleMenu.querySelectorAll('[data-value]').forEach(function (o) { o.classList.remove('active'); });
                        option.classList.add('active');
                        roleMenu.hidden = true;
                        applyFilter();
                    });
                });

                document.addEventListener('click', function (e) {
                    if (! roleSelectEl.contains(e.target)) { roleMenu.hidden = true; }
                });
            }
        })();
        </script>

    </div>
<style>
.wd-new-client{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:10px;

    min-height:42px;
    padding:0 17px;

    background:#242424;
    border:1px solid #242424;
    border-radius:7px;

    color:#fff !important;
    text-decoration:none !important;

    font-size:12px;
    font-weight:650;
    letter-spacing:-.01em;

    box-shadow:0 5px 16px rgba(36,36,36,.10);
    transition:
        transform .15s ease,
        box-shadow .15s ease,
        background .15s ease;
}

.wd-new-client-plus{
    display:flex;
    align-items:center;
    justify-content:center;

    width:20px;
    height:20px;

    border-radius:50%;
    background:#f40087;
    color:#fff;

    font-size:17px;
    font-weight:400;
    line-height:1;
}

.wd-new-client:hover{
    background:#171717;
    box-shadow:0 8px 20px rgba(36,36,36,.16);
    transform:translateY(-1px);
}

.wd-role-select{position:relative}
.wd-role-select-trigger{display:flex;align-items:center;justify-content:space-between;gap:10px;width:100%;min-width:170px;padding:0 14px;height:42px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;font-size:13px;font-weight:600;color:#111827;cursor:pointer;font-family:inherit}
.wd-role-select-trigger:hover{border-color:#ff008a}
.wd-role-select-chevron{width:16px;height:16px;flex:0 0 16px;fill:none;stroke:#9ca3af;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
.wd-role-select-menu{position:absolute;top:calc(100% + 6px);left:0;right:0;z-index:60;background:#fff;border:1px solid #e5e7eb;border-radius:12px;box-shadow:0 12px 30px rgba(17,24,39,.12);padding:6px}
.wd-role-select-menu[hidden]{display:none}
.wd-role-select-option{display:block;width:100%;text-align:left;padding:9px 12px;border-radius:8px;border:0;background:none;font-size:13px;font-weight:600;color:#374151;cursor:pointer;font-family:inherit}
.wd-role-select-option:hover{background:#fff0f7;color:#ff008a}
.wd-role-select-option.active{background:#fff0f7;color:#ff008a}

.wd-statut-pill{display:inline-flex;align-items:center;padding:7px 14px;border-radius:999px;border:1px solid #e5e7eb;background:#f9fafb;font-size:12px;font-weight:600;color:#374151;cursor:pointer;font-family:inherit;white-space:nowrap}
.wd-statut-pill:hover{border-color:#ff008a;color:#ff008a}
.wd-statut-pill.active{background:#ff008a;border-color:#ff008a;color:#fff}

@media(max-width:640px){
.wd-portfolio-name{white-space:normal!important;overflow:visible!important;text-overflow:clip!important}
.wd-kpi-card{padding:14px!important;border-radius:18px!important}
.wd-kpi-value{font-size:22px!important;margin-top:8px!important}
.wd-role-select-trigger{min-width:0}
}
</style>

</x-tenant-app-layout>
