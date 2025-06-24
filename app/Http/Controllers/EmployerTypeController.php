<?php

namespace App\Http\Controllers;

use App\Models\EmployerType;
use Illuminate\Http\Request;

class EmployerTypeController extends Controller
{
    public function index()
    {
        $types = EmployerType::all();
        return view('employer_types.index', compact('types'));
    }

    public function create()
    {
        if (!auth()->user() || !auth()->user()->hasRole('superadmin')) {
            abort(403, 'Only superadmin can create employer types.');
        }
        return view('employer_types.create');
    }

    public function store(Request $request)
    {
        if (!auth()->user() || !auth()->user()->hasRole('superadmin')) {
            abort(403, 'Only superadmin can create employer types.');
        }
        $request->validate([
            'name' => 'required|string|max:255|unique:employer_types,name',
        ]);
        EmployerType::create(['name' => $request->name]);
        return redirect()->route('employer-types.index')->with('success', 'Employer type created.');
    }

    public function edit(EmployerType $employerType)
    {
        if (!auth()->user() || !auth()->user()->hasRole('superadmin')) {
            abort(403, 'Only superadmin can edit employer types.');
        }
        return view('employer_types.edit', compact('employerType'));
    }

    public function update(Request $request, EmployerType $employerType)
    {
        if (!auth()->user() || !auth()->user()->hasRole('superadmin')) {
            abort(403, 'Only superadmin can update employer types.');
        }
        $request->validate([
            'name' => 'required|string|max:255|unique:employer_types,name,' . $employerType->id,
        ]);
        $employerType->update(['name' => $request->name]);
        return redirect()->route('employer-types.index')->with('success', 'Employer type updated.');
    }

    public function destroy(EmployerType $employerType)
    {
        if (!auth()->user() || !auth()->user()->hasRole('superadmin')) {
            abort(403, 'Only superadmin can delete employer types.');
        }
        $employerType->delete();
        return redirect()->route('employer-types.index')->with('success', 'Employer type deleted.');
    }
}
