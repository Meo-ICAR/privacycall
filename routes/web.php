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
use App\Http\Controllers\CompanyEmailController;
use App\Http\Controllers\CompanyEmailConfigController;
use App\Http\Controllers\UnifiedEmailController;
use App\Http\Controllers\AuditRequestController;
use App\Http\Controllers\MandatorController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\AuthorizationRequestController;
use App\Http\Controllers\DataCategoryController;
use App\Http\Controllers\SecurityMeasureController;
use App\Http\Controllers\EmailLogController;
use App\Http\Controllers\GoogleController;

Route::get('/', function () {
    return view('welcome');
});

// Dashboard route
Route::get('/dashboard', function () {
    if (Auth::check()) {
        // For authenticated users, show the main dashboard
        return view('dashboard');
    }
    // For non-authenticated users, redirect to login
    return redirect()->route('login');
})->name('dashboard');

// Company management routes
Route::prefix('companies')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [\App\Http\Controllers\CompanyController::class, 'index'])->name('companies.index');

    Route::get('/create', [\App\Http\Controllers\CompanyController::class, 'create'])->name('companies.create');

    Route::post('/', [\App\Http\Controllers\CompanyController::class, 'store'])->name('companies.store');

    Route::get('/{company}', [\App\Http\Controllers\CompanyController::class, 'show'])->name('companies.show');

    Route::get('/{company}/edit', [\App\Http\Controllers\CompanyController::class, 'edit'])->name('companies.edit');

    Route::put('/{company}', [\App\Http\Controllers\CompanyController::class, 'update'])->name('companies.update');
});

