<x-tenant-app-layout>

@php
    $viewRole = Auth::user()?->effectiveRole();
    $profil = $client->profilInvestisseur;

    $initiales = mb_strtoupper(
        mb_substr($client->prenom ?? '', 0, 1) .
        mb_substr($client->nom ?? '', 0, 1)
    );

    $formatEuro = fn($value) =>
        number_format((float) $value, 0, ',', ' ') . ' €';

    $profilFinal = $profil?->profil_risque_final_echelle
        ?? $profil?->profil_risque_final
        ?? 'Non déterminé';

    $tolerance = $profil?->score_tolerance_risque_echelle
        ?? 'Non déterminée';

    $capacite = $profil?->score_capacite_financiere_echelle
        ?? 'Non déterminée';

    $experience = $profil?->score_experience_global_echelle
        ?? 'Non déterminée';

    $connaissance = $profil?->score_connaissance_global_echelle
        ?? 'Non déterminée';


    /*
    |--------------------------------------------------------------------------
    | Complétion KYC
    |--------------------------------------------------------------------------
    */

    $kycCompletion = 0;

    if ($client->kyc) {

        $kycData = collect($client->kyc->getAttributes())
            ->except([
                'id',
                'client_id',
                'created_at',
                'updated_at',
            ]);

        $totalKycFields = $kycData->count();

        $filledKycFields = $kycData->filter(function ($value) {
            return ! is_null($value)
                && $value !== ''
                && $value !== '[]';
        })->count();

        $kycCompletion = $totalKycFields > 0
            ? (int) round(($filledKycFields / $totalKycFields) * 100)
            : 0;
    }


    /*
    |--------------------------------------------------------------------------
    | Dossier complet (KYC + Patrimoine + Profil investisseur)
    |--------------------------------------------------------------------------
    */

    $dossierStatus = $client->completionStatus();
    $dossierComplet = $dossierStatus['items']['kyc']['done']
        && $dossierStatus['items']['pat']['done']
        && $dossierStatus['items']['inv']['done'];


    /*
    |--------------------------------------------------------------------------
    | Données du client (modal "Voir ses données")
    |--------------------------------------------------------------------------
    */

    $kyc = $client->kyc;

    $labelListe = fn($liste, $valeur) => $valeur
        ? (config("listes.$liste")[$valeur] ?? $valeur)
        : null;

    $formatDateKyc = fn($date) => $date ? $date->translatedFormat('d F Y') : null;

    $nbEnfantsCharge = $client->personnesACharge()->count();

    $donneesClient = [
        'Naissance' => array_filter([
            $client->date_naissance ? 'Le : ' . $formatDateKyc($client->date_naissance) : null,
            $kyc?->commune_naissance ? 'à : ' . $kyc->commune_naissance : null,
            $kyc?->code_postal_naissance,
            $kyc?->francais === 'oui'
                ? 'Nationalité française'
                : ($kyc?->autre_nationalite ? 'Nationalité ' . $labelListe('nationalites', $kyc->autre_nationalite) : null),
        ]),
        'Juridique' => array_filter([
            $labelListe('classification_mif', $kyc?->classification_mif),
            $labelListe('capacite_juridique', $kyc?->capacite_juridique),
        ]),
        'Familial' => array_filter([
            $labelListe('situation_familiale', $kyc?->situation_familiale),
            $nbEnfantsCharge > 0 ? $nbEnfantsCharge . ' enfant(s) à charge' : 'Aucun enfant à charge',
        ]),
        'Matrimonial' => array_filter([
            $labelListe('regime_matrimonial', $kyc?->regime_matrimonial),
        ]),
        'Conjoint' => array_filter([
            $kyc?->conjoint_date_naissance ? 'Né le : ' . $formatDateKyc($kyc->conjoint_date_naissance) : null,
        ]),
        'Professionnel (' . trim($client->prenom . ' ' . $client->nom) . ')' => array_filter([
            $kyc?->profession_libelle ? 'Profession : ' . $kyc->profession_libelle : null,
            $kyc?->csp ? 'CSP : ' . $labelListe('csp', $kyc->csp) : null,
            $kyc?->societe_employeur ? 'Employeur : ' . $kyc->societe_employeur : null,
            $kyc?->date_entree_entreprise ? 'Depuis le : ' . $formatDateKyc($kyc->date_entree_entreprise) : null,
            $kyc?->age_depart_retraite ? 'Départ retraite prévu : ' . $kyc->age_depart_retraite . ' ans' : null,
        ]),
        'Professionnel (conjoint)' => array_filter([
            $kyc?->conjoint_profession_libelle ? 'Profession : ' . $kyc->conjoint_profession_libelle : null,
            $kyc?->conjoint_age_depart_retraite ? 'Départ retraite prévu : ' . $kyc->conjoint_age_depart_retraite . ' ans' : null,
        ]),
        'Donation' => array_filter([
            $kyc?->donation_dernier_vivant_profit === 'oui' ? 'Donation au dernier vivant à votre profit' : null,
            $kyc?->donation_dernier_vivant_conjoint === 'oui' ? 'Donation au dernier vivant au profit de votre conjoint' : null,
        ]),
        'Résidence' => array_filter([
            $client->adresse,
            $client->code_postal,
            $client->ville,
            $client->pays ? $labelListe('pays', $client->pays) : null,
        ]),
        'Exposition' => array_filter([
            $kyc?->est_ppe
                ? ($kyc->est_ppe === 'oui_ppe' ? 'Est une personne politiquement exposée' : "N'est pas une personne politiquement exposée")
                : null,
            $kyc?->proche_ppe
                ? ($kyc->proche_ppe === 'oui_proche_ppe' ? "Est proche d'une personne politiquement exposée" : "N'est pas proche d'une personne politiquement exposée")
                : null,
        ]),
    ];

    $donneesClient = array_filter($donneesClient);


    /*
    |--------------------------------------------------------------------------
    | Données du patrimoine (modal "Voir ses données")
    |--------------------------------------------------------------------------
    */

    $fiscalite = $client->patrimoineFiscalite;

    $donneesPatrimoine = [
        'Fiscalité (IR)' => array_filter([
            $fiscalite?->resident_fiscal_francais === 'non' ? "N'est pas résident fiscal français" : ($fiscalite?->resident_fiscal_francais === 'oui' ? 'Résident fiscal français' : null),
            $fiscalite?->irpp_montant !== null ? 'IR : ' . $formatEuro($fiscalite->irpp_montant) : null,
            $fiscalite?->tmi_ir !== null ? 'TMI IR : ' . $fiscalite->tmi_ir . ' %' : null,
            $fiscalite?->contributions_sociales !== null ? 'Contributions sociales : ' . $formatEuro($fiscalite->contributions_sociales) : null,
            $fiscalite?->reductions_credits_impots !== null ? "Réductions / crédits d'impôt : " . $formatEuro($fiscalite->reductions_credits_impots) : null,
        ]),
        'IFI' => array_filter([
            $fiscalite?->impose_ifi === 'non' ? "N'est pas imposable à l'IFI" : ($fiscalite?->impose_ifi === 'oui' ? "Imposable à l'IFI" : null),
            $fiscalite?->base_imposable_ifi !== null ? 'Base imposable : ' . $formatEuro($fiscalite->base_imposable_ifi) : null,
            $fiscalite?->tmi_ifi !== null ? 'TMI IFI : ' . $fiscalite->tmi_ifi . ' %' : null,
            $fiscalite?->ifi_net_a_payer !== null ? 'IFI net à payer : ' . $formatEuro($fiscalite->ifi_net_a_payer) : null,
        ]),
        'États-Unis' => array_filter([
            $fiscalite?->us_person === 'non' ? "N'est pas US person" : ($fiscalite?->us_person === 'oui' ? 'Est US person' : null),
            $fiscalite?->us_citoyen === 'non' ? 'Non citoyen des États-Unis' : null,
            $fiscalite?->us_resident === 'non' ? "N'est pas résident des États-Unis" : null,
            $fiscalite?->us_carte_verte === 'non' ? 'Ne possède pas de carte verte' : null,
            $fiscalite?->us_sejour === 'non' ? "N'a pas séjourné aux États-Unis" : null,
        ]),
        'Patrimoine' => array_filter([
            'Actifs financiers : ' . $formatEuro($actifsFinanciers),
            'Actifs non financiers : ' . $formatEuro($actifsNonFinanciers),
            'Passifs : ' . $formatEuro($passifs),
            'Patrimoine net : ' . $formatEuro($patrimoineNet),
            'Solde annuel : ' . $formatEuro($soldeAnnuel),
        ]),
        'Épargne' => array_filter([
            $fiscalite?->effort_epargne_mensuel !== null ? "Effort d'épargne : " . $formatEuro($fiscalite->effort_epargne_mensuel) : null,
            $fiscalite?->montant_patrimoine_total !== null ? 'Montant patrimoine déclaré : ' . $formatEuro($fiscalite->montant_patrimoine_total) : null,
            $fiscalite?->montant_revenus_annuels !== null ? 'Montant revenus annuels : ' . $formatEuro($fiscalite->montant_revenus_annuels) : null,
        ]),
        'Objectifs' => $client->patrimoineObjectifs->map(function ($o) {
            $label = config('patrimoine.objectifs')[$o->objectif] ?? $o->objectif;
            return $o->horizon ? $label . ' (horizon : ' . $o->horizon . ' ans)' : $label;
        })->filter()->values()->all(),
    ];

    $donneesPatrimoine = array_filter($donneesPatrimoine);


    /*
    |--------------------------------------------------------------------------
    | Données du profil investisseur (modal "Voir ses données")
    |--------------------------------------------------------------------------
    */

    $donneesInvestisseur = [
        'Profil de risque' => array_filter([
            'Profil final : ' . $profilFinal,
            'Tolérance au risque : ' . $tolerance,
            'Capacité financière : ' . $capacite,
            'Expérience : ' . $experience,
            'Connaissance : ' . $connaissance,
            $profil?->score_capacite_subir_pertes_echelle ? 'Capacité à subir des pertes : ' . $profil->score_capacite_subir_pertes_echelle : null,
        ]),
        'Extra-financier' => array_filter([
            $profil?->engagement_extra_financier_echelle ? 'Engagement extra-financier : ' . $profil->engagement_extra_financier_echelle : null,
            $profil?->orientation_extra_financier_echelle ? 'Orientation : ' . $profil->orientation_extra_financier_echelle : null,
            $profil?->thematiques_esg_echelle ? 'Thématiques ESG : ' . $profil->thematiques_esg_echelle : null,
        ]),
        'Objectifs' => array_filter([
            ! empty($profil?->reponses['profil_investisseur_objetifs'] ?? null)
                ? (config('patrimoine.objectifs')[$profil->reponses['profil_investisseur_objetifs']] ?? $profil->reponses['profil_investisseur_objetifs'])
                : null,
        ]),
    ];

    $donneesInvestisseur = array_filter($donneesInvestisseur);


    /*
    |--------------------------------------------------------------------------
    | Téléphone
    |--------------------------------------------------------------------------
    */

    $formatTelephone = function ($telephone) {

        if (! $telephone) {
            return '-';
        }

        $digits = preg_replace('/\D+/', '', $telephone);

        if (strlen($digits) === 10) {
            return trim(chunk_split($digits, 2, ' '));
        }

        return $telephone;
    };


    $cleanScoreLabel = function ($value) {
        if (! $value) {
            return 'Non déterminé';
        }

        return trim(preg_replace('/^[🔴🟠🟢]+\s*/u', '', $value));
    };

    $profilFinal = $cleanScoreLabel($profilFinal);
    $tolerance = $cleanScoreLabel($tolerance);
    $capacite = $cleanScoreLabel($capacite);
    $experience = $cleanScoreLabel($experience);
    $connaissance = $cleanScoreLabel($connaissance);

    $scoreProfil = (float) ($profil?->profil_risque_final ?? 0);
    $scoreConnaissance = (float) ($profil?->score_connaissance_global ?? 0);
    $scoreExperience = (float) ($profil?->score_experience_global ?? 0);
    $scoreCapacite = (float) ($profil?->score_capacite_financiere ?? 0);
    $scoreTolerance = (float) ($profil?->score_tolerance_risque ?? 0);
    $scorePertes = (float) ($profil?->score_capacite_subir_pertes ?? 0);
    $scoreEsg = (float) ($profil?->score_esg ?? 0);
@endphp

<style>
body > div > nav,
body > div > header{display:none!important}

html,body{
    margin:0!important;
    background:#f3f1ee!important;
}

