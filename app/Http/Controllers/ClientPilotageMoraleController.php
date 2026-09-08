<?php

namespace App\Http\Controllers;

use App\Models\CabinetProfile;
use App\Models\Client;
use App\Models\ClientAnalysis;
use App\Services\AI\PlanActionAnalysisServiceMorale;
use App\Services\AI\RecommandationAnalysisService;
use App\Services\AI\RecommandationAnalysisServiceMorale;
use App\Services\AI\SuggestionAnalysisServiceMorale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * Circuit Aide à la décision / Suggestion / Recommandation (lettre de
 * mission) / Plan d'action pour une personne morale (société).
 * Contrôleur entièrement séparé de ClientController (personne physique),
 * voir claude/kyc-personne-morale.md.
 */
class ClientPilotageMoraleController extends Controller
{
    private const TYPES_ANALYSES = [
        'kyc_morale',
        'patrimoine_morale',
        'profil_investisseur_morale',
    ];

    public function edit(Client $client): View|RedirectResponse
    {
        if (! $client->estMorale()) {
            return redirect()->route('tenant.clients.aide-decision', $client);
        }

        $client->loadMissing(['kycMorale', 'patrimoineElements', 'profilInvestisseurMorale', 'conseiller', 'apporteur']);

        $analysesDossier = $client->analyses()
            ->where('status', 'completed')
            ->whereIn('type', self::TYPES_ANALYSES)
            ->latest('created_at')
            ->get()
            ->groupBy('type')
            ->map(fn ($analyses) => $analyses->first());

        $suggestion = $client->analyses()
            ->where('type', 'suggestion_morale')
            ->where('status', 'completed')
            ->latest('completed_at')
            ->first();

        return view('tenant.clients.pilotage-morale', [
            'client' => $client,
            'analysesDossier' => $analysesDossier,
            'suggestion' => $suggestion,
        ]);
    }

