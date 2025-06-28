@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Clone Representative</h1>
                    <p class="text-gray-600 mt-2">Clone "{{ $representative->full_name }}" to another company</p>
                </div>
                <a href="{{ route('representatives.show', $representative) }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                    Back to Representative
                </a>
            </div>
        </div>

        <!-- Original Representative Info -->
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Original Representative</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="flex items-center">
                        <img src="{{ $representative->logo_url }}"
                             alt="{{ $representative->full_name }}"
                             class="w-16 h-16 rounded-full object-cover mr-4">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">{{ $representative->full_name }}</h3>
                            <p class="text-gray-600">{{ $representative->position }}</p>
                            <p class="text-sm text-gray-500">{{ $representative->company->name }}</p>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div><span class="font-medium">Email:</span> {{ $representative->email }}</div>
                        <div><span class="font-medium">Phone:</span> {{ $representative->phone ?? 'N/A' }}</div>
                        <div><span class="font-medium">Department:</span> {{ $representative->department ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Clone Form -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-6">Clone Settings</h2>

                <form method="POST" action="{{ route('representatives.clone', $representative) }}" enctype="multipart/form-data">
                    @csrf

                    <!-- Target Company Selection -->
                    <div class="mb-6">
                        <label for="target_company_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Target Company <span class="text-red-500">*</span>
                        </label>
                        <select name="target_company_id" id="target_company_id" required
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('target_company_id') border-red-500 @enderror">
                            <option value="">Select a company to clone to...</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ old('target_company_id') == $company->id ? 'selected' : '' }}>
                                    {{ $company->name }} ({{ $company->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('target_company_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Override Fields -->
                    <div class="mb-6">
                        <h3 class="text-md font-medium text-gray-900 mb-4">Override Fields (Optional)</h3>
                        <p class="text-sm text-gray-600 mb-4">You can modify these fields for the cloned representative. Leave blank to keep original values.</p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                                <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $representative->first_name) }}"
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('first_name') border-red-500 @enderror"
                                       placeholder="Enter first name">
                                @error('first_name')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                                <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $representative->last_name) }}"
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('last_name') border-red-500 @enderror"
                                       placeholder="Enter last name">
                                @error('last_name')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <input type="email" name="email" id="email" value="{{ old('email', $representative->email) }}"
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror"
                                       placeholder="Enter email address">
                                <p class="text-sm text-gray-500 mt-1">A unique suffix will be added if email already exists</p>
                                @error('email')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                                <input type="text" name="phone" id="phone" value="{{ old('phone', $representative->phone) }}"
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('phone') border-red-500 @enderror"
                                       placeholder="Enter phone number">
                                @error('phone')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="position" class="block text-sm font-medium text-gray-700 mb-2">Position</label>
                                <input type="text" name="position" id="position" value="{{ old('position', $representative->position) }}"
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('position') border-red-500 @enderror"
                                       placeholder="Enter job position">
                                @error('position')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="department" class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                                <input type="text" name="department" id="department" value="{{ old('department', $representative->department) }}"
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('department') border-red-500 @enderror"
                                       placeholder="Enter department">
                                @error('department')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Status and Notes -->
                    <div class="mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="flex items-center">
                                <input type="checkbox" name="is_active" id="is_active" value="1"
                                       {{ old('is_active', $representative->is_active) ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="is_active" class="ml-2 text-sm text-gray-700">
                                    Active Representative
                                </label>
                            </div>

                            <div class="md:col-span-2">
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                                <textarea name="notes" id="notes" rows="3"
                                          class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('notes') border-red-500 @enderror"
                                          placeholder="Additional notes about the cloned representative...">{{ old('notes', $representative->notes) }}</textarea>
                                @error('notes')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Clone Information -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">What will be cloned?</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <ul class="list-disc list-inside space-y-1">
                                        <li>All disclosure subscriptions</li>
                                        <li>Contact preferences and notification settings</li>
                                        <li>Logo (if uploaded)</li>
                                        <li>All other representative data</li>
                                    </ul>
                                </div>
                                <div class="mt-2 text-sm text-blue-700">
                                    <p><strong>Note:</strong> The cloned representative will maintain a reference to the original record for tracking purposes.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('representatives.show', $representative) }}"
                           class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                            Cancel
                        </a>
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Clone Representative
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-generate email suffix when company changes
    const companySelect = document.getElementById('target_company_id');
    const emailInput = document.getElementById('email');
    const originalEmail = '{{ $representative->email }}';

    companySelect.addEventListener('change', function() {
        if (this.value) {
            const selectedCompany = this.options[this.selectedIndex].text.split(' (')[0];
            const domain = selectedCompany.toLowerCase().replace(/\s+/g, '') + '.com';
            const emailPrefix = originalEmail.split('@')[0];
            emailInput.value = emailPrefix + '@' + domain;
        }
    });
});
</script>
@endsection
