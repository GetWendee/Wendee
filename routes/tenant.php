<?php

declare(strict_types=1);

use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientKycController;
use App\Http\Controllers\SireneLookupController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PatrimoineController;
use App\Http\Controllers\ProfilInvestisseurController;
use App\Http\Controllers\PortefeuilleCabinetController;
use App\Http\Controllers\CabinetProfileController;
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

Route::middleware(['auth', 'verified'])->group(function () {

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

        Route::get('/portefeuille-cabinet', [PortefeuilleCabinetController::class, 'index'])
            ->name('portefeuille-cabinet.index');
        Route::get('/utilisateurs/creer', [UserAccountController::class, 'create'])->name('users.create');
        Route::post('/utilisateurs', [UserAccountController::class, 'store'])->name('users.store');
        Route::get('/utilisateurs/{user}', [UserAccountController::class, 'show'])->name('users.show');
        Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
        Route::get('/clients/creer', [ClientController::class, 'create'])->name('clients.create');
        Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
        Route::get('/clients/{client}', [ClientController::class, 'show'])->name('clients.show');
        Route::get('/aide-decision/{client}', [ClientController::class, 'aideDecision'])->name('clients.aide-decision');
        Route::get('/mission/{client}', [ClientController::class, 'mission'])->name('clients.mission');
        Route::get('/contrats-clients/{client}', [ClientController::class, 'contratsClients'])->name('clients.contrats-clients');
        Route::get('/conformites-clients/{client}', [ClientController::class, 'conformitesClients'])->name('clients.conformites-clients');
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
        Route::put('/recommandation-patrimoniale/{client}/lettre/{analysis}', [ClientController::class, 'modifierRecommandationContenu'])->name('clients.recommandation-patrimoniale.modifier');
        Route::get('/plan-action/{client}', [ClientController::class, 'planAction'])->name('clients.plan-action');
        Route::post('/plan-action/{client}', [ClientController::class, 'genererPlanAction'])->name('clients.plan-action.generer');
        Route::get('/plan-action/{client}/pdf', [ClientController::class, 'telechargerPlanActionPdf'])->name('clients.plan-action.pdf');
        Route::put('/plan-action/{client}/contenu/{analysis}', [ClientController::class, 'modifierPlanActionContenu'])->name('clients.plan-action.modifier');
        Route::post('/aide-decision/{client}/suggestion', [ClientController::class, 'genererSuggestion'])->name('clients.aide-decision.suggestion');
        Route::get('/clients/{client}/modifier', [ClientController::class, 'edit'])->name('clients.edit');
        Route::put('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');
        Route::get('/kyc/{client}', [ClientKycController::class, 'edit'])->name('clients.kyc.edit');
        Route::put('/kyc/{client}', [ClientKycController::class, 'update'])->name('clients.kyc.update');
        Route::get('/sirene/{siret}', [SireneLookupController::class, 'rechercher'])->name('sirene.rechercher');
        Route::get('/patrimoine/{client}', [PatrimoineController::class, 'edit'])->name('clients.patrimoine.edit');
        Route::put('/patrimoine/{client}', [PatrimoineController::class, 'update'])->name('clients.patrimoine.update');
        Route::get('/investisseur/{client}', [ProfilInvestisseurController::class, 'edit'])->name('clients.profil.edit');
        Route::put('/investisseur/{client}', [ProfilInvestisseurController::class, 'update'])->name('clients.profil.update');

        Route::get('/rendez-vous', [CalendarConnectionController::class, 'index'])->name('calendrier.index');
        Route::get('/rendez-vous/connecter/{provider}', [CalendarConnectionController::class, 'redirect'])->name('calendrier.connecter');
        Route::get('/rendez-vous/callback/{provider}', [CalendarConnectionController::class, 'callback'])->name('calendrier.callback');
        Route::delete('/rendez-vous/{connection}', [CalendarConnectionController::class, 'destroy'])->name('calendrier.destroy');
        Route::get('/rendez-vous/disponibilites', [RendezVousController::class, 'disponibilites'])->name('rendez-vous.disponibilites');
        Route::post('/clients/{client}/rendez-vous', [RendezVousController::class, 'store'])->name('clients.rendez-vous.store');
        Route::post('/rendez-vous/{rendezVous}/annuler', [RendezVousController::class, 'annuler'])->name('rendez-vous.annuler');
        Route::post('/rendez-vous/{rendezVous}/decaler', [RendezVousController::class, 'decaler'])->name('rendez-vous.decaler');
    });

    require __DIR__.'/tenant-auth.php';
});
