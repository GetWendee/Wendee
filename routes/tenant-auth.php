<?php

use App\Http\Controllers\TenantAuth\AuthenticatedSessionController;
use App\Http\Controllers\TenantAuth\ConfirmablePasswordController;
use App\Http\Controllers\TenantAuth\DeviceLoginController;
use App\Http\Controllers\TenantAuth\EmailVerificationNotificationController;
use App\Http\Controllers\TenantAuth\EmailVerificationPromptController;
use App\Http\Controllers\TenantAuth\NewPasswordController;
use App\Http\Controllers\TenantAuth\PasswordController;
use App\Http\Controllers\TenantAuth\PasswordResetLinkController;
use App\Http\Controllers\TenantAuth\RegisteredUserController;
use App\Http\Controllers\TenantAuth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');

    // Contrôle du nouvel appareil de connexion : page d'attente affichée
    // tant que la connexion n'est pas confirmée (mail ou autre session).
    Route::get('device-login/attente', [DeviceLoginController::class, 'pending'])
        ->name('device-login.pending');
    Route::get('device-login/statut', [DeviceLoginController::class, 'status'])
        ->name('device-login.status');
    Route::post('device-login/terminer', [DeviceLoginController::class, 'complete'])
        ->name('device-login.complete');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');
    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);
    Route::put('password', [PasswordController::class, 'update'])->name('password.update');
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    // Popup de contrôle d'appareil, affichée sur une session déjà connectée
    // pendant qu'un autre appareil attend une confirmation de connexion.
    Route::get('device-login/en-attente-pour-moi', [DeviceLoginController::class, 'pendingForMe'])
        ->name('device-login.pending-for-me');
});

// Confirmation/refus d'une connexion : accessible soit via le lien mail
// signé (utilisateur pas encore connecté), soit depuis une session déjà
// connectée qui est propriétaire du challenge (popup). Le contrôleur
// vérifie lui-même laquelle des deux conditions s'applique.
Route::match(['get', 'post'], 'device-login/{challenge}/confirmer', [DeviceLoginController::class, 'confirm'])
    ->name('device-login.confirm');
Route::match(['get', 'post'], 'device-login/{challenge}/refuser', [DeviceLoginController::class, 'deny'])
    ->name('device-login.deny');
