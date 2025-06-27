@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route('data-processing-activities.index') }}" class="text-blue-600 hover:text-blue-800 mr-4">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Create Data Processing Activity</h1>
                    <p class="mt-2 text-sm text-gray-600">Define a new data processing activity for {{ $company ? $company->name : 'No company assigned' }}</p>
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
            <form action="{{ route('data-processing-activities.store') }}" method="POST">
                @csrf

                <!-- Company Information (Read-only) -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Company Information</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="text-sm text-gray-900">
                            <strong>Company:</strong> {{ $company ? $company->name : 'No company assigned' }}
                        </div>
                    </div>
                </div>

                <!-- Activity Information -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Activity Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="activity_name" class="block text-sm font-medium text-gray-700 mb-2">Activity Name *</label>
                            <input type="text" name="activity_name" id="activity_name"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('activity_name') border-red-500 @enderror"
                                   value="{{ old('activity_name') }}" required>
                            @error('activity_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="processing_purpose" class="block text-sm font-medium text-gray-700 mb-2">Processing Purpose *</label>
                            <select name="processing_purpose" id="processing_purpose"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('processing_purpose') border-red-500 @enderror"
                                    required>
                                <option value="">Select Purpose</option>
                                <option value="marketing" {{ old('processing_purpose') == 'marketing' ? 'selected' : '' }}>Marketing</option>
                                <option value="customer_service" {{ old('processing_purpose') == 'customer_service' ? 'selected' : '' }}>Customer Service</option>
                                <option value="hr_management" {{ old('processing_purpose') == 'hr_management' ? 'selected' : '' }}>HR Management</option>
                                <option value="financial_processing" {{ old('processing_purpose') == 'financial_processing' ? 'selected' : '' }}>Financial Processing</option>
                                <option value="legal_compliance" {{ old('processing_purpose') == 'legal_compliance' ? 'selected' : '' }}>Legal Compliance</option>
                                <option value="security" {{ old('processing_purpose') == 'security' ? 'selected' : '' }}>Security</option>
                                <option value="other" {{ old('processing_purpose') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('processing_purpose')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6">
                        <label for="activity_description" class="block text-sm font-medium text-gray-700 mb-2">Activity Description</label>
                        <textarea name="activity_description" id="activity_description" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('activity_description') border-red-500 @enderror">{{ old('activity_description') }}</textarea>
                        @error('activity_description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Legal Basis and Data Categories -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Legal Basis & Data Categories</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="legal_basis" class="block text-sm font-medium text-gray-700 mb-2">Legal Basis *</label>
                            <select name="legal_basis" id="legal_basis"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('legal_basis') border-red-500 @enderror"
                                    required>
                                <option value="">Select Legal Basis</option>
                                <option value="consent" {{ old('legal_basis') == 'consent' ? 'selected' : '' }}>Consent</option>
                                <option value="contract" {{ old('legal_basis') == 'contract' ? 'selected' : '' }}>Contract</option>
                                <option value="legal_obligation" {{ old('legal_basis') == 'legal_obligation' ? 'selected' : '' }}>Legal Obligation</option>
                                <option value="vital_interests" {{ old('legal_basis') == 'vital_interests' ? 'selected' : '' }}>Vital Interests</option>
                                <option value="public_task" {{ old('legal_basis') == 'public_task' ? 'selected' : '' }}>Public Task</option>
                                <option value="legitimate_interests" {{ old('legal_basis') == 'legitimate_interests' ? 'selected' : '' }}>Legitimate Interests</option>
                            </select>
                            @error('legal_basis')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="data_categories" class="block text-sm font-medium text-gray-700 mb-2">Data Categories *</label>
                            <select name="data_categories" id="data_categories"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('data_categories') border-red-500 @enderror"
                                    required>
                                <option value="">Select Data Categories</option>
                                <option value="personal_data" {{ old('data_categories') == 'personal_data' ? 'selected' : '' }}>Personal Data</option>
                                <option value="sensitive_data" {{ old('data_categories') == 'sensitive_data' ? 'selected' : '' }}>Sensitive Data</option>
                                <option value="special_categories" {{ old('data_categories') == 'special_categories' ? 'selected' : '' }}>Special Categories</option>
                                <option value="criminal_data" {{ old('data_categories') == 'criminal_data' ? 'selected' : '' }}>Criminal Data</option>
                            </select>
                            @error('data_categories')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Data Subjects and Recipients -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Data Subjects & Recipients</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="data_subjects" class="block text-sm font-medium text-gray-700 mb-2">Data Subjects</label>
                            <select name="data_subjects" id="data_subjects"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('data_subjects') border-red-500 @enderror">
                                <option value="">Select Data Subjects</option>
                                <option value="employees" {{ old('data_subjects') == 'employees' ? 'selected' : '' }}>Employees</option>
                                <option value="customers" {{ old('data_subjects') == 'customers' ? 'selected' : '' }}>Customers</option>
                                <option value="suppliers" {{ old('data_subjects') == 'suppliers' ? 'selected' : '' }}>Suppliers</option>
                                <option value="visitors" {{ old('data_subjects') == 'visitors' ? 'selected' : '' }}>Visitors</option>
                                <option value="prospects" {{ old('data_subjects') == 'prospects' ? 'selected' : '' }}>Prospects</option>
                            </select>
                            @error('data_subjects')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="data_recipients" class="block text-sm font-medium text-gray-700 mb-2">Data Recipients</label>
                            <select name="data_recipients" id="data_recipients"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('data_recipients') border-red-500 @enderror">
                                <option value="">Select Data Recipients</option>
                                <option value="internal" {{ old('data_recipients') == 'internal' ? 'selected' : '' }}>Internal</option>
                                <option value="external" {{ old('data_recipients') == 'external' ? 'selected' : '' }}>External</option>
                                <option value="third_parties" {{ old('data_recipients') == 'third_parties' ? 'selected' : '' }}>Third Parties</option>
                            </select>
                            @error('data_recipients')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Retention and Security -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Retention & Security</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="retention_period" class="block text-sm font-medium text-gray-700 mb-2">Retention Period *</label>
                            <select name="retention_period" id="retention_period"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('retention_period') border-red-500 @enderror"
                                    required>
                                <option value="">Select Retention Period</option>
                                <option value="30_days" {{ old('retention_period') == '30_days' ? 'selected' : '' }}>30 Days</option>
                                <option value="90_days" {{ old('retention_period') == '90_days' ? 'selected' : '' }}>90 Days</option>
                                <option value="1_year" {{ old('retention_period') == '1_year' ? 'selected' : '' }}>1 Year</option>
                                <option value="3_years" {{ old('retention_period') == '3_years' ? 'selected' : '' }}>3 Years</option>
                                <option value="5_years" {{ old('retention_period') == '5_years' ? 'selected' : '' }}>5 Years</option>
                                <option value="7_years" {{ old('retention_period') == '7_years' ? 'selected' : '' }}>7 Years</option>
                                <option value="10_years" {{ old('retention_period') == '10_years' ? 'selected' : '' }}>10 Years</option>
                                <option value="indefinite" {{ old('retention_period') == 'indefinite' ? 'selected' : '' }}>Indefinite</option>
                            </select>
                            @error('retention_period')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="security_measures" class="block text-sm font-medium text-gray-700 mb-2">Security Measures</label>
                            <textarea name="security_measures" id="security_measures" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('security_measures') border-red-500 @enderror">{{ old('security_measures') }}</textarea>
                            @error('security_measures')
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
                            <label for="is_active" class="ml-2 block text-sm text-gray-900">Active Activity</label>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <a href="{{ route('data-processing-activities.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <i class="fas fa-times mr-2"></i>
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <i class="fas fa-save mr-2"></i>
                        Create Activity
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
