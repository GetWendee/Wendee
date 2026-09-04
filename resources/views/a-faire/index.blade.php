<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('À faire') }}</h2>
                <p class="text-sm text-gray-500 mt-1">Liste interne de la console, non visible des cabinets.</p>
            </div>
            <div class="rounded-lg px-4 py-2 text-right" style="background:#171514;">
                <div class="text-2xl font-bold" style="color:#f40087;">{{ $aFaire->count() }}</div>
                <div class="text-xs text-gray-300">tâches à faire</div>
            </div>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-8">
            @if (session('status_simple'))
                <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-4">
                    {{ session('status_simple') }}
                </div>
            @endif

            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Nouvelle tâche</h3>
                <form method="POST" action="{{ route('a-faire.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Titre</label>
                        <input type="text" name="titre" required class="block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Page / module</label>
                        <select name="page_module" class="block w-full border-gray-300 rounded-md shadow-sm">
                            <option value="">Non lié à une page en particulier</option>
                            @foreach ($modules as $module)
                                <option value="{{ $module }}">{{ $module }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="3" class="block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                    </div>
                    <button type="submit" style="background:#242424;border-top:2px solid #f40087;" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest">
                        Enregistrer
                    </button>
                </form>
            </div>

            <div>
                <div class="rounded-lg px-4 py-3 mb-4 font-semibold text-white" style="background:#171514;">
                    À faire ({{ $aFaire->count() }})
                </div>
                <div class="space-y-3">
                    @forelse ($aFaire as $tache)
                        <div x-data="{ edit: false }" class="bg-white shadow-sm rounded-lg p-5">
                            <div x-show="! edit">
                                <div class="flex justify-between items-start">
                                    <h4 class="font-semibold text-gray-800">{{ $tache->titre }}</h4>
                                    @if($tache->page_module)
                                        <span class="text-xs px-2 py-1 rounded-full" style="background:#fdf2f8;color:#f40087;">{{ $tache->page_module }}</span>
                                    @endif
                                </div>
                                @if($tache->description)
                                    <p class="text-sm text-gray-500 mt-1 whitespace-pre-line">{{ $tache->description }}</p>
                                @endif
                                <div class="mt-3 flex gap-2">
                                    <button type="button" @click="edit = true" class="px-3 py-1.5 text-xs font-semibold rounded-md border border-gray-300 text-gray-700">Modifier</button>
                                    <form method="POST" action="{{ route('a-faire.toggle', $tache) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" style="background:#166534;" class="px-3 py-1.5 text-xs font-semibold rounded-md text-white">Fait</button>
                                    </form>
                                </div>
                            </div>
                            <div x-show="edit" x-cloak>
                                <form method="POST" action="{{ route('a-faire.update', $tache) }}" class="space-y-3">
                                    @csrf
                                    @method('PUT')
                                    <input type="text" name="titre" value="{{ $tache->titre }}" required class="block w-full border-gray-300 rounded-md shadow-sm text-sm">
                                    <select name="page_module" class="block w-full border-gray-300 rounded-md shadow-sm text-sm">
                                        <option value="" @selected(! $tache->page_module)>Non lié à une page en particulier</option>
                                        @foreach ($modules as $module)
                                            <option value="{{ $module }}" @selected($tache->page_module === $module)>{{ $module }}</option>
                                        @endforeach
                                    </select>
                                    <textarea name="description" rows="3" class="block w-full border-gray-300 rounded-md shadow-sm text-sm">{{ $tache->description }}</textarea>
                                    <div class="flex gap-2">
                                        <button type="submit" style="background:#242424;border-top:2px solid #f40087;" class="px-3 py-1.5 text-xs font-semibold rounded-md text-white">Enregistrer</button>
                                        <button type="button" @click="edit = false" class="px-3 py-1.5 text-xs font-semibold rounded-md border border-gray-300 text-gray-700">Annuler</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">Rien à faire pour l'instant.</p>
                    @endforelse
                </div>
            </div>

            <div>
                <div class="rounded-lg px-4 py-3 mb-4 font-semibold text-white" style="background:#3f3b38;">
                    Fait ({{ $faites->count() }})
                </div>
                <div class="space-y-3">
                    @forelse ($faites as $tache)
                        <div class="bg-gray-50 shadow-sm rounded-lg p-5">
                            <div class="flex justify-between items-start">
                                <h4 class="font-semibold text-gray-500 line-through">{{ $tache->titre }}</h4>
                                @if($tache->page_module)
                                    <span class="text-xs px-2 py-1 rounded-full bg-gray-200 text-gray-600">{{ $tache->page_module }}</span>
                                @endif
                            </div>
                            @if($tache->description)
                                <p class="text-sm text-gray-400 mt-1 whitespace-pre-line">{{ $tache->description }}</p>
                            @endif
                            <div class="mt-3 flex gap-2">
                                <form method="POST" action="{{ route('a-faire.toggle', $tache) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" style="background:#166534;" class="px-3 py-1.5 text-xs font-semibold rounded-md text-white">Réouvrir</button>
                                </form>
                                <form method="POST" action="{{ route('a-faire.destroy', $tache) }}" onsubmit="return confirm('Supprimer définitivement cette tâche ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 text-xs font-semibold rounded-md border border-red-200 text-red-700 bg-red-50">Supprimer définitivement</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">Aucune tâche terminée.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
