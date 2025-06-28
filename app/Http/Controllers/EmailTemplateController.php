<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('superadmin')) {
            // Superadmin sees all templates
            $templates = EmailTemplate::with('company')->get();
        } else {
            // Regular users see only their company's templates
            $templates = EmailTemplate::where('company_id', $user->company_id)
                ->orWhereNull('company_id') // Global templates
                ->with('company')
                ->get();
        }

        return view('email-templates.index', compact('templates'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!Auth::user()->hasRole('superadmin')) {
            abort(403, 'Only superadmins can create email templates.');
        }

        $companies = \App\Models\Company::all();
        return view('email-templates.create', compact('companies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!Auth::user()->hasRole('superadmin')) {
            abort(403, 'Only superadmins can create email templates.');
        }

        $validated = $request->validate([
            'company_id' => 'nullable|exists:companies,id',
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'variables' => 'nullable|array',
            'is_active' => 'boolean',
            'category' => 'required|string|max:255',
        ]);

        // Ensure unique name per company
        $existingTemplate = EmailTemplate::where('company_id', $validated['company_id'])
            ->where('name', $validated['name'])
            ->first();

        if ($existingTemplate) {
            return back()->withErrors(['name' => 'A template with this name already exists for this company.']);
        }

        $validated['is_active'] = $request->has('is_active');

        EmailTemplate::create($validated);

        return redirect()->route('email-templates.index')
            ->with('success', 'Email template created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(EmailTemplate $emailTemplate)
    {
        $user = Auth::user();

        // Check if user has access to this template
        if (!$user->hasRole('superadmin') &&
            $emailTemplate->company_id &&
            $emailTemplate->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        return view('email-templates.show', compact('emailTemplate'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EmailTemplate $emailTemplate)
    {
        if (!Auth::user()->hasRole('superadmin')) {
            abort(403, 'Only superadmins can edit email templates.');
        }

        $companies = \App\Models\Company::all();
        return view('email-templates.edit', compact('emailTemplate', 'companies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EmailTemplate $emailTemplate)
    {
        if (!Auth::user()->hasRole('superadmin')) {
            abort(403, 'Only superadmins can edit email templates.');
        }

        $validated = $request->validate([
            'company_id' => 'nullable|exists:companies,id',
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'variables' => 'nullable|array',
            'is_active' => 'boolean',
            'category' => 'required|string|max:255',
        ]);

        // Ensure unique name per company (excluding current template)
        $existingTemplate = EmailTemplate::where('company_id', $validated['company_id'])
            ->where('name', $validated['name'])
            ->where('id', '!=', $emailTemplate->id)
            ->first();

        if ($existingTemplate) {
            return back()->withErrors(['name' => 'A template with this name already exists for this company.']);
        }

        $validated['is_active'] = $request->has('is_active');

        $emailTemplate->update($validated);

        return redirect()->route('email-templates.index')
            ->with('success', 'Email template updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmailTemplate $emailTemplate)
    {
        $user = Auth::user();

        // Only superadmins or template owners can delete
        if (!$user->hasRole('superadmin') &&
            $emailTemplate->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        $emailTemplate->delete();

        return redirect()->route('email-templates.index')
            ->with('success', 'Email template deleted successfully.');
    }
}
