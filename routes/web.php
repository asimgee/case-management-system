<?php
// [file name]: web.php - UPDATED
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CaseController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SecuritySettingsController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
// Social Authentication Routes
Route::get('/auth/google', [SocialAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);

Route::get('/auth/microsoft', [SocialAuthController::class, 'redirectToMicrosoft'])->name('auth.microsoft');
Route::get('/auth/microsoft/callback', [SocialAuthController::class, 'handleMicrosoftCallback']);

Route::get('/auth/apple', [SocialAuthController::class, 'redirectToApple'])->name('auth.apple');
Route::get('/auth/apple/callback', [SocialAuthController::class, 'handleAppleCallback']);
// Two-Factor Authentication Routes
Route::get('/2fa/verify', [LoginController::class, 'showTwoFactorVerification'])->name('2fa.verify');
Route::post('/2fa/verify', [LoginController::class, 'verifyTwoFactor'])->name('2fa.verify.submit');
Route::post('/2fa/resend', [LoginController::class, 'resendVerificationCode'])->name('2fa.resend');

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
    
    // AJAX routes for dynamic dropdowns
    Route::get('/cases/remedies/{caseType}', [CaseController::class, 'getRemediesByType']);
    Route::post('/cases/case-types', [CaseController::class, 'storeCaseType']);
    Route::post('/cases/case-remedies', [CaseController::class, 'storeCaseRemedy']);
    Route::post('/cases/court-types', [CaseController::class, 'storeCourtType']);
    Route::post('/cases/lawyers', [CaseController::class, 'storeLawyer']);
    
    // Document download
    Route::get('/cases/{case}/download/{documentIndex}', [CaseController::class, 'downloadDocument'])->name('cases.download');
    
    // Add these routes to your web.php
    Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
    Route::get('/clients/create', [ClientController::class, 'create'])->name('clients.create');
    Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
    Route::get('/clients/{client}', [ClientController::class, 'show'])->name('clients.show');
    Route::get('/clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
    Route::put('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');
    Route::delete('/clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');
    Route::get('/clients-data', [ClientController::class, 'getClientsData'])->name('clients.data');

    Route::get('/hearings', [DashboardController::class, 'hearings'])->name('hearings');
    Route::get('/documents', [DashboardController::class, 'documents'])->name('documents');
    Route::get('/reports', [DashboardController::class, 'reports'])->name('reports');
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
    Route::prefix('admin')->name('admin.')->group(function () {
        // User Management
        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserManagementController::class, 'create'])->name('users.create');
        Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
        Route::post('/users/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('users.toggle-status');
        
        // Role Management
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    });
});