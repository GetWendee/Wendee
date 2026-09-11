<x-tenant-app-layout>
    <div class="p-8 space-y-8">

        @if(session('status'))
        <div style="margin-bottom:-8px;padding:12px 16px;border-radius:8px;background:#fdf2f8;color:#a3195b;font-size:13px;border:1px solid #f6c9e1;">{{ session('status') }}</div>
        @endif

        {{-- En-tête --}}
        <section>
            <p class="text-xs uppercase tracking-[0.18em] text-[#ff008a] font-semibold">
                Compte
            </p>

            <h1 class="mt-1 text-2xl font-semibold text-gray-900">
                Abonnement
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                Le nombre de clients maximum comprend vos clients actifs et clôturés. Les clients archivés ne sont pas comptabilisés.
            </p>
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Abonnement actuel --}}
            <section class="bg-white rounded-3xl border border-gray-200 p-6">
                <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Abonnement actuel</h2>

                <p class="mt-3 text-2xl font-semibold text-gray-900">
                    {{ $nombreClientsMax ? number_format($nombreClientsMax, 0, ',', ' ') : '∞' }} clients
                </p>

                <div class="mt-5">
                    <div class="flex items-center justify-between text-xs text-gray-500 mb-1.5">
                        <span>Utilisation</span>
                        <span class="font-semibold text-gray-700">{{ $nombreClientsActuel }} / {{ $nombreClientsMax ? number_format($nombreClientsMax, 0, ',', ' ') : '∞' }}</span>
                    </div>
                    <div class="h-2 rounded-full bg-gray-100 overflow-hidden">
                        @php
                            $wdPourcentageUsage = $nombreClientsMax ? min(100, round($nombreClientsActuel / $nombreClientsMax * 100)) : 0;
                        @endphp
                        <div class="h-full rounded-full" style="width: {{ $wdPourcentageUsage }}%; background: {{ $wdPourcentageUsage >= 90 ? '#b94d4d' : '#ff008a' }};"></div>
                    </div>
                </div>
            </section>

            {{-- Changer d'offre --}}
            <section class="bg-white rounded-3xl border border-gray-200 p-6">
                <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Changer d'offre</h2>

                <form method="POST" action="{{ route('tenant.abonnement.update') }}" class="mt-3 space-y-3">
                    @csrf
                    @method('PUT')

                    <select name="abonnement_nombre_clients_max" class="block w-full rounded-lg border-gray-200 text-sm focus:border-[#ff008a] focus:ring-[#ff008a]">
                        @foreach ($paliers as $palier)
                            <option
                                value="{{ $palier }}"
                                @selected((int) $nombreClientsMax === $palier)
                                @disabled($palier < $nombreClientsActuel)
                            >
                                {{ number_format($palier, 0, ',', ' ') }} clients @if($palier < $nombreClientsActuel) (insuffisant) @endif
                            </option>
                        @endforeach
                    </select>

                    @error('abonnement_nombre_clients_max')
                        <p class="text-xs font-semibold text-red-600">{{ $message }}</p>
                    @enderror

                    <button type="submit" class="inline-flex rounded-lg bg-[#ff008a] px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-[#e00079] transition">
                        Valider le changement
                    </button>
                </form>

                @if($abonnementModifieParNom)
                    <p class="mt-3 text-xs text-gray-400">
                        Changé par {{ $abonnementModifieParNom }} le {{ $abonnementModifieLe?->translatedFormat('d F Y à H:i') }}
                    </p>
                @endif
            </section>

        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Historique des factures (à venir) --}}
            <section class="bg-white rounded-3xl border border-gray-200 p-6 opacity-70">
                <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide flex items-center gap-2">
                    Historique des factures
                    <span class="inline-flex rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-gray-500">Bientôt disponible</span>
                </h2>
                <p class="mt-2 text-sm text-gray-400">La facturation en ligne arrive prochainement.</p>
            </section>

            {{-- Moyen de paiement (à venir) --}}
            <section class="bg-white rounded-3xl border border-gray-200 p-6 opacity-70">
                <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wide flex items-center gap-2">
                    Moyen de paiement
                    <span class="inline-flex rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-gray-500">Bientôt disponible</span>
                </h2>
                <p class="mt-2 text-sm text-gray-400">La gestion du moyen de paiement arrive prochainement.</p>
            </section>

        </div>

    </div>
</x-tenant-app-layout>
