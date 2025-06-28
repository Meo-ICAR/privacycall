<?php

namespace App\Http\Controllers;

use App\Models\ConsentRecord;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Supplier;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ConsentRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = ConsentRecord::with(['company', 'consentable'])
            ->where('company_id', $user->company_id);

        // Apply filters
        if ($request->filled('consent_type')) {
            $query->ofType($request->consent_type);
        }

        if ($request->filled('consent_status')) {
            $query->withStatus($request->consent_status);
        }

        if ($request->filled('consent_method')) {
            $query->withMethod($request->consent_method);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('consent_text', 'like', "%{$search}%")
                  ->orWhereHasMorph('consentable', [Customer::class, Employee::class, Supplier::class], function ($subQuery) use ($search) {
                      $subQuery->where('first_name', 'like', "%{$search}%")
                               ->orWhere('last_name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $consentRecords = $query->latest('consent_date')->paginate(20);

        // Get statistics
        $stats = [
            'total' => ConsentRecord::where('company_id', $user->company_id)->count(),
            'active' => ConsentRecord::where('company_id', $user->company_id)->valid()->count(),
            'expired' => ConsentRecord::where('company_id', $user->company_id)->expired()->count(),
            'withdrawn' => ConsentRecord::where('company_id', $user->company_id)->withdrawn()->count(),
        ];

        return view('consent_records.index', compact('consentRecords', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        $company = $user->company;

        // Get available entities for consent
        $customers = Customer::where('company_id', $company->id)->get();
        $employees = Employee::where('company_id', $company->id)->get();
        $suppliers = Supplier::where('company_id', $company->id)->get();

        return view('consent_records.create', compact('company', 'customers', 'employees', 'suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'consentable_type' => 'required|in:App\Models\Customer,App\Models\Employee,App\Models\Supplier',
            'consentable_id' => 'required|integer',
            'consent_type' => 'required|in:data_processing,marketing,third_party_sharing,data_retention,cookies,location_data,biometric_data',
            'consent_status' => 'required|in:granted,withdrawn,expired,pending',
            'consent_method' => 'required|in:web_form,email,phone,in_person,document,app_notification',
            'consent_source' => 'required|in:website,mobile_app,call_center,in_store,email_campaign,contract',
            'consent_channel' => 'required|in:online,offline,phone,email',
            'consent_date' => 'required|date',
            'withdrawal_date' => 'nullable|date|after:consent_date',
            'expiry_date' => 'nullable|date|after:consent_date',
            'consent_version' => 'nullable|string|max:50',
            'consent_text' => 'required|string|max:5000',
            'consent_language' => 'required|string|max:10',
            'consent_evidence' => 'nullable|in:screenshot,document,audio_recording,video_recording,email_confirmation,digital_signature',
            'consent_evidence_file' => 'nullable|file|max:10240', // 10MB
            'consent_notes' => 'nullable|string|max:1000',
            'ip_address' => 'nullable|ip',
            'user_agent' => 'nullable|string|max:500',
        ]);

        // Verify the consentable entity belongs to user's company
        $consentableClass = $validated['consentable_type'];
        $consentable = $consentableClass::find($validated['consentable_id']);

        if (!$consentable || $consentable->company_id !== $user->company_id) {
            return back()->withErrors(['consentable_id' => 'Invalid entity selected.']);
        }

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

        $validated['company_id'] = $user->company_id;
        $validated['is_active'] = true;

        ConsentRecord::create($validated);

        return redirect()->route('consent-records.index')
            ->with('success', 'Consent record created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ConsentRecord $consentRecord)
    {
        $user = Auth::user();

        if ($consentRecord->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        $consentRecord->load(['company', 'consentable']);

        return view('consent_records.show', compact('consentRecord'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ConsentRecord $consentRecord)
    {
        $user = Auth::user();

        if ($consentRecord->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        $company = $user->company;
        $customers = Customer::where('company_id', $company->id)->get();
        $employees = Employee::where('company_id', $company->id)->get();
        $suppliers = Supplier::where('company_id', $company->id)->get();

        return view('consent_records.edit', compact('consentRecord', 'company', 'customers', 'employees', 'suppliers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ConsentRecord $consentRecord)
    {
        $user = Auth::user();

        if ($consentRecord->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        $validated = $request->validate([
            'consentable_type' => 'required|in:App\Models\Customer,App\Models\Employee,App\Models\Supplier',
            'consentable_id' => 'required|integer',
            'consent_type' => 'required|in:data_processing,marketing,third_party_sharing,data_retention,cookies,location_data,biometric_data',
            'consent_status' => 'required|in:granted,withdrawn,expired,pending',
            'consent_method' => 'required|in:web_form,email,phone,in_person,document,app_notification',
            'consent_source' => 'required|in:website,mobile_app,call_center,in_store,email_campaign,contract',
            'consent_channel' => 'required|in:online,offline,phone,email',
            'consent_date' => 'required|date',
            'withdrawal_date' => 'nullable|date|after:consent_date',
            'expiry_date' => 'nullable|date|after:consent_date',
            'consent_version' => 'nullable|string|max:50',
            'consent_text' => 'required|string|max:5000',
            'consent_language' => 'required|string|max:10',
            'consent_evidence' => 'nullable|in:screenshot,document,audio_recording,video_recording,email_confirmation,digital_signature',
            'consent_evidence_file' => 'nullable|file|max:10240',
            'consent_notes' => 'nullable|string|max:1000',
        ]);

        // Handle file upload
        if ($request->hasFile('consent_evidence_file')) {
            // Delete old file if exists
            if ($consentRecord->consent_evidence_file) {
                Storage::delete($consentRecord->consent_evidence_file);
            }
            $path = $request->file('consent_evidence_file')->store('consent_evidence');
            $validated['consent_evidence_file'] = $path;
        }

        $consentRecord->update($validated);

        return redirect()->route('consent-records.show', $consentRecord)
            ->with('success', 'Consent record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ConsentRecord $consentRecord)
    {
        $user = Auth::user();

        if ($consentRecord->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        // Delete evidence file if exists
        if ($consentRecord->consent_evidence_file) {
            Storage::delete($consentRecord->consent_evidence_file);
        }

        $consentRecord->delete();

        return redirect()->route('consent-records.index')
            ->with('success', 'Consent record deleted successfully.');
    }

    /**
     * Download consent evidence file.
     */
    public function downloadEvidence(ConsentRecord $consentRecord)
    {
        $user = Auth::user();

        if ($consentRecord->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        if (!$consentRecord->consent_evidence_file || !Storage::exists($consentRecord->consent_evidence_file)) {
            abort(404, 'Evidence file not found.');
        }

        return Storage::download($consentRecord->consent_evidence_file);
    }

    /**
     * Withdraw consent.
     */
    public function withdraw(Request $request, ConsentRecord $consentRecord)
    {
        $user = Auth::user();

        if ($consentRecord->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        $validated = $request->validate([
            'withdrawal_reason' => 'nullable|string|max:500',
        ]);

        $consentRecord->update([
            'consent_status' => 'withdrawn',
            'withdrawal_date' => now(),
            'consent_notes' => $consentRecord->consent_notes . "\n\nWithdrawn: " . ($validated['withdrawal_reason'] ?? 'No reason provided'),
        ]);

        return back()->with('success', 'Consent withdrawn successfully.');
    }
}
