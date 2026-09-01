<?php

use App\Http\Controllers\CabinetController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

foreach (config('tenancy.central_domains') as $domain) {
    Route::domain($domain)->group(function () {
        Route::get('/', function () {
            return view('welcome');
        });

        Route::get('/dashboard', function () {
            return view('dashboard');
        })->middleware(['auth', 'verified'])->name('dashboard');

        Route::middleware('auth')->group(function () {
            Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
            Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
            Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

            Route::get('/cabinets', [CabinetController::class, 'index'])->name('cabinets.index');
            Route::get('/cabinets/creer', [CabinetController::class, 'create'])->name('cabinets.create');
            Route::post('/cabinets/sirene', [CabinetController::class, 'searchSirene'])->name('cabinets.sirene');
            Route::post('/cabinets', [CabinetController::class, 'store'])->name('cabinets.store');
        });

        require __DIR__.'/auth.php';
    });
}
