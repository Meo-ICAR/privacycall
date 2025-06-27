<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class CompanyController extends Controller
{
    /**
     * Display a listing of companies.
     */
    public function index(Request $request)
    {
        $companies = \App\Models\Company::with('holding')->orderBy('name')->get();
        return view('companies.index', compact('companies'));
    }

    /**
     * Show the form for creating a new company.
     */
    public function create(Request $request)
    {
        // Check if user has permission to create companies
        if (!auth()->check() || (!auth()->user()->hasRole('admin') && !auth()->user()->hasRole('superadmin'))) {
            abort(403, 'You do not have permission to create companies.');
        }

        // Get holding_id from query parameter if provided
        $holding_id = $request->query('holding_id');
        $selectedHolding = null;

        if ($holding_id) {
            $selectedHolding = \App\Models\Holding::find($holding_id);
            if (!$selectedHolding) {
                return redirect()->route('companies.create')->with('error', 'Selected holding not found.');
            }
        }

        // Load holdings for the dropdown (only for superadmin)
        $holdings = null;
        if (auth()->user()->hasRole('superadmin')) {
            $holdings = \App\Models\Holding::orderBy('name')->get();
        }

        return view('companies.create', compact('holdings', 'selectedHolding'));
    }

    /**
     * Store a newly created company.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'type' => 'required|in:employer,customer,supplier,partner',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string',
                'notes' => 'nullable|string',
                'gdpr_compliant' => 'nullable|boolean',
                'data_retention_policy' => 'nullable|string',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'holding_id' => 'nullable|exists:holdings,id',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            DB::beginTransaction();

            $data = $validator->validated();

            // Map form fields to database fields
            $companyData = [
                'name' => $data['name'],
                'company_type' => $data['type'],
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'address_line_1' => $data['address'] ?? null,
                'notes' => $data['notes'] ?? null,
                'is_active' => true,
                'size' => 'medium', // Default value
                'country' => 'Unknown', // Default value
                'city' => 'Unknown', // Default value
                'postal_code' => '00000', // Default value
            ];

            // Handle GDPR compliance
            if (isset($data['gdpr_compliant']) && $data['gdpr_compliant']) {
                $companyData['gdpr_consent_date'] = now();
                $companyData['data_retention_period'] = $this->parseRetentionPolicy($data['data_retention_policy'] ?? '7 years');
            }

            // Only superadmin can set holding_id
            if (auth()->user() && auth()->user()->hasRole('superadmin') && isset($data['holding_id'])) {
                $companyData['holding_id'] = $data['holding_id'];
            }

            // Handle logo upload
            if ($request->hasFile('logo')) {
                $image = $request->file('logo');
                $filename = 'company_logos/' . uniqid('logo_') . '.' . $image->getClientOriginalExtension();
                $resized = Image::make($image)->resize(256, 256, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->encode();
                Storage::disk('public')->put($filename, $resized);
                $companyData['logo_url'] = Storage::url($filename);
            }

            $company = Company::create($companyData);

            DB::commit();

            Log::info('Company created: ' . $company->id);

            return redirect()->route('companies.index')->with('success', 'Company created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating company: ' . $e->getMessage());
            return back()->with('error', 'Error creating company: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Parse retention policy string to number of years
     */
    private function parseRetentionPolicy($policy)
    {
        if (preg_match('/(\d+)\s*year/', $policy, $matches)) {
            return (int) $matches[1];
        }
        return 7; // Default to 7 years
    }

    /**
     * Display the specified company.
     */
    public function show(Company $company)
    {
        // Check if user has access to this company
        if (auth()->check() && auth()->user()->company_id && auth()->user()->company_id !== $company->id) {
            abort(403, 'You can only view your own company.');
        }

        // Load relationships
        $company->load(['holding', 'employees', 'customers', 'suppliers']);

        return view('companies.show', compact('company'));
    }

    /**
     * Show the form for editing the specified company.
     */
    public function edit(Company $company)
    {
        // Check if user has access to this company
        if (auth()->check() && auth()->user()->company_id && auth()->user()->company_id !== $company->id) {
            abort(403, 'You can only edit your own company.');
        }

        // Check if user has permission to edit
        if (!auth()->check() || (!auth()->user()->hasRole('admin') && !auth()->user()->hasRole('superadmin'))) {
            abort(403, 'You do not have permission to edit companies.');
        }

        // Load holdings for the dropdown (only for superadmin)
        $holdings = null;
        if (auth()->user()->hasRole('superadmin')) {
            $holdings = \App\Models\Holding::orderBy('name')->get();
        }

        return view('companies.edit', compact('company', 'holdings'));
    }

    /**
     * Update the specified company.
     */
    public function update(Request $request, Company $company)
    {
        // Check if user has access to this company
        if (auth()->check() && auth()->user()->company_id && auth()->user()->company_id !== $company->id) {
            abort(403, 'You can only update your own company.');
        }

        // Check if user has permission to edit
        if (!auth()->check() || (!auth()->user()->hasRole('admin') && !auth()->user()->hasRole('superadmin'))) {
            abort(403, 'You do not have permission to edit companies.');
        }

        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'legal_name' => 'nullable|string|max:255',
                'registration_number' => 'nullable|string|max:100|unique:companies,registration_number,' . $company->id,
                'vat_number' => 'nullable|string|max:100|unique:companies,vat_number,' . $company->id,
                'address_line_1' => 'nullable|string|max:255',
                'address_line_2' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:100',
                'state' => 'nullable|string|max:100',
                'postal_code' => 'nullable|string|max:20',
                'country' => 'nullable|string|max:100',
                'phone' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'website' => 'nullable|url|max:255',
                'company_type' => 'nullable|in:employer,customer,supplier,partner',
                'industry' => 'nullable|string|max:100',
                'size' => 'nullable|in:small,medium,large',
                'is_active' => 'nullable|boolean',

                // GDPR Compliance Fields
                'gdpr_consent_date' => 'nullable|date',
                'data_retention_period' => 'nullable|integer|min:1',
                'data_processing_purpose' => 'nullable|string',
                'data_controller_contact' => 'nullable|string|max:255',
                'data_protection_officer' => 'nullable|string|max:255',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'holding_id' => 'nullable|exists:holdings,id',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            DB::beginTransaction();

            $data = $validator->validated();

            // Only superadmin can set holding_id
            if (!auth()->user() || !auth()->user()->hasRole('superadmin')) {
                unset($data['holding_id']);
            }

            // Handle logo upload
            if ($request->hasFile('logo')) {
                $image = $request->file('logo');
                $filename = 'company_logos/' . uniqid('logo_') . '.' . $image->getClientOriginalExtension();
                $resized = Image::make($image)->resize(256, 256, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->encode();
                Storage::disk('public')->put($filename, $resized);
                $data['logo_url'] = Storage::url($filename);
            }

            $company->update($data);

            DB::commit();

            Log::info('Company updated: ' . $company->id);

            return redirect()->route('companies.show', $company)->with('success', 'Company updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating company: ' . $e->getMessage());
            return back()->with('error', 'Error updating company: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified company (soft delete).
     */
    public function destroy(Company $company): JsonResponse
    {
        try {
            DB::beginTransaction();

            // GDPR: Check if company has requested right to be forgotten
            if ($company->hasRequestedRightToBeForgotten()) {
                // Perform hard delete for right to be forgotten
                $company->forceDelete();
                Log::info('Company permanently deleted due to right to be forgotten: ' . $company->id);
            } else {
                // Perform soft delete
                $company->delete();
                Log::info('Company soft deleted: ' . $company->id);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Company deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting company: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting company'
            ], 500);
        }
    }

    /**
     * Get GDPR compliance status for a company.
     */
    public function gdprStatus(Company $company): JsonResponse
    {
        try {
            $status = [
                'has_valid_consent' => $company->hasValidGdprConsent(),
                'consent_date' => $company->gdpr_consent_date,
                'data_retention_period' => $company->data_retention_period,
                'data_processing_purpose' => $company->data_processing_purpose,
                'data_controller_contact' => $company->data_controller_contact,
                'data_protection_officer' => $company->data_protection_officer,
                'employees_count' => $company->employees()->count(),
                'customers_count' => $company->customers()->count(),
                'suppliers_count' => $company->suppliers()->count(),
                'data_processing_activities_count' => $company->dataProcessingActivities()->count(),
                'consent_records_count' => $company->consentRecords()->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $status,
                'message' => 'GDPR status retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error retrieving GDPR status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving GDPR status'
            ], 500);
        }
    }
}
