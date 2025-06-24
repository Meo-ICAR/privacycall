<?php

namespace App\Http\Controllers;

use App\Models\Holding;
use Illuminate\Http\Request;

class HoldingController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user() || !auth()->user()->hasRole('superadmin')) {
                abort(403, 'Only superadmin can access holdings.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $holdings = Holding::all();
        return view('holdings.index', compact('holdings'));
    }

    public function create()
    {
        return view('holdings.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:holdings,name',
        ]);
        Holding::create(['name' => $request->name]);
        return redirect()->route('holdings.index')->with('success', 'Holding created.');
    }

    public function edit(Holding $holding)
    {
        return view('holdings.edit', compact('holding'));
    }

    public function update(Request $request, Holding $holding)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:holdings,name,' . $holding->id,
        ]);
        $holding->update(['name' => $request->name]);
        return redirect()->route('holdings.index')->with('success', 'Holding updated.');
    }

    public function destroy(Holding $holding)
    {
        $holding->delete();
        return redirect()->route('holdings.index')->with('success', 'Holding deleted.');
    }
}
