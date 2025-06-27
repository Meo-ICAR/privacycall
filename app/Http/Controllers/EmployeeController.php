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
        $employees = Employee::where('company_id', $user->company_id)->get();
        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        $user = Auth::user();
        $companies = collect([$user->company]); // Only user's company
        return view('employees.create', compact('companies'));
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
            'hire_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $validated['company_id'] = $user->company_id;
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
            'hire_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $validated['company_id'] = $user->company_id;
        $employee->update($validated);

        return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
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
