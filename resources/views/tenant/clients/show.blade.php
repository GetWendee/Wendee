<x-tenant-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $client->civilite }} {{ $client->prenom }} {{ $client->nom }}
            </h2>
            <a href="{{ route('tenant.clients.index') }}" class="text-sm text-gray-600 underline">
                {{ __('Retour à la liste') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-800 rounded-lg p-4 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Coordonnées</h3>
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500">Date de naissance</dt>
                        <dd class="text-gray-900">{{ $client->date_naissance?->format('d/m/Y') ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Conseiller</dt>
                        <dd class="text-gray-900">{{ $client->conseiller?->name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Téléphone mobile</dt>
                        <dd class="text-gray-900">{{ $client->telephone_mobile ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Téléphone domicile</dt>
                        <dd class="text-gray-900">{{ $client->telephone_domicile ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">E-mail</dt>
                        <dd class="text-gray-900">{{ $client->email ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Adresse</dt>
                        <dd class="text-gray-900">{{ $client->adresse ?? '—' }} {{ $client->code_postal }} {{ $client->ville }} {{ $client->pays }}</dd>
                    </div>
                </dl>
            </div>

            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-sm text-gray-500">
                {{ __('Recueil de connaissance client (KYC), Patrimoine et Profil investisseur : à venir.') }}
            </div>
        </div>
    </div>
</x-tenant-app-layout>
