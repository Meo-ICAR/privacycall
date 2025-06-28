<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Representative - PrivacyCall</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <h1 class="text-xl font-bold text-gray-800">
                            <i class="fas fa-shield-alt text-blue-600 mr-2"></i>
                            PrivacyCall
                        </h1>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="/" class="text-gray-600 hover:text-gray-900">Home</a>
                    <a href="/dashboard" class="text-gray-600 hover:text-gray-900">Dashboard</a>
                    <a href="/companies" class="text-gray-600 hover:text-gray-900">Companies</a>
                    <a href="/representatives" class="text-blue-600 font-medium">Representatives</a>
                    <a href="/gdpr" class="text-gray-600 hover:text-gray-900">GDPR</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center">
                <a href="{{ route('representatives.index') }}" class="text-blue-600 hover:text-blue-800 mr-4">
                    <i class="fas fa-arrow-left"></i> Back to Representatives
                </a>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mt-2">Edit Representative</h1>
            <p class="text-gray-600 mt-2">Update representative information and disclosure subscriptions</p>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('representatives.update', $representative) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Basic Information -->
                <div class="mb-8">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <input type="hidden" name="company_id" value="{{ $company->id }}">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Company</label>
                            <div class="p-2 bg-gray-100 rounded">{{ $company->name }}</div>
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" id="email" value="{{ old('email', $representative->email) }}" required
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror"
                                   placeholder="representative@company.com">
                            @error('email')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                                First Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $representative->first_name) }}" required
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('first_name') border-red-500 @enderror"
                                   placeholder="John">
                            @error('first_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Last Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $representative->last_name) }}" required
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('last_name') border-red-500 @enderror"
                                   placeholder="Doe">
                            @error('last_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                            <input type="tel" name="phone" id="phone" value="{{ old('phone', $representative->phone) }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('phone') border-red-500 @enderror"
                                   placeholder="+1234567890">
                            @error('phone')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="position" class="block text-sm font-medium text-gray-700 mb-2">Position</label>
                            <input type="text" name="position" id="position" value="{{ old('position', $representative->position) }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('position') border-red-500 @enderror"
                                   placeholder="Data Protection Officer">
                            @error('position')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="department" class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                            <input type="text" name="department" id="department" value="{{ old('department', $representative->department) }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('department') border-red-500 @enderror"
                                   placeholder="Legal">
                            @error('department')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Disclosure Subscriptions -->
                <div class="mb-8">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Disclosure Subscriptions</h2>
                    <p class="text-gray-600 mb-4">Select the types of disclosures this representative should receive notifications for:</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @php
                            $disclosureTypes = [
                                'gdpr_updates' => 'GDPR Updates',
                                'data_breach_notifications' => 'Data Breach Notifications',
                                'privacy_policy_changes' => 'Privacy Policy Changes',
                                'consent_management' => 'Consent Management',
                                'security_updates' => 'Security Updates',
                                'employee_data_processing' => 'Employee Data Processing',
                                'third_party_disclosures' => 'Third Party Disclosures',
                                'data_retention_changes' => 'Data Retention Changes'
                            ];
                            $currentSubscriptions = old('disclosure_subscriptions', $representative->disclosure_subscriptions ?? []);
                        @endphp

                        @foreach($disclosureTypes as $value => $label)
                            <div class="flex items-center">
                                <input type="checkbox" name="disclosure_subscriptions[]" id="disclosure_{{ $value }}"
                                       value="{{ $value }}"
                                       {{ in_array($value, $currentSubscriptions) ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="disclosure_{{ $value }}" class="ml-2 text-sm text-gray-700">
                                    {{ $label }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                    @error('disclosure_subscriptions')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Contact Preferences -->
                <div class="mb-8">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Contact Preferences</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="preferred_contact_method" class="block text-sm font-medium text-gray-700 mb-2">
                                Preferred Contact Method
                            </label>
                            <select name="preferred_contact_method" id="preferred_contact_method"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('preferred_contact_method') border-red-500 @enderror">
                                <option value="email" {{ old('preferred_contact_method', $representative->preferred_contact_method) == 'email' ? 'selected' : '' }}>Email</option>
                                <option value="phone" {{ old('preferred_contact_method', $representative->preferred_contact_method) == 'phone' ? 'selected' : '' }}>Phone</option>
                                <option value="sms" {{ old('preferred_contact_method', $representative->preferred_contact_method) == 'sms' ? 'selected' : '' }}>SMS</option>
                            </select>
                            @error('preferred_contact_method')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" name="email_notifications" id="email_notifications" value="1"
                                   {{ old('email_notifications', $representative->email_notifications) ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="email_notifications" class="ml-2 text-sm text-gray-700">
                                Email Notifications
                            </label>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" name="sms_notifications" id="sms_notifications" value="1"
                                   {{ old('sms_notifications', $representative->sms_notifications) ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="sms_notifications" class="ml-2 text-sm text-gray-700">
                                SMS Notifications
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Status and Notes -->
                <div class="mb-8">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Status & Notes</h2>
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
                                      placeholder="Additional notes about this representative...">{{ old('notes', $representative->notes) }}</textarea>
                            @error('notes')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('representatives.index') }}"
                       class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-md">
                        Cancel
                    </a>
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md">
                        Update Representative
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
