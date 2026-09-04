<x-tenant-app-layout>
    <div class="p-8 space-y-8">

        <section class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
            <div>
                <p class="text-xs uppercase tracking-[0.18em] text-[#ff008a] font-semibold">
                    {{ $profileUser->role === 'courtier' ? 'Courtier' : 'Conseiller' }}
                </p>

                <h1 class="mt-2 text-3xl font-semibold text-gray-900">
                    {{ $profileUser->name }}
                </h1>

                @if ($profileUser->parent)
                    <p class="mt-2 text-sm text-gray-500">
                        Rattaché à {{ $profileUser->parent->name }}
                    </p>
                @endif
            </div>

            <a href="{{ route('tenant.portefeuille-cabinet.index') }}"
               class="inline-flex items-center gap-2 rounded-xl border border-gray-200 px-5 py-3 text-sm font-semibold text-gray-600 hover:border-[#ff008a] hover:text-[#ff008a] transition">
                Retour au portefeuille
            </a>
        </section>

        <section class="bg-white rounded-3xl border border-gray-200 p-6">
            <p class="text-xs uppercase tracking-[0.18em] text-[#ff008a] font-semibold">
                Coordonnées
            </p>

            <dl class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <dt class="text-gray-500">E-mail</dt>
                    <dd class="text-gray-900">{{ $profileUser->email }}</dd>
                </div>
            </dl>
        </section>

        @if ($profileUser->role === 'apporteur' && $profileUser->rib_iban)
            <section class="bg-white rounded-3xl border border-gray-200 p-6">
                <p class="text-xs uppercase tracking-[0.18em] text-[#ff008a] font-semibold">
                    RIB
                </p>

                <div class="mt-4 flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-900">{{ $profileUser->rib_titulaire }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $profileUser->rib_iban }} @if($profileUser->rib_bic) &middot; {{ $profileUser->rib_bic }} @endif</p>
                        <p class="text-xs mt-2 font-semibold {{ $profileUser->rib_valide ? 'text-emerald-600' : 'text-amber-600' }}">
                            {{ $profileUser->rib_valide ? 'Validé' : 'En attente de validation' }}
                        </p>
                    </div>

                    @if (auth()->user()->effectiveRole() === 'courtier' && ! $profileUser->rib_valide)
                        <form method="POST" action="{{ route('tenant.users.valider-rib', $profileUser) }}">
                            @csrf
                            <button type="submit" class="shrink-0 rounded-xl bg-[#ff008a] px-4 py-2 text-sm font-semibold text-white hover:bg-[#e6007c] transition">
                                Valider le RIB
                            </button>
                        </form>
                    @endif
                </div>
            </section>
        @endif

        @if (auth()->user()->effectiveRole() === 'courtier' && $profileUser->role === 'conseiller')
            <section class="bg-white rounded-3xl border border-gray-200 p-6">
                <p class="text-xs uppercase tracking-[0.18em] text-[#ff008a] font-semibold">
                    Droits
                </p>

                <div class="mt-4 flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-900">Voir tous les clients du cabinet</p>
                        <p class="text-xs text-gray-500 mt-1">
                            Par défaut, {{ $profileUser->name }} ne voit que ses propres clients. Active ce droit pour qu'il voie tous les clients du cabinet. Ses apporteurs restent inchangés.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('tenant.users.toggle-voit-tous-les-clients', $profileUser) }}">
                        @csrf
                        <button type="submit"
                                class="shrink-0 rounded-xl border px-4 py-2 text-sm font-semibold transition {{ $profileUser->voit_tous_les_clients ? 'border-[#ff008a] text-[#ff008a] bg-[#ff008a]/5' : 'border-gray-200 text-gray-600 hover:border-[#ff008a] hover:text-[#ff008a]' }}">
                            {{ $profileUser->voit_tous_les_clients ? 'Activé' : 'Désactivé' }}
                        </button>
                    </form>
                </div>
            </section>
        @endif

        <section class="bg-white rounded-3xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
                <p class="text-xs uppercase tracking-[0.18em] text-[#ff008a] font-semibold">
                    Portefeuille
                </p>

                <h2 class="mt-1 text-xl font-semibold text-gray-900">
                    Clients rattachés
                    <span class="ml-2 align-middle inline-flex rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-semibold text-gray-500">
                        {{ $profileUser->clients->count() }}
                    </span>
                </h2>
            </div>

            <div class="p-6">
                @if ($profileUser->clients->isEmpty())
                    <p class="text-sm text-gray-400">Aucun client rattaché.</p>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
                        @foreach ($profileUser->clients as $client)
                            <a href="{{ route('tenant.clients.show', $client) }}"
                               class="group block rounded-2xl border border-gray-100 bg-gray-50 p-5 transition hover:border-[#ff008a] hover:bg-white hover:shadow-sm">
                                <p class="font-semibold text-gray-900 group-hover:text-[#ff008a] transition">
                                    {{ $client->prenom }} {{ $client->nom }}
                                </p>
                                <p class="mt-1 text-xs text-gray-400 truncate">{{ $client->email }}</p>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

    </div>
</x-tenant-app-layout>
