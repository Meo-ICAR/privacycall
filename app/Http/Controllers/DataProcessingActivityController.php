<?php

namespace App\Http\Controllers;

use App\Models\DataProcessingActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DataProcessingActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $activities = DataProcessingActivity::where('company_id', $user->company_id)->with('company')->get();
        return view('data_processing_activities.index', compact('activities'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        $company = $user->company;

        if (!$company) {
            return redirect()->route('data-processing-activities.index')
                ->with('error', 'You must be assigned to a company to create data processing activities.');
        }

        return view('data_processing_activities.create', compact('company'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->company) {
            return redirect()->route('data-processing-activities.index')
                ->with('error', 'You must be assigned to a company to create data processing activities.');
        }

        $validated = $request->validate([
            'activity_name' => 'required|string|max:255',
            'activity_description' => 'nullable|string',
            'processing_purpose' => 'required|string|max:255',
            'legal_basis' => 'required|string|max:255',
            'data_categories' => 'required|string|max:255',
            'data_subjects' => 'nullable|string|max:255',
            'data_recipients' => 'nullable|string|max:255',
            'retention_period' => 'required|string|max:255',
            'security_measures' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['company_id'] = $user->company_id;
        $validated['is_active'] = $request->has('is_active');

        DataProcessingActivity::create($validated);

        return redirect()->route('data-processing-activities.index')
            ->with('success', 'Data processing activity created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(DataProcessingActivity $dataProcessingActivity)
    {
        $user = Auth::user();
        if ($dataProcessingActivity->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }
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
        $user = Auth::user();
        if ($dataProcessingActivity->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
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
        $user = Auth::user();
        if ($dataProcessingActivity->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        $validated = $request->validate([
            'activity_name' => 'required|string|max:255',
            'processing_purpose' => 'required|string',
            'legal_basis' => 'required|string|max:255',
            'data_categories' => 'required|string',
            'data_recipients' => 'nullable|string',
            'retention_period' => 'required|string|max:255',
            'security_measures' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['company_id'] = $user->company_id;
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
        $user = Auth::user();
        if ($dataProcessingActivity->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        $dataProcessingActivity->delete();

        return redirect()->route('data-processing-activities.index')
            ->with('success', 'Data processing activity deleted successfully.');
    }
}
