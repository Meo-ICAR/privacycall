<?php

namespace App\Http\Controllers;

use App\Models\ConsentRecord;
use Illuminate\Http\Request;

class ConsentRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $consentRecords = ConsentRecord::all();
        return view('consent_records.index', compact('consentRecords'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!auth()->user() || !auth()->user()->hasRole('superadmin')) {
            abort(403, 'Only superadmin can create consent records.');
        }
        return view('consent_records.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!auth()->user() || !auth()->user()->hasRole('superadmin')) {
            abort(403, 'Only superadmin can create consent records.');
        }
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'consent_type' => 'required|string|max:255',
            'consent_status' => 'required|string|max:255',
            'consent_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:consent_date',
            'notes' => 'nullable|string',
        ]);
        $validated['company_id'] = auth()->user()->company_id;
        ConsentRecord::create($validated);
        return redirect()->route('consent-records.index')
            ->with('success', 'Consent record created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ConsentRecord $consentRecord)
    {
        return view('consent_records.show', compact('consentRecord'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ConsentRecord $consentRecord)
    {
        if (!auth()->user() || !auth()->user()->hasRole('superadmin')) {
            abort(403, 'Only superadmin can edit consent records.');
        }
        return view('consent_records.edit', ['record' => $consentRecord]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ConsentRecord $consentRecord)
    {
        if (!auth()->user() || !auth()->user()->hasRole('superadmin')) {
            abort(403, 'Only superadmin can update consent records.');
        }
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'consent_type' => 'required|string|max:255',
            'consent_status' => 'required|string|max:255',
            'consent_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:consent_date',
            'notes' => 'nullable|string',
        ]);
        $validated['company_id'] = auth()->user()->company_id;
        $consentRecord->update($validated);
        return redirect()->route('consent-records.index')
            ->with('success', 'Consent record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ConsentRecord $consentRecord)
    {
        if (!auth()->user() || !auth()->user()->hasRole('superadmin')) {
            abort(403, 'Only superadmin can delete consent records.');
        }
        $consentRecord->delete();

        return redirect()->route('consent-records.index')
            ->with('success', 'Consent record deleted successfully.');
    }
}
