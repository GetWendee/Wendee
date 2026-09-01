<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Créer un cabinet') }}
        </h2>
    </x-slot>

    <div class="py-12">

        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                @if ($errors->any())

                    <div class="mb-6 bg-red-50 border border-red-200 text-red-800 rounded-lg p-4 text-sm">

                        <ul class="list-disc list-inside">

                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach

                        </ul>

                    </div>

                @endif


                <div class="mb-8">

                    <h3 class="text-lg font-semibold text-gray-900">
                        Créer un cabinet
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        Identifiez d’abord l’établissement à partir de son SIRET.
                        Les informations administratives seront récupérées auprès
                        de SIRENE.
                    </p>

                </div>


                {{-- ==================================================
                     RECHERCHE SIRENE
                     ================================================== --}}

                <form
                    method="POST"
                    action="{{ route('cabinets.sirene') }}"
                    class="mb-8"
                >

                    @csrf

                    <div>

                        <x-input-label
                            for="siret"
                            value="SIRET de l'établissement"
                        />

                        <div class="flex gap-3 mt-1">

                            <x-text-input
                                id="siret"
                                name="siret"
                                type="text"
                                inputmode="numeric"
                                maxlength="14"
                                class="block w-full"
                                :value="old('siret', $sireneData['siret'] ?? '')"
                                required
                                autofocus
                                placeholder="Ex. 12345678900012"
                            />

                            <x-primary-button type="submit">
                                Rechercher
                            </x-primary-button>

                        </div>

                        <p class="text-xs text-gray-500 mt-1">
                            14 chiffres.
                        </p>

                    </div>

                </form>


                {{-- ==================================================
                     RÉSULTAT SIRENE
                     ================================================== --}}

                @if($sireneData)

                    <div class="mb-8 border border-gray-200 rounded-lg overflow-hidden">

                        <div class="bg-gray-50 px-5 py-4 border-b border-gray-200">

                            <div class="flex items-center justify-between gap-4">

                                <div>

                                    <p class="text-xs uppercase tracking-wide text-gray-500">
                                        Données officielles SIRENE
                                    </p>

                                    <h3 class="text-lg font-semibold text-gray-900 mt-1">
                                        Établissement identifié
                                    </h3>

                                </div>

                                @if(($sireneData['etat_administratif'] ?? null) === 'A')

                                    <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-800">
                                        Établissement actif
                                    </span>

                                @endif

                            </div>

                        </div>


                        <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-5">

                            <div>
                                <p class="text-xs text-gray-500">Raison sociale</p>
                                <p class="mt-1 font-medium text-gray-900">
                                    {{ $sireneData['raison_sociale'] ?? '-' }}
                                </p>
                            </div>


                            <div>
                                <p class="text-xs text-gray-500">Forme juridique</p>
                                <p class="mt-1 font-medium text-gray-900">
                                    {{ $sireneData['forme_juridique'] ?? '-' }}
                                </p>
                            </div>


                            <div>
                                <p class="text-xs text-gray-500">SIREN</p>
                                <p class="mt-1 font-medium text-gray-900">
                                    {{ $sireneData['siren'] ?? '-' }}
                                </p>
                            </div>


                            <div>
                                <p class="text-xs text-gray-500">SIRET</p>
                                <p class="mt-1 font-medium text-gray-900">
                                    {{ $sireneData['siret'] ?? '-' }}
                                </p>
                            </div>


                            <div class="sm:col-span-2">

                                <p class="text-xs text-gray-500">
                                    Adresse de l'établissement
                                </p>

                                <p class="mt-1 font-medium text-gray-900">
                                    {{ $sireneData['adresse'] ?? '-' }}
                                    @if(!empty($sireneData['code_postal']) || !empty($sireneData['ville']))
                                        <br>
                                        {{ $sireneData['code_postal'] ?? '' }}
                                        {{ $sireneData['ville'] ?? '' }}
                                    @endif
                                </p>

                            </div>


                            <div>
                                <p class="text-xs text-gray-500">Code APE</p>
                                <p class="mt-1 font-medium text-gray-900">
                                    {{ $sireneData['code_ape'] ?? '-' }}
                                </p>
                            </div>


                            <div>
                                <p class="text-xs text-gray-500">Date de création</p>
                                <p class="mt-1 font-medium text-gray-900">
                                    {{ $sireneData['date_creation'] ?? '-' }}
                                </p>
                            </div>


                            @if(!empty($sireneData['enseigne']))

                                <div class="sm:col-span-2">

                                    <p class="text-xs text-gray-500">
                                        Enseigne
                                    </p>

                                    <p class="mt-1 font-medium text-gray-900">
                                        {{ $sireneData['enseigne'] }}
                                    </p>

                                </div>

                            @endif

                        </div>

                    </div>


                    {{-- ==================================================
                         INFORMATIONS WENDEE
                         ================================================== --}}

                    <div class="mb-6">

                        <h3 class="text-lg font-semibold text-gray-900">
                            Informations du cabinet
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Ces informations sont propres à Wendee et ne sont
                            pas récupérées par SIRENE.
                        </p>

                    </div>


                    <form
                        method="POST"
                        action="{{ route('cabinets.store') }}"
                        class="space-y-6"
                    >

                        @csrf

                        {{-- SIRET conservé pour le store --}}

                        <input
                            type="hidden"
                            name="siret"
                            value="{{ $sireneData['siret'] ?? '' }}"
                        />


                        <div>

                            <x-input-label
                                for="name"
                                value="Nom commercial du cabinet"
                            />

                            <x-text-input
                                id="name"
                                name="name"
                                type="text"
                                class="block mt-1 w-full"
                                :value="old('name', $sireneData['enseigne'] ?? '')"
                                required
                            />

                            <p class="text-xs text-gray-500 mt-1">
                                Exemple : W Conseils.
                            </p>

                        </div>


                        <div>

                            <x-input-label
                                for="slug"
                                value="Sous-domaine"
                            />

                            <div class="flex items-center mt-1">

                                <x-text-input
                                    id="slug"
                                    name="slug"
                                    type="text"
                                    class="block w-full"
                                    :value="old('slug')"
                                    required
                                    pattern="[a-z0-9\-]+"
                                />

                                <span class="ms-2 text-gray-500 text-sm whitespace-nowrap">
                                    .wendee.fr
                                </span>

                            </div>

                            <p class="text-xs text-gray-500 mt-1">
                                Minuscules, chiffres et tirets uniquement.
                            </p>

                        </div>


                        <hr>


                        <div>

                            <h3 class="text-lg font-semibold text-gray-900">
                                Premier compte du cabinet
                            </h3>

                            <p class="mt-1 text-sm text-gray-500">
                                Ce compte sera créé avec le rôle courtier.
                            </p>

                        </div>


                        <div>

                            <x-input-label
                                for="courtier_name"
                                value="Nom du courtier"
                            />

                            <x-text-input
                                id="courtier_name"
                                name="courtier_name"
                                type="text"
                                class="block mt-1 w-full"
                                :value="old('courtier_name')"
                                required
                            />

                        </div>


                        <div>

                            <x-input-label
                                for="courtier_email"
                                value="Email du courtier"
                            />

                            <x-text-input
                                id="courtier_email"
                                name="courtier_email"
                                type="email"
                                class="block mt-1 w-full"
                                :value="old('courtier_email')"
                                required
                            />

                        </div>


                        <div class="flex justify-end">

                            <x-primary-button>
                                {{ __('Créer le cabinet') }}
                            </x-primary-button>

                        </div>

                    </form>

                @endif

            </div>

        </div>

    </div>

</x-app-layout>
