<x-tenant-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Profil investisseur - {{ $client->prenom }} {{ $client->nom }}
            </h2>
            <a href="{{ route('tenant.clients.show', $client) }}" class="text-sm text-gray-600 underline">
                {{ __('Retour à la fiche client') }}
            </a>
        </div>
    </x-slot>

    <style>
        .wd-investisseur-page {
            color: #242424;
        }

        .wd-investisseur-intro {
            background: #242424;
            border-radius: 16px;
            padding: 34px 38px;
            margin-bottom: 28px;
            position: relative;
            overflow: hidden;
        }

        .wd-investisseur-intro::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: #F40087;
        }

        .wd-investisseur-intro-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.1fr) minmax(340px, .9fr);
            gap: 52px;
        }

        .wd-investisseur-intro-side {
            border-left: 1px solid rgba(255,255,255,.12);
            padding-left: 38px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .wd-investisseur-eyebrow {
            color: #F40087;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .18em;
            text-transform: uppercase;
            margin-bottom: 12px;
        }

        .wd-investisseur-intro h2 {
            color: #fff;
            font-size: 26px;
            line-height: 1.2;
            font-weight: 750;
            margin: 0 0 16px;
        }

        .wd-investisseur-intro p {
            color: #d4d4d4;
            font-size: 14px;
            line-height: 1.7;
            margin: 0 0 14px;
        }

        .wd-investisseur-side-label {
            color: #a9a9a9;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .16em;
            text-transform: uppercase;
            margin-bottom: 18px;
        }

        .wd-investisseur-objective {
            display: grid;
            grid-template-columns: 34px 1fr;
            gap: 14px;
            padding: 15px 0;
            border-top: 1px solid rgba(255,255,255,.10);
        }

        .wd-investisseur-objective:last-child {
            border-bottom: 1px solid rgba(255,255,255,.10);
        }

        .wd-investisseur-objective span {
            color: #F40087;
            font-size: 11px;
            font-weight: 800;
        }

        .wd-investisseur-objective p {
            margin: 0 !important;
            color: #f1f1f1;
            font-size: 13px;
            line-height: 1.55;
        }

        .wd-investisseur-page form > .bg-white {
            border: 1px solid #e7e3df;
            border-radius: 14px !important;
            box-shadow: none !important;
            padding: 28px !important;
        }

        .wd-investisseur-page form > .bg-white h3 {
            color: #242424;
            font-size: 17px;
            font-weight: 750;
            padding-bottom: 16px;
            border-bottom: 1px solid #eeeae6;
        }

        .wd-investisseur-page label {
            color: #3f3c39;
            font-size: 13px;
        }

        .wd-investisseur-page input[type="text"],
        .wd-investisseur-page input[type="number"],
        .wd-investisseur-page input[type="date"],
        .wd-investisseur-page select,
        .wd-investisseur-page textarea {
            border: 1px solid #d8d4cf !important;
            border-radius: 9px !important;
            min-height: 44px;
            background: #FAF9F7 !important;
            color: #242424;
            box-shadow: none !important;
        }

        .wd-investisseur-page select {
            width: min(100%, 620px) !important;
        }

        .wd-investisseur-page input[type="checkbox"],
        .wd-investisseur-page input[type="radio"] {
            accent-color: #F40087 !important;
            color: #F40087 !important;
            border-color: #cfcac5 !important;
            box-shadow: none !important;
        }

        .wd-investisseur-page input[type="checkbox"]:focus,
        .wd-investisseur-page input[type="radio"]:focus {
            border-color: #F40087 !important;
            box-shadow: 0 0 0 2px rgba(244,0,135,.12) !important;
        }


        /* Grille du questionnaire investisseur */

        .wd-investisseur-questions {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
            border-top: 1px solid #ebe7e3;
            padding-top: 20px;
        }

        .wd-investisseur-question-group {
            min-width: 0;
            border: 1px solid #e7e3df;
            border-radius: 12px;
            background: #fff;
            overflow: hidden;
            transition: border-color .2s ease, background .2s ease;
        }

        .wd-investisseur-question-group:hover {
            border-color: #d8d2cd;
        }

        .wd-investisseur-question-group-large {
            grid-column: 1 / -1;
        }

        .wd-investisseur-section-title {
            grid-column: 1 / -1;
            margin-top: 8px;
            padding-top: 16px;
            border-top: 1px solid #ebe7e3;
        }

        .wd-investisseur-section-title:first-child {
            margin-top: 0;
            padding-top: 0;
            border-top: 0;
        }

        .wd-investisseur-section-title-label {
            font-size: 14px;
            font-weight: 700;
            color: #242424;
            margin-bottom: 4px;
        }

        .wd-investisseur-section-title-desc {
            font-size: 12px;
            line-height: 1.5;
            color: #8a847f;
        }

        .wd-investisseur-question {
            min-width: 0;
            padding: 18px 20px;
            border: 0;
            border-radius: 0;
            background: transparent;
            margin: 0;
        }

        .wd-investisseur-question-child {
            margin: 0 20px;
            padding: 18px 0 20px;
            border-top: 1px solid #ebe7e3;
            background: transparent;
        }

        .wd-investisseur-question-child > label {
            font-weight: 650;
        }

        .wd-investisseur-question > label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: #242424;
            margin-bottom: 6px;
            line-height: 1.45;
        }

        .wd-investisseur-question > p {
            font-size: 12px;
            line-height: 1.5;
            color: #8a847f;
            margin-bottom: 10px;
        }

        .wd-investisseur-choices {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px 26px;
            margin-top: 9px;
        }

        .wd-investisseur-choices label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin: 0;
            font-size: 13px;
            line-height: 1.4;
            cursor: pointer;
        }

        .wd-investisseur-checkboxes {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 9px 36px;
            max-width: 1100px;
        }

        .wd-investisseur-page input[type="radio"],
        .wd-investisseur-page input[type="checkbox"] {
            flex: 0 0 auto;
        }

        @media (max-width: 900px) {
            .wd-investisseur-question-group-large {
                grid-column: auto;
            }

            .wd-investisseur-questions {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .wd-investisseur-question-large {
                grid-column: auto;
            }

            .wd-investisseur-checkboxes {
                grid-template-columns: 1fr;
            }
        }


        .wd-investisseur-question-group:has(
            input[name="reponses[lieu_signature_profil_investisseur]"]
        ),
        .wd-investisseur-question-group:has(
            input[name="reponses[acceptation_termes_et_conditions_profil_investisseur][]"]
        ) {
            min-height: 150px;
        }

        .wd-investisseur-question-group:has(
            input[name="reponses[acceptation_termes_et_conditions_profil_investisseur][]"]
        ) .wd-investisseur-question {
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .wd-investisseur-question-group:has(
            input[name="reponses[acceptation_termes_et_conditions_profil_investisseur][]"]
        ) a {
            color: #F40087;
            text-decoration: underline;
            text-underline-offset: 3px;
            font-weight: 600;
        }

        .wd-investisseur-question-group:has(
            input[name="reponses[acceptation_termes_et_conditions_profil_investisseur][]"]
        ) .wd-investisseur-checkboxes {
            display: flex;
            margin-top: 14px;
        }

        .wd-signature-line {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 220px;
            gap: 24px;
            align-items: end;
        }

        .wd-signature-label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: #242424;
            margin-bottom: 7px;
        }

        .wd-signature-date {
            min-height: 44px;
            display: flex;
            align-items: center;
            padding: 0 14px;
            border: 1px solid #d8d4cf;
            border-radius: 9px;
            background: #F3F1EE;
            color: #242424;
            font-size: 14px;
            font-weight: 600;
        }

        @media (max-width: 700px) {
            .wd-signature-line {
                grid-template-columns: 1fr;
            }
        }


        /* ====================================================
           SCÉNARIOS DE RENDEMENT
           ==================================================== */

        .wd-investisseur-question-group:has(
            input[name="reponses[aversion_2_profil_investisseur]"]
        ) {
            width: 100% !important;
            max-width: none !important;
        }

        .wd-scenarios {
            width: 100%;
        }

        .wd-scenarios-title {
            font-size: 17px;
            line-height: 1.45;
            font-weight: 750;
            color: #242424;
            margin-bottom: 24px;
            max-width: 1250px;
        }

        .wd-scenarios-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
            max-width: 1000px;
            margin: 0 auto 24px;
        }

        .wd-scenario-radio {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .wd-scenario-card {
            position: relative;
            border: 1px solid #e1ddd9;
            border-radius: 14px;
            background: #fff;
            padding: 24px 22px 22px;
            min-height: 410px;
            display: flex;
            flex-direction: column;
            cursor: pointer;
            transition:
                border-color .18s ease,
                background .18s ease,
                box-shadow .18s ease;
        }

        .wd-scenario-card:hover {
            border-color: #cbc5c0;
            background: #FCFBFA;
        }

        .wd-scenario-card.is-selected {
            border-color: #F40087;
            background: rgba(244, 0, 135, .025);
            box-shadow: 0 0 0 1px rgba(244, 0, 135, .08);
        }

        .wd-scenario-card:focus-within {
            outline: 2px solid rgba(244, 0, 135, .18);
            outline-offset: 3px;
        }

        .wd-scenario-name {
            text-align: center;
            font-size: 18px;
            font-weight: 750;
            color: #242424;
            margin-bottom: 20px;
        }

        .wd-scenario-subtitle {
            text-align: center;
            margin-top: -12px;
            margin-bottom: 6px;
            color: #918a85;
            font-size: 11px;
            font-weight: 750;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .wd-scenario-card.is-selected .wd-scenario-subtitle {
            color: #F40087;
        }

        .wd-scenario-chart {
            position: relative;
            height: 190px;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            gap: 22px;
            padding: 18px 12px 46px;
            margin-bottom: 16px;
        }

        .wd-scenario-chart::before,
        .wd-scenario-chart::after {
            content: "";
            position: absolute;
            left: 8px;
            right: 8px;
            border-top: 1px dashed #e7e3df;
        }

        .wd-scenario-chart::before {
            top: 42px;
        }

        .wd-scenario-chart::after {
            top: 104px;
        }

        .wd-scenario-zero {
            position: absolute;
            left: 8px;
            right: 8px;
            bottom: 46px;
            border-top: 1px solid #cbc6c1;
        }

        .wd-bar {
            position: relative;
            z-index: 2;
            width: 48px;
            border-radius: 5px 5px 0 0;
            background: linear-gradient(
                to top,
                #F40087 0%,
                #CF32B4 52%,
                #39C5D6 100%
            );
        }

        .wd-a-low {
            height: 5px;
        }

        .wd-a-mid {
            height: 72px;
        }

        .wd-a-high {
            height: 102px;
        }

        .wd-b-low {
            height: 24px;
            margin-bottom: -24px;
            background: #F40087;
            border-radius: 0 0 5px 5px;
        }

        .wd-b-mid {
            height: 92px;
        }

        .wd-b-high {
            height: 126px;
        }

        .wd-c-low {
            height: 46px;
            margin-bottom: -46px;
            background: #F40087;
            border-radius: 0 0 5px 5px;
        }

        .wd-c-mid {
            height: 134px;
        }

        .wd-c-high {
            height: 151px;
        }

        .wd-scenario-card p {
            margin: auto 0 0;
            text-align: center;
            color: #79736e;
            font-size: 13px;
            line-height: 1.55;
        }

        .wd-scenario-card p strong {
            color: #504b47;
            font-weight: 750;
        }

        .wd-scenarios-choice {
            display: flex;
            flex-direction: column;
            gap: 10px;
            width: fit-content;
            margin-top: 6px;
        }

        .wd-scenarios-choice label {
            display: flex;
            align-items: center;
            gap: 9px;
            cursor: pointer;
            font-size: 13px;
            color: #403c39;
        }

        .wd-scenarios-choice input[type="radio"] {
            accent-color: #F40087 !important;
            color: #F40087 !important;
            flex: 0 0 auto;
        }

        @media (max-width: 950px) {
            .wd-scenarios-grid {
                grid-template-columns: 1fr;
                max-width: 520px;
            }

            .wd-scenario-card {
                min-height: 360px;
            }
        }


        /* ====================================================
           CHOIX RISQUE : PLACEMENT ACTUEL / NOUVEAU PLACEMENT
           ==================================================== */

        .wd-investisseur-question-group:has(
            input[name="reponses[aversion_6_profil_investisseur]"]
        ) {
            width: 100% !important;
            max-width: none !important;
        }

        .wd-risk-choice {
            width: 100%;
        }

        .wd-risk-choice-title {
            max-width: 1250px;
            margin-bottom: 24px;
            font-size: 17px;
            line-height: 1.45;
            font-weight: 750;
            color: #242424;
        }

        .wd-risk-choice-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
            max-width: 980px;
            margin: 0 auto;
        }

        .wd-risk-card {
            position: relative;
            display: flex;
            flex-direction: column;
            min-height: 390px;
            padding: 26px 26px 22px;
            border: 1px solid #e1ddd9;
            border-radius: 14px;
            background: #fff;
            cursor: pointer;
            transition:
                border-color .18s ease,
                background .18s ease,
                box-shadow .18s ease;
        }

        .wd-risk-card:hover {
            border-color: #cbc5c0;
            background: #FCFBFA;
        }

        .wd-risk-card.is-selected {
            border-color: #F40087;
            background: rgba(244, 0, 135, .025);
            box-shadow: 0 0 0 1px rgba(244, 0, 135, .08);
        }

        .wd-risk-card:focus-within {
            outline: 2px solid rgba(244, 0, 135, .18);
            outline-offset: 3px;
        }

        .wd-risk-radio {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .wd-risk-card-title {
            text-align: center;
            font-size: 17px;
            line-height: 1.35;
            font-weight: 750;
            color: #242424;
        }

        .wd-risk-card-subtitle {
            margin-top: 6px;
            text-align: center;
            color: #918a85;
            font-size: 11px;
            font-weight: 750;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .wd-risk-card.is-selected .wd-risk-card-subtitle {
            color: #F40087;
        }

        .wd-risk-chart {
            flex: 1;
            margin: 30px 0 18px;
        }

        .wd-risk-chart-safe {
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            align-items: center;
        }

        .wd-risk-chart-variable {
            display: flex;
            align-items: flex-end;
            justify-content: center;
            gap: 20px;
        }

        .wd-risk-outcome {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            align-items: center;
            max-width: 165px;
        }

        .wd-risk-caption {
            margin-bottom: 10px;
            text-align: center;
            color: #242424;
            font-size: 11px;
            font-weight: 750;
        }

        .wd-risk-bar {
            width: 100%;
            max-width: 270px;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 16px 12px;
            border-radius: 7px;
            color: #fff;
            text-align: center;
            background: linear-gradient(
                135deg,
                #F40087 0%,
                #D82FA8 58%,
                #A94CDB 100%
            );
        }

        .wd-risk-bar strong {
            font-size: 13px;
            font-weight: 800;
        }

        .wd-risk-bar-safe {
            height: 100px;
        }

        .wd-risk-bar-high {
            height: 190px;
        }

        .wd-risk-bar-low {
            height: 115px;
        }

        .wd-risk-card-footer {
            padding-top: 16px;
            border-top: 1px solid #eeeae6;
            text-align: center;
            color: #79736e;
            font-size: 12px;
            line-height: 1.45;
        }

        .wd-risk-explanation {
            max-width: 980px;
            margin: 22px auto 0;
            padding: 20px 22px;
            border-radius: 12px;
            background: #FAF9F7;
            color: #4c4743;
            font-size: 13px;
            line-height: 1.6;
        }

        .wd-risk-explanation p {
            margin: 0 0 10px;
        }

        .wd-risk-explanation ul {
            margin: 0;
            padding-left: 20px;
        }

        .wd-risk-explanation strong {
            color: #242424;
            font-weight: 750;
        }

        @media (max-width: 800px) {
            .wd-risk-choice-grid {
                grid-template-columns: 1fr;
                max-width: 520px;
            }

            .wd-risk-card {
                min-height: 350px;
            }
        }

        .wd-investisseur-page button[type="submit"] {
            background: #242424 !important;
            color: #fff !important;
            border-radius: 8px !important;
            box-shadow: none !important;
        }

        @media (max-width: 900px) {
            .wd-investisseur-intro-grid {
                grid-template-columns: 1fr;
                gap: 28px;
            }

            .wd-investisseur-intro-side {
                border-left: 0;
                border-top: 1px solid rgba(255,255,255,.12);
                padding-left: 0;
                padding-top: 24px;
            }
        }

        @media (max-width: 768px) {
            .wd-investisseur-intro {
                padding: 26px 22px;
            }

            .wd-investisseur-page select {
                width: 100% !important;
            }
        }
    </style>

    <div class="py-10 max-w-5xl mx-auto sm:px-6 lg:px-8 wd-investisseur-page">

        <section class="wd-investisseur-intro">
            <div class="wd-investisseur-intro-grid">

                <div>
                    <div class="wd-investisseur-eyebrow">Objectifs</div>

                    <h2>Déterminer votre profil pour adapter chaque décision d'investissement.</h2>

                    <p>
                        Ce questionnaire nous permet de mieux comprendre votre profil d'investisseur afin de vous proposer des solutions adaptées à votre situation.
                    </p>

                    <p style="margin-bottom:0;">
                        Pour vous conseiller au mieux, il est important de répondre avec des informations complètes et sincères.
                    </p>
                </div>

                <div class="wd-investisseur-intro-side">
                    <div class="wd-investisseur-side-label">Ce questionnaire nous aide à</div>

                    <div class="wd-investisseur-objective">
                        <span>01</span>
                        <p>Évaluer votre niveau de connaissance et votre expérience des marchés financiers.</p>
                    </div>

                    <div class="wd-investisseur-objective">
                        <span>02</span>
                        <p>Mesurer votre sensibilité au risque, essentielle dans toute stratégie d'investissement.</p>
                    </div>

                    <div class="wd-investisseur-objective">
                        <span>03</span>
                        <p>Identifier vos préférences et vos objectifs pour vos projets futurs.</p>
                    </div>
                </div>

            </div>
        </section>

        @if (session('status'))
            <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 text-red-800 rounded">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php
            $checkboxFieldNames = [];
            foreach (config('profil_investisseur_questionnaire') as $section) {
                foreach ($section['champs'] as $champ) {
                    if ($champ['type'] === 'checkbox-field') {
                        $checkboxFieldNames[] = $champ['name'];
                    }
                }
            }
            // Options "Je préfère ne pas répondre" / "Aucun" qui doivent être exclusives
            // des autres cases du même groupe de checkbox.
            $exclusiveOptions = [
                'produits_detenus_profil_investisseur' => 'non_reponse_produits_detenus_profil_investisseur',
                'preference_1_profil_investisseur' => 'aucun_tous_les_objectifs_d_investissement_proposes_peuvent_me_convenir',
            ];
        @endphp
        <form
            method="POST"
            action="{{ route('tenant.clients.profil.update', $client) }}"
            x-data="profilInvestisseurForm(JSON.parse(document.getElementById('reponses-initiales').textContent), @js($checkboxFieldNames))"
        >
            @csrf
            @method('PUT')

            @foreach (config('profil_investisseur_questionnaire') as $section)
                <div class="bg-white shadow rounded p-6 mb-6">
                    <h3 class="text-lg font-semibold mb-4">{{ $section['titre'] }}</h3>

                    @php
                        $champsParents = [];

                        foreach ($section['champs'] as $champAnalyse) {
                            foreach (($champAnalyse['conditions'] ?? []) as $conditionAnalyse) {
                                $champsParents[$conditionAnalyse['field']] = true;
                            }
                        }
                    @endphp

                    <div class="wd-investisseur-questions">

                    @foreach ($section['champs'] as $champ)
                        @if (($champ['type'] ?? null) === 'section-title')
                            <div class="wd-investisseur-section-title">
                                <p class="wd-investisseur-section-title-label">{{ $champ['label'] }}</p>
                                @if (!empty($champ['desc']))
                                    <p class="wd-investisseur-section-title-desc">{{ $champ['desc'] }}</p>
                                @endif
                            </div>
                            @continue
                        @endif
                        @php
                            $nom = $champ['name'];

                            if ($nom === 'code_de_verification_client' && ! $verificationRequise) {
                                continue;
                            }

                            $commenceGroupe = empty($champ['conditions']);
                            $conditionExpr = null;
                            if (! empty($champ['conditions'])) {
                                $parts = [];
                                foreach ($champ['conditions'] as $cond) {
                                    $parts[] = "condMatch(reponses, '" . $cond['field'] . "', '" . $cond['value'] . "')";
                                }
                                $conditionExpr = implode(' && ', $parts);
                            }
                            $exclusiveValue = $exclusiveOptions[$nom] ?? null;

                            $nombreOptions = count($champ['options'] ?? []);

                            $champLarge =
                                (
                                    $champ['type'] === 'checkbox-field'
                                    && $nom !== 'acceptation_termes_et_conditions_profil_investisseur'
                                )
                                || $nombreOptions > 4;

                            $groupeAvecDependances = isset($champsParents[$nom]);

                            $groupeLarge =
                                $champLarge
                                || $groupeAvecDependances
                                || $nom === 'aversion_2_profil_investisseur'
                                || $nom === 'aversion_6_profil_investisseur'
                                || $nom === 'aversion_7_profil_investisseur';
                        @endphp

                        @if ($commenceGroupe)

                            @if (! $loop->first)
                                </div>
                            @endif

                            <div class="wd-investisseur-question-group {{ $groupeLarge ? 'wd-investisseur-question-group-large' : '' }}">

                        @endif

                        @if ($nom !== 'code_de_verification_client' || $verificationRequise)
                        <div
                            class="wd-investisseur-question {{ ! $commenceGroupe ? 'wd-investisseur-question-child' : '' }}"
                            @if($conditionExpr) x-show="{{ $conditionExpr }}" @endif
                        >
                            @if ($nom !== 'lieu_signature_profil_investisseur')
                                <label class="block font-medium mb-1">{!! $champ['label'] !!}</label>
                            @endif
                            @if (!empty($champ['desc']))
                                <p class="text-sm text-gray-500 mb-2">{{ $champ['desc'] }}</p>
                            @endif

                            @switch($champ['type'])
                                @case('radio-field')

                                    @if ($nom === 'aversion_2_profil_investisseur')

                                        <div class="wd-scenarios">

                                            <div class="wd-scenarios-title">
                                                Ce graphique illustre trois placements et leurs rendements annuels possibles sur 8 ans, du scénario pessimiste au scénario le plus optimiste.
                                            </div>

                                            <div class="wd-scenarios-grid">

                                                <label
                                                    class="wd-scenario-card"
                                                    :class="{ 'is-selected': reponses['aversion_2_profil_investisseur'] === 'hypothese_pessimiste' }"
                                                >
                                                    <input
                                                        class="wd-scenario-radio"
                                                        type="radio"
                                                        name="reponses[aversion_2_profil_investisseur]"
                                                        value="hypothese_pessimiste"
                                                        x-model="reponses['aversion_2_profil_investisseur']"
                                                    >

                                                    <div class="wd-scenario-name">
                                                        Placement A
                                                    </div>

                                                    <div class="wd-scenario-subtitle">
                                                        Hypothèse pessimiste
                                                    </div>

                                                    <div class="wd-scenario-chart">
                                                        <div class="wd-scenario-zero"></div>

                                                        <div class="wd-bar wd-a-low"></div>
                                                        <div class="wd-bar wd-a-mid"></div>
                                                        <div class="wd-bar wd-a-high"></div>
                                                    </div>

                                                    <p>
                                                        Vous souhaitez <strong>limiter au maximum le risque</strong>
                                                        de vos investissements, quitte à en limiter la performance.
                                                    </p>
                                                </label>

                                                <label
                                                    class="wd-scenario-card"
                                                    :class="{ 'is-selected': reponses['aversion_2_profil_investisseur'] === 'hypothese_moyenne' }"
                                                >
                                                    <input
                                                        class="wd-scenario-radio"
                                                        type="radio"
                                                        name="reponses[aversion_2_profil_investisseur]"
                                                        value="hypothese_moyenne"
                                                        x-model="reponses['aversion_2_profil_investisseur']"
                                                    >

                                                    <div class="wd-scenario-name">
                                                        Placement B
                                                    </div>

                                                    <div class="wd-scenario-subtitle">
                                                        Hypothèse moyenne
                                                    </div>

                                                    <div class="wd-scenario-chart">
                                                        <div class="wd-scenario-zero"></div>

                                                        <div class="wd-bar wd-b-low"></div>
                                                        <div class="wd-bar wd-b-mid"></div>
                                                        <div class="wd-bar wd-b-high"></div>
                                                    </div>

                                                    <p>
                                                        Vous acceptez un <strong>risque modéré</strong>
                                                        afin de dynamiser la performance de vos placements.
                                                    </p>
                                                </label>

                                                <label
                                                    class="wd-scenario-card"
                                                    :class="{ 'is-selected': reponses['aversion_2_profil_investisseur'] === 'hypothese_optimale' }"
                                                >
                                                    <input
                                                        class="wd-scenario-radio"
                                                        type="radio"
                                                        name="reponses[aversion_2_profil_investisseur]"
                                                        value="hypothese_optimale"
                                                        x-model="reponses['aversion_2_profil_investisseur']"
                                                    >

                                                    <div class="wd-scenario-name">
                                                        Placement C
                                                    </div>

                                                    <div class="wd-scenario-subtitle">
                                                        Hypothèse optimale
                                                    </div>

                                                    <div class="wd-scenario-chart">
                                                        <div class="wd-scenario-zero"></div>

                                                        <div class="wd-bar wd-c-low"></div>
                                                        <div class="wd-bar wd-c-mid"></div>
                                                        <div class="wd-bar wd-c-high"></div>
                                                    </div>

                                                    <p>
                                                        Vous recherchez une <strong>très bonne performance</strong>
                                                        et acceptez de voir votre capital fluctuer à la baisse
                                                        durant la durée de votre placement.
                                                    </p>
                                                </label>

                                            </div>


                                        </div>

                                    @elseif ($nom === 'aversion_6_profil_investisseur')

                                        <div class="wd-risk-choice">

                                            <div class="wd-risk-choice-title">
                                                Imaginez que toute votre épargne soit placée sur un support sans risque, vous assurant un revenu annuel de 20 000 €.
                                            </div>

                                            <div class="wd-risk-choice-grid">

                                                <label
                                                    class="wd-risk-card"
                                                    :class="{ 'is-selected': reponses['aversion_6_profil_investisseur'] === 'je_conserve_le_placement_actuel' }"
                                                >
                                                    <input
                                                        class="wd-risk-radio"
                                                        type="radio"
                                                        name="reponses[aversion_6_profil_investisseur]"
                                                        value="je_conserve_le_placement_actuel"
                                                        x-model="reponses['aversion_6_profil_investisseur']"
                                                    >

                                                    <div class="wd-risk-card-title">
                                                        Je conserve le placement actuel
                                                    </div>

                                                    <div class="wd-risk-card-subtitle">
                                                        Placement sans risque
                                                    </div>

                                                    <div class="wd-risk-chart wd-risk-chart-safe">

                                                        <div class="wd-risk-caption">
                                                            Garanti
                                                        </div>

                                                        <div class="wd-risk-bar wd-risk-bar-safe">
                                                            <strong>20 000 € / an</strong>
                                                        </div>

                                                    </div>

                                                    <div class="wd-risk-card-footer">
                                                        Revenu annuel garanti
                                                    </div>
                                                </label>

                                                <label
                                                    class="wd-risk-card"
                                                    :class="{ 'is-selected': reponses['aversion_6_profil_investisseur'] === 'j_accepte_le_nouveau_placement' }"
                                                >
                                                    <input
                                                        class="wd-risk-radio"
                                                        type="radio"
                                                        name="reponses[aversion_6_profil_investisseur]"
                                                        value="j_accepte_le_nouveau_placement"
                                                        x-model="reponses['aversion_6_profil_investisseur']"
                                                    >

                                                    <div class="wd-risk-card-title">
                                                        J'accepte le nouveau placement
                                                    </div>

                                                    <div class="wd-risk-card-subtitle">
                                                        Placement plus risqué
                                                    </div>

                                                    <div class="wd-risk-chart wd-risk-chart-variable">

                                                        <div class="wd-risk-outcome">
                                                            <div class="wd-risk-caption">
                                                                50 % de chance
                                                            </div>

                                                            <div class="wd-risk-bar wd-risk-bar-high">
                                                                <strong>40 000 € / an</strong>
                                                            </div>
                                                        </div>

                                                        <div class="wd-risk-outcome">
                                                            <div class="wd-risk-caption">
                                                                50 % de chance
                                                            </div>

                                                            <div class="wd-risk-bar wd-risk-bar-low">
                                                                <strong>13 333 € / an</strong>
                                                            </div>
                                                        </div>

                                                    </div>

                                                    <div class="wd-risk-card-footer">
                                                        Revenu variable selon le scénario
                                                    </div>
                                                </label>

                                            </div>

                                            <div class="wd-risk-explanation">
                                                <p>On vous propose alors une autre option : investir ce capital sur un placement plus risqué, avec :</p>

                                                <ul>
                                                    <li><strong>50 % de chance</strong> d'obtenir un revenu annuel de <strong>40 000 €</strong></li>
                                                    <li><strong>50 % de chance</strong> d'obtenir un revenu annuel de <strong>13 333 €</strong></li>
                                                </ul>
                                            </div>

                                        </div>

                                    @elseif ($nom === 'aversion_7_profil_investisseur')

                                        <div class="wd-risk-choice">

                                            <div class="wd-risk-choice-title">
                                                Le placement que vous envisagiez n'est plus disponible.
                                            </div>

                                            <div class="wd-risk-choice-grid">

                                                <label
                                                    class="wd-risk-card"
                                                    :class="{ 'is-selected': reponses['aversion_7_profil_investisseur'] === 'conserve_placement_actuel' }"
                                                >
                                                    <input
                                                        class="wd-risk-radio"
                                                        type="radio"
                                                        name="reponses[aversion_7_profil_investisseur]"
                                                        value="conserve_placement_actuel"
                                                        x-model="reponses['aversion_7_profil_investisseur']"
                                                    >

                                                    <div class="wd-risk-card-title">
                                                        Je conserve le placement actuel
                                                    </div>

                                                    <div class="wd-risk-card-subtitle">
                                                        Placement garanti
                                                    </div>

                                                    <div class="wd-risk-chart wd-risk-chart-safe">

                                                        <div class="wd-risk-caption">
                                                            Garanti
                                                        </div>

                                                        <div class="wd-risk-bar wd-risk-bar-safe">
                                                            <strong>20 000 € / an</strong>
                                                        </div>

                                                    </div>

                                                    <div class="wd-risk-card-footer">
                                                        Revenu annuel garanti
                                                    </div>
                                                </label>

                                                <label
                                                    class="wd-risk-card"
                                                    :class="{ 'is-selected': reponses['aversion_7_profil_investisseur'] === 'accepte_nouveau_placement' }"
                                                >
                                                    <input
                                                        class="wd-risk-radio"
                                                        type="radio"
                                                        name="reponses[aversion_7_profil_investisseur]"
                                                        value="accepte_nouveau_placement"
                                                        x-model="reponses['aversion_7_profil_investisseur']"
                                                    >

                                                    <div class="wd-risk-card-title">
                                                        J'accepte le nouveau placement
                                                    </div>

                                                    <div class="wd-risk-card-subtitle">
                                                        Placement plus exposé
                                                    </div>

                                                    <div class="wd-risk-chart wd-risk-chart-variable">

                                                        <div class="wd-risk-outcome">
                                                            <div class="wd-risk-caption">
                                                                50 % de chance
                                                            </div>

                                                            <div class="wd-risk-bar wd-risk-bar-high">
                                                                <strong>40 000 € / an</strong>
                                                            </div>
                                                        </div>

                                                        <div class="wd-risk-outcome">
                                                            <div class="wd-risk-caption">
                                                                50 % de chance
                                                            </div>

                                                            <div class="wd-risk-bar wd-risk-bar-low">
                                                                <strong>10 000 € / an</strong>
                                                            </div>
                                                        </div>

                                                    </div>

                                                    <div class="wd-risk-card-footer">
                                                        Revenu variable selon le scénario
                                                    </div>
                                                </label>

                                            </div>

                                            <div class="wd-risk-explanation">
                                                <p>
                                                    Vous avez actuellement un placement <strong>garanti</strong>
                                                    qui vous procure un revenu annuel de <strong>20 000 €</strong>.
                                                </p>

                                                <p>
                                                    Une alternative vous est proposée : réallouer votre capital
                                                    vers un placement plus exposé aux variations, avec :
                                                </p>

                                                <ul>
                                                    <li>
                                                        <strong>50 % de chance</strong> d'obtenir un revenu annuel de
                                                        <strong>40 000 €</strong>
                                                    </li>
                                                    <li>
                                                        <strong>50 % de chance</strong> d'obtenir un revenu annuel de
                                                        <strong>10 000 €</strong>
                                                    </li>
                                                </ul>
                                            </div>

                                        </div>

                                    @else

                                        <div class="wd-investisseur-choices">
                                            @foreach ($champ['options'] as $opt)
                                                <label class="flex items-center gap-2">
                                                    <input
                                                        type="radio"
                                                        name="reponses[{{ $nom }}]"
                                                        value="{{ $opt['value'] }}"
                                                        x-model="reponses['{{ $nom }}']"
                                                    >
                                                    <span>{{ $opt['label'] }}</span>
                                                </label>
                                            @endforeach
                                        </div>

                                    @endif

                                    @break

                                @case('checkbox-field')
                                    <div class="wd-investisseur-choices wd-investisseur-checkboxes">
                                        @foreach ($champ['options'] as $opt)
                                            <label class="flex items-center gap-2">
                                                <input
                                                    type="checkbox"
                                                    name="reponses[{{ $nom }}][]"
                                                    value="{{ $opt['value'] }}"
                                                    :checked="Array.isArray(reponses['{{ $nom }}']) && reponses['{{ $nom }}'].includes('{{ $opt['value'] }}')"
                                                    @change="toggleReponseCheckbox('{{ $nom }}', '{{ $opt['value'] }}', {{ $exclusiveValue ? "'" . $exclusiveValue . "'" : 'null' }})"
                                                >
                                                <span>{!! $opt['label'] !!}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    @break

                                @case('date-field')
                                    <input
                                        type="date"
                                        name="reponses[{{ $nom }}]"
                                        x-model="reponses['{{ $nom }}']"
                                        max="{{ now()->subYears(18)->format('Y-m-d') }}"
                                        class="border rounded px-3 py-2 w-full"
                                    >
                                    @break

                                @case('number-field')
                                    <input
                                        type="number"
                                        name="reponses[{{ $nom }}]"
                                        x-model="reponses['{{ $nom }}']"
                                        class="border rounded px-3 py-2 w-full"
                                    >
                                    @break

                                @case('select-field')
                                    @if (! empty($champ['options']))
                                        <select
                                            name="reponses[{{ $nom }}]"
                                            x-model="reponses['{{ $nom }}']"
                                            class="border rounded px-3 py-2 w-full"
                                        >
                                            <option value="">Choisir</option>
                                            @foreach ($champ['options'] as $opt)
                                                <option value="{{ $opt['value'] }}">{{ $opt['label'] }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        {{-- Options non disponibles (glossaire JetEngine non exporté) : champ texte temporaire --}}
                                        <input
                                            type="text"
                                            name="reponses[{{ $nom }}]"
                                            x-model="reponses['{{ $nom }}']"
                                            class="border rounded px-3 py-2 w-full"
                                        >
                                    @endif
                                    @break

                                @case('text-field')
                                    @if ($nom === 'lieu_signature_profil_investisseur')
                                        <div class="wd-signature-line">

                                            <div>
                                                <label class="wd-signature-label">Fait à</label>

                                                <div
                                                    x-data="villeAutocomplete(reponses['{{ $nom }}'] || '')"
                                                    x-init="$watch('query', value => reponses['{{ $nom }}'] = value)"
                                                    class="relative"
                                                >
                                                    <input
                                                        type="text"
                                                        name="reponses[{{ $nom }}]"
                                                        x-model="query"
                                                        @input.debounce.300ms="search()"
                                                        @focus="search()"
                                                        @click.outside="suggestions = []"
                                                        autocomplete="off"
                                                        placeholder="Ville"
                                                        class="border rounded px-3 py-2 w-full"
                                                    >

                                                    <ul
                                                        x-show="suggestions.length > 0"
                                                        class="absolute z-20 bg-white border rounded w-full mt-1 max-h-48 overflow-auto shadow"
                                                    >
                                                        <template x-for="s in suggestions" :key="s.code">
                                                            <li
                                                                @click="select(s)"
                                                                class="px-3 py-2 hover:bg-gray-100 cursor-pointer"
                                                                x-text="s.nom"
                                                            ></li>
                                                        </template>
                                                    </ul>
                                                </div>
                                            </div>

                                            <div>
                                                <label class="wd-signature-label">Le</label>

                                                <div class="wd-signature-date">
                                                    {{ now()->format('d/m/Y') }}
                                                </div>
                                            </div>

                                        </div>
                                    @else
                                        <input
                                            type="text"
                                            name="reponses[{{ $nom }}]"
                                            x-model="reponses['{{ $nom }}']"
                                            class="border rounded px-3 py-2 w-full"
                                        >
                                    @endif
                                    @break

                                @case('objectifs-patrimoine-field')
                                    <div
                                        x-data="objectifsPatrimoineField(@js($client->patrimoineObjectifs->pluck('objectif')->values()), reponses['{{ $nom }}'])"
                                        class="wd-objectifs-profil"
                                    >
                                        <p class="text-sm text-gray-400 mb-2" x-show="items.length === 0">
                                            Aucun objectif renseigné dans la fiche Patrimoine pour l’instant.
                                        </p>

                                        <template x-for="(item, index) in items" :key="index">
                                            <label class="flex items-center gap-2 mb-1">
                                                <input
                                                    type="checkbox"
                                                    name="reponses[{{ $nom }}][]"
                                                    :value="item.label"
                                                    x-model="item.checked"
                                                >
                                                <span x-text="item.label" class="flex-1"></span>
                                                <button
                                                    type="button"
                                                    class="text-xs text-red-600"
                                                    @click="items.splice(index, 1)"
                                                >Retirer</button>
                                            </label>
                                        </template>

                                        <div class="flex gap-2 mt-2">
                                            <input
                                                type="text"
                                                x-model="nouveauLabel"
                                                @keydown.enter.prevent="ajouter()"
                                                placeholder="Ajouter un objectif spécifique"
                                                class="border rounded px-3 py-2 flex-1"
                                            >
                                            <button
                                                type="button"
                                                class="text-sm font-medium text-pink-600"
                                                @click="ajouter()"
                                            >+ Ajouter</button>
                                        </div>
                                    </div>
                                    @break

                                @default
                                    <input
                                        type="text"
                                        name="reponses[{{ $nom }}]"
                                        x-model="reponses['{{ $nom }}']"
                                        class="border rounded px-3 py-2 w-full"
                                    >
                            @endswitch
                        </div>
                        @endif

                        @if ($loop->last)
                            </div>
                        @endif

                    @endforeach

                    </div>
                </div>
            @endforeach



            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded">
                Enregistrer
            </button>
        </form>
    </div>

    <script type="application/json" id="reponses-initiales">{!! json_encode($reponses, JSON_HEX_TAG) !!}</script>

    <script>
        function villeAutocomplete(initial) {
            return {
                query: initial,
                suggestions: [],
                search() {
                    if (this.query.length < 2) {
                        this.suggestions = [];
                        return;
                    }
                    fetch('https://geo.api.gouv.fr/communes?nom=' + encodeURIComponent(this.query) + '&fields=nom,code&boost=population&limit=8')
                        .then(function (r) { return r.json(); })
                        .then((data) => { this.suggestions = Array.isArray(data) ? data : []; })
                        .catch(() => { this.suggestions = []; });
                },
                select(s) {
                    this.query = s.nom;
                    this.suggestions = [];
                },
            };
        }

        function objectifsPatrimoineField(patrimoineLabels, savedValue) {
            const saved = Array.isArray(savedValue) ? savedValue : null;

            const items = patrimoineLabels.map((label) => ({
                label: label,
                checked: saved ? saved.includes(label) : true,
            }));

            if (saved) {
                saved.forEach((label) => {
                    if (!patrimoineLabels.includes(label)) {
                        items.push({ label: label, checked: true });
                    }
                });
            }

            return {
                items: items,
                nouveauLabel: '',
                ajouter() {
                    const label = this.nouveauLabel.trim();
                    if (!label) {
                        return;
                    }
                    this.items.push({ label: label, checked: true });
                    this.nouveauLabel = '';
                },
            };
        }

        function condMatch(reponses, field, value) {
            const val = reponses[field];
            if (Array.isArray(val)) {
                return val.includes(value);
            }
            return val === value;
        }

        function profilInvestisseurForm(initiales, checkboxFields) {
            const reponses = initiales || {};
            checkboxFields.forEach(function (name) {
                if (!Array.isArray(reponses[name])) {
                    reponses[name] = [];
                }
            });
            return {
                reponses: reponses,
                toggleReponseCheckbox(field, value, exclusiveValue) {
                    if (!Array.isArray(this.reponses[field])) {
                        this.reponses[field] = [];
                    }
                    const arr = this.reponses[field];
                    const idx = arr.indexOf(value);
                    if (idx === -1) {
                        if (exclusiveValue && value === exclusiveValue) {
                            this.reponses[field] = [value];
                        } else {
                            arr.push(value);
                            if (exclusiveValue) {
                                const exIdx = arr.indexOf(exclusiveValue);
                                if (exIdx !== -1) arr.splice(exIdx, 1);
                            }
                        }
                    } else {
                        arr.splice(idx, 1);
                    }
                },
            };
        }
    </script>
</x-tenant-app-layout>

<style>
    /* Autocomplétion ville : laisser sortir les suggestions de la carte */
    .wd-investisseur-question-group:has(
        input[name="reponses[lieu_signature_profil_investisseur]"]
    ) {
        overflow: visible !important;
        position: relative;
        z-index: 20;
    }

    .wd-investisseur-question-group:has(
        input[name="reponses[lieu_signature_profil_investisseur]"]
    ) ul {
        z-index: 100 !important;
        max-height: 220px;
        overflow-y: auto;
        background: #fff;
        border: 1px solid #d8d4cf;
        border-radius: 9px;
        box-shadow: 0 12px 30px rgba(36,36,36,.10);
    }
</style>

<style>
    /* Signature : CGU à gauche, lieu et date à droite */

    .wd-investisseur-question-group:has(
        input[name="reponses[acceptation_termes_et_conditions_profil_investisseur][]"]
    ) {
        order: 1;
    }

    .wd-investisseur-question-group:has(
        input[name="reponses[lieu_signature_profil_investisseur]"]
    ) {
        order: 2;
    }
</style>

<style>
    /*
     * Profil investisseur
     * Évite les longues lignes horizontales de réponses.
     */

    .wd-investisseur-choices {
        display: grid !important;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 10px 22px !important;
        align-items: start;
    }

    .wd-investisseur-choices label {
        display: flex !important;
        align-items: flex-start !important;
        gap: 9px !important;
        min-width: 0;
        line-height: 1.4;
    }

    .wd-investisseur-choices input[type="radio"],
    .wd-investisseur-choices input[type="checkbox"] {
        flex: 0 0 auto;
        margin-top: 2px;
    }

    /*
     * Les petites questions Oui / Non restent compactes.
     */
    .wd-investisseur-question:not(.wd-investisseur-question-large)
    .wd-investisseur-choices {
        grid-template-columns: repeat(2, max-content);
        column-gap: 26px !important;
    }

    /*
     * Questions larges :
     * on exploite la largeur sans créer une ligne interminable.
     */
    .wd-investisseur-question-large .wd-investisseur-choices {
        grid-template-columns: repeat(3, minmax(180px, 1fr));
    }

    @media (max-width: 1100px) {
        .wd-investisseur-question-large .wd-investisseur-choices {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 700px) {
        .wd-investisseur-choices,
        .wd-investisseur-question-large .wd-investisseur-choices {
            grid-template-columns: 1fr;
        }
    }
</style>

<style>
    /* Largeur visuelle plus compacte pour les questions simples */
    .wd-investisseur-question-group {
        max-width: 100%;
    }

    .wd-investisseur-question-group:not(.wd-investisseur-question-group-large) {
        width: 100%;
    }

    .wd-investisseur-question-group-large {
        grid-column: 1 / -1;
    }

    /*
     * Les groupes larges dont les réponses restent courtes
     * ne doivent pas forcément occuper toute la largeur visuelle.
     */
    .wd-investisseur-question-group-large
    .wd-investisseur-question:not(.wd-investisseur-question-child)
    .wd-investisseur-choices {
        max-width: 980px;
    }

    /*
     * Questions financières à réponses courtes :
     * largeur interne plafonnée.
     */
    .wd-investisseur-question-group:has(
        input[name="reponses[revenus_annuels_du_foyer_profil_investisseur]"]
    ) .wd-investisseur-choices,
    .wd-investisseur-question-group:has(
        input[name="reponses[epargne_mensuelle_profil_investisseur]"]
    ) .wd-investisseur-choices,
    .wd-investisseur-question-group:has(
        input[name="reponses[patrimoine_immobilier_profil_investisseur]"]
    ) .wd-investisseur-choices,
    .wd-investisseur-question-group:has(
        input[name="reponses[patrimoine_financier_profil_investisseur]"]
    ) .wd-investisseur-choices,
    .wd-investisseur-question-group:has(
        input[name="reponses[emprunts_profil_investisseur]"]
    ) .wd-investisseur-choices {
        max-width: 900px;
        grid-template-columns: repeat(2, minmax(220px, max-content)) !important;
        justify-content: start;
    }

    @media (max-width: 900px) {
        .wd-investisseur-question-group:has(
            input[name="reponses[revenus_annuels_du_foyer_profil_investisseur]"]
        ) .wd-investisseur-choices,
        .wd-investisseur-question-group:has(
            input[name="reponses[epargne_mensuelle_profil_investisseur]"]
        ) .wd-investisseur-choices,
        .wd-investisseur-question-group:has(
            input[name="reponses[patrimoine_immobilier_profil_investisseur]"]
        ) .wd-investisseur-choices,
        .wd-investisseur-question-group:has(
            input[name="reponses[patrimoine_financier_profil_investisseur]"]
        ) .wd-investisseur-choices,
        .wd-investisseur-question-group:has(
            input[name="reponses[emprunts_profil_investisseur]"]
        ) .wd-investisseur-choices {
            grid-template-columns: 1fr !important;
            max-width: none;
        }
    }
</style>

<style>
    /*
     * Largeur naturelle des cartes
     * Elles occupent l'espace dont elles ont réellement besoin.
     */

    .wd-investisseur-questions {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 16px !important;
        align-items: start;
    }

    .wd-investisseur-question-group {
        width: fit-content !important;
        min-width: min(420px, 100%) !important;
        max-width: min(850px, 100%) !important;
        justify-self: start;
        align-self: start;
    }

    /*
     * Groupes avec questions conditionnelles ou contenu conséquent.
     */
    .wd-investisseur-question-group-large {
        grid-column: 1 / -1 !important;
        width: fit-content !important;
        min-width: min(620px, 100%) !important;
        max-width: min(1150px, 100%) !important;
        justify-self: start;
    }

    /*
     * Les réponses utilisent une disposition naturelle.
     * On ne force plus 2 ou 3 colonnes artificielles.
     */
    .wd-investisseur-choices,
    .wd-investisseur-question-large .wd-investisseur-choices,
    .wd-investisseur-checkboxes {
        display: flex !important;
        flex-wrap: wrap !important;
        align-items: flex-start;
        gap: 10px 28px !important;
        max-width: 100% !important;
    }

    .wd-investisseur-choices label,
    .wd-investisseur-checkboxes label {
        width: auto !important;
        min-width: 0 !important;
        max-width: 100%;
        flex: 0 1 auto;
    }

    /*
     * Les réponses longues peuvent prendre davantage de place
     * sans forcer toute la carte à devenir gigantesque.
     */
    .wd-investisseur-question-group-large .wd-investisseur-choices label {
        max-width: 520px;
    }

    /*
     * Les petites réponses Oui / Non restent naturellement compactes.
     */
    .wd-investisseur-question-group:not(.wd-investisseur-question-group-large)
    .wd-investisseur-choices {
        width: fit-content !important;
    }

    @media (max-width: 950px) {
        .wd-investisseur-questions {
            grid-template-columns: 1fr !important;
        }

        .wd-investisseur-question-group,
        .wd-investisseur-question-group-large {
            grid-column: auto !important;
            width: 100% !important;
            min-width: 0 !important;
            max-width: 100% !important;
        }
    }

    @media (max-width: 650px) {
        .wd-investisseur-choices,
        .wd-investisseur-checkboxes {
            flex-direction: column !important;
            gap: 10px !important;
        }
    }
</style>

<style>
    /*
     * Disposition générale des cartes
     */

    .wd-investisseur-questions {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 16px !important;
        align-items: start !important;
    }

    .wd-investisseur-question-group {
        width: 100% !important;
        min-width: 0 !important;
        max-width: none !important;
        justify-self: stretch !important;
        align-self: start !important;
    }

    /*
     * Les groupes réellement volumineux ou avec dépendances
     * peuvent conserver toute la largeur.
     */
    .wd-investisseur-question-group-large {
        grid-column: 1 / -1 !important;
        width: 100% !important;
        max-width: none !important;
    }

    /*
     * Tous les choix sont verticaux
     */
    .wd-investisseur-choices,
    .wd-investisseur-checkboxes,
    .wd-investisseur-question-large .wd-investisseur-choices,
    .wd-investisseur-question-group:not(.wd-investisseur-question-group-large)
    .wd-investisseur-choices {
        display: flex !important;
        flex-direction: column !important;
        flex-wrap: nowrap !important;
        align-items: flex-start !important;
        gap: 9px !important;
        width: 100% !important;
        max-width: none !important;
    }

    .wd-investisseur-choices label,
    .wd-investisseur-checkboxes label,
    .wd-investisseur-question-group-large .wd-investisseur-choices label {
        display: flex !important;
        align-items: flex-start !important;
        gap: 9px !important;
        width: 100% !important;
        max-width: none !important;
        margin: 0 !important;
        line-height: 1.45 !important;
    }

    .wd-investisseur-choices input[type="radio"],
    .wd-investisseur-choices input[type="checkbox"] {
        flex: 0 0 auto !important;
        margin-top: 2px !important;
    }

    /*
     * Une carte simple reste compacte verticalement.
     */
    .wd-investisseur-question {
        padding: 18px 20px !important;
    }

    /*
     * Questions enfants dans une même carte
     */
    .wd-investisseur-question-child {
        margin: 0 20px !important;
        padding: 18px 0 20px !important;
    }

    /*
     * Les questions à réponses assez longues peuvent
     * utiliser toute la largeur de leur colonne sans s'étirer.
     */
    .wd-investisseur-question > label {
        max-width: 760px;
    }

    /*
     * Mobile
     */
    @media (max-width: 900px) {
        .wd-investisseur-questions {
            grid-template-columns: 1fr !important;
        }

        .wd-investisseur-question-group,
        .wd-investisseur-question-group-large {
            grid-column: auto !important;
        }
    }
</style>

<style>
    /*
     * Têtes des grandes familles du profil investisseur
     * Exemple : Fonds euros, produits monétaires, obligataires et actions
     */

    .wd-investisseur-question-group-large
    > .wd-investisseur-question:first-child {
        position: relative;
        padding: 22px 24px 20px !important;
        background: #FAF9F7;
        border-bottom: 1px solid #e7e3df;
    }

    .wd-investisseur-question-group-large
    > .wd-investisseur-question:first-child::before {
        content: "";
        position: absolute;
        left: 0;
        top: 18px;
        bottom: 18px;
        width: 3px;
        border-radius: 0 3px 3px 0;
        background: #F40087;
    }

    .wd-investisseur-question-group-large
    > .wd-investisseur-question:first-child
    > label {
        font-size: 15px !important;
        line-height: 1.35 !important;
        font-weight: 750 !important;
        color: #242424 !important;
        letter-spacing: -0.01em;
        margin-bottom: 12px !important;
    }

    /*
     * Les sous-questions restent clairement secondaires.
     */
    .wd-investisseur-question-child {
        padding-top: 20px !important;
        padding-bottom: 20px !important;
    }

    .wd-investisseur-question-child > label {
        font-size: 13px !important;
        font-weight: 700 !important;
        color: #242424 !important;
    }

    /*
     * Séparation plus nette entre deux grandes familles.
     */
    .wd-investisseur-question-group-large {
        margin-top: 8px !important;
        margin-bottom: 8px !important;
        border-color: #ddd8d3 !important;
        box-shadow: 0 5px 18px rgba(36, 36, 36, .035);
    }

    .wd-investisseur-question-group-large + .wd-investisseur-question-group-large {
        margin-top: 18px !important;
    }
</style>
