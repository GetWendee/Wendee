@props([
    'categorie',
    'label',
    'modeDetention' => false,
    'foyerAvecConjoint' => false,
])

@php
    $natures = config("patrimoine.natures.$categorie", []);
    $natureKeys = array_keys($natures);
    $typesPret = config('patrimoine.types_pret', []);
    $bienOptions = config('patrimoine.bien', []);
    $estPassif = $categorie === 'passif';
    $montantLabel = $estPassif ? 'Montant emprunté (€)' : 'Montant (€)';
@endphp

<details class="bg-white shadow-sm sm:rounded-lg mb-4" open>

    <summary class="cursor-pointer px-6 py-4 font-semibold text-gray-800 flex justify-between items-center">
        <span>{{ $label }}</span>

        <span
            class="text-sm font-normal text-gray-500"
            x-text="total('{{ $categorie }}').toLocaleString('fr-FR', {
                style: 'currency',
                currency: 'EUR'
            })"
        ></span>
    </summary>

    <div class="px-7 pb-6 pt-2 space-y-3">

        <template x-for="(e, i) in blocks.{{ $categorie }}" :key="i">

            <div class="border border-gray-200 rounded-xl px-5 py-5">

                <div class="grid grid-cols-1 md:grid-cols-{{ $estPassif ? '4' : '3' }} gap-5">

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-2">
                            Nature
                        </label>

                        @if(count($natures))

                            <select
                                :name="`elements[{{ $categorie }}_${i}][nature]`"
                                x-model="e.nature"
                                class="border-gray-300 rounded-md shadow-sm w-full text-sm"
                            >
                                <option value="">Choisir</option>

                                <option
                                    x-show="
                                        e.nature
                                        && !@js($natureKeys).includes(e.nature)
                                    "
                                    :value="e.nature"
                                    x-text="e.nature"
                                ></option>

                                @foreach($natures as $value => $natureLabel)
                                    <option value="{{ $value }}">
                                        {{ $natureLabel }}
                                    </option>
                                @endforeach
                            </select>

                        @else

                            <input
                                type="text"
                                :name="`elements[{{ $categorie }}_${i}][nature]`"
                                x-model="e.nature"
                                class="border-gray-300 rounded-md shadow-sm w-full text-sm"
                            >

                        @endif
                    </div>

                    @if($estPassif)
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-2">
                            Type de prêt
                        </label>

                        <select
                            :name="`elements[{{ $categorie }}_${i}][type_pret]`"
                            x-model="e.type_pret"
                            class="border-gray-300 rounded-md shadow-sm w-full text-sm"
                        >
                            <option value="">Choisir</option>
                            @foreach($typesPret as $value => $typeLabel)
                                <option value="{{ $value }}">{{ $typeLabel }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-2">
                            Désignation
                        </label>

                        <input
                            type="text"
                            :name="`elements[{{ $categorie }}_${i}][designation]`"
                            x-model="e.designation"
                            class="border-gray-300 rounded-md shadow-sm w-full text-sm"
                        >
                    </div>

                    @if($estPassif)
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-2">
                            Date de souscription
                        </label>

                        <input
                            type="date"
                            :name="`elements[{{ $categorie }}_${i}][date_souscription]`"
                            x-model="e.date_souscription"
                            class="border-gray-300 rounded-md shadow-sm w-full text-sm"
                        >
                    </div>
                    @else
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-2">
                            {{ $montantLabel }}
                        </label>

                        <input
                            type="number"
                            step="0.01"
                            :name="`elements[{{ $categorie }}_${i}][montant]`"
                            x-model.number="e.montant"
                            class="border-gray-300 rounded-md shadow-sm w-full text-sm"
                        >
                    </div>
                    @endif

                </div>

                @if($estPassif)
                <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mt-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-2">
                            Montant emprunté (€)
                        </label>
                        <input
                            type="number"
                            step="0.01"
                            :name="`elements[{{ $categorie }}_${i}][montant]`"
                            x-model.number="e.montant"
                            class="border-gray-300 rounded-md shadow-sm w-full text-sm"
                        >
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-2">
                            Durée (années)
                        </label>
                        <input
                            type="number"
                            :name="`elements[{{ $categorie }}_${i}][duree]`"
                            x-model.number="e.duree"
                            class="border-gray-300 rounded-md shadow-sm w-full text-sm"
                        >
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-2">
                            Taux d'intérêt (%)
                        </label>
                        <input
                            type="number"
                            step="0.01"
                            :name="`elements[{{ $categorie }}_${i}][taux_interet]`"
                            x-model.number="e.taux_interet"
                            class="border-gray-300 rounded-md shadow-sm w-full text-sm"
                        >
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-2">
                            Taux d'assurance (%)
                        </label>
                        <input
                            type="number"
                            step="0.01"
                            :name="`elements[{{ $categorie }}_${i}][taux_assurance]`"
                            x-model.number="e.taux_assurance"
                            class="border-gray-300 rounded-md shadow-sm w-full text-sm"
                        >
                    </div>
                </div>
                @endif


                @if ($modeDetention)

                    <div class="mt-4">

                        <div class="text-xs font-semibold text-gray-600 mb-3">
                            Mode de détention
                        </div>

                        <div class="wd-patrimoine-radios">

                            @foreach([
                                'Pleine propriété',
                                'Usufruit',
                                'Nue-propriété'
                            ] as $detention)

                                <label class="wd-patrimoine-radio">
                                    <input
                                        type="radio"
                                        :name="`elements[{{ $categorie }}_${i}][mode_detention]`"
                                        value="{{ $detention }}"
                                        x-model="e.mode_detention"
                                    >

                                    <span>{{ $detention }}</span>
                                </label>

                            @endforeach

                        </div>

                    </div>

                @endif

                @if($foyerAvecConjoint)
                    <div class="mt-4">

                        <div class="text-xs font-semibold text-gray-600 mb-3">
                            Bien
                        </div>

                        <div class="wd-patrimoine-radios">

                            @foreach($bienOptions as $value => $bienLabel)

                                <label class="wd-patrimoine-radio">
                                    <input
                                        type="radio"
                                        :name="`elements[{{ $categorie }}_${i}][bien]`"
                                        value="{{ $value }}"
                                        x-model="e.bien"
                                    >

                                    <span>{{ $bienLabel }}</span>
                                </label>

                            @endforeach

                        </div>

                    </div>
                @endif


                <input
                    type="hidden"
                    :name="`elements[{{ $categorie }}_${i}][categorie]`"
                    value="{{ $categorie }}"
                >


                <div class="flex justify-end mt-4">

                    <button
                        type="button"
                        @click="blocks.{{ $categorie }}.splice(i, 1)"
                        class="text-xs font-semibold text-red-600"
                    >
                        Retirer
                    </button>

                </div>

            </div>

        </template>


        <button
            type="button"
            @click="blocks.{{ $categorie }}.push({
                nature:'',
                designation:'',
                montant:0,
                mode_detention:'',
                type_pret:'',
                date_souscription:'',
                duree:'',
                taux_interet:'',
                taux_assurance:'',
                bien:''
            })"
            class="text-sm font-semibold text-gray-800 mt-2"
        >
            + Ajouter une ligne
        </button>

    </div>

</details>
