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

            // GDPR Compliance Fields
            'gdpr_consent_date' => 'nullable|date',
            'data_processing_consent' => 'boolean',
            'marketing_consent' => 'boolean',
            'third_party_sharing_consent' => 'boolean',
            'data_retention_consent' => 'boolean',
            'right_to_be_forgotten_requested' => 'boolean',
            'right_to_be_forgotten_date' => 'nullable|date',
            'data_portability_requested' => 'boolean',
            'data_portability_date' => 'nullable|date',
            'data_processing_purpose' => 'nullable|string|max:500',
            'data_retention_period' => 'nullable|integer|min:0',

            // Consent Acquisition Fields
            'consent_method' => 'nullable|in:web_form,email,phone,in_person,document,app_notification',
            'consent_source' => 'nullable|in:website,mobile_app,call_center,in_store,email_campaign,contract',
            'consent_channel' => 'nullable|in:online,offline,phone,email',
            'consent_evidence' => 'nullable|in:screenshot,document,audio_recording,video_recording,email_confirmation,digital_signature',
            'consent_evidence_file' => 'nullable|file|max:10240', // 10MB
            'consent_text' => 'nullable|string|max:5000',
            'consent_language' => 'nullable|string|max:10',
            'consent_version' => 'nullable|string|max:50',
            'ip_address' => 'nullable|ip',
            'user_agent' => 'nullable|string|max:500',
        ]);

        $validated['company_id'] = $user->company_id;
        $validated['is_active'] = $request->has('is_active');

        // Handle GDPR boolean fields
        $validated['data_processing_consent'] = $request->has('data_processing_consent');
        $validated['marketing_consent'] = $request->has('marketing_consent');
        $validated['third_party_sharing_consent'] = $request->has('third_party_sharing_consent');
        $validated['data_retention_consent'] = $request->has('data_retention_consent');
        $validated['right_to_be_forgotten_requested'] = $request->has('right_to_be_forgotten_requested');
        $validated['data_portability_requested'] = $request->has('data_portability_requested');

        // Handle file upload
        if ($request->hasFile('consent_evidence_file')) {
            $path = $request->file('consent_evidence_file')->store('consent_evidence');
            $validated['consent_evidence_file'] = $path;
        }

        // Set IP address and user agent if not provided
        if (empty($validated['ip_address'])) {
            $validated['ip_address'] = $request->ip();
        }
        if (empty($validated['user_agent'])) {
            $validated['user_agent'] = $request->userAgent();
        }

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

            // GDPR Compliance Fields
            'gdpr_consent_date' => 'nullable|date',
            'data_processing_consent' => 'boolean',
            'marketing_consent' => 'boolean',
            'third_party_sharing_consent' => 'boolean',
            'data_retention_consent' => 'boolean',
            'right_to_be_forgotten_requested' => 'boolean',
            'right_to_be_forgotten_date' => 'nullable|date',
            'data_portability_requested' => 'boolean',
            'data_portability_date' => 'nullable|date',
            'data_processing_purpose' => 'nullable|string|max:500',
            'data_retention_period' => 'nullable|integer|min:0',

            // Consent Acquisition Fields
            'consent_method' => 'nullable|in:web_form,email,phone,in_person,document,app_notification',
            'consent_source' => 'nullable|in:website,mobile_app,call_center,in_store,email_campaign,contract',
            'consent_channel' => 'nullable|in:online,offline,phone,email',
            'consent_evidence' => 'nullable|in:screenshot,document,audio_recording,video_recording,email_confirmation,digital_signature',
            'consent_evidence_file' => 'nullable|file|max:10240', // 10MB
            'consent_text' => 'nullable|string|max:5000',
            'consent_language' => 'nullable|string|max:10',
            'consent_version' => 'nullable|string|max:50',
            'ip_address' => 'nullable|ip',
            'user_agent' => 'nullable|string|max:500',
        ]);

        $validated['company_id'] = $user->company_id;
        $validated['is_active'] = $request->has('is_active');

        // Handle GDPR boolean fields
        $validated['data_processing_consent'] = $request->has('data_processing_consent');
        $validated['marketing_consent'] = $request->has('marketing_consent');
        $validated['third_party_sharing_consent'] = $request->has('third_party_sharing_consent');
        $validated['data_retention_consent'] = $request->has('data_retention_consent');
        $validated['right_to_be_forgotten_requested'] = $request->has('right_to_be_forgotten_requested');
        $validated['data_portability_requested'] = $request->has('data_portability_requested');

        // Handle file upload
        if ($request->hasFile('consent_evidence_file')) {
            $path = $request->file('consent_evidence_file')->store('consent_evidence');
            $validated['consent_evidence_file'] = $path;
        }

        // Set IP address and user agent if not provided
        if (empty($validated['ip_address'])) {
            $validated['ip_address'] = $request->ip();
        }
        if (empty($validated['user_agent'])) {
            $validated['user_agent'] = $request->userAgent();
        }

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
