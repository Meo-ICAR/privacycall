<?php

namespace App\Http\Controllers;

use App\Exports\CustomersExport;
use App\Imports\CustomersImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $customers = Customer::where('company_id', $user->company_id)->get();
        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        $user = Auth::user();
        $companies = collect([$user->company]); // Only user's company
        return view('customers.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['company_id'] = $user->company_id;
        Customer::create($validated);

        return redirect()->route('customers.index')->with('success', 'Customer created successfully.');
    }

    public function show(Customer $customer)
    {
        $user = Auth::user();
        if ($customer->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }
        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        $user = Auth::user();
        if ($customer->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }
        $companies = collect([$user->company]); // Only user's company
        return view('customers.edit', compact('customer', 'companies'));
    }

    public function update(Request $request, Customer $customer)
    {
        $user = Auth::user();
        if ($customer->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['company_id'] = $user->company_id;
        $customer->update($validated);

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        $user = Auth::user();
        if ($customer->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
    }

    public function export()
    {
        $user = Auth::user();
        $customers = Customer::where('company_id', $user->company_id)->get();
        return Excel::download(new CustomersExport($customers), 'customers.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $user = Auth::user();
        Excel::import(new CustomersImport($user->company_id), $request->file('file'));
        return back()->with('success', 'Customers imported successfully.');
    }
}
