<?php

declare(strict_types=1);

use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientDocumentController;
use App\Http\Controllers\ClientKycController;
use App\Http\Controllers\ClientKycMoraleController;
use App\Http\Controllers\SireneLookupController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PatrimoineController;
use App\Http\Controllers\ProfilInvestisseurController;
use App\Http\Controllers\ProfilInvestisseurMoraleController;
use App\Http\Controllers\ClientPilotageMoraleController;
use App\Http\Controllers\DossierEnrolementController;
use App\Http\Controllers\BackOfficeEnrolementController;
use App\Http\Controllers\PortefeuilleCabinetController;
use App\Http\Controllers\ComptesCloturesController;
use App\Http\Controllers\AbonnementController;
use App\Http\Controllers\PerformanceController;
use App\Http\Controllers\RevenuController;
use App\Http\Controllers\CommissionController;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\CabinetProfileController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TenantProfileController;
use Stancl\Tenancy\Controllers\TenantAssetsController;
use App\Http\Controllers\UserAccountController;
use App\Http\Controllers\CalendarConnectionController;
use App\Http\Controllers\RendezVousController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->name('tenant.')->group(function () {

    Route::get('/', function () {
        if (auth()->check()) {
            return redirect()->route('tenant.dashboard');
        }

        return redirect()->route('tenant.login');
    });
    Route::get('/tenancy/assets/{path}', [TenantAssetsController::class, 'asset'])
        ->where('path', '.*')
        ->name('stancl.tenancy.asset');

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware(['auth', 'verified'])
        ->name('dashboard');
Route::get('/cabinet/', [CabinetProfileController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('cabinet');

Route::put('/cabinet/', [CabinetProfileController::class, 'update'])
    ->middleware(['auth', 'verified'])
    ->name('cabinet.update');

Route::get('/notifications/{notification}/lire', [NotificationController::class, 'lire'])
    ->middleware(['auth', 'verified'])
    ->name('notifications.lire');

Route::post('/notifications/tout-marquer-lu', [NotificationController::class, 'toutMarquerLu'])
    ->middleware(['auth', 'verified'])
    ->name('notifications.tout-marquer-lu');

Route::get('/profil/changement-email/{demande}/valider', [TenantProfileController::class, 'validerChangementEmail'])
    ->middleware('signed')
    ->name('profil.email.valider');
Route::get('/profil/changement-email/{demande}/refuser', [TenantProfileController::class, 'refuserChangementEmail'])
    ->middleware('signed')
    ->name('profil.email.refuser');

Route::middleware(['auth', 'verified', 'client.access'])->group(function () {

    Route::post('/dev/view-as-role', function (\Illuminate\Http\Request $request) {

        $validated = $request->validate([
            'role' => ['required', 'in:courtier,conseiller,apporteur,client'],
        ]);

        session([
            'dev_view_role' => $validated['role'],
        ]);

        return back();

    })->middleware(['auth', 'verified'])
      ->name('dev.view-role');

        Route::get('/portefeuille', [PortefeuilleCabinetController::class, 'index'])
            ->name('portefeuille.index');
        Route::get('/comptes-clotures', [ComptesCloturesController::class, 'index'])->name('comptes-clotures.index');
        Route::post('/comptes-clotures/{client}/reactiver', [ComptesCloturesController::class, 'reactiver'])->name('comptes-clotures.reactiver');
        Route::post('/comptes-clotures/{client}/demander-reactivation', [ComptesCloturesController::class, 'demanderReactivation'])->name('comptes-clotures.demander-reactivation');
        Route::get('/abonnement', [AbonnementController::class, 'index'])->name('abonnement.index');
        Route::put('/abonnement', [AbonnementController::class, 'update'])->name('abonnement.update');
        Route::get('/performances', [PerformanceController::class, 'index'])->name('performances.index');
        Route::get('/revenus', [RevenuController::class, 'index'])->name('revenus.index');
        Route::get('/utilisateurs/creer', [UserAccountController::class, 'create'])->name('users.create');
        Route::post('/utilisateurs', [UserAccountController::class, 'store'])->name('users.store');
        Route::get('/utilisateurs/{user}', [UserAccountController::class, 'show'])->name('users.show');
        Route::post('/utilisateurs/{user}/voit-tous-les-clients', [UserAccountController::class, 'toggleVoitTousLesClients'])->name('users.toggle-voit-tous-les-clients');
        Route::post('/utilisateurs/{user}/valider-rib', [UserAccountController::class, 'validerRib'])->name('users.valider-rib');
        Route::get('/mon-dossier-enrolement', [DossierEnrolementController::class, 'edit'])->name('dossier-enrolement.edit');
        Route::put('/mon-dossier-enrolement', [DossierEnrolementController::class, 'update'])->name('dossier-enrolement.update');
        Route::post('/mon-dossier-enrolement/justificatifs', [DossierEnrolementController::class, 'uploadJustificatif'])->name('dossier-enrolement.justificatifs.store');
        Route::post('/mon-dossier-enrolement/soumettre', [DossierEnrolementController::class, 'submit'])->name('dossier-enrolement.submit');
        Route::get('/dossiers-enrolement', [BackOfficeEnrolementController::class, 'index'])->name('back-office-enrolement.index');
        Route::get('/dossiers-enrolement/{dossier}', [BackOfficeEnrolementController::class, 'show'])->name('back-office-enrolement.show');
        Route::post('/dossiers-enrolement/{dossier}/demander-piece', [BackOfficeEnrolementController::class, 'demanderPiece'])->name('back-office-enrolement.demander-piece');
        Route::post('/dossiers-enrolement/{dossier}/refuser', [BackOfficeEnrolementController::class, 'refuser'])->name('back-office-enrolement.refuser');
        Route::post('/dossiers-enrolement/{dossier}/valider', [BackOfficeEnrolementController::class, 'valider'])->name('back-office-enrolement.valider');
        Route::post('/dossiers-enrolement/{dossier}/marquer-signe', [BackOfficeEnrolementController::class, 'marquerSigne'])->name('back-office-enrolement.marquer-signe');
        Route::get('/dossiers-enrolement/{dossier}/convention.pdf', [BackOfficeEnrolementController::class, 'telechargerConventionPdf'])->name('back-office-enrolement.convention-pdf');
        Route::get('/dossiers-enrolement/justificatifs/{justificatif}', [BackOfficeEnrolementController::class, 'showJustificatif'])->name('back-office-enrolement.justificatifs.show');
        Route::delete('/dossiers-enrolement/justificatifs/{justificatif}', [BackOfficeEnrolementController::class, 'destroyJustificatif'])->name('back-office-enrolement.justificatifs.destroy');
        Route::get('/profil', [TenantProfileController::class, 'edit'])->name('profil.edit');
        Route::put('/profil', [TenantProfileController::class, 'update'])->name('profil.update');
        Route::get('/mon-rib', [UserAccountController::class, 'editRib'])->name('profil.rib.edit');
        Route::put('/mon-rib', [UserAccountController::class, 'updateRib'])->name('profil.rib.update');
        Route::post('/profil/changement-email', [TenantProfileController::class, 'demanderChangementEmail'])->name('profil.email.demander');
        Route::get('/compte/statut', [TenantProfileController::class, 'statutCompte'])->name('compte.statut');
        Route::post('/factures', [FactureController::class, 'store'])->name('factures.store');
        Route::get('/factures/{facture}', [FactureController::class, 'show'])->name('factures.show');
        Route::delete('/factures/{facture}', [FactureController::class, 'destroy'])->name('factures.destroy');
        Route::get('/commissions', [CommissionController::class, 'index'])->name('commissions.index');
        Route::post('/commissions/confirmer-fonds-recus', [CommissionController::class, 'confirmerFondsRecus'])->name('commissions.confirmer-fonds-recus');
        Route::post('/commissions/valider-virements', [CommissionController::class, 'validerVirements'])->name('commissions.valider-virements');
        Route::get('/clients/creer', [ClientController::class, 'create'])->name('clients.create');
        Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
        Route::get('/clients/{client}', [ClientController::class, 'show'])->name('clients.show');
        Route::get('/aide-decision/{client}', [ClientController::class, 'aideDecision'])->name('clients.aide-decision');
        Route::get('/mission/{client}', [ClientController::class, 'mission'])->name('clients.mission');
        Route::post('/mission/{client}/interet', [ClientController::class, 'manifesterInteret'])->name('clients.mission.interet');
        Route::get('/contrats-clients/{client}', [ClientController::class, 'contratsClients'])->name('clients.contrats-clients');
        Route::get('/conformites-clients/{client}', [ClientController::class, 'conformitesClients'])->name('clients.conformites-clients');
        Route::get('/conformites-clients/{client}/bibliotheque/recherche', [ClientController::class, 'rechercherDocumentsBibliotheque'])->name('clients.documents-bibliotheque.recherche');
        Route::post('/conformites-clients/{client}/documents', [ClientDocumentController::class, 'store'])->name('clients.documents.store');
        Route::get('/conformites-clients/{client}/documents/{type}/telecharger', [ClientDocumentController::class, 'download'])->name('clients.documents.download');
        Route::delete('/conformites-clients/{client}/documents/{type}', [ClientDocumentController::class, 'destroy'])->name('clients.documents.destroy');
        Route::get('/conformite/{client}', [ClientController::class, 'conformite'])->name('clients.conformite-lcbft');
        Route::put('/conformite/{client}', [ClientController::class, 'updateConformite'])->name('clients.conformite-lcbft.update');
        Route::post('/conformite/{client}/screening', [ClientController::class, 'screenerConformite'])->name('clients.conformite-lcbft.screening');
        Route::get('/mandat-assurance-vie/{client}', [ClientController::class, 'mandatAssuranceVie'])->name('clients.mandat-assurance-vie');
        Route::post('/mandat-assurance-vie/{client}', [ClientController::class, 'enregistrerMandatAssuranceVie'])->name('clients.mandat-assurance-vie.enregistrer');
        Route::get('/mandat-assurance-vie/{client}/pdf', [ClientController::class, 'telechargerMandatAssuranceViePdf'])->name('clients.mandat-assurance-vie.pdf');
Route::get('/mandat-assurance-deces/{client}', [ClientController::class, 'mandatAssuranceDeces'])->name('clients.mandat-assurance-deces');
Route::post('/mandat-assurance-deces/{client}', [ClientController::class, 'enregistrerMandatAssuranceDeces'])->name('clients.mandat-assurance-deces.enregistrer');
Route::get('/mandat-assurance-deces/{client}/pdf', [ClientController::class, 'telechargerMandatAssuranceDecesPdf'])->name('clients.mandat-assurance-deces.pdf');
Route::get('/mandat-assurance-emprunteur/{client}', [ClientController::class, 'mandatAssuranceEmprunteur'])->name('clients.mandat-assurance-emprunteur');
Route::post('/mandat-assurance-emprunteur/{client}', [ClientController::class, 'enregistrerMandatAssuranceEmprunteur'])->name('clients.mandat-assurance-emprunteur.enregistrer');
Route::get('/mandat-assurance-emprunteur/{client}/pdf', [ClientController::class, 'telechargerMandatAssuranceEmprunteurPdf'])->name('clients.mandat-assurance-emprunteur.pdf');
Route::get('/mandat-assurance-habitation/{client}', [ClientController::class, 'mandatAssuranceHabitation'])->name('clients.mandat-assurance-habitation');
Route::post('/mandat-assurance-habitation/{client}', [ClientController::class, 'enregistrerMandatAssuranceHabitation'])->name('clients.mandat-assurance-habitation.enregistrer');
Route::get('/mandat-assurance-habitation/{client}/pdf', [ClientController::class, 'telechargerMandatAssuranceHabitationPdf'])->name('clients.mandat-assurance-habitation.pdf');
Route::get('/mandat-assurance-obseques/{client}', [ClientController::class, 'mandatAssuranceObseques'])->name('clients.mandat-assurance-obseques');
Route::post('/mandat-assurance-obseques/{client}', [ClientController::class, 'enregistrerMandatAssuranceObseques'])->name('clients.mandat-assurance-obseques.enregistrer');
Route::get('/mandat-assurance-obseques/{client}/pdf', [ClientController::class, 'telechargerMandatAssuranceObsequesPdf'])->name('clients.mandat-assurance-obseques.pdf');
Route::get('/mandat-complementaire-sante/{client}', [ClientController::class, 'mandatComplementaireSante'])->name('clients.mandat-complementaire-sante');
Route::post('/mandat-complementaire-sante/{client}', [ClientController::class, 'enregistrerMandatComplementaireSante'])->name('clients.mandat-complementaire-sante.enregistrer');
Route::get('/mandat-complementaire-sante/{client}/pdf', [ClientController::class, 'telechargerMandatComplementaireSantePdf'])->name('clients.mandat-complementaire-sante.pdf');
Route::get('/mandat-contrat-capitalisation/{client}', [ClientController::class, 'mandatContratCapitalisation'])->name('clients.mandat-contrat-capitalisation');
Route::post('/mandat-contrat-capitalisation/{client}', [ClientController::class, 'enregistrerMandatContratCapitalisation'])->name('clients.mandat-contrat-capitalisation.enregistrer');
Route::get('/mandat-contrat-capitalisation/{client}/pdf', [ClientController::class, 'telechargerMandatContratCapitalisationPdf'])->name('clients.mandat-contrat-capitalisation.pdf');
Route::get('/mandat-garantie-accident-vie/{client}', [ClientController::class, 'mandatGarantieAccidentVie'])->name('clients.mandat-garantie-accident-vie');
Route::post('/mandat-garantie-accident-vie/{client}', [ClientController::class, 'enregistrerMandatGarantieAccidentVie'])->name('clients.mandat-garantie-accident-vie.enregistrer');
Route::get('/mandat-garantie-accident-vie/{client}/pdf', [ClientController::class, 'telechargerMandatGarantieAccidentViePdf'])->name('clients.mandat-garantie-accident-vie.pdf');
Route::get('/mandat-assurance-vehicule/{client}', [ClientController::class, 'mandatAssuranceVehicule'])->name('clients.mandat-assurance-vehicule');
Route::post('/mandat-assurance-vehicule/{client}', [ClientController::class, 'enregistrerMandatAssuranceVehicule'])->name('clients.mandat-assurance-vehicule.enregistrer');
Route::get('/mandat-assurance-vehicule/{client}/pdf', [ClientController::class, 'telechargerMandatAssuranceVehiculePdf'])->name('clients.mandat-assurance-vehicule.pdf');
Route::get('/mandat-plan-epargne-retraite/{client}', [ClientController::class, 'mandatPlanEpargneRetraite'])->name('clients.mandat-plan-epargne-retraite');
Route::post('/mandat-plan-epargne-retraite/{client}', [ClientController::class, 'enregistrerMandatPlanEpargneRetraite'])->name('clients.mandat-plan-epargne-retraite.enregistrer');
Route::get('/mandat-plan-epargne-retraite/{client}/pdf', [ClientController::class, 'telechargerMandatPlanEpargneRetraitePdf'])->name('clients.mandat-plan-epargne-retraite.pdf');
Route::get('/lettre-mission-scpi/{client}', [ClientController::class, 'lettreMissionScpi'])->name('clients.lettre-mission-scpi');
Route::post('/lettre-mission-scpi/{client}', [ClientController::class, 'enregistrerLettreMissionScpi'])->name('clients.lettre-mission-scpi.enregistrer');
Route::get('/lettre-mission-scpi/{client}/pdf', [ClientController::class, 'telechargerLettreMissionScpiPdf'])->name('clients.lettre-mission-scpi.pdf');
        Route::get('/recommandation-patrimoniale/{client}', [ClientController::class, 'recommandationPatrimoniale'])->name('clients.recommandation-patrimoniale');
        Route::post('/recommandation-patrimoniale/{client}', [ClientController::class, 'genererRecommandation'])->name('clients.recommandation-patrimoniale.generer');
        Route::get('/recommandation-patrimoniale/{client}/pdf', [ClientController::class, 'telechargerRecommandationPdf'])->name('clients.recommandation-patrimoniale.pdf');
        Route::get('/recommandation-patrimoniale/{client}/pdf/voir', [ClientController::class, 'voirRecommandationPdfEnLigne'])->name('clients.recommandation-patrimoniale.pdf.voir');
        Route::post('/recommandation-patrimoniale/{client}/valider', [ClientController::class, 'validerRecommandation'])->name('clients.recommandation-patrimoniale.valider');
        Route::put('/recommandation-patrimoniale/{client}/lettre/{analysis}', [ClientController::class, 'modifierRecommandationContenu'])->name('clients.recommandation-patrimoniale.modifier');
        Route::get('/plan-action/{client}', [ClientController::class, 'planAction'])->name('clients.plan-action');
        Route::post('/plan-action/{client}', [ClientController::class, 'genererPlanAction'])->name('clients.plan-action.generer');
        Route::get('/plan-action/{client}/pdf', [ClientController::class, 'telechargerPlanActionPdf'])->name('clients.plan-action.pdf');
        Route::put('/plan-action/{client}/contenu/{analysis}', [ClientController::class, 'modifierPlanActionContenu'])->name('clients.plan-action.modifier');
        Route::post('/aide-decision/{client}/suggestion', [ClientController::class, 'genererSuggestion'])->name('clients.aide-decision.suggestion');
        Route::get('/clients/{client}/modifier', [ClientController::class, 'edit'])->name('clients.edit');
        Route::put('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');
        Route::post('/clients/{client}/cloturer', [ClientController::class, 'cloturer'])->name('clients.cloturer');
        Route::get('/kyc/{client}', [ClientKycController::class, 'edit'])->name('clients.kyc.edit');
        Route::put('/kyc/{client}', [ClientKycController::class, 'update'])->name('clients.kyc.update');
        Route::get('/kyc/{client}/pdf', [ClientController::class, 'telechargerKycPdf'])->name('clients.kyc.pdf');
        Route::get('/sirene/{siret}', [SireneLookupController::class, 'rechercher'])->name('sirene.rechercher');
        Route::get('/patrimoine/{client}', [PatrimoineController::class, 'edit'])->name('clients.patrimoine.edit');
        Route::put('/patrimoine/{client}', [PatrimoineController::class, 'update'])->name('clients.patrimoine.update');
        Route::get('/patrimoine/{client}/pdf', [ClientController::class, 'telechargerPatrimoinePdf'])->name('clients.patrimoine.pdf');
        Route::get('/investisseur/{client}', [ProfilInvestisseurController::class, 'edit'])->name('clients.profil.edit');
        Route::put('/investisseur/{client}', [ProfilInvestisseurController::class, 'update'])->name('clients.profil.update');
        Route::get('/investisseur/{client}/pdf', [ClientController::class, 'telechargerProfilInvestisseurPdf'])->name('clients.profil.pdf');

        // Personne morale : formulaires séparés (voir claude/kyc-personne-morale.md).
        Route::get('/kyc-societe/{client}', [ClientKycMoraleController::class, 'edit'])->name('clients.kyc-morale.edit');
        Route::put('/kyc-societe/{client}', [ClientKycMoraleController::class, 'update'])->name('clients.kyc-morale.update');
        Route::get('/investisseur-societe/{client}', [ProfilInvestisseurMoraleController::class, 'edit'])->name('clients.profil-investisseur-morale.edit');
        Route::put('/investisseur-societe/{client}', [ProfilInvestisseurMoraleController::class, 'update'])->name('clients.profil-investisseur-morale.update');

        // Circuit Aide à la décision / Suggestion / Recommandation / Plan d'action société.
        Route::get('/pilotage-societe/{client}', [ClientPilotageMoraleController::class, 'edit'])->name('clients.pilotage-morale');
        Route::post('/pilotage-societe/{client}/suggestion', [ClientPilotageMoraleController::class, 'genererSuggestion'])->name('clients.pilotage-morale.suggestion');
        Route::get('/recommandation-societe/{client}', [ClientPilotageMoraleController::class, 'recommandation'])->name('clients.recommandation-morale');
        Route::post('/recommandation-societe/{client}', [ClientPilotageMoraleController::class, 'genererRecommandation'])->name('clients.recommandation-morale.generer');
        Route::get('/recommandation-societe/{client}/pdf', [ClientPilotageMoraleController::class, 'telechargerRecommandationPdf'])->name('clients.recommandation-morale.pdf');
        Route::put('/recommandation-societe/{client}/lettre/{analysis}', [ClientPilotageMoraleController::class, 'modifierRecommandationContenu'])->name('clients.recommandation-morale.modifier');
        Route::get('/plan-action-societe/{client}', [ClientPilotageMoraleController::class, 'planAction'])->name('clients.plan-action-morale');
        Route::post('/plan-action-societe/{client}', [ClientPilotageMoraleController::class, 'genererPlanAction'])->name('clients.plan-action-morale.generer');
        Route::get('/plan-action-societe/{client}/pdf', [ClientPilotageMoraleController::class, 'telechargerPlanActionPdf'])->name('clients.plan-action-morale.pdf');
        Route::put('/plan-action-societe/{client}/contenu/{analysis}', [ClientPilotageMoraleController::class, 'modifierPlanActionContenu'])->name('clients.plan-action-morale.modifier');

        Route::get('/rendez-vous', [RendezVousController::class, 'index'])->name('rendez-vous.index');
        Route::get('/rendez-vous/calendriers', [CalendarConnectionController::class, 'index'])->name('calendrier.index');
        Route::get('/rendez-vous/connecter/{provider}', [CalendarConnectionController::class, 'redirect'])->name('calendrier.connecter');
        Route::get('/rendez-vous/callback/{provider}', [CalendarConnectionController::class, 'callback'])->name('calendrier.callback');
        Route::delete('/rendez-vous/calendriers/{connection}', [CalendarConnectionController::class, 'destroy'])->name('calendrier.destroy');
        Route::get('/rendez-vous/disponibilites', [RendezVousController::class, 'disponibilites'])->name('rendez-vous.disponibilites');
        Route::post('/clients/{client}/rendez-vous', [RendezVousController::class, 'store'])->name('clients.rendez-vous.store');
        Route::post('/clients/{client}/demande-rendez-vous', [RendezVousController::class, 'demanderRendezVous'])->name('clients.rendez-vous.demander');
        Route::post('/rendez-vous/{rendezVous}/annuler', [RendezVousController::class, 'annuler'])->name('rendez-vous.annuler');
        Route::post('/rendez-vous/{rendezVous}/decaler', [RendezVousController::class, 'decaler'])->name('rendez-vous.decaler');
    });

    require __DIR__.'/tenant-auth.php';
});
