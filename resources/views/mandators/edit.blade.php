@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('mandators.index') }}" class="text-blue-600 hover:text-blue-800 mr-4">
            <i class="fas fa-arrow-left"></i> Back to Mandators
        </a>
        <h1 class="text-3xl font-bold text-gray-900 mt-4">Edit Mandator</h1>
        <p class="text-gray-600 mt-2">Update mandator information and preferences</p>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('mandators.update', $mandator) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Company Information -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Company</label>
                <div class="p-3 bg-gray-50 rounded-md">
                    <span class="text-sm font-medium text-gray-900">{{ $company->name }}</span>
                </div>
            </div>

            <!-- Personal Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">First Name *</label>
                    <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $mandator->first_name) }}" required
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('first_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                    <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $mandator->last_name) }}"
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
                    <input type="email" name="email" id="email" value="{{ old('email', $mandator->email) }}" required
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                    <input type="tel" name="phone" id="phone" value="{{ old('phone', $mandator->phone) }}"
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
                    <input type="text" name="position" id="position" value="{{ old('position', $mandator->position) }}"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('position')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="department" class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                    <input type="text" name="department" id="department" value="{{ old('department', $mandator->department) }}"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('department')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Logo Upload -->
            <div class="mb-6">
                <label for="logo" class="block text-sm font-medium text-gray-700 mb-2">Profile Picture</label>
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <img src="{{ $mandator->logo_url }}" alt="Current profile picture"
                             class="w-20 h-20 rounded-full object-cover">
                    </div>
                    <div class="flex-1">
                        <input type="file" name="logo" id="logo" accept="image/*"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <p class="mt-1 text-sm text-gray-500">Upload a new profile picture (JPEG, PNG, JPG, GIF, SVG, max 2MB)</p>
                    </div>
                </div>
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
                               {{ old('email_notifications', $mandator->email_notifications) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="email_notifications" class="ml-2 block text-sm text-gray-900">
                            Email Notifications
                        </label>
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="sms_notifications" id="sms_notifications" value="1"
                               {{ old('sms_notifications', $mandator->sms_notifications) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="sms_notifications" class="ml-2 block text-sm text-gray-900">
                            SMS Notifications
                        </label>
                    </div>
                    <div>
                        <label for="preferred_contact_method" class="block text-sm font-medium text-gray-700 mb-1">Preferred Contact Method</label>
                        <select name="preferred_contact_method" id="preferred_contact_method"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="email" {{ old('preferred_contact_method', $mandator->preferred_contact_method) == 'email' ? 'selected' : '' }}>Email</option>
                            <option value="phone" {{ old('preferred_contact_method', $mandator->preferred_contact_method) == 'phone' ? 'selected' : '' }}>Phone</option>
                            <option value="sms" {{ old('preferred_contact_method', $mandator->preferred_contact_method) == 'sms' ? 'selected' : '' }}>SMS</option>
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
                            $selectedSubscriptions = old('disclosure_subscriptions', $mandator->disclosure_subscriptions ?? []);
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
                           {{ old('is_active', $mandator->is_active) ? 'checked' : '' }}
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
                          class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('notes', $mandator->notes) }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- GDPR Service Agreement -->
            <div class="mb-8 border-t pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">GDPR Service Agreement</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="service_agreement_number" class="block text-sm font-medium text-gray-700 mb-2">Service Agreement Number</label>
                        <input type="text" name="service_agreement_number" id="service_agreement_number"
                               value="{{ old('service_agreement_number', $mandator->service_agreement_number) }}"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('service_agreement_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="service_type" class="block text-sm font-medium text-gray-700 mb-2">Service Type</label>
                        <select name="service_type" id="service_type"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Service Type</option>
                            <option value="gdpr_compliance" {{ old('service_type', $mandator->service_type) == 'gdpr_compliance' ? 'selected' : '' }}>GDPR Compliance</option>
                            <option value="data_audit" {{ old('service_type', $mandator->service_type) == 'data_audit' ? 'selected' : '' }}>Data Audit</option>
                            <option value="dpo_services" {{ old('service_type', $mandator->service_type) == 'dpo_services' ? 'selected' : '' }}>DPO Services</option>
                            <option value="training" {{ old('service_type', $mandator->service_type) == 'training' ? 'selected' : '' }}>Training</option>
                            <option value="consulting" {{ old('service_type', $mandator->service_type) == 'consulting' ? 'selected' : '' }}>Consulting</option>
                        </select>
                        @error('service_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="service_start_date" class="block text-sm font-medium text-gray-700 mb-2">Service Start Date</label>
                        <input type="date" name="service_start_date" id="service_start_date"
                               value="{{ old('service_start_date', $mandator->service_start_date?->format('Y-m-d')) }}"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('service_start_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="service_end_date" class="block text-sm font-medium text-gray-700 mb-2">Service End Date</label>
                        <input type="date" name="service_end_date" id="service_end_date"
                               value="{{ old('service_end_date', $mandator->service_end_date?->format('Y-m-d')) }}"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('service_end_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="service_status" class="block text-sm font-medium text-gray-700 mb-2">Service Status</label>
                        <select name="service_status" id="service_status"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="active" {{ old('service_status', $mandator->service_status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="expired" {{ old('service_status', $mandator->service_status) == 'expired' ? 'selected' : '' }}>Expired</option>
                            <option value="terminated" {{ old('service_status', $mandator->service_status) == 'terminated' ? 'selected' : '' }}>Terminated</option>
                            <option value="pending_renewal" {{ old('service_status', $mandator->service_status) == 'pending_renewal' ? 'selected' : '' }}>Pending Renewal</option>
                        </select>
                        @error('service_status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- GDPR Compliance Tracking -->
            <div class="mb-8 border-t pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">GDPR Compliance Tracking</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="compliance_score" class="block text-sm font-medium text-gray-700 mb-2">Compliance Score (0-100)</label>
                        <input type="number" name="compliance_score" id="compliance_score" min="0" max="100"
                               value="{{ old('compliance_score', $mandator->compliance_score) }}"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('compliance_score')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="gdpr_maturity_level" class="block text-sm font-medium text-gray-700 mb-2">GDPR Maturity Level</label>
                        <select name="gdpr_maturity_level" id="gdpr_maturity_level"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Maturity Level</option>
                            <option value="beginner" {{ old('gdpr_maturity_level', $mandator->gdpr_maturity_level) == 'beginner' ? 'selected' : '' }}>Beginner</option>
                            <option value="intermediate" {{ old('gdpr_maturity_level', $mandator->gdpr_maturity_level) == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                            <option value="advanced" {{ old('gdpr_maturity_level', $mandator->gdpr_maturity_level) == 'advanced' ? 'selected' : '' }}>Advanced</option>
                            <option value="expert" {{ old('gdpr_maturity_level', $mandator->gdpr_maturity_level) == 'expert' ? 'selected' : '' }}>Expert</option>
                        </select>
                        @error('gdpr_maturity_level')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="risk_level" class="block text-sm font-medium text-gray-700 mb-2">Risk Level</label>
                        <select name="risk_level" id="risk_level"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="low" {{ old('risk_level', $mandator->risk_level) == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ old('risk_level', $mandator->risk_level) == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ old('risk_level', $mandator->risk_level) == 'high' ? 'selected' : '' }}>High</option>
                            <option value="very_high" {{ old('risk_level', $mandator->risk_level) == 'very_high' ? 'selected' : '' }}>Very High</option>
                        </select>
                        @error('risk_level')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="last_gdpr_audit_date" class="block text-sm font-medium text-gray-700 mb-2">Last GDPR Audit Date</label>
                        <input type="date" name="last_gdpr_audit_date" id="last_gdpr_audit_date"
                               value="{{ old('last_gdpr_audit_date', $mandator->last_gdpr_audit_date?->format('Y-m-d')) }}"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('last_gdpr_audit_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="next_gdpr_audit_date" class="block text-sm font-medium text-gray-700 mb-2">Next GDPR Audit Date</label>
                        <input type="date" name="next_gdpr_audit_date" id="next_gdpr_audit_date"
                               value="{{ old('next_gdpr_audit_date', $mandator->next_gdpr_audit_date?->format('Y-m-d')) }}"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('next_gdpr_audit_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- GDPR Services and Requirements -->
            <div class="mb-8 border-t pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">GDPR Services & Requirements</h3>
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="gdpr_requirements" class="block text-sm font-medium text-gray-700 mb-2">GDPR Requirements</label>
                        <textarea name="gdpr_requirements" id="gdpr_requirements" rows="3"
                                  class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Describe specific GDPR requirements for this client...">{{ old('gdpr_requirements', $mandator->gdpr_requirements) }}</textarea>
                        @error('gdpr_requirements')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">GDPR Services Provided</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @php
                                $selectedServices = old('gdpr_services_provided', $mandator->gdpr_services_provided ?? []);
                            @endphp
                            @foreach(['compliance_audit', 'data_mapping', 'privacy_policy_review', 'training', 'incident_response', 'dpo_services', 'consent_management', 'data_subject_rights'] as $service)
                                <div class="flex items-center">
                                    <input type="checkbox" name="gdpr_services_provided[]" id="service_{{ $service }}"
                                           value="{{ $service }}"
                                           {{ in_array($service, $selectedServices) ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="service_{{ $service }}" class="ml-2 block text-sm text-gray-900">
                                        {{ ucwords(str_replace('_', ' ', $service)) }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        @error('gdpr_services_provided')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Applicable Regulations</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @php
                                $selectedRegulations = old('applicable_regulations', $mandator->applicable_regulations ?? []);
                            @endphp
                            @foreach(['GDPR', 'CCPA', 'LGPD', 'PIPEDA', 'POPIA', 'PDPA'] as $regulation)
                                <div class="flex items-center">
                                    <input type="checkbox" name="applicable_regulations[]" id="regulation_{{ $regulation }}"
                                           value="{{ $regulation }}"
                                           {{ in_array($regulation, $selectedRegulations) ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="regulation_{{ $regulation }}" class="ml-2 block text-sm text-gray-900">
                                        {{ $regulation }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        @error('applicable_regulations')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- GDPR Training & Documentation -->
            <div class="mb-8 border-t pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">GDPR Training & Documentation</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="last_gdpr_training_date" class="block text-sm font-medium text-gray-700 mb-2">Last Training Date</label>
                        <input type="date" name="last_gdpr_training_date" id="last_gdpr_training_date"
                               value="{{ old('last_gdpr_training_date', $mandator->last_gdpr_training_date?->format('Y-m-d')) }}"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('last_gdpr_training_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="next_gdpr_training_date" class="block text-sm font-medium text-gray-700 mb-2">Next Training Date</label>
                        <input type="date" name="next_gdpr_training_date" id="next_gdpr_training_date"
                               value="{{ old('next_gdpr_training_date', $mandator->next_gdpr_training_date?->format('Y-m-d')) }}"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('next_gdpr_training_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="employees_trained_count" class="block text-sm font-medium text-gray-700 mb-2">Employees Trained</label>
                        <input type="number" name="employees_trained_count" id="employees_trained_count" min="0"
                               value="{{ old('employees_trained_count', $mandator->employees_trained_count) }}"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('employees_trained_count')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="gdpr_training_required" id="gdpr_training_required" value="1"
                               {{ old('gdpr_training_required', $mandator->gdpr_training_required) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="gdpr_training_required" class="ml-2 block text-sm text-gray-900">
                            GDPR Training Required
                        </label>
                    </div>
                </div>

                <!-- Documentation Status -->
                <div class="mt-6">
                    <h4 class="text-md font-medium text-gray-900 mb-3">Documentation Status</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-center">
                            <input type="checkbox" name="privacy_policy_updated" id="privacy_policy_updated" value="1"
                                   {{ old('privacy_policy_updated', $mandator->privacy_policy_updated) ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="privacy_policy_updated" class="ml-2 block text-sm text-gray-900">
                                Privacy Policy Updated
                            </label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="data_processing_register_maintained" id="data_processing_register_maintained" value="1"
                                   {{ old('data_processing_register_maintained', $mandator->data_processing_register_maintained) ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="data_processing_register_maintained" class="ml-2 block text-sm text-gray-900">
                                Data Processing Register Maintained
                            </label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="data_breach_procedures_established" id="data_breach_procedures_established" value="1"
                                   {{ old('data_breach_procedures_established', $mandator->data_breach_procedures_established) ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="data_breach_procedures_established" class="ml-2 block text-sm text-gray-900">
                                Data Breach Procedures Established
                            </label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="data_subject_rights_procedures_established" id="data_subject_rights_procedures_established" value="1"
                                   {{ old('data_subject_rights_procedures_established', $mandator->data_subject_rights_procedures_established) ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="data_subject_rights_procedures_established" class="ml-2 block text-sm text-gray-900">
                                Data Subject Rights Procedures Established
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- GDPR Reporting & Deadlines -->
            <div class="mb-8 border-t pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">GDPR Reporting & Deadlines</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="gdpr_reporting_frequency" class="block text-sm font-medium text-gray-700 mb-2">Reporting Frequency</label>
                        <select name="gdpr_reporting_frequency" id="gdpr_reporting_frequency"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Frequency</option>
                            <option value="monthly" {{ old('gdpr_reporting_frequency', $mandator->gdpr_reporting_frequency) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                            <option value="quarterly" {{ old('gdpr_reporting_frequency', $mandator->gdpr_reporting_frequency) == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                            <option value="annually" {{ old('gdpr_reporting_frequency', $mandator->gdpr_reporting_frequency) == 'annually' ? 'selected' : '' }}>Annually</option>
                            <option value="on_demand" {{ old('gdpr_reporting_frequency', $mandator->gdpr_reporting_frequency) == 'on_demand' ? 'selected' : '' }}>On Demand</option>
                        </select>
                        @error('gdpr_reporting_frequency')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="gdpr_reporting_format" class="block text-sm font-medium text-gray-700 mb-2">Reporting Format</label>
                        <select name="gdpr_reporting_format" id="gdpr_reporting_format"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="pdf" {{ old('gdpr_reporting_format', $mandator->gdpr_reporting_format) == 'pdf' ? 'selected' : '' }}>PDF</option>
                            <option value="excel" {{ old('gdpr_reporting_format', $mandator->gdpr_reporting_format) == 'excel' ? 'selected' : '' }}>Excel</option>
                            <option value="web_dashboard" {{ old('gdpr_reporting_format', $mandator->gdpr_reporting_format) == 'web_dashboard' ? 'selected' : '' }}>Web Dashboard</option>
                            <option value="email" {{ old('gdpr_reporting_format', $mandator->gdpr_reporting_format) == 'email' ? 'selected' : '' }}>Email</option>
                        </select>
                        @error('gdpr_reporting_format')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="next_review_date" class="block text-sm font-medium text-gray-700 mb-2">Next Review Date</label>
                        <input type="date" name="next_review_date" id="next_review_date"
                               value="{{ old('next_review_date', $mandator->next_review_date?->format('Y-m-d')) }}"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('next_review_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6">
                    <label for="gdpr_notes" class="block text-sm font-medium text-gray-700 mb-2">GDPR Notes</label>
                    <textarea name="gdpr_notes" id="gdpr_notes" rows="3"
                              class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Additional GDPR compliance notes...">{{ old('gdpr_notes', $mandator->gdpr_notes) }}</textarea>
                    @error('gdpr_notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('mandators.index') }}"
                   class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-md">
                    Cancel
                </a>
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                    Update Mandator
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
