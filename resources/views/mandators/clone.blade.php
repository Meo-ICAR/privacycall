@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('mandators.index') }}" class="text-blue-600 hover:text-blue-800 mr-4">
            <i class="fas fa-arrow-left"></i> Back to Mandators
        </a>
        <h1 class="text-3xl font-bold text-gray-900 mt-4">Clone Mandator</h1>
        <p class="text-gray-600 mt-2">Clone {{ $mandator->full_name }} to another company</p>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('mandators.clone', $mandator) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Original Mandator Information -->
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Original Mandator</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Name</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $mandator->full_name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Email</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $mandator->email }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Position</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $mandator->position ?? 'Not specified' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Company</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $mandator->company->name }}</p>
                    </div>
                </div>
            </div>

            <!-- Target Company Selection -->
            <div class="mb-6">
                <label for="target_company_id" class="block text-sm font-medium text-gray-700 mb-2">Target Company *</label>
                <select name="target_company_id" id="target_company_id" required
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select target company</option>
                    @foreach($companies as $company)
                        @if($company->id !== $mandator->company_id)
                            <option value="{{ $company->id }}" {{ old('target_company_id') == $company->id ? 'selected' : '' }}>
                                {{ $company->name }}
                            </option>
                        @endif
                    @endforeach
                </select>
                @error('target_company_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Clone Options -->
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Clone Options</h3>
                <div class="space-y-4">
                    <div class="flex items-center">
                        <input type="checkbox" name="clone_contact_preferences" id="clone_contact_preferences" value="1"
                               {{ old('clone_contact_preferences', true) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="clone_contact_preferences" class="ml-2 block text-sm text-gray-900">
                            Clone contact preferences (email/SMS notifications, preferred contact method)
                        </label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="clone_disclosure_subscriptions" id="clone_disclosure_subscriptions" value="1"
                               {{ old('clone_disclosure_subscriptions', true) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="clone_disclosure_subscriptions" class="ml-2 block text-sm text-gray-900">
                            Clone disclosure subscriptions
                        </label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="clone_profile_picture" id="clone_profile_picture" value="1"
                               {{ old('clone_profile_picture', true) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="clone_profile_picture" class="ml-2 block text-sm text-gray-900">
                            Clone profile picture
                        </label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="clone_notes" id="clone_notes" value="1"
                               {{ old('clone_notes', false) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="clone_notes" class="ml-2 block text-sm text-gray-900">
                            Clone notes
                        </label>
                    </div>
                </div>
            </div>

            <!-- Override Fields -->
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Override Fields (Optional)</h3>
                <p class="text-sm text-gray-600 mb-4">You can override specific fields for the cloned mandator:</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="override_email" class="block text-sm font-medium text-gray-700 mb-2">Override Email</label>
                        <input type="email" name="override_email" id="override_email" value="{{ old('override_email') }}"
                               placeholder="Leave empty to use original email"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('override_email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="override_phone" class="block text-sm font-medium text-gray-700 mb-2">Override Phone</label>
                        <input type="tel" name="override_phone" id="override_phone" value="{{ old('override_phone') }}"
                               placeholder="Leave empty to use original phone"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('override_phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="override_position" class="block text-sm font-medium text-gray-700 mb-2">Override Position</label>
                        <input type="text" name="override_position" id="override_position" value="{{ old('override_position') }}"
                               placeholder="Leave empty to use original position"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('override_position')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="override_department" class="block text-sm font-medium text-gray-700 mb-2">Override Department</label>
                        <input type="text" name="override_department" id="override_department" value="{{ old('override_department') }}"
                               placeholder="Leave empty to use original department"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('override_department')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('mandators.index') }}"
                   class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-md">
                    Cancel
                </a>
                <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md">
                    <i class="fas fa-copy mr-2"></i>Clone Mandator
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
