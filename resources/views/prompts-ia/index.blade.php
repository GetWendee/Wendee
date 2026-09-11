<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Configuration IA') }}</h2>
            <p class="text-sm text-gray-500 mt-1">
                Prompts système des moteurs IA de Wendee. Toute modification est confirmée par un code envoyé par email avant d'être appliquée au moteur.
            </p>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @php
                $flashLabels = [
                    'code-envoye' => "Code de confirmation envoyé par email. Saisissez-le ci-dessous pour appliquer la modification.",
                    'confirme' => "Modification appliquée : le moteur IA utilise désormais ce nouveau prompt.",
                    'code-invalide' => "Code incorrect. Réessayez, ou relancez une modification depuis le texte.",
                    'annule' => "Modification en attente annulée, le prompt actif n'a pas changé.",
                ];
                $flashErreur = session('status') === 'code-invalide';
            @endphp

            @if(session('status') && isset($flashLabels[session('status')]))
                <div class="rounded-lg p-4 border {{ $flashErreur ? 'bg-red-50 border-red-200 text-red-800' : 'bg-green-50 border-green-200 text-green-800' }}">
                    {{ $flashLabels[session('status')] }}
                </div>
            @endif

            @if($errors->any())
                <div class="rounded-lg p-4 border bg-red-50 border-red-200 text-red-800">
                    @foreach($errors->all() as $erreur)
                        <div>{{ $erreur }}</div>
                    @endforeach
                </div>
            @endif

            <div x-data="{ ouvert: @js(session('prompt_cle')) }" class="space-y-3">
                @foreach($prompts as $prompt)
                    <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">

                        <button type="button"
                            @click="ouvert = (ouvert === @js($prompt->cle)) ? null : @js($prompt->cle)"
                            class="w-full flex items-center justify-between px-5 py-4 text-left">
                            <span class="font-semibold text-gray-800">{{ $prompt->titre }}</span>
                            <span class="flex items-center gap-3">
                                @if($prompt->enAttenteDeConfirmation())
                                    <span class="text-xs font-semibold uppercase tracking-wide text-amber-600 bg-amber-50 px-2 py-1 rounded-full">En attente de confirmation</span>
                                @endif
                                <svg :class="{'rotate-180': ouvert === @js($prompt->cle)}" class="w-4 h-4 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </span>
                        </button>

                        <div x-show="ouvert === @js($prompt->cle)" x-cloak class="px-5 pb-5 border-t border-gray-100 pt-4">

                            @if($prompt->enAttenteDeConfirmation())
                                <div class="mb-4 bg-amber-50 border border-amber-200 rounded-md p-4 space-y-3">
                                    <p class="text-sm text-amber-800">
                                        Une modification est en attente depuis {{ $prompt->code_envoye_le?->diffForHumans() }}, proposée par {{ $prompt->modifiePar?->name ?? $prompt->modifiePar?->email ?? "un utilisateur" }}.
                                        Un code de validation a été envoyé à l'associé.
                                    </p>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <form method="POST" action="{{ route('prompts-ia.confirmer', $prompt) }}" class="flex flex-wrap items-center gap-2">
                                            @csrf
                                            <input type="text" name="code" maxlength="10" placeholder="Code reçu par email" class="border-gray-300 rounded-md shadow-sm text-sm" required>
                                            <button type="submit" class="px-3 py-2 text-xs font-semibold rounded-md text-white" style="background:#171514;">Confirmer</button>
                                        </form>
                                        <form method="POST" action="{{ route('prompts-ia.annuler', $prompt) }}">
                                            @csrf
                                            <button type="submit" class="text-xs text-gray-500 underline">Annuler cette modification</button>
                                        </form>
                                    </div>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('prompts-ia.update', $prompt) }}">
                                @csrf
                                @method('PUT')
                                <textarea name="contenu" rows="18" class="block w-full border-gray-300 rounded-md shadow-sm font-mono text-xs">{{ old('contenu', $prompt->pending_contenu ?? $prompt->contenu) }}</textarea>
                                <div class="flex items-center justify-between mt-3 flex-wrap gap-2">
                                    <span class="text-xs text-gray-400">
                                        Dernière modification appliquée : {{ $prompt->updated_at?->translatedFormat('d F Y à H:i') ?? '—' }}
                                    </span>
                                    <button type="submit" class="px-4 py-2 text-xs font-semibold rounded-md text-white" style="background:#171514;border-top:2px solid #f40087;">
                                        Enregistrer (code envoyé par email)
                                    </button>
                                </div>
                            </form>

                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
</x-app-layout>
