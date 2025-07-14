@extends('layouts.app')

@section('title', 'Email Configuration - ' . $company->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Email Configuration</h1>
                    <p class="text-gray-600 mt-2">Configure email integration for {{ $company->name }}</p>
                </div>
                <a href="{{ route('companies.show', $company) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Company
                </a>
            </div>
        </div>

        <!-- Current Configuration Status -->
        @if($company->hasEmailConfigured())
        <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-8">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                <div>
                    <h3 class="text-lg font-semibold text-green-800">Email Configured</h3>
                    <p class="text-green-700">
                        Provider: {{ $company->emailProvider->display_name }}<br>
                        Email: {{ $company->data_controller_contact }}<br>
                        Last Sync: {{ $company->email_last_sync ? $company->email_last_sync->diffForHumans() : 'Never' }}
                    </p>
                </div>
            </div>
            @if($company->email_sync_error)
            <div class="mt-4 p-3 bg-red-100 border border-red-200 rounded">
                <p class="text-red-700 text-sm">
                    <strong>Last Error:</strong> {{ $company->email_sync_error }}
                </p>
            </div>
            @endif
        </div>
        @endif

        <!-- Configuration Form -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Email Provider Configuration</h2>
            </div>

            <form action="{{ route('companies.email-config.update', $company) }}" method="POST" id="emailConfigForm">
                @csrf
                @method('PUT')

                <div class="p-6 space-y-6">
                    <!-- Provider Selection -->
                    <div>
                        <label for="email_provider_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Email Provider
                        </label>
                        <select id="email_provider_id" name="email_provider_id" class="form-select w-full" required>
                            <option value="">Select a provider...</option>
                            @foreach($providers as $provider)
                            <option value="{{ $provider->id }}"
                                    data-provider="{{ $provider->name }}"
                                    data-type="{{ $provider->type }}"
                                    data-oauth="{{ $provider->usesOAuth() ? '1' : '0' }}"
                                    {{ $company->email_provider_id == $provider->id ? 'selected' : '' }}>
                                {{ $provider->display_name }}
                            </option>
                            @endforeach
                        </select>
                        @error('email_provider_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Provider Info -->
                    <div id="providerInfo" class="hidden bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                            <div>
                                <h4 class="font-medium text-blue-800" id="providerName"></h4>
                                <p class="text-blue-700 text-sm mt-1" id="providerDescription"></p>
                                <div class="mt-3" id="providerCapabilities"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Email Address -->
                    <div>
                        <label for="email_address" class="block text-sm font-medium text-gray-700 mb-2">
                            Email Address
                        </label>
                        <input type="email" id="email_address" name="email_address"
                               value="{{ old('email_address', $company->data_controller_contact) }}"
                               class="form-input w-full" required>
                        @error('email_address')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Username -->
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                            Username
                        </label>
                        <input type="text" id="username" name="username"
                               value="{{ old('username', $company->getEmailUsername()) }}"
                               class="form-input w-full" required>
                        <p class="text-gray-500 text-sm mt-1">Usually your email address or username</p>
                        @error('username')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Authentication -->
                    <div id="passwordAuth">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Password
                        </label>
                        <input type="password" id="password" name="password"
                               value="{{ old('password') }}"
                               class="form-input w-full"
                               placeholder="{{ $company->hasEmailConfigured() ? 'Leave blank to keep current password' : 'Your email password or app password' }}">
                        <p class="text-gray-500 text-sm mt-1">
                            @if($company->hasEmailConfigured())
                                Leave blank to keep the current password, or enter a new password to update it.
                            @else
                                Your email password or app password
                            @endif
                        </p>
                        @error('password')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Show Password for Existing Config -->
                    @if($company->hasEmailConfigured() && $company->emailProvider && !$company->emailProvider->usesOAuth())
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                            <div>
                                <h4 class="font-medium text-blue-800">Current Password</h4>
                                <p class="text-blue-700 text-sm mt-1">
                                    A password is currently configured for this email account.
                                    You can update it using the password field above, or leave it blank to keep the current password.
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- OAuth Authentication -->
                    <div id="oauthAuth" class="hidden">
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex items-start">
                                <i class="fas fa-key text-yellow-500 mt-1 mr-3"></i>
                                <div>
                                    <h4 class="font-medium text-yellow-800">OAuth Authentication Required</h4>
                                    <p class="text-yellow-700 text-sm mt-1">
                                        This provider requires OAuth authentication. Click the button below to authorize access.
                                    </p>
                                    <button type="button" id="oauthButton" class="btn btn-primary mt-3">
                                        <i class="fab fa-google mr-2"></i>Authorize with Google
                                    </button>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="oauth_token" name="oauth_token" value="{{ old('oauth_token') }}">
                        <input type="hidden" id="oauth_refresh_token" name="oauth_refresh_token" value="{{ old('oauth_refresh_token') }}">
                    </div>

                    <!-- Custom IMAP Settings -->
                    <div id="customSettings" class="hidden space-y-4">
                        <h3 class="text-lg font-medium text-gray-900">Custom IMAP Settings</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="custom_imap_host" class="block text-sm font-medium text-gray-700 mb-2">
                                    IMAP Host
                                </label>
                                <input type="text" id="custom_imap_host" name="custom_imap_host"
                                       value="{{ old('custom_imap_host') }}"
                                       class="form-input w-full">
                                @error('custom_imap_host')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="custom_imap_port" class="block text-sm font-medium text-gray-700 mb-2">
                                    IMAP Port
                                </label>
                                <input type="number" id="custom_imap_port" name="custom_imap_port"
                                       value="{{ old('custom_imap_port', 993) }}"
                                       class="form-input w-full">
                                @error('custom_imap_port')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="custom_imap_encryption" class="block text-sm font-medium text-gray-700 mb-2">
                                    IMAP Encryption
                                </label>
                                <select id="custom_imap_encryption" name="custom_imap_encryption" class="form-select w-full">
                                    <option value="ssl" {{ old('custom_imap_encryption') == 'ssl' ? 'selected' : '' }}>SSL</option>
                                    <option value="tls" {{ old('custom_imap_encryption') == 'tls' ? 'selected' : '' }}>TLS</option>
                                    <option value="none" {{ old('custom_imap_encryption') == 'none' ? 'selected' : '' }}>None</option>
                                </select>
                                @error('custom_imap_encryption')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <h4 class="text-md font-medium text-gray-900 mt-6">SMTP Settings (for sending emails)</h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="custom_smtp_host" class="block text-sm font-medium text-gray-700 mb-2">
                                    SMTP Host
                                </label>
                                <input type="text" id="custom_smtp_host" name="custom_smtp_host"
                                       value="{{ old('custom_smtp_host') }}"
                                       class="form-input w-full">
                                @error('custom_smtp_host')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="custom_smtp_port" class="block text-sm font-medium text-gray-700 mb-2">
                                    SMTP Port
                                </label>
                                <input type="number" id="custom_smtp_port" name="custom_smtp_port"
                                       value="{{ old('custom_smtp_port', 587) }}"
                                       class="form-input w-full">
                                @error('custom_smtp_port')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="custom_smtp_encryption" class="block text-sm font-medium text-gray-700 mb-2">
                                    SMTP Encryption
                                </label>
                                <select id="custom_smtp_encryption" name="custom_smtp_encryption" class="form-select w-full">
                                    <option value="tls" {{ old('custom_smtp_encryption') == 'tls' ? 'selected' : '' }}>TLS</option>
                                    <option value="ssl" {{ old('custom_smtp_encryption') == 'ssl' ? 'selected' : '' }}>SSL</option>
                                    <option value="none" {{ old('custom_smtp_encryption') == 'none' ? 'selected' : '' }}>None</option>
                                </select>
                                @error('custom_smtp_encryption')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Setup Instructions -->
                    <div id="setupInstructions" class="hidden bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <h4 class="font-medium text-gray-800 mb-2">Setup Instructions</h4>
                        <div id="instructionsContent" class="text-gray-700 text-sm"></div>
                    </div>

                    <!-- Connection Test -->
                    <div class="flex items-center space-x-4">
                        <button type="button" id="testConnection" class="btn btn-secondary">
                            <i class="fas fa-plug mr-2"></i>Test Connection
                        </button>
                        <div id="testResult" class="hidden"></div>
                    </div>

                    <!-- Error Messages -->
                    @if($errors->any())
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex">
                            <i class="fas fa-exclamation-triangle text-red-500 mt-1 mr-3"></i>
                            <div>
                                <h4 class="font-medium text-red-800">Configuration Errors</h4>
                                <ul class="text-red-700 text-sm mt-2 list-disc list-inside">
                                    @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Form Actions -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-2"></i>Save Configuration
                    </button>

                    @if($company->hasEmailConfigured())
                    <form action="{{ route('companies.email-config.destroy', $company) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to remove the email configuration?')">
                            <i class="fas fa-trash mr-2"></i>Remove Configuration
                        </button>
                    </form>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const providerSelect = document.getElementById('email_provider_id');
    const providerInfo = document.getElementById('providerInfo');
    const providerName = document.getElementById('providerName');
    const providerDescription = document.getElementById('providerDescription');
    const providerCapabilities = document.getElementById('providerCapabilities');
    const passwordAuth = document.getElementById('passwordAuth');
    const oauthAuth = document.getElementById('oauthAuth');
    const customSettings = document.getElementById('customSettings');
    const setupInstructions = document.getElementById('setupInstructions');
    const instructionsContent = document.getElementById('instructionsContent');
    const testConnectionBtn = document.getElementById('testConnection');
    const testResult = document.getElementById('testResult');
    const oauthButton = document.getElementById('oauthButton');

    function updateProviderInfo() {
        const selectedOption = providerSelect.options[providerSelect.selectedIndex];
        if (!selectedOption.value) {
            providerInfo.classList.add('hidden');
            oauthAuth.classList.add('hidden');
            customSettings.classList.add('hidden');
            setupInstructions.classList.add('hidden');
            return;
        }

        const providerId = selectedOption.value;
        const providerNameValue = selectedOption.text;
        const isOAuth = selectedOption.dataset.oauth === '1';
        const isCustom = selectedOption.dataset.provider === 'custom';

        // Show authentication method immediately based on data attributes
        if (isOAuth) {
            oauthAuth.classList.remove('hidden');
        } else {
            passwordAuth.classList.remove('hidden');
            oauthAuth.classList.add('hidden');
        }

        // Show custom settings
        if (isCustom) {
            customSettings.classList.remove('hidden');
        } else {
            customSettings.classList.add('hidden');
        }

        // Show provider info via AJAX (but don't block on failure)
        fetch(`/email-providers/${providerId}/config`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Provider config not available');
                }
                return response.json();
            })
            .then(data => {
                providerName.textContent = data.provider.display_name;
                providerDescription.textContent = data.provider.description;

                const capabilities = [];
                if (data.supports_imap) capabilities.push('IMAP');
                if (data.supports_pop3) capabilities.push('POP3');
                if (data.supports_smtp) capabilities.push('SMTP');
                if (data.supports_api) capabilities.push('API');
                if (data.uses_oauth) capabilities.push('OAuth');

                providerCapabilities.innerHTML = `
                    <div class="flex flex-wrap gap-2 mt-2">
                        ${capabilities.map(cap => `<span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded">${cap}</span>`).join('')}
                    </div>
                `;

                providerInfo.classList.remove('hidden');

                // Show setup instructions
                if (data.setup_instructions) {
                    instructionsContent.innerHTML = data.setup_instructions.replace(/\n/g, '<br>');
                    setupInstructions.classList.remove('hidden');
                } else {
                    setupInstructions.classList.add('hidden');
                }
            })
            .catch(error => {
                // If AJAX fails, still show basic provider info
                providerName.textContent = providerNameValue;
                providerDescription.textContent = 'Provider information not available';
                providerCapabilities.innerHTML = `
                    <div class="flex flex-wrap gap-2 mt-2">
                        <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded">Basic</span>
                    </div>
                `;
                providerInfo.classList.remove('hidden');
                setupInstructions.classList.add('hidden');
            });
    }

    providerSelect.addEventListener('change', updateProviderInfo);

    // Initialize on page load
    updateProviderInfo();

    // Test connection
    testConnectionBtn.addEventListener('click', function() {
        console.log('Test Connection button clicked');
        const formData = new FormData();
        formData.append('email_provider_id', providerSelect.value);
        formData.append('username', document.getElementById('username').value);
        formData.append('password', document.getElementById('password').value);
        formData.append('oauth_token', document.getElementById('oauth_token').value);
        formData.append('custom_imap_host', document.getElementById('custom_imap_host').value);
        formData.append('custom_imap_port', document.getElementById('custom_imap_port').value);
        formData.append('custom_imap_encryption', document.getElementById('custom_imap_encryption').value);
        formData.append('_token', '{{ csrf_token() }}');

        testConnectionBtn.disabled = true;
        testConnectionBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Testing...';

        fetch('{{ route("companies.email-config.test", $company) }}', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            testResult.classList.remove('hidden');
            if (data.success) {
                testResult.innerHTML = '<div class="text-green-600"><i class="fas fa-check-circle mr-2"></i>Connection successful!</div>';
            } else {
                testResult.innerHTML = `<div class="text-red-600"><i class="fas fa-times-circle mr-2"></i>Connection failed: ${data.error}</div>`;
            }
        })
        .catch(error => {
            testResult.classList.remove('hidden');
            testResult.innerHTML = `<div class="text-red-600"><i class="fas fa-times-circle mr-2"></i>Test failed: ${error.message}</div>`;
        })
        .finally(() => {
            testConnectionBtn.disabled = false;
            testConnectionBtn.innerHTML = '<i class="fas fa-plug mr-2"></i>Test Connection';
        });
    });

    // OAuth button
    if (oauthButton) {
        oauthButton.addEventListener('click', function() {
            const providerId = providerSelect.value;
            if (!providerId) {
                alert('Please select a provider first');
                return;
            }

            fetch(`{{ route('companies.email-config.oauth-url', $company) }}?email_provider_id=${providerId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = data.oauth_url;
                    } else {
                        alert('Failed to get OAuth URL: ' + data.error);
                    }
                })
                .catch(error => {
                    alert('Failed to get OAuth URL: ' + error.message);
                });
        });
    }
});
</script>
@endpush
@endsection
