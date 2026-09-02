<?php

namespace App\Http\Controllers;

use App\Services\Sirene\SireneService;
use Illuminate\Http\JsonResponse;
use RuntimeException;

class SireneLookupController extends Controller
{
    /**
     * Recherche asynchrone d'un établissement par SIRET, pour préremplir
     * le nom de la société employeur dans les formulaires KYC.
     */
    public function rechercher(string $siret, SireneService $sirene): JsonResponse
    {
        try {
            $etablissement = $sirene->findBySiret($siret);
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        if (! $etablissement) {
            return response()->json(['message' => 'Aucun établissement trouvé pour ce SIRET.'], 404);
        }

        return response()->json([
            'siret' => $etablissement['siret'] ?? null,
            'raison_sociale' => $etablissement['raison_sociale'] ?? null,
        ]);
    }
}
