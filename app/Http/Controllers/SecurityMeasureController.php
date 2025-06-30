<?php

namespace App\Http\Controllers;

use App\Models\SecurityMeasure;
use Illuminate\Http\Request;

class SecurityMeasureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $securityMeasures = SecurityMeasure::all();
        return view('security-measures.index', compact('securityMeasures'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('security-measures.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        SecurityMeasure::create($request->all());

        return redirect()->route('security-measures.index')->with('success', 'Security Measure created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SecurityMeasure $securityMeasure)
    {
        return view('security-measures.show', compact('securityMeasure'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SecurityMeasure $securityMeasure)
    {
        return view('security-measures.edit', compact('securityMeasure'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SecurityMeasure $securityMeasure)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $securityMeasure->update($request->all());

        return redirect()->route('security-measures.index')->with('success', 'Security Measure updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SecurityMeasure $securityMeasure)
    {
        $securityMeasure->delete();

        return redirect()->route('security-measures.index')->with('success', 'Security Measure deleted successfully.');
    }
}
