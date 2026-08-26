<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->name('tenant.')->group(function () {

    Route::get('/', function () {
        return redirect()->route('tenant.login');
    });

    Route::get('/dashboard', function () {
        return view('tenant.dashboard');
    })->middleware(['auth', 'verified'])->name('dashboard');

    require __DIR__.'/tenant-auth.php';
});
