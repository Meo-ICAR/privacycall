@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('mandators.index') }}" class="text-blue-600 hover:text-blue-800 mr-4">
            <i class="fas fa-arrow-left"></i> Back to Mandators
        </a>
        <h1 class="text-3xl font-bold text-gray-900 mt-4">Add New Mandator</h1>
        <p class="text-gray-600 mt-2">Create a new mandator for your company</p>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('mandators.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Company Selection -->
            <div class="mb-6">
                <label for="company_id" class="block text-sm font-medium text-gray-700 mb-2">Company *</label>
                @if($company)
                    <input type="hidden" name="company_id" value="{{ $company->id }}">
                    <div class="p-3 bg-gray-50 rounded-md">
                        <span class="text-sm font-medium text-gray-900">{{ $company->name }}</span>
                    </div>
                @else
                    <select name="company_id" id="company_id" required
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select a company</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                {{ $company->name }}
                            </option>
                        @endforeach
                    </select>
                @endif
                @error('company_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Personal Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">First Name *</label>
                    <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" required
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('first_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                    <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('last_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Contact Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                    <input type="tel" name="phone" id="phone" value="{{ old('phone') }}"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Position Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="position" class="block text-sm font-medium text-gray-700 mb-2">Position</label>
                    <input type="text" name="position" id="position" value="{{ old('position') }}"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('position')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="department" class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                    <input type="text" name="department" id="department" value="{{ old('department') }}"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('department')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Logo Upload -->
            <div class="mb-6">
                <label for="logo" class="block text-sm font-medium text-gray-700 mb-2">Profile Picture</label>
                <input type="file" name="logo" id="logo" accept="image/*"
                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <p class="mt-1 text-sm text-gray-500">Upload a profile picture (JPEG, PNG, JPG, GIF, SVG, max 2MB)</p>
                @error('logo')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Contact Preferences -->
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Contact Preferences</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="flex items-center">
                        <input type="checkbox" name="email_notifications" id="email_notifications" value="1"
                               {{ old('email_notifications') ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="email_notifications" class="ml-2 block text-sm text-gray-900">
                            Email Notifications
                        </label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="sms_notifications" id="sms_notifications" value="1"
                               {{ old('sms_notifications') ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="sms_notifications" class="ml-2 block text-sm text-gray-900">
                            SMS Notifications
                        </label>
                    </div>
                    <div>
                        <label for="preferred_contact_method" class="block text-sm font-medium text-gray-700 mb-1">Preferred Contact Method</label>
                        <select name="preferred_contact_method" id="preferred_contact_method"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="email" {{ old('preferred_contact_method') == 'email' ? 'selected' : '' }}>Email</option>
                            <option value="phone" {{ old('preferred_contact_method') == 'phone' ? 'selected' : '' }}>Phone</option>
                            <option value="sms" {{ old('preferred_contact_method') == 'sms' ? 'selected' : '' }}>SMS</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Disclosure Subscriptions -->
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Disclosure Subscriptions</h3>
                <p class="text-sm text-gray-600 mb-4">Select which types of disclosures this mandator should receive notifications for:</p>

                @if($disclosureTypes->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @php
                            $selectedSubscriptions = old('disclosure_subscriptions', []);
                        @endphp
                        @foreach($disclosureTypes as $disclosureType)
                            <div class="flex items-start">
                                <input type="checkbox" name="disclosure_subscriptions[]" id="disclosure_{{ $disclosureType->id }}"
                                       value="{{ $disclosureType->name }}"
                                       {{ in_array($disclosureType->name, $selectedSubscriptions) ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded mt-1">
                                <label for="disclosure_{{ $disclosureType->id }}" class="ml-2 block text-sm text-gray-900">
                                    <div class="font-medium">{{ $disclosureType->display_name }}</div>
                                    @if($disclosureType->description)
                                        <div class="text-gray-500 text-xs">{{ $disclosureType->description }}</div>
                                    @endif
                                </label>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4 text-gray-500">
                        <i class="fas fa-info-circle text-2xl mb-2"></i>
                        <p>No disclosure types available. Please contact a superadmin to create disclosure types.</p>
                    </div>
                @endif

                @error('disclosure_subscriptions')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div class="mb-6">
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1"
                           {{ old('is_active', true) ? 'checked' : '' }}
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="is_active" class="ml-2 block text-sm text-gray-900">
                        Active Mandator
                    </label>
                </div>
                <p class="mt-1 text-sm text-gray-500">Inactive mandators won't receive notifications</p>
            </div>

            <!-- Notes -->
            <div class="mb-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                <textarea name="notes" id="notes" rows="3"
                          class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('mandators.index') }}"
                   class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-md">
                    Cancel
                </a>
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                    Create Mandator
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
