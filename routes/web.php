<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AnnonceController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\AvisController;

Route::get('/', [AnnonceController::class, 'index'])->name('home');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Email verification
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (\Illuminate\Foundation\Auth\EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect()->route('home')->with('success', 'Email vérifié avec succès ! Bienvenue sur Mini-Rb 🎉');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', [AuthController::class, 'resendVerification'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');

Route::middleware(['auth', 'verified'])->group(function () {
    // Annonces CRUD
    Route::get('/annonces/create', [AnnonceController::class, 'create'])->name('annonces.create');
    Route::post('/annonces', [AnnonceController::class, 'store'])->name('annonces.store');
    Route::get('/annonces/{annonce}/edit', [AnnonceController::class, 'edit'])->name('annonces.edit');
    Route::put('/annonces/{annonce}', [AnnonceController::class, 'update'])->name('annonces.update');
    Route::delete('/annonces/{annonce}', [AnnonceController::class, 'destroy'])->name('annonces.destroy');

    // Reservations
    Route::post('/annonces/{annonce}/reserver', [ReservationController::class, 'store'])->name('reservations.store');
    Route::get('/mes-reservations', [ReservationController::class, 'index'])->name('reservations.index');
    Route::patch('/reservations/{id}/accept', [ReservationController::class, 'accept'])->name('reservations.accept');
    Route::patch('/reservations/{id}/refuse', [ReservationController::class, 'refuse'])->name('reservations.refuse');
    Route::patch('/reservations/{id}/cancel', [ReservationController::class, 'cancel'])->name('reservations.cancel');

    // Avis (reviews)
    Route::post('/reservations/{id}/avis', [AvisController::class, 'store'])->name('avis.store');
    Route::delete('/avis/{id}', [AvisController::class, 'destroy'])->name('avis.destroy');

    // Admin dashboard
    Route::get('/admin', [\App\Http\Controllers\AdminController::class, 'index'])->name('admin.index');
    Route::delete('/admin/users/{id}', [\App\Http\Controllers\AdminController::class, 'deleteUser'])->name('admin.users.delete');
    Route::delete('/admin/annonces/{id}', [\App\Http\Controllers\AdminController::class, 'deleteAnnonce'])->name('admin.annonces.delete');
});

// Must be AFTER the auth group so /annonces/create is matched first
Route::get('/annonces/{annonce}', [AnnonceController::class, 'show'])->name('annonces.show');