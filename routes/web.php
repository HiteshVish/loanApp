<?php

use App\Http\Controllers\Admin\KycController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\KycApplicationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Public route - Redirect to login
Route::get('/', function () {
    if (Auth::check()) {
        // If already logged in, redirect based on role and KYC status
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            return redirect()->route('dashboard');
        }
        
        if (!$user->hasSubmittedKyc()) {
            return redirect()->route('kyc.create');
        }
        
        if (!$user->isKycApproved()) {
            return redirect()->route('kyc.status');
        }
        
        return redirect()->route('dashboard');
    }
    
    return redirect()->route('login');
});

// Google OAuth Routes
Route::middleware('guest')->group(function () {
    Route::get('/auth/google', [SocialAuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
});

// KYC Routes (authenticated users only, no KYC check on these routes)
Route::middleware(['auth'])->group(function () {
    Route::get('/kyc/apply', [KycApplicationController::class, 'create'])->name('kyc.create');
    Route::post('/kyc/apply', [KycApplicationController::class, 'store'])->name('kyc.store');
    Route::get('/kyc/status', [KycApplicationController::class, 'status'])->name('kyc.status');
});

// Admin Routes (admin users, no KYC check)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Control Panel
    Route::get('/control-panel', function () {
        return view('admin.control-panel');
    })->name('control-panel');
    
    // Settings
    Route::post('/settings/interest-rate', [App\Http\Controllers\Admin\SettingsController::class, 'updateInterestRate'])->name('settings.interest-rate');
    Route::get('/settings/interest-rate', [App\Http\Controllers\Admin\SettingsController::class, 'getInterestRate'])->name('settings.get-interest-rate');
    
    // User Management
    Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
    Route::resource('users', AdminUserController::class)->only(['index', 'edit', 'update', 'destroy']);
    Route::post('/users/{user}/verify-email', [AdminUserController::class, 'verifyEmail'])->name('users.verify-email');
    
    // KYC Management (each loan is a separate application)
    Route::get('/kyc', [KycController::class, 'index'])->name('kyc.index');
    Route::get('/kyc/create', [KycController::class, 'create'])->name('kyc.create');
    Route::post('/kyc', [KycController::class, 'store'])->name('kyc.store');
    Route::get('/kyc/{loan}', [KycController::class, 'show'])->name('kyc.show');
    Route::get('/kyc/{loan}/contacts', [KycController::class, 'contacts'])->name('kyc.contacts');
    Route::delete('/kyc/{loan}/contacts', [KycController::class, 'deleteAllContacts'])->name('kyc.contacts.deleteAll');
    Route::get('/kyc/{loan}/locations', [KycController::class, 'locations'])->name('kyc.locations');
    Route::get('/kyc/{loan}/edit', [KycController::class, 'edit'])->name('kyc.edit');
    Route::put('/kyc/{loan}', [KycController::class, 'update'])->name('kyc.update');
    Route::delete('/kyc/{loan}', [KycController::class, 'destroy'])->name('kyc.destroy');
    Route::post('/kyc/{loan}/approve', [KycController::class, 'approve'])->name('kyc.approve');
    Route::post('/kyc/{loan}/reject', [KycController::class, 'reject'])->name('kyc.reject');
    Route::post('/kyc/{loan}/disapprove', [KycController::class, 'disapprove'])->name('kyc.disapprove');
    
    // Payment Management
    Route::get('/payment', [App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('payment.index');
    Route::get('/payment/{loanId}', [App\Http\Controllers\Admin\PaymentController::class, 'show'])->name('payment.show');
    Route::post('/payment/{loanId}/record', [App\Http\Controllers\Admin\PaymentController::class, 'recordPayment'])->name('payment.record');
    Route::patch('/loan/{loanId}/complete', [App\Http\Controllers\Admin\PaymentController::class, 'markAsCompleted'])->name('loan.complete');
});

// Protected routes WITH KYC check (regular users need approved KYC)
Route::middleware(['auth', 'kyc'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // Account Pages (All Users)
    Route::get('/account/profile', function () {
        return view('account.profile');
    })->name('account.profile');

    Route::post('/account/profile', function () {
        // Handle profile update
        return redirect()->route('account.profile')->with('success', 'Profile updated successfully!');
    });

    Route::get('/account/security', function () {
        return view('account.security');
    })->name('account.security');

    Route::post('/account/security', function () {
        // Handle password update
        return redirect()->route('account.security')->with('success', 'Password updated successfully!');
    });


    // Legacy profile routes (keeping for compatibility)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
