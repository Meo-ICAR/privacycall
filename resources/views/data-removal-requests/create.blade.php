<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Data Removal Request') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Information Alert -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <div class="flex items-center">
                            <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                            <span class="text-sm text-blue-800">Create a new data removal request for GDPR compliance. This will initiate the "right to be forgotten" process.</span>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('data-removal-requests.store') }}">
                        @csrf

                        <!-- Request Type and Priority -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="request_type" class="block text-sm font-medium text-gray-700">Request Type *</label>
                                <select name="request_type" id="request_type" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Request Type</option>
                                    <option value="customer_direct" {{ old('request_type') == 'customer_direct' ? 'selected' : '' }}>Customer Direct Request</option>
                                    <option value="mandator_request" {{ old('request_type') == 'mandator_request' ? 'selected' : '' }}>Mandator Request</option>
                                    <option value="legal_obligation" {{ old('request_type') == 'legal_obligation' ? 'selected' : '' }}>Legal Obligation</option>
                                    <option value="system_cleanup" {{ old('request_type') == 'system_cleanup' ? 'selected' : '' }}>System Cleanup</option>
                                </select>
                                @error('request_type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="priority" class="block text-sm font-medium text-gray-700">Priority *</label>
                                <select name="priority" id="priority" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Priority</option>
                                    <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                                    <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                </select>
                                @error('priority')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Subject Selection -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Subject *</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="customer_id" class="block text-sm font-medium text-gray-700">Customer</label>
                                    <select name="customer_id" id="customer_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Select Customer</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->first_name }} {{ $customer->last_name }} ({{ $customer->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="mandator_id" class="block text-sm font-medium text-gray-700">Mandator</label>
                                    <select name="mandator_id" id="mandator_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Select Mandator</option>
                                        @foreach($mandators as $mandator)
                                            <option value="{{ $mandator->id }}" {{ old('mandator_id') == $mandator->id ? 'selected' : '' }}>
                                                {{ $mandator->first_name }} {{ $mandator->last_name }} ({{ $mandator->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <p class="mt-1 text-sm text-gray-500">Select either a customer or mandator for this request.</p>
                            @error('customer_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Reason for Removal -->
                        <div class="mb-6">
                            <label for="reason_for_removal" class="block text-sm font-medium text-gray-700">Reason for Removal *</label>
                            <textarea name="reason_for_removal" id="reason_for_removal" rows="4" required
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Describe the reason for the data removal request...">{{ old('reason_for_removal') }}</textarea>
                            @error('reason_for_removal')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Data Categories -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Data Categories to Remove</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                @php
                                    $dataCategories = [
                                        'personal_info' => 'Personal Information',
                                        'contact_details' => 'Contact Details',
                                        'financial_data' => 'Financial Data',
                                        'transaction_history' => 'Transaction History',
                                        'communication_records' => 'Communication Records',
                                        'preferences' => 'Preferences & Settings',
                                        'marketing_data' => 'Marketing Data',
                                        'analytics_data' => 'Analytics Data',
                                        'all_data' => 'All Data'
                                    ];
                                    $selectedCategories = old('data_categories_to_remove', []);
                                @endphp
                                @foreach($dataCategories as $key => $label)
                                    <label class="flex items-center">
                                        <input type="checkbox" name="data_categories_to_remove[]" value="{{ $key }}"
                                               {{ in_array($key, $selectedCategories) ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-700">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Due Date -->
                        <div class="mb-6">
                            <label for="due_date" class="block text-sm font-medium text-gray-700">Due Date</label>
                            <input type="date" name="due_date" id="due_date"
                                   value="{{ old('due_date') }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <p class="mt-1 text-sm text-gray-500">Leave empty if no specific deadline is required.</p>
                            @error('due_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Identity Verification -->
                        <div class="mb-6">
                            <div class="flex items-center">
                                <input type="checkbox" name="identity_verified" id="identity_verified" value="1"
                                       {{ old('identity_verified') ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <label for="identity_verified" class="ml-2 block text-sm text-gray-700">Identity Verified</label>
                            </div>

                            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="verification_method" class="block text-sm font-medium text-gray-700">Verification Method</label>
                                    <select name="verification_method" id="verification_method" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Select Method</option>
                                        <option value="email_verification" {{ old('verification_method') == 'email_verification' ? 'selected' : '' }}>Email Verification</option>
                                        <option value="phone_verification" {{ old('verification_method') == 'phone_verification' ? 'selected' : '' }}>Phone Verification</option>
                                        <option value="document_verification" {{ old('verification_method') == 'document_verification' ? 'selected' : '' }}>Document Verification</option>
                                        <option value="mandator_confirmation" {{ old('verification_method') == 'mandator_confirmation' ? 'selected' : '' }}>Mandator Confirmation</option>
                                        <option value="other" {{ old('verification_method') == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="verification_notes" class="block text-sm font-medium text-gray-700">Verification Notes</label>
                                    <textarea name="verification_notes" id="verification_notes" rows="2"
                                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                              placeholder="Additional notes about verification...">{{ old('verification_notes') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Third Party Notification -->
                        <div class="mb-6">
                            <div class="flex items-center">
                                <input type="checkbox" name="notify_third_parties" id="notify_third_parties" value="1"
                                       {{ old('notify_third_parties') ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <label for="notify_third_parties" class="ml-2 block text-sm text-gray-700">Notify Third Parties</label>
                            </div>

                            <div class="mt-4">
                                <label for="third_party_notification_details" class="block text-sm font-medium text-gray-700">Third Party Notification Details</label>
                                <textarea name="third_party_notification_details" id="third_party_notification_details" rows="3"
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                          placeholder="Details about third parties to be notified...">{{ old('third_party_notification_details') }}</textarea>
                            </div>
                        </div>

                        <!-- Retention Justification (if needed) -->
                        <div class="mb-6">
                            <label for="retention_justification" class="block text-sm font-medium text-gray-700">Retention Justification</label>
                            <textarea name="retention_justification" id="retention_justification" rows="3"
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="If any data needs to be retained, provide justification...">{{ old('retention_justification') }}</textarea>
                            <p class="mt-1 text-sm text-gray-500">Only required if some data needs to be retained for legal or business reasons.</p>
                        </div>

                        <!-- Legal Basis for Retention -->
                        <div class="mb-6">
                            <label for="legal_basis_for_retention" class="block text-sm font-medium text-gray-700">Legal Basis for Retention</label>
                            <input type="text" name="legal_basis_for_retention" id="legal_basis_for_retention"
                                   value="{{ old('legal_basis_for_retention') }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="e.g., Legal obligation, Contract performance, Legitimate interest">
                            <p class="mt-1 text-sm text-gray-500">Specify the legal basis if any data needs to be retained.</p>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('data-removal-requests.index') }}"
                               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-times mr-2"></i>
                                Cancel
                            </a>
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-save mr-2"></i>
                                Create Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // JavaScript to ensure only one subject is selected
        document.addEventListener('DOMContentLoaded', function() {
            const customerSelect = document.getElementById('customer_id');
            const mandatorSelect = document.getElementById('mandator_id');

            customerSelect.addEventListener('change', function() {
                if (this.value) {
                    mandatorSelect.value = '';
                }
            });

            mandatorSelect.addEventListener('change', function() {
                if (this.value) {
                    customerSelect.value = '';
                }
            });
        });
    </script>
</x-app-layout>
