<?php

namespace App\Http\Controllers;

use App\Models\DisclosureType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class DisclosureTypeController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->check() || auth()->user()->role !== 'superadmin') {
                abort(403, 'Unauthorized action. Only superadmins can manage disclosure types.');
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $disclosureTypes = DisclosureType::ordered()->get();

        return view('disclosure-types.index', compact('disclosureTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = [
            'compliance' => 'Compliance',
            'security' => 'Security',
            'privacy' => 'Privacy',
            'general' => 'General',
        ];

        return view('disclosure-types.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:disclosure_types,name',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|in:compliance,security,privacy,general',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        DisclosureType::create($validated);

        return redirect()->route('disclosure-types.index')
            ->with('success', 'Disclosure type created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(DisclosureType $disclosureType)
    {
        $mandators = $disclosureType->mandators()->with('company')->get();

        return view('disclosure-types.show', compact('disclosureType', 'mandators'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DisclosureType $disclosureType)
    {
        $categories = [
            'compliance' => 'Compliance',
            'security' => 'Security',
            'privacy' => 'Privacy',
            'general' => 'General',
        ];

        return view('disclosure-types.edit', compact('disclosureType', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DisclosureType $disclosureType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:disclosure_types,name,' . $disclosureType->id,
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|in:compliance,security,privacy,general',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $disclosureType->update($validated);

        return redirect()->route('disclosure-types.index')
            ->with('success', 'Disclosure type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DisclosureType $disclosureType)
    {
        // Check if any mandators are subscribed to this disclosure type
        if ($disclosureType->mandators()->count() > 0) {
            return redirect()->route('disclosure-types.index')
                ->with('error', 'Cannot delete disclosure type. It is subscribed to by one or more mandators.');
        }

        $disclosureType->delete();

        return redirect()->route('disclosure-types.index')
            ->with('success', 'Disclosure type deleted successfully.');
    }
}
