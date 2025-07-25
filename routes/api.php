<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\GdprController;
use App\Http\Controllers\MandatorController;

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

// GDPR Compliant Company Management API Routes
Route::prefix('v1')->group(function () {

    // Company Management Routes
    Route::prefix('companies')->group(function () {
        Route::get('/', [CompanyController::class, 'index']);
        Route::post('/', [CompanyController::class, 'store']);
        Route::get('/{company}', [CompanyController::class, 'show']);
        Route::put('/{company}', [CompanyController::class, 'update']);
        Route::delete('/{company}', [CompanyController::class, 'destroy']);
        Route::get('/{company}/gdpr-status', [CompanyController::class, 'gdprStatus']);
    });

    // Mandator Management Routes
    Route::prefix('mandators')->group(function () {
        Route::get('/', [MandatorController::class, 'index']);
        Route::post('/', [MandatorController::class, 'store']);
        Route::get('/{mandator}', [MandatorController::class, 'show']);
        Route::put('/{mandator}', [MandatorController::class, 'update']);
        Route::delete('/{mandator}', [MandatorController::class, 'destroy']);

        // Disclosure subscription management
        Route::post('/{mandator}/add-disclosure-subscription', [MandatorController::class, 'addDisclosureSubscription']);
        Route::post('/{mandator}/remove-disclosure-subscription', [MandatorController::class, 'removeDisclosureSubscription']);
        Route::post('/{mandator}/update-last-disclosure-date', [MandatorController::class, 'updateLastDisclosureDate']);

        // Company-specific routes
        Route::get('/company/{company}', [MandatorController::class, 'getByCompany']);
        Route::get('/disclosure-summary', [MandatorController::class, 'getDisclosureSummary']);
    });

    // GDPR Data Subject Rights Routes
    Route::prefix('gdpr')->group(function () {
        // Right to be forgotten
        Route::post('/right-to-be-forgotten', [GdprController::class, 'requestRightToBeForgotten']);

        // Data portability
        Route::post('/data-portability', [GdprController::class, 'requestDataPortability']);
        Route::post('/export-data', [GdprController::class, 'exportData']);

        // Data processing activities
        Route::get('/data-processing-activities', [GdprController::class, 'getDataProcessingActivities']);

        // Consent history
        Route::get('/consent-history', [GdprController::class, 'getConsentHistory']);
    });

    // Employee Management Routes (to be implemented)
    Route::prefix('employees')->group(function () {
        Route::get('/', function () {
            return response()->json(['message' => 'Employee endpoints to be implemented']);
        });
        Route::post('/', function () {
            return response()->json(['message' => 'Employee endpoints to be implemented']);
        });
    });

    // Customer Management Routes (to be implemented)
    Route::prefix('customers')->group(function () {
        Route::get('/', function () {
            return response()->json(['message' => 'Customer endpoints to be implemented']);
        });
        Route::post('/', function () {
            return response()->json(['message' => 'Customer endpoints to be implemented']);
        });
    });

    // Supplier Management Routes (to be implemented)
    Route::prefix('suppliers')->group(function () {
        Route::get('/', function () {
            return response()->json(['message' => 'Supplier endpoints to be implemented']);
        });
        Route::post('/', function () {
            return response()->json(['message' => 'Supplier endpoints to be implemented']);
        });
    });

    // Data Processing Activities Routes (to be implemented)
    Route::prefix('data-processing-activities')->group(function () {
        Route::get('/', function () {
            return response()->json(['message' => 'Data processing activities endpoints to be implemented']);
        });
        Route::post('/', function () {
            return response()->json(['message' => 'Data processing activities endpoints to be implemented']);
        });
    });

    // Consent Records Routes (to be implemented)
    Route::prefix('consent-records')->group(function () {
        Route::get('/', function () {
            return response()->json(['message' => 'Consent records endpoints to be implemented']);
        });
        Route::post('/', function () {
            return response()->json(['message' => 'Consent records endpoints to be implemented']);
        });
    });
});

// GDPR Download Route (for exported data)
Route::get('/gdpr/download/{filename}', function ($filename) {
    // This would be implemented with proper authentication and authorization
    return response()->json(['message' => 'Download endpoint to be implemented']);
})->name('gdpr.download');
