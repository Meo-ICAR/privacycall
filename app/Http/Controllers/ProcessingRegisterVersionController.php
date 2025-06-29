<?php

namespace App\Http\Controllers;

use App\Models\ProcessingRegisterVersion;
use App\Models\ProcessingRegisterChange;
use App\Models\DataProcessingActivity;
use App\Models\DataBreach;
use App\Models\DataProtectionImpactAssessment;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProcessingRegisterVersionController extends Controller
{
    /**
     * Display a listing of register versions.
     */
    public function index(Request $request): View
    {
        $company = Auth::user()->company;

        $versions = ProcessingRegisterVersion::where('company_id', $company->id)
            ->with(['createdBy', 'approvedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('gdpr.register.versions.index', compact('versions'));
    }

    /**
     * Show the form for creating a new version.
     */
    public function create(): View
    {
        $company = Auth::user()->company;

        return view('gdpr.register.versions.create', compact('company'));
    }

    /**
     * Store a newly created version.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'version_name' => 'required|string|max:255',
            'version_description' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $company = Auth::user()->company;

        try {
            DB::beginTransaction();

            // Crea una nuova versione
            $version = ProcessingRegisterVersion::createFromCurrentState($company->id, [
                'version_name' => $request->version_name,
                'version_description' => $request->version_description,
                'notes' => $request->notes,
            ]);

            // Genera lo snapshot del registro corrente
            $registerData = $this->generateRegisterSnapshot($company);
            $activitiesSummary = $this->generateActivitiesSummary($company);
            $complianceSummary = $this->generateComplianceSummary($company);

            $version->update([
                'register_data' => $registerData,
                'activities_summary' => $activitiesSummary,
                'compliance_summary' => $complianceSummary,
            ]);

            // Aggiorna la versione del registro nell'azienda
            $company->update([
                'register_version' => $version->version_number,
                'register_last_updated' => now(),
                'register_last_updated_by' => Auth::id(),
                'register_update_notes' => "Creata nuova versione: {$version->version_name}",
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Versione del registro creata con successo',
                'version' => $version,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Errore durante la creazione della versione: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified version.
     */
    public function show(ProcessingRegisterVersion $version): View
    {
        $this->authorize('view', $version);

        $version->load(['createdBy', 'approvedBy', 'changes.changedBy']);

        return view('gdpr.register.versions.show', compact('version'));
    }

    /**
     * Show the form for editing the specified version.
     */
    public function edit(ProcessingRegisterVersion $version): View
    {
        $this->authorize('update', $version);

        return view('gdpr.register.versions.edit', compact('version'));
    }

    /**
     * Update the specified version.
     */
    public function update(Request $request, ProcessingRegisterVersion $version): JsonResponse
    {
        $this->authorize('update', $version);

        $request->validate([
            'version_name' => 'required|string|max:255',
            'version_description' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $version->update($request->only(['version_name', 'version_description', 'notes']));

        return response()->json([
            'success' => true,
            'message' => 'Versione aggiornata con successo',
            'version' => $version,
        ]);
    }

    /**
     * Approve the specified version.
     */
    public function approve(Request $request, ProcessingRegisterVersion $version): JsonResponse
    {
        $this->authorize('approve', $version);

        $request->validate([
            'approval_notes' => 'nullable|string',
        ]);

        $version->approve(Auth::id(), $request->approval_notes);

        return response()->json([
            'success' => true,
            'message' => 'Versione approvata con successo',
            'version' => $version,
        ]);
    }

    /**
     * Archive the specified version.
     */
    public function archive(ProcessingRegisterVersion $version): JsonResponse
    {
        $this->authorize('archive', $version);

        $version->archive();

        return response()->json([
            'success' => true,
            'message' => 'Versione archiviata con successo',
            'version' => $version,
        ]);
    }

    /**
     * Export the specified version.
     */
    public function export(ProcessingRegisterVersion $version): JsonResponse
    {
        $this->authorize('export', $version);

        $exportData = [
            'version' => $version->toArray(),
            'register_data' => $version->register_data,
            'activities_summary' => $version->activities_summary,
            'compliance_summary' => $version->compliance_summary,
            'changes_log' => $version->changes_log,
        ];

        return response()->json([
            'success' => true,
            'data' => $exportData,
        ]);
    }

    /**
     * Compare two versions.
     */
    public function compare(Request $request): View
    {
        $request->validate([
            'version1_id' => 'required|exists:processing_register_versions,id',
            'version2_id' => 'required|exists:processing_register_versions,id',
        ]);

        $version1 = ProcessingRegisterVersion::with(['createdBy', 'approvedBy'])->findOrFail($request->version1_id);
        $version2 = ProcessingRegisterVersion::with(['createdBy', 'approvedBy'])->findOrFail($request->version2_id);

        $this->authorize('view', $version1);
        $this->authorize('view', $version2);

        $differences = $this->compareVersions($version1, $version2);

        return view('gdpr.register.versions.compare', compact('version1', 'version2', 'differences'));
    }

    /**
     * Get version history for an entity.
     */
    public function entityHistory(Request $request): JsonResponse
    {
        $request->validate([
            'entity_type' => 'required|string',
            'entity_id' => 'required|integer',
        ]);

        $changes = ProcessingRegisterChange::where('entity_type', $request->entity_type)
            ->where('entity_id', $request->entity_id)
            ->with(['changedBy', 'reviewedBy', 'approvedBy'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'changes' => $changes,
        ]);
    }

    /**
     * Generate register snapshot.
     */
    private function generateRegisterSnapshot(Company $company): array
    {
        return [
            'company_info' => $company->toArray(),
            'activities_count' => DataProcessingActivity::where('company_id', $company->id)->count(),
            'breaches_count' => DataBreach::where('company_id', $company->id)->count(),
            'dpias_count' => DataProtectionImpactAssessment::where('company_id', $company->id)->count(),
            'generated_at' => now()->toISOString(),
        ];
    }

    /**
     * Generate activities summary.
     */
    private function generateActivitiesSummary(Company $company): array
    {
        $activities = DataProcessingActivity::where('company_id', $company->id)
            ->latestVersion()
            ->get();

        return [
            'total_activities' => $activities->count(),
            'active_activities' => $activities->where('is_active', true)->count(),
            'by_legal_basis' => $activities->groupBy('legal_basis')->map->count(),
            'by_risk_level' => $activities->groupBy('risk_assessment_level')->map->count(),
            'by_compliance_status' => $activities->groupBy('compliance_status')->map->count(),
            'activities_list' => $activities->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'name' => $activity->activity_name,
                    'purpose' => $activity->processing_purpose,
                    'legal_basis' => $activity->legal_basis,
                    'risk_level' => $activity->risk_assessment_level,
                    'compliance_status' => $activity->compliance_status,
                    'version' => $activity->version,
                ];
            }),
        ];
    }

    /**
     * Generate compliance summary.
     */
    private function generateComplianceSummary(Company $company): array
    {
        $activities = DataProcessingActivity::where('company_id', $company->id)
            ->latestVersion()
            ->get();

        $breaches = DataBreach::where('company_id', $company->id)->get();
        $dpias = DataProtectionImpactAssessment::where('company_id', $company->id)->get();

        return [
            'compliance_score' => $this->calculateComplianceScore($activities),
            'overdue_reviews' => $activities->where('next_compliance_review_date', '<', now())->count(),
            'upcoming_reviews' => $activities->whereBetween('next_compliance_review_date', [now(), now()->addDays(30)])->count(),
            'breaches_this_year' => $breaches->where('breach_date', '>=', now()->startOfYear())->count(),
            'dpias_required' => $activities->where('data_protection_impact_assessment_required', true)->count(),
            'dpias_completed' => $dpias->where('status', 'completed')->count(),
            'risk_distribution' => [
                'low' => $activities->where('risk_assessment_level', 'low')->count(),
                'medium' => $activities->where('risk_assessment_level', 'medium')->count(),
                'high' => $activities->where('risk_assessment_level', 'high')->count(),
                'very_high' => $activities->where('risk_assessment_level', 'very_high')->count(),
            ],
        ];
    }

    /**
     * Calculate compliance score.
     */
    private function calculateComplianceScore($activities): float
    {
        if ($activities->isEmpty()) {
            return 0.0;
        }

        $compliantCount = $activities->where('compliance_status', 'compliant')->count();
        return round(($compliantCount / $activities->count()) * 100, 2);
    }

    /**
     * Compare two versions.
     */
    private function compareVersions(ProcessingRegisterVersion $version1, ProcessingRegisterVersion $version2): array
    {
        $differences = [];

        // Confronta i dati del registro
        if ($version1->register_data && $version2->register_data) {
            $differences['register_data'] = $this->compareArrays(
                $version1->register_data,
                $version2->register_data
            );
        }

        // Confronta il riepilogo delle attività
        if ($version1->activities_summary && $version2->activities_summary) {
            $differences['activities_summary'] = $this->compareArrays(
                $version1->activities_summary,
                $version2->activities_summary
            );
        }

        // Confronta il riepilogo della compliance
        if ($version1->compliance_summary && $version2->compliance_summary) {
            $differences['compliance_summary'] = $this->compareArrays(
                $version1->compliance_summary,
                $version2->compliance_summary
            );
        }

        return $differences;
    }

    /**
     * Compare two arrays and find differences.
     */
    private function compareArrays(array $array1, array $array2): array
    {
        $differences = [];

        foreach ($array2 as $key => $value) {
            if (!isset($array1[$key])) {
                $differences[$key] = [
                    'type' => 'added',
                    'old_value' => null,
                    'new_value' => $value,
                ];
            } elseif ($array1[$key] !== $value) {
                $differences[$key] = [
                    'type' => 'modified',
                    'old_value' => $array1[$key],
                    'new_value' => $value,
                ];
            }
        }

        foreach ($array1 as $key => $value) {
            if (!isset($array2[$key])) {
                $differences[$key] = [
                    'type' => 'removed',
                    'old_value' => $value,
                    'new_value' => null,
                ];
            }
        }

        return $differences;
    }
}