:root{
    --bg:#f3f1ee;
    --white:#fff;
    --soft:#faf9f7;
    --ink:#171514;
    --muted:#817a75;
    --line:#ded9d4;
    --dark:#242424;
    --pink:#f40087;
    --green:#4d8760;
    --amber:#b77a2d;
    --red:#b94d4d;
}

*{box-sizing:border-box}

.wd-sidebar{
    position:fixed;
    inset:0 auto 0 0;
    width:232px;
    height:100vh;
    background:#242424;
    color:#fff;
    padding:22px 14px;
    display:flex;
    flex-direction:column;
    z-index:1000;
}

.wd-logo{
    padding:0 8px 26px;
    font-size:23px;
    font-weight:800;
    letter-spacing:-.05em;
}
.wd-logo b{color:var(--pink)}
.wd-logo small{
    display:block;
    color:#8d8580;
    font-size:8px;
    letter-spacing:.22em;
    text-transform:uppercase;
    margin-top:5px;
}

.wd-nav-section{
    margin:20px 10px 7px;
    color:rgba(255,255,255,.28);
    font-size:9px;
    font-weight:800;
    letter-spacing:.14em;
    text-transform:uppercase;
}

.wd-nav a{
    display:flex;
    align-items:center;
    gap:11px;
    padding:10px 11px;
    margin:2px 0;
    border-radius:8px;
    color:rgba(255,255,255,.62);
    text-decoration:none;
    font-size:12px;
}
.wd-nav a.active{
    background:rgba(244,0,135,.11);
    color:#fff;
}
.wd-nav i{
    width:19px;
    font-style:normal;
    color:#777;
}
.wd-nav a.active i{color:var(--pink)}

.wd-logout{
    margin-top:auto;
    border-top:1px solid rgba(255,255,255,.08);
    padding-top:14px;
}
.wd-logout button{
    border:0;
    background:none;
    color:#938b86;
    font-size:9px;
    text-transform:uppercase;
    letter-spacing:.12em;
}

.wd-main{
    margin-left:232px;
    min-height:100vh;
    padding-top:68px;
}

.wd-topbar{
    position:fixed;
    top:0;
    left:232px;
    right:0;
    height:68px;
    z-index:900;
    background:rgba(255,255,255,.95);
    border-bottom:1px solid var(--line);
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:0 34px;
}

.wd-crumb{
    font-size:10px;
    font-weight:800;
    letter-spacing:.15em;
    text-transform:uppercase;
    color:#96908b;
}

.wd-user{
    text-align:right;
    font-size:12px;
    font-weight:700;
}
.wd-user small{
    display:block;
    color:#a19a95;
    font-size:9px;
    margin-top:2px;
}

.wd-wrap{
    max-width:1540px;
    margin:auto;
    padding:28px 34px 60px;
}

.wd-hero{
    background:#242424;
    color:white;
    border-radius:14px;
    overflow:hidden;
    border-top:3px solid var(--pink);
    box-shadow:0 10px 30px rgba(27,23,22,.08);
}

.wd-hero-non-conforme{
    background:#E67E22;
}
.wd-hero-conforme{border-top-color:var(--green);}

.wd-hero-main{
    display:grid;
    grid-template-columns:1.4fr .6fr;
    gap:24px;
    padding:28px 30px 25px;
}

.wd-identity{
    display:flex;
    align-items:center;
    gap:18px;
}

.wd-avatar{
    width:64px;
    height:64px;
    border-radius:50%;
    background:rgba(255,255,255,.08);
    border:1px solid rgba(255,255,255,.08);
    display:grid;
    place-items:center;
    font-size:18px;
    font-weight:800;
}

.wd-eyebrow{
    font-size:10px;
    color:var(--pink);
    font-weight:850;
    letter-spacing:.19em;
    text-transform:uppercase;
}

