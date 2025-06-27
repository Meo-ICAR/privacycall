<?php

namespace App\Http\Controllers;

use App\Exports\CustomersExport;
use App\Imports\CustomersImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Customer;
use App\Models\CustomerType;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $customers = Customer::with(['company', 'customerType'])
            ->where('company_id', $user->company_id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get statistics
        $totalCustomers = $customers->count();
        $activeCustomers = $customers->where('is_active', true)->count();
        $inactiveCustomers = $customers->where('is_active', false)->count();

        return view('customers.index', compact('customers', 'totalCustomers', 'activeCustomers', 'inactiveCustomers'));
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
                return redirect()->route('customers.create')->with('error', 'Selected company not found.');
            }
            // Check if user has access to this company
            if ($selectedCompany->id !== $user->company_id) {
                abort(403, 'You can only create customers for your own company.');
            }
        }

        $companies = collect([$user->company]); // Only user's company
        $customerTypes = CustomerType::orderBy('name')->get();

        return view('customers.create', compact('companies', 'selectedCompany', 'customerTypes'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'customer_type_id' => 'nullable|exists:customer_types,id',
            'address_line_1' => 'nullable|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'customer_number' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $validated['company_id'] = $user->company_id;
        $validated['is_active'] = $request->has('is_active');

        Customer::create($validated);

        return redirect()->route('customers.index')->with('success', 'Customer created successfully.');
    }

    public function show(Customer $customer)
    {
        $user = Auth::user();
        if ($customer->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        $customer->load(['company', 'customerType']);
        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        $user = Auth::user();
        if ($customer->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        $companies = collect([$user->company]); // Only user's company
        $customerTypes = CustomerType::orderBy('name')->get();

        return view('customers.edit', compact('customer', 'companies', 'customerTypes'));
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
            'customer_type_id' => 'nullable|exists:customer_types,id',
            'address_line_1' => 'nullable|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'customer_number' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $validated['company_id'] = $user->company_id;
        $validated['is_active'] = $request->has('is_active');

        $customer->update($validated);

        return redirect()->route('customers.show', $customer)->with('success', 'Customer updated successfully.');
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
