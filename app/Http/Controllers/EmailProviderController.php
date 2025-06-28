<?php

namespace App\Http\Controllers;

use App\Models\EmailProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmailProviderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $emailProviders = EmailProvider::orderBy('display_name')->paginate(15);
        return view('email-providers.index', compact('emailProviders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('email-providers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:email_providers',
            'display_name' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'imap_host' => 'nullable|string|max:255',
            'imap_port' => 'nullable|integer|min:1|max:65535',
            'imap_encryption' => 'nullable|string|in:none,ssl,tls',
            'pop3_host' => 'nullable|string|max:255',
            'pop3_port' => 'nullable|integer|min:1|max:65535',
            'pop3_encryption' => 'nullable|string|in:none,ssl,tls',
            'smtp_host' => 'nullable|string|max:255',
            'smtp_port' => 'nullable|integer|min:1|max:65535',
            'smtp_encryption' => 'nullable|string|in:none,ssl,tls',
            'smtp_auth_required' => 'boolean',
            'api_endpoint' => 'nullable|url|max:255',
            'api_version' => 'nullable|string|max:50',
            'oauth_client_id' => 'nullable|string|max:255',
            'oauth_client_secret' => 'nullable|string|max:255',
            'oauth_redirect_uri' => 'nullable|url|max:255',
            'oauth_scopes' => 'nullable|array',
            'oauth_scopes.*' => 'string|max:255',
            'timeout' => 'nullable|integer|min:1|max:300',
            'verify_ssl' => 'boolean',
            'auth_type' => 'nullable|string|in:password,oauth,api',
            'settings' => 'nullable|array',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
            'setup_instructions' => 'nullable|string',
        ]);

        // Handle icon upload
        if ($request->hasFile('icon')) {
            $icon = $request->file('icon');
            $filename = 'email-providers/' . uniqid('icon_') . '.' . $icon->getClientOriginalExtension();
            Storage::disk('public')->put($filename, file_get_contents($icon));
            $validated['icon'] = $filename;
        }

        EmailProvider::create($validated);

        return redirect()->route('email-providers.index')
            ->with('success', 'Email provider created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(EmailProvider $emailProvider)
    {
        return view('email-providers.show', compact('emailProvider'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EmailProvider $emailProvider)
    {
        return view('email-providers.edit', compact('emailProvider'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EmailProvider $emailProvider)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:email_providers,name,' . $emailProvider->id,
            'display_name' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'imap_host' => 'nullable|string|max:255',
            'imap_port' => 'nullable|integer|min:1|max:65535',
            'imap_encryption' => 'nullable|string|in:none,ssl,tls',
            'pop3_host' => 'nullable|string|max:255',
            'pop3_port' => 'nullable|integer|min:1|max:65535',
            'pop3_encryption' => 'nullable|string|in:none,ssl,tls',
            'smtp_host' => 'nullable|string|max:255',
            'smtp_port' => 'nullable|integer|min:1|max:65535',
            'smtp_encryption' => 'nullable|string|in:none,ssl,tls',
            'smtp_auth_required' => 'boolean',
            'api_endpoint' => 'nullable|url|max:255',
            'api_version' => 'nullable|string|max:50',
            'oauth_client_id' => 'nullable|string|max:255',
            'oauth_client_secret' => 'nullable|string|max:255',
            'oauth_redirect_uri' => 'nullable|url|max:255',
            'oauth_scopes' => 'nullable|array',
            'oauth_scopes.*' => 'string|max:255',
            'timeout' => 'nullable|integer|min:1|max:300',
            'verify_ssl' => 'boolean',
            'auth_type' => 'nullable|string|in:password,oauth,api',
            'settings' => 'nullable|array',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
            'setup_instructions' => 'nullable|string',
        ]);

        // Handle icon upload
        if ($request->hasFile('icon')) {
            // Delete old icon if exists
            if ($emailProvider->icon && !str_contains($emailProvider->icon, 'fab fa-')) {
                Storage::disk('public')->delete($emailProvider->icon);
            }

            $icon = $request->file('icon');
            $filename = 'email-providers/' . uniqid('icon_') . '.' . $icon->getClientOriginalExtension();
            Storage::disk('public')->put($filename, file_get_contents($icon));
            $validated['icon'] = $filename;
        }

        $emailProvider->update($validated);

        return redirect()->route('email-providers.index')
            ->with('success', 'Email provider updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmailProvider $emailProvider)
    {
        // Check if any companies are using this provider
        if ($emailProvider->companies()->count() > 0) {
            return redirect()->route('email-providers.index')
                ->with('error', 'Cannot delete email provider that is being used by companies.');
        }

        // Delete icon if exists
        if ($emailProvider->icon && !str_contains($emailProvider->icon, 'fab fa-')) {
            Storage::disk('public')->delete($emailProvider->icon);
        }

        $emailProvider->delete();

        return redirect()->route('email-providers.index')
            ->with('success', 'Email provider deleted successfully.');
    }
}
