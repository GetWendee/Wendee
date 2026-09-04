<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Cabinets') }}
            </h2>
            <a href="{{ route('cabinets.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                {{ __('Créer un cabinet') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-800 rounded-lg p-4">
                    <p class="font-semibold">Cabinet « {{ session('status')['cabinet'] }} » créé.</p>
                    <p class="mt-1 text-sm">URL : <a class="underline" href="https://{{ session('status')['domain'] }}" target="_blank">https://{{ session('status')['domain'] }}</a></p>
                    <p class="mt-1 text-sm">Compte courtier : {{ session('status')['courtier_email'] }}</p>
                    <p class="mt-1 text-sm">Un e-mail d’activation a été envoyé au courtier à l’adresse indiquée. Il pourra définir son mot de passe depuis le lien reçu.</p>
                </div>
            @endif
            @if (session('status_simple'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-800 rounded-lg p-4">
                    {{ session('status_simple') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Domaine</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Créé le</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($cabinets as $cabinet)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $cabinet->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    @foreach ($cabinet->domains as $domain)
                                        <a class="underline" href="https://{{ $domain->domain }}" target="_blank">{{ $domain->domain }}</a>
                                    @endforeach
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $cabinet->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-4 text-sm">
                                    @if(($cabinet->actif ?? true))
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Actif</span>
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-200 text-gray-600">Désactivé</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <a href="{{ route('cabinets.edit', $cabinet) }}" class="underline text-gray-700 mr-3">Modifier</a>
                                    <form method="POST" action="{{ route('cabinets.toggle-actif', $cabinet) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        @if(($cabinet->actif ?? true))
                                            <button type="submit" class="underline text-red-600" onclick="return confirm('Désactiver ce cabinet ? Les conseillers ne pourront plus se connecter.');">Désactiver</button>
                                        @else
                                            <button type="submit" class="underline text-green-700">Réactiver</button>
                                        @endif
                                    </form>
                                    <form method="POST" action="{{ route('cabinets.destroy', $cabinet) }}" class="inline ml-3">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="underline text-red-800 font-semibold" onclick="return confirm('Supprimer DÉFINITIVEMENT ce cabinet ? La base de données, le sous-domaine et toutes les données seront perdus, sans retour possible. Continuer ?');">Supprimer définitivement</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-sm text-gray-500">Aucun cabinet pour l'instant.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
