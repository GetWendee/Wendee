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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Clients</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($cabinets as $cabinet)
                            <tr
                                x-data="{ open: {{ (($errors->any() && session('error_cabinet_id') === $cabinet->id)) ? 'true' : 'false' }} }"
                                @click="open = true"
                                class="cursor-pointer hover:bg-gray-50 transition"
                            >
                                <td class="px-6 py-4 text-sm text-gray-900 font-medium">{{ $cabinet->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    @foreach ($cabinet->domains as $domain)
                                        <a class="underline" href="https://{{ $domain->domain }}" target="_blank" @click.stop>{{ $domain->domain }}</a>
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
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $cabinet->abonnement_nombre_clients_max ? number_format($cabinet->abonnement_nombre_clients_max, 0, ',', ' ') : '—' }}
                                </td>

                                {{-- Modale d'édition : nom, abonnement, statut, suppression --}}
                                <template x-teleport="body">
                                    <div
                                        x-show="open"
                                        x-cloak
                                        class="wd-cabinet-modal-overlay"
                                        @click.self="open = false"
                                        @keydown.escape.window="open = false"
                                    >
                                        <div class="wd-cabinet-modal-box" @click.stop>
                                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;">
                                                <h2 style="font-size:17px;font-weight:800;color:#151515;">{{ $cabinet->name }}</h2>
                                                <button type="button" @click="open = false" style="background:none;border:none;font-size:20px;cursor:pointer;color:#817b76;">&times;</button>
                                            </div>

                                            <form method="POST" action="{{ route('cabinets.update', $cabinet) }}" class="space-y-4">
                                                @csrf
                                                @method('PUT')

                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom du cabinet</label>
                                                    <input type="text" name="name" value="{{ (session('error_cabinet_id') === $cabinet->id) ? old('name', $cabinet->name) : $cabinet->name }}" class="block w-full border-gray-300 rounded-md shadow-sm">
                                                </div>

                                                <div class="text-sm text-gray-500">
                                                    Domaine : {{ $cabinet->domains->first()->domain ?? '-' }} (non modifiable ici)
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Abonnement — nombre de clients max</label>
                                                    <select name="abonnement_nombre_clients_max" class="block w-full border-gray-300 rounded-md shadow-sm">
                                                        @foreach (\App\Models\Tenant::PALIERS_ABONNEMENT as $palier)
                                                            <option value="{{ $palier }}" @selected((int) $cabinet->abonnement_nombre_clients_max === $palier)>
                                                                {{ number_format($palier, 0, ',', ' ') }} clients
                                                            </option>
                                                        @endforeach
                                                    </select>

                                                    @if($errors->any() && session('error_cabinet_id') === $cabinet->id)
                                                        <p class="mt-1.5 text-xs font-semibold text-red-600">
                                                            {{ $errors->first('abonnement_nombre_clients_max') }}
                                                        </p>
                                                    @endif

                                                    @if($cabinet->abonnement_modifie_par_nom)
                                                        <p class="mt-1.5 text-xs text-gray-400">
                                                            Changé par {{ $cabinet->abonnement_modifie_par_nom }} le {{ $cabinet->abonnement_modifie_le?->translatedFormat('d F Y à H:i') }}
                                                        </p>
                                                    @endif
                                                </div>

                                                <div class="pt-2">
                                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                                        Enregistrer
                                                    </button>
                                                </div>
                                            </form>

                                            <div class="mt-6 pt-5 border-t border-gray-100 flex items-center gap-5">
                                                <form method="POST" action="{{ route('cabinets.toggle-actif', $cabinet) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    @if(($cabinet->actif ?? true))
                                                        <button type="submit" class="text-sm underline text-red-600" onclick="return confirm('Désactiver ce cabinet ? Les conseillers ne pourront plus se connecter.');">Désactiver</button>
                                                    @else
                                                        <button type="submit" class="text-sm underline text-green-700">Réactiver</button>
                                                    @endif
                                                </form>
                                                <form method="POST" action="{{ route('cabinets.destroy', $cabinet) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-sm underline text-red-800 font-semibold" onclick="return confirm('Supprimer DÉFINITIVEMENT ce cabinet ? La base de données, le sous-domaine et toutes les données seront perdus, sans retour possible. Continuer ?');">Supprimer définitivement</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </template>
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

    {{--
        Centrage de la modale : les propriétés de centrage (display/align-items/
        justify-content) doivent vivre dans une classe, PAS dans le style inline
        de l'élément piloté par x-show. Alpine réécrit directement el.style.display
        pour l'affichage/masquage ; si "display:flex" était déclaré dans ce même
        style inline, Alpine l'efface au moment d'afficher la modale (il repasse
        juste sur "" / "none"), et le flex se perd : la modale retombe alors en
        display:block par défaut, collée en haut à gauche au lieu d'être centrée.
        En gardant le flex dans une classe CSS, Alpine ne touche qu'à l'état
        caché/visible et le centrage défini par la classe reste actif.
    --}}
    <style>
    .wd-cabinet-modal-overlay{
        position:fixed;
        inset:0;
        z-index:9999;
        display:flex;
        align-items:center;
        justify-content:center;
        background:rgba(21,21,21,.45);
    }
    .wd-cabinet-modal-box{
        background:#fff;
        border-radius:16px;
        padding:28px;
        max-width:480px;
        width:92%;
        max-height:88vh;
        overflow-y:auto;
    }
    </style>
</x-app-layout>
