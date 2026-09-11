<?php

require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\FormulaEvaluator;
use App\Services\ProfilInvestisseurScoringService;

$service = new ProfilInvestisseurScoringService(new FormulaEvaluator);

$champs = config('profil_investisseur_champs');

// --- Cas 1 : profil "minimal" - première option de chaque champ radio, aucune case cochée ---
$reponsesMin = [];
foreach ($champs as $nom => $conf) {
    if ($conf['kind'] === 'radio') {
        $reponsesMin[$nom] = array_key_first($conf['options']);
    }
}

$resultatMin = $service->score($reponsesMin);
echo "=== Cas minimal (première option partout, aucune case cochée) ===\n";
foreach (['profil_risque_final', 'profil_risque_final_echelle', 'score_connaissance_global', 'score_experience_global', 'alerte_client_fragile_echelle'] as $k) {
    echo "  $k => ".json_encode($resultatMin[$k], JSON_UNESCAPED_UNICODE)."\n";
}

// --- Cas 2 : profil "maximal" - dernière option de chaque radio, toutes les cases cochées ---
$reponsesMax = [];
foreach ($champs as $nom => $conf) {
    if ($conf['kind'] === 'radio') {
        $keys = array_keys($conf['options']);
        $reponsesMax[$nom] = end($keys);
    } else {
        $reponsesMax[$nom] = array_keys($conf['options']);
    }
}

$resultatMax = $service->score($reponsesMax);
echo "\n=== Cas maximal (dernière option partout, toutes cases cochées) ===\n";
foreach (['profil_risque_final', 'profil_risque_final_echelle', 'score_connaissance_global', 'score_experience_global', 'alerte_client_fragile_echelle'] as $k) {
    echo "  $k => ".json_encode($resultatMax[$k], JSON_UNESCAPED_UNICODE)."\n";
}

// --- Vérifications de cohérence ---
$erreurs = 0;

echo "\n=== Vérifications ===\n";

if (count($resultatMin) !== 51) {
    echo "FAIL nombre de résultats (min) : ".count($resultatMin)." au lieu de 51\n";
    $erreurs++;
} else {
    echo "OK   51 résultats produits (cas min)\n";
}
if (count($resultatMax) !== 51) {
    echo "FAIL nombre de résultats (max) : ".count($resultatMax)." au lieu de 51\n";
    $erreurs++;
} else {
    echo "OK   51 résultats produits (cas max)\n";
}

foreach (['profil_risque_final', 'score_connaissance_global', 'score_experience_global', 'score_capacite_subir_pertes'] as $k) {
    foreach (['min' => $resultatMin, 'max' => $resultatMax] as $label => $res) {
        $v = $res[$k];
        if (! is_numeric($v) || $v < 0 || $v > 10.01) {
            echo "FAIL $k ($label) hors bornes [0,10] : ".json_encode($v)."\n";
            $erreurs++;
        } else {
            echo "OK   $k ($label) = $v (dans [0,10])\n";
        }
    }
}

if ($resultatMax['profil_risque_final'] <= $resultatMin['profil_risque_final']) {
    echo "FAIL le cas maximal ne donne pas un profil_risque_final plus élevé que le cas minimal\n";
    $erreurs++;
} else {
    echo "OK   profil_risque_final cas max (".$resultatMax['profil_risque_final'].") > cas min (".$resultatMin['profil_risque_final'].")\n";
}

echo "\n".($erreurs === 0 ? "TOUT OK, 0 échec(s)" : "$erreurs échec(s)")."\n";
exit($erreurs > 0 ? 1 : 0);
