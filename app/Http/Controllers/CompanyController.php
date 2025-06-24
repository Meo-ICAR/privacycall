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
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Company::query();

            // Apply filters
            if ($request->has('type')) {
                $query->ofType($request->type);
            }

            if ($request->has('active')) {
                $query->active();
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('legal_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            // GDPR: Only return active companies with valid consent
            $query->where('is_active', true);

            $companies = $query->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $companies,
                'message' => 'Companies retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error retrieving companies: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving companies'
            ], 500);
        }
    }

    /**
     * Store a newly created company.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'legal_name' => 'nullable|string|max:255',
                'registration_number' => 'nullable|string|max:100|unique:companies',
                'vat_number' => 'nullable|string|max:100|unique:companies',
                'address_line_1' => 'required|string|max:255',
                'address_line_2' => 'nullable|string|max:255',
                'city' => 'required|string|max:100',
                'state' => 'nullable|string|max:100',
                'postal_code' => 'required|string|max:20',
                'country' => 'required|string|max:100',
                'phone' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'website' => 'nullable|url|max:255',
                'company_type' => 'required|in:employer,customer,supplier,partner',
                'industry' => 'nullable|string|max:100',
                'size' => 'required|in:small,medium,large',

                // GDPR Compliance Fields
                'gdpr_consent_date' => 'nullable|date',
                'data_retention_period' => 'nullable|integer|min:1',
                'data_processing_purpose' => 'nullable|string',
                'data_controller_contact' => 'nullable|string|max:255',
                'data_protection_officer' => 'nullable|string|max:255',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            $data = $validator->validated();

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

            $company = Company::create($data);

            // GDPR: Set default consent date if not provided
            if (!$company->gdpr_consent_date) {
                $company->update(['gdpr_consent_date' => now()]);
            }

            DB::commit();

            Log::info('Company created: ' . $company->id);

            return response()->json([
                'success' => true,
                'data' => $company,
                'message' => 'Company created successfully'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating company: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error creating company'
            ], 500);
        }
    }

    /**
     * Display the specified company.
     */
    public function show(Company $company): JsonResponse
    {
        try {
            // GDPR: Check if company has valid consent
            if (!$company->hasValidGdprConsent()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Company data access requires valid GDPR consent'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'data' => $company->load(['employees', 'customers', 'suppliers']),
                'message' => 'Company retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error retrieving company: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving company'
            ], 500);
        }
    }

    /**
     * Update the specified company.
     */
    public function update(Request $request, Company $company): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'legal_name' => 'nullable|string|max:255',
                'registration_number' => 'nullable|string|max:100|unique:companies,registration_number,' . $company->id,
                'vat_number' => 'nullable|string|max:100|unique:companies,vat_number,' . $company->id,
                'address_line_1' => 'sometimes|required|string|max:255',
                'address_line_2' => 'nullable|string|max:255',
                'city' => 'sometimes|required|string|max:100',
                'state' => 'nullable|string|max:100',
                'postal_code' => 'sometimes|required|string|max:20',
                'country' => 'sometimes|required|string|max:100',
                'phone' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'website' => 'nullable|url|max:255',
                'company_type' => 'sometimes|required|in:employer,customer,supplier,partner',
                'industry' => 'nullable|string|max:100',
                'size' => 'sometimes|required|in:small,medium,large',
                'is_active' => 'sometimes|boolean',

                // GDPR Compliance Fields
                'gdpr_consent_date' => 'nullable|date',
                'data_retention_period' => 'nullable|integer|min:1',
                'data_processing_purpose' => 'nullable|string',
                'data_controller_contact' => 'nullable|string|max:255',
                'data_protection_officer' => 'nullable|string|max:255',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            $data = $validator->validated();

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

            return response()->json([
                'success' => true,
                'data' => $company,
                'message' => 'Company updated successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating company: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating company'
            ], 500);
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
