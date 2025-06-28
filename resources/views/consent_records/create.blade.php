@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Create Consent Record</h1>
        <p class="mt-2 text-gray-600">Add a new data subject consent record</p>
    </div>

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

    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form method="POST" action="{{ route('consent-records.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="consentable_type" class="block text-sm font-medium text-gray-700">Data Subject Type</label>
                        <select id="consentable_type" name="consentable_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="">Select type</option>
                            <option value="App\\Models\\Customer" {{ old('consentable_type') == 'App\\Models\\Customer' ? 'selected' : '' }}>Customer</option>
                            <option value="App\\Models\\Employee" {{ old('consentable_type') == 'App\\Models\\Employee' ? 'selected' : '' }}>Employee</option>
                            <option value="App\\Models\\Supplier" {{ old('consentable_type') == 'App\\Models\\Supplier' ? 'selected' : '' }}>Supplier</option>
                        </select>
                        @error('consentable_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label for="consentable_id" class="block text-sm font-medium text-gray-700">Data Subject</label>
                        <input type="number" id="consentable_id" name="consentable_id" value="{{ old('consentable_id') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required placeholder="Enter ID (autocomplete can be added)">
                        @error('consentable_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="consent_type" class="block text-sm font-medium text-gray-700">Consent Type</label>
                        <select id="consent_type" name="consent_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="">Select consent type</option>
                            <option value="data_processing" {{ old('consent_type') == 'data_processing' ? 'selected' : '' }}>Data Processing</option>
                            <option value="marketing" {{ old('consent_type') == 'marketing' ? 'selected' : '' }}>Marketing</option>
                            <option value="third_party_sharing" {{ old('consent_type') == 'third_party_sharing' ? 'selected' : '' }}>Third Party Sharing</option>
                            <option value="data_retention" {{ old('consent_type') == 'data_retention' ? 'selected' : '' }}>Data Retention</option>
                            <option value="cookies" {{ old('consent_type') == 'cookies' ? 'selected' : '' }}>Cookies</option>
                            <option value="location_data" {{ old('consent_type') == 'location_data' ? 'selected' : '' }}>Location Data</option>
                            <option value="biometric_data" {{ old('consent_type') == 'biometric_data' ? 'selected' : '' }}>Biometric Data</option>
                        </select>
                        @error('consent_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="consent_status" class="block text-sm font-medium text-gray-700">Consent Status</label>
                        <select id="consent_status" name="consent_status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="">Select status</option>
                            <option value="granted" {{ old('consent_status') == 'granted' ? 'selected' : '' }}>Granted</option>
                            <option value="withdrawn" {{ old('consent_status') == 'withdrawn' ? 'selected' : '' }}>Withdrawn</option>
                            <option value="expired" {{ old('consent_status') == 'expired' ? 'selected' : '' }}>Expired</option>
                            <option value="pending" {{ old('consent_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        </select>
                        @error('consent_status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="consent_method" class="block text-sm font-medium text-gray-700">Consent Method</label>
                        <select id="consent_method" name="consent_method" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="">Select method</option>
                            <option value="web_form" {{ old('consent_method') == 'web_form' ? 'selected' : '' }}>Web Form</option>
                            <option value="email" {{ old('consent_method') == 'email' ? 'selected' : '' }}>Email</option>
                            <option value="phone" {{ old('consent_method') == 'phone' ? 'selected' : '' }}>Phone</option>
                            <option value="in_person" {{ old('consent_method') == 'in_person' ? 'selected' : '' }}>In Person</option>
                            <option value="document" {{ old('consent_method') == 'document' ? 'selected' : '' }}>Document</option>
                            <option value="app_notification" {{ old('consent_method') == 'app_notification' ? 'selected' : '' }}>App Notification</option>
                        </select>
                        @error('consent_method')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="consent_source" class="block text-sm font-medium text-gray-700">Consent Source</label>
                        <select id="consent_source" name="consent_source" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="">Select source</option>
                            <option value="website" {{ old('consent_source') == 'website' ? 'selected' : '' }}>Website</option>
                            <option value="mobile_app" {{ old('consent_source') == 'mobile_app' ? 'selected' : '' }}>Mobile App</option>
                            <option value="call_center" {{ old('consent_source') == 'call_center' ? 'selected' : '' }}>Call Center</option>
                            <option value="in_store" {{ old('consent_source') == 'in_store' ? 'selected' : '' }}>In Store</option>
                            <option value="email_campaign" {{ old('consent_source') == 'email_campaign' ? 'selected' : '' }}>Email Campaign</option>
                            <option value="contract" {{ old('consent_source') == 'contract' ? 'selected' : '' }}>Contract</option>
                        </select>
                        @error('consent_source')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="consent_channel" class="block text-sm font-medium text-gray-700">Consent Channel</label>
                        <select id="consent_channel" name="consent_channel" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="">Select channel</option>
                            <option value="online" {{ old('consent_channel') == 'online' ? 'selected' : '' }}>Online</option>
                            <option value="offline" {{ old('consent_channel') == 'offline' ? 'selected' : '' }}>Offline</option>
                            <option value="phone" {{ old('consent_channel') == 'phone' ? 'selected' : '' }}>Phone</option>
                            <option value="email" {{ old('consent_channel') == 'email' ? 'selected' : '' }}>Email</option>
                        </select>
                        @error('consent_channel')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="consent_date" class="block text-sm font-medium text-gray-700">Consent Date</label>
                        <input type="date" id="consent_date" name="consent_date" value="{{ old('consent_date') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                        @error('consent_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="withdrawal_date" class="block text-sm font-medium text-gray-700">Withdrawal Date (Optional)</label>
                        <input type="date" id="withdrawal_date" name="withdrawal_date" value="{{ old('withdrawal_date') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('withdrawal_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="expiry_date" class="block text-sm font-medium text-gray-700">Expiry Date (Optional)</label>
                        <input type="date" id="expiry_date" name="expiry_date" value="{{ old('expiry_date') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('expiry_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="consent_version" class="block text-sm font-medium text-gray-700">Consent Version (Optional)</label>
                        <input type="text" id="consent_version" name="consent_version" value="{{ old('consent_version') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('consent_version')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="consent_language" class="block text-sm font-medium text-gray-700">Consent Language</label>
                        <input type="text" id="consent_language" name="consent_language" value="{{ old('consent_language', app()->getLocale()) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                        @error('consent_language')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label for="consent_text" class="block text-sm font-medium text-gray-700">Consent Text</label>
                        <textarea id="consent_text" name="consent_text" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>{{ old('consent_text') }}</textarea>
                        @error('consent_text')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="consent_evidence" class="block text-sm font-medium text-gray-700">Consent Evidence</label>
                        <select id="consent_evidence" name="consent_evidence" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select evidence type</option>
                            <option value="screenshot" {{ old('consent_evidence') == 'screenshot' ? 'selected' : '' }}>Screenshot</option>
                            <option value="document" {{ old('consent_evidence') == 'document' ? 'selected' : '' }}>Document</option>
                            <option value="audio_recording" {{ old('consent_evidence') == 'audio_recording' ? 'selected' : '' }}>Audio Recording</option>
                            <option value="video_recording" {{ old('consent_evidence') == 'video_recording' ? 'selected' : '' }}>Video Recording</option>
                            <option value="email_confirmation" {{ old('consent_evidence') == 'email_confirmation' ? 'selected' : '' }}>Email Confirmation</option>
                            <option value="digital_signature" {{ old('consent_evidence') == 'digital_signature' ? 'selected' : '' }}>Digital Signature</option>
                        </select>
                        @error('consent_evidence')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="consent_evidence_file" class="block text-sm font-medium text-gray-700">Evidence File (Optional, max 10MB)</label>
                        <input type="file" id="consent_evidence_file" name="consent_evidence_file" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('consent_evidence_file')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label for="consent_notes" class="block text-sm font-medium text-gray-700">Notes (Optional)</label>
                        <textarea id="consent_notes" name="consent_notes" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('consent_notes') }}</textarea>
                        @error('consent_notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="ip_address" class="block text-sm font-medium text-gray-700">IP Address</label>
                        <input type="text" id="ip_address" name="ip_address" value="{{ old('ip_address', request()->ip()) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('ip_address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="user_agent" class="block text-sm font-medium text-gray-700">User Agent</label>
                        <input type="text" id="user_agent" name="user_agent" value="{{ old('user_agent', request()->header('User-Agent')) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('user_agent')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <a href="{{ route('consent-records.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        Create Consent Record
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
