<?php

namespace App\Http\Controllers;

use App\Models\DataBreach;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DataBreachController extends Controller
{
    /**
     * Display a listing of data breaches.
     */
    public function index(Request $request): View
    {
        $company = Auth::user()->company;

        $breaches = DataBreach::where('company_id', $company->id)
            ->with(['reportedBy', 'investigatedBy'])
            ->latest()
            ->paginate(15);

        return view('data-breaches.index', compact('breaches'));
    }

    /**
     * Show the form for creating a new data breach.
     */
    public function create(): View
    {
        $company = Auth::user()->company;

        return view('data-breaches.create', compact('company'));
    }

    /**
     * Store a newly created data breach.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'breach_type' => 'required|string',
            'breach_date' => 'required|date',
            'discovery_date' => 'required|date|after_or_equal:breach_date',
            'affected_data_categories' => 'required|array',
            'affected_data_subjects' => 'required|array',
            'number_of_affected_individuals' => 'required|integer|min:1',
            'breach_description' => 'required|string',
            'root_cause' => 'required|string',
            'immediate_actions_taken' => 'required|string',
            'risk_level' => 'required|in:low,medium,high,critical',
            'notification_required' => 'required|boolean',
            'notification_date' => 'nullable|date|after_or_equal:discovery_date',
            'supervisory_authority_notified' => 'nullable|boolean',
            'data_subjects_notified' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);

        $company = Auth::user()->company;

        try {
            DB::beginTransaction();

            $breach = DataBreach::create(array_merge($request->all(), [
                'company_id' => $company->id,
                'reported_by' => Auth::id(),
                'status' => 'reported',
            ]));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Violazione dati registrata con successo',
                'breach' => $breach,
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
     * Display the specified data breach.
     */
    public function show(DataBreach $dataBreach): View
    {
        $this->authorize('view', $dataBreach);

        $dataBreach->load(['reportedBy', 'investigatedBy', 'company']);

        return view('data-breaches.show', compact('dataBreach'));
    }

    /**
     * Show the form for editing the specified data breach.
     */
    public function edit(DataBreach $dataBreach): View
    {
        $this->authorize('update', $dataBreach);

        return view('data-breaches.edit', compact('dataBreach'));
    }

    /**
     * Update the specified data breach.
     */
    public function update(Request $request, DataBreach $dataBreach): JsonResponse
    {
        $this->authorize('update', $dataBreach);

        $request->validate([
            'breach_type' => 'required|string',
            'breach_date' => 'required|date',
            'discovery_date' => 'required|date|after_or_equal:breach_date',
            'affected_data_categories' => 'required|array',
            'affected_data_subjects' => 'required|array',
            'number_of_affected_individuals' => 'required|integer|min:1',
            'breach_description' => 'required|string',
            'root_cause' => 'required|string',
            'immediate_actions_taken' => 'required|string',
            'risk_level' => 'required|in:low,medium,high,critical',
            'notification_required' => 'required|boolean',
            'notification_date' => 'nullable|date|after_or_equal:discovery_date',
            'supervisory_authority_notified' => 'nullable|boolean',
            'data_subjects_notified' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);

        $dataBreach->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Violazione dati aggiornata con successo',
            'breach' => $dataBreach,
        ]);
    }

    /**
     * Remove the specified data breach.
     */
    public function destroy(DataBreach $dataBreach): JsonResponse
    {
        $this->authorize('delete', $dataBreach);

        $dataBreach->delete();

        return response()->json([
            'success' => true,
            'message' => 'Violazione dati eliminata con successo',
        ]);
    }

    /**
     * Mark breach as investigated.
     */
    public function markInvestigated(Request $request, DataBreach $dataBreach): JsonResponse
    {
        $this->authorize('update', $dataBreach);

        $request->validate([
            'investigation_findings' => 'required|string',
            'corrective_actions' => 'required|string',
            'preventive_measures' => 'required|string',
        ]);

        $dataBreach->update([
            'status' => 'investigated',
            'investigation_findings' => $request->investigation_findings,
            'corrective_actions' => $request->corrective_actions,
            'preventive_measures' => $request->preventive_measures,
            'investigated_by' => Auth::id(),
            'investigation_date' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Violazione dati marcata come investigata',
            'breach' => $dataBreach,
        ]);
    }

    /**
     * Mark breach as resolved.
     */
    public function markResolved(Request $request, DataBreach $dataBreach): JsonResponse
    {
        $this->authorize('update', $dataBreach);

        $request->validate([
            'resolution_summary' => 'required|string',
        ]);

        $dataBreach->update([
            'status' => 'resolved',
            'resolution_summary' => $request->resolution_summary,
            'resolution_date' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Violazione dati marcata come risolta',
            'breach' => $dataBreach,
        ]);
    }

    /**
     * Export data breaches.
     */
    public function export(Request $request): JsonResponse
    {
        $company = Auth::user()->company;

        $breaches = DataBreach::where('company_id', $company->id)
            ->with(['reportedBy', 'investigatedBy'])
            ->get();

        $exportData = [
            'company' => $company->name,
            'export_date' => now()->toISOString(),
            'breaches' => $breaches->map(function ($breach) {
                return [
                    'id' => $breach->id,
                    'breach_type' => $breach->breach_type,
                    'breach_date' => $breach->breach_date->format('Y-m-d'),
                    'discovery_date' => $breach->discovery_date->format('Y-m-d'),
                    'affected_data_categories' => $breach->affected_data_categories,
                    'affected_data_subjects' => $breach->affected_data_subjects,
                    'number_of_affected_individuals' => $breach->number_of_affected_individuals,
                    'risk_level' => $breach->risk_level,
                    'status' => $breach->status,
                    'notification_required' => $breach->notification_required,
                    'notification_date' => $breach->notification_date?->format('Y-m-d'),
                    'supervisory_authority_notified' => $breach->supervisory_authority_notified,
                    'data_subjects_notified' => $breach->data_subjects_notified,
                    'reported_by' => $breach->reportedBy->name,
                    'investigated_by' => $breach->investigatedBy?->name,
                    'created_at' => $breach->created_at->format('Y-m-d H:i:s'),
                ];
            }),
        ];

        return response()->json([
            'success' => true,
            'data' => $exportData,
        ]);
    }

    /**
     * Dashboard for data breaches.
     */
    public function dashboard(): View
    {
        $company = Auth::user()->company;

        $breaches = DataBreach::where('company_id', $company->id);

        $stats = [
            'total_breaches' => $breaches->count(),
            'reported_breaches' => $breaches->where('status', 'reported')->count(),
            'investigated_breaches' => $breaches->where('status', 'investigated')->count(),
            'resolved_breaches' => $breaches->where('status', 'resolved')->count(),
            'critical_breaches' => $breaches->where('risk_level', 'critical')->count(),
            'high_risk_breaches' => $breaches->where('risk_level', 'high')->count(),
            'breaches_this_year' => $breaches->where('breach_date', '>=', now()->startOfYear())->count(),
            'breaches_this_month' => $breaches->where('breach_date', '>=', now()->startOfMonth())->count(),
        ];

        $recentBreaches = $breaches->with(['reportedBy'])
            ->latest('breach_date')
            ->limit(5)
            ->get();

        return view('data-breaches.dashboard', compact('stats', 'recentBreaches'));
    }
}