.wd-hero .wd-eyebrow{color:#c9c2be}

.wd-hero-non-conforme .wd-eyebrow{color:#fff}

.wd-hero h1{
    margin:6px 0 4px;
    font-size:34px;
    letter-spacing:-.045em;
}

.wd-hero-meta{
    color:#aaa29e;
    font-size:12px;
}

.wd-hero-non-conforme .wd-hero-meta{
    color:#fff;
}

.wd-actions{
    display:flex;
    justify-content:flex-end;
    gap:9px;
    align-items:flex-start;
}

.wd-btn{
    min-height:40px;
    padding:0 14px;
    border-radius:8px;
    border:1px solid rgba(255,255,255,.14);
    background:rgba(255,255,255,.06);
    color:white;
    display:inline-flex;
    align-items:center;
    text-decoration:none;
    font-size:11px;
    font-weight:700;
}

.wd-btn.primary{
    background:#fff;
    color:#242424;
}

.wd-hero-foot{
    display:grid;
    grid-template-columns:repeat(auto-fit, minmax(180px, 1fr));
    border-top:1px solid rgba(255,255,255,.10);
    background:#242424;
}

.wd-hero-foot>div{
    padding:16px 20px;
    border-right:1px solid rgba(255,255,255,.10);
}

.wd-hero-foot>div:last-child{
    border-right:0;
}

.wd-hero-foot span{
    display:block;
    color:#8f8883;
    font-size:9px;
    text-transform:uppercase;
    letter-spacing:.11em;
    font-weight:800;
}
.wd-hero-foot strong{
    display:block;
    margin-top:6px;
    font-size:13px;
}

.wd-tabs{
    display:flex;
    gap:4px;
    margin:16px 0 24px;
    padding:5px;
    background:#ebe8e5;
    border-radius:10px;
}

.wd-tabs a{
    flex:1;
    text-align:center;
    padding:10px 16px;
    border-radius:7px;
    text-decoration:none;
    color:#77706c;
    font-size:11px;
}

.wd-tabs a.active{
    background:#fff;
    color:#171514;
    font-weight:750;
}

.wd-tabs-action{
    margin-left:auto;
}

.wd-subtabs{
    display:flex;
    align-items:center;
    gap:8px;
    margin:-14px 0 24px;
}

.wd-subtabs a{
    padding:8px 14px;
    border-radius:7px;
    background:#ebe8e5;
    color:#77706c;
    font-size:11px;
    font-weight:750;
    text-decoration:none;
}

.wd-subtabs a:hover{
    background:#ded9d4;
    color:#171514;
}

.wd-subtabs-action{
    margin-left:auto;
}

.wd-btn-dark{
    flex:0 0 auto;
    min-width:220px;
    min-height:40px;
    padding:0 20px;
    border:1px solid rgba(255,255,255,.10);
    border-top:2px solid #FF3399;
    border-radius:8px;
    background:#242424;
    color:#ffffff;
    font-size:9px;
    font-weight:800;
    letter-spacing:.10em;
    text-transform:uppercase;
    text-decoration:none;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    cursor:pointer;
    font-family:inherit;
    transition:background .18s ease,border-color .18s ease,transform .18s ease,box-shadow .18s ease;
}
.wd-btn-dark:hover{
    background:#242424;
    border-color:rgba(255,255,255,.10);
    border-top-color:#FF3399;
    color:#ffffff;
    box-shadow:0 0 0 2px rgba(255,51,153,.10);
    transform:translateY(-1px);
}
.wd-btn-dark-disabled{
    flex:0 0 auto;
    min-height:40px;
    padding:0 20px;
    border:1px solid #D2D8D5;
    border-top:2px solid #C8CFCC;
    border-radius:8px;
    background:#E2E5E4;
    color:#929A97;
    font-size:9px;
    font-weight:800;
    letter-spacing:.10em;
    text-transform:uppercase;
    cursor:not-allowed;
    display:inline-flex;
    align-items:center;
    justify-content:center;
}

.wd-btn-outline{
    background:#fff;
    border:1px solid var(--line);
    color:#171514;
    font-size:11px;
    font-weight:700;
    padding:0 16px;
    height:38px;
    border-radius:8px;
    cursor:pointer;
    display:inline-flex;
    align-items:center;
    font-family:inherit;
}

.wd-btn-outline:hover{
    border-color:var(--pink);
    color:var(--pink);
}

.wd-kyc-progress{
    padding:20px 24px;
}

.wd-donnees-modal{
    max-width:920px;
}

.wd-donnees-head-dark{
    display:flex;
    align-items:center;
    gap:12px;
    background:#242424;
    color:#fff;
    padding:18px 26px;
    margin:-26px -26px 20px;
    border-radius:14px 14px 0 0;
}

.wd-donnees-head-dark-icon{
    width:34px;
    height:34px;
    flex:0 0 34px;
    border-radius:8px;
    background:rgba(255,255,255,.08);
    display:grid;
    place-items:center;
}

.wd-donnees-head-dark-icon svg{
    width:18px;
    height:18px;
    stroke:var(--pink);
    fill:none;
    stroke-width:2;
    stroke-linecap:round;
    stroke-linejoin:round;
}

.wd-donnees-head-dark-text{
    flex:1;
}

.wd-donnees-head-dark-text h3{
    margin:0;
    font-size:15px;
    font-weight:800;
    color:#fff;
}

.wd-donnees-head-dark-text p{
    margin:2px 0 0;
    font-size:11px;
    color:#b9b2ad;
}

.wd-donnees-close-dark{
    background:none;
    border:0;
    color:#fff;
    font-size:22px;
    line-height:1;
    cursor:pointer;
    padding:0 4px;
    flex:0 0 auto;
}

.wd-donnees-close-dark:hover{
    color:var(--pink);
}

.wd-donnees-grid{
    margin-top:20px;
    display:grid;
    grid-template-columns:repeat(3, 1fr);
    gap:16px;
    max-height:65vh;
    overflow-y:auto;
}

.wd-donnees-card{
    border:1px solid var(--line);
    border-radius:10px;
    padding:14px 16px;
}

.wd-donnees-card h4{
    margin:0 0 10px;
    font-size:12px;
    font-weight:800;
}

.wd-donnees-pills{
    display:flex;
    flex-direction:column;
    gap:6px;
}

.wd-donnees-pill{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:8px;
    border:1px solid var(--line);
    border-radius:20px;
    padding:6px 10px;
    background:#faf9f7;
    font-size:11px;
    text-align:left;
    cursor:pointer;
    color:inherit;
    font-family:inherit;
}

.wd-donnees-pill:hover{
    border-color:var(--pink);
}

.wd-donnees-pill svg{
    flex:0 0 auto;
    color:#918984;
}

.wd-donnees-pill.copied svg{
    color:var(--green);
}

.wd-section{
    margin-top:22px;
}

.wd-section-head{
    display:flex;
    justify-content:space-between;
    align-items:end;
    margin-bottom:11px;
}

.wd-section-head h2{
    margin:4px 0 0;
    font-size:21px;
    letter-spacing:-.03em;
}

.wd-panel{
    background:white;
    border:1px solid var(--line);
    border-radius:12px;
    overflow:hidden;
}

.wd-overview{
    display:grid;
    grid-template-columns:repeat(4,1fr);
}

.wd-kpi{
    padding:22px;
    border-right:1px solid var(--line);
}
.wd-kpi:last-child{border-right:0}

.wd-kpi span{
    display:block;
    color:#918984;
    font-size:9px;
    text-transform:uppercase;
    letter-spacing:.1em;
    font-weight:800;
}

.wd-kpi strong{
    display:block;
    margin-top:8px;
    font-size:24px;
    letter-spacing:-.035em;
}

.wd-patrimoine-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:20px;
}

.wd-assets{
    padding:20px;
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:12px;
}

.wd-asset{
    border:1px solid #ebe7e4;
    border-radius:10px;
    padding:16px;
    background:#fff;
}

.wd-asset span{
    color:#918984;
    font-size:9px;
    text-transform:uppercase;
    letter-spacing:.1em;
    font-weight:800;
}

.wd-asset strong{
    display:block;
    margin-top:7px;
    font-size:18px;
}

.wd-chart{
    padding:22px;
}

.wd-bar-row{
    display:grid;
    grid-template-columns:150px 1fr 110px;
    gap:14px;
    align-items:center;
    margin:14px 0;
}

.wd-bar-label{
    font-size:11px;
    font-weight:650;
}

.wd-bar-track{
    height:8px;
    background:var(--chart-track);
    border-radius:20px;
    overflow:hidden;
}

.wd-bar-fill{
    height:100%;
    background:#242424;
    border-radius:20px;
}

.wd-bar-value{
    text-align:right;
    font-size:11px;
    font-weight:700;
}

.wd-profile-grid{
    display:grid;
    grid-template-columns:.85fr 1.15fr;
    gap:20px;
}

.wd-risk{
    padding:26px;
    text-align:center;
}

.wd-risk-score{
    width:150px;
    height:150px;
    margin:auto;
    border-radius:50%;
    border:18px solid #e9e5e2;
    display:grid;
    place-items:center;
    position:relative;
}

.wd-risk-score:after{
    content:"";
    position:absolute;
    inset:-18px;
    border-radius:50%;
    border:18px solid transparent;
    border-top-color:#242424;
    border-right-color:#242424;
    transform:rotate(20deg);
}

.wd-risk-score strong{
    font-size:20px;
    position:relative;
    z-index:2;
}

.wd-risk h3{
    margin:18px 0 5px;
    font-size:22px;
}

.wd-signals{
    padding:20px;
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:12px;
}

.wd-signal{
    padding:15px;
    border:1px solid #ebe7e4;
    border-radius:10px;
}

.wd-signal span{
    font-size:9px;
    color:#918984;
    text-transform:uppercase;
    letter-spacing:.09em;
    font-weight:800;
}

.wd-signal strong{
    display:block;
    margin-top:6px;
    font-size:13px;
}

.wd-empty{
    padding:35px;
    color:#918984;
    text-align:center;
    font-size:12px;
}

@media(max-width:1050px){
    .wd-sidebar{width:72px}
    .wd-nav span,.wd-nav-section,.wd-logo small{display:none}
    .wd-main{margin-left:72px}
    .wd-topbar{left:72px}
    .wd-patrimoine-grid,.wd-profile-grid{grid-template-columns:1fr}
    .wd-overview{grid-template-columns:1fr 1fr}
}



/* ============================================================
   PROFIL INVESTISSEUR
   ============================================================ */

.wd-profile-edit{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-height:34px;
    padding:0 12px;
    border:1px solid var(--line);
    border-radius:7px;
    background:#fff;
    color:#514c48;
    text-decoration:none;
    font-size:10px;
    font-weight:750;
}

.wd-profile-edit:hover{
    border-color:#c8c2bd;
    color:#171514;
}

.wd-profile-new-grid{
    display:grid;
    grid-template-columns:.78fr 1.22fr;
    gap:16px;
    align-items:stretch;
}

.wd-profile-summary{
    background:#242424;
    color:#fff;
    border-radius:13px;
    padding:27px 28px 24px;
    border-top:3px solid var(--pink);
    box-shadow:0 8px 24px rgba(30,26,24,.06);
}

.wd-profile-summary-top{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    gap:20px;
}

.wd-profile-caption{
    color:#a49d98;
    font-size:9px;
    font-weight:800;
    letter-spacing:.14em;
    text-transform:uppercase;
}

.wd-profile-name{
    margin-top:8px;
    font-size:28px;
    line-height:1;
    font-weight:800;
    letter-spacing:-.04em;
}

.wd-profile-score{
    white-space:nowrap;
    display:flex;
    align-items:baseline;
    gap:3px;
}

.wd-profile-score strong{
    font-size:34px;
    line-height:1;
    letter-spacing:-.05em;
}

.wd-profile-score span{
    color:#8e8782;
    font-size:11px;
}

.wd-profile-scale{
    margin-top:30px;
}

.wd-profile-scale-track{
    height:5px;
    overflow:hidden;
    background:rgba(255,255,255,.10);
    border-radius:20px;
}

.wd-profile-scale-value{
    height:100%;
    min-width:4px;
    background:var(--pink);
    border-radius:20px;
}

.wd-profile-scale-labels{
    display:flex;
    justify-content:space-between;
    margin-top:8px;
    color:#837c78;
    font-size:8px;
    text-transform:uppercase;
    letter-spacing:.07em;
}

.wd-profile-reading{
    margin-top:27px;
    padding-top:20px;
    border-top:1px solid rgba(255,255,255,.08);
    color:#aaa39e;
    font-size:11px;
    line-height:1.65;
}

.wd-profile-date{
    margin-top:17px;
    color:#77716d;
    font-size:9px;
    text-transform:uppercase;
    letter-spacing:.08em;
}

.wd-profile-date strong{
    display:block;
    margin-top:4px;
    color:#c9c2be;
    font-size:10px;
    text-transform:none;
    letter-spacing:0;
}

.wd-profile-metrics{
    display:grid;
    grid-template-columns:repeat(2,minmax(0,1fr));
    gap:10px;
}

.wd-profile-metric{
    min-width:0;
    background:#fff;
    border:1px solid var(--line);
    border-radius:11px;
    padding:17px 18px 15px;
}

.wd-profile-metric-head{
    display:flex;
    justify-content:space-between;
    gap:15px;
    align-items:center;
}

.wd-profile-metric-head span{
    color:#79726d;
    font-size:9px;
    font-weight:800;
    text-transform:uppercase;
    letter-spacing:.09em;
}

.wd-profile-metric-head strong{
    color:#242424;
    font-size:17px;
    letter-spacing:-.025em;
}

.wd-profile-meter{
    height:3px;
    margin:15px 0 11px;
    overflow:hidden;
    background:var(--chart-track);
    border-radius:20px;
}

.wd-profile-meter i{
    display:block;
    height:100%;
    background:#242424;
    border-radius:20px;
}

.wd-profile-metric small{
    display:block;
    color:#45403d;
    font-size:11px;
    font-weight:650;
    line-height:1.4;
}

@media(max-width:1050px){
    .wd-profile-new-grid{
        grid-template-columns:1fr;
    }
}

@media(max-width:700px){
    .wd-profile-metrics{
        grid-template-columns:1fr;
    }

    .wd-profile-summary-top{
        flex-direction:column;
    }
}



/* ============================================================
   COMPATIBILITÉ DES PLACEMENTS
   ============================================================ */

.wd-compatibility-section{
    margin-top:30px;
}

.wd-compatibility-panel{
    background:#fff;
    border:1px solid var(--line);
    border-radius:12px;
    overflow:hidden;
}

.wd-compatibility-head{
    display:grid;
    grid-template-columns:.9fr 1.1fr;
    background:#faf9f7;
    border-bottom:1px solid var(--line);
}

.wd-compatibility-head>div{
    padding:13px 20px;
}

.wd-compatibility-head span{
    color:#918984;
    font-size:9px;
    font-weight:800;
    text-transform:uppercase;
    letter-spacing:.10em;
}

.wd-compatibility-row{
    position:relative;
    display:grid;
    grid-template-columns:.9fr 1.1fr;
    border-bottom:1px solid #eeeae7;
}

.wd-compatibility-row:last-child{
    border-bottom:0;
}

.wd-compatibility-row::before{
    content:"";
    position:absolute;
    top:14px;
    bottom:14px;
    left:0;
    width:3px;
    border-radius:0 3px 3px 0;
    background:#d7d1cc;
}

.wd-compatibility-compatible::before{
    background:#7d9c88;
}

.wd-compatibility-vigilance::before{
    background:#c79a62;
}

.wd-compatibility-non_adapte::before{
    background:#9d5f66;
}

.wd-compatibility-product,
.wd-compatibility-reading{
    padding:18px 20px;
}

.wd-compatibility-reading{
    border-left:1px solid #eeeae7;
}

.wd-compatibility-title{
    color:#242424;
    font-size:13px;
    font-weight:750;
}

.wd-compatibility-detail{
    margin-top:5px;
    color:#918984;
    font-size:10px;
    line-height:1.5;
}

.wd-compatibility-status{
    color:#242424;
    font-size:12px;
    font-weight:800;
}

.wd-compatibility-compatible .wd-compatibility-status{
    color:#556e5f;
}

.wd-compatibility-vigilance .wd-compatibility-status{
    color:#8c642f;
}

.wd-compatibility-non_adapte .wd-compatibility-status{
    color:#844950;
}

.wd-compatibility-reason{
    margin-top:5px;
    color:#77706c;
    font-size:10px;
    line-height:1.55;
}

.wd-compatibility-note{
    margin-top:10px;
    padding:14px 16px;
    border:1px solid #e6e1dc;
    border-radius:10px;
    background:#faf9f7;
    color:#837c77;
    font-size:9px;
    line-height:1.6;
}

@media(max-width:800px){

    .wd-compatibility-head{
        display:none;
    }

    .wd-compatibility-row{
        grid-template-columns:1fr;
    }

    .wd-compatibility-reading{
        border-left:0;
        border-top:1px solid #eeeae7;
        padding-top:14px;
    }
}



/* ============================================================
   FILTRES COMPATIBILITÉ
   ============================================================ */

.wd-compatibility-filters{
    display:flex;
    align-items:center;
    flex-wrap:wrap;
    gap:5px;
    margin-bottom:10px;
}

.wd-compatibility-filters button{
    appearance:none;
    border:1px solid var(--line);
    background:#fff;
    color:#77706c;
    border-radius:7px;
    padding:7px 11px;
    font-family:inherit;
    font-size:9px;
    font-weight:750;
    line-height:1;
    cursor:pointer;
    transition:
        background .15s ease,
        color .15s ease,
        border-color .15s ease;
}

.wd-compatibility-filters button:hover{
    border-color:#c8c2bd;
    color:#242424;
}

.wd-compatibility-filters button.active{
    background:#242424;
    border-color:#242424;
    color:#fff;
}



/* ============================================================
   PATRIMOINE PREMIUM
   ============================================================ */

.wd-patrimoine-premium{
    margin-top:28px;
}

.wd-patrimoine-kpis{
    display:grid;
    grid-template-columns:repeat(4,minmax(0,1fr));
    background:#fff;
    border:1px solid var(--line);
    border-radius:12px;
    overflow:hidden;
}

.wd-patrimoine-kpi{
    padding:20px 22px;
    border-right:1px solid var(--line);
}

.wd-patrimoine-kpi:last-child{
    border-right:0;
}

.wd-patrimoine-kpi span{
    display:block;
    color:#918984;
    font-size:9px;
    font-weight:800;
    text-transform:uppercase;
    letter-spacing:.1em;
}

.wd-patrimoine-kpi strong{
    display:block;
    margin-top:7px;
    color:#242424;
    font-size:24px;
    letter-spacing:-.035em;
}

.wd-patrimoine-kpi small{
    display:block;
    margin-top:5px;
    color:#9b948f;
    font-size:9px;
}

.wd-patrimoine-main-grid{
    display:grid;
    grid-template-columns:1fr 1fr 1fr;
    gap:14px;
    margin-top:14px;
}

.wd-patrimoine-card{
    padding:20px;
}

.wd-patrimoine-card-head{
    display:flex;
    align-items:center;
    justify-content:space-between;
    margin-bottom:18px;
}

.wd-patrimoine-card-head h3{
    margin:0;
    color:#242424;
    font-size:13px;
    font-weight:800;
    letter-spacing:-.01em;
}

.wd-patrimoine-card-head h3::after{
    content:"";
    display:block;
    width:28px;
    height:2px;
    margin-top:8px;
    background:#80A29A;
    border-radius:10px;
}

.wd-patrimoine-bars{
    display:flex;
    flex-direction:column;
    gap:18px;
}

.wd-patrimoine-bar-top{
    display:flex;
    justify-content:space-between;
    gap:15px;
    align-items:center;
}

.wd-patrimoine-bar-top span{
    color:#45403d;
    font-size:11px;
    font-weight:650;
}

.wd-patrimoine-bar-top strong{
    color:#242424;
    font-size:11px;
}

.wd-patrimoine-bar-track{
    height:4px;
    margin-top:9px;
    overflow:hidden;
    background:var(--chart-track);
    border-radius:20px;
}

.wd-patrimoine-bar-track i{
    display:block;
    height:100%;
    background:#242424;
    border-radius:20px;
}

.wd-patrimoine-bar-track i.is-passif{
    background:#D39480;
}

.wd-patrimoine-bar-item small{
    display:block;
    margin-top:5px;
    color:#aaa29d;
    font-size:8px;
    text-align:right;
}

.wd-patrimoine-structure{
    display:grid;
    grid-template-columns:170px 1fr;
    gap:24px;
    align-items:center;
}

.wd-patrimoine-donut{
    width:160px;
    height:160px;
    border-radius:50%;
    background:
        conic-gradient(
            #242424 0 calc(var(--fin) * 1%),
            #C9C3BF calc(var(--fin) * 1%) calc((var(--fin) + var(--nonfin)) * 1%),
            #D39480 calc((var(--fin) + var(--nonfin)) * 1%) 100%
        );
    position:relative;
}

.wd-patrimoine-donut::after{
    content:"";
    position:absolute;
    inset:26px;
    border-radius:50%;
    background:#fff;
}

.wd-patrimoine-donut-center{
    position:absolute;
    inset:0;
    display:grid;
    place-content:center;
    text-align:center;
    z-index:2;
}

.wd-patrimoine-donut-center strong{
    font-size:13px;
    color:#242424;
}

.wd-patrimoine-donut-center span{
    margin-top:3px;
    color:#9a938e;
    font-size:8px;
}

.wd-patrimoine-legend{
    display:flex;
    flex-direction:column;
    gap:14px;
}

.wd-patrimoine-legend>div{
    display:flex;
    gap:9px;
    align-items:flex-start;
}

.wd-patrimoine-legend .dot{
    width:8px;
    height:8px;
    margin-top:4px;
    border-radius:50%;
    flex:0 0 auto;
}

.dot-fin{background:#242424}
.dot-nonfin{background:#C9C3BF}
.dot-passif{background:#D39480}

.wd-patrimoine-legend p{
    margin:0;
}

.wd-patrimoine-legend strong{
    display:block;
    color:#45403d;
    font-size:10px;
}

.wd-patrimoine-legend small{
    display:block;
    margin-top:3px;
    color:#99918c;
    font-size:9px;
}

.wd-flux-chart{
    height:220px;
    display:flex;
    justify-content:center;
    align-items:flex-end;
    gap:28px;
    padding:12px 8px 0;
}

.wd-flux-item{
    height:100%;
    width:30%;
    display:flex;
    flex-direction:column;
    justify-content:flex-end;
    align-items:center;
}

.wd-flux-value{
    margin-bottom:7px;
    color:#242424;
    font-size:10px;
    font-weight:750;
}

.wd-flux-column{
    width:54px;
    height:150px;
    display:flex;
    align-items:flex-end;
}

.wd-flux-column i{
    display:block !important;
    width:100% !important;
    min-height:3px !important;
    background:#242424 !important;
    border-radius:5px 5px 0 0;
    opacity:1 !important;
    visibility:visible !important;
}

.wd-flux-column i.is-charge{
    background:#D39480 !important;
}

.wd-flux-column i.is-solde{
    background:#918984 !important;
}

.wd-flux-item span{
    margin-top:8px;
    color:#817a75;
    font-size:9px;
}

.wd-patrimoine-detail{
    margin-top:14px;
    padding:20px;
}

.wd-patrimoine-table-wrap{
    overflow-x:auto;
}

.wd-patrimoine-table{
    width:100%;
    border-collapse:collapse;
    min-width:900px;
}

.wd-patrimoine-table th{
    padding:11px 10px;
    border-bottom:1px solid var(--line);
    color:#918984;
    font-size:8px;
    font-weight:800;
    text-align:left;
    text-transform:uppercase;
    letter-spacing:.09em;
}

.wd-patrimoine-table td{
    padding:12px 10px;
    border-bottom:1px solid #f0ece9;
    color:#514c48;
    font-size:10px;
}

.wd-patrimoine-table tr:last-child td{
    border-bottom:0;
}

.wd-patrimoine-category{
    display:inline-flex;
    align-items:center;
    min-height:24px;
    padding:0 8px;
    border-radius:6px;
    background:#eef3f0;
    color:#526d60;
    font-size:8px;
    font-weight:750;
}

.wd-patrimoine-category.is-negative{
    background:#f7eee9;
    color:#9b6048;
}

.wd-patrimoine-amount{
    color:#242424 !important;
    font-weight:750;
    white-space:nowrap;
}

.wd-patrimoine-amount.is-negative{
    color:#b56f54 !important;
}

.wd-patrimoine-row-share{
    display:grid;
    grid-template-columns:1fr 42px;
    gap:8px;
    align-items:center;
}

.wd-patrimoine-row-track{
    height:3px;
    overflow:hidden;
    background:var(--chart-track);
    border-radius:20px;
}

.wd-patrimoine-row-track i{
    display:block;
    height:100%;
    background:#242424;
    border-radius:20px;
}

.wd-patrimoine-row-track i.is-negative{
    background:#D39480;
}

.wd-patrimoine-row-share span{
    color:#99918c;
    font-size:8px;
    text-align:right;
}

@media(max-width:1100px){
    .wd-patrimoine-kpis{
        grid-template-columns:1fr 1fr;
    }

    .wd-patrimoine-kpi:nth-child(2){
        border-right:0;
    }

    .wd-patrimoine-kpi:nth-child(-n+2){
        border-bottom:1px solid var(--line);
    }

    .wd-patrimoine-main-grid{
        grid-template-columns:1fr;
    }
}

@media(max-width:650px){
    .wd-patrimoine-kpis{
        grid-template-columns:1fr;
    }

    .wd-patrimoine-kpi{
        border-right:0;
        border-bottom:1px solid var(--line);
    }

    .wd-patrimoine-kpi:last-child{
        border-bottom:0;
    }

    .wd-patrimoine-structure{
        grid-template-columns:1fr;
        justify-items:center;
    }
}



/* ============================================================
   STRUCTURE PATRIMONIALE DÉTAILLÉE
   ============================================================ */

.wd-patrimoine-main-grid{
    grid-template-columns:.88fr 1.45fr .88fr;
}

.wd-patrimoine-structure-card{
    min-width:0;
}

.wd-asset-donuts{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:24px;
}

.wd-asset-donut-block{
    min-width:0;
}

.wd-asset-donut-block + .wd-asset-donut-block{
    border-left:1px solid #eeeae7;
    padding-left:24px;
}

.wd-asset-donut-title{
    margin-bottom:16px;
    color:#242424;
    font-size:11px;
    font-weight:800;
    text-align:center;
}

.wd-asset-donut-layout{
    display:grid;
    grid-template-columns:132px minmax(0,1fr);
    gap:17px;
    align-items:center;
}

.wd-asset-donut{
    position:relative;
    width:132px;
    height:132px;
    border-radius:50%;
}

.wd-asset-donut::after{
    content:"";
    position:absolute;
    inset:25px;
    border-radius:50%;
    background:#fff;
}

.wd-asset-donut-hole{
    position:absolute;
    inset:0;
    z-index:2;
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    text-align:center;
}

.wd-asset-donut-hole strong{
    max-width:90px;
    color:#242424;
    font-size:11px;
    line-height:1.2;
}

.wd-asset-donut-hole span{
    margin-top:3px;
    color:#a09994;
    font-size:7px;
    text-transform:uppercase;
    letter-spacing:.08em;
}

.wd-asset-donut-legend{
    display:flex;
    flex-direction:column;
    gap:10px;
    max-height:180px;
    overflow-y:auto;
    padding-right:4px;
}

.wd-asset-legend-row{
    display:grid;
    grid-template-columns:8px minmax(0,1fr);
    gap:8px;
    align-items:start;
}

.wd-asset-legend-dot{
    width:7px;
    height:7px;
    margin-top:3px;
    border-radius:50%;
}

.wd-asset-legend-row strong{
    display:block;
    color:#4b4642;
    font-size:9px;
    line-height:1.25;
    font-weight:700;
}

.wd-asset-legend-row small{
    display:block;
    margin-top:2px;
    color:#9b948f;
    font-size:8px;
    line-height:1.3;
}

.wd-asset-legend-empty{
    color:#9b948f;
    font-size:9px;
}

@media(max-width:1250px){

    .wd-patrimoine-main-grid{
        grid-template-columns:1fr;
    }

    .wd-asset-donut-layout{
        grid-template-columns:150px 1fr;
    }

}

@media(max-width:750px){

    .wd-asset-donuts{
        grid-template-columns:1fr;
    }

    .wd-asset-donut-block + .wd-asset-donut-block{
        border-left:0;
        border-top:1px solid #eeeae7;
        padding-left:0;
        padding-top:22px;
    }

}



/* ============================================================
   FILTRES DÉTAIL PATRIMOINE
   ============================================================ */

.wd-patrimoine-detail-head{
    align-items:center;
    gap:20px;
}

.wd-patrimoine-filters{
    display:inline-flex;
    align-items:center;
    gap:3px;
    padding:4px;
    margin-left:auto;
    background:#f1efed;
    border-radius:8px;
}

.wd-patrimoine-filters button{
    appearance:none;
    border:0;
    background:transparent;
    padding:7px 11px;
    border-radius:6px;
    color:#817a75;
    font-family:inherit;
    font-size:9px;
    font-weight:650;
    line-height:1;
    cursor:pointer;
    white-space:nowrap;
    transition:
        background .15s ease,
        color .15s ease,
        box-shadow .15s ease;
}

.wd-patrimoine-filters button:hover{
    color:#242424;
}

.wd-patrimoine-filters button.active{
    background:#fff;
    color:#242424;
    font-weight:800;
    box-shadow:0 1px 3px rgba(36,36,36,.08);
}

.wd-patrimoine-table tr.wd-filter-hidden{
    display:none;
}

@media(max-width:750px){

    .wd-patrimoine-detail-head{
        align-items:flex-start;
        flex-direction:column;
    }

    .wd-patrimoine-filters{
        margin-left:0;
        max-width:100%;
        overflow-x:auto;
    }

}



/* ============================================================
   TOOLTIPS GRAPHIQUES PATRIMOINE
   ============================================================ */

.wd-asset-donut[data-donut-tooltip]{
    cursor:pointer;
}

.wd-flux-item[data-flux-tooltip] .wd-flux-column i{
    cursor:pointer;
    transition:
        opacity .15s ease,
        transform .15s ease;
    transform-origin:bottom;
}

.wd-flux-item[data-flux-tooltip]:hover .wd-flux-column i{
    opacity:.88;
    transform:scaleX(1.04);
}

.wd-chart-tooltip{
    position:fixed;
    z-index:99999;
    min-width:150px;
    max-width:250px;
    padding:10px 12px;
    border-radius:8px;
    background:#242424;
    color:#fff;
    box-shadow:0 8px 25px rgba(0,0,0,.18);
    pointer-events:none;
    opacity:0;
    visibility:hidden;
    transform:translateY(4px);
    transition:
        opacity .1s ease,
        transform .1s ease,
        visibility .1s ease;
}

.wd-chart-tooltip.is-visible{
    opacity:1;
    visibility:visible;
    transform:translateY(0);
}

.wd-chart-tooltip-title{
    font-size:10px;
    line-height:1.35;
    font-weight:750;
}

.wd-chart-tooltip-value{
    margin-top:5px;
    font-size:13px;
    font-weight:800;
    letter-spacing:-.02em;
}

.wd-chart-tooltip-meta{
    margin-top:3px;
    color:#b9b2ad;
    font-size:9px;
}



/* ============================================================
   DEV : SIMULATION DE RÔLE
   ============================================================ */

.wd-view-role{
    margin:0 14px 12px;
    padding:12px;
    border-top:1px solid rgba(255,255,255,.08);
}

.wd-view-role-label{
    margin-bottom:7px;
    color:#817a75;
    font-size:8px;
    font-weight:800;
    text-transform:uppercase;
    letter-spacing:.1em;
}

.wd-view-role select{
    width:100%;
    min-height:34px;
    padding:0 9px;
    border:1px solid rgba(255,255,255,.12);
    border-radius:7px;
    background:#2d2d2d;
    color:#fff;
    font-family:inherit;
    font-size:10px;
}

[x-cloak]{display:none!important}

.wd-rdv-overlay{
    position:fixed;
    inset:0;
    z-index:9999;
    display:flex;
    align-items:center;
    justify-content:center;
    background:rgba(21,21,21,.45);
    color-scheme:light;
}

</style>
<div class="wd-wrap">

<section class="wd-hero {{ $dossierStatus['a_jour'] ? 'wd-hero-conforme' : 'wd-hero-non-conforme' }}">

<div class="wd-hero-main">

<div class="wd-identity">

<div class="wd-avatar">{{ $initiales }}</div>

<div>
<div class="wd-eyebrow">Client · portefeuille privé</div>
<h1>{{ $client->prenom }} {{ $client->nom }}</h1>
<div class="wd-hero-meta">
Dossier client · suivi patrimonial
</div>
</div>

</div>

<div class="wd-actions">

<a class="wd-btn"
href="{{ route('tenant.clients.edit', $client) }}">
Modifier
</a>

<button type="button" class="wd-btn" x-data x-on:click="$dispatch('ouvrir-mes-rdv')">
Mes rendez-vous
</button>

<button type="button" x-data x-on:click="$dispatch('ouvrir-rdv')" style="width:38px;height:38px;border-radius:50%;background:#f40087;color:#fff;border:none;font-size:19px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;line-height:1;flex:0 0 auto;">
+
</button>

</div>

</div>

<div
    x-data="rdvPopup(@js(route('tenant.rendez-vous.disponibilites')), @js(route('tenant.clients.rendez-vous.store', $client)))"
    x-on:ouvrir-rdv.window="ouvrir()"
>
<template x-teleport="body">
    <div
        class="wd-rdv-overlay"
        x-show="visible"
        x-cloak
    >
    <div style="background:#fff;border-radius:16px;padding:28px;max-width:440px;width:92%;max-height:86vh;overflow-y:auto;color-scheme:light;" x-on:click.outside="fermer()">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;">
            <h2 style="font-size:17px;font-weight:800;color:#151515;">Prendre rendez-vous</h2>
            <button type="button" x-on:click="fermer()" style="background:none;border:none;font-size:20px;cursor:pointer;color:#817b76;">&times;</button>
        </div>

        <label style="display:block;font-size:11px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:#9a928d;margin-bottom:8px;">Date</label>
        <input type="date" x-model="date" x-on:change="chargerCreneaux()" style="width:100%;border:1px solid #ded9d4;border-radius:7px;padding:9px 11px;font-size:13px;margin-bottom:18px;color:#151515;color-scheme:light;background:#fff;">

        <template x-if="chargement">
            <p style="font-size:12.5px;color:#817b76;">Chargement des créneaux…</p>
        </template>

        <template x-if="!chargement && date && creneaux.length === 0">
            <p style="font-size:12.5px;color:#817b76;">Aucun créneau disponible ce jour-là.</p>
        </template>

        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-bottom:18px;">
            <template x-for="c in creneaux" :key="c.start">
                <button
                    type="button"
                    x-on:click="creneauChoisi = c"
                    :style="creneauChoisi && creneauChoisi.start === c.start ? 'background:#f40087;color:#fff;border-color:#f40087;' : 'background:#fff;color:#151515;border-color:#ded9d4;'"
                    style="border:1px solid #ded9d4;border-radius:8px;padding:8px 4px;font-size:12.5px;font-weight:600;cursor:pointer;"
                    x-text="new Date(c.start).toLocaleTimeString('fr-FR', {hour:'2-digit', minute:'2-digit'})"
                ></button>
            </template>
        </div>

        <form x-on:submit.prevent="reserver()">
            <label style="display:block;font-size:11px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:#9a928d;margin-bottom:8px;">Format</label>
            <select x-model="format" style="width:100%;border:1px solid #ded9d4;border-radius:7px;padding:9px 11px;font-size:13px;margin-bottom:18px;background:#fff;color:#151515;color-scheme:light;">
                <option value="">Choisir</option>
                <option value="visioconference">Visioconférence</option>
                <option value="telephone">Téléphone</option>
                <option value="agence">Agence</option>
                <option value="domicile">Domicile</option>
            </select>

            <label style="display:block;font-size:11px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:#9a928d;margin-bottom:8px;">Votre demande</label>
            <select x-model="sujet" style="width:100%;border:1px solid #ded9d4;border-radius:7px;padding:9px 11px;font-size:13px;margin-bottom:18px;background:#fff;color:#151515;color-scheme:light;">
                <option value="">Choisir</option>
                <option value="point_etape">Point d'étape</option>
                <option value="bilan_patrimonial">Bilan patrimonial</option>
                <option value="signature_document">Signature de document</option>
                <option value="suivi_portefeuille">Suivi de portefeuille</option>
                <option value="autre">Autre</option>
            </select>

            <label style="display:block;font-size:11px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:#9a928d;margin-bottom:8px;">Notes (optionnel)</label>
            <textarea x-model="notes" rows="2" style="width:100%;border:1px solid #ded9d4;border-radius:7px;padding:9px 11px;font-size:13px;margin-bottom:18px;resize:vertical;color:#151515;color-scheme:light;background:#fff;"></textarea>

            <button type="submit" :disabled="!creneauChoisi" style="width:100%;background:#1b1716;color:#fff;border:none;border-radius:999px;padding:12px;font-size:13px;font-weight:700;cursor:pointer;opacity:1;" x-bind:style="!creneauChoisi ? 'width:100%;background:#ded9d4;color:#817b76;border:none;border-radius:999px;padding:12px;font-size:13px;font-weight:700;cursor:not-allowed;' : 'width:100%;background:#1b1716;color:#fff;border:none;border-radius:999px;padding:12px;font-size:13px;font-weight:700;cursor:pointer;'">
                Confirmer le rendez-vous
            </button>
        </form>
    </div>
    </div>
</template>
</div>

<script>
function rdvLigne(data, urlDisponibilites, urlDecaler) {
    return {
        ...data,
        open: false,
        decalage: false,
        nouvelleDate: '',
        creneaux: [],
        creneauChoisi: null,
        chargement: false,
        toggle() {
            this.open = !this.open;
        },
        formatLabel() {
            const labels = {
                visioconference: 'Visioconférence',
                telephone: 'Téléphone',
                agence: 'Agence',
                domicile: 'Domicile',
            };
            return labels[this.format] || null;
        },
        sujetLabel() {
            const labels = {
                point_etape: "Point d'étape",
                bilan_patrimonial: 'Bilan patrimonial',
                signature_document: 'Signature de document',
                suivi_portefeuille: 'Suivi de portefeuille',
                autre: 'Autre',
            };
            return labels[this.sujet] || null;
        },
        ouvrirDecalage() {
            this.decalage = true;
            this.nouvelleDate = new Date().toISOString().slice(0, 10);
            this.chargerCreneaux();
        },
        chargerCreneaux() {
            if (!this.nouvelleDate) return;
            this.chargement = true;
            this.creneauChoisi = null;
            fetch(urlDisponibilites + '?date=' + this.nouvelleDate + '&duree=' + this.dureeMinutes + '&exclure=' + this.id)
                .then(r => r.json())
                .then(d => { this.creneaux = d.creneaux || []; })
                .finally(() => { this.chargement = false; });
        },
        confirmerDecalage() {
            if (!this.creneauChoisi) return;
            fetch(urlDecaler, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    starts_at: this.creneauChoisi.start,
                    ends_at: this.creneauChoisi.end,
                }),
            }).then(() => { window.location.reload(); });
        },
    };
}

function rdvPopup(urlDisponibilites, urlStore) {
    return {
        visible: false,
        date: '',
        creneaux: [],
        creneauChoisi: null,
        format: '',
        sujet: '',
        notes: '',
        chargement: false,
        ouvrir() {
            this.visible = true;
            this.date = new Date().toISOString().slice(0, 10);
            this.chargerCreneaux();
        },
        fermer() {
            this.visible = false;
        },
        chargerCreneaux() {
            if (!this.date) return;
            this.chargement = true;
            this.creneauChoisi = null;
            fetch(urlDisponibilites + '?date=' + this.date)
                .then(r => r.json())
                .then(data => { this.creneaux = data.creneaux || []; })
                .finally(() => { this.chargement = false; });
        },
        reserver() {
            if (!this.creneauChoisi) return;
            fetch(urlStore, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    starts_at: this.creneauChoisi.start,
                    ends_at: this.creneauChoisi.end,
                    format: this.format,
                    sujet: this.sujet,
                    notes: this.notes,
                }),
            }).then(() => {
                this.visible = false;
                window.location.reload();
            });
        },
    };
}
</script>

