<?php

namespace App\Http\Controllers;

use App\Models\DataSubjectRightsRequest;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DataSubjectRightsRequestController extends Controller
{
    /**
     * Display a listing of data subject rights requests.
     */
    public function index(Request $request): View
    {
        $company = Auth::user()->company;

        $requests = DataSubjectRightsRequest::where('company_id', $company->id)
            ->with(['dataSubject', 'assignedTo', 'createdBy'])
            ->latest()
            ->paginate(15);

        return view('data-subject-rights-requests.index', compact('requests'));
    }

    /**
     * Show the form for creating a new request.
     */
    public function create(): View
    {
        $company = Auth::user()->company;

        return view('data-subject-rights-requests.create', compact('company'));
    }

    /**
     * Store a newly created request.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'data_subject_name' => 'required|string|max:255',
            'data_subject_email' => 'required|email',
            'data_subject_phone' => 'nullable|string',
            'data_subject_address' => 'nullable|string',
            'request_type' => 'required|in:access,rectification,erasure,portability,restriction,objection,withdrawal',
            'request_description' => 'required|string',
            'data_categories_requested' => 'required|array',
            'processing_activities_concerned' => 'required|array',
            'request_date' => 'required|date',
            'verification_method' => 'required|string',
            'verification_completed' => 'required|boolean',
            'verification_date' => 'nullable|date|after_or_equal:request_date',
            'verification_notes' => 'nullable|string',
            'identity_verified' => 'required|boolean',
            'request_legitimate' => 'required|boolean',
            'legitimacy_assessment' => 'nullable|string',
            'response_deadline' => 'required|date|after:request_date',
            'status' => 'required|in:received,under_review,requires_clarification,approved,rejected,completed',
            'assigned_to' => 'nullable|exists:users,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'notes' => 'nullable|string',
        ]);

        $company = Auth::user()->company;

        try {
            DB::beginTransaction();

            $rightsRequest = DataSubjectRightsRequest::create(array_merge($request->all(), [
                'company_id' => $company->id,
                'created_by' => Auth::id(),
            ]));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Richiesta diritti interessato creata con successo',
                'request' => $rightsRequest,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Errore durante la creazione: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified request.
     */
    public function show(DataSubjectRightsRequest $dataSubjectRightsRequest): View
    {
        $this->authorize('view', $dataSubjectRightsRequest);

        $dataSubjectRightsRequest->load([
            'dataSubject',
            'assignedTo',
            'createdBy',
            'company'
        ]);

        return view('data-subject-rights-requests.show', compact('dataSubjectRightsRequest'));
    }

    /**
     * Show the form for editing the specified request.
     */
    public function edit(DataSubjectRightsRequest $dataSubjectRightsRequest): View
    {
        $this->authorize('update', $dataSubjectRightsRequest);

        return view('data-subject-rights-requests.edit', compact('dataSubjectRightsRequest'));
    }

    /**
     * Update the specified request.
     */
    public function update(Request $request, DataSubjectRightsRequest $dataSubjectRightsRequest): JsonResponse
    {
        $this->authorize('update', $dataSubjectRightsRequest);

        $request->validate([
            'data_subject_name' => 'required|string|max:255',
            'data_subject_email' => 'required|email',
            'data_subject_phone' => 'nullable|string',
            'data_subject_address' => 'nullable|string',
            'request_type' => 'required|in:access,rectification,erasure,portability,restriction,objection,withdrawal',
            'request_description' => 'required|string',
            'data_categories_requested' => 'required|array',
            'processing_activities_concerned' => 'required|array',
            'request_date' => 'required|date',
            'verification_method' => 'required|string',
            'verification_completed' => 'required|boolean',
            'verification_date' => 'nullable|date|after_or_equal:request_date',
            'verification_notes' => 'nullable|string',
            'identity_verified' => 'required|boolean',
            'request_legitimate' => 'required|boolean',
            'legitimacy_assessment' => 'nullable|string',
            'response_deadline' => 'required|date|after:request_date',
            'status' => 'required|in:received,under_review,requires_clarification,approved,rejected,completed',
            'assigned_to' => 'nullable|exists:users,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'notes' => 'nullable|string',
        ]);

        $dataSubjectRightsRequest->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Richiesta aggiornata con successo',
            'request' => $dataSubjectRightsRequest,
        ]);
    }

    /**
     * Remove the specified request.
     */
    public function destroy(DataSubjectRightsRequest $dataSubjectRightsRequest): JsonResponse
    {
        $this->authorize('delete', $dataSubjectRightsRequest);

        $dataSubjectRightsRequest->delete();

        return response()->json([
            'success' => true,
            'message' => 'Richiesta eliminata con successo',
        ]);
    }

    /**
     * Assign request to user.
     */
    public function assign(Request $request, DataSubjectRightsRequest $dataSubjectRightsRequest): JsonResponse
    {
        $this->authorize('update', $dataSubjectRightsRequest);

        $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        $dataSubjectRightsRequest->update([
            'assigned_to' => $request->assigned_to,
            'assigned_date' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Richiesta assegnata con successo',
            'request' => $dataSubjectRightsRequest,
        ]);
    }

    /**
     * Update request status.
     */
    public function updateStatus(Request $request, DataSubjectRightsRequest $dataSubjectRightsRequest): JsonResponse
    {
        $this->authorize('update', $dataSubjectRightsRequest);

        $request->validate([
            'status' => 'required|in:received,under_review,requires_clarification,approved,rejected,completed',
            'status_notes' => 'nullable|string',
        ]);

        $dataSubjectRightsRequest->update([
            'status' => $request->status,
            'status_notes' => $request->status_notes,
            'status_updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Stato richiesta aggiornato con successo',
            'request' => $dataSubjectRightsRequest,
        ]);
    }

    /**
     * Complete request.
     */
    public function complete(Request $request, DataSubjectRightsRequest $dataSubjectRightsRequest): JsonResponse
    {
        $this->authorize('update', $dataSubjectRightsRequest);

        $request->validate([
            'response_summary' => 'required|string',
            'response_date' => 'required|date',
            'response_method' => 'required|string',
            'data_provided' => 'nullable|boolean',
            'data_provided_date' => 'nullable|date',
            'data_provided_method' => 'nullable|string',
        ]);

        $dataSubjectRightsRequest->update([
            'status' => 'completed',
            'response_summary' => $request->response_summary,
            'response_date' => $request->response_date,
            'response_method' => $request->response_method,
            'data_provided' => $request->data_provided,
            'data_provided_date' => $request->data_provided_date,
            'data_provided_method' => $request->data_provided_method,
            'completion_date' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Richiesta completata con successo',
            'request' => $dataSubjectRightsRequest,
        ]);
    }

    /**
     * Export requests.
     */
    public function export(Request $request): JsonResponse
    {
        $company = Auth::user()->company;

        $requests = DataSubjectRightsRequest::where('company_id', $company->id)
            ->with(['dataSubject', 'assignedTo', 'createdBy'])
            ->get();

        $exportData = [
            'company' => $company->name,
            'export_date' => now()->toISOString(),
            'requests' => $requests->map(function ($rightsRequest) {
                return [
                    'id' => $rightsRequest->id,
                    'data_subject_name' => $rightsRequest->data_subject_name,
                    'data_subject_email' => $rightsRequest->data_subject_email,
                    'request_type' => $rightsRequest->request_type,
                    'request_description' => $rightsRequest->request_description,
                    'data_categories_requested' => $rightsRequest->data_categories_requested,
                    'processing_activities_concerned' => $rightsRequest->processing_activities_concerned,
                    'request_date' => $rightsRequest->request_date->format('Y-m-d'),
                    'verification_completed' => $rightsRequest->verification_completed,
                    'verification_date' => $rightsRequest->verification_date?->format('Y-m-d'),
                    'identity_verified' => $rightsRequest->identity_verified,
                    'request_legitimate' => $rightsRequest->request_legitimate,
                    'response_deadline' => $rightsRequest->response_deadline->format('Y-m-d'),
                    'status' => $rightsRequest->status,
                    'priority' => $rightsRequest->priority,
                    'assigned_to' => $rightsRequest->assignedTo?->name,
                    'response_date' => $rightsRequest->response_date?->format('Y-m-d'),
                    'data_provided' => $rightsRequest->data_provided,
                    'data_provided_date' => $rightsRequest->data_provided_date?->format('Y-m-d'),
                    'created_by' => $rightsRequest->createdBy->name,
                    'created_at' => $rightsRequest->created_at->format('Y-m-d H:i:s'),
                ];
            }),
        ];

        return response()->json([
            'success' => true,
            'data' => $exportData,
        ]);
    }

    /**
     * Dashboard for requests.
     */
    public function dashboard(): View
    {
        $company = Auth::user()->company;

        $requests = DataSubjectRightsRequest::where('company_id', $company->id);

        $stats = [
            'total_requests' => $requests->count(),
            'received_requests' => $requests->where('status', 'received')->count(),
            'under_review_requests' => $requests->where('status', 'under_review')->count(),
            'requires_clarification_requests' => $requests->where('status', 'requires_clarification')->count(),
            'approved_requests' => $requests->where('status', 'approved')->count(),
            'rejected_requests' => $requests->where('status', 'rejected')->count(),
            'completed_requests' => $requests->where('status', 'completed')->count(),
            'requests_this_year' => $requests->where('request_date', '>=', now()->startOfYear())->count(),
            'requests_this_month' => $requests->where('request_date', '>=', now()->startOfMonth())->count(),
            'urgent_requests' => $requests->where('priority', 'urgent')->count(),
            'overdue_requests' => $requests->where('response_deadline', '<', now())->whereNotIn('status', ['completed', 'rejected'])->count(),
        ];

        $recentRequests = $requests->with(['dataSubject', 'assignedTo'])
            ->latest('request_date')
            ->limit(5)
            ->get();

        return view('data-subject-rights-requests.dashboard', compact('stats', 'recentRequests'));
    }
}
