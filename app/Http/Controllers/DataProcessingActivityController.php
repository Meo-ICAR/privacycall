<?php

namespace App\Http\Controllers;

use App\Models\DataProcessingActivity;
use Illuminate\Http\Request;

class DataProcessingActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dataProcessingActivities = DataProcessingActivity::all();
        return view('data_processing_activities.index', compact('dataProcessingActivities'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!auth()->user() || !auth()->user()->hasRole('superadmin')) {
            abort(403, 'Only superadmin can create data processing activities.');
        }
        return view('data_processing_activities.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!auth()->user() || !auth()->user()->hasRole('superadmin')) {
            abort(403, 'Only superadmin can create data processing activities.');
        }
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'activity_name' => 'required|string|max:255',
            'purpose' => 'required|string',
            'legal_basis' => 'required|string|max:255',
            'data_categories' => 'required|string',
            'recipients' => 'nullable|string',
            'retention_period' => 'required|string|max:255',
            'security_measures' => 'nullable|string',
            'status' => 'required|string|max:255',
        ]);

        DataProcessingActivity::create($validated);

        return redirect()->route('data-processing-activities.index')
            ->with('success', 'Data processing activity created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(DataProcessingActivity $dataProcessingActivity)
    {
        return view('data_processing_activities.show', compact('dataProcessingActivity'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DataProcessingActivity $dataProcessingActivity)
    {
        if (!auth()->user() || !auth()->user()->hasRole('superadmin')) {
            abort(403, 'Only superadmin can edit data processing activities.');
        }
        return view('data_processing_activities.edit', compact('dataProcessingActivity'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DataProcessingActivity $dataProcessingActivity)
    {
        if (!auth()->user() || !auth()->user()->hasRole('superadmin')) {
            abort(403, 'Only superadmin can update data processing activities.');
        }
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'activity_name' => 'required|string|max:255',
            'purpose' => 'required|string',
            'legal_basis' => 'required|string|max:255',
            'data_categories' => 'required|string',
            'recipients' => 'nullable|string',
            'retention_period' => 'required|string|max:255',
            'security_measures' => 'nullable|string',
            'status' => 'required|string|max:255',
        ]);

        $dataProcessingActivity->update($validated);

        return redirect()->route('data-processing-activities.index')
            ->with('success', 'Data processing activity updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DataProcessingActivity $dataProcessingActivity)
    {
        if (!auth()->user() || !auth()->user()->hasRole('superadmin')) {
            abort(403, 'Only superadmin can delete data processing activities.');
        }
        $dataProcessingActivity->delete();

        return redirect()->route('data-processing-activities.index')
            ->with('success', 'Data processing activity deleted successfully.');
    }
}