<div class="wd-hero-foot">

<div>
<span>Téléphone</span>
<strong>{{ $formatTelephone($client->telephone_mobile) }}</strong>
</div>

<div>
<span>Email</span>
<strong>{{ $client->email ?: '-' }}</strong>
</div>

@if($viewRole === 'courtier')

<div>
<span>Conseiller</span>
<strong>{{ $client->conseiller?->name ?: '-' }}</strong>
</div>

@elseif($viewRole === 'conseiller' && $client->apporteur)

<div>
<span>Apporteur</span>
<strong>{{ $client->apporteur->name }}</strong>
</div>

@endif

<div>
<span>Dernière mise à jour</span>
<strong>{{ $client->updated_at?->translatedFormat('d F Y') }}</strong>
</div>

</div>

</section>

<div x-data="{ visible: false }" x-on:ouvrir-mes-rdv.window="visible = true">
<template x-teleport="body">
    <div class="wd-rdv-overlay" x-show="visible" x-cloak>
        <div style="background:#fff;border-radius:16px;padding:28px;max-width:460px;width:92%;max-height:80vh;overflow-y:auto;" x-on:click.outside="visible = false">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;">
                <h2 style="font-size:17px;font-weight:800;color:#151515;">Mes rendez-vous</h2>
                <button type="button" x-on:click="visible = false" style="background:none;border:none;font-size:20px;cursor:pointer;color:#817b76;">&times;</button>
            </div>

            @forelse($rendezVousAVenir as $rdv)
            <div
                x-data="rdvLigne(@js([
                    'id' => $rdv->id,
                    'format' => $rdv->format,
                    'sujet' => $rdv->sujet,
                    'notes' => $rdv->notes,
                    'dureeMinutes' => $rdv->starts_at->diffInMinutes($rdv->ends_at),
                ]), @js(route('tenant.rendez-vous.disponibilites')), @js(route('tenant.rendez-vous.decaler', $rdv)))"
                style="padding:12px 0;{{ !$loop->last ? 'border-bottom:1px solid #ded9d4;' : '' }}"
            >
                <div style="display:flex;justify-content:space-between;align-items:center;cursor:pointer;" x-on:click="toggle()">
                    <div>
                        <strong style="font-size:13.5px;color:#151515;">{{ $rdv->titre }}</strong>
                        <span style="display:block;font-size:12px;color:#817b76;margin-top:2px;">{{ $rdv->starts_at->translatedFormat('d F Y') }} à {{ $rdv->starts_at->format('H:i') }}<template x-if="sujetLabel()"><span x-text="' · ' + sujetLabel()"></span></template></span>
                    </div>
                    <span style="font-size:11px;color:#9a928d;" x-text="open ? '▲' : '▼'"></span>
                </div>

                <div x-show="open" x-cloak style="margin-top:12px;padding:14px;background:#f7f5f3;border-radius:10px;">
                    <template x-if="formatLabel()">
                        <p style="font-size:12.5px;color:#151515;margin:0 0 6px;"><strong>Format :</strong> <span x-text="formatLabel()"></span></p>
                    </template>
                    <template x-if="notes">
                        <p style="font-size:12.5px;color:#151515;margin:0 0 12px;"><strong>Notes :</strong> <span x-text="notes"></span></p>
                    </template>

                    <template x-if="!decalage">
                        <div style="display:flex;gap:8px;">
                            <button type="button" x-on:click="ouvrirDecalage()" style="background:none;border:1px solid #ded9d4;border-radius:999px;padding:6px 14px;font-size:12px;font-weight:600;color:#151515;cursor:pointer;">Décaler</button>
                            <form method="POST" action="{{ route('tenant.rendez-vous.annuler', $rdv) }}" onsubmit="return confirm('Annuler ce rendez-vous ?');">
                                @csrf
                                <button type="submit" style="background:none;border:1px solid #ded9d4;border-radius:999px;padding:6px 14px;font-size:12px;font-weight:600;color:#b94d4d;cursor:pointer;">Annuler</button>
                            </form>
                        </div>
                    </template>

                    <template x-if="decalage">
                        <div>
                            <label style="display:block;font-size:10.5px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;color:#9a928d;margin-bottom:6px;">Nouvelle date</label>
                            <input type="date" x-model="nouvelleDate" x-on:change="chargerCreneaux()" style="width:100%;border:1px solid #ded9d4;border-radius:7px;padding:8px 10px;font-size:13px;margin-bottom:10px;color:#151515;color-scheme:light;background:#fff;">

                            <template x-if="chargement">
                                <p style="font-size:12px;color:#817b76;">Chargement…</p>
                            </template>
                            <template x-if="!chargement && nouvelleDate && creneaux.length === 0">
                                <p style="font-size:12px;color:#817b76;">Aucun créneau disponible ce jour-là.</p>
                            </template>

                            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:6px;margin-bottom:10px;">
                                <template x-for="c in creneaux" :key="c.start">
                                    <button
                                        type="button"
                                        x-on:click="creneauChoisi = c"
                                        :style="creneauChoisi && creneauChoisi.start === c.start ? 'background:#f40087;color:#fff;border-color:#f40087;' : 'background:#fff;color:#151515;border-color:#ded9d4;'"
                                        style="border:1px solid #ded9d4;border-radius:7px;padding:6px 4px;font-size:12px;font-weight:600;cursor:pointer;"
                                        x-text="new Date(c.start).toLocaleTimeString('fr-FR', {hour:'2-digit', minute:'2-digit'})"
                                    ></button>
                                </template>
                            </div>

                            <div style="display:flex;gap:8px;">
                                <button type="button" x-on:click="confirmerDecalage()" :disabled="!creneauChoisi" style="background:#1b1716;color:#fff;border:none;border-radius:999px;padding:7px 16px;font-size:12px;font-weight:700;cursor:pointer;">Confirmer le décalage</button>
                                <button type="button" x-on:click="decalage = false" style="background:none;border:1px solid #ded9d4;border-radius:999px;padding:7px 16px;font-size:12px;font-weight:600;color:#817b76;cursor:pointer;">Annuler le décalage</button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
            @empty
            <p style="font-size:12.5px;color:#817b76;">Aucun rendez-vous à venir.</p>
            @endforelse
        </div>
    </div>
