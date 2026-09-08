<?php

namespace App\Services;

use App\Models\CabinetProfile;
use App\Models\DossierEnrolement;

/**
 * Génère le corps HTML de la convention de mandataire d'intermédiaire
 * d'assurance à partir des variables du dossier d'enrôlement, avec
 * clauses conditionnelles (encaissement de fonds notamment). Génération
 * déterministe par gabarit, pas d'IA : voir
 * claude/enrolement-conseillers-mandataires.md sections 9 à 11.
 */
class ConventionMandatService
{
    public function genererHtml(DossierEnrolement $dossier, ?CabinetProfile $cabinet): string
    {
        $mandataireNom = $dossier->mode_exercice === 'societe'
            ? ($dossier->societe_denomination ?: $dossier->user->name)
            : $dossier->user->name;

        $mandantNom = $cabinet?->raison_sociale ?: $cabinet?->nom_commercial ?: 'Le Cabinet';

        $html = '';

        $html .= '<h2 class="section-title">1. Identification des parties</h2>';
        $html .= '<p class="corps-paragraphe">La présente convention est conclue entre :</p>';
        $html .= '<p class="corps-paragraphe"><strong>' . e($mandantNom) . '</strong>'
            . ($cabinet?->numero_orias ? ', immatriculé ORIAS n° ' . e($cabinet->numero_orias) : '')
            . ', ci-après désigné « le Mandant »,</p>';
        $html .= '<p class="corps-paragraphe">Et</p>';

        if ($dossier->mode_exercice === 'societe') {
            $html .= '<p class="corps-paragraphe"><strong>' . e($dossier->societe_denomination) . '</strong>'
                . ($dossier->societe_forme_juridique ? ', ' . e($dossier->societe_forme_juridique) : '')
                . ($dossier->societe_siren ? ', SIREN ' . e($dossier->societe_siren) : '')
                . ($dossier->societe_rcs ? ', RCS ' . e($dossier->societe_ville_rcs) . ' ' . e($dossier->societe_rcs) : '')
                . ', représentée par ' . e(trim($dossier->representant_prenom . ' ' . $dossier->representant_nom))
                . ($dossier->representant_fonction ? ', ' . e($dossier->representant_fonction) : '')
                . ', ci-après désigné « le Mandataire »,</p>';
        } else {
            $html .= '<p class="corps-paragraphe"><strong>' . e($mandataireNom) . '</strong>'
                . ($dossier->orias_numero ? ', immatriculé ORIAS n° ' . e($dossier->orias_numero) : '')
                . ', ci-après désigné « le Mandataire »,</p>';
        }

        $html .= '<h2 class="section-title">2. Statut et cadre réglementaire</h2>';
        $html .= '<p class="corps-paragraphe">Le Mandataire exerce en qualité de mandataire d\'intermédiaire d\'assurance, '
            . 'immatriculé auprès de l\'ORIAS' . ($dossier->orias_numero ? ' sous le numéro ' . e($dossier->orias_numero) : '')
            . '. Il agit sous la responsabilité du Mandant dans les conditions et limites définies par la présente convention.</p>';

        $html .= '<h2 class="section-title">3. Périmètre du mandat</h2>';
        $zoneLabels = [
            'france_entiere' => 'France entière',
            'region' => 'la région ' . ($dossier->mandat_zone_detail ?: ''),
            'departements' => 'le(s) département(s) ' . ($dossier->mandat_zone_detail ?: ''),
            'autre' => $dossier->mandat_zone_detail ?: 'zone à préciser',
        ];
        $html .= '<p class="corps-paragraphe">Le Mandataire intervient sur la zone géographique suivante : '
            . e($zoneLabels[$dossier->mandat_zone] ?? 'non définie') . '.</p>';

        $clienteleLabels = ['particuliers' => 'Particuliers', 'professionnels' => 'Professionnels', 'tns' => 'TNS', 'dirigeants' => 'Dirigeants', 'personnes_morales' => 'Personnes morales'];
        $clientele = collect($dossier->mandat_clientele ?? [])->map(fn ($c) => $clienteleLabels[$c] ?? $c)->implode(', ');
        $html .= '<p class="corps-paragraphe">Clientèle autorisée : ' . e($clientele ?: 'non définie') . '.</p>';

        $missionsLabels = [
            'prospection' => 'Prospection',
            'decouverte_client' => 'Découverte client',
            'recueil_besoins' => 'Recueil des besoins et exigences',
            'presentation_solutions' => 'Présentation des solutions',
            'proposition' => 'Proposition',
            'aide_souscription' => 'Aide à la souscription',
            'signature_contrat' => 'Signature du contrat',
            'suivi_relation' => 'Suivi de la relation client',
        ];
        $missions = collect($dossier->mandat_missions_autorisees ?? [])->map(fn ($m) => $missionsLabels[$m] ?? $m)->implode(', ');
        $html .= '<p class="corps-paragraphe">Missions autorisées : ' . e($missions ?: 'non définies') . '.</p>';

        $html .= '<h2 class="section-title">4. Limites de la mission</h2>';
        $html .= '<p class="corps-paragraphe">La mission du Mandataire est limitée à la présentation, la proposition '
            . 'ou l\'aide à la conclusion d\'une opération d\'assurance. Il n\'est pas habilité à intervenir dans la '
            . 'gestion, l\'estimation ou la liquidation des sinistres, ni à engager le Mandant sans validation préalable.</p>';

        if (! empty($dossier->mandat_missions_interdites)) {
            $html .= '<p class="corps-paragraphe">Missions expressément interdites ou limitées : '
                . nl2br(e($dossier->mandat_missions_interdites)) . '</p>';
        }

        $html .= '<h2 class="section-title">5. Encaissement de fonds</h2>';
        if ($dossier->encaissement_fonds) {
            $html .= '<p class="corps-paragraphe">Le Mandataire est autorisé à encaisser des fonds pour le compte '
                . 'du Mandant, dans le cadre de la garantie financière suivante : '
                . e($dossier->garantie_financiere_organisme ?: 'à préciser')
                . ($dossier->garantie_financiere_numero ? ', n° ' . e($dossier->garantie_financiere_numero) : '') . '.</p>';
        } else {
            $html .= '<p class="corps-paragraphe">Le Mandataire n\'est pas autorisé à encaisser, sous quelque forme '
                . 'que ce soit, des fonds, effets ou valeurs pour le compte des clients ou du Mandant.</p>';
        }

        $html .= '<h2 class="section-title">6. Rémunération</h2>';
        if ($dossier->mandat_remuneration_taux) {
            $frequenceLabels = ['unique' => 'à titre unique', 'recurrente' => 'de façon récurrente'];
            $html .= '<p class="corps-paragraphe">Le Mandataire perçoit une rémunération au taux de '
                . number_format((float) $dossier->mandat_remuneration_taux, 2, ',', ' ') . ' %'
                . ($dossier->mandat_remuneration_frequence ? ', versée ' . ($frequenceLabels[$dossier->mandat_remuneration_frequence] ?? $dossier->mandat_remuneration_frequence) : '')
                . '.</p>';
        } else {
            $html .= '<p class="corps-paragraphe">Les modalités de rémunération seront précisées par avenant.</p>';
        }

        $html .= '<h2 class="section-title">7. Obligations du Mandataire</h2>';
        $html .= '<p class="corps-paragraphe">Le Mandataire s\'engage à respecter les obligations relatives à son '
            . 'statut, son immatriculation, son honorabilité, sa capacité professionnelle, sa responsabilité civile '
            . 'professionnelle, sa garantie financière le cas échéant, sa formation continue ainsi que son devoir '
            . 'd\'information et de conseil.</p>';

        $html .= '<h2 class="section-title">8. LCB-FT, réclamations et RGPD</h2>';
        $html .= '<p class="corps-paragraphe">Le Mandataire déclare avoir pris connaissance et accepté la procédure '
            . 'LCB-FT, la procédure de traitement des réclamations et la politique de protection des données '
            . 'personnelles (RGPD) du Mandant, dont les versions acceptées sont historisées dans le dossier '
            . 'd\'enrôlement.</p>';

        $html .= '<h2 class="section-title">9. Durée et résiliation</h2>';
        $dureeLabels = ['determinee' => 'durée déterminée', 'indeterminee' => 'durée indéterminée'];
        $html .= '<p class="corps-paragraphe">La présente convention est conclue pour une '
            . e($dureeLabels[$dossier->mandat_duree] ?? 'durée à préciser')
            . '. Elle peut être résiliée par chacune des parties par lettre recommandée avec accusé de réception, '
            . 'moyennant un préavis de ' . ($dossier->mandat_preavis_resiliation_jours ?: 30) . ' jours.</p>';

        return $html;
    }

    public function variables(DossierEnrolement $dossier, ?CabinetProfile $cabinet): array
    {
        return [
            'mandant' => [
                'denomination' => $cabinet?->raison_sociale,
                'siren' => $cabinet?->siren,
                'rcs' => $cabinet?->numero_rcs,
                'orias' => $cabinet?->numero_orias,
            ],
            'mandataire' => [
                'nom' => $dossier->user->name,
                'societe' => $dossier->societe_denomination,
                'siren' => $dossier->societe_siren,
                'orias' => $dossier->orias_numero,
            ],
            'mandat' => [
                'zone' => $dossier->mandat_zone,
                'clientele' => $dossier->mandat_clientele,
                'duree' => $dossier->mandat_duree,
            ],
            'remuneration' => [
                'taux' => $dossier->mandat_remuneration_taux,
                'frequence' => $dossier->mandat_remuneration_frequence,
            ],
            'resiliation' => [
                'preavis' => $dossier->mandat_preavis_resiliation_jours,
            ],
        ];
    }
}
