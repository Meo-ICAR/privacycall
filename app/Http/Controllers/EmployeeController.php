<?php

namespace App\Http\Controllers;

use App\Exports\EmployeesExport;
use App\Imports\EmployeesImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Debug information
        \Log::info('EmployeeController index called', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'company_id' => $user->company_id,
            'is_impersonating' => session('impersonate_original_id') ? true : false,
            'impersonate_original_id' => session('impersonate_original_id')
        ]);

        // Ensure user has a company_id
        if (!$user->company_id) {
            \Log::warning('User has no company_id', ['user_id' => $user->id]);
            return redirect()->back()->with('error', 'Your account is not associated with any company.');
        }

        $employees = Employee::where('company_id', $user->company_id)->get();

        \Log::info('Employees found', [
            'company_id' => $user->company_id,
            'employee_count' => $employees->count()
        ]);

        return view('employees.index', compact('employees'));
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
                return redirect()->route('employees.create')->with('error', 'Selected company not found.');
            }
            // Check if user has access to this company
            if ($selectedCompany->id !== $user->company_id) {
                abort(403, 'You can only create employees for your own company.');
            }
        }

        $companies = collect([$user->company]); // Only user's company
        return view('employees.create', compact('companies', 'selectedCompany'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'position' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'hire_date' => 'nullable|date',
            'salary' => 'nullable|numeric|min:0',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['company_id'] = $user->company_id;
        $validated['is_active'] = $request->has('is_active');

        Employee::create($validated);

        return redirect()->route('employees.index')->with('success', 'Employee created successfully.');
    }

    public function show(Employee $employee)
    {
        $user = Auth::user();
        if ($employee->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }
        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $user = Auth::user();
        if ($employee->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }
        $companies = collect([$user->company]); // Only user's company
        return view('employees.edit', compact('employee', 'companies'));
    }

    public function update(Request $request, Employee $employee)
    {
        $user = Auth::user();
        if ($employee->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'position' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'hire_date' => 'nullable|date',
            'salary' => 'nullable|numeric|min:0',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['company_id'] = $user->company_id;
        $validated['is_active'] = $request->has('is_active');

        $employee->update($validated);

        return redirect()->route('employees.show', $employee)->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        $user = Auth::user();
        if ($employee->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully.');
    }

    public function export()
    {
        $user = Auth::user();
        $employees = Employee::where('company_id', $user->company_id)->get();
        return Excel::download(new EmployeesExport($employees), 'employees.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $user = Auth::user();
        Excel::import(new EmployeesImport($user->company_id), $request->file('file'));
        return back()->with('success', 'Employees imported successfully.');
    }
}