</template>
</div>

<nav class="wd-tabs">

<a href="{{ route('tenant.dashboard') }}">
Tableau de bord
</a>

<a href="{{ route('tenant.clients.show', $client) }}"
class="active">
Profil
</a>

<a href="{{ route('tenant.clients.aide-decision', $client) }}">
Analyse
</a>

<a href="{{ route('tenant.clients.mission', $client) }}">
Mission
</a>

<a href="{{ route('tenant.clients.contrats-clients', $client) }}">
Contrat
</a>

<a href="{{ route('tenant.clients.conformites-clients', $client) }}">
Archives
</a>

</nav>

<nav class="wd-subtabs">

<a href="#kyc">KYC</a>

<a href="#patrimoine">Patrimoine</a>

<a href="#investisseur">Investisseur</a>

<button type="button" class="wd-btn-dark wd-subtabs-action" data-dossier-trigger>
{{ $dossierComplet ? 'Modifier les formulaires' : 'Compléter les formulaires' }}
</button>

</nav>

<div class="wd-newaccount-overlay" data-dossier-modal hidden>
<div class="wd-newaccount-modal">
<div class="wd-newaccount-head">
<div>
<div class="wd-eyebrow">Dossier client</div>
<h3>{{ $dossierComplet ? 'Modifier les formulaires' : 'Compléter les formulaires' }}</h3>
</div>
<button type="button" class="wd-newaccount-close" data-dossier-close aria-label="Fermer">&times;</button>
</div>
<div class="wd-newaccount-choices">
<a href="{{ route('tenant.clients.kyc.edit', $client) }}" class="wd-newaccount-choice">
<span class="wd-newaccount-choice-title">KYC</span>
<span class="wd-newaccount-choice-desc">Recueil de connaissance client.</span>
</a>
<a href="{{ route('tenant.clients.patrimoine.edit', $client) }}" class="wd-newaccount-choice">
<span class="wd-newaccount-choice-title">Patrimoine</span>
<span class="wd-newaccount-choice-desc">Analyse patrimoniale du client.</span>
</a>
<a href="{{ route('tenant.clients.profil.edit', $client) }}" class="wd-newaccount-choice">
<span class="wd-newaccount-choice-title">Profil investisseur</span>
<span class="wd-newaccount-choice-desc">Profil de risque et objectifs.</span>
</a>
</div>
</div>
</div>





