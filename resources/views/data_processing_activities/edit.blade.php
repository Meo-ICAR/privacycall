@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('data-processing-activities.index') }}" class="text-blue-600 hover:text-blue-800 mr-4">
            <i class="fas fa-arrow-left"></i> Back to Activities
        </a>
        <h1 class="text-3xl font-bold text-gray-900 mt-4">Edit Data Processing Activity</h1>
        <p class="text-gray-600 mt-2">Update activity information and settings</p>
    </div>

    <!-- Form -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form action="{{ route('data-processing-activities.update', $dataProcessingActivity) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Activity Name -->
                    <div class="sm:col-span-2">
                        <label for="activity_name" class="block text-sm font-medium text-gray-700">Activity Name *</label>
                        <input type="text" name="activity_name" id="activity_name" value="{{ old('activity_name', $dataProcessingActivity->activity_name) }}" required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        @error('activity_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Processing Purpose -->
                    <div class="sm:col-span-2">
                        <label for="processing_purpose" class="block text-sm font-medium text-gray-700">Processing Purpose *</label>
                        <textarea name="processing_purpose" id="processing_purpose" rows="3" required
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ old('processing_purpose', $dataProcessingActivity->processing_purpose) }}</textarea>
                        @error('processing_purpose')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Legal Basis -->
                    <div>
                        <label for="legal_basis" class="block text-sm font-medium text-gray-700">Legal Basis *</label>
                        <select name="legal_basis" id="legal_basis" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">Select Legal Basis</option>
                            <option value="Consent" {{ old('legal_basis', $dataProcessingActivity->legal_basis) == 'Consent' ? 'selected' : '' }}>Consent</option>
                            <option value="Contract" {{ old('legal_basis', $dataProcessingActivity->legal_basis) == 'Contract' ? 'selected' : '' }}>Contract</option>
                            <option value="Legal Obligation" {{ old('legal_basis', $dataProcessingActivity->legal_basis) == 'Legal Obligation' ? 'selected' : '' }}>Legal Obligation</option>
                            <option value="Vital Interests" {{ old('legal_basis', $dataProcessingActivity->legal_basis) == 'Vital Interests' ? 'selected' : '' }}>Vital Interests</option>
                            <option value="Public Task" {{ old('legal_basis', $dataProcessingActivity->legal_basis) == 'Public Task' ? 'selected' : '' }}>Public Task</option>
                            <option value="Legitimate Interests" {{ old('legal_basis', $dataProcessingActivity->legal_basis) == 'Legitimate Interests' ? 'selected' : '' }}>Legitimate Interests</option>
                        </select>
                        @error('legal_basis')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Retention Period -->
                    <div>
                        <label for="retention_period" class="block text-sm font-medium text-gray-700">Retention Period *</label>
                        <input type="text" name="retention_period" id="retention_period" value="{{ old('retention_period', $dataProcessingActivity->retention_period) }}" required
                               placeholder="e.g., 7 years, 30 days, etc."
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        @error('retention_period')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Data Categories -->
                    <div class="sm:col-span-2">
                        <label for="data_categories" class="block text-sm font-medium text-gray-700">Data Categories *</label>
                        <textarea name="data_categories" id="data_categories" rows="3" required
                                  placeholder="e.g., Personal data, Contact information, Financial data, etc."
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ old('data_categories', $dataProcessingActivity->data_categories) }}</textarea>
                        @error('data_categories')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Data Recipients -->
                    <div class="sm:col-span-2">
                        <label for="data_recipients" class="block text-sm font-medium text-gray-700">Data Recipients</label>
                        <textarea name="data_recipients" id="data_recipients" rows="2"
                                  placeholder="e.g., Internal departments, Third-party vendors, etc."
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ old('data_recipients', $dataProcessingActivity->data_recipients) }}</textarea>
                        @error('data_recipients')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Security Measures -->
                    <div class="sm:col-span-2">
                        <label for="security_measures" class="block text-sm font-medium text-gray-700">Security Measures</label>
                        <textarea name="security_measures" id="security_measures" rows="3"
                                  placeholder="e.g., Encryption, Access controls, Data minimization, etc."
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ old('security_measures', $dataProcessingActivity->security_measures) }}</textarea>
                        @error('security_measures')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Active Status -->
                    <div class="sm:col-span-2">
                        <div class="flex items-center">
                            <input type="checkbox" name="is_active" id="is_active" value="1"
                                   {{ old('is_active', $dataProcessingActivity->is_active) ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                Active
                            </label>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">Check this box to mark the activity as active</p>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="mt-8 flex space-x-3">
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-save mr-2"></i>
                        Update Activity
                    </button>
                    <a href="{{ route('data-processing-activities.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-times mr-2"></i>
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
