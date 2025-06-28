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
use App\Http\Controllers\ImpersonationController;
use App\Http\Controllers\SupplierMailMergeController;
use App\Http\Controllers\RepresentativeController;
use App\Http\Controllers\CompanyEmailController;
use App\Http\Controllers\CompanyEmailConfigController;
use App\Http\Controllers\UnifiedEmailController;
use App\Http\Controllers\AuditRequestController;

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

    Route::get('/create', [\App\Http\Controllers\CompanyController::class, 'create'])->name('companies.create');

    Route::post('/', [\App\Http\Controllers\CompanyController::class, 'store'])->name('companies.store');

    Route::get('/{company}', [\App\Http\Controllers\CompanyController::class, 'show'])->name('companies.show');

    Route::get('/{company}/edit', [\App\Http\Controllers\CompanyController::class, 'edit'])->name('companies.edit');

    Route::put('/{company}', [\App\Http\Controllers\CompanyController::class, 'update'])->name('companies.update');
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
    Route::resource('holdings', HoldingController::class);
});

// Employee management routes (admin/superadmin)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::get('employees/create', [EmployeeController::class, 'create'])->name('employees.create');
    Route::post('employees', [EmployeeController::class, 'store'])->name('employees.store');
    Route::get('employees/{employee}', [EmployeeController::class, 'show'])->name('employees.show');
    Route::get('employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
    Route::put('employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::delete('employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
    Route::get('employees/export', [EmployeeController::class, 'export'])->name('employees.export');
    Route::post('employees/import', [EmployeeController::class, 'import'])->name('employees.import');
});

// Customer management routes (admin/superadmin)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('customers/create', [CustomerController::class, 'create'])->name('customers.create');
    Route::post('customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::get('customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
    Route::get('customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::put('customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    Route::delete('customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
    Route::get('customers/export', [CustomerController::class, 'export'])->name('customers.export');
    Route::post('customers/import', [CustomerController::class, 'import'])->name('customers.import');
});

// Representative management routes (admin/superadmin)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('representatives', RepresentativeController::class);

    // Cloning routes (superadmin only)
    Route::middleware(['role:superadmin'])->group(function () {
        Route::get('/representatives/{representative}/clone', [RepresentativeController::class, 'showCloneForm'])->name('representatives.clone-form');
        Route::post('/representatives/{representative}/clone', [RepresentativeController::class, 'clone'])->name('representatives.clone');
        Route::post('/representatives/{representative}/clone-multiple', [RepresentativeController::class, 'cloneToMultiple'])->name('representatives.clone-multiple');
    });

    // Related representatives routes
    Route::get('/representatives/{representative}/clones', [RepresentativeController::class, 'getClones'])->name('representatives.clones');
    Route::get('/representatives/{representative}/related', [RepresentativeController::class, 'getRelated'])->name('representatives.related');

    // Disclosure subscription routes
    Route::post('/representatives/{representative}/add-disclosure-subscription', [RepresentativeController::class, 'addDisclosureSubscription'])->name('representatives.add-disclosure-subscription');
    Route::post('/representatives/{representative}/remove-disclosure-subscription', [RepresentativeController::class, 'removeDisclosureSubscription'])->name('representatives.remove-disclosure-subscription');
    Route::get('/representatives/disclosure-summary', [RepresentativeController::class, 'getDisclosureSummary'])->name('representatives.disclosure-summary');
});

// Company email management routes (admin/superadmin)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('companies/{company}/emails')->group(function () {
        Route::get('/', [\App\Http\Controllers\CompanyEmailController::class, 'index'])->name('companies.emails.index');
        Route::get('/create', [\App\Http\Controllers\CompanyEmailController::class, 'create'])->name('companies.emails.create');
        Route::post('/', [\App\Http\Controllers\CompanyEmailController::class, 'store'])->name('companies.emails.store');
        Route::get('/{email}', [\App\Http\Controllers\CompanyEmailController::class, 'show'])->name('companies.emails.show');
        Route::get('/{email}/reply', [\App\Http\Controllers\CompanyEmailController::class, 'reply'])->name('companies.emails.reply');
        Route::post('/{email}/reply', [\App\Http\Controllers\CompanyEmailController::class, 'sendReply'])->name('companies.emails.send-reply');
        Route::put('/{email}', [\App\Http\Controllers\CompanyEmailController::class, 'update'])->name('companies.emails.update');
        Route::delete('/{email}', [\App\Http\Controllers\CompanyEmailController::class, 'destroy'])->name('companies.emails.destroy');
        Route::post('/fetch', [\App\Http\Controllers\CompanyEmailController::class, 'fetchEmails'])->name('companies.emails.fetch');
        Route::get('/stats', [\App\Http\Controllers\CompanyEmailController::class, 'stats'])->name('companies.emails.stats');
    });
});

