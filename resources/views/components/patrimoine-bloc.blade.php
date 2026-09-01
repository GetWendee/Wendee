@props([
    'categorie',
    'label',
    'modeDetention' => false
])

@php
    $natures = config("patrimoine.natures.$categorie", []);
    $natureKeys = array_keys($natures);
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

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

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


                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-2">
                            Montant (€)
                        </label>

                        <input
                            type="number"
                            step="0.01"
                            :name="`elements[{{ $categorie }}_${i}][montant]`"
                            x-model.number="e.montant"
                            class="border-gray-300 rounded-md shadow-sm w-full text-sm"
                        >
                    </div>

                </div>


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
                mode_detention:''
            })"
            class="text-sm font-semibold text-gray-800 mt-2"
        >
            + Ajouter une ligne
        </button>

    </div>

</details>
