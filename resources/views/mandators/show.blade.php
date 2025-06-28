@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('mandators.index') }}" class="text-blue-600 hover:text-blue-800 mr-4">
            <i class="fas fa-arrow-left"></i> Back to Mandators
        </a>
        <div class="flex justify-between items-start mt-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $mandator->full_name }}</h1>
                <p class="text-gray-600 mt-2">{{ $mandator->position }} at {{ $mandator->company->name }}</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('mandators.edit', $mandator) }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
                <form action="{{ route('mandators.destroy', $mandator) }}"
                      method="POST"
                      class="inline"
                      onsubmit="return confirm('Are you sure you want to delete this mandator?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md">
                        <i class="fas fa-trash mr-2"></i>Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Basic Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Full Name</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $mandator->full_name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Email</label>
                        <p class="mt-1 text-sm text-gray-900">
                            <a href="mailto:{{ $mandator->email }}" class="text-blue-600 hover:text-blue-800">
                                {{ $mandator->email }}
                            </a>
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Phone</label>
                        <p class="mt-1 text-sm text-gray-900">
                            @if($mandator->phone)
                                <a href="tel:{{ $mandator->phone }}" class="text-blue-600 hover:text-blue-800">
                                    {{ $mandator->phone }}
                                </a>
                            @else
                                <span class="text-gray-400">Not provided</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Position</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $mandator->position ?? 'Not specified' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Department</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $mandator->department ?? 'Not specified' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Status</label>
                        <p class="mt-1 text-sm text-gray-900">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $mandator->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $mandator->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Contact Preferences -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Contact Preferences</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Email Notifications</label>
                        <p class="mt-1 text-sm text-gray-900">
                            <span class="inline-flex items-center">
                                @if($mandator->email_notifications)
                                    <i class="fas fa-check text-green-600 mr-2"></i>Enabled
                                @else
                                    <i class="fas fa-times text-red-600 mr-2"></i>Disabled
                                @endif
                            </span>
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">SMS Notifications</label>
                        <p class="mt-1 text-sm text-gray-900">
                            <span class="inline-flex items-center">
                                @if($mandator->sms_notifications)
                                    <i class="fas fa-check text-green-600 mr-2"></i>Enabled
                                @else
                                    <i class="fas fa-times text-red-600 mr-2"></i>Disabled
                                @endif
                            </span>
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Preferred Contact Method</label>
                        <p class="mt-1 text-sm text-gray-900 capitalize">{{ $mandator->preferred_contact_method }}</p>
                    </div>
                </div>
            </div>

            <!-- Disclosure Subscriptions -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Disclosure Subscriptions</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($mandator->disclosure_subscriptions as $subscription)
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-600 mr-2"></i>
                            <span class="text-sm text-gray-900">{{ $subscription }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Notes -->
            @if($mandator->notes)
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Notes</h2>
                    <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $mandator->notes }}</p>
                </div>
            @endif

            <!-- GDPR Service Agreement -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">GDPR Service Agreement</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Service Agreement Number</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $mandator->service_agreement_number ?? 'Not specified' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Service Type</label>
                        <p class="mt-1 text-sm text-gray-900">
                            @if($mandator->service_type)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ ucwords(str_replace('_', ' ', $mandator->service_type)) }}
                                </span>
                            @else
                                <span class="text-gray-400">Not specified</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Service Start Date</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $mandator->service_start_date?->format('M d, Y') ?? 'Not specified' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Service End Date</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $mandator->service_end_date?->format('M d, Y') ?? 'Not specified' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Service Status</label>
                        <p class="mt-1 text-sm text-gray-900">
                            @if($mandator->service_status)
                                @php
                                    $statusColors = [
                                        'active' => 'bg-green-100 text-green-800',
                                        'expired' => 'bg-red-100 text-red-800',
                                        'terminated' => 'bg-gray-100 text-gray-800',
                                        'pending_renewal' => 'bg-yellow-100 text-yellow-800'
                                    ];
                                    $color = $statusColors[$mandator->service_status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $color }}">
                                    {{ ucwords(str_replace('_', ' ', $mandator->service_status)) }}
                                </span>
                            @else
                                <span class="text-gray-400">Not specified</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- GDPR Compliance Tracking -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">GDPR Compliance Tracking</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Compliance Score</label>
                        <p class="mt-1 text-sm text-gray-900">
                            @if($mandator->compliance_score !== null)
                                <div class="flex items-center">
                                    <div class="w-16 bg-gray-200 rounded-full h-2 mr-3">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $mandator->compliance_score }}%"></div>
                                    </div>
                                    <span class="text-sm font-medium">{{ $mandator->compliance_score }}%</span>
                                </div>
                            @else
                                <span class="text-gray-400">Not assessed</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">GDPR Maturity Level</label>
                        <p class="mt-1 text-sm text-gray-900">
                            @if($mandator->gdpr_maturity_level)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    {{ ucwords($mandator->gdpr_maturity_level) }}
                                </span>
                            @else
                                <span class="text-gray-400">Not specified</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Risk Level</label>
                        <p class="mt-1 text-sm text-gray-900">
                            @if($mandator->risk_level)
                                @php
                                    $riskColors = [
                                        'low' => 'bg-green-100 text-green-800',
                                        'medium' => 'bg-yellow-100 text-yellow-800',
                                        'high' => 'bg-orange-100 text-orange-800',
                                        'very_high' => 'bg-red-100 text-red-800'
                                    ];
                                    $color = $riskColors[$mandator->risk_level] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $color }}">
                                    {{ ucwords(str_replace('_', ' ', $mandator->risk_level)) }}
                                </span>
                            @else
                                <span class="text-gray-400">Not specified</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Last GDPR Audit</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $mandator->last_gdpr_audit_date?->format('M d, Y') ?? 'Not performed' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Next GDPR Audit</label>
                        <p class="mt-1 text-sm text-gray-900">
                            @if($mandator->next_gdpr_audit_date)
                                @if($mandator->next_gdpr_audit_date->isPast())
                                    <span class="text-red-600 font-medium">Overdue: {{ $mandator->next_gdpr_audit_date->format('M d, Y') }}</span>
                                @elseif($mandator->next_gdpr_audit_date->diffInDays(now()) <= 30)
                                    <span class="text-yellow-600 font-medium">Due Soon: {{ $mandator->next_gdpr_audit_date->format('M d, Y') }}</span>
                                @else
                                    {{ $mandator->next_gdpr_audit_date->format('M d, Y') }}
                                @endif
                            @else
                                <span class="text-gray-400">Not scheduled</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- GDPR Services & Requirements -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">GDPR Services & Requirements</h2>

                @if($mandator->gdpr_requirements)
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-500 mb-2">GDPR Requirements</label>
                        <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $mandator->gdpr_requirements }}</p>
                    </div>
                @endif

                @if($mandator->gdpr_services_provided && count($mandator->gdpr_services_provided) > 0)
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-500 mb-2">GDPR Services Provided</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            @foreach($mandator->gdpr_services_provided as $service)
                                <div class="flex items-center">
                                    <i class="fas fa-check-circle text-green-600 mr-2"></i>
                                    <span class="text-sm text-gray-900">{{ ucwords(str_replace('_', ' ', $service)) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($mandator->applicable_regulations && count($mandator->applicable_regulations) > 0)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-2">Applicable Regulations</label>
                        <div class="flex flex-wrap gap-2">
                            @foreach($mandator->applicable_regulations as $regulation)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                    {{ $regulation }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- GDPR Training & Documentation -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">GDPR Training & Documentation</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Last Training Date</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $mandator->last_gdpr_training_date?->format('M d, Y') ?? 'Not performed' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Next Training Date</label>
                        <p class="mt-1 text-sm text-gray-900">
                            @if($mandator->next_gdpr_training_date)
                                @if($mandator->next_gdpr_training_date->isPast())
                                    <span class="text-red-600 font-medium">Overdue: {{ $mandator->next_gdpr_training_date->format('M d, Y') }}</span>
                                @elseif($mandator->next_gdpr_training_date->diffInDays(now()) <= 30)
                                    <span class="text-yellow-600 font-medium">Due Soon: {{ $mandator->next_gdpr_training_date->format('M d, Y') }}</span>
                                @else
                                    {{ $mandator->next_gdpr_training_date->format('M d, Y') }}
                                @endif
                            @else
                                <span class="text-gray-400">Not scheduled</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Employees Trained</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $mandator->employees_trained_count ?? 'Not specified' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Training Required</label>
                        <p class="mt-1 text-sm text-gray-900">
                            <span class="inline-flex items-center">
                                @if($mandator->gdpr_training_required)
                                    <i class="fas fa-check text-green-600 mr-2"></i>Yes
                                @else
                                    <i class="fas fa-times text-red-600 mr-2"></i>No
                                @endif
                            </span>
                        </p>
                    </div>
                </div>

                <!-- Documentation Status -->
                <div class="mt-6">
                    <h4 class="text-md font-medium text-gray-900 mb-3">Documentation Status</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-center">
                            @if($mandator->privacy_policy_updated)
                                <i class="fas fa-check-circle text-green-600 mr-2"></i>
                                <span class="text-sm text-gray-900">Privacy Policy Updated</span>
                            @else
                                <i class="fas fa-times-circle text-red-600 mr-2"></i>
                                <span class="text-sm text-gray-500">Privacy Policy Not Updated</span>
                            @endif
                        </div>
                        <div class="flex items-center">
                            @if($mandator->data_processing_register_maintained)
                                <i class="fas fa-check-circle text-green-600 mr-2"></i>
                                <span class="text-sm text-gray-900">Data Processing Register Maintained</span>
                            @else
                                <i class="fas fa-times-circle text-red-600 mr-2"></i>
                                <span class="text-sm text-gray-500">Data Processing Register Not Maintained</span>
                            @endif
                        </div>
                        <div class="flex items-center">
                            @if($mandator->data_breach_procedures_established)
                                <i class="fas fa-check-circle text-green-600 mr-2"></i>
                                <span class="text-sm text-gray-900">Data Breach Procedures Established</span>
                            @else
                                <i class="fas fa-times-circle text-red-600 mr-2"></i>
                                <span class="text-sm text-gray-500">Data Breach Procedures Not Established</span>
                            @endif
                        </div>
                        <div class="flex items-center">
                            @if($mandator->data_subject_rights_procedures_established)
                                <i class="fas fa-check-circle text-green-600 mr-2"></i>
                                <span class="text-sm text-gray-900">Data Subject Rights Procedures Established</span>
                            @else
                                <i class="fas fa-times-circle text-red-600 mr-2"></i>
                                <span class="text-sm text-gray-500">Data Subject Rights Procedures Not Established</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- GDPR Reporting & Deadlines -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">GDPR Reporting & Deadlines</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Reporting Frequency</label>
                        <p class="mt-1 text-sm text-gray-900">
                            @if($mandator->gdpr_reporting_frequency)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ ucwords(str_replace('_', ' ', $mandator->gdpr_reporting_frequency)) }}
                                </span>
                            @else
                                <span class="text-gray-400">Not specified</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Reporting Format</label>
                        <p class="mt-1 text-sm text-gray-900">
                            @if($mandator->gdpr_reporting_format)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ strtoupper($mandator->gdpr_reporting_format) }}
                                </span>
                            @else
                                <span class="text-gray-400">Not specified</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Next Review Date</label>
                        <p class="mt-1 text-sm text-gray-900">
                            @if($mandator->next_review_date)
                                @if($mandator->next_review_date->isPast())
                                    <span class="text-red-600 font-medium">Overdue: {{ $mandator->next_review_date->format('M d, Y') }}</span>
                                @elseif($mandator->next_review_date->diffInDays(now()) <= 30)
                                    <span class="text-yellow-600 font-medium">Due Soon: {{ $mandator->next_review_date->format('M d, Y') }}</span>
                                @else
                                    {{ $mandator->next_review_date->format('M d, Y') }}
                                @endif
                            @else
                                <span class="text-gray-400">Not scheduled</span>
                            @endif
                        </p>
                    </div>
                </div>

                @if($mandator->gdpr_notes)
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-500 mb-2">GDPR Notes</label>
                        <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $mandator->gdpr_notes }}</p>
                    </div>
                @endif
            </div>

            <!-- Related Mandators -->
            @if($mandator->clones && $mandator->clones->count() > 0)
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Related Mandators</h2>
                    <div class="space-y-3">
                        @foreach($mandator->clones as $clone)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $clone->full_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $clone->company->name }}</p>
                                </div>
                                <a href="{{ route('mandators.show', $clone) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                    View
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Profile Picture -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Profile Picture</h3>
                <div class="flex justify-center">
                    <img src="{{ $mandator->logo_url }}"
                         alt="{{ $mandator->full_name }}"
                         class="w-32 h-32 rounded-full object-cover">
                </div>
            </div>

            <!-- Company Information -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Company</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Company Name</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $mandator->company->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Company Email</label>
                        <p class="mt-1 text-sm text-gray-900">
                            <a href="mailto:{{ $mandator->company->email }}" class="text-blue-600 hover:text-blue-800">
                                {{ $mandator->company->email }}
                            </a>
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Company Phone</label>
                        <p class="mt-1 text-sm text-gray-900">
                            @if($mandator->company->phone)
                                <a href="tel:{{ $mandator->company->phone }}" class="text-blue-600 hover:text-blue-800">
                                    {{ $mandator->company->phone }}
                                </a>
                            @else
                                <span class="text-gray-400">Not provided</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- GDPR Summary -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">GDPR Summary</h3>
                <div class="space-y-4">
                    <!-- Service Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Service Status</label>
                        @if($mandator->service_status)
                            @php
                                $statusColors = [
                                    'active' => 'bg-green-100 text-green-800',
                                    'expired' => 'bg-red-100 text-red-800',
                                    'terminated' => 'bg-gray-100 text-gray-800',
                                    'pending_renewal' => 'bg-yellow-100 text-yellow-800'
                                ];
                                $color = $statusColors[$mandator->service_status] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $color }}">
                                {{ ucwords(str_replace('_', ' ', $mandator->service_status)) }}
                            </span>
                        @else
                            <span class="text-gray-400 text-sm">Not specified</span>
                        @endif
                    </div>

                    <!-- Compliance Score -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Compliance Score</label>
                        @if($mandator->compliance_score !== null)
                            <div class="flex items-center">
                                <div class="w-full bg-gray-200 rounded-full h-2 mr-3">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $mandator->compliance_score }}%"></div>
                                </div>
                                <span class="text-sm font-medium">{{ $mandator->compliance_score }}%</span>
                            </div>
                        @else
                            <span class="text-gray-400 text-sm">Not assessed</span>
                        @endif
                    </div>

                    <!-- Risk Level -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Risk Level</label>
                        @if($mandator->risk_level)
                            @php
                                $riskColors = [
                                    'low' => 'bg-green-100 text-green-800',
                                    'medium' => 'bg-yellow-100 text-yellow-800',
                                    'high' => 'bg-orange-100 text-orange-800',
                                    'very_high' => 'bg-red-100 text-red-800'
                                ];
                                $color = $riskColors[$mandator->risk_level] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $color }}">
                                {{ ucwords(str_replace('_', ' ', $mandator->risk_level)) }}
                            </span>
                        @else
                            <span class="text-gray-400 text-sm">Not specified</span>
                        @endif
                    </div>

                    <!-- Next Audit -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Next Audit</label>
                        @if($mandator->next_gdpr_audit_date)
                            @if($mandator->next_gdpr_audit_date->isPast())
                                <span class="text-red-600 text-sm font-medium">Overdue</span>
                            @elseif($mandator->next_gdpr_audit_date->diffInDays(now()) <= 30)
                                <span class="text-yellow-600 text-sm font-medium">Due Soon</span>
                            @else
                                <span class="text-gray-900 text-sm">{{ $mandator->next_gdpr_audit_date->format('M d, Y') }}</span>
                            @endif
                        @else
                            <span class="text-gray-400 text-sm">Not scheduled</span>
                        @endif
                    </div>

                    <!-- Next Training -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Next Training</label>
                        @if($mandator->next_gdpr_training_date)
                            @if($mandator->next_gdpr_training_date->isPast())
                                <span class="text-red-600 text-sm font-medium">Overdue</span>
                            @elseif($mandator->next_gdpr_training_date->diffInDays(now()) <= 30)
                                <span class="text-yellow-600 text-sm font-medium">Due Soon</span>
                            @else
                                <span class="text-gray-900 text-sm">{{ $mandator->next_gdpr_training_date->format('M d, Y') }}</span>
                            @endif
                        @else
                            <span class="text-gray-400 text-sm">Not scheduled</span>
                        @endif
                    </div>

                    <!-- Services Count -->
                    @if($mandator->gdpr_services_provided && count($mandator->gdpr_services_provided) > 0)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Services Provided</label>
                            <span class="text-sm text-gray-900">{{ count($mandator->gdpr_services_provided) }} services</span>
                        </div>
                    @endif

                    <!-- Regulations Count -->
                    @if($mandator->applicable_regulations && count($mandator->applicable_regulations) > 0)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Regulations</label>
                            <div class="flex flex-wrap gap-1">
                                @foreach(array_slice($mandator->applicable_regulations, 0, 3) as $regulation)
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                                        {{ $regulation }}
                                    </span>
                                @endforeach
                                @if(count($mandator->applicable_regulations) > 3)
                                    <span class="text-xs text-gray-500">+{{ count($mandator->applicable_regulations) - 3 }} more</span>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <a href="mailto:{{ $mandator->email }}" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <i class="fas fa-envelope mr-2"></i>Send Email
                    </a>
                    @if($mandator->phone)
                        <a href="tel:{{ $mandator->phone }}" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                            <i class="fas fa-phone mr-2"></i>Call
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
