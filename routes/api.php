<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API routes for reports
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('reports')->group(function () {
        Route::get('/stats', [\App\Http\Controllers\ReportController::class, 'getStats'])->name('api.reports.stats');
        Route::get('/chart-data', [\App\Http\Controllers\ReportController::class, 'getChartData'])->name('api.reports.chart-data');
    });

    // Cases API
    Route::prefix('cases')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\CaseController::class, 'index']);
        Route::get('/{case}', [\App\Http\Controllers\Api\CaseController::class, 'show']);
        Route::post('/', [\App\Http\Controllers\Api\CaseController::class, 'store']);
        Route::put('/{case}', [\App\Http\Controllers\Api\CaseController::class, 'update']);
        Route::delete('/{case}', [\App\Http\Controllers\Api\CaseController::class, 'destroy']);
        
        // Case Types
        Route::get('/types', [\App\Http\Controllers\Api\CaseController::class, 'getCaseTypes']);
        Route::post('/types', [\App\Http\Controllers\Api\CaseController::class, 'storeCaseType']);
        
        // Remedies
        Route::get('/remedies/{caseType}', [\App\Http\Controllers\Api\CaseController::class, 'getRemediesByType']);
        Route::post('/remedies', [\App\Http\Controllers\Api\CaseController::class, 'storeCaseRemedy']);
        
        // Court Types
        Route::get('/court-types', [\App\Http\Controllers\Api\CaseController::class, 'getCourtTypes']);
        Route::post('/court-types', [\App\Http\Controllers\Api\CaseController::class, 'storeCourtType']);
        
        // Lawyers
        Route::get('/lawyers', [\App\Http\Controllers\Api\CaseController::class, 'getLawyers']);
        Route::post('/lawyers', [\App\Http\Controllers\Api\CaseController::class, 'storeLawyer']);
        
        // Documents
        Route::post('/{case}/documents', [\App\Http\Controllers\Api\CaseController::class, 'uploadDocument']);
        Route::delete('/documents/{document}', [\App\Http\Controllers\Api\CaseController::class, 'removeDocument']);
        Route::get('/documents/{document}/download', [\App\Http\Controllers\Api\CaseController::class, 'downloadDocument']);
    });

    // Clients API
    Route::prefix('clients')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\ClientController::class, 'index']);
        Route::get('/{client}', [\App\Http\Controllers\Api\ClientController::class, 'show']);
        Route::post('/', [\App\Http\Controllers\Api\ClientController::class, 'store']);
        Route::put('/{client}', [\App\Http\Controllers\Api\ClientController::class, 'update']);
        Route::delete('/{client}', [\App\Http\Controllers\Api\ClientController::class, 'destroy']);
        Route::get('/{client}/cases', [\App\Http\Controllers\Api\ClientController::class, 'getClientCases']);
    });

    // Documents API
    Route::prefix('documents')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\DocumentController::class, 'index']);
        Route::get('/{document}', [\App\Http\Controllers\Api\DocumentController::class, 'show']);
        Route::post('/', [\App\Http\Controllers\Api\DocumentController::class, 'store']);
        Route::put('/{document}', [\App\Http\Controllers\Api\DocumentController::class, 'update']);
        Route::delete('/{document}', [\App\Http\Controllers\Api\DocumentController::class, 'destroy']);
        Route::get('/{document}/download', [\App\Http\Controllers\Api\DocumentController::class, 'download']);
        Route::post('/bulk-delete', [\App\Http\Controllers\Api\DocumentController::class, 'bulkDelete']);
        Route::post('/bulk-download', [\App\Http\Controllers\Api\DocumentController::class, 'bulkDownload']);
    });

    // Hearings API
    Route::prefix('hearings')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\HearingController::class, 'index']);
        Route::get('/calendar', [\App\Http\Controllers\Api\HearingController::class, 'calendar']);
        Route::get('/upcoming', [\App\Http\Controllers\Api\HearingController::class, 'upcoming']);
        Route::post('/{case}/update-next-date', [\App\Http\Controllers\Api\HearingController::class, 'updateNextDate']);
        Route::get('/{hearing}', [\App\Http\Controllers\Api\HearingController::class, 'show']);
        Route::post('/', [\App\Http\Controllers\Api\HearingController::class, 'store']);
        Route::put('/{hearing}', [\App\Http\Controllers\Api\HearingController::class, 'update']);
        Route::delete('/{hearing}', [\App\Http\Controllers\Api\HearingController::class, 'destroy']);
    });

    // Profile API
    Route::prefix('profile')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\ProfileController::class, 'index']);
        Route::put('/', [\App\Http\Controllers\Api\ProfileController::class, 'update']);
        Route::put('/password', [\App\Http\Controllers\Api\ProfileController::class, 'updatePassword']);
        Route::post('/enable-email-2fa', [\App\Http\Controllers\Api\ProfileController::class, 'enableEmail2FA']);
        Route::post('/disable-email-2fa', [\App\Http\Controllers\Api\ProfileController::class, 'disableEmail2FA']);
        Route::post('/enable-google-2fa', [\App\Http\Controllers\Api\ProfileController::class, 'enableGoogle2FA']);
        Route::post('/disable-google-2fa', [\App\Http\Controllers\Api\ProfileController::class, 'disableGoogle2FA']);
    });

    // Subscription API
    Route::prefix('subscription')->group(function () {
        Route::get('/plans', [\App\Http\Controllers\Api\SubscriptionController::class, 'plans']);
        Route::post('/subscribe/{plan}', [\App\Http\Controllers\Api\SubscriptionController::class, 'subscribe']);
        Route::post('/cancel', [\App\Http\Controllers\Api\SubscriptionController::class, 'cancelSubscription']);
        Route::get('/billing-history', [\App\Http\Controllers\Api\SubscriptionController::class, 'billingHistory']);
    });
});

// Public API routes (no authentication required)
Route::get('/public/case-types', [\App\Http\Controllers\Api\PublicController::class, 'getCaseTypes']);
Route::get('/public/court-types', [\App\Http\Controllers\Api\PublicController::class, 'getCourtTypes']);
Route::get('/public/lawyers', [\App\Http\Controllers\Api\PublicController::class, 'getLawyers']);

// Authentication API routes
Route::prefix('auth')->group(function () {
    Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);
    Route::post('/register', [\App\Http\Controllers\Api\AuthController::class, 'register']);
    Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::post('/refresh', [\App\Http\Controllers\Api\AuthController::class, 'refresh']);
    
    // Password reset
    Route::post('/forgot-password', [\App\Http\Controllers\Api\AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [\App\Http\Controllers\Api\AuthController::class, 'resetPassword']);
    
    // 2FA
    Route::post('/2fa/verify', [\App\Http\Controllers\Api\AuthController::class, 'verifyTwoFactor']);
    Route::post('/2fa/resend', [\App\Http\Controllers\Api\AuthController::class, 'resendVerificationCode']);
    
    // Social authentication
    Route::get('/{provider}/redirect', [\App\Http\Controllers\Api\AuthController::class, 'redirectToProvider']);
    Route::get('/{provider}/callback', [\App\Http\Controllers\Api\AuthController::class, 'handleProviderCallback']);
});