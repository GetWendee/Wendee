<x-tenant-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Nouveau client') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                @if ($errors->any())
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-800 rounded-lg p-4 text-sm">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('tenant.clients.store') }}" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <x-input-label for="civilite" value="Civilité" />
                            <select id="civilite" name="civilite" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                                <option value="">—</option>
                                <option value="M." {{ old('civilite') == 'M.' ? 'selected' : '' }}>M.</option>
                                <option value="Mme" {{ old('civilite') == 'Mme' ? 'selected' : '' }}>Mme</option>
                            </select>
                        </div>
                        <div class="col-span-2">
                            <x-input-label for="date_naissance" value="Date de naissance" />
                            <x-text-input id="date_naissance" name="date_naissance" type="date" class="block mt-1 w-full" :value="old('date_naissance')" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="prenom" value="Prénom" />
                            <x-text-input id="prenom" name="prenom" type="text" class="block mt-1 w-full" :value="old('prenom')" required autofocus />
                        </div>
                        <div>
                            <x-input-label for="nom" value="Nom" />
                            <x-text-input id="nom" name="nom" type="text" class="block mt-1 w-full" :value="old('nom')" required />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="telephone_mobile" value="Téléphone mobile" />
                            <x-text-input id="telephone_mobile" name="telephone_mobile" type="text" class="block mt-1 w-full" :value="old('telephone_mobile')" />
                        </div>
                        <div>
                            <x-input-label for="telephone_domicile" value="Téléphone domicile" />
                            <x-text-input id="telephone_domicile" name="telephone_domicile" type="text" class="block mt-1 w-full" :value="old('telephone_domicile')" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="email" value="E-mail" />
                        <x-text-input id="email" name="email" type="email" class="block mt-1 w-full" :value="old('email')" />
                    </div>

                    <div>
                        <x-input-label for="adresse" value="Adresse" />
                        <x-text-input id="adresse" name="adresse" type="text" class="block mt-1 w-full" :value="old('adresse')" />
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <x-input-label for="code_postal" value="Code postal" />
                            <x-text-input id="code_postal" name="code_postal" type="text" class="block mt-1 w-full" :value="old('code_postal')" />
                        </div>
                        <div class="col-span-2">
                            <x-input-label for="ville" value="Ville" />
                            <x-text-input id="ville" name="ville" type="text" class="block mt-1 w-full" :value="old('ville')" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="pays" value="Pays" />
                        <x-text-input id="pays" name="pays" type="text" class="block mt-1 w-full" :value="old('pays', 'France')" />
                    </div>

                    <div class="flex justify-end">
                        <x-primary-button>
                            {{ __('Créer le client') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-tenant-app-layout>