<section class="wd-section wd-kyc-section" id="kyc">

<div class="wd-section-head">
<div>
<div class="wd-eyebrow">KYC</div>
<h2>Recueil de connaissance</h2>
</div>
</div>

<div class="wd-panel wd-kyc-progress">
<div class="wd-bar-row">
<div class="wd-bar-label">Complétion du dossier</div>
<div class="wd-bar-track"><div class="wd-bar-fill" style="width: {{ $kycCompletion }}%"></div></div>
<div class="wd-bar-value">{{ $kycCompletion }} %</div>
</div>
</div>

</section>

<div class="wd-newaccount-overlay" data-donnees-modal hidden>
<div class="wd-newaccount-modal wd-donnees-modal">
<div class="wd-donnees-head-dark">
<div class="wd-donnees-head-dark-icon"><svg viewBox="0 0 24 24"><path d="M6 2h9l5 5v15H6z"/><path d="M14 2v6h6"/></svg></div>
<div class="wd-donnees-head-dark-text">
<h3>KYC</h3>
<p>Toutes les données KYC</p>
</div>
<button type="button" class="wd-donnees-close-dark" data-donnees-close aria-label="Fermer">&times;</button>
</div>

<div class="wd-donnees-grid">
@foreach($donneesClient as $categorie => $lignes)
<div class="wd-donnees-card">
<h4>{{ $categorie }}</h4>
<div class="wd-donnees-pills">
@foreach($lignes as $ligne)
<button type="button" class="wd-donnees-pill" data-copy="{{ $ligne }}">
<span>{{ $ligne }}</span>
<svg viewBox="0 0 24 24" width="12" height="12"><rect x="9" y="9" width="11" height="11" rx="2" stroke="currentColor" fill="none" stroke-width="2"/><path d="M5 15V5a2 2 0 0 1 2-2h10" stroke="currentColor" fill="none" stroke-width="2"/></svg>
</button>
@endforeach
</div>
</div>
@endforeach
</div>

</div>
</div>

<div class="wd-newaccount-overlay" data-patrimoine-donnees-modal hidden>
<div class="wd-newaccount-modal wd-donnees-modal">
<div class="wd-donnees-head-dark">
<div class="wd-donnees-head-dark-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v10M9 9.5c0-1.4 1.3-2.5 3-2.5s3 1.1 3 2.5-1.3 2-3 2.5-3 1.1-3 2.5 1.3 2.5 3 2.5 3-1.1 3-2.5"/></svg></div>
<div class="wd-donnees-head-dark-text">
<h3>Patrimoine</h3>
<p>Toutes les données patrimoine</p>
</div>
<button type="button" class="wd-donnees-close-dark" data-patrimoine-donnees-close aria-label="Fermer">&times;</button>
</div>

<div class="wd-donnees-grid">
@foreach($donneesPatrimoine as $categorie => $lignes)
<div class="wd-donnees-card">
<h4>{{ $categorie }}</h4>
<div class="wd-donnees-pills">
@foreach($lignes as $ligne)
<button type="button" class="wd-donnees-pill" data-copy="{{ $ligne }}">
<span>{{ $ligne }}</span>
<svg viewBox="0 0 24 24" width="12" height="12"><rect x="9" y="9" width="11" height="11" rx="2" stroke="currentColor" fill="none" stroke-width="2"/><path d="M5 15V5a2 2 0 0 1 2-2h10" stroke="currentColor" fill="none" stroke-width="2"/></svg>
</button>
@endforeach
</div>
</div>
@endforeach
</div>

</div>
</div>

<div class="wd-newaccount-overlay" data-investisseur-donnees-modal hidden>
<div class="wd-newaccount-modal wd-donnees-modal">
<div class="wd-donnees-head-dark">
<div class="wd-donnees-head-dark-icon"><svg viewBox="0 0 24 24"><path d="M4 20V10M12 20V4M20 20v-7"/></svg></div>
<div class="wd-donnees-head-dark-text">
<h3>Investisseur</h3>
<p>Toutes les données profil investisseur</p>
</div>
<button type="button" class="wd-donnees-close-dark" data-investisseur-donnees-close aria-label="Fermer">&times;</button>
</div>

<div class="wd-donnees-grid">
@foreach($donneesInvestisseur as $categorie => $lignes)
<div class="wd-donnees-card">
<h4>{{ $categorie }}</h4>
<div class="wd-donnees-pills">
@foreach($lignes as $ligne)
<button type="button" class="wd-donnees-pill" data-copy="{{ $ligne }}">
<span>{{ $ligne }}</span>
<svg viewBox="0 0 24 24" width="12" height="12"><rect x="9" y="9" width="11" height="11" rx="2" stroke="currentColor" fill="none" stroke-width="2"/><path d="M5 15V5a2 2 0 0 1 2-2h10" stroke="currentColor" fill="none" stroke-width="2"/></svg>
</button>
@endforeach
</div>
</div>
@endforeach
</div>

</div>
</div>

<section class="wd-section wd-patrimoine-premium" id="patrimoine">

<div class="wd-section-head">
    <div>
        <div class="wd-eyebrow">Patrimoine</div>
        <h2>Vue patrimoniale</h2>
    </div>
    <div style="display:flex;gap:10px;">
    <a
    href="{{ route('tenant.clients.patrimoine.edit', $client) }}"
    class="wd-btn-dark">
    Modifier le patrimoine
    </a>
    </div>
</div>

<div class="wd-patrimoine-kpis">

    <div class="wd-patrimoine-kpi">
        <span>Actifs totaux</span>
        <strong>{{ $formatEuro($actifs) }}</strong>
        <small>100 % du patrimoine brut</small>
    </div>

    <div class="wd-patrimoine-kpi">
        <span>Passifs totaux</span>
        <strong>{{ $formatEuro($passifs) }}</strong>
        <small>{{ $actifs > 0 ? number_format(($passifs / $actifs) * 100, 1, ',', ' ') : 0 }} % des actifs</small>
    </div>

    <div class="wd-patrimoine-kpi">
        <span>Patrimoine net</span>
        <strong>{{ $formatEuro($patrimoineNet) }}</strong>
        <small>{{ $actifs > 0 ? number_format(($patrimoineNet / $actifs) * 100, 1, ',', ' ') : 0 }} % net</small>
    </div>

    <div class="wd-patrimoine-kpi">
        <span>Solde annuel</span>
        <strong>{{ $formatEuro($soldeAnnuel) }}</strong>
        <small>Revenus moins charges</small>
    </div>

</div>