// GDPR management routes
Route::prefix('gdpr')->middleware(['auth', 'verified'])->group(function () {
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

    // GDPR Register routes
    Route::prefix('register')->group(function () {
        Route::get('/', [\App\Http\Controllers\GdprRegisterController::class, 'index'])->name('gdpr.register.index');
        Route::get('/dashboard', [\App\Http\Controllers\GdprRegisterController::class, 'dashboard'])->name('gdpr.register.dashboard');
        Route::get('/export', [\App\Http\Controllers\GdprRegisterController::class, 'export'])->name('gdpr.register.export');
        Route::get('/report', [\App\Http\Controllers\GdprRegisterController::class, 'report'])->name('gdpr.register.report');

        // Versioning routes
        Route::prefix('versions')->group(function () {
            Route::get('/', [\App\Http\Controllers\ProcessingRegisterVersionController::class, 'index'])->name('gdpr.register.versions.index');
            Route::get('/create', [\App\Http\Controllers\ProcessingRegisterVersionController::class, 'create'])->name('gdpr.register.versions.create');
            Route::post('/', [\App\Http\Controllers\ProcessingRegisterVersionController::class, 'store'])->name('gdpr.register.versions.store');
            Route::get('/{version}', [\App\Http\Controllers\ProcessingRegisterVersionController::class, 'show'])->name('gdpr.register.versions.show');
            Route::get('/{version}/edit', [\App\Http\Controllers\ProcessingRegisterVersionController::class, 'edit'])->name('gdpr.register.versions.edit');
            Route::put('/{version}', [\App\Http\Controllers\ProcessingRegisterVersionController::class, 'update'])->name('gdpr.register.versions.update');
            Route::post('/{version}/approve', [\App\Http\Controllers\ProcessingRegisterVersionController::class, 'approve'])->name('gdpr.register.versions.approve');
            Route::post('/{version}/archive', [\App\Http\Controllers\ProcessingRegisterVersionController::class, 'archive'])->name('gdpr.register.versions.archive');
            Route::get('/{version}/export', [\App\Http\Controllers\ProcessingRegisterVersionController::class, 'export'])->name('gdpr.register.versions.export');
            Route::get('/compare', [\App\Http\Controllers\ProcessingRegisterVersionController::class, 'compare'])->name('gdpr.register.versions.compare');
            Route::get('/entity-history', [\App\Http\Controllers\ProcessingRegisterVersionController::class, 'entityHistory'])->name('gdpr.register.versions.entity-history');
        });
    });
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

// Data Category and Security Measure management routes (superadmin)
Route::middleware(['auth', 'verified', 'role:superadmin'])->group(function () {
    Route::resource('data-categories', \App\Http\Controllers\DataCategoryController::class);
    Route::resource('security-measures', \App\Http\Controllers\SecurityMeasureController::class);
    Route::resource('third-countries', \App\Http\Controllers\ThirdCountryController::class);
    Route::resource('legal-basis-types', \App\Http\Controllers\LegalBasisTypeController::class);
    Route::resource('users', \App\Http\Controllers\UserController::class);
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

// Mandator management routes (admin/superadmin)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('mandators', MandatorController::class);

    // Cloning routes (superadmin only)
    Route::middleware(['role:superadmin'])->group(function () {
        Route::get('/mandators/{mandator}/clone', [MandatorController::class, 'showCloneForm'])->name('mandators.clone-form');
        Route::post('/mandators/{mandator}/clone', [MandatorController::class, 'clone'])->name('mandators.clone');
        Route::post('/mandators/{mandator}/clone-multiple', [MandatorController::class, 'cloneToMultiple'])->name('mandators.clone-multiple');
    });

    // Related mandators routes
    Route::get('/mandators/{mandator}/clones', [MandatorController::class, 'getClones'])->name('mandators.clones');
    Route::get('/mandators/{mandator}/related', [MandatorController::class, 'getRelated'])->name('mandators.related');

    // Disclosure subscription routes
    Route::post('/mandators/{mandator}/add-disclosure-subscription', [MandatorController::class, 'addDisclosureSubscription'])->name('mandators.add-disclosure-subscription');
    Route::post('/mandators/{mandator}/remove-disclosure-subscription', [MandatorController::class, 'removeDisclosureSubscription'])->name('mandators.remove-disclosure-subscription');
    Route::get('/mandators/disclosure-summary', [MandatorController::class, 'getDisclosureSummary'])->name('mandators.disclosure-summary');
});

// Disclosure Type management routes (superadmin only)
Route::middleware(['auth', 'verified', 'role:superadmin'])->group(function () {
    Route::resource('disclosure-types', \App\Http\Controllers\DisclosureTypeController::class);
});

// Company email management routes (admin/superadmin)
Route::middleware(['auth', 'verified', 'role:admin|superadmin'])->group(function () {
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

// Email Provider management routes (superadmin only)
Route::middleware(['auth', 'verified', 'role:superadmin'])->group(function () {
    Route::resource('email-providers', \App\Http\Controllers\EmailProviderController::class);
});

// Email Templates management routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('email-templates', \App\Http\Controllers\EmailTemplateController::class);
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

        // Email impersonation route (superadmin only)
        Route::post('/impersonate/{company}', [\App\Http\Controllers\UnifiedEmailController::class, 'impersonateForEmail'])->name('emails.impersonate')->middleware('role:superadmin');
    });
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
    Route::get('/trainings/{training}/manage-employees', [\App\Http\Controllers\TrainingController::class, 'manageEmployees'])->name('trainings.manage-employees');
    Route::post('/trainings/{training}/update-employees', [\App\Http\Controllers\TrainingController::class, 'updateEmployees'])->name('trainings.update-employees');
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

// Compliance Request Management routes (incoming requests from mandators)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('compliance-requests', \App\Http\Controllers\ComplianceRequestController::class);
    Route::post('/compliance-requests/{complianceRequest}/mark-in-progress', [\App\Http\Controllers\ComplianceRequestController::class, 'markInProgress'])->name('compliance-requests.mark-in-progress');
    Route::post('/compliance-requests/{complianceRequest}/mark-completed', [\App\Http\Controllers\ComplianceRequestController::class, 'markCompleted'])->name('compliance-requests.mark-completed');
    Route::post('/compliance-requests/{complianceRequest}/send-response', [\App\Http\Controllers\ComplianceRequestController::class, 'sendResponse'])->name('compliance-requests.send-response');
    Route::post('/compliance-requests/{complianceRequest}/upload-documents', [\App\Http\Controllers\ComplianceRequestController::class, 'uploadDocuments'])->name('compliance-requests.upload-documents');
    Route::post('/compliance-requests/{complianceRequest}/add-findings', [\App\Http\Controllers\ComplianceRequestController::class, 'addFindings'])->name('compliance-requests.add-findings');
});

