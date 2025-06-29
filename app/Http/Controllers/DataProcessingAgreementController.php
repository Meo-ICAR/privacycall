<?php

namespace App\Http\Controllers;

use App\Models\DataProcessingAgreement;
use App\Models\Supplier;
use App\Models\DataProcessingActivity;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DataProcessingAgreementController extends Controller
{
    /**
     * Display a listing of data processing agreements.
     */
    public function index(Request $request): View
    {
        $company = Auth::user()->company;

        $agreements = DataProcessingAgreement::where('company_id', $company->id)
            ->with(['supplier', 'dataProcessingActivity', 'createdBy'])
            ->latest()
            ->paginate(15);

        return view('data-processing-agreements.index', compact('agreements'));
    }

    /**
     * Show the form for creating a new agreement.
     */
    public function create(): View
    {
        $company = Auth::user()->company;

        $suppliers = Supplier::where('company_id', $company->id)
            ->where('is_data_processor', true)
            ->get();

        $activities = DataProcessingActivity::where('company_id', $company->id)
            ->where('has_data_processors', true)
            ->get();

        return view('data-processing-agreements.create', compact('company', 'suppliers', 'activities'));
    }

    /**
     * Store a newly created agreement.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'data_processing_activity_id' => 'required|exists:data_processing_activities,id',
            'agreement_title' => 'required|string|max:255',
            'agreement_date' => 'required|date',
            'effective_date' => 'required|date|after_or_equal:agreement_date',
            'expiry_date' => 'nullable|date|after:effective_date',
            'processing_purpose' => 'required|string',
            'data_categories_processed' => 'required|array',
            'data_subjects_affected' => 'required|array',
            'processing_duration' => 'required|string',
            'data_retention_period' => 'required|string',
            'security_measures' => 'required|array',
            'sub_processor_authorization' => 'required|boolean',
            'sub_processors_list' => 'nullable|array',
            'audit_rights' => 'required|boolean',
            'audit_frequency' => 'nullable|string',
            'breach_notification_period' => 'required|integer|min:1',
            'assistance_obligations' => 'required|array',
            'data_return_deletion' => 'required|boolean',
            'return_deletion_period' => 'nullable|integer|min:1',
            'liability_provisions' => 'required|string',
            'indemnification_clause' => 'required|boolean',
            'insurance_requirements' => 'nullable|string',
            'termination_clause' => 'required|string',
            'governing_law' => 'required|string',
            'dispute_resolution' => 'required|string',
            'status' => 'required|in:draft,active,expired,terminated',
            'notes' => 'nullable|string',
        ]);

        $company = Auth::user()->company;

        try {
            DB::beginTransaction();

            $agreement = DataProcessingAgreement::create(array_merge($request->all(), [
                'company_id' => $company->id,
                'created_by' => Auth::id(),
            ]));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Accordo di trattamento dati creato con successo',
                'agreement' => $agreement,
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
     * Display the specified agreement.
     */
    public function show(DataProcessingAgreement $dataProcessingAgreement): View
    {
        $this->authorize('view', $dataProcessingAgreement);

        $dataProcessingAgreement->load([
            'supplier',
            'dataProcessingActivity',
            'createdBy',
            'company'
        ]);

        return view('data-processing-agreements.show', compact('dataProcessingAgreement'));
    }

    /**
     * Show the form for editing the specified agreement.
     */
    public function edit(DataProcessingAgreement $dataProcessingAgreement): View
    {
        $this->authorize('update', $dataProcessingAgreement);

        $company = Auth::user()->company;

        $suppliers = Supplier::where('company_id', $company->id)
            ->where('is_data_processor', true)
            ->get();

        $activities = DataProcessingActivity::where('company_id', $company->id)
            ->where('has_data_processors', true)
            ->get();

        return view('data-processing-agreements.edit', compact('dataProcessingAgreement', 'suppliers', 'activities'));
    }

    /**
     * Update the specified agreement.
     */
    public function update(Request $request, DataProcessingAgreement $dataProcessingAgreement): JsonResponse
    {
        $this->authorize('update', $dataProcessingAgreement);

        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'data_processing_activity_id' => 'required|exists:data_processing_activities,id',
            'agreement_title' => 'required|string|max:255',
            'agreement_date' => 'required|date',
            'effective_date' => 'required|date|after_or_equal:agreement_date',
            'expiry_date' => 'nullable|date|after:effective_date',
            'processing_purpose' => 'required|string',
            'data_categories_processed' => 'required|array',
            'data_subjects_affected' => 'required|array',
            'processing_duration' => 'required|string',
            'data_retention_period' => 'required|string',
            'security_measures' => 'required|array',
            'sub_processor_authorization' => 'required|boolean',
            'sub_processors_list' => 'nullable|array',
            'audit_rights' => 'required|boolean',
            'audit_frequency' => 'nullable|string',
            'breach_notification_period' => 'required|integer|min:1',
            'assistance_obligations' => 'required|array',
            'data_return_deletion' => 'required|boolean',
            'return_deletion_period' => 'nullable|integer|min:1',
            'liability_provisions' => 'required|string',
            'indemnification_clause' => 'required|boolean',
            'insurance_requirements' => 'nullable|string',
            'termination_clause' => 'required|string',
            'governing_law' => 'required|string',
            'dispute_resolution' => 'required|string',
            'status' => 'required|in:draft,active,expired,terminated',
            'notes' => 'nullable|string',
        ]);

        $dataProcessingAgreement->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Accordo aggiornato con successo',
            'agreement' => $dataProcessingAgreement,
        ]);
    }

    /**
     * Remove the specified agreement.
     */
    public function destroy(DataProcessingAgreement $dataProcessingAgreement): JsonResponse
    {
        $this->authorize('delete', $dataProcessingAgreement);

        $dataProcessingAgreement->delete();

        return response()->json([
            'success' => true,
            'message' => 'Accordo eliminato con successo',
        ]);
    }

    /**
     * Activate agreement.
     */
    public function activate(Request $request, DataProcessingAgreement $dataProcessingAgreement): JsonResponse
    {
        $this->authorize('update', $dataProcessingAgreement);

        $dataProcessingAgreement->update([
            'status' => 'active',
            'activation_date' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Accordo attivato con successo',
            'agreement' => $dataProcessingAgreement,
        ]);
    }

    /**
     * Terminate agreement.
     */
    public function terminate(Request $request, DataProcessingAgreement $dataProcessingAgreement): JsonResponse
    {
        $this->authorize('update', $dataProcessingAgreement);

        $request->validate([
            'termination_reason' => 'required|string',
        ]);

        $dataProcessingAgreement->update([
            'status' => 'terminated',
            'termination_reason' => $request->termination_reason,
            'termination_date' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Accordo terminato con successo',
            'agreement' => $dataProcessingAgreement,
        ]);
    }

    /**
     * Export agreements.
     */
    public function export(Request $request): JsonResponse
    {
        $company = Auth::user()->company;

        $agreements = DataProcessingAgreement::where('company_id', $company->id)
            ->with(['supplier', 'dataProcessingActivity', 'createdBy'])
            ->get();

        $exportData = [
            'company' => $company->name,
            'export_date' => now()->toISOString(),
            'agreements' => $agreements->map(function ($agreement) {
                return [
                    'id' => $agreement->id,
                    'agreement_title' => $agreement->agreement_title,
                    'supplier' => $agreement->supplier->name,
                    'data_processing_activity' => $agreement->dataProcessingActivity->activity_name,
                    'agreement_date' => $agreement->agreement_date->format('Y-m-d'),
                    'effective_date' => $agreement->effective_date->format('Y-m-d'),
                    'expiry_date' => $agreement->expiry_date?->format('Y-m-d'),
                    'processing_purpose' => $agreement->processing_purpose,
                    'data_categories_processed' => $agreement->data_categories_processed,
                    'data_subjects_affected' => $agreement->data_subjects_affected,
                    'processing_duration' => $agreement->processing_duration,
                    'data_retention_period' => $agreement->data_retention_period,
                    'security_measures' => $agreement->security_measures,
                    'sub_processor_authorization' => $agreement->sub_processor_authorization,
                    'sub_processors_list' => $agreement->sub_processors_list,
                    'audit_rights' => $agreement->audit_rights,
                    'audit_frequency' => $agreement->audit_frequency,
                    'breach_notification_period' => $agreement->breach_notification_period,
                    'assistance_obligations' => $agreement->assistance_obligations,
                    'data_return_deletion' => $agreement->data_return_deletion,
                    'return_deletion_period' => $agreement->return_deletion_period,
                    'liability_provisions' => $agreement->liability_provisions,
                    'indemnification_clause' => $agreement->indemnification_clause,
                    'insurance_requirements' => $agreement->insurance_requirements,
                    'termination_clause' => $agreement->termination_clause,
                    'governing_law' => $agreement->governing_law,
                    'dispute_resolution' => $agreement->dispute_resolution,
                    'status' => $agreement->status,
                    'created_by' => $agreement->createdBy->name,
                    'created_at' => $agreement->created_at->format('Y-m-d H:i:s'),
                ];
            }),
        ];

        return response()->json([
            'success' => true,
            'data' => $exportData,
        ]);
    }

    /**
     * Dashboard for agreements.
     */
    public function dashboard(): View
    {
        $company = Auth::user()->company;

        $agreements = DataProcessingAgreement::where('company_id', $company->id);

        $stats = [
            'total_agreements' => $agreements->count(),
            'draft_agreements' => $agreements->where('status', 'draft')->count(),
            'active_agreements' => $agreements->where('status', 'active')->count(),
            'expired_agreements' => $agreements->where('status', 'expired')->count(),
            'terminated_agreements' => $agreements->where('status', 'terminated')->count(),
            'agreements_this_year' => $agreements->where('agreement_date', '>=', now()->startOfYear())->count(),
            'agreements_this_month' => $agreements->where('agreement_date', '>=', now()->startOfMonth())->count(),
            'agreements_expiring_soon' => $agreements->where('expiry_date', '<=', now()->addMonths(3))->count(),
        ];

        $recentAgreements = $agreements->with(['supplier', 'dataProcessingActivity'])
            ->latest('agreement_date')
            ->limit(5)
            ->get();

        return view('data-processing-agreements.dashboard', compact('stats', 'recentAgreements'));
    }
}
