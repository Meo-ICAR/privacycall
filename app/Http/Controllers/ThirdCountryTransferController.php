<?php

namespace App\Http\Controllers;

use App\Models\ThirdCountryTransfer;
use App\Models\DataProcessingActivity;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ThirdCountryTransferController extends Controller
{
    /**
     * Display a listing of third country transfers.
     */
    public function index(Request $request): View
    {
        $company = Auth::user()->company;

        $transfers = ThirdCountryTransfer::where('company_id', $company->id)
            ->with(['dataProcessingActivity', 'destinationCountry', 'createdBy'])
            ->latest()
            ->paginate(15);

        return view('third-country-transfers.index', compact('transfers'));
    }

    /**
     * Show the form for creating a new transfer.
     */
    public function create(): View
    {
        $company = Auth::user()->company;

        $activities = DataProcessingActivity::where('company_id', $company->id)
            ->where('has_third_country_transfers', true)
            ->get();

        return view('third-country-transfers.create', compact('company', 'activities'));
    }

    /**
     * Store a newly created transfer.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'data_processing_activity_id' => 'required|exists:data_processing_activities,id',
            'destination_country_id' => 'required|exists:third_countries,id',
            'transfer_purpose' => 'required|string',
            'transfer_date' => 'required|date',
            'data_categories_transferred' => 'required|array',
            'data_subjects_affected' => 'required|array',
            'number_of_individuals' => 'required|integer|min:1',
            'legal_basis' => 'required|string',
            'adequacy_decision' => 'nullable|boolean',
            'adequacy_decision_details' => 'nullable|string',
            'standard_contractual_clauses' => 'nullable|boolean',
            'scc_version' => 'nullable|string',
            'binding_corporate_rules' => 'nullable|boolean',
            'bcr_details' => 'nullable|string',
            'approved_codes_of_conduct' => 'nullable|boolean',
            'code_details' => 'nullable|string',
            'certification_mechanisms' => 'nullable|boolean',
            'certification_details' => 'nullable|string',
            'derogations' => 'nullable|boolean',
            'derogation_details' => 'nullable|string',
            'risk_assessment' => 'required|string',
            'safeguards_implemented' => 'required|array',
            'monitoring_mechanisms' => 'required|string',
            'review_frequency' => 'required|string',
            'next_review_date' => 'required|date|after:transfer_date',
            'status' => 'required|in:active,suspended,terminated',
            'notes' => 'nullable|string',
        ]);

        $company = Auth::user()->company;

        try {
            DB::beginTransaction();

            $transfer = ThirdCountryTransfer::create(array_merge($request->all(), [
                'company_id' => $company->id,
                'created_by' => Auth::id(),
            ]));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Trasferimento verso paese terzo registrato con successo',
                'transfer' => $transfer,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Errore durante la registrazione: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified transfer.
     */
    public function show(ThirdCountryTransfer $thirdCountryTransfer): View
    {
        $this->authorize('view', $thirdCountryTransfer);

        $thirdCountryTransfer->load([
            'dataProcessingActivity',
            'destinationCountry',
            'createdBy',
            'company'
        ]);

        return view('third-country-transfers.show', compact('thirdCountryTransfer'));
    }

    /**
     * Show the form for editing the specified transfer.
     */
    public function edit(ThirdCountryTransfer $thirdCountryTransfer): View
    {
        $this->authorize('update', $thirdCountryTransfer);

        $company = Auth::user()->company;

        $activities = DataProcessingActivity::where('company_id', $company->id)
            ->where('has_third_country_transfers', true)
            ->get();

        return view('third-country-transfers.edit', compact('thirdCountryTransfer', 'activities'));
    }

    /**
     * Update the specified transfer.
     */
    public function update(Request $request, ThirdCountryTransfer $thirdCountryTransfer): JsonResponse
    {
        $this->authorize('update', $thirdCountryTransfer);

        $request->validate([
            'data_processing_activity_id' => 'required|exists:data_processing_activities,id',
            'destination_country_id' => 'required|exists:third_countries,id',
            'transfer_purpose' => 'required|string',
            'transfer_date' => 'required|date',
            'data_categories_transferred' => 'required|array',
            'data_subjects_affected' => 'required|array',
            'number_of_individuals' => 'required|integer|min:1',
            'legal_basis' => 'required|string',
            'adequacy_decision' => 'nullable|boolean',
            'adequacy_decision_details' => 'nullable|string',
            'standard_contractual_clauses' => 'nullable|boolean',
            'scc_version' => 'nullable|string',
            'binding_corporate_rules' => 'nullable|boolean',
            'bcr_details' => 'nullable|string',
            'approved_codes_of_conduct' => 'nullable|boolean',
            'code_details' => 'nullable|string',
            'certification_mechanisms' => 'nullable|boolean',
            'certification_details' => 'nullable|string',
            'derogations' => 'nullable|boolean',
            'derogation_details' => 'nullable|string',
            'risk_assessment' => 'required|string',
            'safeguards_implemented' => 'required|array',
            'monitoring_mechanisms' => 'required|string',
            'review_frequency' => 'required|string',
            'next_review_date' => 'required|date|after:transfer_date',
            'status' => 'required|in:active,suspended,terminated',
            'notes' => 'nullable|string',
        ]);

        $thirdCountryTransfer->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Trasferimento aggiornato con successo',
            'transfer' => $thirdCountryTransfer,
        ]);
    }

    /**
     * Remove the specified transfer.
     */
    public function destroy(ThirdCountryTransfer $thirdCountryTransfer): JsonResponse
    {
        $this->authorize('delete', $thirdCountryTransfer);

        $thirdCountryTransfer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Trasferimento eliminato con successo',
        ]);
    }

    /**
     * Suspend transfer.
     */
    public function suspend(Request $request, ThirdCountryTransfer $thirdCountryTransfer): JsonResponse
    {
        $this->authorize('update', $thirdCountryTransfer);

        $request->validate([
            'suspension_reason' => 'required|string',
        ]);

        $thirdCountryTransfer->update([
            'status' => 'suspended',
            'suspension_reason' => $request->suspension_reason,
            'suspension_date' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Trasferimento sospeso con successo',
            'transfer' => $thirdCountryTransfer,
        ]);
    }

    /**
     * Terminate transfer.
     */
    public function terminate(Request $request, ThirdCountryTransfer $thirdCountryTransfer): JsonResponse
    {
        $this->authorize('update', $thirdCountryTransfer);

        $request->validate([
            'termination_reason' => 'required|string',
        ]);

        $thirdCountryTransfer->update([
            'status' => 'terminated',
            'termination_reason' => $request->termination_reason,
            'termination_date' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Trasferimento terminato con successo',
            'transfer' => $thirdCountryTransfer,
        ]);
    }

    /**
     * Export transfers.
     */
    public function export(Request $request): JsonResponse
    {
        $company = Auth::user()->company;

        $transfers = ThirdCountryTransfer::where('company_id', $company->id)
            ->with(['dataProcessingActivity', 'destinationCountry', 'createdBy'])
            ->get();

        $exportData = [
            'company' => $company->name,
            'export_date' => now()->toISOString(),
            'transfers' => $transfers->map(function ($transfer) {
                return [
                    'id' => $transfer->id,
                    'data_processing_activity' => $transfer->dataProcessingActivity->activity_name,
                    'destination_country' => $transfer->destinationCountry->name,
                    'transfer_purpose' => $transfer->transfer_purpose,
                    'transfer_date' => $transfer->transfer_date->format('Y-m-d'),
                    'data_categories_transferred' => $transfer->data_categories_transferred,
                    'data_subjects_affected' => $transfer->data_subjects_affected,
                    'number_of_individuals' => $transfer->number_of_individuals,
                    'legal_basis' => $transfer->legal_basis,
                    'adequacy_decision' => $transfer->adequacy_decision,
                    'standard_contractual_clauses' => $transfer->standard_contractual_clauses,
                    'scc_version' => $transfer->scc_version,
                    'binding_corporate_rules' => $transfer->binding_corporate_rules,
                    'risk_assessment' => $transfer->risk_assessment,
                    'safeguards_implemented' => $transfer->safeguards_implemented,
                    'monitoring_mechanisms' => $transfer->monitoring_mechanisms,
                    'review_frequency' => $transfer->review_frequency,
                    'next_review_date' => $transfer->next_review_date->format('Y-m-d'),
                    'status' => $transfer->status,
                    'created_by' => $transfer->createdBy->name,
                    'created_at' => $transfer->created_at->format('Y-m-d H:i:s'),
                ];
            }),
        ];

        return response()->json([
            'success' => true,
            'data' => $exportData,
        ]);
    }

    /**
     * Dashboard for transfers.
     */
    public function dashboard(): View
    {
        $company = Auth::user()->company;

        $transfers = ThirdCountryTransfer::where('company_id', $company->id);

        $stats = [
            'total_transfers' => $transfers->count(),
            'active_transfers' => $transfers->where('status', 'active')->count(),
            'suspended_transfers' => $transfers->where('status', 'suspended')->count(),
            'terminated_transfers' => $transfers->where('status', 'terminated')->count(),
            'transfers_this_year' => $transfers->where('transfer_date', '>=', now()->startOfYear())->count(),
            'transfers_this_month' => $transfers->where('transfer_date', '>=', now()->startOfMonth())->count(),
            'transfers_with_adequacy' => $transfers->where('adequacy_decision', true)->count(),
            'transfers_with_scc' => $transfers->where('standard_contractual_clauses', true)->count(),
        ];

        $recentTransfers = $transfers->with(['dataProcessingActivity', 'destinationCountry'])
            ->latest('transfer_date')
            ->limit(5)
            ->get();

        return view('third-country-transfers.dashboard', compact('stats', 'recentTransfers'));
    }
}
