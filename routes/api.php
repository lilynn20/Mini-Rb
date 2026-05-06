<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AnnonceController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\AvisController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\FavoriteController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/annonces', [AnnonceController::class, 'index']);
Route::get('/annonces/{annonce}', [AnnonceController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/email/verification-notification', [AuthController::class, 'resendVerification']);

    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);

    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/favorites/{annonce}', [FavoriteController::class, 'store']);
    Route::delete('/favorites/{annonce}', [FavoriteController::class, 'destroy']);

    Route::middleware('verified')->group(function () {
        Route::post('/annonces', [AnnonceController::class, 'store']);
        Route::post('/annonces/{annonce}', [AnnonceController::class, 'update']);
        Route::delete('/annonces/{annonce}', [AnnonceController::class, 'destroy']);

        Route::get('/reservations', [ReservationController::class, 'index']);
        Route::post('/annonces/{annonce}/reserver', [ReservationController::class, 'store']);
        Route::patch('/reservations/{id}/accept', [ReservationController::class, 'accept']);
        Route::patch('/reservations/{id}/refuse', [ReservationController::class, 'refuse']);
        Route::patch('/reservations/{id}/cancel', [ReservationController::class, 'cancel']);

        Route::post('/reservations/{id}/avis', [AvisController::class, 'store']);
        Route::delete('/avis/{id}', [AvisController::class, 'destroy']);

        Route::get('/admin', [AdminController::class, 'index']);
        Route::delete('/admin/users/{id}', [AdminController::class, 'deleteUser']);
        Route::delete('/admin/annonces/{id}', [AdminController::class, 'deleteAnnonce']);
    });
});
