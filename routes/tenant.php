<?php

declare(strict_types=1);

use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientKycController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PatrimoineController;
use App\Http\Controllers\ProfilInvestisseurController;
use App\Http\Controllers\PortefeuilleCabinetController;
use App\Http\Controllers\CabinetProfileController;
use Stancl\Tenancy\Controllers\TenantAssetsController;
use App\Http\Controllers\UserAccountController;
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
        Route::get('/recommandation-patrimoniale/{client}', [ClientController::class, 'recommandationPatrimoniale'])->name('clients.recommandation-patrimoniale');
        Route::post('/recommandation-patrimoniale/{client}', [ClientController::class, 'genererRecommandation'])->name('clients.recommandation-patrimoniale.generer');
        Route::get('/recommandation-patrimoniale/{client}/pdf', [ClientController::class, 'telechargerRecommandationPdf'])->name('clients.recommandation-patrimoniale.pdf');
        Route::put('/recommandation-patrimoniale/{client}/lettre/{analysis}', [ClientController::class, 'modifierRecommandationContenu'])->name('clients.recommandation-patrimoniale.modifier');
        Route::post('/aide-decision/{client}/suggestion', [ClientController::class, 'genererSuggestion'])->name('clients.aide-decision.suggestion');
        Route::get('/clients/{client}/modifier', [ClientController::class, 'edit'])->name('clients.edit');
        Route::put('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');
        Route::get('/kyc/{client}', [ClientKycController::class, 'edit'])->name('clients.kyc.edit');
        Route::put('/kyc/{client}', [ClientKycController::class, 'update'])->name('clients.kyc.update');
        Route::get('/patrimoine/{client}', [PatrimoineController::class, 'edit'])->name('clients.patrimoine.edit');
        Route::put('/patrimoine/{client}', [PatrimoineController::class, 'update'])->name('clients.patrimoine.update');
        Route::get('/investisseur/{client}', [ProfilInvestisseurController::class, 'edit'])->name('clients.profil.edit');
        Route::put('/investisseur/{client}', [ProfilInvestisseurController::class, 'update'])->name('clients.profil.update');
    });

    require __DIR__.'/tenant-auth.php';
});
