@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route('customers.index') }}" class="text-blue-600 hover:text-blue-800 mr-4">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Add New Customer</h1>
                    <p class="mt-2 text-sm text-gray-600">Create a new customer record with GDPR compliance information</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <!-- Form -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form action="{{ route('customers.store') }}" method="POST">
                @csrf

                <!-- Personal Information -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Personal Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">First Name *</label>
                            <input type="text" name="first_name" id="first_name"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('first_name') border-red-500 @enderror"
                                   value="{{ old('first_name') }}" required>
                            @error('first_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">Last Name *</label>
                            <input type="text" name="last_name" id="last_name"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('last_name') border-red-500 @enderror"
                                   value="{{ old('last_name') }}" required>
                            @error('last_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" name="email" id="email"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror"
                                   value="{{ old('email') }}">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                            <input type="tel" name="phone" id="phone"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('phone') border-red-500 @enderror"
                                   value="{{ old('phone') }}">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                            <input type="date" name="date_of_birth" id="date_of_birth"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('date_of_birth') border-red-500 @enderror"
                                   value="{{ old('date_of_birth') }}">
                            @error('date_of_birth')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="customer_number" class="block text-sm font-medium text-gray-700 mb-2">Customer Number</label>
                            <input type="text" name="customer_number" id="customer_number"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('customer_number') border-red-500 @enderror"
                                   value="{{ old('customer_number') }}">
                            @error('customer_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Company and Type -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Company & Type</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="company_id" class="block text-sm font-medium text-gray-700 mb-2">Company *</label>
                            <select name="company_id" id="company_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('company_id') border-red-500 @enderror"
                                    required>
                                <option value="">Select Company</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}" {{ old('company_id', $selectedCompany->id ?? '') == $company->id ? 'selected' : '' }}>
                                        {{ $company->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('company_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="customer_type_id" class="block text-sm font-medium text-gray-700 mb-2">Customer Type</label>
                            <select name="customer_type_id" id="customer_type_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('customer_type_id') border-red-500 @enderror">
                                <option value="">Select Customer Type</option>
                                @foreach($customerTypes as $type)
                                    <option value="{{ $type->id }}" {{ old('customer_type_id') == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('customer_type_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Address Information -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Address Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label for="address_line_1" class="block text-sm font-medium text-gray-700 mb-2">Address Line 1</label>
                            <input type="text" name="address_line_1" id="address_line_1"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('address_line_1') border-red-500 @enderror"
                                   value="{{ old('address_line_1') }}">
                            @error('address_line_1')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="address_line_2" class="block text-sm font-medium text-gray-700 mb-2">Address Line 2</label>
                            <input type="text" name="address_line_2" id="address_line_2"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('address_line_2') border-red-500 @enderror"
                                   value="{{ old('address_line_2') }}">
                            @error('address_line_2')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 mb-2">City</label>
                            <input type="text" name="city" id="city"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('city') border-red-500 @enderror"
                                   value="{{ old('city') }}">
                            @error('city')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="state" class="block text-sm font-medium text-gray-700 mb-2">State/Province</label>
                            <input type="text" name="state" id="state"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('state') border-red-500 @enderror"
                                   value="{{ old('state') }}">
                            @error('state')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-2">Postal Code</label>
                            <input type="text" name="postal_code" id="postal_code"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('postal_code') border-red-500 @enderror"
                                   value="{{ old('postal_code') }}">
                            @error('postal_code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="country" class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                            <input type="text" name="country" id="country"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('country') border-red-500 @enderror"
                                   value="{{ old('country') }}">
                            @error('country')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Information</h3>
                    <div class="space-y-4">
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                            <textarea name="notes" id="notes" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" name="is_active" id="is_active" value="1"
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label for="is_active" class="ml-2 block text-sm text-gray-900">Active Customer</label>
                        </div>
                    </div>
                </div>

                <!-- GDPR Compliance -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">GDPR Compliance</h3>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <div class="flex items-center">
                            <i class="fas fa-shield-alt text-blue-600 mr-2"></i>
                            <span class="text-sm text-blue-800">GDPR compliance information for data protection and privacy management</span>
                        </div>
                    </div>

                    <!-- Consent Management -->
                    <div class="mb-6">
                        <h4 class="text-md font-medium text-gray-800 mb-3">Consent Management</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="gdpr_consent_date" class="block text-sm font-medium text-gray-700 mb-2">GDPR Consent Date</label>
                                <input type="date" name="gdpr_consent_date" id="gdpr_consent_date"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error("gdpr_consent_date") border-red-500 @enderror"
                                       value="{{ old("gdpr_consent_date") }}">
                                @error("gdpr_consent_date")
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="data_processing_purpose" class="block text-sm font-medium text-gray-700 mb-2">Data Processing Purpose</label>
                                <input type="text" name="data_processing_purpose" id="data_processing_purpose"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error("data_processing_purpose") border-red-500 @enderror"
                                       value="{{ old("data_processing_purpose") }}"
                                       placeholder="e.g., Service provision, marketing, analytics">
                                @error("data_processing_purpose")
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="data_retention_period" class="block text-sm font-medium text-gray-700 mb-2">Data Retention Period (days)</label>
                                <input type="number" name="data_retention_period" id="data_retention_period"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error("data_retention_period") border-red-500 @enderror"
                                       value="{{ old("data_retention_period") }}"
                                       placeholder="365">
                                @error("data_retention_period")
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Consent Acquisition -->
                    <div class="mb-6">
                        <h4 class="text-md font-medium text-gray-800 mb-3">Consent Acquisition</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="consent_method" class="block text-sm font-medium text-gray-700 mb-2">Consent Method</label>
                                <select name="consent_method" id="consent_method"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error("consent_method") border-red-500 @enderror">
                                    <option value="">Select method</option>
                                    <option value="web_form" {{ old("consent_method") == "web_form" ? "selected" : "" }}>Web Form</option>
                                    <option value="email" {{ old("consent_method") == "email" ? "selected" : "" }}>Email</option>
                                    <option value="phone" {{ old("consent_method") == "phone" ? "selected" : "" }}>Phone</option>
                                    <option value="in_person" {{ old("consent_method") == "in_person" ? "selected" : "" }}>In Person</option>
                                    <option value="document" {{ old("consent_method") == "document" ? "selected" : "" }}>Document</option>
                                    <option value="app_notification" {{ old("consent_method") == "app_notification" ? "selected" : "" }}>App Notification</option>
                                </select>
                                @error("consent_method")
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="consent_source" class="block text-sm font-medium text-gray-700 mb-2">Consent Source</label>
                                <select name="consent_source" id="consent_source"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error("consent_source") border-red-500 @enderror">
                                    <option value="">Select source</option>
                                    <option value="website" {{ old("consent_source") == "website" ? "selected" : "" }}>Website</option>
                                    <option value="mobile_app" {{ old("consent_source") == "mobile_app" ? "selected" : "" }}>Mobile App</option>
                                    <option value="call_center" {{ old("consent_source") == "call_center" ? "selected" : "" }}>Call Center</option>
                                    <option value="in_store" {{ old("consent_source") == "in_store" ? "selected" : "" }}>In Store</option>
                                    <option value="email_campaign" {{ old("consent_source") == "email_campaign" ? "selected" : "" }}>Email Campaign</option>
                                    <option value="contract" {{ old("consent_source") == "contract" ? "selected" : "" }}>Contract</option>
                                </select>
                                @error("consent_source")
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="consent_channel" class="block text-sm font-medium text-gray-700 mb-2">Consent Channel</label>
                                <select name="consent_channel" id="consent_channel"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error("consent_channel") border-red-500 @enderror">
                                    <option value="">Select channel</option>
                                    <option value="online" {{ old("consent_channel") == "online" ? "selected" : "" }}>Online</option>
                                    <option value="offline" {{ old("consent_channel") == "offline" ? "selected" : "" }}>Offline</option>
                                    <option value="phone" {{ old("consent_channel") == "phone" ? "selected" : "" }}>Phone</option>
                                    <option value="email" {{ old("consent_channel") == "email" ? "selected" : "" }}>Email</option>
                                </select>
                                @error("consent_channel")
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="consent_evidence" class="block text-sm font-medium text-gray-700 mb-2">Consent Evidence</label>
                                <select name="consent_evidence" id="consent_evidence"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error("consent_evidence") border-red-500 @enderror">
                                    <option value="">Select evidence type</option>
                                    <option value="screenshot" {{ old("consent_evidence") == "screenshot" ? "selected" : "" }}>Screenshot</option>
                                    <option value="document" {{ old("consent_evidence") == "document" ? "selected" : "" }}>Document</option>
                                    <option value="audio_recording" {{ old("consent_evidence") == "audio_recording" ? "selected" : "" }}>Audio Recording</option>
                                    <option value="video_recording" {{ old("consent_evidence") == "video_recording" ? "selected" : "" }}>Video Recording</option>
                                    <option value="email_confirmation" {{ old("consent_evidence") == "email_confirmation" ? "selected" : "" }}>Email Confirmation</option>
                                    <option value="digital_signature" {{ old("consent_evidence") == "digital_signature" ? "selected" : "" }}>Digital Signature</option>
                                </select>
                                @error("consent_evidence")
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="consent_evidence_file" class="block text-sm font-medium text-gray-700 mb-2">Evidence File (Optional, max 10MB)</label>
                                <input type="file" name="consent_evidence_file" id="consent_evidence_file"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error("consent_evidence_file") border-red-500 @enderror">
                                @error("consent_evidence_file")
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="consent_version" class="block text-sm font-medium text-gray-700 mb-2">Consent Version</label>
                                <input type="text" name="consent_version" id="consent_version"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error("consent_version") border-red-500 @enderror"
                                       value="{{ old("consent_version") }}"
                                       placeholder="e.g., v1.0, 2024-01">
                                @error("consent_version")
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="consent_language" class="block text-sm font-medium text-gray-700 mb-2">Consent Language</label>
                                <input type="text" name="consent_language" id="consent_language"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error("consent_language") border-red-500 @enderror"
                                       value="{{ old("consent_language", app()->getLocale()) }}"
                                       placeholder="en, de, fr">
                                @error("consent_language")
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="ip_address" class="block text-sm font-medium text-gray-700 mb-2">IP Address</label>
                                <input type="text" name="ip_address" id="ip_address"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error("ip_address") border-red-500 @enderror"
                                       value="{{ old("ip_address", request()->ip()) }}">
                                @error("ip_address")
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="user_agent" class="block text-sm font-medium text-gray-700 mb-2">User Agent</label>
                                <input type="text" name="user_agent" id="user_agent"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error("user_agent") border-red-500 @enderror"
                                       value="{{ old("user_agent", request()->header("User-Agent")) }}">
                                @error("user_agent")
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4">
                            <label for="consent_text" class="block text-sm font-medium text-gray-700 mb-2">Consent Text</label>
                            <textarea name="consent_text" id="consent_text" rows="4"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error("consent_text") border-red-500 @enderror"
                                      placeholder="Enter the exact consent text that was presented to the customer">{{ old("consent_text") }}</textarea>
                            @error("consent_text")
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Consent Types -->
                <div class="mb-6">
                    <h4 class="text-md font-medium text-gray-800 mb-3">Consent Types</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="flex items-center">
                            <input type="checkbox" name="data_processing_consent" id="data_processing_consent" value="1"
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                   {{ old("data_processing_consent") ? "checked" : "" }}>
                            <label for="data_processing_consent" class="ml-2 block text-sm text-gray-900">Data Processing Consent</label>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" name="marketing_consent" id="marketing_consent" value="1"
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                   {{ old("marketing_consent") ? "checked" : "" }}>
                            <label for="marketing_consent" class="ml-2 block text-sm text-gray-900">Marketing Consent</label>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" name="third_party_sharing_consent" id="third_party_sharing_consent" value="1"
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                   {{ old("third_party_sharing_consent") ? "checked" : "" }}>
                            <label for="third_party_sharing_consent" class="ml-2 block text-sm text-gray-900">Third Party Sharing Consent</label>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" name="data_retention_consent" id="data_retention_consent" value="1"
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                   {{ old("data_retention_consent") ? "checked" : "" }}>
                            <label for="data_retention_consent" class="ml-2 block text-sm text-gray-900">Data Retention Consent</label>
                        </div>
                    </div>
                </div>

                <!-- Data Subject Rights -->
                <div class="mb-6">
                    <h4 class="text-md font-medium text-gray-800 mb-3">Data Subject Rights</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="flex items-center">
                            <input type="checkbox" name="right_to_be_forgotten_requested" id="right_to_be_forgotten_requested" value="1"
                                   class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded"
                                   {{ old("right_to_be_forgotten_requested") ? "checked" : "" }}>
                            <label for="right_to_be_forgotten_requested" class="ml-2 block text-sm text-gray-900">Right to be Forgotten Requested</label>
                        </div>

                        <div>
                            <label for="right_to_be_forgotten_date" class="block text-sm font-medium text-gray-700 mb-2">Right to be Forgotten Date</label>
                            <input type="date" name="right_to_be_forgotten_date" id="right_to_be_forgotten_date"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error("right_to_be_forgotten_date") border-red-500 @enderror"
                                   value="{{ old("right_to_be_forgotten_date") }}">
                            @error("right_to_be_forgotten_date")
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" name="data_portability_requested" id="data_portability_requested" value="1"
                                   class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded"
                                   {{ old("data_portability_requested") ? "checked" : "" }}>
                            <label for="data_portability_requested" class="ml-2 block text-sm text-gray-900">Data Portability Requested</label>
                        </div>

                        <div>
                            <label for="data_portability_date" class="block text-sm font-medium text-gray-700 mb-2">Data Portability Date</label>
                            <input type="date" name="data_portability_date" id="data_portability_date"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error("data_portability_date") border-red-500 @enderror"
                                   value="{{ old("data_portability_date") }}">
                            @error("data_portability_date")
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <a href="{{ route('customers.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <i class="fas fa-times mr-2"></i>
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <i class="fas fa-save mr-2"></i>
                        Create Customer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
