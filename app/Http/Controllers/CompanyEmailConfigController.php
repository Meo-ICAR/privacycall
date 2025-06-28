<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\EmailProvider;
use App\Services\EmailIntegrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CompanyEmailConfigController extends Controller
{
    protected $emailService;

    public function __construct(EmailIntegrationService $emailService)
    {
        $this->emailService = $emailService;
    }

    /**
     * Show the email configuration form for a company.
     */
    public function show(Company $company)
    {
        $providers = EmailProvider::active()->orderBy('display_name')->get();

        return view('companies.email-config', compact('company', 'providers'));
    }

    /**
     * Update email configuration for a company.
     */
    public function update(Request $request, Company $company)
    {
        $validator = Validator::make($request->all(), [
            'email_provider_id' => 'required|exists:email_providers,id',
            'email_address' => 'required|email',
            'username' => 'required|string|max:255',
            'password' => 'nullable|string|max:255',
            'oauth_token' => 'nullable|string',
            'oauth_refresh_token' => 'nullable|string',
            'custom_imap_host' => 'nullable|string|max:255',
            'custom_imap_port' => 'nullable|integer|min:1|max:65535',
            'custom_imap_encryption' => 'nullable|in:ssl,tls,none',
            'custom_smtp_host' => 'nullable|string|max:255',
            'custom_smtp_port' => 'nullable|integer|min:1|max:65535',
            'custom_smtp_encryption' => 'nullable|in:ssl,tls,none',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $provider = EmailProvider::findOrFail($request->email_provider_id);

        // Validate custom settings if provider is custom
        if ($provider->name === 'custom') {
            $customValidator = Validator::make($request->all(), [
                'custom_imap_host' => 'required|string|max:255',
                'custom_imap_port' => 'required|integer|min:1|max:65535',
                'custom_imap_encryption' => 'required|in:ssl,tls,none',
                'custom_smtp_host' => 'required|string|max:255',
                'custom_smtp_port' => 'required|integer|min:1|max:65535',
                'custom_smtp_encryption' => 'required|in:ssl,tls,none',
            ]);

            if ($customValidator->fails()) {
                return back()->withErrors($customValidator)->withInput();
            }
        }

        try {
            // Prepare credentials
            $credentials = [
                'username' => $request->username,
                'email_address' => $request->email_address,
            ];

            if ($provider->usesOAuth()) {
                $credentials['oauth_token'] = $request->oauth_token;
                $credentials['oauth_refresh_token'] = $request->oauth_refresh_token;
            } else {
                $credentials['password'] = $request->password;
            }

            // Add custom settings if provider is custom
            if ($provider->name === 'custom') {
                $credentials['custom_settings'] = [
                    'imap_host' => $request->custom_imap_host,
                    'imap_port' => $request->custom_imap_port,
                    'imap_encryption' => $request->custom_imap_encryption,
                    'smtp_host' => $request->custom_smtp_host,
                    'smtp_port' => $request->custom_smtp_port,
                    'smtp_encryption' => $request->custom_smtp_encryption,
                ];
            }

            // Test the connection
            $testResult = $this->emailService->testConnection($company, $provider, $credentials);

            if (!$testResult['success']) {
                return back()->withErrors(['connection' => $testResult['error']])->withInput();
            }

            // Update company with email configuration
            $company->update([
                'email_provider_id' => $provider->id,
                'data_controller_contact' => $request->email_address,
                'email_credentials' => $credentials,
                'email_configured' => true,
                'email_sync_error' => null,
            ]);

            return redirect()->route('companies.show', $company)
                ->with('success', 'Email configuration updated successfully!');

        } catch (\Exception $e) {
            return back()->withErrors(['general' => 'Failed to update email configuration: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Test email connection.
     */
    public function testConnection(Request $request, Company $company)
    {
        $validator = Validator::make($request->all(), [
            'email_provider_id' => 'required|exists:email_providers,id',
            'username' => 'required|string|max:255',
            'password' => 'nullable|string|max:255',
            'oauth_token' => 'nullable|string',
            'custom_imap_host' => 'nullable|string|max:255',
            'custom_imap_port' => 'nullable|integer|min:1|max:65535',
            'custom_imap_encryption' => 'nullable|in:ssl,tls,none',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->errors()->first()]);
        }

        $provider = EmailProvider::findOrFail($request->email_provider_id);

        $credentials = [
            'username' => $request->username,
        ];

        if ($provider->usesOAuth()) {
            $credentials['oauth_token'] = $request->oauth_token;
        } else {
            $credentials['password'] = $request->password;
        }

        if ($provider->name === 'custom') {
            $credentials['custom_settings'] = [
                'imap_host' => $request->custom_imap_host,
                'imap_port' => $request->custom_imap_port,
                'imap_encryption' => $request->custom_imap_encryption,
            ];
        }

        $result = $this->emailService->testConnection($company, $provider, $credentials);

        return response()->json($result);
    }

    /**
     * Remove email configuration from a company.
     */
    public function destroy(Company $company)
    {
        $company->update([
            'email_provider_id' => null,
            'email_credentials' => null,
            'email_configured' => false,
            'email_last_sync' => null,
            'email_sync_error' => null,
        ]);

        return redirect()->route('companies.show', $company)
            ->with('success', 'Email configuration removed successfully!');
    }

    /**
     * Get provider configuration for AJAX requests.
     */
    public function getProviderConfig(EmailProvider $provider)
    {
        return response()->json([
            'provider' => $provider,
            'supports_imap' => $provider->supportsImap(),
            'supports_pop3' => $provider->supportsPop3(),
            'supports_smtp' => $provider->supportsSmtp(),
            'supports_api' => $provider->supportsApi(),
            'uses_oauth' => $provider->usesOAuth(),
            'imap_config' => $provider->getImapConfig(),
            'smtp_config' => $provider->getSmtpConfig(),
            'api_config' => $provider->getApiConfig(),
            'setup_instructions' => $provider->getSetupInstructionsWithPlaceholders(),
        ]);
    }

    /**
     * Show OAuth authorization URL for providers that support it.
     */
    public function getOAuthUrl(Request $request, Company $company)
    {
        $validator = Validator::make($request->all(), [
            'email_provider_id' => 'required|exists:email_providers,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->errors()->first()]);
        }

        $provider = EmailProvider::findOrFail($request->email_provider_id);

        if (!$provider->usesOAuth()) {
            return response()->json(['success' => false, 'error' => 'This provider does not support OAuth']);
        }

        try {
            $oauthUrl = $this->emailService->getOAuthUrl($provider, $company);
            return response()->json(['success' => true, 'oauth_url' => $oauthUrl]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Handle OAuth callback.
     */
    public function oauthCallback(Request $request, Company $company)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string',
            'state' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->route('companies.email-config.show', $company)
                ->withErrors(['oauth' => 'Invalid OAuth callback parameters']);
        }

        try {
            $result = $this->emailService->handleOAuthCallback($request->code, $request->state, $company);

            if ($result['success']) {
                return redirect()->route('companies.email-config.show', $company)
                    ->with('success', 'OAuth authentication successful! You can now save the configuration.');
            } else {
                return redirect()->route('companies.email-config.show', $company)
                    ->withErrors(['oauth' => $result['error']]);
            }
        } catch (\Exception $e) {
            return redirect()->route('companies.email-config.show', $company)
                ->withErrors(['oauth' => 'OAuth authentication failed: ' . $e->getMessage()]);
        }
    }
}