<div class="wd-patrimoine-main-grid">

    <div class="wd-panel wd-patrimoine-card">

        <div class="wd-patrimoine-card-head">
            <h3>Répartition par nature</h3>
        </div>

        <div class="wd-patrimoine-bars">

            @php
                $financierPct = $actifs > 0 ? ($actifsFinanciers / $actifs) * 100 : 0;
                $nonFinancierPct = $actifs > 0 ? ($actifsNonFinanciers / $actifs) * 100 : 0;
                $passifPct = $actifs > 0 ? ($passifs / $actifs) * 100 : 0;
                $netPct = $actifs > 0 ? ($patrimoineNet / $actifs) * 100 : 0;
            @endphp

            <div class="wd-patrimoine-bar-item">
                <div class="wd-patrimoine-bar-top">
                    <span>Actifs financiers</span>
                    <strong>{{ $formatEuro($actifsFinanciers) }}</strong>
                </div>
                <div class="wd-patrimoine-bar-track">
                    <i style="width:{{ min(100, $financierPct) }}%"></i>
                </div>
                <small>{{ number_format($financierPct, 1, ',', ' ') }} %</small>
            </div>

            <div class="wd-patrimoine-bar-item">
                <div class="wd-patrimoine-bar-top">
                    <span>Actifs non financiers</span>
                    <strong>{{ $formatEuro($actifsNonFinanciers) }}</strong>
                </div>
                <div class="wd-patrimoine-bar-track">
                    <i style="width:{{ min(100, $nonFinancierPct) }}%"></i>
                </div>
                <small>{{ number_format($nonFinancierPct, 1, ',', ' ') }} %</small>
            </div>

            <div class="wd-patrimoine-bar-item">
                <div class="wd-patrimoine-bar-top">
                    <span>Passifs</span>
                    <strong>{{ $formatEuro($passifs) }}</strong>
                </div>
                <div class="wd-patrimoine-bar-track">
                    <i class="is-passif" style="width:{{ min(100, $passifPct) }}%"></i>
                </div>
                <small>{{ number_format($passifPct, 1, ',', ' ') }} %</small>
            </div>

            <div class="wd-patrimoine-bar-item">
                <div class="wd-patrimoine-bar-top">
                    <span>Patrimoine net</span>
                    <strong>{{ $formatEuro($patrimoineNet) }}</strong>
                </div>
                <div class="wd-patrimoine-bar-track">
                    <i style="width:{{ min(100, $netPct) }}%"></i>
                </div>
                <small>{{ number_format($netPct, 1, ',', ' ') }} %</small>
            </div>

        </div>

    </div>


    <div class="wd-panel wd-patrimoine-card wd-patrimoine-structure-card">

        <div class="wd-patrimoine-card-head">
            <h3>Structure du patrimoine</h3>
        </div>

        @php

            $labelsFinanciers = config('patrimoine.natures.actif_financier', []);
            $labelsNonFinanciers = config('patrimoine.natures.actif_non_financier', []);

            $repartitionFinanciere = $client->patrimoineElements
                ->where('categorie', 'actif_financier')
                ->groupBy('nature')
                ->map(fn($items) => (float) $items->sum('montant'))
                ->filter(fn($value) => $value > 0)
                ->sortDesc();

            $repartitionNonFinanciere = $client->patrimoineElements
                ->where('categorie', 'actif_non_financier')
                ->groupBy('nature')
                ->map(fn($items) => (float) $items->sum('montant'))
                ->filter(fn($value) => $value > 0)
                ->sortDesc();

            $paletteFinanciere = [
                '#242424',
                '#5A5653',
                '#918984',
                '#AAA39E',
                '#C9C3BF',
                '#E8E4E1',
                '#F50087',
                '#6F6965',
            ];

            $paletteNonFinanciere = [
                '#918984',
                '#AAA39E',
                '#C0BBB7',
                '#C9C3BF',
                '#D8D4D1',
                '#E8E4E1',
                '#6F6965',
                '#F50087',
            ];

            $buildGradient = function ($items, $total, $palette) {

                if ($items->isEmpty() || $total <= 0) {
                    return '#ebe8e5 0% 100%';
                }

                $cursor = 0;
                $parts = [];

                foreach ($items->values() as $index => $montant) {

                    $pct = ((float) $montant / $total) * 100;

                    $start = $cursor;
                    $cursor += $pct;

                    $color = $palette[$index % count($palette)];

                    $parts[] =
                        $color . ' ' .
                        number_format($start, 4, '.', '') . '% ' .
                        number_format($cursor, 4, '.', '') . '%';
                }

                return implode(', ', $parts);
            };

            $gradientFinancier = $buildGradient(
                $repartitionFinanciere,
                $actifsFinanciers,
                $paletteFinanciere
            );

            $gradientNonFinancier = $buildGradient(
                $repartitionNonFinanciere,
                $actifsNonFinanciers,
                $paletteNonFinanciere
            );



            $segmentsNonFinanciers = $repartitionNonFinanciere
                ->map(function ($montant, $nature) use ($labelsNonFinanciers, $actifsNonFinanciers) {
                    return [
                        'label' => $labelsNonFinanciers[$nature] ?? $nature ?? 'Non renseigné',
                        'montant' => (float) $montant,
                        'pct' => $actifsNonFinanciers > 0
                            ? ((float) $montant / $actifsNonFinanciers) * 100
                            : 0,
                    ];
                })
                ->values()
                ->all();

            $segmentsFinanciers = $repartitionFinanciere
                ->map(function ($montant, $nature) use ($labelsFinanciers, $actifsFinanciers) {
                    return [
                        'label' => $labelsFinanciers[$nature] ?? $nature ?? 'Non renseigné',
                        'montant' => (float) $montant,
                        'pct' => $actifsFinanciers > 0
                            ? ((float) $montant / $actifsFinanciers) * 100
                            : 0,
                    ];
                })
                ->values()
                ->all();

        @endphp


        <div class="wd-asset-donuts">

            {{-- ACTIFS NON FINANCIERS --}}
            <div class="wd-asset-donut-block">

                <div class="wd-asset-donut-title">
                    Actifs non financiers
                </div>

                <div class="wd-asset-donut-layout">

                    <div
                        class="wd-asset-donut"
                        data-donut-tooltip
                        data-segments="{{ json_encode($segmentsNonFinanciers, JSON_UNESCAPED_UNICODE) }}"
                        style="background:conic-gradient({{ $gradientNonFinancier }})"
                    >
                        <div class="wd-asset-donut-hole">
                            <strong>{{ $formatEuro($actifsNonFinanciers) }}</strong>
                            <span>Total</span>
                        </div>
                    </div>

                    <div class="wd-asset-donut-legend">

                        @forelse($repartitionNonFinanciere as $nature => $montant)

                            @php
                                $index = array_search(
                                    $nature,
                                    $repartitionNonFinanciere->keys()->all()
                                );

                                $color = $paletteNonFinanciere[
                                    $index % count($paletteNonFinanciere)
                                ];

                                $labelNature =
                                    $labelsNonFinanciers[$nature]
                                    ?? $nature
                                    ?? 'Non renseigné';

                                $pctNature = $actifsNonFinanciers > 0
                                    ? ($montant / $actifsNonFinanciers) * 100
                                    : 0;
                            @endphp

                            <div class="wd-asset-legend-row">

                                <span
                                    class="wd-asset-legend-dot"
                                    style="background:{{ $color }}"
                                ></span>

                                <div>
                                    <strong>{{ $labelNature }}</strong>

                                    <small>
                                        {{ $formatEuro($montant) }}
                                        ·
                                        {{ number_format($pctNature, 1, ',', ' ') }} %
                                    </small>
                                </div>

                            </div>

                        @empty

                            <div class="wd-asset-legend-empty">
                                Aucun actif renseigné
                            </div>

                        @endforelse

                    </div>

                </div>

            </div>


            {{-- ACTIFS FINANCIERS --}}
            <div class="wd-asset-donut-block">

                <div class="wd-asset-donut-title">
                    Actifs financiers
                </div>

                <div class="wd-asset-donut-layout">

                    <div
                        class="wd-asset-donut"
                        data-donut-tooltip
                        data-segments="{{ json_encode($segmentsFinanciers, JSON_UNESCAPED_UNICODE) }}"
                        style="background:conic-gradient({{ $gradientFinancier }})"
                    >
                        <div class="wd-asset-donut-hole">
                            <strong>{{ $formatEuro($actifsFinanciers) }}</strong>
                            <span>Total</span>
                        </div>
                    </div>

                    <div class="wd-asset-donut-legend">

                        @forelse($repartitionFinanciere as $nature => $montant)

                            @php
                                $index = array_search(
                                    $nature,
                                    $repartitionFinanciere->keys()->all()
                                );

                                $color = $paletteFinanciere[
                                    $index % count($paletteFinanciere)
                                ];

                                $labelNature =
                                    $labelsFinanciers[$nature]
                                    ?? $nature
                                    ?? 'Non renseigné';

                                $pctNature = $actifsFinanciers > 0
                                    ? ($montant / $actifsFinanciers) * 100
                                    : 0;
                            @endphp

                            <div class="wd-asset-legend-row">

                                <span
                                    class="wd-asset-legend-dot"
                                    style="background:{{ $color }}"
                                ></span>

                                <div>
                                    <strong>{{ $labelNature }}</strong>

                                    <small>
                                        {{ $formatEuro($montant) }}
                                        ·
                                        {{ number_format($pctNature, 1, ',', ' ') }} %
                                    </small>
                                </div>

                            </div>

                        @empty

                            <div class="wd-asset-legend-empty">
                                Aucun actif renseigné
                            </div>

                        @endforelse

                    </div>

                </div>

            </div>

        </div>

    </div>


<div class="wd-panel wd-patrimoine-card">

        <div class="wd-patrimoine-card-head">
            <h3>Flux annuels</h3>
        </div>

        @php
            $fluxMax = max(1, $revenus, $charges, abs($soldeAnnuel));
        @endphp

        <div class="wd-flux-chart">

            <div class="wd-flux-item" data-flux-tooltip>
                <div class="wd-flux-value">{{ $formatEuro($revenus) }}</div>
                <div class="wd-flux-column">
                    <i style="height:{{ ($revenus / $fluxMax) * 100 }}%"></i>
                </div>
                <span>Revenus</span>
            </div>

            <div class="wd-flux-item" data-flux-tooltip>
                <div class="wd-flux-value">{{ $formatEuro($charges) }}</div>
                <div class="wd-flux-column">
                    <i class="is-charge" style="height:{{ ($charges / $fluxMax) * 100 }}%"></i>
                </div>
                <span>Charges</span>
            </div>

            <div class="wd-flux-item" data-flux-tooltip>
                <div class="wd-flux-value">{{ $formatEuro($soldeAnnuel) }}</div>
                <div class="wd-flux-column">
                    <i class="is-solde" style="height:{{ (abs($soldeAnnuel) / $fluxMax) * 100 }}%"></i>
                </div>
                <span>Solde annuel</span>
            </div>

        </div>

    </div>

</div>


<div class="wd-panel wd-patrimoine-detail">

    <div class="wd-patrimoine-card-head wd-patrimoine-detail-head">
        <h3>Détail du patrimoine</h3>

        <div class="wd-patrimoine-filters" id="wdPatrimoineFilters">

            <button
                type="button"
                class="active"
                data-filter="all"
            >
                Tous
            </button>

            <button
                type="button"
                data-filter="actif_financier"
            >
                Actifs financiers
            </button>

            <button
                type="button"
                data-filter="actif_non_financier"
            >
                Actifs non financiers
            </button>

            <button
                type="button"
                data-filter="passif"
            >
                Passifs
            </button>

        </div>
    </div>

    <div class="wd-patrimoine-table-wrap">
        <table class="wd-patrimoine-table">

            <thead>
                <tr>
                    <th>Catégorie</th>
                    <th>Nature</th>
                    <th>Désignation</th>
                    <th>Mode de détention</th>
                    <th>Montant</th>
                    <th>Répartition</th>
                </tr>
            </thead>

            <tbody>
            @foreach($client->patrimoineElements as $element)

                @php
                    $isNegative = in_array($element->categorie, ['passif', 'charge']);
                    $base = $actifs > 0 ? $actifs : 1;
                    $pct = min(100, ((float) $element->montant / $base) * 100);

                    $categorieLabel = match($element->categorie) {
                        'actif_financier' => 'Actif financier',
                        'actif_non_financier' => 'Actif non financier',
                        'passif' => 'Passif',
                        'revenu' => 'Revenu',
                        'charge' => 'Charge',
                        default => ucfirst(str_replace('_', ' ', $element->categorie)),
                    };
                @endphp

                <tr data-patrimoine-category="{{ $element->categorie }}">
                    <td>
                        <span class="wd-patrimoine-category {{ $isNegative ? 'is-negative' : '' }}">
                            {{ $categorieLabel }}
                        </span>
                    </td>

                    <td>
                        {{
                            $element->nature
                                ? config(
                                    "patrimoine.natures.{$element->categorie}.{$element->nature}",
                                    $element->nature
                                )
                                : '-'
                        }}
                    </td>

                    <td>{{ $element->designation ?: '-' }}</td>

                    <td>{{ $element->mode_detention ?: '-' }}</td>

                    <td class="wd-patrimoine-amount {{ $isNegative ? 'is-negative' : '' }}">
                        {{ $isNegative ? '-' : '' }}{{ $formatEuro($element->montant) }}
                    </td>

                    <td>
                        <div class="wd-patrimoine-row-share">
                            <div class="wd-patrimoine-row-track">
                                <i class="{{ $isNegative ? 'is-negative' : '' }}"
                                   style="width:{{ $pct }}%"></i>
                            </div>
                            <span>{{ number_format($pct, 1, ',', ' ') }} %</span>
                        </div>
                    </td>
                </tr>

            @endforeach
            </tbody>

        </table>
    </div>

</div>

</section>


<section class="wd-section wd-investor-profile" id="investisseur">

<div class="wd-section-head">

<div>
<div class="wd-eyebrow">Profil investisseur</div>
<h2>Adéquation et profil de risque</h2>
</div>

<div style="display:flex;gap:10px;">
<a
href="{{ route('tenant.clients.profil.edit', $client) }}"
class="wd-btn-dark">
Modifier le profil investisseur
</a>
</div>

</div>

@if($profil)

