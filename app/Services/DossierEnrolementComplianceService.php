<?php

namespace App\Services;

use App\Models\DossierEnrolement;

/**
 * Moteur de conformité du dossier d'enrôlement d'un conseiller mandataire :
 * règles bloquantes et non bloquantes (pas de score global), voir
 * claude/enrolement-conseillers-mandataires.md section 6.
 *
 * Chaque contrôle retourne un statut : 'ok' (🟢), 'attention' (🟠) ou
 * 'bloquant' (🔴). Le statut global se déduit de la pire valeur trouvée.
 */
class DossierEnrolementComplianceService
{
    public function evaluer(DossierEnrolement $dossier): array
    {
        $checks = [
            'identite' => $this->controleIdentite($dossier),
            'orias' => $this->controleOrias($dossier),
            'capacite_professionnelle' => $this->controleCapaciteProfessionnelle($dossier),
            'honorabilite' => $this->controleHonorabilite($dossier),
            'rcp' => $this->controleRcp($dossier),
            'garantie_financiere' => $this->controleGarantieFinanciere($dossier),
            'formation_continue' => $this->controleFormationContinue($dossier),
            'documents' => $this->controleDocuments($dossier),
            'lcbft' => $this->controleProcedure($dossier, 'lcbft', 'Procédure LCB-FT'),
            'reclamations' => $this->controleProcedure($dossier, 'reclamations', 'Procédure réclamations'),
            'rgpd' => $this->controleProcedure($dossier, 'rgpd', 'Politique RGPD'),
        ];

        $aUnBlocage = collect($checks)->contains(fn ($c) => $c['statut'] === 'bloquant');
        $aUneAttention = collect($checks)->contains(fn ($c) => $c['statut'] === 'attention');

        $conformiteReglementaire = $aUnBlocage ? 'non_conforme' : 'conforme';
        $statutGlobal = $aUnBlocage ? 'non_eligible' : ($aUneAttention ? 'a_valider' : 'eligible');

        return [
            'checks' => $checks,
            'conformite_reglementaire' => $conformiteReglementaire,
            'statut_global' => $statutGlobal,
        ];
    }

    private function ok(string $label): array
    {
        return ['statut' => 'ok', 'label' => $label];
    }

    private function attention(string $label): array
    {
        return ['statut' => 'attention', 'label' => $label];
    }

    private function bloquant(string $label): array
    {
        return ['statut' => 'bloquant', 'label' => $label];
    }

    private function na(string $label): array
    {
        return ['statut' => 'na', 'label' => $label];
    }

    private function controleIdentite(DossierEnrolement $dossier): array
    {
        if (! $dossier->mode_exercice) {
            return $this->bloquant('Identité non renseignée.');
        }

        if ($dossier->mode_exercice === 'societe') {
            $complet = $dossier->societe_denomination
                && $dossier->societe_siren
                && $dossier->representant_nom
                && $dossier->representant_prenom;
        } else {
            $complet = $dossier->civilite
                && $dossier->date_naissance
                && $dossier->adresse
                && $dossier->ville;
        }

        return $complet ? $this->ok('Identité complète.') : $this->bloquant('Identité incomplète.');
    }

    private function controleOrias(DossierEnrolement $dossier): array
    {
        if (empty($dossier->orias_numero)) {
            return $this->bloquant('Numéro ORIAS manquant.');
        }

        if (empty($dossier->orias_categories)) {
            return $this->bloquant('Catégorie ORIAS non renseignée.');
        }

        if ($dossier->orias_statut_actif === false) {
            return $this->bloquant('Immatriculation ORIAS inactive.');
        }

        return match ($dossier->orias_controle) {
            'verifie' => $this->ok('ORIAS vérifié.'),
            'non_conforme' => $this->bloquant('ORIAS déclaré non conforme.'),
            default => $this->attention('ORIAS déclaré, vérification cabinet en attente.'),
        };
    }

