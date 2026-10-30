<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientOnboardingController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\IntakeQueueController;
use App\Http\Controllers\ReferralController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Authentication
Route::post('/login', [AuthController::class, 'login']);

// Patient QR Onboarding (Scan from mobile app / web)
Route::get('/onboard/verify', [ClientOnboardingController::class, 'verifyToken']);
Route::post('/onboard/activate', [ClientOnboardingController::class, 'activateAccount']);

// Public Feed
Route::get('/contents/public', [ContentController::class, 'index']);

/*
|--------------------------------------------------------------------------
| Protected Routes (Sanctum Authenticated)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    // Current User Profile
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::put('/change-password', [AuthController::class, 'changePassword']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Intake Queue (Appointments)
    Route::get('/intake-queue', [IntakeQueueController::class, 'index']);
    Route::post('/intake-queue/{id}/send-to-clinic', [IntakeQueueController::class, 'sendToClinic']);
    Route::post('/intake-queue/{id}/decline', [IntakeQueueController::class, 'decline']);

    // Patient Registration & QR Generation
    Route::post('/clients/register-qr', [ClientOnboardingController::class, 'registerWithQr']);

    // Panel Doctors
    Route::get('/doctors', [DoctorController::class, 'index']);
    Route::post('/doctors', [DoctorController::class, 'store']);
    Route::post('/doctors/{id}/verify-smc', [DoctorController::class, 'verifySmc']);

    // Referrals & Payouts
    Route::get('/referrals', [ReferralController::class, 'index']);
    Route::get('/referrals/stats', [ReferralController::class, 'stats']);
    Route::put('/referrals/{id}/status', [ReferralController::class, 'updateStatus']);

    // Content Management
    Route::get('/contents', [ContentController::class, 'index']);
    Route::post('/contents', [ContentController::class, 'store']);
});

// Fallback/Open endpoints for dev testing without token if desired
Route::prefix('dev')->group(function () {
    Route::get('/intake-queue', [IntakeQueueController::class, 'index']);
    Route::get('/doctors', [DoctorController::class, 'index']);
    Route::get('/referrals', [ReferralController::class, 'index']);
    Route::get('/referrals/stats', [ReferralController::class, 'stats']);
    Route::get('/contents', [ContentController::class, 'index']);
});
