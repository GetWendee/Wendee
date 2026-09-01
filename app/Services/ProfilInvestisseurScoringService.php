<?php

namespace App\Services;

/**
 * Orchestre le calcul du profil investisseur : résout les réponses brutes du
 * questionnaire (radio/checkbox) en scores numériques via config/profil_investisseur_champs.php,
 * puis évalue les 51 formules de config/profil_investisseur_formules.php dans leur ordre
 * d'origine, chaque résultat venant enrichir les données disponibles pour les formules suivantes.
 */
class ProfilInvestisseurScoringService
{
    public function __construct(private FormulaEvaluator $evaluator)
    {
    }

    /**
     * @param  array<string, mixed>  $reponses  Réponses brutes du questionnaire, indexées par nom de champ.
     *                                           Champ radio : chaîne (valeur de l'option sélectionnée).
     *                                           Champ checkbox : tableau de valeurs sélectionnées.
     *                                           Champ nombre/texte/date/select : valeur brute.
     * @return array<string, mixed> Les 51 résultats calculés, indexés par nom de champ calculé,
     *                              dans l'ordre d'origine du formulaire.
     */
    public function score(array $reponses): array
    {
        $champs = config('profil_investisseur_champs');
        $formules = config('profil_investisseur_formules');

        $data = [];

        // 1. Résout les champs à choix (radio/checkbox) en scores numériques.
        foreach ($champs as $nom => $config) {
            $data[$nom] = $this->resoudreScore($config, $reponses[$nom] ?? null);
        }

        // 2. Ajoute les champs bruts (nombre, texte, date, select...) tels quels.
        foreach ($reponses as $nom => $valeur) {
            if (! array_key_exists($nom, $data)) {
                $data[$nom] = $valeur;
            }
        }

        // 3. Évalue les formules dans l'ordre d'origine. Chaque résultat enrichit $data
        //    pour que les formules suivantes puissent le référencer (dépendances en chaîne).
        $resultats = [];
        foreach ($formules as $formule) {
            $valeur = $this->evaluator->evaluate($formule['formula'], $data);
            $data[$formule['name']] = $valeur;
            $resultats[$formule['name']] = $valeur;
        }

        return $resultats;
    }

    /**
     * @param  array{kind: string, options: array<string, float>}  $config
     */
    private function resoudreScore(array $config, mixed $valeur): float
    {
        if ($config['kind'] === 'radio') {
            if (! is_string($valeur)) {
                return 0.0;
            }

            return (float) ($config['options'][$valeur] ?? 0);
        }

        // checkbox : somme des scores de toutes les valeurs cochées
        $valeurs = is_array($valeur) ? $valeur : [];
        $total = 0.0;
        foreach ($valeurs as $v) {
            $total += (float) ($config['options'][$v] ?? 0);
        }

        return $total;
    }
}