    private function controleCapaciteProfessionnelle(DossierEnrolement $dossier): array
    {
        $fondements = $dossier->capacite_pro_fondement ?? [];

        if (empty($fondements)) {
            return $this->bloquant('Capacité professionnelle non justifiée.');
        }

        if (in_array('diplome', $fondements, true) && empty($dossier->capacite_pro_diplome_intitule)) {
            return $this->attention('Diplôme sélectionné mais détails incomplets.');
        }

        if (in_array('experience', $fondements, true) && empty($dossier->capacite_pro_experience_employeur)) {
            return $this->attention('Expérience sélectionnée mais détails incomplets.');
        }

        if (in_array('formation', $fondements, true) && empty($dossier->capacite_pro_formation_organisme)) {
            return $this->attention('Formation sélectionnée mais détails incomplets.');
        }

        return $this->ok('Capacité professionnelle justifiée.');
    }

    private function controleHonorabilite(DossierEnrolement $dossier): array
    {
        return $dossier->honorabilite_declaree
            ? $this->ok('Honorabilité déclarée sur l\'honneur.')
            : $this->bloquant('Déclaration d\'honorabilité manquante.');
    }

    private function controleRcp(DossierEnrolement $dossier): array
    {
        if (empty($dossier->rcp_assureur) || empty($dossier->rcp_numero_police) || ! $dossier->rcp_date_expiration) {
            return $this->bloquant('RCP non renseignée.');
        }

        if ($dossier->rcp_date_expiration->isPast()) {
            return $this->bloquant('RCP expirée.');
        }

        if ($dossier->rcp_date_expiration->lt(now()->addDays(30))) {
            return $this->attention('RCP expire dans moins de 30 jours.');
        }

        return $this->ok('RCP valide.');
    }

    private function controleGarantieFinanciere(DossierEnrolement $dossier): array
    {
        if (! $dossier->encaissement_fonds) {
            return $this->na('Encaissement de fonds non autorisé, garantie non applicable.');
        }

        if (empty($dossier->garantie_financiere_organisme) || ! $dossier->garantie_financiere_date_echeance) {
            return $this->bloquant('Encaissement autorisé mais garantie financière non renseignée.');
        }

        if ($dossier->garantie_financiere_date_echeance->isPast()) {
            return $this->bloquant('Garantie financière expirée.');
        }

        return $this->ok('Garantie financière valide.');
    }

    private function controleFormationContinue(DossierEnrolement $dossier): array
    {
        if ($dossier->statut === 'invited' || $dossier->statut === 'onboarding') {
            return $this->na('Non applicable avant activation.');
        }

        return $dossier->formation_continue_realisee
            ? $this->ok('Formation continue à jour.')
            : $this->attention('Formation continue non renseignée pour l\'année en cours.');
    }

    private function controleDocuments(DossierEnrolement $dossier): array
    {
        $typesObligatoires = ['identite', 'orias', 'rcp'];

        if ($dossier->encaissement_fonds) {
            $typesObligatoires[] = 'garantie_financiere';
        }

        $typesRecus = $dossier->justificatifs()
            ->whereIn('type', $typesObligatoires)
            ->pluck('type')
            ->unique();

        $manquants = array_diff($typesObligatoires, $typesRecus->all());

        return empty($manquants)
            ? $this->ok('Justificatifs obligatoires reçus.')
            : $this->attention('Justificatifs manquants : ' . implode(', ', $manquants) . '.');
    }

    private function controleProcedure(DossierEnrolement $dossier, string $cle, string $label): array
    {
        $procedures = $dossier->procedures_acceptees ?? [];

        $acceptee = collect($procedures)->contains(fn ($p) => ($p['cle'] ?? null) === $cle);

        return $acceptee
            ? $this->ok($label . ' acceptée.')
            : $this->bloquant($label . ' non acceptée.');
    }
}
