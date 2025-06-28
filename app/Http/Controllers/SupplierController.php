<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use App\Exports\SuppliersExport;
use App\Imports\SuppliersImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $suppliers = Supplier::where('company_id', $user->company_id)->get();
        return view('suppliers.index', compact('suppliers'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();

        // Get company_id from query parameter if provided
        $company_id = $request->query('company_id');
        $selectedCompany = null;

        if ($company_id) {
            $selectedCompany = \App\Models\Company::find($company_id);
            if (!$selectedCompany) {
                return redirect()->route('suppliers.create')->with('error', 'Selected company not found.');
            }
            // Check if user has access to this company
            if ($selectedCompany->id !== $user->company_id) {
                abort(403, 'You can only create suppliers for your own company.');
            }
        }

        $companies = collect([$user->company]); // Only user's company
        $supplierTypes = \App\Models\SupplierType::all();
        return view('suppliers.create', compact('companies', 'supplierTypes', 'selectedCompany'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'supplier_type_id' => 'required|exists:supplier_types,id',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $data = $validator->validated();
            $data['company_id'] = $user->company_id;

            if ($request->hasFile('logo')) {
                $image = $request->file('logo');
                $filename = 'supplier_logos/' . uniqid('logo_') . '.' . $image->getClientOriginalExtension();
                $resized = Image::make($image)->resize(256, 256, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->encode();
                Storage::disk('public')->put($filename, $resized);
                $data['logo_url'] = Storage::url($filename);
            }

            $supplier = Supplier::create($data);
            DB::commit();
            return redirect()->route('suppliers.show', $supplier->id)->with('success', 'Supplier created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating supplier: ' . $e->getMessage());
            return back()->with('error', 'Error creating supplier.');
        }
    }

    public function show(Supplier $supplier)
    {
        $user = Auth::user();
        if ($supplier->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }
        return view('suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        $user = Auth::user();
        if ($supplier->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }
        $companies = collect([$user->company]); // Only user's company
        $supplierTypes = \App\Models\SupplierType::all();
        return view('suppliers.edit', compact('supplier', 'companies', 'supplierTypes'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $user = Auth::user();
        if ($supplier->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'supplier_number' => 'nullable|string|max:255',
            'legal_name' => 'nullable|string|max:255',
            'registration_number' => 'nullable|string|max:255',
            'vat_number' => 'nullable|string|max:255',
            'contact_person_name' => 'nullable|string|max:255',
            'contact_person_email' => 'nullable|email|max:255',
            'contact_person_phone' => 'nullable|string|max:20',
            'address_line_1' => 'nullable|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'supplier_type_id' => 'required|exists:supplier_types,id',
            'supplier_category' => 'nullable|string|in:primary,secondary,emergency',
            'supplier_status' => 'nullable|string|in:active,inactive,suspended,approved,pending',
            'supplier_since' => 'nullable|date',
            'last_order_date' => 'nullable|date',
            'total_orders' => 'nullable|integer|min:0',
            'total_spent' => 'nullable|numeric|min:0',
            'payment_terms' => 'nullable|string|max:255',
            'credit_limit' => 'nullable|numeric|min:0',
            'gdpr_consent_date' => 'nullable|date',
            'data_processing_consent' => 'boolean',
            'third_party_sharing_consent' => 'boolean',
            'data_retention_consent' => 'boolean',
            'data_processing_agreement_signed' => 'boolean',
            'data_processing_agreement_date' => 'nullable|date',
            'data_processing_purpose' => 'nullable|string',
            'data_retention_period' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $data = $validator->validated();

            // Handle boolean fields
            $data['data_processing_consent'] = $request->has('data_processing_consent');
            $data['third_party_sharing_consent'] = $request->has('third_party_sharing_consent');
            $data['data_retention_consent'] = $request->has('data_retention_consent');
            $data['data_processing_agreement_signed'] = $request->has('data_processing_agreement_signed');
            $data['is_active'] = $request->has('is_active');

            // Handle date fields
            if (empty($data['supplier_since'])) {
                $data['supplier_since'] = null;
            }
            if (empty($data['last_order_date'])) {
                $data['last_order_date'] = null;
            }
            if (empty($data['gdpr_consent_date'])) {
                $data['gdpr_consent_date'] = null;
            }
            if (empty($data['data_processing_agreement_date'])) {
                $data['data_processing_agreement_date'] = null;
            }

            // Handle numeric fields
            if (empty($data['total_orders'])) {
                $data['total_orders'] = null;
            }
            if (empty($data['total_spent'])) {
                $data['total_spent'] = null;
            }
            if (empty($data['credit_limit'])) {
                $data['credit_limit'] = null;
            }
            if (empty($data['data_retention_period'])) {
                $data['data_retention_period'] = null;
            }

            // Ensure company_id is set (for admin users it's hidden)
            if (!auth()->user()->hasRole('superadmin')) {
                $data['company_id'] = $user->company_id;
            }

            if ($request->hasFile('logo')) {
                $image = $request->file('logo');
                $filename = 'supplier_logos/' . uniqid('logo_') . '.' . $image->getClientOriginalExtension();
                $resized = Image::make($image)->resize(256, 256, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->encode();
                Storage::disk('public')->put($filename, $resized);
                $data['logo_url'] = Storage::url($filename);
            }

            $supplier->update($data);
            DB::commit();
            return redirect()->route('suppliers.show', $supplier->id)->with('success', 'Supplier updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating supplier: ' . $e->getMessage());
            return back()->with('error', 'Error updating supplier.');
        }
    }

    public function destroy(Supplier $supplier)
    {
        $user = Auth::user();
        if ($supplier->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        $supplier->delete();
        return redirect()->route('suppliers.index')->with('success', 'Supplier deleted successfully.');
    }

    public function export()
    {
        $user = Auth::user();
        $suppliers = Supplier::where('company_id', $user->company_id)->get();
        return Excel::download(new SuppliersExport($suppliers), 'suppliers.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $user = Auth::user();
        Excel::import(new SuppliersImport($user->company_id), $request->file('file'));
        return back()->with('success', 'Suppliers imported successfully.');
    }
}
