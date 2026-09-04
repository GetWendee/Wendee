<x-tenant-app-layout>
    <div class="p-8 max-w-xl mx-auto space-y-8">

        <div>
            <p class="text-xs uppercase tracking-[0.18em] text-[#ff008a] font-semibold">Mon profil</p>
            <h1 class="mt-2 text-3xl font-semibold text-gray-900">Mes coordonnées bancaires</h1>
            <p class="mt-2 text-sm text-gray-500">
                Nécessaires pour recevoir le virement de vos commissions. Toute modification doit être revalidée par votre courtier avant le prochain virement.
            </p>
        </div>

        @if (session('status'))
            <div class="rounded-2xl bg-[#ff008a]/5 border border-[#ff008a]/20 px-5 py-4 text-sm text-[#ff008a] font-semibold">
                {{ session('status') }}
            </div>
        @endif

        <div class="bg-white rounded-3xl border border-gray-200 p-6">
            @if ($profileUser->rib_iban)
                <p class="text-xs uppercase tracking-[0.14em] text-gray-400 font-semibold mb-2">Statut</p>
                <p class="text-sm font-semibold {{ $profileUser->rib_valide ? 'text-emerald-600' : 'text-amber-600' }} mb-6">
                    {{ $profileUser->rib_valide ? 'RIB validé par le courtier' : 'RIB en attente de validation' }}
                </p>
            @endif

            <form method="POST" action="{{ route('tenant.profil.rib.update') }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Titulaire du compte</label>
                    <input type="text" name="rib_titulaire" value="{{ old('rib_titulaire', $profileUser->rib_titulaire) }}"
                           class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-[#ff008a] focus:outline-none">
                    @error('rib_titulaire')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">IBAN</label>
                    <input type="text" name="rib_iban" value="{{ old('rib_iban', $profileUser->rib_iban) }}"
                           class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-[#ff008a] focus:outline-none">
                    @error('rib_iban')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">BIC</label>
                    <input type="text" name="rib_bic" value="{{ old('rib_bic', $profileUser->rib_bic) }}"
                           class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-[#ff008a] focus:outline-none">
                    @error('rib_bic')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-[#ff008a] px-6 py-3 text-sm font-semibold text-white hover:bg-[#e6007c] transition">
                    Enregistrer
                </button>
            </form>
        </div>

    </div>
</x-tenant-app-layout>
