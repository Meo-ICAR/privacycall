<?php

namespace App\Http\Controllers;

use App\Models\ComplianceRequest;
use App\Models\Company;
use App\Models\Mandator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ComplianceRequestController extends Controller
{
    /**
     * Display a listing of compliance requests.
     */
    public function index(Request $request, Company $company = null)
    {
        $user = Auth::user();
        $company = $company ?? $user->company;

        $query = ComplianceRequest::where('company_id', $company->id)
            ->with(['mandator', 'assignedUser']);

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

        if ($request->filled('mandator_id')) {
            $query->where('mandator_id', $request->mandator_id);
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        // Apply search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%")
                  ->orWhereHas('mandator', function ($sq) use ($search) {
                      $sq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $complianceRequests = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get statistics
        $stats = [
            'total' => ComplianceRequest::where('company_id', $company->id)->count(),
            'pending' => ComplianceRequest::where('company_id', $company->id)->pending()->count(),
            'in_progress' => ComplianceRequest::where('company_id', $company->id)->inProgress()->count(),
            'overdue' => ComplianceRequest::where('company_id', $company->id)->overdue()->count(),
            'upcoming' => ComplianceRequest::where('company_id', $company->id)->upcoming()->count(),
            'high_priority' => ComplianceRequest::where('company_id', $company->id)->highPriority()->count(),
            'requires_response' => ComplianceRequest::where('company_id', $company->id)->requiresResponse()->count(),
            'requires_documents' => ComplianceRequest::where('company_id', $company->id)->requiresDocuments()->count(),
        ];

        $mandators = Mandator::where('company_id', $company->id)->get();
        $assignedUsers = User::where('company_id', $company->id)
            ->role(['admin', 'manager', 'employee'])
            ->get();

        return view('compliance-requests.index', compact('complianceRequests', 'stats', 'mandators', 'assignedUsers', 'company'));
    }

    /**
     * Show the form for creating a new compliance request.
     */
    public function create(Company $company = null)
    {
        $user = Auth::user();
        $company = $company ?? $user->company;

        $mandators = Mandator::where('company_id', $company->id)->get();
        $assignedUsers = User::where('company_id', $company->id)
            ->role(['admin', 'manager', 'employee'])
            ->get();

        // Get mandator_id from query parameter for pre-selection
        $selectedMandatorId = request('mandator_id');

        return view('compliance-requests.create', compact('mandators', 'assignedUsers', 'company', 'selectedMandatorId'));
    }

    /**
     * Store a newly created compliance request.
     */
    public function store(Request $request, Company $company = null)
    {
        $user = Auth::user();
        $company = $company ?? $user->company;

        $request->validate([
            'mandator_id' => 'required|exists:mandators,id',
            'request_type' => 'required|in:compliance,security,gdpr,financial,operational,data_processing',
            'request_scope' => 'required|in:full,partial,specific_area,document_only',
            'priority' => 'required|in:low,normal,high,urgent',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
            'requested_documents' => 'nullable|array',
            'requested_documents.*' => 'string|max:255',
            'requested_deadline' => 'nullable|date|after:today',
            'scheduled_date' => 'nullable|date|after:today',
            'scheduled_time' => 'nullable|date_format:H:i',
            'meeting_type' => 'nullable|in:call,visit,video_conference,document_review',
            'meeting_link' => 'nullable|url',
            'meeting_location' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        try {
            $complianceRequest = ComplianceRequest::create([
                'company_id' => $company->id,
                'mandator_id' => $request->mandator_id,
                'request_type' => $request->request_type,
                'request_scope' => $request->request_scope,
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
                'assigned_to' => $request->assigned_to,
            ]);

            return redirect()->route('compliance-requests.show', $complianceRequest)
                ->with('success', 'Compliance request created successfully!');

        } catch (\Exception $e) {
            Log::error('Error creating compliance request: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to create compliance request. Please try again.']);
        }
    }

    /**
     * Display the specified compliance request.
     */
    public function show(ComplianceRequest $complianceRequest)
    {
        $this->authorize('view', $complianceRequest);

        $complianceRequest->load(['mandator', 'assignedUser', 'company']);

        return view('compliance-requests.show', compact('complianceRequest'));
    }

    /**
     * Show the form for editing the specified compliance request.
     */
    public function edit(ComplianceRequest $complianceRequest)
    {
        $this->authorize('update', $complianceRequest);

        $mandators = Mandator::where('company_id', $complianceRequest->company_id)->get();
        $assignedUsers = User::where('company_id', $complianceRequest->company_id)
            ->role(['admin', 'manager', 'employee'])
            ->get();

        return view('compliance-requests.edit', compact('complianceRequest', 'mandators', 'assignedUsers'));
    }

    /**
     * Update the specified compliance request.
     */
    public function update(Request $request, ComplianceRequest $complianceRequest)
    {
        $this->authorize('update', $complianceRequest);

        $request->validate([
            'mandator_id' => 'required|exists:mandators,id',
            'request_type' => 'required|in:compliance,security,gdpr,financial,operational,data_processing',
            'request_scope' => 'required|in:full,partial,specific_area,document_only',
            'priority' => 'required|in:low,normal,high,urgent',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
            'requested_documents' => 'nullable|array',
            'requested_documents.*' => 'string|max:255',
            'requested_deadline' => 'nullable|date',
            'scheduled_date' => 'nullable|date',
            'scheduled_time' => 'nullable|date_format:H:i',
            'meeting_type' => 'nullable|in:call,visit,video_conference,document_review',
            'meeting_link' => 'nullable|url',
            'meeting_location' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        try {
            $complianceRequest->update($request->all());

            return redirect()->route('compliance-requests.show', $complianceRequest)
                ->with('success', 'Compliance request updated successfully!');

        } catch (\Exception $e) {
            Log::error('Error updating compliance request: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to update compliance request. Please try again.']);
        }
    }

    /**
     * Remove the specified compliance request.
     */
    public function destroy(ComplianceRequest $complianceRequest)
    {
        $this->authorize('delete', $complianceRequest);

        try {
            $complianceRequest->delete();

            return redirect()->route('compliance-requests.index')
                ->with('success', 'Compliance request deleted successfully!');

        } catch (\Exception $e) {
            Log::error('Error deleting compliance request: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to delete compliance request. Please try again.']);
        }
    }

    /**
     * Mark compliance request as in progress.
     */
    public function markInProgress(ComplianceRequest $complianceRequest)
    {
        $this->authorize('update', $complianceRequest);

        try {
            $complianceRequest->markAsInProgress();

            return back()->with('success', 'Compliance request marked as in progress!');

        } catch (\Exception $e) {
            Log::error('Error marking compliance request as in progress: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to update status. Please try again.']);
        }
    }

    /**
     * Mark compliance request as completed.
     */
    public function markCompleted(ComplianceRequest $complianceRequest)
    {
        $this->authorize('update', $complianceRequest);

        try {
            $complianceRequest->markAsCompleted();

            return back()->with('success', 'Compliance request marked as completed!');

        } catch (\Exception $e) {
            Log::error('Error marking compliance request as completed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to update status. Please try again.']);
        }
    }

    /**
     * Send response to mandator.
     */
    public function sendResponse(Request $request, ComplianceRequest $complianceRequest)
    {
        $this->authorize('update', $complianceRequest);

        $request->validate([
            'response_message' => 'required|string|max:5000',
        ]);

        try {
            $complianceRequest->markResponseSent($request->response_message);

            return back()->with('success', 'Response sent to mandator successfully!');

        } catch (\Exception $e) {
            Log::error('Error sending response: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to send response. Please try again.']);
        }
    }

    /**
     * Upload documents for compliance request.
     */
    public function uploadDocuments(Request $request, ComplianceRequest $complianceRequest)
    {
        $this->authorize('update', $complianceRequest);

        $request->validate([
            'documents' => 'required|array',
            'documents.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,jpg,jpeg,png|max:10240',
        ]);

        try {
            $uploadedDocuments = [];

            foreach ($request->file('documents') as $document) {
                $path = $document->store('compliance-documents/' . $complianceRequest->id, 'public');
                $uploadedDocuments[] = [
                    'name' => $document->getClientOriginalName(),
                    'path' => $path,
                    'size' => $document->getSize(),
                    'type' => $document->getMimeType(),
                    'uploaded_at' => now(),
                ];
            }

            $complianceRequest->markDocumentsUploaded($uploadedDocuments);

            return back()->with('success', 'Documents uploaded successfully!');

        } catch (\Exception $e) {
            Log::error('Error uploading documents: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to upload documents. Please try again.']);
        }
    }

    /**
     * Add compliance findings.
     */
    public function addFindings(Request $request, ComplianceRequest $complianceRequest)
    {
        $this->authorize('update', $complianceRequest);

        $request->validate([
            'findings' => 'required|array',
            'findings.*.description' => 'required|string',
            'findings.*.severity' => 'required|in:minor,major,critical',
            'findings.*.category' => 'required|string',
            'findings.*.recommendation' => 'nullable|string',
        ]);

        try {
            $complianceRequest->update([
                'compliance_findings' => $request->findings,
                'compliance_score' => $this->calculateComplianceScore($request->findings),
                'risk_level' => $this->determineRiskLevel($request->findings),
            ]);

            return back()->with('success', 'Compliance findings added successfully!');

        } catch (\Exception $e) {
            Log::error('Error adding compliance findings: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to add findings. Please try again.']);
        }
    }

    /**
     * Calculate compliance score based on findings.
     */
    protected function calculateComplianceScore($findings)
    {
        if (empty($findings)) {
            return 100;
        }

        $totalFindings = count($findings);
        $criticalFindings = collect($findings)
            ->where('severity', 'critical')
            ->count();
        $majorFindings = collect($findings)
            ->where('severity', 'major')
            ->count();
        $minorFindings = collect($findings)
            ->where('severity', 'minor')
            ->count();

        // Scoring algorithm: Critical = -20, Major = -10, Minor = -5
        $score = 100 - ($criticalFindings * 20) - ($majorFindings * 10) - ($minorFindings * 5);

        return max(0, $score);
    }

    /**
     * Determine risk level based on findings.
     */
    protected function determineRiskLevel($findings)
    {
        $score = $this->calculateComplianceScore($findings);

        if ($score >= 90) return 'low';
        if ($score >= 70) return 'medium';
        if ($score >= 50) return 'high';
        return 'critical';
    }
}
