<x-tenant-app-layout>
<style>
body > div > nav,
body > div > header{display:none!important}
html,body{margin:0!important;background:#f3f1ee!important}
:root{
--bg:#f3f1ee;--white:#fff;--ink:#151515;--muted:#817b76;--line:#ded9d4;
--dark:#1b1716;--pink:#f40087;--red:#b94d4d;--green:#4d8760;
}
*{box-sizing:border-box}
.wd-sidebar{
position:fixed;inset:0 auto 0 0;width:232px;height:100vh;
background:#242424;color:#fff;padding:24px 16px;
display:flex;flex-direction:column;z-index:1000;overflow:hidden
}
.wd-logo{padding:0 7px 36px;font-size:23px;font-weight:800;letter-spacing:-.06em}
.wd-logo b{color:var(--pink)}
.wd-logo small{display:block;color:#7e7773;font-size:8px;letter-spacing:.25em;text-transform:uppercase;margin-top:5px}
.wd-nav{display:grid;gap:3px}
.wd-nav-section{margin:22px 10px 7px;font-size:9px;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:rgba(255,255,255,.30)}
.wd-nav a{display:flex;align-items:center;gap:11px;padding:10px 11px;margin:2px 0;border-radius:8px;color:rgba(255,255,255,.62);font-size:12px;text-decoration:none}
.wd-nav a svg{width:18px;height:18px;flex:0 0 18px;fill:none;stroke:currentColor;stroke-width:1.7;stroke-linecap:round;stroke-linejoin:round;color:rgba(255,255,255,.42)}
.wd-nav a.active{background:rgba(244,0,135,.11);color:#fff}
.wd-nav a.active svg{color:var(--pink)}
.wd-nav a:hover{background:rgba(255,255,255,.06);color:#fff}
.wd-bottom-nav{margin-top:auto;border-top:1px solid #302a28;padding-top:14px}
.wd-bottom-nav a,.wd-bottom-nav button{display:block;width:100%;padding:9px 10px;color:#8e8681;font-size:9px;text-transform:uppercase;letter-spacing:.12em;text-align:left;background:none;border:0}
.wd-main{margin-left:232px;min-height:100vh;padding-top:68px}
.wd-topbar{
position:fixed;top:0;left:232px;right:0;height:68px;z-index:900;
background:rgba(255,255,255,.94);border-bottom:1px solid var(--line);
display:flex;align-items:center;justify-content:space-between;padding:0 34px;
backdrop-filter:blur(10px)
}
.wd-crumb{color:#96908b;font-size:11px;font-weight:800;letter-spacing:.17em;text-transform:uppercase}
.wd-who{display:flex;gap:10px;align-items:center;text-align:right}
.wd-who strong{font-size:14px}
.wd-who small{display:block;color:#99918c;font-size:10px;text-transform:uppercase;letter-spacing:.1em}
.wd-top-avatar{width:34px;height:34px;border-radius:50%;background:var(--pink);display:grid;place-items:center;color:#fff;font-size:10px;font-weight:800}
.wd-wrap{max-width:1540px;margin:auto;padding:30px 34px 60px}
.wd-head{display:flex;justify-content:space-between;align-items:end}
.wd-eyebrow{font-size:12px;color:var(--pink);font-weight:850;letter-spacing:.2em;text-transform:uppercase}
.wd-head h1{font-size:38px;line-height:1;margin:8px 0 0;letter-spacing:-.05em;font-weight:650}
.wd-head p{color:var(--muted);margin:10px 0 0;font-size:15px}
.wd-date{font-size:12px;color:#8e8782;text-transform:uppercase;letter-spacing:.12em}
@media(max-width:1050px){
.wd-sidebar{width:72px}
.wd-logo{font-size:0}.wd-logo b{font-size:22px}.wd-logo small,.wd-nav-section,.wd-bottom-nav{display:none}
.wd-nav a{justify-content:center}
.wd-main{margin-left:72px}
.wd-topbar{left:72px}
}
@media(max-width:650px){
.wd-wrap{padding:22px 14px 50px}
.wd-head{flex-direction:column;align-items:flex-start;gap:15px}
}
</style>
<div class="wd-wrap">
    @if(session('cabinet_saved'))
    <section class="wd-cabinet-success">
        Les informations du cabinet ont été enregistrées.
    </section>
    @endif
    <!-- =====================================================
         EN-TÊTE
         ===================================================== -->
    <section class="wd-head">
        <div>
            <div class="wd-eyebrow">Administration</div>
            <h1>Mon cabinet.</h1>
            <p>
                Gérez votre structure, votre équipe et votre environnement Wendee.
            </p>
        </div>
        <div class="wd-date">
            {{ now()->translatedFormat('l d F Y') }}
        </div>
    </section>
    <!-- =====================================================
         ÉTAT DU CABINET
         ===================================================== -->
    <section class="wd-cabinet-alert {{ $completionStatus['complete'] ? 'wd-cabinet-alert-ok' : '' }}">
        <div class="wd-cabinet-alert-main">
            <div class="wd-cabinet-alert-icon">
                @if($completionStatus['complete'])
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 13l4 4L19 7"/></svg>
                @else
                    !
                @endif
            </div>
            <div>
                <div class="wd-cabinet-alert-kicker">
                    Configuration du cabinet
                </div>
                @if($completionStatus['complete'])
                    <h2>
                        Votre cabinet est prêt à travailler.
                    </h2>
                    <p>
                        Les informations essentielles de votre structure sont complètes.
                    </p>
                @else
                    <h2>
                        Votre cabinet doit être configuré.
                    </h2>
                    <p>
                        Complétez les informations professionnelles de votre structure
                        avant de commencer à utiliser pleinement Wendee.
                    </p>
                @endif
            </div>
        </div>
        @unless($completionStatus['complete'])
        <button type="button" class="wd-cabinet-alert-button" data-open-cabinet-modal>
            Configurer mon cabinet
            <span>→</span>
        </button>
        @endunless
    </section>
    @unless($completionStatus['complete'])
    <div class="wd-cabinet-modal-overlay" data-cabinet-modal hidden>
        <div class="wd-cabinet-modal">
            <div class="wd-cabinet-modal-head">
                <h3>Informations manquantes</h3>
                <button type="button" class="wd-cabinet-modal-close" data-close-cabinet-modal>&times;</button>
            </div>
            <div class="wd-cabinet-modal-body">
                @if(count($completionStatus['identite']))
                <div class="wd-cabinet-modal-section">
                    <div class="wd-cabinet-modal-section-title">Identité professionnelle</div>
                    <ul>
                        @foreach($completionStatus['identite'] as $item)
                        <li>
                            <button type="button" class="wd-cabinet-modal-item" data-jump-anchor="{{ $item['anchor'] }}" @if($item['tab']) data-jump-tab="{{ $item['tab'] }}" @endif>
                                {{ $item['label'] }}
                                <span>→</span>
                            </button>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif
                @if(count($completionStatus['tarifs']))
                <div class="wd-cabinet-modal-section">
                    <div class="wd-cabinet-modal-section-title">Tarifs & rémunération</div>
                    <ul>
                        @foreach($completionStatus['tarifs'] as $item)
                        <li>
                            <button type="button" class="wd-cabinet-modal-item" data-jump-anchor="{{ $item['anchor'] }}">
                                {{ $item['label'] }}
                                <span>→</span>
                            </button>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif
                @if(count($completionStatus['objectifs']))
                <div class="wd-cabinet-modal-section">
                    <div class="wd-cabinet-modal-section-title">Objectifs</div>
                    <ul>
                        @foreach($completionStatus['objectifs'] as $item)
                        <li>
                            <button type="button" class="wd-cabinet-modal-item" data-jump-anchor="{{ $item['anchor'] }}">
                                {{ $item['label'] }}
                                <span>→</span>
                            </button>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
        </div>
    </div>
    <script>
    (function() {
        var openBtn = document.querySelector('[data-open-cabinet-modal]');
        var overlay = document.querySelector('[data-cabinet-modal]');
        var closeBtn = document.querySelector('[data-close-cabinet-modal]');
        if (!overlay) {
            return;
        }
        function open() {
            overlay.hidden = false;
        }
        function close() {
            overlay.hidden = true;
        }
        @if(session('cabinet_gate_redirect'))
        open();
        @endif
        if (openBtn) {
            openBtn.addEventListener('click', open);
        }
        if (closeBtn) {
            closeBtn.addEventListener('click', close);
        }
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) {
                close();
            }
        });
        overlay.querySelectorAll('[data-jump-anchor]').forEach(function(item) {
            item.addEventListener('click', function() {
                var tab = item.getAttribute('data-jump-tab');
                if (tab) {
                    var tabBtn = document.querySelector('.wd-cabinet-tab[data-tab="' + tab + '"]');
                    if (tabBtn) {
                        tabBtn.click();
                    }
                }
                close();
                var target = document.getElementById(item.getAttribute('data-jump-anchor'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
    })();
    </script>
    @endunless
    <!-- =====================================================
         RESSOURCES
         ===================================================== -->
    <section class="wd-cabinet-section">
        <div class="wd-cabinet-section-head">
            <div>
                <div class="wd-eyebrow">Ressources</div>
                <h2>Votre organisation.</h2>
            </div>
            <span>
                Gestion du cabinet
            </span>
        </div>
        <div class="wd-cabinet-resources">
            <a href="#" class="wd-cabinet-resource">
                <div class="wd-cabinet-resource-top">
                    <div class="wd-cabinet-resource-icon">
                        <svg viewBox="0 0 24 24">
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M3 21v-2a6 6 0 0 1 12 0v2"/>
                            <path d="M16 11a4 4 0 0 0 0-8"/>
                            <path d="M16 13a6 6 0 0 1 5 6v2"/>
                        </svg>
                    </div>
                    <span class="wd-cabinet-resource-arrow">→</span>
                </div>
                <div class="wd-cabinet-resource-label">
                    Équipe
                </div>
                <h3>
                    Conseillers
                </h3>
                <p>
                    Gérez les conseillers qui travaillent au sein du cabinet.
                </p>
                <div class="wd-cabinet-resource-footer">
                    <strong>{{ $cabinet->users_count ?? 0 }}</strong>
                    <span>utilisateur(s)</span>
                </div>
            </a>
            <a href="{{ route('tenant.clients.index') }}" class="wd-cabinet-resource">
                <div class="wd-cabinet-resource-top">
                    <div class="wd-cabinet-resource-icon">
                        <svg viewBox="0 0 24 24">
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M3 21v-2a6 6 0 0 1 12 0v2"/>
                            <path d="M16 11a4 4 0 0 0 0-8"/>
                            <path d="M16 13a6 6 0 0 1 5 6v2"/>
                        </svg>
                    </div>
                    <span class="wd-cabinet-resource-arrow">→</span>
                </div>
                <div class="wd-cabinet-resource-label">
                    Portefeuille
                </div>
                <h3>
                    Clients
                </h3>
                <p>
                    Retrouvez et pilotez l'ensemble des clients du cabinet.
                </p>
                <div class="wd-cabinet-resource-footer">
                    <strong>-</strong>
                    <span>client(s)</span>
                </div>
            </a>
            <a href="#" class="wd-cabinet-resource">
                <div class="wd-cabinet-resource-top">
                    <div class="wd-cabinet-resource-icon">
                        <svg viewBox="0 0 24 24">
                            <circle cx="7" cy="12" r="3"/>
                            <circle cx="17" cy="12" r="3"/>
                            <path d="M10 12h4"/>
                        </svg>
                    </div>
                    <span class="wd-cabinet-resource-arrow">→</span>
                </div>
                <div class="wd-cabinet-resource-label">
                    Développement
                </div>
                <h3>
                    Apporteurs
                </h3>
                <p>
                    Développez et administrez votre réseau d'apporteurs.
                </p>
                <div class="wd-cabinet-resource-footer">
                    <strong>-</strong>
                    <span>apporteur(s)</span>
                </div>
            </a>
        </div>
    </section>
    <!-- =====================================================
         GESTION
         ===================================================== -->
    <section class="wd-cabinet-section">
        <div class="wd-cabinet-section-head">
            <div>
                <div class="wd-eyebrow">Pilotage</div>
                <h2>Votre environnement.</h2>
            </div>
        </div>
        <div class="wd-cabinet-management-grid">
            <a href="#informations-cabinet" class="wd-cabinet-management">
                <div class="wd-cabinet-management-icon">
                    <svg viewBox="0 0 24 24">
                        <rect x="4" y="3" width="16" height="18" rx="2"/>
                        <path d="M8 8h8M8 12h8M8 16h5"/>
                    </svg>
                </div>
                <div class="wd-cabinet-management-content">
                    <span>Identité professionnelle</span>
                    <strong>Informations du cabinet</strong>
                    <p>
                        Coordonnées, identité juridique, ORIAS et activité.
                    </p>
                </div>
                <span class="wd-cabinet-management-arrow">→</span>
            </a>
            <a href="#tarifs-cabinet" class="wd-cabinet-management">
                <div class="wd-cabinet-management-icon">
                    <svg viewBox="0 0 24 24">
                        <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H7"/>
                    </svg>
                </div>
                <div class="wd-cabinet-management-content">
                    <span>Rémunération</span>
                    <strong>Tarifs & rémunération</strong>
                    <p>
                        Mode de facturation de vos prestations.
                    </p>
                </div>
                <span class="wd-cabinet-management-arrow">→</span>
            </a>
            <a href="#objectifs-cabinet" class="wd-cabinet-management">
                <div class="wd-cabinet-management-icon">
                    <svg viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="9"/>
                        <circle cx="12" cy="12" r="4"/>
                        <circle cx="12" cy="12" r="0.5"/>
                    </svg>
                </div>
                <div class="wd-cabinet-management-content">
                    <span>Pilotage</span>
                    <strong>Objectifs</strong>
                    <p>
                        Vos objectifs d'activité et de revenu.
                    </p>
                </div>
                <span class="wd-cabinet-management-arrow">→</span>
            </a>
            <a href="#" class="wd-cabinet-management">
                <div class="wd-cabinet-management-icon">
                    <svg viewBox="0 0 24 24">
                        <rect x="3" y="6" width="18" height="12" rx="2"/>
                        <path d="M7 10h.01M17 14h.01M9 12h6"/>
                    </svg>
                </div>
                <div class="wd-cabinet-management-content">
                    <span>Offre Wendee</span>
                    <strong>Abonnement</strong>
                    <p>
                        Consultez votre offre et faites évoluer votre abonnement.
                    </p>
                </div>
                <span class="wd-cabinet-management-arrow">→</span>
            </a>
            <a href="#" class="wd-cabinet-management">
                <div class="wd-cabinet-management-icon">
                    <svg viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="3"/>
                        <path d="M12 2v3M12 19v3M4.9 4.9l2.1 2.1M17 17l2.1 2.1M2 12h3M19 12h3M4.9 19.1 7 17M17 7l2.1-2.1"/>
                    </svg>
                </div>
                <div class="wd-cabinet-management-content">
                    <span>Administration</span>
                    <strong>Paramètres</strong>
                    <p>
                        Sécurité, préférences et configuration de votre espace.
                    </p>
                </div>
                <span class="wd-cabinet-management-arrow">→</span>
            </a>
        </div>
    </section>
    @if(isset($cabinet))
    <!-- =====================================================
         INFORMATIONS (éditable)
         ===================================================== -->
    <section id="informations-cabinet" class="wd-cabinet-information">
        <div class="wd-cabinet-information-head">
            <div>
                <div class="wd-eyebrow">Identité professionnelle</div>
                <h2>Informations du cabinet.</h2>
                <p>
                    Les informations utilisées pour identifier votre structure
                    dans Wendee.
                </p>
            </div>
        </div>
        <div class="wd-cabinet-tabs">
            <button type="button" class="wd-cabinet-tab active" data-tab="identification">Identification</button>
            <button type="button" class="wd-cabinet-tab" data-tab="coordonnees">Coordonnées</button>
            <button type="button" class="wd-cabinet-tab" data-tab="orias">ORIAS & statuts</button>
            <button type="button" class="wd-cabinet-tab" data-tab="autorites">Autorités</button>
            <button type="button" class="wd-cabinet-tab" data-tab="rcp">Assurance RCP</button>
            <button type="button" class="wd-cabinet-tab" data-tab="garantie">Garantie financière</button>
            <button type="button" class="wd-cabinet-tab" data-tab="gouvernance">Gouvernance</button>
            <button type="button" class="wd-cabinet-tab" data-tab="partenaires">Partenaires</button>
        </div>
        <form method="POST" action="{{ route('tenant.cabinet.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="wd-cabinet-tab-panel" data-tab-panel="identification">
                <div class="wd-cabinet-logo-field">
                    <label>Logo du cabinet</label>
                    @if($cabinet->logo)
                    <img src="{{ tenant_asset($cabinet->logo) }}" alt="Logo" class="wd-cabinet-logo-preview">
                    @endif
                    <input type="file" name="logo" accept="image/*">
                </div>
                <div class="wd-cabinet-information-grid">
                    <div class="wd-cabinet-field">
                        <label>Nom commercial</label>
                        <input type="text" name="nom_commercial" value="{{ old('nom_commercial', $cabinet->nom_commercial) }}">
                    </div>
                    <div class="wd-cabinet-field">
                        <label>Raison sociale</label>
                        <input type="text" name="raison_sociale" value="{{ old('raison_sociale', $cabinet->raison_sociale) }}">
                    </div>
                    <div class="wd-cabinet-field">
                        <label>Forme juridique</label>
                        <input type="text" name="forme_juridique" value="{{ old('forme_juridique', $cabinet->forme_juridique) }}">
                    </div>
                    <div class="wd-cabinet-field">
                        <label>Capital social (€)</label>
                        <input type="number" step="0.01" name="capital_social" value="{{ old('capital_social', $cabinet->capital_social) }}">
                    </div>
                    <div class="wd-cabinet-field">
                        <label>Numéro RCS</label>
                        <input type="text" name="numero_rcs" value="{{ old('numero_rcs', $cabinet->numero_rcs) }}">
                    </div>
                    <div class="wd-cabinet-field">
                        <label>Ville RCS</label>
                        <input type="text" name="ville_rcs" value="{{ old('ville_rcs', $cabinet->ville_rcs) }}">
                    </div>
                    <div class="wd-cabinet-field">
                        <label>N° TVA intracommunautaire</label>
                        <input type="text" name="numero_tva" value="{{ old('numero_tva', $cabinet->numero_tva) }}">
                    </div>
                </div>
            </div>
            <div class="wd-cabinet-tab-panel" data-tab-panel="coordonnees" hidden>
                <div class="wd-cabinet-information-grid">
                    <div class="wd-cabinet-field">
                        <label>Adresse</label>
                        <input type="text" name="adresse" value="{{ old('adresse', $cabinet->adresse) }}">
                    </div>
                    <div class="wd-cabinet-field">
                        <label>Code postal</label>
                        <input type="text" name="code_postal" value="{{ old('code_postal', $cabinet->code_postal) }}">
                    </div>
                    <div class="wd-cabinet-field">
                        <label>Ville</label>
                        <input type="text" name="ville" value="{{ old('ville', $cabinet->ville) }}">
                    </div>
                    <div class="wd-cabinet-field">
                        <label>Téléphone</label>
                        <input type="tel" name="telephone" value="{{ old('telephone', $cabinet->telephone) }}">
                    </div>
                    <div class="wd-cabinet-field">
                        <label>Email</label>
                        <input type="email" name="email" value="{{ old('email', $cabinet->email) }}">
                    </div>
                    <div class="wd-cabinet-field">
                        <label>Site internet</label>
                        <input type="text" name="site_internet" value="{{ old('site_internet', $cabinet->site_internet) }}">
                    </div>
                </div>
            </div>
            <div class="wd-cabinet-tab-panel" data-tab-panel="orias" hidden>
                <div class="wd-cabinet-information-grid">
                    <div class="wd-cabinet-field">
                        <label>Numéro ORIAS</label>
                        <input type="text" name="numero_orias" value="{{ old('numero_orias', $cabinet->numero_orias) }}">
                    </div>
                    <div class="wd-cabinet-field">
                        <label>Immatriculation CCI</label>
                        <input type="text" name="immatriculation_cci" value="{{ old('immatriculation_cci', $cabinet->immatriculation_cci) }}">
                    </div>
                    <div class="wd-cabinet-field">
                        <label>Date d'inscription ORIAS</label>
                        <input type="date" name="date_orias" value="{{ old('date_orias', $cabinet->date_orias ? \Carbon\Carbon::parse($cabinet->date_orias)->format('Y-m-d') : '') }}">
                    </div>
                </div>
                <div class="wd-cabinet-information-subhead">
                    Statut ORIAS
                </div>
                <input type="hidden" name="statuts_reglementaires" value="">
                <div class="wd-cabinet-checkbox-group">
                    @php $statuts = $cabinet->statuts_reglementaires ?? []; @endphp
                    @foreach([
                        'ias_courtier' => 'Courtier en assurances (IAS - courtier)',
                        'ias_mandataire' => "Mandataire d'assurance (IAS - mandataire)",
                        'ias_mia' => "Mandataire d'intermédiaire d'assurance (MIA)",
                        'iobsp_courtier' => 'Courtier en opérations de banque et services de paiement (IOBSP - courtier)',
                        'iobsp_mandataire' => 'Mandataire IOBSP',
                        'iobsp_mandataire_non_exclusif' => 'Mandataire non exclusif IOBSP',
                        'iobsp_mandataire_exclusif' => 'Mandataire exclusif IOBSP',
                        'cif' => 'Conseiller en investissements financiers (CIF)',
                        'agent_immobilier' => 'Agent immobilier',
                        'mandataire_agent_immobilier' => "Mandataire d'agent immobilier",
                    ] as $key => $label)
                    <label class="wd-cabinet-checkbox">
                        <input type="checkbox" name="statuts_reglementaires[]" value="{{ $key }}" {{ in_array($key, $statuts) ? 'checked' : '' }}>
                        <span class="wd-cabinet-checkbox-box">
                            <svg viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                        </span>
                        <span>{{ $label }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            <div class="wd-cabinet-tab-panel" data-tab-panel="autorites" hidden>
                <div class="wd-cabinet-information-subhead">
                    Association professionnelle agréée
                </div>
                <input type="hidden" name="association_professionnelle" value="">
                <div class="wd-cabinet-checkbox-group">
                    @php $assocs = $cabinet->association_professionnelle ?? []; @endphp
                    @foreach([
                        'acpr' => 'ACPR - Autorité de contrôle prudentiel et de résolution',
                        'amf' => 'AMF - Autorité des marchés financiers',
                        'cci' => "CCI - Chambre de commerce et d'industrie",
                    ] as $key => $label)
                    <label class="wd-cabinet-checkbox">
                        <input type="checkbox" name="association_professionnelle[]" value="{{ $key }}" {{ in_array($key, $assocs) ? 'checked' : '' }}>
                        <span class="wd-cabinet-checkbox-box">
                            <svg viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                        </span>
                        <span>{{ $label }}</span>
                    </label>
                    @endforeach
                </div>
                <div class="wd-cabinet-information-grid">
                    <div class="wd-cabinet-field">
                        <label>Numéro d'adhésion</label>
                        <input type="text" name="numero_association" value="{{ old('numero_association', $cabinet->numero_association) }}">
                    </div>
                </div>
            </div>
            <div class="wd-cabinet-tab-panel" data-tab-panel="rcp" hidden>
                <div class="wd-cabinet-information-subhead">
                    Assureur
                </div>
                <div class="wd-cabinet-information-grid">
                    <div class="wd-cabinet-field">
                        <label>Compagnie d'assurance</label>
                        <input type="text" name="assurance_compagnie" value="{{ old('assurance_compagnie', $cabinet->assurance_compagnie) }}">
                    </div>
                    <div class="wd-cabinet-field">
                        <label>Adresse</label>
                        <input type="text" name="assurance_adresse" value="{{ old('assurance_adresse', $cabinet->assurance_adresse) }}">
                    </div>
                    <div class="wd-cabinet-field">
                        <label>Code postal</label>
                        <input type="text" name="assurance_code_postal" value="{{ old('assurance_code_postal', $cabinet->assurance_code_postal) }}">
                    </div>
                    <div class="wd-cabinet-field">
                        <label>Ville</label>
                        <input type="text" name="assurance_ville" value="{{ old('assurance_ville', $cabinet->assurance_ville) }}">
                    </div>
                </div>
                <div class="wd-cabinet-information-subhead">
                    Plafonds de garantie
                </div>
                <div class="wd-cabinet-information-grid">
                    <div class="wd-cabinet-field">
                        <label>Par sinistre - IAS (€)</label>
                        <input type="number" step="0.01" name="plafond_garanties_sinistre_ias" value="{{ old('plafond_garanties_sinistre_ias', $cabinet->plafond_garanties_sinistre_ias) }}">
                    </div>
                    <div class="wd-cabinet-field">
                        <label>Par année - IAS (€)</label>
                        <input type="number" step="0.01" name="plafond_garanties_annee_ias" value="{{ old('plafond_garanties_annee_ias', $cabinet->plafond_garanties_annee_ias) }}">
                    </div>
                    <div class="wd-cabinet-field">
                        <label>Par sinistre - IOBSP (€)</label>
                        <input type="number" step="0.01" name="plafond_garanties_sinistre_iobsp" value="{{ old('plafond_garanties_sinistre_iobsp', $cabinet->plafond_garanties_sinistre_iobsp) }}">
                    </div>
                    <div class="wd-cabinet-field">
                        <label>Par année - IOBSP (€)</label>
                        <input type="number" step="0.01" name="plafond_garanties_annee_iobsp" value="{{ old('plafond_garanties_annee_iobsp', $cabinet->plafond_garanties_annee_iobsp) }}">
                    </div>
                    <div class="wd-cabinet-field">
                        <label>Par sinistre - CIF (€)</label>
                        <input type="number" step="0.01" name="plafond_garanties_sinistre_cif" value="{{ old('plafond_garanties_sinistre_cif', $cabinet->plafond_garanties_sinistre_cif) }}">
                    </div>
                    <div class="wd-cabinet-field">
                        <label>Par année - CIF (€)</label>
                        <input type="number" step="0.01" name="plafond_garanties_annee_cif" value="{{ old('plafond_garanties_annee_cif', $cabinet->plafond_garanties_annee_cif) }}">
                    </div>
                    <div class="wd-cabinet-field">
                        <label>RC exploitation par sinistre (€)</label>
                        <input type="number" step="0.01" name="responsabilite_civile_exploitation_sinistre" value="{{ old('responsabilite_civile_exploitation_sinistre', $cabinet->responsabilite_civile_exploitation_sinistre) }}">
                    </div>
                </div>
                <div class="wd-cabinet-information-subhead">
                    Contrat
                </div>
                <div class="wd-cabinet-information-grid">
                    <div class="wd-cabinet-field">
                        <label>N° de contrat</label>
                        <input type="text" name="assurance_police" value="{{ old('assurance_police', $cabinet->assurance_police) }}">
                    </div>
                    <div class="wd-cabinet-field">
                        <label>Date de début</label>
                        <input type="date" name="assurance_date_debut" value="{{ old('assurance_date_debut', $cabinet->assurance_date_debut ? \Carbon\Carbon::parse($cabinet->assurance_date_debut)->format('Y-m-d') : '') }}">
                    </div>
                    <div class="wd-cabinet-field">
                        <label>Date de fin</label>
                        <input type="date" name="assurance_date_fin" value="{{ old('assurance_date_fin', $cabinet->assurance_date_fin ? \Carbon\Carbon::parse($cabinet->assurance_date_fin)->format('Y-m-d') : '') }}">
                    </div>
                </div>
                <div class="wd-cabinet-information-subhead">
                    Zone de couverture
                </div>
                <div class="wd-cabinet-radio-group">
                    @php $zoneCouverture = old('assurance_zone_couverture', $cabinet->assurance_zone_couverture); @endphp
                    @foreach([
                        'france' => 'France uniquement',
                        'ue' => 'Union européenne',
                        'europe' => 'Europe (UE + hors UE)',
                        'hors_usa' => 'Monde (hors USA / Canada)',
                        'monde' => 'Monde entier',
                    ] as $key => $label)
                    <label class="wd-cabinet-radio">
                        <input type="radio" name="assurance_zone_couverture" value="{{ $key }}" {{ $zoneCouverture === $key ? 'checked' : '' }}>
                        <span>{{ $label }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            <div class="wd-cabinet-tab-panel" data-tab-panel="garantie" hidden>
                <div class="wd-cabinet-information-subhead">
                    Garantie financière IOBSP
                </div>
                <div class="wd-cabinet-radio-group">
                    @php $gfIobsp = old('garantie_financiere_iobsp', $cabinet->garantie_financiere_iobsp); @endphp
                    <label class="wd-cabinet-radio">
                        <input type="radio" name="garantie_financiere_iobsp" value="oui" {{ $gfIobsp === 'oui' ? 'checked' : '' }}>
                        <span>Oui</span>
                    </label>
                    <label class="wd-cabinet-radio">
                        <input type="radio" name="garantie_financiere_iobsp" value="non" {{ $gfIobsp === 'non' ? 'checked' : '' }}>
                        <span>Non</span>
                    </label>
                </div>
                <div class="wd-cabinet-information-grid">
                    <div class="wd-cabinet-field">
                        <label>Assureur</label>
                        <input type="text" name="garantie_financiere_iobsp_assureur" value="{{ old('garantie_financiere_iobsp_assureur', $cabinet->garantie_financiere_iobsp_assureur) }}">
                    </div>
                    <div class="wd-cabinet-field">
                        <label>N° de contrat</label>
                        <input type="text" name="garantie_financiere_iobsp_numero" value="{{ old('garantie_financiere_iobsp_numero', $cabinet->garantie_financiere_iobsp_numero) }}">
                    </div>
                    <div class="wd-cabinet-field">
                        <label>Montant (€)</label>
                        <input type="number" step="0.01" name="garantie_financiere_iobsp_montant" value="{{ old('garantie_financiere_iobsp_montant', $cabinet->garantie_financiere_iobsp_montant) }}">
                    </div>
                    <div class="wd-cabinet-field">
                        <label>Date de fin</label>
                        <input type="date" name="garantie_financiere_iobsp_date_fin" value="{{ old('garantie_financiere_iobsp_date_fin', $cabinet->garantie_financiere_iobsp_date_fin ? \Carbon\Carbon::parse($cabinet->garantie_financiere_iobsp_date_fin)->format('Y-m-d') : '') }}">
                    </div>
                </div>
                <div class="wd-cabinet-information-subhead">
                    Garantie financière Agent immobilier
                </div>
                <div class="wd-cabinet-radio-group">
                    @php $gfImmo = old('garantie_financiere_immo', $cabinet->garantie_financiere_immo); @endphp
                    <label class="wd-cabinet-radio">
                        <input type="radio" name="garantie_financiere_immo" value="oui" {{ $gfImmo === 'oui' ? 'checked' : '' }}>
                        <span>Oui</span>
                    </label>
                    <label class="wd-cabinet-radio">
                        <input type="radio" name="garantie_financiere_immo" value="non" {{ $gfImmo === 'non' ? 'checked' : '' }}>
                        <span>Non</span>
                    </label>
                </div>
                <div class="wd-cabinet-information-grid">
                    <div class="wd-cabinet-field">
                        <label>Assureur</label>
                        <input type="text" name="garantie_financiere_immo_assureur" value="{{ old('garantie_financiere_immo_assureur', $cabinet->garantie_financiere_immo_assureur) }}">
                    </div>
                    <div class="wd-cabinet-field">
                        <label>N° de contrat</label>
                        <input type="text" name="garantie_financiere_immo_numero" value="{{ old('garantie_financiere_immo_numero', $cabinet->garantie_financiere_immo_numero) }}">
                    </div>
                    <div class="wd-cabinet-field">
                        <label>Montant (€)</label>
                        <input type="number" step="0.01" name="garantie_financiere_immo_montant" value="{{ old('garantie_financiere_immo_montant', $cabinet->garantie_financiere_immo_montant) }}">
                    </div>
                    <div class="wd-cabinet-field">
                        <label>Date de fin</label>
                        <input type="date" name="garantie_financiere_immo_date_fin" value="{{ old('garantie_financiere_immo_date_fin', $cabinet->garantie_financiere_immo_date_fin ? \Carbon\Carbon::parse($cabinet->garantie_financiere_immo_date_fin)->format('Y-m-d') : '') }}">
                    </div>
                </div>
            </div>
            <div class="wd-cabinet-tab-panel" data-tab-panel="gouvernance" hidden>
                <div class="wd-cabinet-information-grid">
                    <div class="wd-cabinet-field">
                        <label>Nom du dirigeant</label>
                        <input type="text" name="dirigeant_nom" value="{{ old('dirigeant_nom', $cabinet->dirigeant_nom) }}">
                    </div>
                    <div class="wd-cabinet-field">
                        <label>Prénom du dirigeant</label>
                        <input type="text" name="dirigeant_prenom" value="{{ old('dirigeant_prenom', $cabinet->dirigeant_prenom) }}">
                    </div>
                    <div class="wd-cabinet-field">
                        <label>Fonction du dirigeant</label>
                        <input type="text" name="dirigeant_fonction" value="{{ old('dirigeant_fonction', $cabinet->dirigeant_fonction) }}">
                    </div>
                    <div class="wd-cabinet-field">
                        <label>Responsable conformité</label>
                        <input type="text" name="responsable_conformite" value="{{ old('responsable_conformite', $cabinet->responsable_conformite) }}">
                    </div>
                    <div class="wd-cabinet-field">
                        <label>Mail responsable conformité</label>
                        <input type="email" name="mail_responsable_conformite" value="{{ old('mail_responsable_conformite', $cabinet->mail_responsable_conformite) }}">
                    </div>
                    <div class="wd-cabinet-field">
                        <label>Responsable Tracfin / LCB-FT</label>
                        <input type="text" name="lcbft_responsable_nom" value="{{ old('lcbft_responsable_nom', $cabinet->lcbft_responsable_nom) }}">
                    </div>
                    <div class="wd-cabinet-field">
                        <label>Médiateur (nom)</label>
                        <input type="text" name="mediateur_nom" value="{{ old('mediateur_nom', $cabinet->mediateur_nom) }}">
                    </div>
                    <div class="wd-cabinet-field">
                        <label>Médiateur (contact)</label>
                        <input type="text" name="mediateur_contact" value="{{ old('mediateur_contact', $cabinet->mediateur_contact) }}">
                    </div>
                </div>
                <div class="wd-cabinet-information-subhead">
                    Conflits d'intérêts
                </div>
                <div class="wd-cabinet-radio-group">
                    @php $conflit = old('conflits_interets_existe', $cabinet->conflits_interets_existe); @endphp
                    <label class="wd-cabinet-radio">
                        <input type="radio" name="conflits_interets_existe" value="oui" {{ $conflit === 'oui' ? 'checked' : '' }}>
                        <span>Oui</span>
                    </label>
                    <label class="wd-cabinet-radio">
                        <input type="radio" name="conflits_interets_existe" value="non" {{ $conflit === 'non' ? 'checked' : '' }}>
                        <span>Non</span>
                    </label>
                </div>
                <div class="wd-cabinet-field">
                    <label>Description des situations de conflits d'intérêts et mesures mises en place</label>
                    <textarea name="conflits_interets_description" rows="4">{{ old('conflits_interets_description', $cabinet->conflits_interets_description) }}</textarea>
                </div>
            </div>
            <div class="wd-cabinet-tab-panel" data-tab-panel="partenaires" hidden>
                <div class="wd-cabinet-information-subhead">
                    Partenaires
                </div>
                <div data-partenaires-list>
                    @php $partenaires = $cabinet->partenaires ?? []; @endphp
                    @foreach($partenaires as $i => $partenaire)
                    <div class="wd-cabinet-partenaire-row">
                        <div class="wd-cabinet-information-grid">
                            <div class="wd-cabinet-field">
                                <label>Type</label>
                                @php $partenaireType = $partenaire['type'] ?? ''; @endphp
                                <select name="partenaires[{{ $i }}][type]">
                                    <option value="">Choisir</option>
                                    <option value="assureur" {{ $partenaireType === 'assureur' ? 'selected' : '' }}>Assureur</option>
                                    <option value="banque" {{ $partenaireType === 'banque' ? 'selected' : '' }}>Banque</option>
                                    <option value="societe_gestion" {{ $partenaireType === 'societe_gestion' ? 'selected' : '' }}>Société de gestion</option>
                                    <option value="promoteur_immobilier" {{ $partenaireType === 'promoteur_immobilier' ? 'selected' : '' }}>Promoteur immobilier</option>
                                    <option value="plateforme" {{ $partenaireType === 'plateforme' ? 'selected' : '' }}>Plateforme</option>
                                    <option value="autre" {{ $partenaireType === 'autre' ? 'selected' : '' }}>Autre</option>
                                </select>
                            </div>
                            <div class="wd-cabinet-field wd-cabinet-autocomplete-field">
                                <label>Nom du partenaire</label>
                                <input type="text" name="partenaires[{{ $i }}][nom]" value="{{ $partenaire['nom'] ?? '' }}" autocomplete="off" data-partenaire-nom data-partenaire-lockable {{ $partenaireType === '' ? 'disabled' : '' }}>
                                <div class="wd-cabinet-autocomplete-list" data-partenaire-suggestions hidden></div>
                            </div>
                            <div class="wd-cabinet-field">
                                <label>Mode de relation</label>
                                @php $partenaireModeRelation = $partenaire['mode_relation'] ?? ''; @endphp
                                <select name="partenaires[{{ $i }}][mode_relation]" data-partenaire-lockable {{ $partenaireType === '' ? 'disabled' : '' }}>
                                    <option value="">Choisir</option>
                                    <option value="courtier" {{ $partenaireModeRelation === 'courtier' ? 'selected' : '' }}>Courtier</option>
                                    <option value="mandataire" {{ $partenaireModeRelation === 'mandataire' ? 'selected' : '' }}>Mandataire</option>
                                    <option value="apporteur" {{ $partenaireModeRelation === 'apporteur' ? 'selected' : '' }}>Apporteur</option>
                                    <option value="partenaire_reference" {{ $partenaireModeRelation === 'partenaire_reference' ? 'selected' : '' }}>Partenaire référence</option>
                                </select>
                            </div>
                            <div class="wd-cabinet-field">
                                <label>Identifiant</label>
                                <input type="text" name="partenaires[{{ $i }}][identifiant]" value="{{ $partenaire['identifiant'] ?? '' }}" data-partenaire-lockable {{ $partenaireType === '' ? 'disabled' : '' }}>
                            </div>
                            <div class="wd-cabinet-field">
                                <label>URL</label>
                                <input type="text" name="partenaires[{{ $i }}][url]" value="{{ $partenaire['url'] ?? '' }}" data-partenaire-lockable {{ $partenaireType === '' ? 'disabled' : '' }}>
                            </div>
                            <div class="wd-cabinet-field">
                                <label>Notes</label>
                                <input type="text" name="partenaires[{{ $i }}][notes]" value="{{ $partenaire['notes'] ?? '' }}" data-partenaire-lockable {{ $partenaireType === '' ? 'disabled' : '' }}>
                            </div>
                        </div>
                        <button type="button" class="wd-cabinet-remove-row" data-remove-partenaire>Retirer</button>
                    </div>
                    @endforeach
                </div>
                <button type="button" class="wd-cabinet-add-row" data-add-partenaire>+ Ajouter un partenaire</button>
            </div>
            <button type="submit" class="wd-cabinet-save">Enregistrer</button>
        </form>
        <div class="wd-cabinet-tab-panel" data-tab-panel="identification">
            <div class="wd-cabinet-information-subhead">
                Données SIRENE (lecture seule)
            </div>
            <div class="wd-cabinet-information-grid">
                <div>
                    <span>SIREN</span>
                    <strong>
                        {{ $cabinet->siren ?: 'À renseigner' }}
                    </strong>
                </div>
                <div>
                    <span>SIRET</span>
                    <strong>
                        {{ $cabinet->siret ?: 'À renseigner' }}
                    </strong>
                </div>
                <div>
                    <span>Code APE</span>
                    <strong>
                        {{ $cabinet->code_ape ?: 'À renseigner' }}
                    </strong>
                </div>
                <div>
                    <span>État administratif</span>
                    <strong>
                        {{ $cabinet->etat_administratif === 'A' ? 'Actif' : ($cabinet->etat_administratif ?: 'À renseigner') }}
                    </strong>
                </div>
                <div>
                    <span>Date de création</span>
                    <strong>
                        {{ $cabinet->date_creation ? \Carbon\Carbon::parse($cabinet->date_creation)->translatedFormat('d F Y') : 'À renseigner' }}
                    </strong>
                </div>
            </div>
        </div>
    </section>
    <script>
    (function() {
        var tabs = document.querySelectorAll('.wd-cabinet-tab');
        tabs.forEach(function(btn) {
            btn.addEventListener('click', function() {
                var target = btn.dataset.tab;
                tabs.forEach(function(b) {
                    b.classList.toggle('active', b === btn);
                });
                document.querySelectorAll('[data-tab-panel]').forEach(function(panel) {
                    panel.hidden = panel.dataset.tabPanel !== target;
                });
            });
        });
    })();
    (function() {
        var list = document.querySelector('[data-partenaires-list]');
        var addBtn = document.querySelector('[data-add-partenaire]');
        if (!list || !addBtn) {
            return;
        }
        var index = list.querySelectorAll('.wd-cabinet-partenaire-row').length;
        var fields = [
            ['type', 'Type', 'select'],
            ['nom', 'Nom du partenaire', 'autocomplete'],
            ['mode_relation', 'Mode de relation', 'select'],
            ['identifiant', 'Identifiant', 'text'],
            ['url', 'URL', 'text'],
            ['notes', 'Notes', 'text']
        ];
        var selectOptions = {
            type: [
                ['', 'Choisir'],
                ['assureur', 'Assureur'],
                ['banque', 'Banque'],
                ['societe_gestion', 'Société de gestion'],
                ['promoteur_immobilier', 'Promoteur immobilier'],
                ['plateforme', 'Plateforme'],
                ['autre', 'Autre']
            ],
            mode_relation: [
                ['', 'Choisir'],
                ['courtier', 'Courtier'],
                ['mandataire', 'Mandataire'],
                ['apporteur', 'Apporteur'],
                ['partenaire_reference', 'Partenaire référence']
            ]
        };
        addBtn.addEventListener('click', function() {
            var row = document.createElement('div');
            row.className = 'wd-cabinet-partenaire-row';
            var grid = document.createElement('div');
            grid.className = 'wd-cabinet-information-grid';
            fields.forEach(function(f) {
                var field = document.createElement('div');
                field.className = 'wd-cabinet-field';
                var label = document.createElement('label');
                label.textContent = f[1];
                var input;
                if (f[2] === 'select') {
                    input = document.createElement('select');
                    selectOptions[f[0]].forEach(function(opt) {
                        var option = document.createElement('option');
                        option.value = opt[0];
                        option.textContent = opt[1];
                        input.appendChild(option);
                    });
                } else if (f[2] === 'autocomplete') {
                    field.className = 'wd-cabinet-field wd-cabinet-autocomplete-field';
                    input = document.createElement('input');
                    input.type = 'text';
                    input.autocomplete = 'off';
                    input.setAttribute('data-partenaire-nom', '');
                } else {
                    input = document.createElement('input');
                    input.type = f[2];
                }
                if (f[0] !== 'type') {
                    input.setAttribute('data-partenaire-lockable', '');
                    input.disabled = true;
                }
                input.name = 'partenaires[' + index + '][' + f[0] + ']';
                field.appendChild(label);
                field.appendChild(input);
                if (f[2] === 'autocomplete') {
                    var suggestions = document.createElement('div');
                    suggestions.className = 'wd-cabinet-autocomplete-list';
                    suggestions.setAttribute('data-partenaire-suggestions', '');
                    suggestions.hidden = true;
                    field.appendChild(suggestions);
                }
                grid.appendChild(field);
            });
            row.appendChild(grid);
            var removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'wd-cabinet-remove-row';
            removeBtn.setAttribute('data-remove-partenaire', '');
            removeBtn.textContent = 'Retirer';
            row.appendChild(removeBtn);
            list.appendChild(row);
            index++;
        });
        list.addEventListener('click', function(e) {
            if (e.target.matches('[data-remove-partenaire]')) {
                e.target.closest('.wd-cabinet-partenaire-row').remove();
            }
        });
        list.addEventListener('change', function(e) {
            if (!e.target.matches('select[name$="[type]"]')) {
                return;
            }
            var row = e.target.closest('.wd-cabinet-partenaire-row');
            var locked = e.target.value === '';
            row.querySelectorAll('[data-partenaire-lockable]').forEach(function(field) {
                field.disabled = locked;
                if (locked) {
                    field.value = '';
                }
            });
        });
    })();
    (function() {
        var list = document.querySelector('[data-partenaires-list]');
        if (!list) {
            return;
        }
        var ASSUREURS = [
            ['AXA France', 'axa.fr'],
            ['Generali', 'generali.fr'],
            ['CNP Assurances', 'cnp.fr'],
            ['Suravenir', 'suravenir.fr'],
            ['Spirica', 'spirica.fr'],
            ['Apicil', 'apicil.com'],
            ['Swiss Life', 'swisslife.fr'],
            ['Cardif (BNP Paribas)', 'cardif.fr'],
            ['Sogecap (Société Générale)', 'societegenerale.com'],
            ['Predica (Crédit Agricole)', 'credit-agricole.fr'],
            ['AG2R La Mondiale', 'ag2rlamondiale.fr'],
            ['Oradéa Vie', 'oradeavie.fr'],
            ['Neuflize Vie', 'neuflize.com'],
            ['Allianz Vie', 'allianz.fr'],
            ['MAIF Vie', 'maif.fr'],
            ['MACSF Épargne Retraite', 'macsf.fr'],
            ['Groupama Gan Vie', 'groupama.fr'],
            ['Abeille Vie', 'abeille-assurances.fr'],
            ['UAF Life Patrimoine', 'uaf-lifepatrimoine.fr'],
            ['Vie Plus', 'vieplus.fr'],
            ['Afer', 'afer.asso.fr'],
            ['La Banque Postale Assurances', 'labanquepostale.fr'],
            ['Mutavie', 'mutavie.fr'],
            ['Vitis Life', 'vitislife.com'],
            ['OneLife', 'onelife.eu.com'],
            ['Lombard International', 'lombardinternational.com'],
            ['Garance', 'garance.fr'],
            ['Malakoff Humanis', 'malakoffhumanis.com'],
            ['Wealins', 'wealins.com'],
            ['Coverlife', 'coverlife.lu']
        ];
        function normalize(str) {
            return (str || '').toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
        }
        function closeSuggestions(panel) {
            panel.hidden = true;
            panel.innerHTML = '';
        }
        function renderSuggestions(panel, input) {
            var query = normalize(input.value);
            var matches = ASSUREURS.filter(function(a) {
                return normalize(a[0]).indexOf(query) !== -1;
            }).slice(0, 8);
            panel.innerHTML = '';
            if (!matches.length) {
                closeSuggestions(panel);
                return;
            }
            matches.forEach(function(a) {
                var item = document.createElement('button');
                item.type = 'button';
                item.className = 'wd-cabinet-autocomplete-item';
                item.setAttribute('data-suggestion', '');
                item.setAttribute('data-value', a[0]);
                var img = document.createElement('img');
                img.src = 'https://logo.clearbit.com/' + a[1];
                img.alt = '';
                img.loading = 'lazy';
                img.onerror = function() { img.style.visibility = 'hidden'; };
                var span = document.createElement('span');
                span.textContent = a[0];
                item.appendChild(img);
                item.appendChild(span);
                panel.appendChild(item);
            });
            panel.hidden = false;
        }
        list.addEventListener('input', function(e) {
            if (!e.target.matches('[data-partenaire-nom]')) {
                return;
            }
            var row = e.target.closest('.wd-cabinet-partenaire-row');
            var typeSelect = row.querySelector('select[name$="[type]"]');
            var panel = row.querySelector('[data-partenaire-suggestions]');
            if (!panel || !typeSelect || typeSelect.value !== 'assureur') {
                if (panel) {
                    closeSuggestions(panel);
                }
                return;
            }
            renderSuggestions(panel, e.target);
        });
        list.addEventListener('focusin', function(e) {
            if (!e.target.matches('[data-partenaire-nom]')) {
                return;
            }
            var row = e.target.closest('.wd-cabinet-partenaire-row');
            var typeSelect = row.querySelector('select[name$="[type]"]');
            var panel = row.querySelector('[data-partenaire-suggestions]');
            if (panel && typeSelect && typeSelect.value === 'assureur') {
                renderSuggestions(panel, e.target);
            }
        });
        list.addEventListener('click', function(e) {
            var suggestion = e.target.closest('[data-suggestion]');
            if (!suggestion) {
                return;
            }
            var row = suggestion.closest('.wd-cabinet-partenaire-row');
            var input = row.querySelector('[data-partenaire-nom]');
            var panel = row.querySelector('[data-partenaire-suggestions]');
            input.value = suggestion.getAttribute('data-value');
            closeSuggestions(panel);
        });
        document.addEventListener('click', function(e) {
            if (e.target.closest('.wd-cabinet-autocomplete-field')) {
                return;
            }
            list.querySelectorAll('[data-partenaire-suggestions]').forEach(closeSuggestions);
        });
    })();
    </script>
    <!-- =====================================================
         TARIFS & RÉMUNÉRATION (éditable)
         ===================================================== -->
    <section id="tarifs-cabinet" class="wd-cabinet-information">
        <div class="wd-cabinet-information-head">
            <div>
                <div class="wd-eyebrow">Rémunération</div>
                <h2>Tarifs & rémunération.</h2>
                <p>
                    Définissez le mode de facturation de chacune de vos prestations.
                </p>
            </div>
        </div>
        @php $prestations = $cabinet->prestations ?? []; @endphp
        <form method="POST" action="{{ route('tenant.cabinet.update') }}">
            @csrf
            @method('PUT')
            <div class="wd-cabinet-information-subhead">
                Mode de rémunération
            </div>
            <div class="wd-cabinet-information-grid">
                <div class="wd-cabinet-field">
                    <label>Mode de rémunération</label>
                    @php $modeRem = old('mode_remuneration', $cabinet->mode_remuneration); @endphp
                    <select name="mode_remuneration" data-mode-remuneration>
                        <option value="">Choisir</option>
                        <option value="honoraires" {{ $modeRem === 'honoraires' ? 'selected' : '' }}>Honoraires exclusivement</option>
                        <option value="commissions" {{ $modeRem === 'commissions' ? 'selected' : '' }}>Commissions exclusivement</option>
                        <option value="honoraires_commissions" {{ $modeRem === 'honoraires_commissions' ? 'selected' : '' }}>Honoraires et commissions</option>
                    </select>
                </div>
            </div>
            <p class="wd-cabinet-tab-note" data-commission-note hidden>
                Rémunération perçue via commissions des partenaires : aucune grille tarifaire client à renseigner ci-dessous.
            </p>
            @php
                $prestationLabels = [
                    'Mandat de courtage banque',
                    'Mandat de courtage assurance',
                    'Conseils en investissements financiers (CIF)',
                ];
            @endphp
            @for($i = 0; $i < 3; $i++)
                @php $presta = $prestations[$i] ?? []; @endphp
                <div data-prestation-pricing>
                    <div class="wd-cabinet-information-subhead">
                        {{ $prestationLabels[$i] }}
                    </div>
                    <div class="wd-cabinet-information-grid">
                        <div class="wd-cabinet-field">
                            <label>Mode</label>
                            <div class="wd-cabinet-radio-group">
                                <label class="wd-cabinet-radio">
                                    <input type="radio" name="prestations[{{ $i }}][mode]" value="forfait" data-prestation-mode {{ ($presta['mode'] ?? '') === 'forfait' ? 'checked' : '' }}>
                                    <span>Forfait</span>
                                </label>
                                <label class="wd-cabinet-radio">
                                    <input type="radio" name="prestations[{{ $i }}][mode]" value="pourcentage" data-prestation-mode {{ ($presta['mode'] ?? '') === 'pourcentage' ? 'checked' : '' }}>
                                    <span>Pourcentage</span>
                                </label>
                            </div>
                        </div>
                        <div class="wd-cabinet-field" data-prestation-champ="forfait">
                            <label>Montant forfait (€)</label>
                            <input type="number" step="0.01" name="prestations[{{ $i }}][forfait]" value="{{ $presta['forfait'] ?? '' }}">
                        </div>
                        <div class="wd-cabinet-field" data-prestation-champ="pourcentage">
                            <label>Taux (%)</label>
                            <input type="number" step="0.01" name="prestations[{{ $i }}][pourcentage]" value="{{ $presta['pourcentage'] ?? '' }}">
                        </div>
                    </div>
                </div>
            @endfor
            <button type="submit" class="wd-cabinet-save">Enregistrer</button>
        </form>
        <script>
        (function() {
            var select = document.querySelector('[data-mode-remuneration]');
            if (!select) {
                return;
            }
            var blocks = document.querySelectorAll('[data-prestation-pricing]');
            var note = document.querySelector('[data-commission-note]');
            function apply() {
                var isCommissions = select.value === 'commissions';
                blocks.forEach(function(block) {
                    block.hidden = isCommissions;
                });
                if (note) {
                    note.hidden = !isCommissions;
                }
            }
            select.addEventListener('change', apply);
            apply();
        })();

        (function() {
            document.querySelectorAll('[data-prestation-pricing]').forEach(function(block) {
                var radios = block.querySelectorAll('[data-prestation-mode]');
                var champForfait = block.querySelector('[data-prestation-champ="forfait"]');
                var champPourcentage = block.querySelector('[data-prestation-champ="pourcentage"]');
                function appliquer() {
                    var choisi = null;
                    radios.forEach(function(r) {
                        if (r.checked) { choisi = r.value; }
                    });
                    if (champForfait) { champForfait.hidden = choisi !== 'forfait'; }
                    if (champPourcentage) { champPourcentage.hidden = choisi !== 'pourcentage'; }
                }
                radios.forEach(function(r) {
                    r.addEventListener('change', appliquer);
                });
                appliquer();
            });
        })();
        </script>
    </section>
    <!-- =====================================================
         OBJECTIFS (éditable)
         ===================================================== -->
    <section id="objectifs-cabinet" class="wd-cabinet-information">
        <div class="wd-cabinet-information-head">
            <div>
                <div class="wd-eyebrow">Pilotage</div>
                <h2>Objectifs.</h2>
                <p>
                    Vos objectifs d'activité hebdomadaires, mensuels et annuels.
                </p>
            </div>
        </div>
        @php $objectifs = Auth::user()->objectifs ?? []; @endphp
        <form method="POST" action="{{ route('tenant.cabinet.update') }}">
            @csrf
            @method('PUT')
            <div class="wd-cabinet-information-subhead">
                Par semaine
            </div>
            <div class="wd-cabinet-information-grid">
                <div class="wd-cabinet-field">
                    <label>Clients / semaine</label>
                    <input type="number" name="objectifs[client_semaine]" value="{{ $objectifs['client_semaine'] ?? '' }}">
                </div>
                <div class="wd-cabinet-field">
                    <label>RDV / semaine</label>
                    <input type="number" name="objectifs[rdv_semaine]" value="{{ $objectifs['rdv_semaine'] ?? '' }}">
                </div>
                <div class="wd-cabinet-field">
                    <label>Collectes / semaine</label>
                    <input type="number" name="objectifs[collectes_semaine]" value="{{ $objectifs['collectes_semaine'] ?? '' }}">
                </div>
            </div>
            <div class="wd-cabinet-information-subhead">
                Performance & revenu
            </div>
            <div class="wd-cabinet-information-grid">
                <div class="wd-cabinet-field">
                    <label>Taux de transformation (%)</label>
                    <input type="number" step="0.01" name="objectifs[taux_transformation]" value="{{ $objectifs['taux_transformation'] ?? '' }}">
                </div>
                <div class="wd-cabinet-field">
                    <label>Revenu mensuel (€)</label>
                    <input type="number" step="0.01" name="objectifs[revenu_mensuel]" value="{{ $objectifs['revenu_mensuel'] ?? '' }}">
                </div>
                <div class="wd-cabinet-field">
                    <label>Revenu annuel (€)</label>
                    <input type="number" step="0.01" name="objectifs[revenu_annuel]" value="{{ $objectifs['revenu_annuel'] ?? '' }}">
                </div>
            </div>
            <button type="submit" class="wd-cabinet-save">Enregistrer</button>
        </form>
    </section>
    @endif
</div>
<style id="wd-cabinet-design">
.wd-cabinet-success{
    margin-top:28px;
    padding:14px 18px;
    background:#f3f9f4;
    border:1px solid #d7e8da;
    border-radius:8px;
    color:#4d8760;
    font-size:12px;
    font-weight:700;
}
.wd-cabinet-alert{
    margin-top:28px;
    padding:22px 24px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:30px;
    background:#24201f;
    border-radius:10px;
    border-left:4px solid #f40087;
}
.wd-cabinet-alert-main{
    display:flex;
    align-items:center;
    gap:17px;
}
.wd-cabinet-alert-icon{
    width:38px;
    height:38px;
    flex:0 0 38px;
    display:grid;
    place-items:center;
    border-radius:50%;
    background:#f40087;
    color:#fff;
    font-size:17px;
    font-weight:800;
}
.wd-cabinet-alert-kicker{
    color:#f40087;
    font-size:9px;
    font-weight:800;
    letter-spacing:.15em;
    text-transform:uppercase;
}
.wd-cabinet-alert h2{
    margin:5px 0 0;
    color:#fff;
    font-size:18px;
    letter-spacing:-.025em;
}
.wd-cabinet-alert p{
    margin:5px 0 0;
    color:#aaa29e;
    font-size:11px;
}
.wd-cabinet-alert-button{
    display:flex;
    align-items:center;
    gap:12px;
    flex:0 0 auto;
    padding:11px 16px;
    border-radius:7px;
    background:#f40087;
    color:#fff;
    text-decoration:none;
    font-size:10px;
    font-weight:800;
    white-space:nowrap;
}
.wd-cabinet-alert-button span{
    font-size:15px;
}
.wd-cabinet-alert-ok{
    border-left-color:#4d8760;
}
.wd-cabinet-alert-ok .wd-cabinet-alert-icon{
    background:#4d8760;
}
.wd-cabinet-alert-ok .wd-cabinet-alert-kicker{
    color:#4d8760;
}
.wd-cabinet-modal-overlay{
    position:fixed;
    inset:0;
    background:rgba(20,16,15,.55);
    display:flex;
    align-items:center;
    justify-content:center;
    z-index:2000;
    padding:20px;
}
.wd-cabinet-modal-overlay[hidden]{
    display:none;
}
.wd-cabinet-modal{
    background:#fff;
    border-radius:12px;
    max-width:480px;
    width:100%;
    max-height:80vh;
    overflow-y:auto;
    padding:24px;
}
.wd-cabinet-modal-head{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:16px;
}
.wd-cabinet-modal-head h3{
    margin:0;
    font-size:16px;
    color:#242424;
}
.wd-cabinet-modal-close{
    border:0;
    background:none;
    font-size:22px;
    line-height:1;
    color:#9a928d;
    cursor:pointer;
    padding:0;
}
.wd-cabinet-modal-section{
    margin-bottom:18px;
}
.wd-cabinet-modal-section:last-child{
    margin-bottom:0;
}
.wd-cabinet-modal-section-title{
    color:#9a928d;
    font-size:9px;
    font-weight:800;
    letter-spacing:.13em;
    text-transform:uppercase;
    margin-bottom:8px;
}
.wd-cabinet-modal-section ul{
    list-style:none;
    margin:0;
    padding:0;
}
.wd-cabinet-modal-item{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:10px;
    width:100%;
    padding:9px 11px;
    border:1px solid #eeeae7;
    border-radius:7px;
    background:#fdfcfb;
    color:#242424;
    font-size:12px;
    font-family:inherit;
    cursor:pointer;
    text-align:left;
    margin-bottom:6px;
}
.wd-cabinet-modal-item:hover{
    border-color:#f40087;
    color:#f40087;
}
.wd-cabinet-modal-item span{
    color:#f40087;
    font-size:13px;
}
.wd-cabinet-section{
    margin-top:34px;
}
.wd-cabinet-section-head{
    display:flex;
    justify-content:space-between;
    align-items:flex-end;
    margin-bottom:15px;
}
.wd-cabinet-section-head h2{
    margin:5px 0 0;
    color:#242424;
    font-size:21px;
    letter-spacing:-.035em;
}
.wd-cabinet-section-head>span{
    color:#9a928d;
    font-size:10px;
}
.wd-cabinet-resources{
    display:grid;
    grid-template-columns:repeat(3,minmax(0,1fr));
    gap:15px;
}
.wd-cabinet-resource{
    min-height:205px;
    padding:20px;
    position:relative;
    display:flex;
    flex-direction:column;
    background:#fff;
    border:1px solid #ded9d4;
    border-radius:10px;
    color:inherit;
    text-decoration:none;
    transition:transform .15s ease,box-shadow .15s ease;
}
.wd-cabinet-resource:hover{
    transform:translateY(-2px);
    box-shadow:0 8px 22px rgba(30,25,23,.07);
}
.wd-cabinet-resource-top{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
}
.wd-cabinet-resource-icon{
    width:38px;
    height:38px;
    display:grid;
    place-items:center;
    border-radius:8px;
    background:#f3f1ee;
}
.wd-cabinet-resource-icon svg{
    width:19px;
    height:19px;
    fill:none;
    stroke:#242424;
    stroke-width:1.7;
    stroke-linecap:round;
    stroke-linejoin:round;
}
.wd-cabinet-resource-arrow{
    color:#f40087;
    font-size:16px;
}
.wd-cabinet-resource-label{
    margin-top:21px;
    color:#9a928d;
    font-size:9px;
    font-weight:800;
    letter-spacing:.14em;
    text-transform:uppercase;
}
.wd-cabinet-resource h3{
    margin:5px 0 0;
    color:#242424;
    font-size:18px;
    letter-spacing:-.025em;
}
.wd-cabinet-resource p{
    margin:6px 0 0;
    color:#817b76;
    font-size:11px;
    line-height:1.5;
}
.wd-cabinet-resource-footer{
    margin-top:auto;
    padding-top:15px;
    display:flex;
    align-items:baseline;
    gap:5px;
    border-top:1px solid #eeeae7;
}
.wd-cabinet-resource-footer strong{
    color:#242424;
    font-size:16px;
}
.wd-cabinet-resource-footer span{
    color:#9a928d;
    font-size:9px;
}
.wd-cabinet-management-grid{
    display:grid;
    grid-template-columns:repeat(3,minmax(0,1fr));
    gap:12px;
}
.wd-cabinet-management{
    min-height:112px;
    padding:18px 20px;
    display:flex;
    align-items:center;
    gap:15px;
    position:relative;
    background:#fff;
    border:1px solid #ded9d4;
    border-radius:11px;
    overflow:hidden;
    color:inherit;
    text-decoration:none;
}
.wd-cabinet-management:hover{
    border-color:#cbc4bf;
    background:#fdfcfb;
}
.wd-cabinet-management-icon{
    width:36px;
    height:36px;
    flex:0 0 36px;
    display:grid;
    place-items:center;
    border-radius:8px;
    background:#f3f1ee;
}
.wd-cabinet-management-icon svg{
    width:18px;
    height:18px;
    fill:none;
    stroke:#242424;
    stroke-width:1.6;
    stroke-linecap:round;
    stroke-linejoin:round;
}
.wd-cabinet-management-content{
    padding-right:25px;
}
.wd-cabinet-management-content span{
    color:#9a928d;
    font-size:8px;
    font-weight:800;
    letter-spacing:.13em;
    text-transform:uppercase;
}
.wd-cabinet-management-content strong{
    display:block;
    margin-top:4px;
    color:#242424;
    font-size:14px;
}
.wd-cabinet-management-content p{
    margin:4px 0 0;
    color:#817b76;
    font-size:10px;
}
.wd-cabinet-management-arrow{
    position:absolute;
    right:18px;
    color:#f40087;
    font-size:16px;
}
.wd-cabinet-information{
    margin-top:32px;
    padding:23px;
    background:#fff;
    border:1px solid #ded9d4;
    border-radius:10px;
    scroll-margin-top:90px;
}
.wd-cabinet-information-head{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    gap:20px;
}
.wd-cabinet-information-head h2{
    margin:5px 0 0;
    color:#242424;
    font-size:20px;
    letter-spacing:-.035em;
}
.wd-cabinet-information-head p{
    margin:6px 0 0;
    color:#817b76;
    font-size:11px;
}
.wd-cabinet-information-grid{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:0;
    margin-top:20px;
    border-top:1px solid #eeeae7;
}
.wd-cabinet-information-grid>div{
    padding:16px 18px 3px 0;
    border-bottom:1px solid #eeeae7;
}
.wd-cabinet-information-grid span{
    display:block;
    color:#9a928d;
    font-size:8px;
    font-weight:800;
    letter-spacing:.12em;
    text-transform:uppercase;
}
.wd-cabinet-information-grid strong{
    display:block;
    margin-top:5px;
    color:#242424;
    font-size:12px;
}
.wd-cabinet-information-subhead{
    margin-top:24px;
    color:#9a928d;
    font-size:10px;
    font-weight:800;
    letter-spacing:.13em;
    text-transform:uppercase;
}
.wd-cabinet-field{
    padding:16px 18px 14px 0;
    border-bottom:1px solid #eeeae7;
}
.wd-cabinet-field label{
    display:block;
    color:#9a928d;
    font-size:8px;
    font-weight:800;
    letter-spacing:.12em;
    text-transform:uppercase;
    margin-bottom:8px;
}
.wd-cabinet-field input[type=text],
.wd-cabinet-field input[type=number],
.wd-cabinet-field input[type=email],
.wd-cabinet-field input[type=tel],
.wd-cabinet-field input[type=date],
.wd-cabinet-field select,
.wd-cabinet-field textarea{
    width:calc(100% - 18px);
    border:1px solid #ded9d4;
    border-radius:7px;
    padding:9px 11px;
    font-size:13px;
    color:#242424;
    background:#fff;
    font-family:inherit;
}
.wd-cabinet-field textarea{
    resize:vertical;
    min-height:80px;
}
.wd-cabinet-field input:focus,
.wd-cabinet-field select:focus,
.wd-cabinet-field textarea:focus{
    outline:none;
    border-color:#f40087;
}
.wd-cabinet-field input:disabled,
.wd-cabinet-field select:disabled{
    background:#f5f2f0;
    color:#b3aca6;
    cursor:not-allowed;
}
.wd-cabinet-partenaire-row{
    padding:16px 18px 18px 0;
    border-bottom:1px solid #eeeae7;
    margin-bottom:4px;
}
.wd-cabinet-add-row,
.wd-cabinet-remove-row{
    border:1px solid #ded9d4;
    border-radius:7px;
    background:#fff;
    color:#817b76;
    font-size:11px;
    font-weight:700;
    padding:8px 14px;
    cursor:pointer;
    font-family:inherit;
    margin-top:8px;
}
.wd-cabinet-add-row:hover{
    border-color:#f40087;
    color:#f40087;
}
.wd-cabinet-remove-row{
    color:#c0392b;
    border-color:#eececa;
}
.wd-cabinet-remove-row:hover{
    background:#fdf2f0;
}
.wd-cabinet-autocomplete-field{
    position:relative;
}
.wd-cabinet-autocomplete-list{
    position:absolute;
    top:100%;
    left:0;
    right:18px;
    z-index:20;
    background:#fff;
    border:1px solid #ded9d4;
    border-radius:7px;
    box-shadow:0 8px 20px rgba(0,0,0,.08);
    max-height:240px;
    overflow-y:auto;
    margin-top:2px;
}
.wd-cabinet-autocomplete-item{
    display:flex;
    align-items:center;
    gap:10px;
    width:100%;
    padding:8px 11px;
    border:0;
    background:none;
    text-align:left;
    cursor:pointer;
    font-family:inherit;
    font-size:12px;
    color:#242424;
}
.wd-cabinet-autocomplete-item:hover{
    background:#fdf2f8;
}
.wd-cabinet-autocomplete-item img{
    width:20px;
    height:20px;
    object-fit:contain;
    border-radius:4px;
    flex-shrink:0;
}
.wd-cabinet-logo-field{
    padding:16px 18px 14px 0;
    border-bottom:1px solid #eeeae7;
    margin-bottom:4px;
}
.wd-cabinet-logo-field label{
    display:block;
    color:#9a928d;
    font-size:8px;
    font-weight:800;
    letter-spacing:.12em;
    text-transform:uppercase;
    margin-bottom:8px;
}
.wd-cabinet-logo-preview{
    display:block;
    max-height:60px;
    margin-bottom:10px;
}
.wd-cabinet-tabs{
    display:flex;
    gap:4px;
    margin-top:22px;
    border-bottom:1px solid #eeeae7;
    flex-wrap:wrap;
}
.wd-cabinet-tab{
    padding:10px 14px;
    border:0;
    background:none;
    color:#9a928d;
    font-size:11px;
    font-weight:800;
    letter-spacing:.02em;
    cursor:pointer;
    font-family:inherit;
    border-bottom:2px solid transparent;
    margin-bottom:-1px;
}
.wd-cabinet-tab.active{
    color:#242424;
    border-bottom-color:#f40087;
}
.wd-cabinet-tab-panel[hidden]{
    display:none;
}
.wd-cabinet-tab-note{
    margin-top:18px;
    color:#9a928d;
    font-size:11px;
}
.wd-cabinet-checkbox-group{
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:10px;
    margin-top:16px;
}
.wd-cabinet-checkbox{
    display:flex;
    align-items:center;
    gap:10px;
    padding:9px 12px;
    border:1px solid #ded9d4;
    border-radius:7px;
    font-size:12px;
    font-weight:700;
    color:#817b76;
    cursor:pointer;
}
.wd-cabinet-checkbox input{
    display:none;
}
.wd-cabinet-checkbox-box{
    width:16px;
    height:16px;
    flex:0 0 16px;
    display:grid;
    place-items:center;
    border:1px solid #ded9d4;
    border-radius:4px;
    background:#fff;
}
.wd-cabinet-checkbox-box svg{
    width:10px;
    height:10px;
    display:none;
    fill:none;
    stroke:#fff;
    stroke-width:3;
    stroke-linecap:round;
    stroke-linejoin:round;
}
.wd-cabinet-checkbox:has(input:checked){
    border-color:#242424;
    color:#242424;
    background:#f3f1ee;
}
.wd-cabinet-checkbox:has(input:checked) .wd-cabinet-checkbox-box{
    border-color:#242424;
    background:#242424;
}
.wd-cabinet-checkbox:has(input:checked) .wd-cabinet-checkbox-box svg{
    display:block;
}
.wd-cabinet-radio-group{
    display:flex;
    flex-wrap:wrap;
    gap:8px;
    width:calc(100% - 18px);
}
.wd-cabinet-radio{
    flex:1 1 auto;
    min-width:110px;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:9px 8px;
    border:1px solid #ded9d4;
    border-radius:7px;
    font-size:11px;
    font-weight:700;
    color:#817b76;
    cursor:pointer;
}
.wd-cabinet-radio input{
    display:none;
}
.wd-cabinet-radio:has(input:checked){
    border-color:#242424;
    color:#242424;
    background:#f3f1ee;
}
.wd-cabinet-save{
    margin-top:20px;
    display:inline-flex;
    align-items:center;
    gap:10px;
    padding:11px 18px;
    border-radius:7px;
    background:#242424;
    color:#fff;
    border:0;
    font-size:11px;
    font-weight:800;
    letter-spacing:.02em;
    cursor:pointer;
    font-family:inherit;
}
.wd-cabinet-save:hover{
    background:#171717;
}
@media(max-width:900px){
    .wd-cabinet-resources{
        grid-template-columns:1fr;
    }
    .wd-cabinet-management-grid{
        grid-template-columns:1fr;
    }
    .wd-cabinet-information-grid{
        grid-template-columns:1fr 1fr;
    }
}
@media(max-width:650px){
    .wd-cabinet-alert{
        flex-direction:column;
        align-items:flex-start;
    }
    .wd-cabinet-alert-button{
        width:100%;
        justify-content:center;
    }
    .wd-cabinet-information-grid{
        grid-template-columns:1fr;
    }
    .wd-cabinet-radio-group{
        flex-direction:column;
    }
}
</style>
</x-tenant-app-layout>