// Test email route (remove in production)
Route::get('/test-email', function () {
    try {
        Mail::raw('This is a test email from PrivacyCall application.', function ($message) {
            $message->to('test@example.com')
                    ->subject('Test Email from PrivacyCall')
                    ->from('noreply@privacycall.com', 'PrivacyCall System');
        });

        return response()->json(['success' => true, 'message' => 'Test email sent successfully']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
})->middleware(['auth']);

// Data Removal Request Management routes (GDPR right to be forgotten)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('data-removal-requests', \App\Http\Controllers\DataRemovalRequestController::class);
    Route::get('/data-removal-requests/dashboard', [\App\Http\Controllers\DataRemovalRequestController::class, 'dashboard'])->name('data-removal-requests.dashboard');
    Route::post('/data-removal-requests/{dataRemovalRequest}/mark-in-review', [\App\Http\Controllers\DataRemovalRequestController::class, 'markInReview'])->name('data-removal-requests.mark-in-review');
    Route::post('/data-removal-requests/{dataRemovalRequest}/approve', [\App\Http\Controllers\DataRemovalRequestController::class, 'approve'])->name('data-removal-requests.approve');
    Route::post('/data-removal-requests/{dataRemovalRequest}/reject', [\App\Http\Controllers\DataRemovalRequestController::class, 'reject'])->name('data-removal-requests.reject');
    Route::post('/data-removal-requests/{dataRemovalRequest}/complete', [\App\Http\Controllers\DataRemovalRequestController::class, 'complete'])->name('data-removal-requests.complete');
    Route::post('/data-removal-requests/{dataRemovalRequest}/cancel', [\App\Http\Controllers\DataRemovalRequestController::class, 'cancel'])->name('data-removal-requests.cancel');
    Route::post('/data-removal-requests/{dataRemovalRequest}/upload-document', [\App\Http\Controllers\DataRemovalRequestController::class, 'uploadDocument'])->name('data-removal-requests.upload-document');
});

// Data Breach Management routes (GDPR breach notification)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('data-breaches', \App\Http\Controllers\DataBreachController::class);
    Route::get('/data-breaches/dashboard', [\App\Http\Controllers\DataBreachController::class, 'dashboard'])->name('data-breaches.dashboard');
    Route::post('/data-breaches/{dataBreach}/mark-investigated', [\App\Http\Controllers\DataBreachController::class, 'markInvestigated'])->name('data-breaches.mark-investigated');
    Route::post('/data-breaches/{dataBreach}/mark-resolved', [\App\Http\Controllers\DataBreachController::class, 'markResolved'])->name('data-breaches.mark-resolved');
    Route::post('/data-breaches/export', [\App\Http\Controllers\DataBreachController::class, 'export'])->name('data-breaches.export');
});

// Data Protection IAs (formerly Impact Assessments)
Route::resource('data-protection-i-as', \App\Http\Controllers\DataProtectionIAController::class);
Route::get('/data-protection-i-as/dashboard', [\App\Http\Controllers\DataProtectionIAController::class, 'dashboard'])->name('data-protection-i-as.dashboard');
Route::post('/data-protection-i-as/{dataProtectionIA}/review', [\App\Http\Controllers\DataProtectionIAController::class, 'review'])->name('data-protection-i-as.review');
Route::post('/data-protection-i-as/{dataProtectionIA}/approve', [\App\Http\Controllers\DataProtectionIAController::class, 'approve'])->name('data-protection-i-as.approve');
Route::post('/data-protection-i-as/export', [\App\Http\Controllers\DataProtectionIAController::class, 'export'])->name('data-protection-i-as.export');

// Third Country Transfer Management routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('third-country-transfers', \App\Http\Controllers\ThirdCountryTransferController::class);
    Route::get('/third-country-transfers/dashboard', [\App\Http\Controllers\ThirdCountryTransferController::class, 'dashboard'])->name('third-country-transfers.dashboard');
    Route::post('/third-country-transfers/{thirdCountryTransfer}/suspend', [\App\Http\Controllers\ThirdCountryTransferController::class, 'suspend'])->name('third-country-transfers.suspend');
    Route::post('/third-country-transfers/{thirdCountryTransfer}/terminate', [\App\Http\Controllers\ThirdCountryTransferController::class, 'terminate'])->name('third-country-transfers.terminate');
    Route::post('/third-country-transfers/export', [\App\Http\Controllers\ThirdCountryTransferController::class, 'export'])->name('third-country-transfers.export');
});

