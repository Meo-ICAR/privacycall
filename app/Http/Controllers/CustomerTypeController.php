<?php

namespace App\Http\Controllers;

use App\Models\CustomerType;
use Illuminate\Http\Request;

class CustomerTypeController extends Controller
{
    public function index()
    {
        $types = CustomerType::all();
        return view('customer_types.index', compact('types'));
    }

    public function create()
    {
        if (!auth()->user() || !auth()->user()->hasRole('superadmin')) {
            abort(403, 'Only superadmin can create customer types.');
        }
        return view('customer_types.create');
    }

    public function store(Request $request)
    {
        if (!auth()->user() || !auth()->user()->hasRole('superadmin')) {
            abort(403, 'Only superadmin can create customer types.');
        }
        $request->validate([
            'name' => 'required|string|max:255|unique:customer_types,name',
            'icon' => 'nullable|string|max:255',
        ]);
        CustomerType::create($request->only('name', 'icon'));
        return redirect()->route('customer-types.index')->with('success', 'Customer type created.');
    }

    public function edit(CustomerType $customerType)
    {
        if (!auth()->user() || !auth()->user()->hasRole('superadmin')) {
            abort(403, 'Only superadmin can edit customer types.');
        }
        return view('customer_types.edit', compact('customerType'));
    }

    public function update(Request $request, CustomerType $customerType)
    {
        if (!auth()->user() || !auth()->user()->hasRole('superadmin')) {
            abort(403, 'Only superadmin can update customer types.');
        }
        $request->validate([
            'name' => 'required|string|max:255|unique:customer_types,name,' . $customerType->id,
            'icon' => 'nullable|string|max:255',
        ]);
        $customerType->update($request->only('name', 'icon'));
        return redirect()->route('customer-types.index')->with('success', 'Customer type updated.');
    }

    public function destroy(CustomerType $customerType)
    {
        if (!auth()->user() || !auth()->user()->hasRole('superadmin')) {
            abort(403, 'Only superadmin can delete customer types.');
        }
        $customerType->delete();
        return redirect()->route('customer-types.index')->with('success', 'Customer type deleted.');
    }
}
