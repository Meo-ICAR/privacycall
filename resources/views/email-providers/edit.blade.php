@extends('layouts.app')

@section('title', 'Edit Email Provider')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Edit Email Provider</h1>
        <form action="{{ route('email-providers.update', $emailProvider) }}" method="POST" enctype="multipart/form-data" class="bg-white shadow rounded-lg p-6 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="display_name" class="block text-sm font-medium text-gray-700 mb-1">Display Name</label>
                    <input type="text" name="display_name" id="display_name" value="{{ old('display_name', $emailProvider->display_name) }}" class="form-input w-full" required>
                    @error('display_name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">System Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $emailProvider->name) }}" class="form-input w-full" required>
                    @error('name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                    <input type="text" name="type" id="type" value="{{ old('type', $emailProvider->type) }}" class="form-input w-full" required>
                    @error('type')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="color" class="block text-sm font-medium text-gray-700 mb-1">Color (hex)</label>
                    <input type="text" name="color" id="color" value="{{ old('color', $emailProvider->color) }}" class="form-input w-full" placeholder="#123456">
                    @error('color')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label for="icon" class="block text-sm font-medium text-gray-700 mb-1">Icon (optional)</label>
                <input type="file" name="icon" id="icon" class="form-input w-full">
                @if($emailProvider->icon)
                    <div class="mt-2">
                        <img src="{{ asset('storage/' . $emailProvider->icon) }}" alt="Icon" class="h-8 inline-block mr-2">
                        <span class="text-xs text-gray-500">Current icon</span>
                    </div>
                @endif
                @error('icon')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="is_active" class="block text-sm font-medium text-gray-700 mb-1">Active</label>
                    <select name="is_active" id="is_active" class="form-select w-full">
                        <option value="1" @if(old('is_active', $emailProvider->is_active)) selected @endif>Active</option>
                        <option value="0" @if(!old('is_active', $emailProvider->is_active)) selected @endif>Inactive</option>
                    </select>
                    @error('is_active')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="auth_type" class="block text-sm font-medium text-gray-700 mb-1">Auth Type</label>
                    <select name="auth_type" id="auth_type" class="form-select w-full">
                        <option value="password" @if(old('auth_type', $emailProvider->auth_type)==='password') selected @endif>Password</option>
                        <option value="oauth" @if(old('auth_type', $emailProvider->auth_type)==='oauth') selected @endif>OAuth</option>
                        <option value="api" @if(old('auth_type', $emailProvider->auth_type)==='api') selected @endif>API</option>
                    </select>
                    @error('auth_type')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <hr>
            <h2 class="text-lg font-semibold text-gray-800 mt-4 mb-2">IMAP Settings</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="imap_host" class="block text-sm font-medium text-gray-700 mb-1">IMAP Host</label>
                    <input type="text" name="imap_host" id="imap_host" value="{{ old('imap_host', $emailProvider->imap_host) }}" class="form-input w-full">
                </div>
                <div>
                    <label for="imap_port" class="block text-sm font-medium text-gray-700 mb-1">IMAP Port</label>
                    <input type="number" name="imap_port" id="imap_port" value="{{ old('imap_port', $emailProvider->imap_port) }}" class="form-input w-full">
                </div>
                <div>
                    <label for="imap_encryption" class="block text-sm font-medium text-gray-700 mb-1">IMAP Encryption</label>
                    <input type="text" name="imap_encryption" id="imap_encryption" value="{{ old('imap_encryption', $emailProvider->imap_encryption) }}" class="form-input w-full" placeholder="ssl, tls, none">
                </div>
            </div>

            <hr>
            <h2 class="text-lg font-semibold text-gray-800 mt-4 mb-2">POP3 Settings</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="pop3_host" class="block text-sm font-medium text-gray-700 mb-1">POP3 Host</label>
                    <input type="text" name="pop3_host" id="pop3_host" value="{{ old('pop3_host', $emailProvider->pop3_host) }}" class="form-input w-full">
                </div>
                <div>
                    <label for="pop3_port" class="block text-sm font-medium text-gray-700 mb-1">POP3 Port</label>
                    <input type="number" name="pop3_port" id="pop3_port" value="{{ old('pop3_port', $emailProvider->pop3_port) }}" class="form-input w-full">
                </div>
                <div>
                    <label for="pop3_encryption" class="block text-sm font-medium text-gray-700 mb-1">POP3 Encryption</label>
                    <input type="text" name="pop3_encryption" id="pop3_encryption" value="{{ old('pop3_encryption', $emailProvider->pop3_encryption) }}" class="form-input w-full" placeholder="ssl, tls, none">
                </div>
            </div>

            <hr>
            <h2 class="text-lg font-semibold text-gray-800 mt-4 mb-2">SMTP Settings</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="smtp_host" class="block text-sm font-medium text-gray-700 mb-1">SMTP Host</label>
                    <input type="text" name="smtp_host" id="smtp_host" value="{{ old('smtp_host', $emailProvider->smtp_host) }}" class="form-input w-full">
                </div>
                <div>
                    <label for="smtp_port" class="block text-sm font-medium text-gray-700 mb-1">SMTP Port</label>
                    <input type="number" name="smtp_port" id="smtp_port" value="{{ old('smtp_port', $emailProvider->smtp_port) }}" class="form-input w-full">
                </div>
                <div>
                    <label for="smtp_encryption" class="block text-sm font-medium text-gray-700 mb-1">SMTP Encryption</label>
                    <input type="text" name="smtp_encryption" id="smtp_encryption" value="{{ old('smtp_encryption', $emailProvider->smtp_encryption) }}" class="form-input w-full" placeholder="ssl, tls, none">
                </div>
                <div>
                    <label for="smtp_auth_required" class="block text-sm font-medium text-gray-700 mb-1">SMTP Auth Required</label>
                    <select name="smtp_auth_required" id="smtp_auth_required" class="form-select w-full">
                        <option value="1" @if(old('smtp_auth_required', $emailProvider->smtp_auth_required)) selected @endif>Yes</option>
                        <option value="0" @if(!old('smtp_auth_required', $emailProvider->smtp_auth_required)) selected @endif>No</option>
                    </select>
                </div>
            </div>

            <hr>
            <h2 class="text-lg font-semibold text-gray-800 mt-4 mb-2">API & OAuth Settings</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="api_endpoint" class="block text-sm font-medium text-gray-700 mb-1">API Endpoint</label>
                    <input type="text" name="api_endpoint" id="api_endpoint" value="{{ old('api_endpoint', $emailProvider->api_endpoint) }}" class="form-input w-full">
                </div>
                <div>
                    <label for="api_version" class="block text-sm font-medium text-gray-700 mb-1">API Version</label>
                    <input type="text" name="api_version" id="api_version" value="{{ old('api_version', $emailProvider->api_version) }}" class="form-input w-full">
                </div>
                <div>
                    <label for="oauth_client_id" class="block text-sm font-medium text-gray-700 mb-1">OAuth Client ID</label>
                    <input type="text" name="oauth_client_id" id="oauth_client_id" value="{{ old('oauth_client_id', $emailProvider->oauth_client_id) }}" class="form-input w-full">
                </div>
                <div>
                    <label for="oauth_client_secret" class="block text-sm font-medium text-gray-700 mb-1">OAuth Client Secret</label>
                    <input type="text" name="oauth_client_secret" id="oauth_client_secret" value="{{ old('oauth_client_secret', $emailProvider->oauth_client_secret) }}" class="form-input w-full">
                </div>
                <div>
                    <label for="oauth_redirect_uri" class="block text-sm font-medium text-gray-700 mb-1">OAuth Redirect URI</label>
                    <input type="text" name="oauth_redirect_uri" id="oauth_redirect_uri" value="{{ old('oauth_redirect_uri', $emailProvider->oauth_redirect_uri) }}" class="form-input w-full">
                </div>
                <div>
                    <label for="oauth_scopes" class="block text-sm font-medium text-gray-700 mb-1">OAuth Scopes (comma separated)</label>
                    <input type="text" name="oauth_scopes" id="oauth_scopes" value="{{ old('oauth_scopes', is_array($emailProvider->oauth_scopes) ? implode(',', $emailProvider->oauth_scopes) : $emailProvider->oauth_scopes) }}" class="form-input w-full">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="timeout" class="block text-sm font-medium text-gray-700 mb-1">Timeout (seconds)</label>
                    <input type="number" name="timeout" id="timeout" value="{{ old('timeout', $emailProvider->timeout) }}" class="form-input w-full">
                </div>
                <div>
                    <label for="verify_ssl" class="block text-sm font-medium text-gray-700 mb-1">Verify SSL</label>
                    <select name="verify_ssl" id="verify_ssl" class="form-select w-full">
                        <option value="1" @if(old('verify_ssl', $emailProvider->verify_ssl)) selected @endif>Yes</option>
                        <option value="0" @if(!old('verify_ssl', $emailProvider->verify_ssl)) selected @endif>No</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" id="description" rows="2" class="form-textarea w-full">{{ old('description', $emailProvider->description) }}</textarea>
            </div>
            <div>
                <label for="setup_instructions" class="block text-sm font-medium text-gray-700 mb-1">Setup Instructions</label>
                <textarea name="setup_instructions" id="setup_instructions" rows="2" class="form-textarea w-full">{{ old('setup_instructions', $emailProvider->setup_instructions) }}</textarea>
            </div>

            <div class="flex justify-end space-x-3 mt-6">
                <a href="{{ route('email-providers.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection
