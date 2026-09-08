<?php

namespace App\Support;

class Formatage
{
    /**
     * Particules qui restent en minuscule au milieu d'un nom (ex : "de Gaulle"),
     * jamais en tout début de chaîne.
     */
    private const PARTICULES = [
        'de', 'du', 'des', 'le', 'la', 'les',
        'von', 'van', 'af', 'di', 'da', 'dos', 'das', 'del', 'della', 'y',
    ];

    public static function nomPropre(?string $valeur): ?string
    {
        if ($valeur === null) {
            return null;
        }

        $valeur = trim(preg_replace('/\s+/', ' ', $valeur) ?? '');

        if ($valeur === '') {
            return $valeur;
        }

        $mots = explode(' ', $valeur);

        foreach ($mots as $index => $mot) {
            $mots[$index] = ($index > 0 && in_array(mb_strtolower($mot), self::PARTICULES, true))
                ? mb_strtolower($mot)
                : self::capitaliserMot($mot);
        }

        return implode(' ', $mots);
    }

    private static function capitaliserMot(string $mot): string
    {
        $segments = preg_split("/(['\x{2019}-])/u", $mot, -1, PREG_SPLIT_DELIM_CAPTURE);

        if ($segments === false) {
            return $mot;
        }

        foreach ($segments as $index => $segment) {
            if ($segment === '-' || $segment === "'" || $segment === "\u{2019}" || $segment === '') {
                continue;
            }

            $segments[$index] = mb_strtoupper(mb_substr($segment, 0, 1)).mb_strtolower(mb_substr($segment, 1));
        }

        return implode('', $segments);
    }
}
