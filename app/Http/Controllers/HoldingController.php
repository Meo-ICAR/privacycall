<?php

namespace App\Http\Controllers;

use App\Models\Holding;
use Illuminate\Http\Request;

class HoldingController extends Controller
{
    public function index()
    {
        $holdings = Holding::all();
        return view('holdings.index', compact('holdings'));
    }

    public function create()
    {
        if (!auth()->user() || !auth()->user()->hasRole('superadmin')) {
            abort(403, 'Only superadmin can create holdings.');
        }
        return view('holdings.create');
    }

    public function store(Request $request)
    {
        if (!auth()->user() || !auth()->user()->hasRole('superadmin')) {
            abort(403, 'Only superadmin can create holdings.');
        }
        $request->validate([
            'name' => 'required|string|max:255|unique:holdings,name',
        ]);
        Holding::create(['name' => $request->name]);
        return redirect()->route('holdings.index')->with('success', 'Holding created.');
    }

    public function show(Holding $holding)
    {
        // Load companies associated with this holding
        $holding->load(['companies' => function($query) {
            $query->orderBy('name');
        }]);

        return view('holdings.show', compact('holding'));
    }

    public function edit(Holding $holding)
    {
        if (!auth()->user() || !auth()->user()->hasRole('superadmin')) {
            abort(403, 'Only superadmin can edit holdings.');
        }
        return view('holdings.edit', compact('holding'));
    }

    public function update(Request $request, Holding $holding)
    {
        if (!auth()->user() || !auth()->user()->hasRole('superadmin')) {
            abort(403, 'Only superadmin can update holdings.');
        }
        $request->validate([
            'name' => 'required|string|max:255|unique:holdings,name,' . $holding->id,
        ]);
        $holding->update(['name' => $request->name]);
        return redirect()->route('holdings.index')->with('success', 'Holding updated.');
    }

    public function destroy(Holding $holding)
    {
        if (!auth()->user() || !auth()->user()->hasRole('superadmin')) {
            abort(403, 'Only superadmin can delete holdings.');
        }
        $holding->delete();
        return redirect()->route('holdings.index')->with('success', 'Holding deleted.');
    }
}
