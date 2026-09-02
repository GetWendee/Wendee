<x-tenant-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Patrimoine - {{ $client->prenom }} {{ $client->nom }}
            </h2>
            <a href="{{ route('tenant.clients.show', $client) }}" class="text-sm text-gray-600 underline">
                {{ __('Retour à la fiche client') }}
            </a>
        </div>
    </x-slot>

    <style>
        .wd-patrimoine-page {
            color: #242424;
        }

        .wd-patrimoine-intro {
            background: #242424;
            border-radius: 16px;
            padding: 34px 38px;
            margin-bottom: 28px;
            position: relative;
            overflow: hidden;
        }

        .wd-patrimoine-intro::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: #F40087;
        }

        .wd-patrimoine-intro-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.1fr) minmax(340px, .9fr);
            gap: 52px;
        }

        .wd-patrimoine-intro-side {
            border-left: 1px solid rgba(255,255,255,.12);
            padding-left: 38px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .wd-patrimoine-eyebrow {
            color: #F40087;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .18em;
            text-transform: uppercase;
            margin-bottom: 12px;
        }

        .wd-patrimoine-intro h2 {
            color: #fff;
            font-size: 26px;
            line-height: 1.2;
            font-weight: 750;
            margin: 0 0 16px;
        }

        .wd-patrimoine-intro p {
            color: #d4d4d4;
            font-size: 14px;
            line-height: 1.7;
            margin: 0 0 14px;
        }

        .wd-patrimoine-side-label {
            color: #a9a9a9;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .16em;
            text-transform: uppercase;
            margin-bottom: 18px;
        }

        .wd-patrimoine-objective {
            display: grid;
            grid-template-columns: 34px 1fr;
            gap: 14px;
            padding: 15px 0;
            border-top: 1px solid rgba(255,255,255,.10);
        }

        .wd-patrimoine-objective:last-child {
            border-bottom: 1px solid rgba(255,255,255,.10);
        }

        .wd-patrimoine-objective span {
            color: #F40087;
            font-size: 11px;
            font-weight: 800;
        }

        .wd-patrimoine-objective p {
            margin: 0 !important;
            color: #f1f1f1;
            font-size: 13px;
            line-height: 1.55;
        }

        .wd-patrimoine-page .bg-white {
            border: 1px solid #e7e3df;
            border-radius: 14px !important;
            box-shadow: none !important;
        }

        .wd-patrimoine-page input[type="text"],
        .wd-patrimoine-page input[type="number"],
        .wd-patrimoine-page input[type="date"],
        .wd-patrimoine-page select,
        .wd-patrimoine-page textarea {
            border: 1px solid #d8d4cf !important;
            border-radius: 9px !important;
            min-height: 44px;
            background: #FAF9F7 !important;
            color: #242424;
            box-shadow: none !important;
        }

        .wd-patrimoine-page select {
            width: min(100%, 620px) !important;
        }

        .wd-patrimoine-page input:focus,
        .wd-patrimoine-page select:focus,
        .wd-patrimoine-page textarea:focus {
            border-color: #242424 !important;
            box-shadow: 0 0 0 2px rgba(36,36,36,.08) !important;
        }

        .wd-patrimoine-page input[type="checkbox"],
        .wd-patrimoine-page input[type="radio"] {
            accent-color: #F40087 !important;
            color: #F40087 !important;
        }




        /* Respiration intérieure des blocs patrimoine */
        .wd-patrimoine-page details > summary {
            padding: 22px 28px !important;
        }

        .wd-patrimoine-page details > div {
            padding: 10px 28px 28px !important;
        }

        .wd-patrimoine-entry {
            max-width: none;
            margin: 0;
            padding: 18px 0 8px;
            border: 0;
            border-radius: 0;
            background: transparent;
        }

        .wd-patrimoine-entry-top {
            display: grid;
            grid-template-columns: 1.05fr 1.15fr .8fr;
            gap: 24px;
            align-items: end;
        }

        .wd-patrimoine-entry label,
        .wd-patrimoine-detention-title {
            display: block;
            font-size: 11px;
            font-weight: 700;
            color: #6f6964;
            margin-bottom: 7px;
        }

        .wd-patrimoine-entry input[type="text"],
        .wd-patrimoine-entry input[type="number"] {
            width: 100%;
            background: #fff !important;
        }

        .wd-patrimoine-detention {
            margin-top: 18px;
            padding-top: 16px;
            border-top: 1px solid #eeeae6;
        }

        .wd-patrimoine-radio-group {
            display: flex;
            flex-wrap: wrap;
            gap: 16px 30px;
            margin-top: 10px;
        }

        .wd-patrimoine-radio-group label {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            margin: 0;
            font-size: 13px;
            font-weight: 500;
            color: #3d3936;
            cursor: pointer;
        }

        .wd-patrimoine-radio-group input[type="radio"] {
            width: 17px;
            height: 17px;
            accent-color: #F40087 !important;
            color: #F40087 !important;
        }

        .wd-patrimoine-entry-footer {
            display: flex;
            justify-content: flex-end;
            margin-top: 10px;
        }

        .wd-patrimoine-remove {
            background: transparent !important;
            color: #9a3f46 !important;
            padding: 0 !important;
            font-size: 12px !important;
            font-weight: 600 !important;
            text-decoration: none !important;
        }

        .wd-patrimoine-add {
            display: inline-flex;
            align-items: center;
            margin-top: 6px;
            width: auto;
            text-align: left;
            background: transparent !important;
            color: #242424 !important;
            padding: 6px 0 !important;
            font-size: 12px !important;
            font-weight: 700 !important;
            text-decoration: none !important;
        }

        @media (max-width: 900px) {
            .wd-patrimoine-entry-top {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 650px) {
            .wd-patrimoine-entry {
                padding: 20px;
            }

            .wd-patrimoine-entry-top {
                grid-template-columns: 1fr;
                gap: 18px;
            }

            .wd-patrimoine-radio-group {
                flex-direction: column;
                gap: 12px;
            }
        }

        .wd-patrimoine-page button {
            border-radius: 8px !important;
        }

        .wd-patrimoine-page button[type="submit"] {
            background: #242424 !important;
            color: #fff !important;
            box-shadow: none !important;
        }

        @media (max-width: 900px) {
            .wd-patrimoine-intro-grid {
                grid-template-columns: 1fr;
                gap: 28px;
            }

            .wd-patrimoine-intro-side {
                border-left: 0;
                border-top: 1px solid rgba(255,255,255,.12);
                padding-left: 0;
                padding-top: 24px;
            }
        }

        @media (max-width: 768px) {
            .wd-patrimoine-intro {
                padding: 26px 22px;
            }

            .wd-patrimoine-page select {
                width: 100% !important;
            }
        }


/* ============================================================
   RADIOS PATRIMOINE
   ============================================================ */

.wd-patrimoine-radios{
    display:flex;
    flex-wrap:wrap;
    align-items:center;
    gap:10px 26px;
    margin-top:2px;
}

.wd-patrimoine-radio{
    display:inline-flex;
    align-items:center;
    gap:8px;
    cursor:pointer;
    color:#3f3a37;
    font-size:13px;
    line-height:1.2;
    white-space:nowrap;
}

.wd-patrimoine-radio input[type="radio"]{
    appearance:none;
    -webkit-appearance:none;
    width:16px;
    height:16px;
    flex:0 0 16px;
    margin:0;
    border:1.5px solid #b9b2ad;
    border-radius:50%;
    background:#fff;
    display:grid;
    place-content:center;
    cursor:pointer;
}

.wd-patrimoine-radio input[type="radio"]::before{
    content:"";
    width:8px;
    height:8px;
    border-radius:50%;
    background:#F40087;
    transform:scale(0);
    transition:transform .12s ease;
}

.wd-patrimoine-radio input[type="radio"]:checked{
    border-color:#F40087;
}

.wd-patrimoine-radio input[type="radio"]:checked::before{
    transform:scale(1);
}

.wd-patrimoine-radio:hover input[type="radio"]{
    border-color:#F40087;
}

.wd-patrimoine-section-title {
    font-size: 15px;
    font-weight: 700;
    color: #242424;
    margin-bottom: 4px;
}

.wd-patrimoine-section-intro {
    font-size: 12px;
    color: #6f6964;
    margin-bottom: 18px;
}

</style>

    <div class="py-10 wd-patrimoine-page">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            <section class="wd-patrimoine-intro">
                <div class="wd-patrimoine-intro-grid">

                    <div>
                        <div class="wd-patrimoine-eyebrow">Objectifs</div>

                        <h2>Comprendre la structure et l'organisation de votre patrimoine.</h2>

                        <p>
                            Ce questionnaire nous permet de comprendre l'organisation de votre patrimoine et la manière dont vos actifs sont détenus.
                        </p>

                        <p style="margin-bottom:0;">
                            Pour vous accompagner efficacement, il est essentiel de fournir des informations complètes et sincères.
                        </p>
                    </div>

                    <div class="wd-patrimoine-intro-side">
                        <div class="wd-patrimoine-side-label">Ce questionnaire nous aide à</div>

                        <div class="wd-patrimoine-objective">
                            <span>01</span>
                            <p>Analyser la composition de votre foyer sur les plans civil, juridique et fiscal.</p>
                        </div>

                        <div class="wd-patrimoine-objective">
                            <span>02</span>
                            <p>Identifier le mode de détention de vos actifs : bien propre, bien commun, indivision ou démembrement.</p>
                        </div>

                        <div class="wd-patrimoine-objective">
                            <span>03</span>
                            <p>Comprendre la répartition de vos avoirs entre les différentes classes d'actifs.</p>
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

            <script type="application/json" id="patrimoine-data">{!! json_encode($elements, JSON_HEX_TAG) !!}</script>
            <form method="POST" action="{{ route('tenant.clients.patrimoine.update', $client) }}" x-data="patrimoineForm(JSON.parse(document.getElementById('patrimoine-data').textContent))">
                @csrf
                @method('PUT')

                <div class="bg-white shadow-sm sm:rounded-lg mb-6 p-6">
                    <h3 class="font-semibold text-gray-800 mb-4">Synthèse</h3>
                    <dl class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <dt class="text-gray-500">Total actifs</dt>
                            <dd class="text-gray-900 font-semibold" x-text="eur(totalActifs)"></dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Total passifs</dt>
                            <dd class="text-gray-900 font-semibold" x-text="eur(totalPassifs)"></dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Solde net</dt>
                            <dd class="font-semibold" :class="solde >= 0 ? 'text-green-700' : 'text-red-700'" x-text="eur(solde)"></dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Revenus</dt>
                            <dd class="text-gray-900 font-semibold" x-text="eur(totalRevenus)"></dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Charges</dt>
                            <dd class="text-gray-900 font-semibold" x-text="eur(totalCharges)"></dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Reste à vivre</dt>
                            <dd class="font-semibold" :class="resteAVivre >= 0 ? 'text-green-700' : 'text-red-700'" x-text="eur(resteAVivre)"></dd>
                        </div>
                    </dl>
                </div>

                <x-patrimoine-bloc categorie="actif_financier" label="Actifs financiers" :mode-detention="true" :type-detention="true" />
                <x-patrimoine-bloc categorie="actif_non_financier" label="Actifs non financiers" :mode-detention="true" :type-detention="true" />
                <x-patrimoine-bloc categorie="passif" label="Passifs" :foyer-avec-conjoint="$foyerAvecConjoint" />
                <x-patrimoine-bloc categorie="revenu" label="Revenus" :type-detention="true" detention-label="Origine du revenu" detention-propre-label="Vous" :periodicite-montant="true" />
                <x-patrimoine-bloc categorie="charge" label="Charges" :type-detention="true" detention-label="Origine de la charge" detention-propre-label="Vous" :periodicite-montant="true" />

                <div class="bg-white shadow-sm sm:rounded-lg mb-6 p-6"
                     x-data="{
                        residentFiscal: '{{ old('resident_fiscal_francais', $fiscalite?->resident_fiscal_francais ?? '') }}',
                        connaitTmiIr: '{{ old('connait_tmi_ir', $fiscalite?->connait_tmi_ir ?? '') }}',
                        imposeIfi: '{{ old('impose_ifi', $fiscalite?->impose_ifi ?? '') }}',
                        connaitTmiIfi: '{{ old('connait_tmi_ifi', $fiscalite?->connait_tmi_ifi ?? '') }}'
                     }">
                    <div class="wd-patrimoine-section-title">Fiscalité</div>
                    <div class="wd-patrimoine-section-intro">Remplissez les éléments-clés de votre environnement fiscal.</div>

                    <div class="mb-5">
                        <label class="block text-xs font-semibold text-gray-600 mb-2">Êtes-vous résident fiscal français ?</label>
                        <div class="wd-patrimoine-radios">
                            <label class="wd-patrimoine-radio"><input type="radio" name="resident_fiscal_francais" value="oui" x-model="residentFiscal"><span>Oui</span></label>
                            <label class="wd-patrimoine-radio"><input type="radio" name="resident_fiscal_francais" value="non" x-model="residentFiscal"><span>Non</span></label>
                        </div>
                    </div>

                    <div class="text-xs font-semibold text-gray-600 mb-3">Impôt sur le revenu (IRPP)</div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-5">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-2">Impôt sur le revenu (IRPP)</label>
                            <input type="number" step="0.01" name="irpp_montant" value="{{ old('irpp_montant', $fiscalite?->irpp_montant ?? '') }}" class="border-gray-300 rounded-md shadow-sm w-full text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-2">Nombre de parts</label>
                            <input type="number" step="0.25" name="irpp_nombre_parts" value="{{ old('irpp_nombre_parts', $fiscalite?->irpp_nombre_parts ?? '') }}" class="border-gray-300 rounded-md shadow-sm w-full text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-2">Connaissez-vous votre TMI (IR) ?</label>
                            <div class="wd-patrimoine-radios">
                                <label class="wd-patrimoine-radio"><input type="radio" name="connait_tmi_ir" value="oui" x-model="connaitTmiIr"><span>Oui</span></label>
                                <label class="wd-patrimoine-radio"><input type="radio" name="connait_tmi_ir" value="non" x-model="connaitTmiIr"><span>Non</span></label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-5" x-show="connaitTmiIr === 'oui'">
                        <label class="block text-xs font-semibold text-gray-600 mb-2">Taux Marginal d'Imposition (TMI)</label>
                        <select name="tmi_ir" class="border-gray-300 rounded-md shadow-sm w-full text-sm">
                            <option value="">Choisir</option>
                            @foreach(['0' => '0 %', '11' => '11 %', '30' => '30 %', '41' => '41 %', '45' => '45 %'] as $valeur => $label)
                                <option value="{{ $valeur }}" {{ old('tmi_ir', $fiscalite?->tmi_ir ?? '') === $valeur ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-2">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-2">Réductions et crédits d'impôts</label>
                            <input type="number" step="0.01" name="reductions_credits_impots" value="{{ old('reductions_credits_impots', $fiscalite?->reductions_credits_impots ?? '') }}" class="border-gray-300 rounded-md shadow-sm w-full text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-2">Impôt net à payer</label>
                            <input type="number" step="0.01" name="impot_net_a_payer" value="{{ old('impot_net_a_payer', $fiscalite?->impot_net_a_payer ?? '') }}" class="border-gray-300 rounded-md shadow-sm w-full text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-2">Contributions sociales</label>
                            <input type="number" step="0.01" name="contributions_sociales" value="{{ old('contributions_sociales', $fiscalite?->contributions_sociales ?? '') }}" class="border-gray-300 rounded-md shadow-sm w-full text-sm">
                        </div>
                    </div>

                    <div class="mt-6 pt-5" style="border-top:1px solid #eeeae6;">
                        <div class="text-xs font-semibold text-gray-600 mb-3">Impôt sur la fortune immobilière (IFI)</div>
                        <div class="mb-5">
                            <label class="block text-xs font-semibold text-gray-600 mb-2">Êtes-vous imposé à l'IFI ?</label>
                            <div class="wd-patrimoine-radios">
                                <label class="wd-patrimoine-radio"><input type="radio" name="impose_ifi" value="oui" x-model="imposeIfi"><span>Oui</span></label>
                                <label class="wd-patrimoine-radio"><input type="radio" name="impose_ifi" value="non" x-model="imposeIfi"><span>Non</span></label>
                            </div>
                        </div>

                        <div x-show="imposeIfi === 'oui'">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-5">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-2">Base imposable</label>
                                    <input type="number" step="0.01" name="base_imposable_ifi" value="{{ old('base_imposable_ifi', $fiscalite?->base_imposable_ifi ?? '') }}" class="border-gray-300 rounded-md shadow-sm w-full text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-2">Connaissez-vous votre TMI (IFI) ?</label>
                                    <div class="wd-patrimoine-radios">
                                        <label class="wd-patrimoine-radio"><input type="radio" name="connait_tmi_ifi" value="oui" x-model="connaitTmiIfi"><span>Oui</span></label>
                                        <label class="wd-patrimoine-radio"><input type="radio" name="connait_tmi_ifi" value="non" x-model="connaitTmiIfi"><span>Non</span></label>
                                    </div>
                                </div>
                                <div x-show="connaitTmiIfi === 'oui'">
                                    <label class="block text-xs font-semibold text-gray-600 mb-2">Taux Marginal d'Imposition (IFI)</label>
                                    <select name="tmi_ifi" class="border-gray-300 rounded-md shadow-sm w-full text-sm">
                                        <option value="">Choisir</option>
                                        @foreach(['0' => '0,00 %', '0.5' => '0,50 %', '0.7' => '0,70 %', '1' => '1,00 %', '1.25' => '1,25 %', '1.5' => '1,50 %'] as $valeur => $label)
                                            <option value="{{ $valeur }}" {{ old('tmi_ifi', $fiscalite?->tmi_ifi ?? '') === $valeur ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-2">Réductions d'IFI</label>
                                    <input type="number" step="0.01" name="reductions_ifi" value="{{ old('reductions_ifi', $fiscalite?->reductions_ifi ?? '') }}" class="border-gray-300 rounded-md shadow-sm w-full text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-2">IFI net à payer</label>
                                    <input type="number" step="0.01" name="ifi_net_a_payer" value="{{ old('ifi_net_a_payer', $fiscalite?->ifi_net_a_payer ?? '') }}" class="border-gray-300 rounded-md shadow-sm w-full text-sm">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg mb-6 p-6"
                     x-data="{ usPerson: '{{ old('us_person', $fiscalite?->us_person ?? '') }}' }">
                    <div class="wd-patrimoine-section-title">Résident U.S.</div>
                    <div class="wd-patrimoine-section-intro">
                        Si vous êtes susceptible d'avoir le statut de contribuable américain, veuillez répondre "oui". Vous pourrez alors préciser le critère qui vous définit en tant que "US Person".
                    </div>

                    <div class="mb-5">
                        <label class="block text-xs font-semibold text-gray-600 mb-2">Êtes-vous une US Person ?</label>
                        <div class="wd-patrimoine-radios">
                            <label class="wd-patrimoine-radio"><input type="radio" name="us_person" value="oui" x-model="usPerson"><span>Oui</span></label>
                            <label class="wd-patrimoine-radio"><input type="radio" name="us_person" value="non" x-model="usPerson"><span>Non</span></label>
                        </div>
                    </div>

                    <div x-show="usPerson === 'oui'" class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        @php
                            $criteresUs = [
                                'us_citoyen' => 'Citoyen des États-Unis',
                                'us_resident' => 'Résident des États-Unis',
                                'us_carte_verte' => 'Possède une carte verte',
                                'us_sejour' => 'A séjourné aux États-Unis',
                                'us_entite' => "US Person par possession d'une entité",
                                'us_autre_raison' => "US Person pour d'autres raisons",
                                'us_tin' => "En possession d'un numéro fiscal (US TIN)",
                            ];
                        @endphp
                        @foreach($criteresUs as $champ => $labelCritere)
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-2">{{ $labelCritere }}</label>
                                <div class="wd-patrimoine-radios">
                                    <label class="wd-patrimoine-radio"><input type="radio" name="{{ $champ }}" value="oui" {{ old($champ, $fiscalite?->$champ ?? '') === 'oui' ? 'checked' : '' }}><span>Oui</span></label>
                                    <label class="wd-patrimoine-radio"><input type="radio" name="{{ $champ }}" value="non" {{ old($champ, $fiscalite?->$champ ?? '') === 'non' ? 'checked' : '' }}><span>Non</span></label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <script type="application/json" id="objectifs-data">{!! json_encode($objectifs, JSON_HEX_TAG) !!}</script>
                <div class="bg-white shadow-sm sm:rounded-lg mb-6 p-6"
                     x-data="{
                        objectifs: JSON.parse(document.getElementById('objectifs-data').textContent).length
                            ? JSON.parse(document.getElementById('objectifs-data').textContent)
                            : [{ objectif: '', horizon: '' }]
                     }">
                    <div class="wd-patrimoine-section-title">Objectifs</div>
                    <div class="wd-patrimoine-section-intro">Définissez vos objectifs (préparer la retraite, réduire la fiscalité, transmettre, générer des revenus...).</div>

                    <template x-for="(o, i) in objectifs" :key="i">
                        <div class="border border-gray-200 rounded-xl px-5 py-5 mb-3">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 items-end">
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-semibold text-gray-600 mb-2">Quels sont vos objectifs ?</label>
                                    <select :name="`objectifs[${i}][objectif]`" x-model="o.objectif" class="border-gray-300 rounded-md shadow-sm w-full text-sm">
                                        <option value="">Choisir</option>
                                        @foreach(config('patrimoine.objectifs', []) as $valeur => $label)
                                            <option value="{{ $valeur }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 mb-2">Horizon (années)</label>
                                    <input type="number" :name="`objectifs[${i}][horizon]`" x-model.number="o.horizon" class="border-gray-300 rounded-md shadow-sm w-full text-sm">
                                </div>
                            </div>
                            <div class="flex justify-end mt-3">
                                <button type="button" @click="objectifs.splice(i, 1)" class="text-xs font-semibold text-red-600">Retirer</button>
                            </div>
                        </div>
                    </template>

                    <button type="button" @click="objectifs.push({ objectif: '', horizon: '' })" class="text-sm font-semibold text-gray-800 mt-2">
                        + Ajouter un objectif
                    </button>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mt-6 pt-5" style="border-top:1px solid #eeeae6;">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-2">Effort d'épargne mensuel dédié à vos objectifs</label>
                            <input type="number" step="0.01" name="effort_epargne_mensuel" value="{{ old('effort_epargne_mensuel', $fiscalite?->effort_epargne_mensuel ?? '') }}" class="border-gray-300 rounded-md shadow-sm w-full text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-2">Montant de votre patrimoine total (si connu)</label>
                            <input type="number" step="0.01" name="montant_patrimoine_total" value="{{ old('montant_patrimoine_total', $fiscalite?->montant_patrimoine_total ?? '') }}" class="border-gray-300 rounded-md shadow-sm w-full text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-2">Montant de vos revenus annuels (si connu)</label>
                            <input type="number" step="0.01" name="montant_revenus_annuels" value="{{ old('montant_revenus_annuels', $fiscalite?->montant_revenus_annuels ?? '') }}" class="border-gray-300 rounded-md shadow-sm w-full text-sm">
                        </div>
                    </div>
                </div>

                @if ($verificationRequise)
                    <div class="bg-white shadow-sm sm:rounded-lg mb-6 p-6">
                        <x-input-label for="code_de_verification_client" value="Code de vérification client" />
                        <x-text-input id="code_de_verification_client" name="code_de_verification_client" type="text" class="block mt-1 w-full" :value="old('code_de_verification_client')" />
                        <p class="text-sm text-gray-500 mt-2">
                            Un code de vérification a été envoyé au client pour valider ce premier recueil.
                        </p>
                    </div>
                @endif

                <div class="flex justify-end mt-6">
                    <x-primary-button>
                        {{ __('Enregistrer le patrimoine') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-tenant-app-layout>

<style>
    /* Compacité verticale des catégories patrimoine */
    .wd-patrimoine-page details > summary {
        padding-bottom: 16px !important;
    }

    .wd-patrimoine-page details > div {
        padding-top: 0 !important;
        padding-bottom: 20px !important;
    }

    .wd-patrimoine-add {
        margin-top: 0 !important;
    }
</style>