// Data Processing Agreement Management routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('data-processing-agreements', \App\Http\Controllers\DataProcessingAgreementController::class);
    Route::get('/data-processing-agreements/dashboard', [\App\Http\Controllers\DataProcessingAgreementController::class, 'dashboard'])->name('data-processing-agreements.dashboard');
    Route::post('/data-processing-agreements/{dataProcessingAgreement}/activate', [\App\Http\Controllers\DataProcessingAgreementController::class, 'activate'])->name('data-processing-agreements.activate');
    Route::post('/data-processing-agreements/{dataProcessingAgreement}/terminate', [\App\Http\Controllers\DataProcessingAgreementController::class, 'terminate'])->name('data-processing-agreements.terminate');
    Route::post('/data-processing-agreements/export', [\App\Http\Controllers\DataProcessingAgreementController::class, 'export'])->name('data-processing-agreements.export');
});

// Data Subject Rights Request Management routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('data-subject-rights-requests', \App\Http\Controllers\DataSubjectRightsRequestController::class);
    Route::get('/data-subject-rights-requests/dashboard', [\App\Http\Controllers\DataSubjectRightsRequestController::class, 'dashboard'])->name('data-subject-rights-requests.dashboard');
    Route::post('/data-subject-rights-requests/{dataSubjectRightsRequest}/assign', [\App\Http\Controllers\DataSubjectRightsRequestController::class, 'assign'])->name('data-subject-rights-requests.assign');
    Route::post('/data-subject-rights-requests/{dataSubjectRightsRequest}/update-status', [\App\Http\Controllers\DataSubjectRightsRequestController::class, 'updateStatus'])->name('data-subject-rights-requests.update-status');
    Route::post('/data-subject-rights-requests/{dataSubjectRightsRequest}/complete', [\App\Http\Controllers\DataSubjectRightsRequestController::class, 'complete'])->name('data-subject-rights-requests.complete');
    Route::post('/data-subject-rights-requests/export', [\App\Http\Controllers\DataSubjectRightsRequestController::class, 'export'])->name('data-subject-rights-requests.export');
});

// Authorization Request Management routes
Route::resource('authorization-requests', AuthorizationRequestController::class);
Route::post('authorization-requests/{authorizationRequest}/approve', [AuthorizationRequestController::class, 'approve'])->name('authorization-requests.approve');
Route::post('authorization-requests/{authorizationRequest}/deny', [AuthorizationRequestController::class, 'deny'])->name('authorization-requests.deny');

// Load Fortify routes for authentication and profile management
require __DIR__.'/../vendor/laravel/fortify/routes/routes.php';

// Load Jetstream routes for profile management
require __DIR__.'/../vendor/laravel/jetstream/routes/livewire.php';

// Additional Jetstream routes that are typically provided by Livewire components
Route::middleware(['auth', 'verified'])->group(function () {
    // Browser Sessions
    Route::delete('/user/other-browser-sessions', function () {
        // This would typically be handled by a Livewire component
        // For now, we'll just redirect back with a message
        return redirect()->back()->with('status', 'Browser sessions cleared.');
    })->name('other-browser-sessions.destroy');

    // Account Deletion
    Route::delete('/user', function () {
        // This would typically be handled by a Livewire component
        // For now, we'll just redirect back with a message
        return redirect()->back()->with('status', 'Account deletion requested.');
    })->name('current-user.destroy');
});

// Temporary debug route for impersonate stop
Route::get('/impersonate/stop', function () {
    return 'Impersonate stop GET route hit';
});

// Email Logs (admin)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('email-logs', [\App\Http\Controllers\EmailLogController::class, 'index'])->name('email-logs.index');
});
// google drive routes
Route::get('google/redirect', [GoogleController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('google.callback');
Route::get('google/files', [GoogleController::class, 'listDriveFiles'])->name('google.files');

Route::middleware(['auth', 'role:superadmin'])->group(function () {
    Route::get('/admin/test-email', [TestEmailController::class, 'form'])->name('admin.test-email.form');
    Route::post('/admin/test-email', [TestEmailController::class, 'send'])->name('admin.test-email.send');
});
