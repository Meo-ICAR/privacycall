<?php

namespace App\Http\Controllers;

use App\Models\LegalBasisType;
use Illuminate\Http\Request;

class LegalBasisTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $legalBasisTypes = LegalBasisType::all();
        return view('legal-basis-types.index', compact('legalBasisTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('legal-basis-types.create');
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

        LegalBasisType::create($request->all());

        return redirect()->route('legal-basis-types.index')->with('success', 'Legal Basis Type created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(LegalBasisType $legalBasisType)
    {
        return view('legal-basis-types.show', compact('legalBasisType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LegalBasisType $legalBasisType)
    {
        return view('legal-basis-types.edit', compact('legalBasisType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LegalBasisType $legalBasisType)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $legalBasisType->update($request->all());

        return redirect()->route('legal-basis-types.index')->with('success', 'Legal Basis Type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LegalBasisType $legalBasisType)
    {
        $legalBasisType->delete();

        return redirect()->route('legal-basis-types.index')->with('success', 'Legal Basis Type deleted successfully.');
    }
}
