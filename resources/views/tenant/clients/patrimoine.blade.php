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

                <x-patrimoine-bloc categorie="actif_financier" label="Actifs financiers" :mode-detention="true" />
                <x-patrimoine-bloc categorie="actif_non_financier" label="Actifs non financiers" :mode-detention="true" />
                <x-patrimoine-bloc categorie="passif" label="Passifs" />
                <x-patrimoine-bloc categorie="revenu" label="Revenus" />
                <x-patrimoine-bloc categorie="charge" label="Charges" />

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
