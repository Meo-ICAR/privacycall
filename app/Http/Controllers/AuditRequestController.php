<?php

namespace App\Http\Controllers;

use App\Models\AuditRequest;
use App\Models\Company;
use App\Models\Supplier;
use App\Models\User;
use App\Models\EmailTemplate;
use App\Services\EmailIntegrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AuditRequestController extends Controller
{
    protected $emailService;

    public function __construct(EmailIntegrationService $emailService)
    {
        $this->emailService = $emailService;
    }

    /**
     * Display a listing of audit requests.
     */
    public function index(Request $request, Company $company = null)
    {
        $user = Auth::user();
        $company = $company ?? $user->company;

        $query = AuditRequest::where('company_id', $company->id)
            ->with(['user', 'supplier', 'auditor']);

        // Apply filters
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        if ($request->filled('priority')) {
            $query->byPriority($request->priority);
        }

        if ($request->filled('risk_level')) {
            $query->byRiskLevel($request->risk_level);
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('auditor_id')) {
            $query->where('auditor_assigned', $request->auditor_id);
        }

        // Apply search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $auditRequests = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get statistics
        $stats = [
            'total' => AuditRequest::where('company_id', $company->id)->count(),
            'pending' => AuditRequest::where('company_id', $company->id)->pending()->count(),
            'in_progress' => AuditRequest::where('company_id', $company->id)->inProgress()->count(),
            'overdue' => AuditRequest::where('company_id', $company->id)->overdue()->count(),
            'upcoming' => AuditRequest::where('company_id', $company->id)->upcoming()->count(),
            'high_risk' => AuditRequest::where('company_id', $company->id)->highRisk()->count(),
            'requires_follow_up' => AuditRequest::where('company_id', $company->id)->requiresFollowUp()->count(),
        ];

        $suppliers = Supplier::where('company_id', $company->id)->get();
        $auditors = User::where('company_id', $company->id)
            ->role(['admin', 'mandator'])
            ->get();

        return view('audit-requests.index', compact('auditRequests', 'stats', 'suppliers', 'auditors', 'company'));
    }

    /**
     * Show the form for creating a new audit request.
     */
    public function create(Company $company = null)
    {
        $user = Auth::user();
        $company = $company ?? $user->company;

        $suppliers = Supplier::where('company_id', $company->id)->get();
        $auditors = User::where('company_id', $company->id)
            ->role(['admin', 'mandator'])
            ->get();
        $templates = EmailTemplate::where('company_id', $company->id)
            ->where('category', 'audit')
            ->get();

        return view('audit-requests.create', compact('suppliers', 'auditors', 'templates', 'company'));
    }

    /**
     * Store a newly created audit request.
     */
    public function store(Request $request, Company $company = null)
    {
        $user = Auth::user();
        $company = $company ?? $user->company;

        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'audit_type' => 'required|in:compliance,security,gdpr,financial,operational',
            'audit_scope' => 'required|in:full,partial,specific_area',
            'priority' => 'required|in:low,normal,high,urgent',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
            'requested_documents' => 'nullable|array',
            'requested_documents.*' => 'string|max:255',
            'requested_deadline' => 'nullable|date|after:today',
            'scheduled_date' => 'nullable|date|after:today',
            'scheduled_time' => 'nullable|date_format:H:i',
            'meeting_type' => 'nullable|in:call,visit,video_conference',
            'meeting_link' => 'nullable|url',
            'meeting_location' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'auditor_assigned' => 'nullable|exists:users,id',
            'audit_frequency' => 'nullable|in:monthly,quarterly,semi_annual,annual,biennial',
            'next_audit_date' => 'nullable|date|after:today',
            'audit_cost' => 'nullable|numeric|min:0',
            'audit_duration_hours' => 'nullable|numeric|min:0',
            'supplier_response_deadline' => 'nullable|date|after:today',
            'certification_status' => 'nullable|string|max:255',
            'certification_expiry_date' => 'nullable|date',
            'send_email' => 'boolean',
        ]);

        try {
            // Create audit request
            $auditRequest = AuditRequest::create([
                'company_id' => $company->id,
                'user_id' => $user->id,
                'supplier_id' => $request->supplier_id,
                'audit_type' => $request->audit_type,
                'audit_scope' => $request->audit_scope,
                'priority' => $request->priority,
                'subject' => $request->subject,
                'message' => $request->message,
                'requested_documents' => $request->requested_documents,
                'requested_deadline' => $request->requested_deadline,
                'scheduled_date' => $request->scheduled_date,
                'scheduled_time' => $request->scheduled_time,
                'meeting_type' => $request->meeting_type,
                'meeting_link' => $request->meeting_link,
                'meeting_location' => $request->meeting_location,
                'notes' => $request->notes,
                'auditor_assigned' => $request->auditor_assigned,
                'audit_frequency' => $request->audit_frequency,
                'next_audit_date' => $request->next_audit_date,
                'audit_cost' => $request->audit_cost,
                'audit_duration_hours' => $request->audit_duration_hours,
                'supplier_response_deadline' => $request->supplier_response_deadline,
                'certification_status' => $request->certification_status,
                'certification_expiry_date' => $request->certification_expiry_date,
            ]);

            // Send email if requested
            if ($request->boolean('send_email')) {
                $this->sendAuditEmail($auditRequest);
            }

            return redirect()->route('audit-requests.show', $auditRequest)
                ->with('success', 'Audit request created successfully!');

        } catch (\Exception $e) {
            Log::error('Error creating audit request: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to create audit request. Please try again.']);
        }
    }

    /**
     * Display the specified audit request.
     */
    public function show(AuditRequest $auditRequest)
    {
        $this->authorize('view', $auditRequest);

        $auditRequest->load(['user', 'supplier', 'company', 'auditor']);

        // Get audit history for this supplier
        $auditHistory = $auditRequest->auditHistory()->with(['user', 'auditor'])->get();

        return view('audit-requests.show', compact('auditRequest', 'auditHistory'));
    }

    /**
     * Show the form for editing the specified audit request.
     */
    public function edit(AuditRequest $auditRequest)
    {
        $this->authorize('update', $auditRequest);

        $suppliers = Supplier::where('company_id', $auditRequest->company_id)->get();
        $auditors = User::where('company_id', $auditRequest->company_id)
            ->role(['admin', 'mandator'])
            ->get();

        return view('audit-requests.edit', compact('auditRequest', 'suppliers', 'auditors'));
    }

    /**
     * Update the specified audit request.
     */
    public function update(Request $request, AuditRequest $auditRequest)
    {
        $this->authorize('update', $auditRequest);

        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'audit_type' => 'required|in:compliance,security,gdpr,financial,operational',
            'audit_scope' => 'required|in:full,partial,specific_area',
            'priority' => 'required|in:low,normal,high,urgent',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
            'requested_documents' => 'nullable|array',
            'requested_documents.*' => 'string|max:255',
            'requested_deadline' => 'nullable|date',
            'scheduled_date' => 'nullable|date',
            'scheduled_time' => 'nullable|date_format:H:i',
            'meeting_type' => 'nullable|in:call,visit,video_conference',
            'meeting_link' => 'nullable|url',
            'meeting_location' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'auditor_assigned' => 'nullable|exists:users,id',
            'audit_frequency' => 'nullable|in:monthly,quarterly,semi_annual,annual,biennial',
            'next_audit_date' => 'nullable|date',
            'audit_cost' => 'nullable|numeric|min:0',
            'audit_duration_hours' => 'nullable|numeric|min:0',
            'supplier_response_deadline' => 'nullable|date',
            'certification_status' => 'nullable|string|max:255',
            'certification_expiry_date' => 'nullable|date',
        ]);

        try {
            $auditRequest->update($request->all());

            return redirect()->route('audit-requests.show', $auditRequest)
                ->with('success', 'Audit request updated successfully!');

        } catch (\Exception $e) {
            Log::error('Error updating audit request: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to update audit request. Please try again.']);
        }
    }

    /**
     * Remove the specified audit request.
     */
    public function destroy(AuditRequest $auditRequest)
    {
        $this->authorize('delete', $auditRequest);

        try {
            $auditRequest->delete();

            return redirect()->route('audit-requests.index')
                ->with('success', 'Audit request deleted successfully!');

        } catch (\Exception $e) {
            Log::error('Error deleting audit request: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to delete audit request. Please try again.']);
        }
    }

    /**
     * Send audit email to supplier.
     */
    public function sendEmail(AuditRequest $auditRequest)
    {
        $this->authorize('update', $auditRequest);

        try {
            $this->sendAuditEmail($auditRequest);

            return back()->with('success', 'Audit email sent successfully!');

        } catch (\Exception $e) {
            Log::error('Error sending audit email: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to send audit email. Please try again.']);
        }
    }

    /**
     * Mark audit as in progress.
     */
    public function markInProgress(AuditRequest $auditRequest)
    {
        $this->authorize('update', $auditRequest);

        $auditRequest->markAsInProgress();

        return back()->with('success', 'Audit marked as in progress!');
    }

    /**
     * Mark audit as completed.
     */
    public function markCompleted(AuditRequest $auditRequest)
    {
        $this->authorize('update', $auditRequest);

        $auditRequest->markAsCompleted();

        return back()->with('success', 'Audit marked as completed!');
    }

    /**
     * Add audit findings.
     */
    public function addFindings(Request $request, AuditRequest $auditRequest)
    {
        $this->authorize('update', $auditRequest);

        $request->validate([
            'findings' => 'required|array',
            'findings.*.description' => 'required|string',
            'findings.*.severity' => 'required|in:minor,major,critical',
            'findings.*.category' => 'required|string',
            'findings.*.recommendation' => 'nullable|string',
        ]);

        try {
            $findings = $request->findings;
            $auditRequest->update([
                'audit_findings' => $findings,
                'compliance_score' => $auditRequest->calculateComplianceScore(),
                'risk_level' => $auditRequest->determineRiskLevel(),
            ]);

            return back()->with('success', 'Audit findings added successfully!');

        } catch (\Exception $e) {
            Log::error('Error adding audit findings: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to add audit findings. Please try again.']);
        }
    }

    /**
     * Add corrective actions.
     */
    public function addCorrectiveActions(Request $request, AuditRequest $auditRequest)
    {
        $this->authorize('update', $auditRequest);

        $request->validate([
            'corrective_actions' => 'required|array',
            'corrective_actions.*.action' => 'required|string',
            'corrective_actions.*.responsible_party' => 'required|string',
            'corrective_actions.*.deadline' => 'required|date',
            'corrective_actions.*.status' => 'required|in:pending,in_progress,completed',
        ]);

        try {
            $auditRequest->update([
                'corrective_actions' => $request->corrective_actions,
            ]);

            return back()->with('success', 'Corrective actions added successfully!');

        } catch (\Exception $e) {
            Log::error('Error adding corrective actions: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to add corrective actions. Please try again.']);
        }
    }

    /**
     * Mark supplier response as received.
     */
    public function markResponseReceived(AuditRequest $auditRequest)
    {
        $this->authorize('update', $auditRequest);

        $auditRequest->update([
            'supplier_response_received' => true,
        ]);

        return back()->with('success', 'Supplier response marked as received!');
    }

    /**
     * Upload audit report.
     */
    public function uploadReport(Request $request, AuditRequest $auditRequest)
    {
        $this->authorize('update', $auditRequest);

        $request->validate([
            'audit_report' => 'required|file|mimes:pdf,doc,docx|max:10240',
        ]);

        try {
            $file = $request->file('audit_report');
            $filename = 'audit_reports/' . uniqid('audit_report_') . '.' . $file->getClientOriginalExtension();

            Storage::disk('public')->put($filename, file_get_contents($file));

            $auditRequest->update([
                'audit_report_url' => Storage::url($filename),
            ]);

            return back()->with('success', 'Audit report uploaded successfully!');

        } catch (\Exception $e) {
            Log::error('Error uploading audit report: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to upload audit report. Please try again.']);
        }
    }

    /**
     * Schedule next audit.
     */
    public function scheduleNextAudit(Request $request, AuditRequest $auditRequest)
    {
        $this->authorize('update', $auditRequest);

        $request->validate([
            'next_audit_date' => 'required|date|after:today',
            'audit_frequency' => 'required|in:monthly,quarterly,semi_annual,annual,biennial',
        ]);

        try {
            $auditRequest->update([
                'next_audit_date' => $request->next_audit_date,
                'audit_frequency' => $request->audit_frequency,
            ]);

            return back()->with('success', 'Next audit scheduled successfully!');

        } catch (\Exception $e) {
            Log::error('Error scheduling next audit: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to schedule next audit. Please try again.']);
        }
    }

    /**
     * Get supplier audit dashboard.
     */
    public function supplierDashboard(Supplier $supplier)
    {
        $user = Auth::user();

        if ($supplier->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        $audits = AuditRequest::where('supplier_id', $supplier->id)
            ->with(['user', 'auditor'])
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'total_audits' => $audits->count(),
            'completed_audits' => $audits->where('status', 'completed')->count(),
            'pending_audits' => $audits->where('status', 'pending')->count(),
            'in_progress_audits' => $audits->where('status', 'in_progress')->count(),
            'average_compliance_score' => $audits->where('compliance_score')->avg('compliance_score'),
            'high_risk_audits' => $audits->whereIn('risk_level', ['high', 'critical'])->count(),
        ];

        return view('audit-requests.supplier-dashboard', compact('supplier', 'audits', 'stats'));
    }

    /**
     * Send audit email to supplier.
     */
    protected function sendAuditEmail(AuditRequest $auditRequest)
    {
        $supplier = $auditRequest->supplier;

        if (!$supplier || !$supplier->email) {
            throw new \Exception('Supplier email not available');
        }

        // Prepare email data
        $emailData = [
            'to_email' => $supplier->email,
            'to_name' => $supplier->name,
            'subject' => $auditRequest->subject,
            'body' => $this->formatAuditEmailBody($auditRequest),
            'priority' => $auditRequest->priority,
        ];

        // Send email
        $sent = $this->emailService->sendEmail($emailData);

        if ($sent) {
            // Update audit status
            $auditRequest->markAsInProgress();
        }

        return $sent;
    }

    /**
     * Format audit email body with all relevant information.
     */
    protected function formatAuditEmailBody(AuditRequest $auditRequest): string
    {
        $body = $auditRequest->message . "\n\n";

        if (!empty($auditRequest->requested_documents)) {
            $body .= "**Requested Documents:**\n";
            foreach ($auditRequest->requested_documents as $document) {
                $body .= "- " . $document . "\n";
            }
            $body .= "\n";
        }

        if ($auditRequest->requested_deadline) {
            $body .= "**Document Submission Deadline:** " . $auditRequest->requested_deadline->format('F d, Y') . "\n\n";
        }

        if ($auditRequest->scheduled_date) {
            $body .= "**Scheduled Audit Date:** " . $auditRequest->scheduled_date->format('F d, Y');
            if ($auditRequest->scheduled_time) {
                $body .= " at " . $auditRequest->scheduled_time->format('H:i');
            }
            $body .= "\n";
        }

        if ($auditRequest->meeting_type) {
            $body .= "**Meeting Type:** " . ucfirst(str_replace('_', ' ', $auditRequest->meeting_type)) . "\n";
        }

        if ($auditRequest->meeting_location) {
            $body .= "**Meeting Location:** " . $auditRequest->meeting_location . "\n";
        }

        if ($auditRequest->meeting_link) {
            $body .= "**Meeting Link:** " . $auditRequest->meeting_link . "\n";
        }

        $body .= "\nPlease respond to this audit request within the specified timeframe.\n\n";
        $body .= "Best regards,\n";
        $body .= $auditRequest->user->name . "\n";
        $body .= $auditRequest->company->name;

        return $body;
    }
}
