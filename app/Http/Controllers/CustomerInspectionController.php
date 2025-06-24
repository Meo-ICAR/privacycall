<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomerInspection;
use App\Models\Employee;
use App\Models\Company;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;

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
        return view('customer_inspections.create', compact('company', 'customers', 'employees'));
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
            'employees' => 'array', // array of employee data
            'employees.*.id' => 'required|exists:employees,id',
            'employees.*.position' => 'nullable|string',
            'employees.*.hire_date' => 'nullable|date',
        ]);

        $inspection = CustomerInspection::create([
            'company_id' => $validated['company_id'],
            'customer_id' => $validated['customer_id'],
            'inspection_date' => $validated['inspection_date'],
            'notes' => $validated['notes'] ?? null,
            'status' => $validated['status'],
        ]);

        // Prepare attach data for employees
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

        return response()->json(['message' => 'Customer inspection created', 'inspection' => $inspection->load('employees')], 201);
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
