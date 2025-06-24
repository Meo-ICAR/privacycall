<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\GdprController;

Route::get('/', function () {
    return view('welcome');
});

// Dashboard route
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

// Company management routes
Route::prefix('companies')->group(function () {
    Route::get('/', function () {
        return view('companies.index');
    })->name('companies.index');

    Route::get('/create', function () {
        return view('companies.create');
    })->name('companies.create');

    Route::get('/{id}', function ($id) {
        return view('companies.show', compact('id'));
    })->name('companies.show');
});

// GDPR management routes
Route::prefix('gdpr')->group(function () {
    Route::get('/', function () {
        return view('gdpr.dashboard');
    })->name('gdpr.dashboard');

    Route::get('/consent-management', function () {
        return view('gdpr.consent');
    })->name('gdpr.consent');

    Route::get('/data-processing-activities', function () {
        return view('gdpr.activities');
    })->name('gdpr.activities');

    Route::get('/data-subject-rights', function () {
        return view('gdpr.rights');
    })->name('gdpr.rights');
});

// API documentation route
Route::get('/api-docs', function () {
    return view('api.documentation');
})->name('api.docs');