// Company email configuration routes (admin/superadmin)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('companies/{company}/email-config')->group(function () {
        Route::get('/', [\App\Http\Controllers\CompanyEmailConfigController::class, 'show'])->name('companies.email-config.show');
        Route::put('/', [\App\Http\Controllers\CompanyEmailConfigController::class, 'update'])->name('companies.email-config.update');
        Route::delete('/', [\App\Http\Controllers\CompanyEmailConfigController::class, 'destroy'])->name('companies.email-config.destroy');
        Route::post('/test-connection', [\App\Http\Controllers\CompanyEmailConfigController::class, 'testConnection'])->name('companies.email-config.test');
        Route::get('/oauth-url', [\App\Http\Controllers\CompanyEmailConfigController::class, 'getOAuthUrl'])->name('companies.email-config.oauth-url');
        Route::get('/oauth-callback', [\App\Http\Controllers\CompanyEmailConfigController::class, 'oauthCallback'])->name('companies.email-config.oauth-callback');
    });

    // Email provider configuration routes
    Route::prefix('email-providers')->group(function () {
        Route::get('/{provider}/config', [\App\Http\Controllers\CompanyEmailConfigController::class, 'getProviderConfig'])->name('email-providers.config');
    });
});

// Unified Email Management routes (admin/superadmin)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('emails')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\UnifiedEmailController::class, 'dashboard'])->name('emails.dashboard');
        Route::get('/', [\App\Http\Controllers\UnifiedEmailController::class, 'index'])->name('emails.index');
        Route::get('/{id}/{type?}', [\App\Http\Controllers\UnifiedEmailController::class, 'show'])->name('emails.show');
        Route::post('/{email}/reply', [\App\Http\Controllers\UnifiedEmailController::class, 'reply'])->name('emails.reply');
        Route::post('/send', [\App\Http\Controllers\UnifiedEmailController::class, 'send'])->name('emails.send');
        Route::post('/quick-mail-merge', [\App\Http\Controllers\UnifiedEmailController::class, 'quickMailMerge'])->name('emails.quick-mail-merge');
        Route::post('/fetch', [\App\Http\Controllers\UnifiedEmailController::class, 'fetchEmails'])->name('emails.fetch');
        Route::get('/attachment/{id}/{type?}', [\App\Http\Controllers\UnifiedEmailController::class, 'downloadAttachment'])->name('emails.download-attachment');
    });
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

// Impersonation routes (superadmin only)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/impersonate/{user}', [\App\Http\Controllers\ImpersonationController::class, 'start'])->name('impersonate.start');
    Route::post('/impersonate/stop', [\App\Http\Controllers\ImpersonationController::class, 'stop'])->name('impersonate.stop');
});

// Supplier mail merge routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/supplier-mail-merge', [\App\Http\Controllers\SupplierMailMergeController::class, 'index'])->name('supplier-mail-merge.index');
    Route::post('/supplier-mail-merge/preview', [\App\Http\Controllers\SupplierMailMergeController::class, 'preview'])->name('supplier-mail-merge.preview');
    Route::post('/supplier-mail-merge/send', [\App\Http\Controllers\SupplierMailMergeController::class, 'send'])->name('supplier-mail-merge.send');
});

// Audit Request Management routes (admin/superadmin)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('audit-requests', \App\Http\Controllers\AuditRequestController::class);
    Route::post('/audit-requests/{auditRequest}/send-email', [\App\Http\Controllers\AuditRequestController::class, 'sendEmail'])->name('audit-requests.send-email');
    Route::post('/audit-requests/{auditRequest}/mark-in-progress', [\App\Http\Controllers\AuditRequestController::class, 'markInProgress'])->name('audit-requests.mark-in-progress');
    Route::post('/audit-requests/{auditRequest}/mark-completed', [\App\Http\Controllers\AuditRequestController::class, 'markCompleted'])->name('audit-requests.mark-completed');

    // Enhanced supplier audit features
    Route::post('/audit-requests/{auditRequest}/add-findings', [\App\Http\Controllers\AuditRequestController::class, 'addFindings'])->name('audit-requests.add-findings');
    Route::post('/audit-requests/{auditRequest}/add-corrective-actions', [\App\Http\Controllers\AuditRequestController::class, 'addCorrectiveActions'])->name('audit-requests.add-corrective-actions');
    Route::post('/audit-requests/{auditRequest}/mark-response-received', [\App\Http\Controllers\AuditRequestController::class, 'markResponseReceived'])->name('audit-requests.mark-response-received');
    Route::post('/audit-requests/{auditRequest}/upload-report', [\App\Http\Controllers\AuditRequestController::class, 'uploadReport'])->name('audit-requests.upload-report');
    Route::post('/audit-requests/{auditRequest}/schedule-next-audit', [\App\Http\Controllers\AuditRequestController::class, 'scheduleNextAudit'])->name('audit-requests.schedule-next-audit');

    // Supplier audit dashboard
    Route::get('/suppliers/{supplier}/audit-dashboard', [\App\Http\Controllers\AuditRequestController::class, 'supplierDashboard'])->name('suppliers.audit-dashboard');
});
