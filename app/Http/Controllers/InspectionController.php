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
        $user = Auth::user();
        $inspections = Inspection::where('company_id', $user->company_id)
            ->with(['company', 'customer'])
            ->latest()
            ->paginate(20);
        return view('inspections.index', compact('inspections'));
    }

    public function create()
    {
        $user = Auth::user();
        $companies = collect([$user->company]); // Only user's company
        $customers = Customer::where('company_id', $user->company_id)->get();
        return view('inspections.create', compact('companies', 'customers'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'inspection_date' => 'required|date',
            'notes' => 'nullable|string',
            'status' => 'required|in:pending,sent,acknowledged',
        ]);

        $data = $request->all();
        $data['company_id'] = $user->company_id;

        // Verify customer belongs to user's company
        $customer = Customer::find($request->customer_id);
        if ($customer->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        $inspection = Inspection::create($data);
        return redirect()->route('inspections.show', $inspection)->with('success', 'Inspection created.');
    }

    public function show(Inspection $inspection)
    {
        $user = Auth::user();
        if ($inspection->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        $inspection->load(['company', 'customer', 'documents']);
        return view('inspections.show', compact('inspection'));
    }

    public function edit(Inspection $inspection)
    {
        $user = Auth::user();
        if ($inspection->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        $companies = collect([$user->company]); // Only user's company
        $customers = Customer::where('company_id', $user->company_id)->get();
        return view('inspections.edit', compact('inspection', 'companies', 'customers'));
    }

    public function update(Request $request, Inspection $inspection)
    {
        $user = Auth::user();
        if ($inspection->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'inspection_date' => 'required|date',
            'notes' => 'nullable|string',
            'status' => 'required|in:pending,sent,acknowledged',
        ]);

        // Verify customer belongs to user's company
        $customer = Customer::find($request->customer_id);
        if ($customer->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        $data = $request->all();
        $data['company_id'] = $user->company_id;

        $inspection->update($data);
        return redirect()->route('inspections.show', $inspection)->with('success', 'Inspection updated.');
    }

    public function destroy(Inspection $inspection)
    {
        $user = Auth::user();
        if ($inspection->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        $inspection->delete();
        return redirect()->route('inspections.index')->with('success', 'Inspection deleted.');
    }
}
