<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomerInspection;
use App\Models\Employee;
use App\Models\Company;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use App\Models\Supplier;
use App\Models\Document;

class CustomerInspectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        $company = $user->company;
        $customers = Customer::where('company_id', $company->id)->get();
        $employees = Employee::where('company_id', $company->id)->get();
        $suppliers = Supplier::where('company_id', $company->id)
            ->where('supplier_type', '!=', 'Individual')->get();
        // Fetch all documents related to the company
        $companyId = $company->id;
        $companyDocIds = Document::where('documentable_type', Company::class)
            ->where('documentable_id', $companyId)
            ->pluck('id');
        $customerIds = Customer::where('company_id', $companyId)->pluck('id');
        $customerDocIds = Document::where('documentable_type', Customer::class)
            ->whereIn('documentable_id', $customerIds)
            ->pluck('id');
        $employeeIds = Employee::where('company_id', $companyId)->pluck('id');
        $employeeDocIds = Document::where('documentable_type', Employee::class)
            ->whereIn('documentable_id', $employeeIds)
            ->pluck('id');
        $supplierIds = Supplier::where('company_id', $companyId)->pluck('id');
        $supplierDocIds = Document::where('documentable_type', Supplier::class)
            ->whereIn('documentable_id', $supplierIds)
            ->pluck('id');
        $allDocIds = $companyDocIds
            ->merge($customerDocIds)
            ->merge($employeeDocIds)
            ->merge($supplierDocIds)
            ->unique();
        $documents = Document::whereIn('id', $allDocIds)->get();
        return view('customer_inspections.create', compact('company', 'customers', 'employees', 'suppliers', 'documents'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'customer_id' => 'required|exists:customers,id',
            'inspection_date' => 'required|date',
            'notes' => 'nullable|string',
            'status' => 'required|string',
            'employees' => 'array',
            'employees.*.id' => 'required|exists:employees,id',
            'employees.*.position' => 'nullable|string',
            'employees.*.hire_date' => 'nullable|date',
            'suppliers' => 'array',
            'suppliers.*' => 'exists:suppliers,id',
            'documents' => 'array',
            'documents.*' => 'exists:documents,id',
            'new_documents' => 'array',
            'new_documents.*.file_name' => 'required|string',
            'new_documents.*.file_path' => 'required|string',
            'new_documents.*.mime_type' => 'required|string',
        ]);

        $inspection = CustomerInspection::create([
            'company_id' => $validated['company_id'],
            'customer_id' => $validated['customer_id'],
            'inspection_date' => $validated['inspection_date'],
            'notes' => $validated['notes'] ?? null,
            'status' => $validated['status'],
        ]);

        // Employees
        $attachData = [];
        if (!empty($validated['employees'])) {
            foreach ($validated['employees'] as $emp) {
                $attachData[$emp['id']] = [
                    'position' => $emp['position'] ?? null,
                    'hire_date' => $emp['hire_date'] ?? null,
                ];
            }
        }
        $inspection->employees()->sync($attachData);

        // Suppliers
        if (!empty($validated['suppliers'])) {
            $inspection->suppliers()->sync($validated['suppliers']);
        }

        // Documents (existing)
        if (!empty($validated['documents'])) {
            $inspection->documents()->sync($validated['documents']);
        }

        // New documents
        if (!empty($validated['new_documents'])) {
            foreach ($validated['new_documents'] as $docData) {
                $doc = Document::create([
                    'file_name' => $docData['file_name'],
                    'file_path' => $docData['file_path'],
                    'mime_type' => $docData['mime_type'],
                    'uploaded_by' => $request->user()->id,
                ]);
                $inspection->documents()->attach($doc->id);
            }
        }

        // Clone documents
        if (!empty($request->input('clone_documents'))) {
            foreach ($request->input('clone_documents') as $docId) {
                $original = Document::find($docId);
                if ($original) {
                    $cloned = $original->replicate();
                    $cloned->save();
                    $inspection->documents()->attach($cloned->id);
                }
            }
        }

        return response()->json(['message' => 'Customer inspection created', 'inspection' => $inspection->load(['employees', 'suppliers', 'documents'])], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
