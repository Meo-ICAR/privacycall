<?php

namespace App\Http\Controllers;

use App\Models\Representative;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class RepresentativeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Representative::with('company');

        // Filter by company
        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
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

        $representatives = $query->paginate($request->get('per_page', 15));
        $companies = Company::active()->get();

        // Return JSON for API requests, view for web requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $representatives,
                'message' => 'Representatives retrieved successfully'
            ]);
        }

        return view('representatives.index', compact('representatives', 'companies'));
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
        return view('representatives.create', compact('companies', 'company'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'required|exists:companies,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:representatives,email',
            'phone' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'disclosure_subscriptions' => 'nullable|array',
            'disclosure_subscriptions.*' => 'string|max:255',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'preferred_contact_method' => 'in:email,phone,sms',
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

        $representative = Representative::create($validator->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $representative->load('company'),
                'message' => 'Representative created successfully'
            ], 201);
        }

        return redirect()->route('representatives.index')
            ->with('success', 'Representative created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Representative $representative, Request $request)
    {
        $representative->load('company');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $representative,
                'disclosure_summary' => $representative->disclosure_summary,
                'message' => 'Representative retrieved successfully'
            ]);
        }

        return view('representatives.show', compact('representative'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Representative $representative)
    {
        $company = $representative->company;
        return view('representatives.edit', compact('representative', 'company'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Representative $representative)
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'sometimes|required|exists:companies,id',
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => [
                'sometimes',
                'required',
                'email',
                Rule::unique('representatives')->ignore($representative->id)
            ],
            'phone' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'disclosure_subscriptions' => 'nullable|array',
            'disclosure_subscriptions.*' => 'string|max:255',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'preferred_contact_method' => 'in:email,phone,sms',
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

        $representative->update($validator->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $representative->load('company'),
                'message' => 'Representative updated successfully'
            ]);
        }

        return redirect()->route('representatives.index')
            ->with('success', 'Representative updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Representative $representative, Request $request)
    {
        $representative->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Representative deleted successfully'
            ]);
        }

        return redirect()->route('representatives.index')
            ->with('success', 'Representative deleted successfully.');
    }

    /**
     * Add disclosure subscription to representative.
     */
    public function addDisclosureSubscription(Request $request, Representative $representative): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'disclosure_type' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $disclosureType = $request->disclosure_type;

        if ($representative->isSubscribedTo($disclosureType)) {
            return response()->json([
                'success' => false,
                'message' => 'Representative is already subscribed to this disclosure type'
            ], 400);
        }

        $representative->addDisclosureSubscription($disclosureType);

        return response()->json([
            'success' => true,
            'data' => $representative->load('company'),
            'disclosure_summary' => $representative->disclosure_summary,
            'message' => 'Disclosure subscription added successfully'
        ]);
    }

    /**
     * Remove disclosure subscription from representative.
     */
    public function removeDisclosureSubscription(Request $request, Representative $representative): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'disclosure_type' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $disclosureType = $request->disclosure_type;

        if (!$representative->isSubscribedTo($disclosureType)) {
            return response()->json([
                'success' => false,
                'message' => 'Representative is not subscribed to this disclosure type'
            ], 400);
        }

        $representative->removeDisclosureSubscription($disclosureType);

        return response()->json([
            'success' => true,
            'data' => $representative->load('company'),
            'disclosure_summary' => $representative->disclosure_summary,
            'message' => 'Disclosure subscription removed successfully'
        ]);
    }

    /**
     * Update last disclosure date.
     */
    public function updateLastDisclosureDate(Representative $representative): JsonResponse
    {
        $representative->updateLastDisclosureDate();

        return response()->json([
            'success' => true,
            'data' => $representative->load('company'),
            'disclosure_summary' => $representative->disclosure_summary,
            'message' => 'Last disclosure date updated successfully'
        ]);
    }

    /**
     * Get representatives by company.
     */
    public function getByCompany(Company $company): JsonResponse
    {
        $representatives = $company->representatives()
            ->active()
            ->with('company')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $representatives,
            'message' => 'Company representatives retrieved successfully'
        ]);
    }

    /**
     * Get disclosure summary for all representatives.
     */
    public function getDisclosureSummary(Request $request): JsonResponse
    {
        $query = Representative::with('company');

        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        $representatives = $query->get();

        $summary = [
            'total_representatives' => $representatives->count(),
            'active_representatives' => $representatives->where('is_active', true)->count(),
            'total_subscriptions' => $representatives->sum(function ($rep) {
                return count($rep->disclosure_subscriptions ?? []);
            }),
            'subscription_types' => $representatives->flatMap(function ($rep) {
                return $rep->disclosure_subscriptions ?? [];
            })->unique()->values(),
            'representatives_with_subscriptions' => $representatives->filter(function ($rep) {
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