    public function genererSuggestion(Client $client, SuggestionAnalysisServiceMorale $suggestionAnalysis): RedirectResponse
    {
        abort_unless($client->estMorale(), 404);

        $analyses = $suggestionAnalysis->getRequiredAnalyses($client);

        foreach (self::TYPES_ANALYSES as $type) {
            if (! $analyses->has($type)) {
                return redirect()
                    ->route('tenant.clients.pilotage-morale', $client)
                    ->with('error', 'La suggestion nécessite les trois analyses du dossier société, réalisées depuis moins d’un an.');
            }
        }

        try {
            $suggestionAnalysis->analyze($client);

            return redirect()
                ->route('tenant.clients.pilotage-morale', $client)
                ->with('status', 'Suggestion de prestations générée.');
        } catch (\Throwable $e) {
            Log::error('Erreur génération suggestion prestations société', [
                'client_id' => $client->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->route('tenant.clients.pilotage-morale', $client)
                ->with('error', 'La suggestion n’a pas pu être générée. Les analyses du dossier restent inchangées.');
        }
    }

    public function recommandation(Client $client): View|RedirectResponse
    {
        abort_unless($client->estMorale(), 404);

        $suggestion = $client->analyses()
            ->where('type', 'suggestion_morale')
            ->where('status', 'completed')
            ->latest('completed_at')
            ->first();

        if (! $suggestion) {
            return redirect()
                ->route('tenant.clients.pilotage-morale', $client)
                ->with('error', 'La recommandation patrimoniale nécessite une suggestion de prestations générée au préalable.');
        }

        $cabinet = CabinetProfile::query()->first();

        $derniereRecommandation = $client->analyses()
            ->where('type', 'recommandation_morale')
            ->latest('created_at')
            ->first();

        return view('tenant.clients.recommandation-morale', [
            'client' => $client,
            'suggestion' => $suggestion,
            'cabinet' => $cabinet,
            'recommandation' => $derniereRecommandation,
        ]);
    }

    public function genererRecommandation(Request $request, Client $client, RecommandationAnalysisServiceMorale $recommandationAnalysis): RedirectResponse
    {
        abort_unless($client->estMorale(), 404);

        $validated = $request->validate([
            'contexte' => ['nullable', 'string', 'max:5000'],
            'missions' => ['nullable', 'array'],
            'missions.*' => ['string', 'in:courtage_banque,courtage_assurance,conseil_investissement_financier'],
            'montants' => ['nullable', 'array'],
            'montants.*' => ['nullable', 'numeric'],
            'taux' => ['nullable', 'array'],
            'taux.*' => ['nullable', 'numeric'],
        ]);

        $missionLabels = [
            'courtage_banque' => 'Mandat de courtage banque',
            'courtage_assurance' => 'Mandat de courtage assurance',
            'conseil_investissement_financier' => 'Conseils en investissements financiers (CIF)',
        ];
        $missionIndex = [
            'courtage_banque' => 0,
            'courtage_assurance' => 1,
            'conseil_investissement_financier' => 2,
        ];

        $cabinet = CabinetProfile::query()->first();
        $prestations = $cabinet->prestations ?? [];

        $missionsRetenues = [];
        $total = 0.0;
        foreach ($validated['missions'] ?? [] as $key) {
            $index = $missionIndex[$key] ?? null;
            $mode = $index !== null ? ($prestations[$index]['mode'] ?? null) : null;
            $montant = (float) ($validated['montants'][$key] ?? 0);
            $taux = (float) ($validated['taux'][$key] ?? 0);
            $montantFinal = $mode === 'pourcentage'
                ? round($montant * $taux / 100, 2)
                : round($montant, 2);
            $missionsRetenues[] = [
                'key' => $key,
                'label' => $missionLabels[$key] ?? $key,
                'mode' => $mode,
                'montant' => $montantFinal,
            ];
            $total += $montantFinal;
        }

        try {
            $recommandationAnalysis->analyze($client, [
                'contexte' => $validated['contexte'] ?? '',
                'missions' => $missionsRetenues,
                'total' => round($total, 2),
            ]);

            return redirect()
                ->route('tenant.clients.recommandation-morale', $client)
                ->with('status', 'Recommandation patrimoniale générée.');
        } catch (\Throwable $e) {
            Log::error('Erreur génération recommandation patrimoniale société', [
                'client_id' => $client->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->route('tenant.clients.recommandation-morale', $client)
                ->with('error', "La recommandation n'a pas pu être générée.");
        }
    }

    public function modifierRecommandationContenu(Request $request, Client $client, ClientAnalysis $analysis): RedirectResponse
    {
        abort_unless($client->estMorale(), 404);
        abort_unless($analysis->client_id === $client->id, 404);
        abort_unless($analysis->type === 'recommandation_morale', 404);

        $validated = $request->validate([
            'contenu_html' => ['required', 'string'],
        ]);

        $contenuNettoye = strip_tags($validated['contenu_html'], '<h2><span><p><strong><br><ul><ol><li><em>');

        $resultJson = $analysis->result_json ?? [];
        $resultJson['lettre_mission_html'] = $contenuNettoye;
        $analysis->update(['result_json' => $resultJson]);

        return redirect()
            ->route('tenant.clients.recommandation-morale', $client)
            ->with('status', 'Modifications enregistrées.');
    }

    public function telechargerRecommandationPdf(Request $request, Client $client): \Symfony\Component\HttpFoundation\Response
    {
        abort_unless($client->estMorale(), 404);

        $recommandation = $client->analyses()
            ->where('type', 'recommandation_morale')
            ->where('status', 'completed')
            ->latest('completed_at')
            ->first();

        if (! $recommandation) {
            return redirect()
                ->route('tenant.clients.recommandation-morale', $client)
                ->with('error', 'Aucune recommandation générée à exporter.');
        }

        $cabinet = CabinetProfile::query()->first();
        $conseiller = $client->conseiller;

        $nomClient = $client->nomAffichage();

        $corpsHtml = $recommandation->result_json['lettre_mission_html']
            ?? RecommandationAnalysisService::convertirMarkdownEnHtml(
                $recommandation->result_json['lettre_mission'] ?? $recommandation->raw_response ?? ''
            );

        $data = [
            'client' => $client,
            'cabinet' => $cabinet,
            'recommandation' => $recommandation,
            'nomClient' => $nomClient,
            'nomConseiller' => $conseiller?->name ?? auth()->user()->name,
            'telConseiller' => $conseiller?->telephone_mobile,
            'mailConseiller' => $conseiller?->email,
            'lieuSignature' => $request->query('lieu') ?: $cabinet?->ville,
            'dateGeneration' => now()->translatedFormat('d F Y'),
            'corpsHtml' => $corpsHtml,
            'fontRegular' => base_path('resources/fonts/Montserrat-Regular.ttf'),
            'fontMedium' => base_path('resources/fonts/Montserrat-Medium.ttf'),
            'fontSemiBold' => base_path('resources/fonts/Montserrat-SemiBold.ttf'),
            'fontBold' => base_path('resources/fonts/Montserrat-Bold.ttf'),
            'logoPath' => $cabinet?->logo ? storage_path('app/public/' . $cabinet->logo) : null,
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('tenant.clients.pdf.recommandation-patrimoniale', $data);

        $filename = 'Recommandation patrimoniale - ' . $client->nomAffichage() . '.pdf';

        return $pdf->download($filename);
    }

    public function planAction(Client $client): View|RedirectResponse
    {
        abort_unless($client->estMorale(), 404);

        $recommandation = $client->analyses()
            ->where('type', 'recommandation_morale')
            ->where('status', 'completed')
            ->latest('completed_at')
            ->first();

        if (! $recommandation) {
            return redirect()
                ->route('tenant.clients.recommandation-morale', $client)
                ->with('error', "Le plan d'action nécessite une recommandation patrimoniale générée au préalable.");
        }

        $cabinet = CabinetProfile::query()->first();

        $dernierPlanAction = $client->analyses()
            ->where('type', 'plan_action_morale')
            ->latest('created_at')
            ->first();

        return view('tenant.clients.plan-action-morale', [
            'client' => $client,
            'cabinet' => $cabinet,
            'planAction' => $dernierPlanAction,
        ]);
    }

    public function genererPlanAction(Request $request, Client $client, PlanActionAnalysisServiceMorale $planActionAnalysis): RedirectResponse
    {
        abort_unless($client->estMorale(), 404);

        $validated = $request->validate([
            'contexte' => ['nullable', 'string', 'max:5000'],
        ]);

        try {
            $planActionAnalysis->analyze($client, [
                'contexte' => $validated['contexte'] ?? '',
            ]);

            return redirect()
                ->route('tenant.clients.plan-action-morale', $client)
                ->with('status', "Plan d'action généré.");
        } catch (\Throwable $e) {
            Log::error('Erreur génération plan d\'action société', [
                'client_id' => $client->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->route('tenant.clients.plan-action-morale', $client)
                ->with('error', "Le plan d'action n'a pas pu être généré.");
        }
    }

    public function modifierPlanActionContenu(Request $request, Client $client, ClientAnalysis $analysis): RedirectResponse
    {
        abort_unless($client->estMorale(), 404);
        abort_unless($analysis->client_id === $client->id, 404);
        abort_unless($analysis->type === 'plan_action_morale', 404);

        $validated = $request->validate([
            'contenu_html' => ['required', 'string'],
        ]);

        $contenuNettoye = strip_tags($validated['contenu_html'], '<h2><span><p><strong><br><ul><ol><li><em>');

        $resultJson = $analysis->result_json ?? [];
        $resultJson['plan_action_html'] = $contenuNettoye;
        $analysis->update(['result_json' => $resultJson]);

        return redirect()
            ->route('tenant.clients.plan-action-morale', $client)
            ->with('status', 'Modifications enregistrées.');
    }

    public function telechargerPlanActionPdf(Request $request, Client $client): \Symfony\Component\HttpFoundation\Response
    {
        abort_unless($client->estMorale(), 404);

        $planAction = $client->analyses()
            ->where('type', 'plan_action_morale')
            ->where('status', 'completed')
            ->latest('completed_at')
            ->first();

        if (! $planAction) {
            return redirect()
                ->route('tenant.clients.plan-action-morale', $client)
                ->with('error', "Aucun plan d'action généré à exporter.");
        }

        $cabinet = CabinetProfile::query()->first();
        $conseiller = $client->conseiller;

        $nomClient = $client->nomAffichage();

        $corpsHtml = $planAction->result_json['plan_action_html']
            ?? RecommandationAnalysisService::convertirMarkdownEnHtml(
                $planAction->result_json['plan_action'] ?? $planAction->raw_response ?? ''
            );

        $data = [
            'client' => $client,
            'cabinet' => $cabinet,
            'planAction' => $planAction,
            'nomClient' => $nomClient,
            'nomConseiller' => $conseiller?->name ?? auth()->user()->name,
            'telConseiller' => $conseiller?->telephone_mobile,
            'mailConseiller' => $conseiller?->email,
            'lieuSignature' => $request->query('lieu') ?: $cabinet?->ville,
            'dateGeneration' => now()->translatedFormat('d F Y'),
            'corpsHtml' => $corpsHtml,
            'fontRegular' => base_path('resources/fonts/Montserrat-Regular.ttf'),
            'fontMedium' => base_path('resources/fonts/Montserrat-Medium.ttf'),
            'fontSemiBold' => base_path('resources/fonts/Montserrat-SemiBold.ttf'),
            'fontBold' => base_path('resources/fonts/Montserrat-Bold.ttf'),
            'logoPath' => $cabinet?->logo ? storage_path('app/public/' . $cabinet->logo) : null,
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('tenant.clients.pdf.plan-action', $data);

        $filename = "Plan d'action - " . $client->nomAffichage() . '.pdf';

        return $pdf->download($filename);
    }
}
