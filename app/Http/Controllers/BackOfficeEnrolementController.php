<?php

namespace App\Http\Controllers;

use App\Models\CabinetProfile;
use App\Models\DossierEnrolement;
use App\Services\ConventionMandatService;
use App\Services\DossierEnrolementComplianceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Tableau de contrôle du courtier sur les dossiers d'enrôlement de ses
 * conseillers mandataires : décision (valider / refuser / demander une
 * pièce) et génération de la convention de mandat à la validation.
 * Voir claude/enrolement-conseillers-mandataires.md section 8.
 */
class BackOfficeEnrolementController extends Controller
{
    public function index(Request $request): View
    {
        $viewer = $request->user();

        abort_unless($viewer->effectiveRole() === 'courtier', 403);

        $dossiers = DossierEnrolement::query()
            ->whereHas('user', fn ($q) => $q->where('parent_id', $viewer->id))
            ->with('user')
            ->orderByRaw("FIELD(statut, 'pending_validation', 'onboarding', 'invited', 'contract_pending', 'active', 'rejected')")
            ->orderByDesc('updated_at')
            ->get();

        return view('tenant.dossiers-enrolement.back-office-index', [
            'dossiers' => $dossiers,
        ]);
    }

    public function show(Request $request, DossierEnrolement $dossier): View
    {
        $viewer = $request->user();

        abort_unless($viewer->effectiveRole() === 'courtier', 403);
        abort_unless($dossier->user->parent_id === $viewer->id, 403);

        $dossier->load(['user', 'justificatifs' => function ($query) {
            $query->orderByDesc('version');
        }]);

        return view('tenant.dossiers-enrolement.back-office-show', [
            'dossier' => $dossier,
        ]);
    }

    public function demanderPiece(Request $request, DossierEnrolement $dossier): RedirectResponse
    {
        $viewer = $request->user();

        abort_unless($viewer->effectiveRole() === 'courtier', 403);
        abort_unless($dossier->user->parent_id === $viewer->id, 403);

        $validated = $request->validate([
            'notes_back_office' => ['required', 'string', 'max:2000'],
        ]);

        $dossier->update([
            'statut' => 'onboarding',
            'notes_back_office' => $validated['notes_back_office'],
        ]);

        return redirect()->route('tenant.back-office-enrolement.show', $dossier)->with('status', 'Demande de pièce complémentaire envoyée.');
    }

    public function refuser(Request $request, DossierEnrolement $dossier): RedirectResponse
    {
        $viewer = $request->user();

        abort_unless($viewer->effectiveRole() === 'courtier', 403);
        abort_unless($dossier->user->parent_id === $viewer->id, 403);

        $validated = $request->validate([
            'refuse_motif' => ['required', 'string', 'max:2000'],
        ]);

        $dossier->update([
            'statut' => 'rejected',
            'decision_cabinet' => 'refuse',
            'refuse_motif' => $validated['refuse_motif'],
            'valide_par_id' => $viewer->id,
            'valide_le' => now(),
        ]);

        return redirect()->route('tenant.back-office-enrolement.index')->with('status', 'Dossier refusé.');
    }

    public function valider(
        Request $request,
        DossierEnrolement $dossier,
        DossierEnrolementComplianceService $compliance,
        ConventionMandatService $convention
    ): RedirectResponse {
        $viewer = $request->user();

        abort_unless($viewer->effectiveRole() === 'courtier', 403);
        abort_unless($dossier->user->parent_id === $viewer->id, 403);

        $resultat = $compliance->evaluer($dossier);

        abort_if($resultat['statut_global'] === 'non_eligible', 422, 'Ce dossier comporte un point bloquant et ne peut pas être validé.');

        $cabinet = CabinetProfile::query()->first();

        $html = $convention->genererHtml($dossier, $cabinet);
        $variables = $convention->variables($dossier, $cabinet);

        $dossier->update([
            'checks_conformite' => $resultat['checks'],
            'conformite_reglementaire' => $resultat['conformite_reglementaire'],
            'decision_cabinet' => 'valide',
            'valide_par_id' => $viewer->id,
            'valide_le' => now(),
            'statut' => 'contract_pending',
            'convention_html' => $html,
            'convention_variables' => $variables,
            'convention_genere_le' => now(),
            'convention_statut' => 'generee',
        ]);

        return redirect()->route('tenant.back-office-enrolement.show', $dossier)->with('status', 'Dossier validé, convention générée.');
    }

    public function marquerSigne(Request $request, DossierEnrolement $dossier): RedirectResponse
    {
        $viewer = $request->user();

        abort_unless($viewer->effectiveRole() === 'courtier', 403);
        abort_unless($dossier->user->parent_id === $viewer->id, 403);
        abort_unless($dossier->convention_statut === 'generee', 404);

        $dossier->update([
            'convention_statut' => 'signee',
            'convention_signe_le' => now(),
            'statut' => 'active',
        ]);

        $dossier->user()->update(['activation_pending' => false]);

        return redirect()->route('tenant.back-office-enrolement.show', $dossier)->with('status', 'Convention signée, conseiller activé.');
    }

    public function telechargerConventionPdf(Request $request, DossierEnrolement $dossier): \Symfony\Component\HttpFoundation\Response
    {
        $viewer = $request->user();

        abort_unless($viewer->effectiveRole() === 'courtier' || $viewer->id === $dossier->user_id, 403);
        abort_unless($viewer->effectiveRole() === 'courtier' ? $dossier->user->parent_id === $viewer->id : true, 403);
        abort_unless($dossier->convention_html, 404);

        $cabinet = CabinetProfile::query()->first();

        $nomMandataire = $dossier->mode_exercice === 'societe'
            ? ($dossier->societe_denomination ?: $dossier->user->name)
            : $dossier->user->name;

        $data = [
            'dossier' => $dossier,
            'cabinet' => $cabinet,
            'nomMandataire' => $nomMandataire,
            'lieuSignature' => $cabinet?->ville,
            'dateGeneration' => now()->translatedFormat('d F Y'),
            'corpsHtml' => $dossier->convention_html,
            'fontRegular' => base_path('resources/fonts/Montserrat-Regular.ttf'),
            'fontMedium' => base_path('resources/fonts/Montserrat-Medium.ttf'),
            'fontSemiBold' => base_path('resources/fonts/Montserrat-SemiBold.ttf'),
            'fontBold' => base_path('resources/fonts/Montserrat-Bold.ttf'),
            'logoPath' => $cabinet?->logo ? storage_path('app/public/' . $cabinet->logo) : null,
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('tenant.dossiers-enrolement.pdf.convention-mandat', $data);

        return $pdf->download('Convention de mandat - ' . $nomMandataire . '.pdf');
    }
}
