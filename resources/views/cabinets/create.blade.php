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
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-800 rounded-lg p-4 text-sm">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('cabinets.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="name" value="Nom du cabinet" />
                        <x-text-input id="name" name="name" type="text" class="block mt-1 w-full" :value="old('name')" required autofocus />
                    </div>

                    <div>
                        <x-input-label for="slug" value="Sous-domaine (ex: dupont-patrimoine)" />
                        <div class="flex items-center mt-1">
                            <x-text-input id="slug" name="slug" type="text" class="block w-full" :value="old('slug')" required pattern="[a-z0-9\-]+" />
                            <span class="ms-2 text-gray-500 text-sm whitespace-nowrap">.wendee.fr</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Minuscules, chiffres et tirets uniquement.</p>
                    </div>

                    <hr>

                    <div>
                        <x-input-label for="courtier_name" value="Nom du courtier (premier compte du cabinet)" />
                        <x-text-input id="courtier_name" name="courtier_name" type="text" class="block mt-1 w-full" :value="old('courtier_name')" required />
                    </div>

                    <div>
                        <x-input-label for="courtier_email" value="Email du courtier" />
                        <x-text-input id="courtier_email" name="courtier_email" type="email" class="block mt-1 w-full" :value="old('courtier_email')" required />
                    </div>

                    <div class="flex justify-end">
                        <x-primary-button>
                            {{ __('Créer le cabinet') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
