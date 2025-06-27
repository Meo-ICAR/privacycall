<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\GdprController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DocumentTypeController;
use App\Http\Controllers\EmployerTypeController;
use App\Http\Controllers\CustomerTypeController;
use App\Http\Controllers\HoldingController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InspectionController;
use App\Http\Controllers\SupplierInspectionController;

Route::get('/', function () {
    return view('welcome');
});

// Dashboard route
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

// Company management routes
Route::prefix('companies')->group(function () {
    Route::get('/', [\App\Http\Controllers\CompanyController::class, 'index'])->name('companies.index');

    Route::get('/create', function () {
        return view('companies.create');
    })->name('companies.create');

    Route::post('/', [\App\Http\Controllers\CompanyController::class, 'store'])->name('companies.store');

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

// Roles and Permissions management (admin access)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/roles-permissions', [RolePermissionController::class, 'index'])->name('roles.permissions.index');
    Route::post('/roles-permissions/assign', [RolePermissionController::class, 'assign'])->name('roles.permissions.assign');
});

// Supplier management routes
Route::prefix('suppliers')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [SupplierController::class, 'index'])->name('suppliers.index');
    Route::get('/create', [SupplierController::class, 'create'])->name('suppliers.create');
    Route::post('/', [SupplierController::class, 'store'])->name('suppliers.store');
    Route::get('/{supplier}', [SupplierController::class, 'show'])->name('suppliers.show');
    Route::get('/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
    Route::put('/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
    Route::delete('/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');
    Route::get('/export', [SupplierController::class, 'export'])->name('suppliers.export');
    Route::post('/import', [SupplierController::class, 'import'])->name('suppliers.import');
});

// Document upload and delete routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');
});

// Document type management routes (admin/superadmin)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('document-types', DocumentTypeController::class)->except(['show']);
});

// Employer type management routes (admin/superadmin)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('employer-types', EmployerTypeController::class)->except(['show']);
});

// Customer type management routes (admin/superadmin)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('customer-types', CustomerTypeController::class)->except(['show']);
});

// Holding management routes (admin/superadmin)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('holdings', HoldingController::class)->except(['show']);
});

// Employee management routes (admin/superadmin)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::get('employees/export', [EmployeeController::class, 'export'])->name('employees.export');
    Route::post('employees/import', [EmployeeController::class, 'import'])->name('employees.import');
});

// Customer management routes (admin/superadmin)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('customers/export', [CustomerController::class, 'export'])->name('customers.export');
    Route::post('customers/import', [CustomerController::class, 'import'])->name('customers.import');
});

// Inspection management routes (admin/superadmin)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('inspections', InspectionController::class);
});

// Supplier inspection management routes (admin/superadmin)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('supplier-inspections', SupplierInspectionController::class);
});

// Consent records management routes (admin/superadmin)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('consent-records', \App\Http\Controllers\ConsentRecordController::class);
});

// Data processing activities management routes (admin/superadmin)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('data-processing-activities', \App\Http\Controllers\DataProcessingActivityController::class);
});

// Supplier types management routes (admin/superadmin)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('supplier-types', \App\Http\Controllers\SupplierTypeController::class)->except(['show']);
});

// Training management routes (admin/superadmin)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('trainings', \App\Http\Controllers\TrainingController::class);
    Route::resource('employee-training', \App\Http\Controllers\EmployeeTrainingController::class);
});
