@php
    $kyc = $client->kyc;
    $old = fn($field, $default = null) => old($field, $kyc?->{$field} ?? $default);
    $opts = function($liste, $field) use ($old) {
        $current = $old($field);
        $html = '<option value="">-</option>';
        foreach (($liste ?? []) as $value => $label) {
            $sel = ((string) $current === (string) $value) ? 'selected' : '';
            $html .= '<option value="'.e($value).'" '.$sel.'>'.e($label).'</option>';
        }
        return $html;
    };
@endphp
<x-tenant-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Recueil KYC - {{ $client->prenom }} {{ $client->nom }}
            </h2>
            <a href="{{ route('tenant.clients.show', $client) }}" class="text-sm text-gray-600 underline">
                {{ __('Retour à la fiche client') }}
            </a>
        </div>
    </x-slot>

    <style>
        .wd-kyc-page {
            color: #242424;
        }

        .wd-kyc-intro {
            background: #242424;
            border-radius: 16px;
            padding: 34px 38px;
            margin-bottom: 28px;
            position: relative;
            overflow: hidden;
        }

        .wd-kyc-intro::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: #F40087;
        }

        .wd-kyc-intro-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.1fr) minmax(340px, .9fr);
            gap: 52px;
            align-items: stretch;
        }

        .wd-kyc-intro-main {
            padding-right: 12px;
        }

        .wd-kyc-intro-side {
            border-left: 1px solid rgba(255,255,255,.12);
            padding-left: 38px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .wd-kyc-side-label {
            color: #a9a9a9;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .16em;
            text-transform: uppercase;
            margin-bottom: 18px;
        }

        .wd-kyc-objective {
            display: grid;
            grid-template-columns: 34px 1fr;
            gap: 14px;
            padding: 15px 0;
            border-top: 1px solid rgba(255,255,255,.10);
        }

        .wd-kyc-objective:last-child {
            border-bottom: 1px solid rgba(255,255,255,.10);
        }

        .wd-kyc-objective span {
            color: #F40087;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .08em;
            padding-top: 2px;
        }

        .wd-kyc-objective p {
            margin: 0 !important;
            color: #f1f1f1;
            font-size: 13px;
            line-height: 1.55;
        }

        .wd-kyc-eyebrow {
            color: #F40087;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .18em;
            text-transform: uppercase;
            margin-bottom: 12px;
        }

        .wd-kyc-intro h2 {
            color: #fff;
            font-size: 26px;
            line-height: 1.2;
            font-weight: 750;
            margin: 0 0 16px;
        }

        .wd-kyc-intro p {
            color: #d4d4d4;
            font-size: 14px;
            line-height: 1.7;
            margin: 0 0 14px;
            max-width: 850px;
        }

        .wd-kyc-intro ul {
            margin: 18px 0;
            padding: 0;
            list-style: none;
            display: grid;
            gap: 10px;
        }

        .wd-kyc-intro li {
            color: #efefef;
            font-size: 14px;
            line-height: 1.55;
            padding-left: 20px;
            position: relative;
        }

        .wd-kyc-intro li::before {
            content: "";
            width: 6px;
            height: 6px;
            border-radius: 999px;
            background: #F40087;
            position: absolute;
            left: 0;
            top: .58em;
        }

        .wd-kyc-page details {
            background: #fff !important;
            border: 1px solid #e7e3df;
            border-radius: 14px !important;
            box-shadow: none !important;
            margin-bottom: 12px !important;
            overflow: hidden;
            transition: border-color .2s ease, box-shadow .2s ease;
        }

        .wd-kyc-page details[open] {
            border-color: #d8d2cd;
            box-shadow: 0 10px 28px rgba(36,36,36,.05) !important;
        }

        .wd-kyc-page details summary {
            padding: 20px 24px !important;
            font-size: 15px;
            font-weight: 700;
            color: #242424;
            background: #fff;
            list-style-position: outside;
        }

        .wd-kyc-page details[open] summary {
            border-bottom: 1px solid #eeeae6;
        }

        .wd-kyc-page details > div {
            padding: 24px !important;
        }

        .wd-kyc-page label {
            color: #4a4744;
            font-size: 13px;
            font-weight: 600;
        }

        .wd-kyc-page input[type="text"],
        .wd-kyc-page input[type="date"],
        .wd-kyc-page input[type="email"],
        .wd-kyc-page input[type="number"],
        .wd-kyc-page select,
        .wd-kyc-page textarea {
            border: 1px solid #d8d4cf !important;
            border-radius: 9px !important;
            min-height: 44px;
            background: #FAF9F7 !important;
            color: #242424;
            box-shadow: none !important;
        }


        .wd-kyc-two-cols {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 24px 32px;
        }

        .wd-kyc-two-cols select {
            width: 100% !important;
            max-width: none !important;
        }

        /* Champs de sélection : largeur de lecture confortable */
        .wd-kyc-page select {
            width: min(100%, 620px) !important;
            display: block;
        }

        /* Radios & checkbox à la charte Wendee */
        .wd-kyc-page input[type="checkbox"],
        .wd-kyc-page input[type="radio"] {
            accent-color: #F40087 !important;
            color: #F40087 !important;
            border-color: #cfcac5 !important;
            box-shadow: none !important;
        }

        .wd-kyc-page input[type="checkbox"]:focus,
        .wd-kyc-page input[type="radio"]:focus {
            border-color: #F40087 !important;
            box-shadow: 0 0 0 2px rgba(244,0,135,.12) !important;
        }

        .wd-kyc-page input:focus,
        .wd-kyc-page select:focus,
        .wd-kyc-page textarea:focus {
            border-color: #242424 !important;
            outline: none !important;
            box-shadow: 0 0 0 2px rgba(36,36,36,.08) !important;
        }

        .wd-kyc-page button[type="submit"] {
            background: #242424 !important;
            color: #fff !important;
            border-radius: 8px !important;
            padding: 13px 20px !important;
            font-size: 11px !important;
            font-weight: 800 !important;
            letter-spacing: .08em !important;
            box-shadow: none !important;
        }

        .wd-kyc-page button[type="submit"]:hover {
            opacity: .92;
        }

        @media (max-width: 900px) {
            .wd-kyc-intro-grid {
                grid-template-columns: 1fr;
                gap: 28px;
            }

            .wd-kyc-intro-side {
                border-left: 0;
                border-top: 1px solid rgba(255,255,255,.12);
                padding-left: 0;
                padding-top: 24px;
            }
        }

        @media (max-width: 768px) {
            .wd-kyc-two-cols {
                grid-template-columns: 1fr;
                gap: 18px;
            }

            .wd-kyc-page select {
                width: 100% !important;
            }
            .wd-kyc-intro {
                padding: 26px 22px;
                border-radius: 12px;
            }

            .wd-kyc-intro h2 {
                font-size: 22px;
            }

            .wd-kyc-page details summary {
                padding: 18px !important;
            }

            .wd-kyc-page details > div {
                padding: 18px !important;
            }
        }
    </style>

    <div class="py-10 wd-kyc-page">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            <section class="wd-kyc-intro">
                <div class="wd-kyc-intro-grid">

                    <div class="wd-kyc-intro-main">
                        <div class="wd-kyc-eyebrow">Objectifs</div>

                        <h2>Comprendre votre situation pour vous conseiller avec précision.</h2>

                        <p>
                            Ce questionnaire a pour finalité de nous permettre de vous délivrer un conseil adapté à votre situation.
                        </p>

                        <p>
                            La qualité et la pertinence de nos recommandations reposent directement sur les informations que vous nous communiquez.
                        </p>

                        <p>
                            En l’absence d’informations complètes ou fiables, nous ne pourrons pas formuler de recommandations personnalisées.
                        </p>

                        <p style="margin-bottom:0;">
                            La sincérité et la précision de vos réponses sont donc essentielles pour garantir un accompagnement de qualité.
                        </p>
                    </div>

                    <div class="wd-kyc-intro-side">
                        <div class="wd-kyc-side-label">Ce questionnaire permet de</div>

                        <div class="wd-kyc-objective">
                            <span>01</span>
                            <p>Vérifier l’exactitude et l’exhaustivité de votre situation personnelle, professionnelle et patrimoniale.</p>
                        </div>

                        <div class="wd-kyc-objective">
                            <span>02</span>
                            <p>Respecter nos obligations réglementaires dans le cadre de notre mission de conseil.</p>
                        </div>

                        <div class="wd-kyc-objective">
                            <span>03</span>
                            <p>Déterminer les solutions et stratégies cohérentes avec votre profil.</p>
                        </div>
                    </div>

                </div>
            </section>

            @if (session('status'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-800 rounded-lg p-4 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-800 rounded-lg p-4 text-sm">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('tenant.clients.kyc.update', $client) }}" x-data="{
                neEnFrance: '{{ $old('ne_en_france') }}',
                francais: '{{ $old('francais') }}',
                situationFamiliale: '{{ $old('situation_familiale') }}',
                aConjoint: {{ $old('a_conjoint') ? 'true' : 'false' }},
                aPac: {{ $old('a_personnes_a_charge') ? 'true' : 'false' }},
                conjointAjouterProfession: {{ $old('conjoint_ajouter_profession') ? 'true' : 'false' }},
                residenceIdentique: '{{ $old('residence_fiscale_identique') }}',
                estPpe: '{{ $old('est_ppe') }}',
                prochePpe: '{{ $old('proche_ppe') }}',
                pac: {{ $client->personnesACharge->count() ? $client->personnesACharge->map(fn($p) => ['civilite' => $p->civilite, 'prenom' => $p->prenom, 'nom' => $p->nom, 'date_naissance' => $p->date_naissance?->format('Y-m-d'), 'enfant_de' => $p->enfant_de, 'fiscalement_a_charge' => $p->fiscalement_a_charge])->toJson() : '[]' }},
            }">
                @csrf
                @method('PUT')

                {{-- Naissance et nationalité --}}
                <details open class="bg-white shadow-sm sm:rounded-lg mb-4">
                    <summary class="cursor-pointer px-6 py-4 font-semibold text-gray-800">
                        Naissance et nationalité
                    </summary>

                    <div class="p-6 pt-0 space-y-6">

                        {{-- Questions principales --}}
                        <div class="wd-kyc-two-cols">
                            <div>
                                <x-input-label value="Êtes-vous né(e) en France métropolitaine ?" />
                                <select
                                    name="ne_en_france"
                                    x-model="neEnFrance"
                                    class="border-gray-300 rounded-md shadow-sm mt-1 w-full"
                                >
                                    {!! $opts($listes['oui_non'], 'ne_en_france') !!}
                                </select>
                            </div>

                            <div>
                                <x-input-label value="Êtes-vous de nationalité française ?" />
                                <select
                                    name="francais"
                                    x-model="francais"
                                    class="border-gray-300 rounded-md shadow-sm mt-1 w-full"
                                >
                                    {!! $opts($listes['oui_non'], 'francais') !!}
                                </select>
                            </div>
                        </div>

                        {{-- Né(e) en France --}}
                        <div
                            x-show="neEnFrance === 'oui'"
                            class="wd-kyc-two-cols"
                        >
                            <div
                                class="relative"
                                x-data="addressAutocomplete('municipality', @js($old('commune_naissance') ?? ''))"
                            >
                                <x-input-label
                                    for="commune_naissance"
                                    value="Commune de naissance"
                                />

                                <input
                                    id="commune_naissance"
                                    name="commune_naissance"
                                    type="text"
                                    autocomplete="off"
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full"
                                    x-model="query"
                                    @input.debounce.300ms="search()"
                                >

                                <ul
                                    x-show="open"
                                    @click.outside="open = false"
                                    class="absolute z-10 bg-white border border-gray-200 rounded-md shadow-md mt-1 w-full max-h-56 overflow-y-auto text-sm"
                                >
                                    <template x-for="f in results" :key="f.properties.id">
                                        <li
                                            @click="select(f, (p) => {
                                                query = p.city;
                                                document.getElementById('code_postal_naissance').value = p.postcode;
                                            })"
                                            class="px-3 py-2 hover:bg-gray-100 cursor-pointer"
                                            x-text="f.properties.label"
                                        ></li>
                                    </template>
                                </ul>
                            </div>

                            <div>
                                <x-input-label
                                    for="code_postal_naissance"
                                    value="Code postal de naissance"
                                />

                                <x-text-input
                                    id="code_postal_naissance"
                                    name="code_postal_naissance"
                                    type="text"
                                    class="block mt-1 w-full"
                                    :value="$old('code_postal_naissance')"
                                />
                            </div>
                        </div>

                        {{-- Né(e) hors de France --}}
                        <div x-show="neEnFrance === 'non'">
                            <x-input-label value="Pays de naissance" />
                            <select
                                name="pays_naissance"
                                class="border-gray-300 rounded-md shadow-sm mt-1 w-full"
                            >
                                {!! $opts($listes['pays_hors_france'], 'pays_naissance') !!}
                            </select>
                        </div>

                        {{-- Nationalité étrangère --}}
                        <div x-show="francais === 'non'">
                            <x-input-label value="Autre nationalité" />
                            <select
                                name="autre_nationalite"
                                class="border-gray-300 rounded-md shadow-sm mt-1 w-full"
                            >
                                {!! $opts($listes['nationalites'], 'autre_nationalite') !!}
                            </select>
                        </div>

                    </div>
                </details>

                {{-- Classification --}}
                <details class="bg-white shadow-sm sm:rounded-lg mb-4">
                    <summary class="cursor-pointer px-6 py-4 font-semibold text-gray-800">
                        Classification et capacité juridique
                    </summary>

                    <div class="p-6 pt-0">
                        <div class="wd-kyc-two-cols">

                            <div>
                                <x-input-label value="Classification client MIF" />
                                <select
                                    name="classification_mif"
                                    class="border-gray-300 rounded-md shadow-sm mt-1 w-full"
                                >
                                    {!! $opts($listes['classification_mif'], 'classification_mif') !!}
                                </select>
                            </div>

                            <div>
                                <x-input-label value="Capacité juridique" />
                                <select
                                    name="capacite_juridique"
                                    class="border-gray-300 rounded-md shadow-sm mt-1 w-full"
                                >
                                    {!! $opts($listes['capacite_juridique'], 'capacite_juridique') !!}
                                </select>
                            </div>

                        </div>
                    </div>
                </details>

                {{-- Situation familiale --}}
                <details class="bg-white shadow-sm sm:rounded-lg mb-4">
                    <summary class="cursor-pointer px-6 py-4 font-semibold text-gray-800">Situation familiale</summary>
                    <div class="p-6 pt-0 space-y-4">
                        <div>
                            <x-input-label value="Situation familiale" />
                            <select name="situation_familiale" x-model="situationFamiliale" class="border-gray-300 rounded-md shadow-sm mt-1 w-full">
                                {!! $opts($listes['situation_familiale'], 'situation_familiale') !!}
                            </select>
                        </div>
                        <div x-show="situationFamiliale === 'marie'" class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="date_mariage" value="Date du mariage" />
                                <x-text-input id="date_mariage" name="date_mariage" type="date" class="block mt-1 w-full" max="{{ now()->subDay()->format('Y-m-d') }}" :value="$old('date_mariage')" />
                            </div>
                            <div>
                                <x-input-label for="lieu_mariage" value="Lieu du mariage" />
                                <x-text-input id="lieu_mariage" name="lieu_mariage" type="text" class="block mt-1 w-full" :value="$old('lieu_mariage')" />
                            </div>
                            <div class="col-span-2">
                                <x-input-label value="Régime matrimonial" />
                                <select name="regime_matrimonial" class="border-gray-300 rounded-md shadow-sm mt-1 w-full">
                                    {!! $opts($listes['regime_matrimonial'], 'regime_matrimonial') !!}
                                </select>
                            </div>
                            <div>
                                <x-input-label value="Donation au dernier vivant à votre profit" />
                                <select name="donation_dernier_vivant_profit" class="border-gray-300 rounded-md shadow-sm mt-1 w-full">
                                    {!! $opts($listes['oui_non'], 'donation_dernier_vivant_profit') !!}
                                </select>
                            </div>
                            <div>
                                <x-input-label value="Donation au dernier vivant au profit du conjoint" />
                                <select name="donation_dernier_vivant_conjoint" class="border-gray-300 rounded-md shadow-sm mt-1 w-full">
                                    {!! $opts($listes['oui_non'], 'donation_dernier_vivant_conjoint') !!}
                                </select>
                            </div>
                        </div>
                        <div x-show="situationFamiliale === 'pacse'" class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="date_pacs" value="Date du PACS" />
                                <x-text-input id="date_pacs" name="date_pacs" type="date" class="block mt-1 w-full" max="{{ now()->subDay()->format('Y-m-d') }}" :value="$old('date_pacs')" />
                            </div>
                            <div>
                                <x-input-label for="lieu_pacs" value="Lieu du PACS" />
                                <x-text-input id="lieu_pacs" name="lieu_pacs" type="text" class="block mt-1 w-full" :value="$old('lieu_pacs')" />
                            </div>
                            <div class="col-span-2">
                                <x-input-label value="Convention de PACS" />
                                <select name="convention_pacs" class="border-gray-300 rounded-md shadow-sm mt-1 w-full">
                                    {!! $opts($listes['convention_pacs'], 'convention_pacs') !!}
                                </select>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <input type="hidden" name="a_conjoint" value="0">
                            <input type="checkbox" id="a_conjoint" name="a_conjoint" value="1" x-model="aConjoint" class="rounded border-gray-300">
                            <label for="a_conjoint" class="ms-2 text-sm text-gray-700">Ajouter le conjoint / partenaire</label>
                        </div>
                        <div x-show="aConjoint" class="grid grid-cols-2 gap-4 border-t pt-4">
                            <div>
                                <x-input-label value="Civilité (conjoint)" />
                                <select name="conjoint_civilite" class="border-gray-300 rounded-md shadow-sm mt-1 w-full">
                                    {!! $opts($listes['civilite_conjoint'], 'conjoint_civilite') !!}
                                </select>
                            </div>
                            <div>
                                <x-input-label for="conjoint_prenom" value="Prénom (conjoint)" />
                                <x-text-input id="conjoint_prenom" name="conjoint_prenom" type="text" class="block mt-1 w-full" :value="$old('conjoint_prenom')" />
                            </div>
                            <div>
                                <x-input-label for="conjoint_nom" value="Nom (conjoint)" />
                                <x-text-input id="conjoint_nom" name="conjoint_nom" type="text" class="block mt-1 w-full" :value="$old('conjoint_nom')" />
                            </div>
                            <div>
                                <x-input-label for="conjoint_nom_naissance" value="Nom de naissance" />
                                <x-text-input id="conjoint_nom_naissance" name="conjoint_nom_naissance" type="text" class="block mt-1 w-full" :value="$old('conjoint_nom_naissance')" />
                            </div>
                            <div>
                                <x-input-label for="conjoint_date_naissance" value="Date de naissance (conjoint)" />
                                <x-text-input id="conjoint_date_naissance" name="conjoint_date_naissance" type="date" class="block mt-1 w-full" max="{{ now()->subYears(18)->format('Y-m-d') }}" :value="$old('conjoint_date_naissance')" />
                            </div>
                        </div>

                        <div class="flex items-center border-t pt-4">
                            <input type="hidden" name="a_personnes_a_charge" value="0">
                            <input type="checkbox" id="a_personnes_a_charge" name="a_personnes_a_charge" value="1" x-model="aPac" class="rounded border-gray-300">
                            <label for="a_personnes_a_charge" class="ms-2 text-sm text-gray-700">Personnes à charge</label>
                        </div>
                        <div x-show="aPac" class="space-y-4">
                            <template x-for="(p, i) in pac" :key="i">
                                <div class="grid grid-cols-7 gap-2 items-end border rounded-md p-3">
                                    <div class="col-span-1">
                                        <label class="text-xs text-gray-500">Civilité</label>
                                        <select :name="`personnes_a_charge[${i}][civilite]`" x-model="p.civilite" class="border-gray-300 rounded-md shadow-sm mt-1 w-full text-sm">
                                            {!! $opts($listes['civilite_personne_charge'], '') !!}
                                        </select>
                                    </div>
                                    <div class="col-span-1">
                                        <label class="text-xs text-gray-500">Prénom</label>
                                        <input type="text" :name="`personnes_a_charge[${i}][prenom]`" x-model="p.prenom" class="border-gray-300 rounded-md shadow-sm mt-1 w-full text-sm">
                                    </div>
                                    <div class="col-span-1">
                                        <label class="text-xs text-gray-500">Nom</label>
                                        <input type="text" :name="`personnes_a_charge[${i}][nom]`" x-model="p.nom" class="border-gray-300 rounded-md shadow-sm mt-1 w-full text-sm">
                                    </div>
                                    <div class="col-span-1">
                                        <label class="text-xs text-gray-500">Naissance</label>
                                        <input type="date" :name="`personnes_a_charge[${i}][date_naissance]`" x-model="p.date_naissance" class="border-gray-300 rounded-md shadow-sm mt-1 w-full text-sm">
                                    </div>
                                    <div class="col-span-1">
                                        <label class="text-xs text-gray-500">Enfant de</label>
                                        <select :name="`personnes_a_charge[${i}][enfant_de]`" x-model="p.enfant_de" class="border-gray-300 rounded-md shadow-sm mt-1 w-full text-sm">
                                            <option value="">-</option>
                                            <option value="client">Client</option>
                                            <option value="conjoint">Conjoint</option>
                                            <option value="commun">Commun</option>
                                        </select>
                                    </div>
                                    <div class="col-span-1">
                                        <label class="text-xs text-gray-500">Fiscalement à charge</label>
                                        <select :name="`personnes_a_charge[${i}][fiscalement_a_charge]`" x-model="p.fiscalement_a_charge" class="border-gray-300 rounded-md shadow-sm mt-1 w-full text-sm">
                                            {!! $opts($listes['oui_non'], '') !!}
                                        </select>
                                    </div>
                                    <div class="col-span-1">
                                        <button type="button" @click="pac.splice(i, 1)" class="text-red-600 text-sm underline">Retirer</button>
                                    </div>
                                </div>
                            </template>
                            <button type="button" @click="pac.push({civilite:'',prenom:'',nom:'',date_naissance:'',enfant_de:'',fiscalement_a_charge:''})" class="text-sm text-gray-700 underline">
                                + Ajouter une personne à charge
                            </button>
                        </div>
                    </div>
                </details>

                {{-- Professionnel --}}
                <details class="bg-white shadow-sm sm:rounded-lg mb-4">
                    <summary class="cursor-pointer px-6 py-4 font-semibold text-gray-800">Situation professionnelle</summary>
                    <div class="p-6 pt-0 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label value="Statut" />
                                <select name="statut_professionnel" class="border-gray-300 rounded-md shadow-sm mt-1 w-full">
                                    {!! $opts($listes['statut_professionnel'], 'statut_professionnel') !!}
                                </select>
                            </div>
                            <div>
                                <x-input-label value="Profession actuelle (CSP)" />
                                <select name="csp" class="border-gray-300 rounded-md shadow-sm mt-1 w-full">
                                    {!! $opts($listes['csp'], 'csp') !!}
                                </select>
                            </div>
                            <div>
                                <x-input-label for="societe_employeur" value="Société, employeur" />
                                <x-text-input id="societe_employeur" name="societe_employeur" type="text" class="block mt-1 w-full" :value="$old('societe_employeur')" />
                            </div>
                            <div>
                                <x-input-label for="date_entree_entreprise" value="Dans l'entreprise depuis le" />
                                <x-text-input id="date_entree_entreprise" name="date_entree_entreprise" type="date" class="block mt-1 w-full" :value="$old('date_entree_entreprise')" />
                            </div>
                            <div>
                                <x-input-label for="profession_libelle" value="Profession (libellé)" />
                                <x-text-input id="profession_libelle" name="profession_libelle" type="text" class="block mt-1 w-full" :value="$old('profession_libelle')" />
                            </div>
                            <div>
                                <x-input-label value="Code NAF" />
                                <select name="code_naf" class="border-gray-300 rounded-md shadow-sm mt-1 w-full">
                                    {!! $opts($listes['code_naf'], 'code_naf') !!}
                                </select>
                            </div>
                            <div>
                                <x-input-label for="age_depart_retraite" value="Départ en retraite prévu à l'âge de" />
                                <x-text-input id="age_depart_retraite" name="age_depart_retraite" type="number" class="block mt-1 w-full" :value="$old('age_depart_retraite')" />
                            </div>
                            <div>
                                <x-input-label for="siret_employeur" value="SIRET" />
                                <x-text-input id="siret_employeur" name="siret_employeur" type="text" class="block mt-1 w-full" :value="$old('siret_employeur')" />
                            </div>
                        </div>

                        <div class="flex items-center border-t pt-4">
                            <input type="hidden" name="conjoint_ajouter_profession" value="0">
                            <input type="checkbox" id="conjoint_ajouter_profession" name="conjoint_ajouter_profession" value="1" x-model="conjointAjouterProfession" class="rounded border-gray-300">
                            <label for="conjoint_ajouter_profession" class="ms-2 text-sm text-gray-700">Ajouter la profession du conjoint</label>
                        </div>
                        <div x-show="conjointAjouterProfession" class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label value="Statut (conjoint)" />
                                <select name="conjoint_statut_professionnel" class="border-gray-300 rounded-md shadow-sm mt-1 w-full">
                                    {!! $opts($listes['statut_professionnel'], 'conjoint_statut_professionnel') !!}
                                </select>
                            </div>
                            <div>
                                <x-input-label value="Profession actuelle (CSP) (conjoint)" />
                                <select name="conjoint_csp" class="border-gray-300 rounded-md shadow-sm mt-1 w-full">
                                    {!! $opts($listes['csp'], 'conjoint_csp') !!}
                                </select>
                            </div>
                            <div>
                                <x-input-label for="conjoint_societe_employeur" value="Société, employeur (conjoint)" />
                                <x-text-input id="conjoint_societe_employeur" name="conjoint_societe_employeur" type="text" class="block mt-1 w-full" :value="$old('conjoint_societe_employeur')" />
                            </div>
                            <div>
                                <x-input-label for="conjoint_date_entree_entreprise" value="Dans l'entreprise depuis le (conjoint)" />
                                <x-text-input id="conjoint_date_entree_entreprise" name="conjoint_date_entree_entreprise" type="date" class="block mt-1 w-full" :value="$old('conjoint_date_entree_entreprise')" />
                            </div>
                            <div>
                                <x-input-label for="conjoint_profession_libelle" value="Profession (libellé) (conjoint)" />
                                <x-text-input id="conjoint_profession_libelle" name="conjoint_profession_libelle" type="text" class="block mt-1 w-full" :value="$old('conjoint_profession_libelle')" />
                            </div>
                            <div>
                                <x-input-label value="Code NAF (conjoint)" />
                                <select name="conjoint_code_naf" class="border-gray-300 rounded-md shadow-sm mt-1 w-full">
                                    {!! $opts($listes['code_naf'], 'conjoint_code_naf') !!}
                                </select>
                            </div>
                            <div>
                                <x-input-label for="conjoint_age_depart_retraite" value="Départ en retraite prévu à l'âge de (conjoint)" />
                                <x-text-input id="conjoint_age_depart_retraite" name="conjoint_age_depart_retraite" type="number" class="block mt-1 w-full" :value="$old('conjoint_age_depart_retraite')" />
                            </div>
                            <div>
                                <x-input-label for="conjoint_siret_employeur" value="SIRET (conjoint)" />
                                <x-text-input id="conjoint_siret_employeur" name="conjoint_siret_employeur" type="text" class="block mt-1 w-full" :value="$old('conjoint_siret_employeur')" />
                            </div>
                        </div>
                    </div>
                </details>

                {{-- Résidence fiscale --}}
                <details class="bg-white shadow-sm sm:rounded-lg mb-4">
                    <summary class="cursor-pointer px-6 py-4 font-semibold text-gray-800">Résidence fiscale</summary>
                    <div class="p-6 pt-0 space-y-4">
                        <div>
                            <x-input-label value="Votre adresse de résidence fiscale est-elle identique à votre adresse principale ?" />
                            <select name="residence_fiscale_identique" x-model="residenceIdentique" class="border-gray-300 rounded-md shadow-sm mt-1 w-full">
                                {!! $opts($listes['oui_non'], 'residence_fiscale_identique') !!}
                            </select>
                        </div>
                        <div x-show="residenceIdentique === 'non'">
                            <x-input-label value="Autre pays de résidence fiscale" />
                            <select name="autre_pays_residence_fiscale" class="border-gray-300 rounded-md shadow-sm mt-1 w-full">
                                {!! $opts($listes['pays_hors_france'], 'autre_pays_residence_fiscale') !!}
                            </select>
                        </div>
                        <div>
                            <x-input-label value="Êtes-vous hébergé par une tierce personne ?" />
                            <select name="heberge_par_tiers" class="border-gray-300 rounded-md shadow-sm mt-1 w-full">
                                {!! $opts($listes['oui_non_bis'], 'heberge_par_tiers') !!}
                            </select>
                        </div>
                    </div>
                </details>

                {{-- PPE --}}
                <details class="bg-white shadow-sm sm:rounded-lg mb-4">
                    <summary class="cursor-pointer px-6 py-4 font-semibold text-gray-800">Personne Politiquement Exposée (PPE)</summary>
                    <div class="p-6 pt-0 space-y-4">
                        <div>
                            <x-input-label value="Êtes-vous une Personne Politiquement Exposée (PPE) ?" />
                            <select name="est_ppe" x-model="estPpe" class="border-gray-300 rounded-md shadow-sm mt-1 w-full">
                                {!! $opts($listes['oui_non_ter'], 'est_ppe') !!}
                            </select>
                        </div>
                        <div x-show="estPpe === 'oui_ppe'" class="grid grid-cols-2 gap-4 border-t pt-4">
                            <div class="col-span-2">
                                <x-input-label value="Exercice sur les 12 derniers mois" />
                                <select name="ppe_exercice_12_mois" class="border-gray-300 rounded-md shadow-sm mt-1 w-full">
                                    {!! $opts($listes['en_activite_ppe'], 'ppe_exercice_12_mois') !!}
                                </select>
                            </div>
                            <div class="col-span-2">
                                <x-input-label value="Fonction exercée" />
                                <select name="ppe_fonction" class="border-gray-300 rounded-md shadow-sm mt-1 w-full">
                                    {!! $opts($listes['exercice_ppe'], 'ppe_fonction') !!}
                                </select>
                            </div>
                            <div>
                                <x-input-label for="ppe_date_debut" value="Date de début" />
                                <x-text-input id="ppe_date_debut" name="ppe_date_debut" type="date" class="block mt-1 w-full" :value="$old('ppe_date_debut')" />
                            </div>
                            <div>
                                <x-input-label for="ppe_date_fin" value="Date de fin" />
                                <x-text-input id="ppe_date_fin" name="ppe_date_fin" type="date" class="block mt-1 w-full" :value="$old('ppe_date_fin')" />
                            </div>
                            <div class="col-span-2">
                                <x-input-label value="Pays" />
                                <select name="ppe_pays" class="border-gray-300 rounded-md shadow-sm mt-1 w-full">
                                    {!! $opts($listes['pays'], 'ppe_pays') !!}
                                </select>
                            </div>
                        </div>

                        <div class="border-t pt-4">
                            <x-input-label value="Êtes-vous proche d'une Personne Politiquement Exposée ?" />
                            <select name="proche_ppe" x-model="prochePpe" class="border-gray-300 rounded-md shadow-sm mt-1 w-full">
                                {!! $opts($listes['oui_non_quater'], 'proche_ppe') !!}
                            </select>
                        </div>
                        <div x-show="prochePpe === 'oui_proche_ppe'" class="grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <x-input-label value="Exercice sur les 12 derniers mois" />
                                <select name="proche_ppe_exercice_12_mois" class="border-gray-300 rounded-md shadow-sm mt-1 w-full">
                                    {!! $opts($listes['en_activite_proche_ppe'], 'proche_ppe_exercice_12_mois') !!}
                                </select>
                            </div>
                            <div class="col-span-2">
                                <x-input-label value="Fonction exercée" />
                                <select name="proche_ppe_fonction" class="border-gray-300 rounded-md shadow-sm mt-1 w-full">
                                    {!! $opts($listes['exercice_ppe'], 'proche_ppe_fonction') !!}
                                </select>
                            </div>
                            <div>
                                <x-input-label for="proche_ppe_nom" value="Nom de la personne liée" />
                                <x-text-input id="proche_ppe_nom" name="proche_ppe_nom" type="text" class="block mt-1 w-full" :value="$old('proche_ppe_nom')" />
                            </div>
                            <div>
                                <x-input-label for="proche_ppe_prenom" value="Prénom de la personne liée" />
                                <x-text-input id="proche_ppe_prenom" name="proche_ppe_prenom" type="text" class="block mt-1 w-full" :value="$old('proche_ppe_prenom')" />
                            </div>
                            <div class="col-span-2">
                                <x-input-label value="Nature du lien" />
                                <select name="proche_ppe_nature_lien" class="border-gray-300 rounded-md shadow-sm mt-1 w-full">
                                    {!! $opts($listes['nature_du_lien'], 'proche_ppe_nature_lien') !!}
                                </select>
                            </div>
                            <div>
                                <x-input-label for="proche_ppe_date_debut" value="Date de début" />
                                <x-text-input id="proche_ppe_date_debut" name="proche_ppe_date_debut" type="date" class="block mt-1 w-full" :value="$old('proche_ppe_date_debut')" />
                            </div>
                            <div>
                                <x-input-label for="proche_ppe_date_fin" value="Date de fin" />
                                <x-text-input id="proche_ppe_date_fin" name="proche_ppe_date_fin" type="date" class="block mt-1 w-full" :value="$old('proche_ppe_date_fin')" />
                            </div>
                            <div class="col-span-2">
                                <x-input-label value="Pays" />
                                <select name="proche_ppe_pays" class="border-gray-300 rounded-md shadow-sm mt-1 w-full">
                                    {!! $opts($listes['pays'], 'proche_ppe_pays') !!}
                                </select>
                            </div>
                        </div>
                    </div>
                </details>

                {{-- Signature --}}
                <details class="bg-white shadow-sm sm:rounded-lg mb-4">
                    <summary class="cursor-pointer px-6 py-4 font-semibold text-gray-800">Signature</summary>
                    <div class="p-6 pt-0 space-y-4">
                        <div>
                            <x-input-label for="lieu_signature" value="Fait à" />
                            <x-text-input id="lieu_signature" name="lieu_signature" type="text" class="block mt-1 w-full" :value="$old('lieu_signature')" />
                        </div>
                        @if ($verificationRequise)
                            <div>
                                <x-input-label for="code_de_verification_client" value="Code de vérification client" />
                                <x-text-input id="code_de_verification_client" name="code_de_verification_client" type="text" class="block mt-1 w-full" :value="$old('code_de_verification_client')" />
                                <p class="text-sm text-gray-500 mt-2">
                                    Un code de vérification a été envoyé au client pour valider ce premier recueil.
                                </p>
                            </div>
                        @endif
                        <div class="flex items-center">
                            <input type="hidden" name="accepte_cgu" value="0">
                            <input type="checkbox" id="accepte_cgu" name="accepte_cgu" value="1" {{ $old('accepte_cgu') ? 'checked' : '' }} class="rounded border-gray-300">
                            <label for="accepte_cgu" class="ms-2 text-sm text-gray-700">Le client accepte les conditions générales d'utilisation et la politique de confidentialité.</label>
                        </div>
                    </div>
                </details>

                <div class="flex justify-end mt-6">
                    <x-primary-button>
                        {{ __('Enregistrer le recueil KYC') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-tenant-app-layout>
