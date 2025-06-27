<?php

namespace App\Http\Controllers;

use App\Models\SupplierInspection;
use App\Models\Company;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierInspectionController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $inspections = SupplierInspection::where('company_id', $user->company_id)
            ->with(['company', 'supplier'])
            ->latest()
            ->paginate(20);
        return view('supplier_inspections.index', compact('inspections'));
    }

    public function create()
    {
        $user = Auth::user();
        $companies = collect([$user->company]); // Only user's company
        $suppliers = Supplier::where('company_id', $user->company_id)->get();
        return view('supplier_inspections.create', compact('companies', 'suppliers'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'inspection_date' => 'required|date',
            'notes' => 'nullable|string',
            'status' => 'required|in:pending,sent,acknowledged',
        ]);

        // Verify supplier belongs to user's company
        $supplier = Supplier::find($request->supplier_id);
        if ($supplier->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        $data = $request->all();
        $data['company_id'] = $user->company_id;

        $inspection = SupplierInspection::create($data);
        return redirect()->route('supplier-inspections.show', $inspection)->with('success', 'Supplier inspection created.');
    }

    public function show(SupplierInspection $supplierInspection)
    {
        $user = Auth::user();
        if ($supplierInspection->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        $supplierInspection->load(['company', 'supplier', 'documents']);
        return view('supplier_inspections.show', ['inspection' => $supplierInspection]);
    }

    public function edit(SupplierInspection $supplierInspection)
    {
        $user = Auth::user();
        if ($supplierInspection->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        $companies = collect([$user->company]); // Only user's company
        $suppliers = Supplier::where('company_id', $user->company_id)->get();
        return view('supplier_inspections.edit', [
            'inspection' => $supplierInspection,
            'companies' => $companies,
            'suppliers' => $suppliers,
        ]);
    }

    public function update(Request $request, SupplierInspection $supplierInspection)
    {
        $user = Auth::user();
        if ($supplierInspection->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'inspection_date' => 'required|date',
            'notes' => 'nullable|string',
            'status' => 'required|in:pending,sent,acknowledged',
        ]);

        // Verify supplier belongs to user's company
        $supplier = Supplier::find($request->supplier_id);
        if ($supplier->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        $data = $request->all();
        $data['company_id'] = $user->company_id;

        $supplierInspection->update($data);
        return redirect()->route('supplier-inspections.show', $supplierInspection)->with('success', 'Supplier inspection updated.');
    }

    public function destroy(SupplierInspection $supplierInspection)
    {
        $user = Auth::user();
        if ($supplierInspection->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        $supplierInspection->delete();
        return redirect()->route('supplier-inspections.index')->with('success', 'Supplier inspection deleted.');
    }
}
