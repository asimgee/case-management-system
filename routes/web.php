<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CaseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\HearingController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SecuritySettingsController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SocialLoginController;
use App\Http\Middleware\CheckRole;
use App\Http\Controllers\AIController;
Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Two-Factor Authentication Routes
Route::get('/2fa/verify', [LoginController::class, 'showTwoFactorVerification'])->name('2fa.verify');
Route::post('/2fa/verify', [LoginController::class, 'verifyTwoFactor'])->name('2fa.verify.submit');
Route::post('/2fa/resend', [LoginController::class, 'resendVerificationCode'])->name('2fa.resend');

// Social Login Routes
Route::prefix('auth')->group(function () {
    Route::get('/google', [SocialLoginController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/google/callback', [SocialLoginController::class, 'handleGoogleCallback']);
    
    Route::get('/microsoft', [SocialLoginController::class, 'redirectToMicrosoft'])->name('auth.microsoft');
    Route::get('/microsoft/callback', [SocialLoginController::class, 'handleMicrosoftCallback']);
    
    Route::get('/apple', [SocialLoginController::class, 'redirectToApple'])->name('auth.apple');
    Route::get('/apple/callback', [SocialLoginController::class, 'handleAppleCallback']);
});


Route::middleware(['auth'])->group(function () {
    // AI Dashboard Route
    Route::get('/ai', [AIController::class, 'index'])->name('ai.index');
    
    // AI API Routes
    Route::prefix('ai')->name('ai.')->group(function () {
        Route::post('/chat/{case?}', [AIController::class, 'chat'])->name('chat');
        Route::post('/quick/{case}', [AIController::class, 'quickAction'])->name('quick');
        Route::post('/generate-document/{case}', [AIController::class, 'generateDocument'])->name('generate-document');
        Route::get('/status', [AIController::class, 'status'])->name('status');
        
        // Additional AI features
        Route::post('/analyze/{case}', [AIController::class, 'analyze'])->name('analyze');
        Route::post('/summarize/{case}', [AIController::class, 'summarize'])->name('summarize');
        Route::get('/history', [AIController::class, 'history'])->name('history');
    });
});
// Protected Routes
Route::middleware(['auth'])->group(function () {
    // Dashboard Routes
    Route::get('/admin', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    
    // Case Routes
    Route::get('/cases', [CaseController::class, 'index'])->name('cases.index');
    Route::get('/cases/create', [CaseController::class, 'create'])->name('cases.create');
    Route::post('/cases', [CaseController::class, 'store'])->name('cases.store');
    Route::get('/cases/{case}', [CaseController::class, 'show'])->name('cases.show');
    Route::get('/cases/{case}/edit', [CaseController::class, 'edit'])->name('cases.edit');
    Route::put('/cases/{case}', [CaseController::class, 'update'])->name('cases.update');
    Route::delete('/cases/{case}', [CaseController::class, 'destroy'])->name('cases.destroy');
    
    // Case Documents
    Route::get('/cases/{case}/download-document/{index}', [CaseController::class, 'downloadDocumentByIndex'])->name('cases.download-document');
    Route::delete('/cases/{case}/remove-document/{index}', [CaseController::class, 'removeDocumentByIndex'])->name('cases.remove-document');
    
    // AJAX Routes
    Route::get('/cases/remedies/{caseType}', [CaseController::class, 'getRemediesByType']);
    Route::post('/cases/case-types', [CaseController::class, 'storeCaseType']);
    Route::post('/cases/case-remedies', [CaseController::class, 'storeCaseRemedy']);
    Route::post('/cases/court-types', [CaseController::class, 'storeCourtType']);
    Route::post('/cases/lawyers', [CaseController::class, 'storeLawyer']);
    
    // Client Routes
    Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
    Route::get('/clients/create', [ClientController::class, 'create'])->name('clients.create');
    Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
    Route::get('/clients/{client}', [ClientController::class, 'show'])->name('clients.show');
    Route::get('/clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
    Route::put('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');
    Route::delete('/clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');
    Route::get('/clients-data', [ClientController::class, 'getClientsData'])->name('clients.data');

    // Hearing Routes
    Route::resource('hearings', HearingController::class);

    Route::prefix('hearings')->group(function () {
        Route::get('/{hearing}/complete', [HearingController::class, 'markAsCompleted'])->name('hearings.complete');
        Route::post('/{hearing}/cancel', [HearingController::class, 'markAsCancelled'])->name('hearings.cancel');
        Route::post('/{hearing}/adjourn', [HearingController::class, 'adjourn'])->name('hearings.adjourn');
        Route::post('/{hearing}/reschedule', [HearingController::class, 'reschedule'])->name('hearings.reschedule');
        Route::post('/{hearing}/notes', [HearingController::class, 'addNote'])->name('hearings.add-note');
        Route::post('/{hearing}/send-reminder', [HearingController::class, 'sendReminder'])->name('hearings.send-reminder');
        
        // AJAX routes
        Route::get('/statistics', [HearingController::class, 'getStatistics'])->name('hearings.statistics');
        Route::get('/today', [HearingController::class, 'getTodaysHearings'])->name('hearings.today');
        Route::get('/upcoming', [HearingController::class, 'getUpcomingHearings'])->name('hearings.upcoming');
    });
    // Calendar routes
Route::get('/calendar', [HearingController::class, 'calendar'])->name('hearings.calendar');
Route::get('/hearings/{hearing}/modal', [HearingController::class, 'modalView'])->name('hearings.modal');
    // Document Routes
    Route::prefix('documents')->group(function () {
        Route::get('/', [DocumentController::class, 'index'])->name('documents.index');
        Route::get('/create', [DocumentController::class, 'create'])->name('documents.create');
        Route::post('/', [DocumentController::class, 'store'])->name('documents.store');
        Route::get('/{document}', [DocumentController::class, 'show'])->name('documents.show');
        Route::get('/{document}/edit', [DocumentController::class, 'edit'])->name('documents.edit');
        Route::put('/{document}', [DocumentController::class, 'update'])->name('documents.update');
        Route::delete('/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');
        Route::get('/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
        Route::get('/{document}/preview', [DocumentController::class, 'preview'])->name('documents.preview');
        Route::post('/bulk-delete', [DocumentController::class, 'bulkDelete'])->name('documents.bulk-delete');
        Route::post('/bulk-download', [DocumentController::class, 'bulkDownload'])->name('documents.bulk-download');
    });
   
    // Report Routes
    Route::prefix('reports')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/financial', [ReportController::class, 'financial'])->name('reports.financial');
        Route::get('/case-analysis', [ReportController::class, 'caseAnalysis'])->name('reports.case-analysis');
        Route::get('/client-analysis', [ReportController::class, 'clientAnalysis'])->name('reports.client-analysis');
        Route::get('/performance', [ReportController::class, 'performance'])->name('reports.performance');
        Route::post('/generate', [ReportController::class, 'generate'])->name('reports.generate');
        Route::get('/export/{type}', [ReportController::class, 'export'])->name('reports.export');
        Route::get('/api/stats', [ReportController::class, 'getStats'])->name('reports.api.stats');
        Route::get('/api/chart-data', [ReportController::class, 'getChartData'])->name('reports.api.chart-data');
    });
    
    Route::get('/settings', [DashboardController::class, 'settings'])->name('settings');
    
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    
    // 2FA Routes
    Route::post('/profile/enable-email-2fa', [ProfileController::class, 'enableEmail2FA'])->name('profile.enable-email-2fa');
    Route::post('/profile/disable-email-2fa', [ProfileController::class, 'disableEmail2FA'])->name('profile.disable-email-2fa');
    Route::get('/profile/google2fa-setup', [ProfileController::class, 'showGoogle2FASetup'])->name('profile.google2fa-setup');
    Route::post('/profile/enable-google-2fa', [ProfileController::class, 'enableGoogle2FA'])->name('profile.enable-google-2fa');
    Route::post('/profile/disable-google-2fa', [ProfileController::class, 'disableGoogle2FA'])->name('profile.disable-google-2fa');
    
    // Security Settings Routes
    Route::get('/security-settings', [SecuritySettingsController::class, 'index'])->name('security.settings');
    Route::post('/security-settings', [SecuritySettingsController::class, 'updateSecuritySettings'])->name('security.settings.update');
    Route::post('/enable-two-factor', [SecuritySettingsController::class, 'enableTwoFactor'])->name('two-factor.enable');
    Route::post('/disable-two-factor', [SecuritySettingsController::class, 'disableTwoFactor'])->name('two-factor.disable');
    
    // Subscription Routes
    Route::get('/plans', [SubscriptionController::class, 'plans'])->name('subscriptions.plans');
    Route::post('/subscribe/{plan}', [SubscriptionController::class, 'subscribe'])->name('subscriptions.subscribe');
    Route::post('/cancel-subscription', [SubscriptionController::class, 'cancelSubscription'])->name('subscriptions.cancel');
    Route::get('/billing-history', [SubscriptionController::class, 'billingHistory'])->name('subscriptions.history');
    
    // Admin Routes
    Route::prefix('admin')->name('admin.')->middleware(['role:admin'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');
        
        // User Management
        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserManagementController::class, 'create'])->name('users.create');
        Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
        Route::post('/users/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::post('/users/{user}/reset-password', [UserManagementController::class, 'resetPassword'])->name('users.reset-password');
        
        // Role Management
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
        Route::post('/roles/{role}/assign-permission', [RoleController::class, 'assignPermission'])->name('roles.assign-permission');
        Route::post('/roles/{role}/remove-permission', [RoleController::class, 'removePermission'])->name('roles.remove-permission');
        
        // System Settings
        Route::get('/settings', [DashboardController::class, 'adminSettings'])->name('settings');
        Route::post('/settings/general', [DashboardController::class, 'updateGeneralSettings'])->name('settings.general.update');
        Route::post('/settings/email', [DashboardController::class, 'updateEmailSettings'])->name('settings.email.update');
        Route::post('/settings/security', [DashboardController::class, 'updateSecuritySettings'])->name('settings.security.update');
        
        // Audit Logs
        Route::get('/audit-logs', [DashboardController::class, 'auditLogs'])->name('audit-logs');
        Route::get('/audit-logs/{log}', [DashboardController::class, 'showAuditLog'])->name('audit-logs.show');
        
        // Backup
        Route::get('/backup', [DashboardController::class, 'backup'])->name('backup');
        Route::post('/backup/create', [DashboardController::class, 'createBackup'])->name('backup.create');
        Route::post('/backup/restore', [DashboardController::class, 'restoreBackup'])->name('backup.restore');
        Route::delete('/backup/{backup}', [DashboardController::class, 'deleteBackup'])->name('backup.delete');
        
        // System Health
        Route::get('/system-health', [DashboardController::class, 'systemHealth'])->name('system-health');
    });
});

// Catch-all route for SPA or 404
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});