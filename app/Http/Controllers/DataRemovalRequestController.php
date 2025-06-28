<?php

namespace App\Http\Controllers;

use App\Models\DataRemovalRequest;
use App\Models\Customer;
use App\Models\Mandator;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Notifications\DataRemovalRequestCreated;
use App\Notifications\DataRemovalRequestApproved;
use App\Notifications\DataRemovalRequestRejected;
use App\Notifications\DataRemovalRequestCompleted;
use App\Notifications\DataRemovalRequestCancelled;

class DataRemovalRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = DataRemovalRequest::with(['company', 'customer', 'mandator', 'requestedByUser'])
            ->forCompany($user->company_id);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->byPriority($request->priority);
        }

        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('request_number', 'like', "%{$search}%")
                  ->orWhere('reason_for_removal', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($customerQuery) use ($search) {
                      $customerQuery->where('first_name', 'like', "%{$search}%")
                                   ->orWhere('last_name', 'like', "%{$search}%")
                                   ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('mandator', function ($mandatorQuery) use ($search) {
                      $mandatorQuery->where('first_name', 'like', "%{$search}%")
                                   ->orWhere('last_name', 'like', "%{$search}%")
                                   ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Apply date filters
        if ($request->filled('date_from')) {
            $query->where('request_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('request_date', '<=', $request->date_to);
        }

        // Show overdue and due soon by default
        if (!$request->filled('status') && !$request->filled('search')) {
            $query->where(function ($q) {
                $q->overdue()->orWhere(function ($subQ) {
                    $subQ->dueSoon()->where('priority', '!=', 'low');
                });
            });
        }

        $requests = $query->latest('request_date')->paginate(20);

        // Get statistics
        $stats = [
            'total' => DataRemovalRequest::forCompany($user->company_id)->count(),
            'pending' => DataRemovalRequest::forCompany($user->company_id)->pending()->count(),
            'overdue' => DataRemovalRequest::forCompany($user->company_id)->overdue()->count(),
            'due_soon' => DataRemovalRequest::forCompany($user->company_id)->dueSoon()->count(),
            'completed' => DataRemovalRequest::forCompany($user->company_id)->completed()->count(),
        ];

        return view('data-removal-requests.index', compact('requests', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        $company = $user->company;

        // Get customers who haven't already requested data removal
        $customers = Customer::where('company_id', $company->id)
            ->where('right_to_be_forgotten_requested', false)
            ->where('is_active', true)
            ->get();

        $mandators = Mandator::where('company_id', $company->id)
            ->where('is_active', true)
            ->get();

        return view('data-removal-requests.create', compact('company', 'customers', 'mandators'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'mandator_id' => 'nullable|exists:mandators,id',
            'request_type' => ['required', Rule::in(['customer_direct', 'mandator_request', 'legal_obligation', 'system_cleanup'])],
            'priority' => ['required', Rule::in(['low', 'medium', 'high', 'urgent'])],
            'reason_for_removal' => 'required|string|max:1000',
            'data_categories_to_remove' => 'nullable|array',
            'data_categories_to_remove.*' => 'string',
            'retention_justification' => 'nullable|string|max:1000',
            'legal_basis_for_retention' => 'nullable|string|max:500',
            'due_date' => 'nullable|date|after:today',
            'identity_verified' => 'boolean',
            'verification_method' => 'nullable|string|max:200',
            'verification_notes' => 'nullable|string|max:500',
            'notify_third_parties' => 'boolean',
            'third_party_notification_details' => 'nullable|string|max:500',
        ]);

        // Ensure either customer_id or mandator_id is provided
        if (!$validated['customer_id'] && !$validated['mandator_id']) {
            return back()->withErrors(['customer_id' => 'Either a customer or mandator must be selected.']);
        }

        // Verify customer belongs to user's company
        if ($validated['customer_id']) {
            $customer = Customer::find($validated['customer_id']);
            if ($customer->company_id !== $user->company_id) {
                abort(403, 'Access denied.');
            }
        }

        // Verify mandator belongs to user's company
        if ($validated['mandator_id']) {
            $mandator = Mandator::find($validated['mandator_id']);
            if ($mandator->company_id !== $user->company_id) {
                abort(403, 'Access denied.');
            }
        }

        $dataRemovalRequest = DataRemovalRequest::create([
            'company_id' => $user->company_id,
            'customer_id' => $validated['customer_id'],
            'mandator_id' => $validated['mandator_id'],
            'requested_by_user_id' => $user->id,
            'request_type' => $validated['request_type'],
            'priority' => $validated['priority'],
            'reason_for_removal' => $validated['reason_for_removal'],
            'data_categories_to_remove' => $validated['data_categories_to_remove'],
            'retention_justification' => $validated['retention_justification'],
            'legal_basis_for_retention' => $validated['legal_basis_for_retention'],
            'due_date' => $validated['due_date'],
            'identity_verified' => $validated['identity_verified'] ?? false,
            'verification_method' => $validated['verification_method'],
            'verification_notes' => $validated['verification_notes'],
            'notify_third_parties' => $validated['notify_third_parties'] ?? false,
            'third_party_notification_details' => $validated['third_party_notification_details'],
        ]);
        $dataRemovalRequest->logAction('created', $user->id);

        // Notify DPO or fallback to company admin
        $dpo = $user->company->data_protection_officer ?? null;
        if ($dpo) {
            $dpo->notify(new DataRemovalRequestCreated($dataRemovalRequest));
        } else {
            // Fallback: notify the user who created the request
            $user->notify(new DataRemovalRequestCreated($dataRemovalRequest));
        }

        return redirect()->route('data-removal-requests.show', $dataRemovalRequest)
            ->with('success', 'Data removal request created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(DataRemovalRequest $dataRemovalRequest)
    {
        $user = Auth::user();

        if ($dataRemovalRequest->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        $dataRemovalRequest->load([
            'company', 'customer', 'mandator',
            'requestedByUser', 'reviewedByUser', 'completedByUser',
            'documents', 'auditLogs.user'
        ]);

        return view('data-removal-requests.show', compact('dataRemovalRequest'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DataRemovalRequest $dataRemovalRequest)
    {
        $user = Auth::user();

        if ($dataRemovalRequest->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        if ($dataRemovalRequest->isCompleted()) {
            return redirect()->route('data-removal-requests.show', $dataRemovalRequest)
                ->with('error', 'Completed requests cannot be edited.');
        }

        $company = $user->company;
        $customers = Customer::where('company_id', $company->id)->get();
        $mandators = Mandator::where('company_id', $company->id)->get();

        return view('data-removal-requests.edit', compact('dataRemovalRequest', 'company', 'customers', 'mandators'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DataRemovalRequest $dataRemovalRequest)
    {
        $user = Auth::user();

        if ($dataRemovalRequest->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        if ($dataRemovalRequest->isCompleted()) {
            return redirect()->route('data-removal-requests.show', $dataRemovalRequest)
                ->with('error', 'Completed requests cannot be updated.');
        }

        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'mandator_id' => 'nullable|exists:mandators,id',
            'request_type' => ['required', Rule::in(['customer_direct', 'mandator_request', 'legal_obligation', 'system_cleanup'])],
            'priority' => ['required', Rule::in(['low', 'medium', 'high', 'urgent'])],
            'reason_for_removal' => 'required|string|max:1000',
            'data_categories_to_remove' => 'nullable|array',
            'data_categories_to_remove.*' => 'string',
            'retention_justification' => 'nullable|string|max:1000',
            'legal_basis_for_retention' => 'nullable|string|max:500',
            'due_date' => 'nullable|date',
            'identity_verified' => 'boolean',
            'verification_method' => 'nullable|string|max:200',
            'verification_notes' => 'nullable|string|max:500',
            'notify_third_parties' => 'boolean',
            'third_party_notification_details' => 'nullable|string|max:500',
        ]);

        $dataRemovalRequest->update($validated);

        return redirect()->route('data-removal-requests.show', $dataRemovalRequest)
            ->with('success', 'Data removal request updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DataRemovalRequest $dataRemovalRequest)
    {
        $user = Auth::user();

        if ($dataRemovalRequest->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        if ($dataRemovalRequest->isCompleted()) {
            return redirect()->route('data-removal-requests.show', $dataRemovalRequest)
                ->with('error', 'Completed requests cannot be deleted.');
        }

        $dataRemovalRequest->delete();

        return redirect()->route('data-removal-requests.index')
            ->with('success', 'Data removal request deleted successfully.');
    }

    /**
     * Mark request as in review.
     */
    public function markInReview(DataRemovalRequest $dataRemovalRequest)
    {
        $user = Auth::user();

        if ($dataRemovalRequest->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        if (!$dataRemovalRequest->requiresReview()) {
            return back()->with('error', 'Request cannot be marked as in review.');
        }

        $dataRemovalRequest->markAsInReview($user);

        return back()->with('success', 'Request marked as in review.');
    }

    /**
     * Approve the request.
     */
    public function approve(Request $request, DataRemovalRequest $dataRemovalRequest)
    {
        $user = Auth::user();

        if ($dataRemovalRequest->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        $validated = $request->validate([
            'review_notes' => 'nullable|string|max:1000',
        ]);

        $dataRemovalRequest->approve($user, $validated['review_notes'] ?? null);
        $dataRemovalRequest->logAction('approved', $user->id, $validated['review_notes'] ?? null);

        // Notify DPO or fallback
        $dpo = $user->company->data_protection_officer ?? null;
        if ($dpo) {
            $dpo->notify(new DataRemovalRequestApproved($dataRemovalRequest));
        } else {
            $user->notify(new DataRemovalRequestApproved($dataRemovalRequest));
        }

        return back()->with('success', 'Request approved successfully.');
    }

    /**
     * Reject the request.
     */
    public function reject(Request $request, DataRemovalRequest $dataRemovalRequest)
    {
        $user = Auth::user();

        if ($dataRemovalRequest->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $dataRemovalRequest->reject($user, $validated['rejection_reason']);
        $dataRemovalRequest->logAction('rejected', $user->id, $validated['rejection_reason']);

        // Notify DPO or fallback
        $dpo = $user->company->data_protection_officer ?? null;
        if ($dpo) {
            $dpo->notify(new DataRemovalRequestRejected($dataRemovalRequest));
        } else {
            $user->notify(new DataRemovalRequestRejected($dataRemovalRequest));
        }

        return back()->with('success', 'Request rejected successfully.');
    }

    /**
     * Complete the request.
     */
    public function complete(Request $request, DataRemovalRequest $dataRemovalRequest)
    {
        $user = Auth::user();

        if ($dataRemovalRequest->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        $validated = $request->validate([
            'data_removal_method' => 'required|string|max:200',
            'completion_notes' => 'nullable|string|max:1000',
        ]);

        $dataRemovalRequest->complete($user, $validated['data_removal_method'], $validated['completion_notes'] ?? null);
        $dataRemovalRequest->logAction('completed', $user->id, $validated['completion_notes'] ?? null);

        // Notify DPO or fallback
        $dpo = $user->company->data_protection_officer ?? null;
        if ($dpo) {
            $dpo->notify(new DataRemovalRequestCompleted($dataRemovalRequest));
        } else {
            $user->notify(new DataRemovalRequestCompleted($dataRemovalRequest));
        }

        return back()->with('success', 'Data removal request completed successfully.');
    }

    /**
     * Cancel the request.
     */
    public function cancel(Request $request, DataRemovalRequest $dataRemovalRequest)
    {
        $user = Auth::user();

        if ($dataRemovalRequest->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'nullable|string|max:1000',
        ]);

        $dataRemovalRequest->cancel($user, $validated['rejection_reason'] ?? null);
        $dataRemovalRequest->logAction('cancelled', $user->id, $validated['rejection_reason'] ?? null);

        // Notify DPO or fallback
        $dpo = $user->company->data_protection_officer ?? null;
        if ($dpo) {
            $dpo->notify(new DataRemovalRequestCancelled($dataRemovalRequest));
        } else {
            $user->notify(new DataRemovalRequestCancelled($dataRemovalRequest));
        }

        return back()->with('success', 'Request cancelled successfully.');
    }

    /**
     * Dashboard for data removal requests.
     */
    public function dashboard()
    {
        $user = Auth::user();

        $stats = [
            'total' => DataRemovalRequest::forCompany($user->company_id)->count(),
            'pending' => DataRemovalRequest::forCompany($user->company_id)->pending()->count(),
            'in_review' => DataRemovalRequest::forCompany($user->company_id)->inReview()->count(),
            'approved' => DataRemovalRequest::forCompany($user->company_id)->approved()->count(),
            'completed' => DataRemovalRequest::forCompany($user->company_id)->completed()->count(),
            'overdue' => DataRemovalRequest::forCompany($user->company_id)->overdue()->count(),
            'due_soon' => DataRemovalRequest::forCompany($user->company_id)->dueSoon()->count(),
        ];

        $recentRequests = DataRemovalRequest::with(['customer', 'mandator'])
            ->forCompany($user->company_id)
            ->latest('request_date')
            ->take(10)
            ->get();

        $urgentRequests = DataRemovalRequest::with(['customer', 'mandator'])
            ->forCompany($user->company_id)
            ->where(function ($query) {
                $query->where('priority', 'urgent')
                      ->orWhere(function ($subQuery) {
                          $subQuery->overdue();
                      });
            })
            ->latest('request_date')
            ->take(5)
            ->get();

        return view('data-removal-requests.dashboard', compact('stats', 'recentRequests', 'urgentRequests'));
    }

    public function uploadDocument(Request $request, DataRemovalRequest $dataRemovalRequest)
    {
        $user = Auth::user();
        if ($dataRemovalRequest->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }
        $validated = $request->validate([
            'file' => 'required|file|max:10240', // 10MB
            'description' => 'nullable|string|max:255',
        ]);
        $document = $dataRemovalRequest->addDocument($request->file('file'), $user->id, $validated['description'] ?? null);
        $dataRemovalRequest->logAction('file_uploaded', $user->id, $validated['description'] ?? null);
        return back()->with('success', 'File uploaded successfully.');
    }
}
