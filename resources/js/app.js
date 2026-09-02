
import Alpine from 'alpinejs';

window.addressAutocomplete = function (type, initial = '') {
    return {
        query: initial,
        results: [],
        open: false,
        loading: false,
        async search() {
            if (this.query.length < 3) {
                this.results = [];
                this.open = false;
                return;
            }
            this.loading = true;
            const params = new URLSearchParams({ q: this.query, limit: 5 });
            if (type) params.set('type', type);
            try {
                const res = await fetch(`https://api-adresse.data.gouv.fr/search/?${params}`);
                const data = await res.json();
                this.results = data.features || [];
                this.open = this.results.length > 0;
            } catch (e) {
                this.results = [];
                this.open = false;
            }
            this.loading = false;
        },
        select(feature, fill) {
            this.open = false;
            fill(feature.properties);
        },
    };
};

window.villeAutocomplete = function (initial) {
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
};

window.siretLookup = function (societeFieldId) {
    return {
        loading: false,
        async rechercher(e) {
            const siret = (e.target.value || '').replace(/\D/g, '');
            if (siret.length !== 14) {
                return;
            }
            this.loading = true;
            try {
                const res = await fetch(`/sirene/${siret}`);
                if (!res.ok) {
                    return;
                }
                const data = await res.json();
                const target = document.getElementById(societeFieldId);
                if (target && !target.value && data.raison_sociale) {
                    target.value = data.raison_sociale;
                }
            } catch (err) {
                // Silencieux : la recherche SIRENE est un confort de saisie, jamais un blocage.
            }
            this.loading = false;
        },
    };
};

window.patrimoineForm = function (elements) {
    return {
        blocks: elements,
        total(cat) {
            return this.blocks[cat].reduce((s, e) => {
                const montant = parseFloat(e.montant) || 0;
                return s + (e.periodicite === 'mensuel' ? montant * 12 : montant);
            }, 0);
        },
        get totalActifs() {
            return this.total('actif_financier') + this.total('actif_non_financier');
        },
        get totalPassifs() {
            return this.total('passif');
        },
        get solde() {
            return this.totalActifs - this.totalPassifs;
        },
        get totalRevenus() {
            return this.total('revenu');
        },
        get totalCharges() {
            return this.total('charge');
        },
        get resteAVivre() {
            return this.totalRevenus - this.totalCharges;
        },
        eur(v) {
            return (v || 0).toLocaleString('fr-FR', { style: 'currency', currency: 'EUR' });
        },
    };
};

window.Alpine = Alpine;

Alpine.start();
