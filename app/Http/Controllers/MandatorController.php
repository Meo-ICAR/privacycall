<?php

namespace App\Http\Controllers;

use App\Models\Mandator;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class MandatorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Mandator::with('company');

        // Filter by company
        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Filter by clone status
        if ($request->has('is_clone')) {
            if ($request->boolean('is_clone')) {
                $query->clones();
            } else {
                $query->originals();
            }
        }

        // Search by name or email
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $mandators = $query->paginate($request->get('per_page', 15));
        $companies = Company::active()->get();

        // Return JSON for API requests, view for web requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $mandators,
                'message' => 'Mandators retrieved successfully'
            ]);
        }

        return view('mandators.index', compact('mandators', 'companies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $companyId = $request->query('company_id');
        $company = null;
        if ($companyId) {
            $company = Company::findOrFail($companyId);
        }
        $companies = $company ? null : Company::active()->get();

        // Get active disclosure types from database
        $disclosureTypes = \App\Models\DisclosureType::active()->ordered()->get();

        return view('mandators.create', compact('companies', 'company', 'disclosureTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'required|exists:companies,id',
            'agent_company_id' => 'nullable|exists:companies,id',
            'gdpr_representative_id' => 'nullable|exists:users,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:mandators,email',
            'phone' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'disclosure_subscriptions' => 'nullable|array',
            'disclosure_subscriptions.*' => 'string|max:255',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'preferred_contact_method' => 'in:email,phone,sms',
            // GDPR service agreement fields
            'service_agreement_number' => 'nullable|string|max:255',
            'service_start_date' => 'nullable|date',
            'service_end_date' => 'nullable|date|after_or_equal:service_start_date',
            'service_status' => 'nullable|in:active,expired,terminated,pending_renewal',
            'service_type' => 'nullable|in:gdpr_compliance,data_audit,dpo_services,training,consulting',
            // GDPR compliance tracking
            'compliance_score' => 'nullable|integer|min:0|max:100',
            'last_gdpr_audit_date' => 'nullable|date',
            'next_gdpr_audit_date' => 'nullable|date|after:last_gdpr_audit_date',
            'gdpr_maturity_level' => 'nullable|in:beginner,intermediate,advanced,expert',
            'risk_level' => 'nullable|in:low,medium,high,very_high',
            // GDPR service scope
            'gdpr_services_provided' => 'nullable|array',
            'gdpr_services_provided.*' => 'string|max:255',
            'gdpr_requirements' => 'nullable|string|max:1000',
            'applicable_regulations' => 'nullable|array',
            'applicable_regulations.*' => 'string|max:255',
            // Communication preferences for GDPR matters
            'gdpr_reporting_frequency' => 'nullable|in:monthly,quarterly,annually,on_demand',
            'gdpr_reporting_format' => 'nullable|in:pdf,excel,web_dashboard,email',
            'gdpr_reporting_recipients' => 'nullable|array',
            'gdpr_reporting_recipients.*' => 'email|max:255',
            // GDPR incident management
            'last_data_incident_date' => 'nullable|date',
            'data_incidents_count' => 'nullable|integer|min:0',
            'incident_response_plan' => 'nullable|string|max:1000',
            // GDPR training and awareness
            'last_gdpr_training_date' => 'nullable|date',
            'next_gdpr_training_date' => 'nullable|date|after:last_gdpr_training_date',
            'employees_trained_count' => 'nullable|integer|min:0',
            'gdpr_training_required' => 'boolean',
            // GDPR documentation
            'privacy_policy_updated' => 'boolean',
            'privacy_policy_last_updated' => 'nullable|date',
            'data_processing_register_maintained' => 'boolean',
            'data_breach_procedures_established' => 'boolean',
            'data_subject_rights_procedures_established' => 'boolean',
            // GDPR deadlines and reminders
            'upcoming_gdpr_deadlines' => 'nullable|array',
            'upcoming_gdpr_deadlines.*' => 'string|max:255',
            'next_review_date' => 'nullable|date',
            'gdpr_notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            return back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $filename = 'mandator_logos/' . uniqid('logo_') . '.' . $logo->getClientOriginalExtension();
            Storage::disk('public')->put($filename, file_get_contents($logo));
            $data['logo_url'] = Storage::url($filename);
        }

        $mandator = Mandator::create($data);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $mandator->load('company'),
                'message' => 'Mandator created successfully'
            ], 201);
        }

        return redirect()->route('mandators.index')
            ->with('success', 'Mandator created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Mandator $mandator, Request $request)
    {
        $mandator->load(['company', 'original', 'clones.company']);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $mandator,
                'message' => 'Mandator retrieved successfully'
            ]);
        }

        return view('mandators.show', compact('mandator'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Mandator $mandator)
    {
        $company = $mandator->company;

        // Get active disclosure types from database
        $disclosureTypes = \App\Models\DisclosureType::active()->ordered()->get();

        return view('mandators.edit', compact('mandator', 'company', 'disclosureTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Mandator $mandator)
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'sometimes|required|exists:companies,id',
            'agent_company_id' => 'nullable|exists:companies,id',
            'gdpr_representative_id' => 'nullable|exists:users,id',
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => [
                'sometimes',
                'required',
                'email',
                Rule::unique('mandators')->ignore($mandator->id)
            ],
            'phone' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'disclosure_subscriptions' => 'nullable|array',
            'disclosure_subscriptions.*' => 'string|max:255',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'preferred_contact_method' => 'in:email,phone,sms',
            // GDPR service agreement fields
            'service_agreement_number' => 'nullable|string|max:255',
            'service_start_date' => 'nullable|date',
            'service_end_date' => 'nullable|date|after_or_equal:service_start_date',
            'service_status' => 'nullable|in:active,expired,terminated,pending_renewal',
            'service_type' => 'nullable|in:gdpr_compliance,data_audit,dpo_services,training,consulting',
            // GDPR compliance tracking
            'compliance_score' => 'nullable|integer|min:0|max:100',
            'last_gdpr_audit_date' => 'nullable|date',
            'next_gdpr_audit_date' => 'nullable|date|after:last_gdpr_audit_date',
            'gdpr_maturity_level' => 'nullable|in:beginner,intermediate,advanced,expert',
            'risk_level' => 'nullable|in:low,medium,high,very_high',
            // GDPR service scope
            'gdpr_services_provided' => 'nullable|array',
            'gdpr_services_provided.*' => 'string|max:255',
            'gdpr_requirements' => 'nullable|string|max:1000',
            'applicable_regulations' => 'nullable|array',
            'applicable_regulations.*' => 'string|max:255',
            // Communication preferences for GDPR matters
            'gdpr_reporting_frequency' => 'nullable|in:monthly,quarterly,annually,on_demand',
            'gdpr_reporting_format' => 'nullable|in:pdf,excel,web_dashboard,email',
            'gdpr_reporting_recipients' => 'nullable|array',
            'gdpr_reporting_recipients.*' => 'email|max:255',
            // GDPR incident management
            'last_data_incident_date' => 'nullable|date',
            'data_incidents_count' => 'nullable|integer|min:0',
            'incident_response_plan' => 'nullable|string|max:1000',
            // GDPR training and awareness
            'last_gdpr_training_date' => 'nullable|date',
            'next_gdpr_training_date' => 'nullable|date|after:last_gdpr_training_date',
            'employees_trained_count' => 'nullable|integer|min:0',
            'gdpr_training_required' => 'boolean',
            // GDPR documentation
            'privacy_policy_updated' => 'boolean',
            'privacy_policy_last_updated' => 'nullable|date',
            'data_processing_register_maintained' => 'boolean',
            'data_breach_procedures_established' => 'boolean',
            'data_subject_rights_procedures_established' => 'boolean',
            // GDPR deadlines and reminders
            'upcoming_gdpr_deadlines' => 'nullable|array',
            'upcoming_gdpr_deadlines.*' => 'string|max:255',
            'next_review_date' => 'nullable|date',
            'gdpr_notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            return back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($mandator->logo_url && !str_contains($mandator->logo_url, 'ui-avatars.com')) {
                $oldLogoPath = str_replace('/storage/', '', $mandator->logo_url);
                Storage::disk('public')->delete($oldLogoPath);
            }

            $logo = $request->file('logo');
            $filename = 'mandator_logos/' . uniqid('logo_') . '.' . $logo->getClientOriginalExtension();
            Storage::disk('public')->put($filename, file_get_contents($logo));
            $data['logo_url'] = Storage::url($filename);
        }

        $mandator->update($data);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $mandator->load('company'),
                'message' => 'Mandator updated successfully'
            ]);
        }

        return redirect()->route('mandators.index')
            ->with('success', 'Mandator updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Mandator $mandator, Request $request)
    {
        // Delete logo file if exists
        if ($mandator->logo_url && !str_contains($mandator->logo_url, 'ui-avatars.com')) {
            $logoPath = str_replace('/storage/', '', $mandator->logo_url);
            Storage::disk('public')->delete($logoPath);
        }

        $mandator->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Mandator deleted successfully'
            ]);
        }

        return redirect()->route('mandators.index')
            ->with('success', 'Mandator deleted successfully.');
    }

    /**
     * Show the form for cloning a mandator to another company.
     */
    public function showCloneForm(Mandator $mandator)
    {
        // Check if user is superadmin
        if (Auth::user()->role !== 'superadmin') {
            abort(403, 'Only superadmins can clone mandators.');
        }

        $companies = Company::where('id', '!=', $mandator->company_id)->active()->get();
        return view('mandators.clone', compact('mandator', 'companies'));
    }

    /**
     * Clone a mandator to another company.
     */
    public function clone(Request $request, Mandator $mandator)
    {
        // Check if user is superadmin
        if (Auth::user()->role !== 'superadmin') {
            abort(403, 'Only superadmins can clone mandators.');
        }

        $validator = Validator::make($request->all(), [
            'target_company_id' => 'required|exists:companies,id',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $overrides = $validator->validated();
            unset($overrides['target_company_id']);

            $clonedMandator = $mandator->cloneToCompany(
                $request->target_company_id,
                $overrides
            );

            return redirect()->route('mandators.show', $clonedMandator)
                ->with('success', 'Mandator cloned successfully to ' . $clonedMandator->company->name);

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to clone mandator: ' . $e->getMessage()]);
        }
    }

    /**
     * Clone a mandator to multiple companies.
     */
    public function cloneToMultiple(Request $request, Mandator $mandator)
    {
        // Check if user is superadmin
        if (Auth::user()->role !== 'superadmin') {
            abort(403, 'Only superadmins can clone mandators.');
        }

        $validator = Validator::make($request->all(), [
            'target_company_ids' => 'required|array|min:1',
            'target_company_ids.*' => 'exists:companies,id',
            'overrides' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $clonedMandators = [];
            $overrides = $request->input('overrides', []);

            foreach ($request->target_company_ids as $companyId) {
                if ($companyId != $mandator->company_id) {
                    $clonedMandators[] = $mandator->cloneToCompany($companyId, $overrides);
                }
            }

            return response()->json([
                'success' => true,
                'data' => $clonedMandators,
                'message' => 'Mandator cloned to ' . count($clonedMandators) . ' companies successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clone mandator: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all clones of a mandator.
     */
    public function getClones(Mandator $mandator, Request $request)
    {
        $clones = $mandator->clones()->with('company')->get();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $clones,
                'message' => 'Clones retrieved successfully'
            ]);
        }

        return view('mandators.clones', compact('mandator', 'clones'));
    }

    /**
     * Get all related mandators (original + clones).
     */
    public function getRelated(Mandator $mandator, Request $request)
    {
        $related = $mandator->getAllRelated()->load('company');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $related,
                'message' => 'Related mandators retrieved successfully'
            ]);
        }

        return view('mandators.related', compact('mandator', 'related'));
    }

    /**
     * Add a disclosure subscription.
     */
    public function addDisclosureSubscription(Request $request, Mandator $mandator): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'disclosure_type' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $disclosureType = $request->disclosure_type;

        if ($mandator->isSubscribedTo($disclosureType)) {
            return response()->json([
                'success' => false,
                'message' => 'Mandator is already subscribed to this disclosure type'
            ], 400);
        }

        $mandator->addDisclosureSubscription($disclosureType);

        return response()->json([
            'success' => true,
            'message' => 'Disclosure subscription added successfully'
        ]);
    }

    /**
     * Remove a disclosure subscription.
     */
    public function removeDisclosureSubscription(Request $request, Mandator $mandator): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'disclosure_type' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $disclosureType = $request->disclosure_type;

        if (!$mandator->isSubscribedTo($disclosureType)) {
            return response()->json([
                'success' => false,
                'message' => 'Mandator is not subscribed to this disclosure type'
            ], 400);
        }

        $mandator->removeDisclosureSubscription($disclosureType);

        return response()->json([
            'success' => true,
            'message' => 'Disclosure subscription removed successfully'
        ]);
    }

    /**
     * Get disclosure summary statistics.
     */
    public function getDisclosureSummary(Request $request): JsonResponse
    {
        $query = Mandator::query();

        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        $mandators = $query->get();

        $summary = [
            'total_mandators' => $mandators->count(),
            'active_mandators' => $mandators->where('is_active', true)->count(),
            'total_subscriptions' => $mandators->sum(function ($rep) {
                return count($rep->disclosure_subscriptions ?? []);
            }),
            'subscription_types' => $mandators->flatMap(function ($rep) {
                return $rep->disclosure_subscriptions ?? [];
            })->unique()->values(),
            'mandators_with_subscriptions' => $mandators->filter(function ($rep) {
                return !empty($rep->disclosure_subscriptions);
            })->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $summary,
            'message' => 'Disclosure summary retrieved successfully'
        ]);
    }
}
