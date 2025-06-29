<?php

namespace App\Http\Controllers;

use App\Models\DataProtectionImpactAssessment;
use App\Models\DataProcessingActivity;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DataProtectionImpactAssessmentController extends Controller
{
    /**
     * Display a listing of DPIAs.
     */
    public function index(Request $request): View
    {
        $company = Auth::user()->company;

        $dpias = DataProtectionImpactAssessment::where('company_id', $company->id)
            ->with(['dataProcessingActivity', 'assessor', 'reviewer'])
            ->latest()
            ->paginate(15);

        return view('data-protection-impact-assessments.index', compact('dpias'));
    }

    /**
     * Show the form for creating a new DPIA.
     */
    public function create(): View
    {
        $company = Auth::user()->company;

        $activities = DataProcessingActivity::where('company_id', $company->id)
            ->where('data_protection_impact_assessment_required', true)
            ->get();

        return view('data-protection-impact-assessments.create', compact('company', 'activities'));
    }

    /**
     * Store a newly created DPIA.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'data_processing_activity_id' => 'required|exists:data_processing_activities,id',
            'assessment_title' => 'required|string|max:255',
            'assessment_description' => 'required|string',
            'assessment_date' => 'required|date',
            'risk_assessment_methodology' => 'required|string',
            'identified_risks' => 'required|array',
            'risk_mitigation_measures' => 'required|array',
            'residual_risks' => 'required|array',
            'consultation_required' => 'required|boolean',
            'consultation_date' => 'nullable|date|after_or_equal:assessment_date',
            'consultation_notes' => 'nullable|string',
            'supervisory_authority_consultation' => 'nullable|boolean',
            'supervisory_authority_consultation_date' => 'nullable|date|after_or_equal:assessment_date',
            'supervisory_authority_response' => 'nullable|string',
            'recommendations' => 'required|string',
            'approval_required' => 'required|boolean',
            'approval_date' => 'nullable|date|after_or_equal:assessment_date',
            'approval_notes' => 'nullable|string',
            'status' => 'required|in:draft,in_progress,completed,approved,rejected',
            'notes' => 'nullable|string',
        ]);

        $company = Auth::user()->company;

        try {
            DB::beginTransaction();

            $dpia = DataProtectionImpactAssessment::create(array_merge($request->all(), [
                'company_id' => $company->id,
                'assessor_id' => Auth::id(),
            ]));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'DPIA creata con successo',
                'dpia' => $dpia,
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
     * Display the specified DPIA.
     */
    public function show(DataProtectionImpactAssessment $dataProtectionImpactAssessment): View
    {
        $this->authorize('view', $dataProtectionImpactAssessment);

        $dataProtectionImpactAssessment->load([
            'dataProcessingActivity',
            'assessor',
            'reviewer',
            'company'
        ]);

        return view('data-protection-impact-assessments.show', compact('dataProtectionImpactAssessment'));
    }

    /**
     * Show the form for editing the specified DPIA.
     */
    public function edit(DataProtectionImpactAssessment $dataProtectionImpactAssessment): View
    {
        $this->authorize('update', $dataProtectionImpactAssessment);

        $company = Auth::user()->company;

        $activities = DataProcessingActivity::where('company_id', $company->id)
            ->where('data_protection_impact_assessment_required', true)
            ->get();

        return view('data-protection-impact-assessments.edit', compact('dataProtectionImpactAssessment', 'activities'));
    }

    /**
     * Update the specified DPIA.
     */
    public function update(Request $request, DataProtectionImpactAssessment $dataProtectionImpactAssessment): JsonResponse
    {
        $this->authorize('update', $dataProtectionImpactAssessment);

        $request->validate([
            'data_processing_activity_id' => 'required|exists:data_processing_activities,id',
            'assessment_title' => 'required|string|max:255',
            'assessment_description' => 'required|string',
            'assessment_date' => 'required|date',
            'risk_assessment_methodology' => 'required|string',
            'identified_risks' => 'required|array',
            'risk_mitigation_measures' => 'required|array',
            'residual_risks' => 'required|array',
            'consultation_required' => 'required|boolean',
            'consultation_date' => 'nullable|date|after_or_equal:assessment_date',
            'consultation_notes' => 'nullable|string',
            'supervisory_authority_consultation' => 'nullable|boolean',
            'supervisory_authority_consultation_date' => 'nullable|date|after_or_equal:assessment_date',
            'supervisory_authority_response' => 'nullable|string',
            'recommendations' => 'required|string',
            'approval_required' => 'required|boolean',
            'approval_date' => 'nullable|date|after_or_equal:assessment_date',
            'approval_notes' => 'nullable|string',
            'status' => 'required|in:draft,in_progress,completed,approved,rejected',
            'notes' => 'nullable|string',
        ]);

        $dataProtectionImpactAssessment->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'DPIA aggiornata con successo',
            'dpia' => $dataProtectionImpactAssessment,
        ]);
    }

    /**
     * Remove the specified DPIA.
     */
    public function destroy(DataProtectionImpactAssessment $dataProtectionImpactAssessment): JsonResponse
    {
        $this->authorize('delete', $dataProtectionImpactAssessment);

        $dataProtectionImpactAssessment->delete();

        return response()->json([
            'success' => true,
            'message' => 'DPIA eliminata con successo',
        ]);
    }

    /**
     * Review DPIA.
     */
    public function review(Request $request, DataProtectionImpactAssessment $dataProtectionImpactAssessment): JsonResponse
    {
        $this->authorize('review', $dataProtectionImpactAssessment);

        $request->validate([
            'review_notes' => 'required|string',
            'review_decision' => 'required|in:approved,rejected,requires_changes',
            'required_changes' => 'nullable|string',
        ]);

        $dataProtectionImpactAssessment->update([
            'reviewer_id' => Auth::id(),
            'review_date' => now(),
            'review_notes' => $request->review_notes,
            'review_decision' => $request->review_decision,
            'required_changes' => $request->required_changes,
            'status' => $request->review_decision === 'approved' ? 'approved' : 'in_progress',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'DPIA revisionata con successo',
            'dpia' => $dataProtectionImpactAssessment,
        ]);
    }

    /**
     * Approve DPIA.
     */
    public function approve(Request $request, DataProtectionImpactAssessment $dataProtectionImpactAssessment): JsonResponse
    {
        $this->authorize('approve', $dataProtectionImpactAssessment);

        $request->validate([
            'approval_notes' => 'nullable|string',
        ]);

        $dataProtectionImpactAssessment->update([
            'status' => 'approved',
            'approval_date' => now(),
            'approval_notes' => $request->approval_notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'DPIA approvata con successo',
            'dpia' => $dataProtectionImpactAssessment,
        ]);
    }

    /**
     * Export DPIAs.
     */
    public function export(Request $request): JsonResponse
    {
        $company = Auth::user()->company;

        $dpias = DataProtectionImpactAssessment::where('company_id', $company->id)
            ->with(['dataProcessingActivity', 'assessor', 'reviewer'])
            ->get();

        $exportData = [
            'company' => $company->name,
            'export_date' => now()->toISOString(),
            'dpias' => $dpias->map(function ($dpia) {
                return [
                    'id' => $dpia->id,
                    'assessment_title' => $dpia->assessment_title,
                    'data_processing_activity' => $dpia->dataProcessingActivity->activity_name,
                    'assessment_date' => $dpia->assessment_date->format('Y-m-d'),
                    'status' => $dpia->status,
                    'risk_assessment_methodology' => $dpia->risk_assessment_methodology,
                    'identified_risks' => $dpia->identified_risks,
                    'risk_mitigation_measures' => $dpia->risk_mitigation_measures,
                    'residual_risks' => $dpia->residual_risks,
                    'consultation_required' => $dpia->consultation_required,
                    'consultation_date' => $dpia->consultation_date?->format('Y-m-d'),
                    'supervisory_authority_consultation' => $dpia->supervisory_authority_consultation,
                    'supervisory_authority_consultation_date' => $dpia->supervisory_authority_consultation_date?->format('Y-m-d'),
                    'recommendations' => $dpia->recommendations,
                    'approval_required' => $dpia->approval_required,
                    'approval_date' => $dpia->approval_date?->format('Y-m-d'),
                    'assessor' => $dpia->assessor->name,
                    'reviewer' => $dpia->reviewer?->name,
                    'created_at' => $dpia->created_at->format('Y-m-d H:i:s'),
                ];
            }),
        ];

        return response()->json([
            'success' => true,
            'data' => $exportData,
        ]);
    }

    /**
     * Dashboard for DPIAs.
     */
    public function dashboard(): View
    {
        $company = Auth::user()->company;

        $dpias = DataProtectionImpactAssessment::where('company_id', $company->id);

        $stats = [
            'total_dpias' => $dpias->count(),
            'draft_dpias' => $dpias->where('status', 'draft')->count(),
            'in_progress_dpias' => $dpias->where('status', 'in_progress')->count(),
            'completed_dpias' => $dpias->where('status', 'completed')->count(),
            'approved_dpias' => $dpias->where('status', 'approved')->count(),
            'rejected_dpias' => $dpias->where('status', 'rejected')->count(),
            'dpias_this_year' => $dpias->where('assessment_date', '>=', now()->startOfYear())->count(),
            'dpias_this_month' => $dpias->where('assessment_date', '>=', now()->startOfMonth())->count(),
        ];

        $recentDpias = $dpias->with(['dataProcessingActivity', 'assessor'])
            ->latest('assessment_date')
            ->limit(5)
            ->get();

        return view('data-protection-impact-assessments.dashboard', compact('stats', 'recentDpias'));
    }
}
