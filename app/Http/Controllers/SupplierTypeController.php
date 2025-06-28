<?php

namespace App\Http\Controllers;

use App\Models\SupplierType;
use Illuminate\Http\Request;

class SupplierTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $types = SupplierType::all();
        return view('supplier_types.index', compact('types'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!auth()->user() || !auth()->user()->hasRole('superadmin')) {
            abort(403, 'Only superadmin can create supplier types.');
        }
        return view('supplier_types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!auth()->user() || !auth()->user()->hasRole('superadmin')) {
            abort(403, 'Only superadmin can create supplier types.');
        }
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:supplier_types',
            'description' => 'nullable|string',
        ]);

        SupplierType::create($validated);

        return redirect()->route('supplier-types.index')
            ->with('success', 'Supplier type created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SupplierType $supplierType)
    {
        return view('supplier_types.show', compact('supplierType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SupplierType $supplierType)
    {
        if (!auth()->user() || !auth()->user()->hasRole('superadmin')) {
            abort(403, 'Only superadmin can edit supplier types.');
        }
        return view('supplier_types.edit', compact('supplierType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SupplierType $supplierType)
    {
        if (!auth()->user() || !auth()->user()->hasRole('superadmin')) {
            abort(403, 'Only superadmin can update supplier types.');
        }
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:supplier_types,name,' . $supplierType->id,
            'description' => 'nullable|string',
        ]);

        $supplierType->update($validated);

        return redirect()->route('supplier-types.index')
            ->with('success', 'Supplier type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SupplierType $supplierType)
    {
        if (!auth()->user() || !auth()->user()->hasRole('superadmin')) {
            abort(403, 'Only superadmin can delete supplier types.');
        }
        $supplierType->delete();

        return redirect()->route('supplier-types.index')
            ->with('success', 'Supplier type deleted successfully.');
    }
}
