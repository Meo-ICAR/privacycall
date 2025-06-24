<?php

namespace App\Http\Controllers;

use App\Models\Inspection;
use App\Models\Company;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InspectionController extends Controller
{
    public function index()
    {
        $inspections = Inspection::with(['company', 'customer'])->latest()->paginate(20);
        return view('inspections.index', compact('inspections'));
    }

    public function create()
    {
        $companies = Company::all();
        $customers = Customer::all();
        return view('inspections.create', compact('companies', 'customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'customer_id' => 'required|exists:customers,id',
            'inspection_date' => 'required|date',
            'notes' => 'nullable|string',
            'status' => 'required|in:pending,sent,acknowledged',
        ]);
        $inspection = Inspection::create($request->all());
        return redirect()->route('inspections.show', $inspection)->with('success', 'Inspection created.');
    }

    public function show(Inspection $inspection)
    {
        $inspection->load(['company', 'customer', 'documents']);
        return view('inspections.show', compact('inspection'));
    }

    public function edit(Inspection $inspection)
    {
        $companies = Company::all();
        $customers = Customer::all();
        return view('inspections.edit', compact('inspection', 'companies', 'customers'));
    }

    public function update(Request $request, Inspection $inspection)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'customer_id' => 'required|exists:customers,id',
            'inspection_date' => 'required|date',
            'notes' => 'nullable|string',
            'status' => 'required|in:pending,sent,acknowledged',
        ]);
        $inspection->update($request->all());
        return redirect()->route('inspections.show', $inspection)->with('success', 'Inspection updated.');
    }

    public function destroy(Inspection $inspection)
    {
        $inspection->delete();
        return redirect()->route('inspections.index')->with('success', 'Inspection deleted.');
    }
}
