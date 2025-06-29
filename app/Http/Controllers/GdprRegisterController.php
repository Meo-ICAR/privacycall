<?php

namespace App\Http\Controllers;

use App\Models\DataProcessingActivity;
use App\Models\DataBreach;
use App\Models\DataProtectionIA;
use App\Models\ThirdCountryTransfer;
use App\Models\DataProcessingAgreement;
use App\Models\DataSubjectRightsRequest;
use App\Models\LegalBasisType;
use App\Models\DataCategory;
use App\Models\SecurityMeasure;
use App\Models\ThirdCountry;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GdprRegisterController extends Controller
{
    /**
     * Display the GDPR register dashboard.
     */
    public function dashboard()
    {
        $user = Auth::user();
        $company = $user->company;

        if (!$company) {
            return redirect()->route('dashboard')
                ->with('error', 'You must be assigned to a company to access the GDPR register.');
        }

        // Get statistics
        $stats = $this->getGdprStatistics($company->id);

        // Get recent activities
        $recentActivities = DataProcessingActivity::where('company_id', $company->id)
            ->with('company')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get overdue items
        $overdueItems = $this->getOverdueItems($company->id);

        // Get compliance status
        $complianceStatus = $this->getComplianceStatus($company->id);

        return view('gdpr.register.dashboard', compact(
            'stats',
            'recentActivities',
            'overdueItems',
            'complianceStatus',
            'company'
        ));
    }

    /**
     * Display the complete GDPR register.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $company = $user->company;

        if (!$company) {
            return redirect()->route('dashboard')
                ->with('error', 'You must be assigned to a company to access the GDPR register.');
        }

        $query = DataProcessingActivity::where('company_id', $company->id)
            ->with(['company', 'dataProtectionIAs', 'thirdCountryTransfers', 'dataProcessingAgreements']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('compliance_status', $request->status);
        }

        if ($request->filled('risk_level')) {
            $query->where('risk_assessment_level', $request->risk_level);
        }

        if ($request->filled('legal_basis')) {
            $query->where('legal_basis', $request->legal_basis);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('activity_name', 'like', "%{$search}%")
                  ->orWhere('processing_purpose', 'like', "%{$search}%")
                  ->orWhere('activity_description', 'like', "%{$search}%");
            });
        }

        $activities = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get filter options
        $legalBasisTypes = LegalBasisType::where('is_active', true)->get();
        $dataCategories = DataCategory::where('is_active', true)->get();

        return view('gdpr.register.index', compact(
            'activities',
            'legalBasisTypes',
            'dataCategories',
            'company'
        ));
    }

    /**
     * Export the GDPR register.
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        $company = $user->company;

        if (!$company) {
            return redirect()->route('gdpr.register.index')
                ->with('error', 'You must be assigned to a company to export the GDPR register.');
        }

        $format = $request->get('format', 'pdf');

        $activities = DataProcessingActivity::where('company_id', $company->id)
            ->with(['company', 'dataProtectionIAs', 'thirdCountryTransfers', 'dataProcessingAgreements'])
            ->orderBy('created_at', 'desc')
            ->get();

        $breaches = DataBreach::where('company_id', $company->id)
            ->orderBy('detection_date', 'desc')
            ->get();

        $dpias = DataProtectionIA::where('company_id', $company->id)
            ->orderBy('assessment_date', 'desc')
            ->get();

        $transfers = ThirdCountryTransfer::where('company_id', $company->id)
            ->orderBy('start_date', 'desc')
            ->get();

        $agreements = DataProcessingAgreement::where('company_id', $company->id)
            ->orderBy('agreement_date', 'desc')
            ->get();

        $data = [
            'company' => $company,
            'activities' => $activities,
            'breaches' => $breaches,
            'dpias' => $dpias,
            'transfers' => $transfers,
            'agreements' => $agreements,
            'export_date' => now(),
        ];

        switch ($format) {
            case 'pdf':
                return $this->exportToPdf($data);
            case 'excel':
                return $this->exportToExcel($data);
            case 'json':
                return $this->exportToJson($data);
            default:
                return $this->exportToPdf($data);
        }
    }

    /**
     * Get GDPR statistics for the company.
     */
    private function getGdprStatistics($companyId)
    {
        return [
            'total_activities' => DataProcessingActivity::where('company_id', $companyId)->count(),
            'active_activities' => DataProcessingActivity::where('company_id', $companyId)->where('is_active', true)->count(),
            'compliant_activities' => DataProcessingActivity::where('company_id', $companyId)->where('compliance_status', 'compliant')->count(),
            'non_compliant_activities' => DataProcessingActivity::where('company_id', $companyId)->where('compliance_status', 'non_compliant')->count(),
            'total_breaches' => DataBreach::where('company_id', $companyId)->count(),
            'open_breaches' => DataBreach::where('company_id', $companyId)->whereIn('status', ['detected', 'investigating'])->count(),
            'total_dpias' => DataProtectionIA::where('company_id', $companyId)->count(),
            'pending_dpias' => DataProtectionIA::where('company_id', $companyId)->whereIn('assessment_status', ['draft', 'in_progress'])->count(),
            'total_transfers' => ThirdCountryTransfer::where('company_id', $companyId)->count(),
            'active_transfers' => ThirdCountryTransfer::where('company_id', $companyId)->where('is_active', true)->count(),
            'total_agreements' => DataProcessingAgreement::where('company_id', $companyId)->count(),
            'active_agreements' => DataProcessingAgreement::where('company_id', $companyId)->where('agreement_status', 'active')->count(),
            'total_requests' => DataSubjectRightsRequest::where('company_id', $companyId)->count(),
            'pending_requests' => DataSubjectRightsRequest::where('company_id', $companyId)->whereIn('status', ['received', 'processing'])->count(),
        ];
    }

    /**
     * Get overdue items for the company.
     */
    private function getOverdueItems($companyId)
    {
        return [
            'compliance_reviews' => DataProcessingActivity::where('company_id', $companyId)
                ->where('next_compliance_review_date', '<', now())
                ->count(),
            'dpia_reviews' => DataProtectionIA::where('company_id', $companyId)
                ->where('next_review_date', '<', now())
                ->count(),
            'breach_notifications' => DataBreach::where('company_id', $companyId)
                ->where('detection_date', '<', now()->subHours(72))
                ->where('dpa_notified', false)
                ->count(),
            'rights_requests' => DataSubjectRightsRequest::where('company_id', $companyId)
                ->where('response_deadline', '<', now())
                ->where('response_provided', false)
                ->count(),
        ];
    }

    /**
     * Get compliance status for the company.
     */
    private function getComplianceStatus($companyId)
    {
        $totalActivities = DataProcessingActivity::where('company_id', $companyId)->count();
        $compliantActivities = DataProcessingActivity::where('company_id', $companyId)
            ->where('compliance_status', 'compliant')->count();

        if ($totalActivities === 0) {
            return [
                'percentage' => 0,
                'status' => 'no_data',
                'color' => 'gray',
            ];
        }

        $percentage = round(($compliantActivities / $totalActivities) * 100, 1);

        if ($percentage >= 90) {
            $status = 'excellent';
            $color = 'green';
        } elseif ($percentage >= 75) {
            $status = 'good';
            $color = 'blue';
        } elseif ($percentage >= 50) {
            $status = 'fair';
            $color = 'yellow';
        } else {
            $status = 'poor';
            $color = 'red';
        }

        return [
            'percentage' => $percentage,
            'status' => $status,
            'color' => $color,
            'total' => $totalActivities,
            'compliant' => $compliantActivities,
        ];
    }

    /**
     * Export to PDF.
     */
    private function exportToPdf($data)
    {
        // Implementation for PDF export
        // This would typically use a library like DomPDF or similar
        return response()->json([
            'message' => 'PDF export functionality would be implemented here',
            'data' => $data
        ]);
    }

    /**
     * Export to Excel.
     */
    private function exportToExcel($data)
    {
        // Implementation for Excel export
        // This would typically use a library like Maatwebsite Excel
        return response()->json([
            'message' => 'Excel export functionality would be implemented here',
            'data' => $data
        ]);
    }

    /**
     * Export to JSON.
     */
    private function exportToJson($data)
    {
        return response()->json($data);
    }

    /**
     * Show the GDPR register report.
     */
    public function report()
    {
        $user = Auth::user();
        $company = $user->company;

        if (!$company) {
            return redirect()->route('dashboard')
                ->with('error', 'You must be assigned to a company to access the GDPR report.');
        }

        $stats = $this->getGdprStatistics($company->id);
        $complianceStatus = $this->getComplianceStatus($company->id);
        $overdueItems = $this->getOverdueItems($company->id);

        // Get activities by compliance status
        $activitiesByStatus = DataProcessingActivity::where('company_id', $company->id)
            ->select('compliance_status', DB::raw('count(*) as count'))
            ->groupBy('compliance_status')
            ->get();

        // Get activities by risk level
        $activitiesByRisk = DataProcessingActivity::where('company_id', $company->id)
            ->select('risk_assessment_level', DB::raw('count(*) as count'))
            ->groupBy('risk_assessment_level')
            ->get();

        // Get recent breaches
        $recentBreaches = DataBreach::where('company_id', $company->id)
            ->orderBy('detection_date', 'desc')
            ->limit(10)
            ->get();

        // Get recent DPIAs
        $recentDpias = DataProtectionIA::where('company_id', $company->id)
            ->orderBy('assessment_date', 'desc')
            ->limit(10)
            ->get();

        return view('gdpr.register.report', compact(
            'stats',
            'complianceStatus',
            'overdueItems',
            'activitiesByStatus',
            'activitiesByRisk',
            'recentBreaches',
            'recentDpias',
            'company'
        ));
    }
}