<div class="wd-profile-new-grid">

    <div class="wd-profile-summary">

        <div class="wd-profile-summary-top">

            <div>
                <div class="wd-profile-caption">Profil de risque</div>

                <div class="wd-profile-name">
                    {{ $profilFinal }}
                </div>
            </div>

            <div class="wd-profile-score">
                <strong>{{ number_format($scoreProfil, 1, ',', ' ') }}</strong>
                <span>/ 10</span>
            </div>

        </div>

        <div class="wd-profile-scale">

            <div class="wd-profile-scale-track">
                <div
                    class="wd-profile-scale-value"
                    style="width:{{ min(100, max(0, $scoreProfil * 10)) }}%">
                </div>
            </div>

            <div class="wd-profile-scale-labels">
                <span>Conservateur</span>
                <span>Équilibré</span>
                <span>Dynamique</span>
            </div>

        </div>

        <div class="wd-profile-reading">
            Profil établi à partir des connaissances, de l'expérience,
            de la situation financière et de la capacité à supporter
            une perte en capital.
        </div>

        <div class="wd-profile-date">
            Dernière évaluation
            <strong>
                {{ $profil->updated_at?->translatedFormat('d F Y') ?: 'Non déterminée' }}
            </strong>
        </div>

    </div>


    <div class="wd-profile-metrics">

        <div class="wd-profile-metric">
            <div class="wd-profile-metric-head">
                <span>Connaissance</span>
                <strong>{{ number_format($scoreConnaissance, 1, ',', ' ') }}</strong>
            </div>

            <div class="wd-profile-meter">
                <i style="width:{{ min(100, max(0, $scoreConnaissance * 10)) }}%"></i>
            </div>

            <small>{{ $connaissance }}</small>
        </div>


        <div class="wd-profile-metric">
            <div class="wd-profile-metric-head">
                <span>Expérience</span>
                <strong>{{ number_format($scoreExperience, 1, ',', ' ') }}</strong>
            </div>

            <div class="wd-profile-meter">
                <i style="width:{{ min(100, max(0, $scoreExperience * 10)) }}%"></i>
            </div>

            <small>{{ $experience }}</small>
        </div>


        <div class="wd-profile-metric">
            <div class="wd-profile-metric-head">
                <span>Capacité financière</span>
                <strong>{{ number_format($scoreCapacite, 1, ',', ' ') }}</strong>
            </div>

            <div class="wd-profile-meter">
                <i style="width:{{ min(100, max(0, $scoreCapacite * 10)) }}%"></i>
            </div>

            <small>{{ $capacite }}</small>
        </div>


        <div class="wd-profile-metric">
            <div class="wd-profile-metric-head">
                <span>Tolérance au risque</span>
                <strong>{{ number_format($scoreTolerance, 1, ',', ' ') }}</strong>
            </div>

            <div class="wd-profile-meter">
                <i style="width:{{ min(100, max(0, $scoreTolerance * 10)) }}%"></i>
            </div>

            <small>{{ $tolerance }}</small>
        </div>


        <div class="wd-profile-metric">
            <div class="wd-profile-metric-head">
                <span>Capacité à subir des pertes</span>
                <strong>{{ number_format($scorePertes, 1, ',', ' ') }}</strong>
            </div>

            <div class="wd-profile-meter">
                <i style="width:{{ min(100, max(0, $scorePertes * 10)) }}%"></i>
            </div>

            <small>
                {{ $cleanScoreLabel($profil->score_capacite_subir_pertes_echelle) }}
            </small>
        </div>


        <div class="wd-profile-metric">
            <div class="wd-profile-metric-head">
                <span>Extra-financier</span>
                <strong>{{ number_format($scoreEsg, 1, ',', ' ') }}</strong>
            </div>

            <div class="wd-profile-meter">
                <i style="width:{{ min(100, max(0, $scoreEsg * 10)) }}%"></i>
            </div>

            <small>
                {{ $cleanScoreLabel($profil->engagement_extra_financier_echelle) }}
            </small>
        </div>

    </div>

</div>

@else

<div class="wd-panel wd-empty">
Le profil investisseur n'a pas encore été renseigné.
</div>

@endif


<section class="wd-section wd-compatibility-section">

<div class="wd-section-head">

<div>
<h2>
Compatibilité des placements</h2>
</div>

</div>

@if(!empty($compatibilitesPlacements))

<div
    class="wd-compatibility-wrap"
    x-data="{ filtre: 'tous' }"
>

    <div class="wd-compatibility-filters">

        <button
            type="button"
            @click="filtre = 'tous'"
            :class="{ 'active': filtre === 'tous' }"
        >
            Tous
        </button>

        <button
            type="button"
            @click="filtre = 'compatible'"
            :class="{ 'active': filtre === 'compatible' }"
        >
            Compatible
        </button>

        <button
            type="button"
            @click="filtre = 'vigilance'"
            :class="{ 'active': filtre === 'vigilance' }"
        >
            À surveiller
        </button>

        <button
            type="button"
            @click="filtre = 'non_adapte'"
            :class="{ 'active': filtre === 'non_adapte' }"
        >
            Non adapté
        </button>

    </div>

<div class="wd-compatibility-panel">

    <div class="wd-compatibility-head">
        <div>
            <span>Classe de placement</span>
        </div>

        <div>
            <span>Lecture</span>
        </div>
    </div>

    @foreach($compatibilitesPlacements as $compatibilite)

        @php
            $niveau = $compatibilite['niveau'] ?? 'vigilance';

            $niveauLabel = match($niveau) {
                'compatible' => 'Compatible',
                'non_adapte' => 'Non adapté',
                default => 'À surveiller',
            };
        @endphp

        <div
            class="wd-compatibility-row wd-compatibility-{{ $niveau }}"
            x-show="filtre === 'tous' || filtre === '{{ $niveau }}'"
            x-transition.opacity.duration.150ms
        >

            <div class="wd-compatibility-product">

                <div class="wd-compatibility-title">
                    {{ $compatibilite['label'] }}
                </div>

                <div class="wd-compatibility-detail">
                    {{ $compatibilite['detail'] }}
                </div>

            </div>

            <div class="wd-compatibility-reading">

                <div class="wd-compatibility-status">
                    {{ $niveauLabel }}
                </div>

                <div class="wd-compatibility-reason">
                    {{ $compatibilite['motif'] }}
                </div>

            </div>

        </div>

    @endforeach

</div>

</div>

<div class="wd-compatibility-note">
    Cette lecture constitue une aide à l'analyse du profil investisseur.
    L'adéquation définitive d'un produit dépend également de ses caractéristiques,
    de l'horizon d'investissement et de la situation du client au moment du conseil.
</div>

@else

<div class="wd-panel wd-empty">
La compatibilité des placements ne peut pas encore être déterminée.
</div>

@endif

</section>

</section>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const filters = document.getElementById('wdPatrimoineFilters');

    if (!filters) {
        return;
    }

    const buttons = filters.querySelectorAll('[data-filter]');
    const rows = document.querySelectorAll(
        '.wd-patrimoine-table tbody tr[data-patrimoine-category]'
    );

    buttons.forEach(function (button) {

        button.addEventListener('click', function () {

            const filter = button.dataset.filter;

            buttons.forEach(function (item) {
                item.classList.remove('active');
            });

            button.classList.add('active');

            rows.forEach(function (row) {

                const category = row.dataset.patrimoineCategory;

                const visible =
                    filter === 'all'
                    || category === filter;

                row.classList.toggle(
                    'wd-filter-hidden',
                    !visible
                );

            });

        });

    });

});
</script>



<script>
document.addEventListener('DOMContentLoaded', function () {

    const euroFormatter = new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR',
        maximumFractionDigits: 0
    });

    /*
    |--------------------------------------------------------------------------
    | Tooltip global
    |--------------------------------------------------------------------------
    */

    const tooltip = document.createElement('div');
    tooltip.className = 'wd-chart-tooltip';

    tooltip.innerHTML = `
        <div class="wd-chart-tooltip-title"></div>
        <div class="wd-chart-tooltip-value"></div>
        <div class="wd-chart-tooltip-meta"></div>
    `;

    document.body.appendChild(tooltip);

    const titleElement = tooltip.querySelector('.wd-chart-tooltip-title');
    const valueElement = tooltip.querySelector('.wd-chart-tooltip-value');
    const metaElement = tooltip.querySelector('.wd-chart-tooltip-meta');


    function showTooltip(event, title, value, meta = '') {

        titleElement.textContent = title;
        valueElement.textContent = value;
        metaElement.textContent = meta;

        tooltip.classList.add('is-visible');

        moveTooltip(event);
    }


    function moveTooltip(event) {

        const gap = 14;

        let x = event.clientX + gap;
        let y = event.clientY + gap;

        const width = tooltip.offsetWidth;
        const height = tooltip.offsetHeight;

        if (x + width > window.innerWidth - 10) {
            x = event.clientX - width - gap;
        }

        if (y + height > window.innerHeight - 10) {
            y = event.clientY - height - gap;
        }

        tooltip.style.left = `${x}px`;
        tooltip.style.top = `${y}px`;
    }


    function hideTooltip() {
        tooltip.classList.remove('is-visible');
    }


    /*
    |--------------------------------------------------------------------------
    | Donuts
    |--------------------------------------------------------------------------
    */

    document.querySelectorAll('[data-donut-tooltip]').forEach(function (donut) {

        let segments = [];

        try {
            segments = JSON.parse(donut.dataset.segments || '[]');
        } catch (error) {
            console.warn('Segments patrimoine invalides', error);
            return;
        }

        donut.addEventListener('mousemove', function (event) {

            if (!segments.length) {
                return;
            }

            const rect = donut.getBoundingClientRect();

            const centerX = rect.left + rect.width / 2;
            const centerY = rect.top + rect.height / 2;

            const dx = event.clientX - centerX;
            const dy = event.clientY - centerY;

            const radius = Math.sqrt((dx * dx) + (dy * dy));

            const outerRadius = rect.width / 2;
            const innerRadius = outerRadius * 0.60;

            /*
             * Pas de tooltip dans le trou central.
             */
            if (radius < innerRadius || radius > outerRadius) {
                hideTooltip();
                return;
            }

            /*
             * conic-gradient commence en haut et tourne dans le sens horaire.
             */
            let angle =
                Math.atan2(dy, dx) * 180 / Math.PI;

            angle = (angle + 450) % 360;

            const pctPosition = angle / 360 * 100;

            let cursor = 0;
            let hoveredSegment = null;

            for (const segment of segments) {

                cursor += Number(segment.pct || 0);

                if (pctPosition <= cursor) {
                    hoveredSegment = segment;
                    break;
                }
            }

            if (!hoveredSegment) {
                hideTooltip();
                return;
            }

            showTooltip(
                event,
                hoveredSegment.label,
                euroFormatter.format(hoveredSegment.montant),
                `${Number(hoveredSegment.pct).toLocaleString('fr-FR', {
                    minimumFractionDigits:1,
                    maximumFractionDigits:1
                })} % de la catégorie`
            );

        });

        donut.addEventListener('mouseleave', hideTooltip);

    });


    /*
    |--------------------------------------------------------------------------
    | Histogramme des flux
    |--------------------------------------------------------------------------
    */

    document.querySelectorAll('[data-flux-tooltip]').forEach(function (item) {

        const bar = item.querySelector('.wd-flux-column i');
        const label = item.querySelector('span');
        const value = item.querySelector('.wd-flux-value');

        if (!bar || !label || !value) {
            return;
        }

        bar.addEventListener('mouseenter', function (event) {

            showTooltip(
                event,
                label.textContent.trim(),
                value.textContent.trim(),
                'Flux annuel'
            );

        });

        bar.addEventListener('mousemove', moveTooltip);
        bar.addEventListener('mouseleave', hideTooltip);

    });

});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    var dossierOverlay = document.querySelector('[data-dossier-modal]');
    var dossierTriggers = document.querySelectorAll('[data-dossier-trigger]');
    var dossierClose = document.querySelector('[data-dossier-close]');

    if (dossierOverlay && dossierTriggers.length) {
        dossierTriggers.forEach(function (trigger) {
            trigger.addEventListener('click', function () { dossierOverlay.hidden = false; });
        });
        if (dossierClose) {
            dossierClose.addEventListener('click', function () { dossierOverlay.hidden = true; });
        }
        dossierOverlay.addEventListener('click', function (e) {
            if (e.target === dossierOverlay) { dossierOverlay.hidden = true; }
        });
    }

    var donneesOverlay = document.querySelector('[data-donnees-modal]');
    var donneesTriggers = document.querySelectorAll('[data-donnees-trigger]');
    var donneesClose = document.querySelector('[data-donnees-close]');

    if (donneesOverlay && donneesTriggers.length) {
        donneesTriggers.forEach(function (trigger) {
            trigger.addEventListener('click', function () { donneesOverlay.hidden = false; });
        });
        if (donneesClose) {
            donneesClose.addEventListener('click', function () { donneesOverlay.hidden = true; });
        }
        donneesOverlay.addEventListener('click', function (e) {
            if (e.target === donneesOverlay) { donneesOverlay.hidden = true; }
        });
    }

    [
        ['data-patrimoine-donnees-modal', 'data-patrimoine-donnees-trigger', 'data-patrimoine-donnees-close'],
        ['data-investisseur-donnees-modal', 'data-investisseur-donnees-trigger', 'data-investisseur-donnees-close']
    ].forEach(function (attrs) {
        var overlay = document.querySelector('[' + attrs[0] + ']');
        var triggers = document.querySelectorAll('[' + attrs[1] + ']');
        var closeBtn = document.querySelector('[' + attrs[2] + ']');
        if (!overlay || !triggers.length) { return; }
        triggers.forEach(function (trigger) {
            trigger.addEventListener('click', function () { overlay.hidden = false; });
        });
        if (closeBtn) {
            closeBtn.addEventListener('click', function () { overlay.hidden = true; });
        }
        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) { overlay.hidden = true; }
        });
    });

    document.querySelectorAll('[data-copy]').forEach(function (pill) {
        pill.addEventListener('click', function () {
            var texte = pill.getAttribute('data-copy');
            if (!texte || !navigator.clipboard) { return; }
            navigator.clipboard.writeText(texte).then(function () {
                pill.classList.add('copied');
                setTimeout(function () { pill.classList.remove('copied'); }, 1200);
            });
        });
    });

});
</script>

</x-tenant-app-layout>
