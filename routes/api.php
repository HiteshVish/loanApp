<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\UserRegistrationController;
use App\Http\Controllers\Api\LoanController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\SystemSettingsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public routes - Only Login APIs
Route::post('/login', [AuthController::class, 'login']);
Route::post('/google-login-simple', [AuthController::class, 'googleLoginSimple']);

// Protected routes (All authenticated users)
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::get('/user', [AuthController::class, 'user']);
    Route::get('/userDetails', [AuthController::class, 'userDetails']);
    Route::get('/kycStatus', [AuthController::class, 'kycStatus']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Contacts - Bulk Insert Only
    Route::post('/contacts/bulk', [ContactController::class, 'storeBulk']);
    
    // Locations - Single Insert Only
    Route::post('/locations', [LocationController::class, 'store']);
    
    // User Registration Form
    Route::post('/userRegistration/form', [UserRegistrationController::class, 'form']);
    
    // Loan Details
    Route::post('/loanDetails', [LoanController::class, 'loanDetails']);
    Route::get('/loan_applications', [LoanController::class, 'loanApplications']);
    Route::get('/loan_application', [LoanController::class, 'loanApplicationSummary']);
    
    // Dashboard
    Route::get('/appDashboard', [DashboardController::class, 'appDashboard']);
    
    // Transactions
    Route::get('/transaction', [TransactionController::class, 'transaction']);
    
    // System Settings
    Route::get('/system-settings', [SystemSettingsController::class, 'index']);
});
