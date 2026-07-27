<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\QrGenerationController;
use App\Http\Controllers\QrRedirectController;
use App\Http\Controllers\Teams\TeamInvitationController;
use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');

Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', EnsureTeamMembership::class])
    ->group(function () {
        Route::get('dashboard', DashboardController::class)->name('dashboard');
    });

Route::get('/qrcodes/features', [QrGenerationController::class, 'features'])->name('qrcodes.features');

// --- Gestion des QR codes : protégée par la connexion Jetstream existante ---
Route::middleware(['auth'])->group(function () {
    Route::get('/qrcodes', [QrGenerationController::class, 'index'])->name('qrcodes.index');
    Route::get('/qrcodes/create', [QrGenerationController::class, 'create'])->name('qrcodes.create');
    Route::post('/qrcodes', [QrGenerationController::class, 'store'])->name('qrcodes.store');
    Route::patch('/qrcodes/{resource}/toggle', [QrGenerationController::class, 'toggleActive'])->name('qrcodes.toggle');
    Route::delete('/qrcodes/{resource}', [QrGenerationController::class, 'destroy'])->name('qrcodes.destroy');
    Route::get('/qrcodes/{resource}/statistiques', [QrGenerationController::class, 'statistiques'])->name('qrcodes.statistiques');
});

// --- Redirection publique du QR code (scannée par n'importe qui, sans compte) ---
Route::get('/r/{short_code}', [QrRedirectController::class, 'redirect'])->name('qr.redirect');

require __DIR__.'/settings.php';