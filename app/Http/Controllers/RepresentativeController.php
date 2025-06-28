<?php

namespace App\Http\Controllers;

use App\Models\Representative;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

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
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
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

        $data = $validator->validated();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $filename = 'representative_logos/' . uniqid('logo_') . '.' . $logo->getClientOriginalExtension();
            Storage::disk('public')->put($filename, file_get_contents($logo));
            $data['logo_url'] = Storage::url($filename);
        }

        $representative = Representative::create($data);

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
        $representative->load(['company', 'original', 'clones.company']);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $representative,
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
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
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

        $data = $validator->validated();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($representative->logo_url && !str_contains($representative->logo_url, 'ui-avatars.com')) {
                $oldLogoPath = str_replace('/storage/', '', $representative->logo_url);
                Storage::disk('public')->delete($oldLogoPath);
            }

            $logo = $request->file('logo');
            $filename = 'representative_logos/' . uniqid('logo_') . '.' . $logo->getClientOriginalExtension();
            Storage::disk('public')->put($filename, file_get_contents($logo));
            $data['logo_url'] = Storage::url($filename);
        }

        $representative->update($data);

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
        // Delete logo file if exists
        if ($representative->logo_url && !str_contains($representative->logo_url, 'ui-avatars.com')) {
            $logoPath = str_replace('/storage/', '', $representative->logo_url);
            Storage::disk('public')->delete($logoPath);
        }

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
     * Show the form for cloning a representative to another company.
     */
    public function showCloneForm(Representative $representative)
    {
        // Check if user is superadmin
        if (Auth::user()->role !== 'superadmin') {
            abort(403, 'Only superadmins can clone representatives.');
        }

        $companies = Company::where('id', '!=', $representative->company_id)->active()->get();
        return view('representatives.clone', compact('representative', 'companies'));
    }

    /**
     * Clone a representative to another company.
     */
    public function clone(Request $request, Representative $representative)
    {
        // Check if user is superadmin
        if (Auth::user()->role !== 'superadmin') {
            abort(403, 'Only superadmins can clone representatives.');
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

            $clonedRepresentative = $representative->cloneToCompany(
                $request->target_company_id,
                $overrides
            );

            return redirect()->route('representatives.show', $clonedRepresentative)
                ->with('success', 'Representative cloned successfully to ' . $clonedRepresentative->company->name);

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to clone representative: ' . $e->getMessage()]);
        }
    }

    /**
     * Clone a representative to multiple companies.
     */
    public function cloneToMultiple(Request $request, Representative $representative)
    {
        // Check if user is superadmin
        if (Auth::user()->role !== 'superadmin') {
            abort(403, 'Only superadmins can clone representatives.');
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
            $clonedRepresentatives = [];
            $overrides = $request->input('overrides', []);

            foreach ($request->target_company_ids as $companyId) {
                if ($companyId != $representative->company_id) {
                    $clonedRepresentatives[] = $representative->cloneToCompany($companyId, $overrides);
                }
            }

            return response()->json([
                'success' => true,
                'data' => $clonedRepresentatives,
                'message' => 'Representative cloned to ' . count($clonedRepresentatives) . ' companies successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clone representative: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all clones of a representative.
     */
    public function getClones(Representative $representative, Request $request)
    {
        $clones = $representative->clones()->with('company')->get();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $clones,
                'message' => 'Clones retrieved successfully'
            ]);
        }

        return view('representatives.clones', compact('representative', 'clones'));
    }

    /**
     * Get all related representatives (original + clones).
     */
    public function getRelated(Representative $representative, Request $request)
    {
        $related = $representative->getAllRelated()->load('company');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $related,
                'message' => 'Related representatives retrieved successfully'
            ]);
        }

        return view('representatives.related', compact('representative', 'related'));
    }

    /**
     * Add a disclosure subscription.
     */
    public function addDisclosureSubscription(Request $request, Representative $representative): JsonResponse
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

        if ($representative->isSubscribedTo($disclosureType)) {
            return response()->json([
                'success' => false,
                'message' => 'Representative is already subscribed to this disclosure type'
            ], 400);
        }

        $representative->addDisclosureSubscription($disclosureType);

        return response()->json([
            'success' => true,
            'message' => 'Disclosure subscription added successfully'
        ]);
    }

    /**
     * Remove a disclosure subscription.
     */
    public function removeDisclosureSubscription(Request $request, Representative $representative): JsonResponse
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

        if (!$representative->isSubscribedTo($disclosureType)) {
            return response()->json([
                'success' => false,
                'message' => 'Representative is not subscribed to this disclosure type'
            ], 400);
        }

        $representative->removeDisclosureSubscription($disclosureType);

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
        $query = Representative::query();

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
