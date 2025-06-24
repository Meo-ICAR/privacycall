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
        $inspections = SupplierInspection::with(['company', 'supplier'])->latest()->paginate(20);
        return view('supplier_inspections.index', compact('inspections'));
    }

    public function create()
    {
        $companies = Company::all();
        $suppliers = Supplier::all();
        return view('supplier_inspections.create', compact('companies', 'suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'inspection_date' => 'required|date',
            'notes' => 'nullable|string',
            'status' => 'required|in:pending,sent,acknowledged',
        ]);
        $inspection = SupplierInspection::create($request->all());
        return redirect()->route('supplier-inspections.show', $inspection)->with('success', 'Supplier inspection created.');
    }

    public function show(SupplierInspection $supplierInspection)
    {
        $supplierInspection->load(['company', 'supplier', 'documents']);
        return view('supplier_inspections.show', ['inspection' => $supplierInspection]);
    }

    public function edit(SupplierInspection $supplierInspection)
    {
        $companies = Company::all();
        $suppliers = Supplier::all();
        return view('supplier_inspections.edit', [
            'inspection' => $supplierInspection,
            'companies' => $companies,
            'suppliers' => $suppliers,
        ]);
    }

    public function update(Request $request, SupplierInspection $supplierInspection)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'inspection_date' => 'required|date',
            'notes' => 'nullable|string',
            'status' => 'required|in:pending,sent,acknowledged',
        ]);
        $supplierInspection->update($request->all());
        return redirect()->route('supplier-inspections.show', $supplierInspection)->with('success', 'Supplier inspection updated.');
    }

    public function destroy(SupplierInspection $supplierInspection)
    {
        $supplierInspection->delete();
        return redirect()->route('supplier-inspections.index')->with('success', 'Supplier inspection deleted.');
    }
}
