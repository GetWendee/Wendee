<?php

require __DIR__.'/vendor/autoload.php';

use App\Services\FormulaEvaluator;

$evaluator = new FormulaEvaluator;

$failures = 0;
$total = 0;

function check(FormulaEvaluator $ev, string $label, string $formula, array $data, mixed $expected): void
{
    global $failures, $total;
    $total++;
    $result = $ev->evaluate($formula, $data);

    $ok = is_float($expected) || is_int($expected)
        ? abs((float) $result - (float) $expected) < 0.001
        : $result === $expected;

    if ($ok) {
        echo "OK   {$label} => ".json_encode($result)."\n";
    } else {
        $failures++;
        echo "FAIL {$label} => obtenu ".json_encode($result).", attendu ".json_encode($expected)."\n";
    }
}

// --- Formules réelles du fichier fields_full.json (texte exact, non simplifié) ---

$formula_niveau_experience_simple = '(  (%reponse_2_fonds_euros_profil_investisseur% || 0)  + (%reponse_2_produits_monetaires_profil_investisseur% || 0)  + (%reponse_2_produits_obligataires_profil_investisseur% || 0)  + (%reponse_2_produits_actions_profil_investisseur% || 0)) / 40 * 10';

$formula_profil_risque_final = '(  (%score_connaissance_global% || 0)  + (%score_experience_global% || 0)  + (%score_capacite_subir_pertes% || 0)) / 30 * 10';

$formula_alerte_client_fragile = '(  (%score_capacite_subir_pertes% <= 2)  || (%score_capacite_financiere% <= 2)  || ((%score_contrainte_financiere% >= 8) && (%score_capacite_financiere% <= 4))) ? 2: (  (%score_capacite_subir_pertes% <= 4)  || ((%score_contrainte_financiere% >= 6) && (%score_capacite_financiere% <= 4))) ? 1: 0';

$formula_score_capacite_subir_pertes = '(  (    (%score_capacite_financiere% || 0)    - (%score_contrainte_financiere% || 0)    + (%score_tolerance_risque% || 0)    + (%preference_1_profil_investisseur% || 0)  ) < 0) ? 0:(  (%score_capacite_financiere% || 0)  - (%score_contrainte_financiere% || 0)  + (%score_tolerance_risque% || 0)  + (%preference_1_profil_investisseur% || 0)) / 30 * 10';

$formula_profil_risque_final_echelle = '(%profil_risque_final% <= 2) ? "Conservateur": (%profil_risque_final% <= 4) ? "Prudent": (%profil_risque_final% <= 6) ? "Équilibré": (%profil_risque_final% <= 8) ? "Équilibré dynamique": "Dynamique"';

echo "== niveau_experience_simple ==\n";
check($evaluator, 'somme 40/40', $formula_niveau_experience_simple, [
    'reponse_2_fonds_euros_profil_investisseur' => 10,
    'reponse_2_produits_monetaires_profil_investisseur' => 10,
    'reponse_2_produits_obligataires_profil_investisseur' => 10,
    'reponse_2_produits_actions_profil_investisseur' => 10,
], 10.0);
check($evaluator, 'aucune réponse', $formula_niveau_experience_simple, [], 0.0);

echo "\n== profil_risque_final ==\n";
check($evaluator, 'moyenne simple', $formula_profil_risque_final, [
    'score_connaissance_global' => 6,
    'score_experience_global' => 6,
    'score_capacite_subir_pertes' => 6,
], 6.0);

echo "\n== alerte_client_fragile ==\n";
check($evaluator, 'capacité à subir des pertes <= 2 => alerte 2', $formula_alerte_client_fragile, [
    'score_capacite_subir_pertes' => 1,
    'score_capacite_financiere' => 10,
    'score_contrainte_financiere' => 0,
], 2);
check($evaluator, 'aucun critère déclenché => 0', $formula_alerte_client_fragile, [
    'score_capacite_subir_pertes' => 5,
    'score_capacite_financiere' => 5,
    'score_contrainte_financiere' => 5,
], 0);
check($evaluator, 'contrainte forte + capacité faible (croisé) => 2', $formula_alerte_client_fragile, [
    'score_capacite_subir_pertes' => 10,
    'score_capacite_financiere' => 3,
    'score_contrainte_financiere' => 9,
], 2);
check($evaluator, 'capacité à subir des pertes <= 4 (niveau 2) => 1', $formula_alerte_client_fragile, [
    'score_capacite_subir_pertes' => 3,
    'score_capacite_financiere' => 10,
    'score_contrainte_financiere' => 0,
], 1);

echo "\n== score_capacite_subir_pertes ==\n";
check($evaluator, 'résultat positif', $formula_score_capacite_subir_pertes, [
    'score_capacite_financiere' => 10,
    'score_contrainte_financiere' => 5,
    'score_tolerance_risque' => 5,
    'preference_1_profil_investisseur' => 5,
], 5.0);
check($evaluator, 'résultat négatif => clampé à 0', $formula_score_capacite_subir_pertes, [
    'score_capacite_financiere' => 0,
    'score_contrainte_financiere' => 20,
    'score_tolerance_risque' => 0,
    'preference_1_profil_investisseur' => 0,
], 0.0);

echo "\n== profil_risque_final_echelle ==\n";
check($evaluator, 'profil 1 => Conservateur', $formula_profil_risque_final_echelle, ['profil_risque_final' => 1], 'Conservateur');
check($evaluator, 'profil 3 => Prudent', $formula_profil_risque_final_echelle, ['profil_risque_final' => 3], 'Prudent');
check($evaluator, 'profil 5 => Équilibré', $formula_profil_risque_final_echelle, ['profil_risque_final' => 5], 'Équilibré');
check($evaluator, 'profil 7 => Équilibré dynamique', $formula_profil_risque_final_echelle, ['profil_risque_final' => 7], 'Équilibré dynamique');
check($evaluator, 'profil 9 => Dynamique', $formula_profil_risque_final_echelle, ['profil_risque_final' => 9], 'Dynamique');

echo "\n== idiome '(%x% || 0)' ==\n";
check($evaluator, 'champ manquant => défaut 0', '(%champ_absent% || 0) + 5', [], 5.0);
check($evaluator, 'champ à 7', '(%x% || 0) + 5', ['x' => 7], 12.0);

echo "\n== égalité stricte sur nombre ==\n";
check($evaluator, '2 == 2 => yes', '(%x% == 2) ? "yes" : "no"', ['x' => 2], 'yes');
check($evaluator, '3 == 2 => no', '(%x% == 2) ? "yes" : "no"', ['x' => 3], 'no');

echo "\n=====================================\n";
echo "{$total} tests, ".($total - $failures)." OK, {$failures} échec(s)\n";

exit($failures > 0 ? 1 : 0);
