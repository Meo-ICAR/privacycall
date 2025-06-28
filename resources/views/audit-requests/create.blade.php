@extends('layouts.app')

@section('title', 'Create Audit Request')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Create New Audit Request</h1>
        <a href="{{ route('audit-requests.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
            Back to Audits
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-6">
        <form action="{{ route('audit-requests.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Basic Information -->
            <div class="border-b border-gray-200 pb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Basic Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-2">Supplier *</label>
                        <select name="supplier_id" id="supplier_id" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select a supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }} - {{ $supplier->email }}
                                </option>
                            @endforeach
                        </select>
                        @error('supplier_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="audit_type" class="block text-sm font-medium text-gray-700 mb-2">Audit Type *</label>
                        <select name="audit_type" id="audit_type" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select audit type</option>
                            <option value="compliance" {{ old('audit_type') == 'compliance' ? 'selected' : '' }}>Compliance</option>
                            <option value="security" {{ old('audit_type') == 'security' ? 'selected' : '' }}>Security</option>
                            <option value="gdpr" {{ old('audit_type') == 'gdpr' ? 'selected' : '' }}>GDPR</option>
                            <option value="financial" {{ old('audit_type') == 'financial' ? 'selected' : '' }}>Financial</option>
                            <option value="operational" {{ old('audit_type') == 'operational' ? 'selected' : '' }}>Operational</option>
                        </select>
                        @error('audit_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="audit_scope" class="block text-sm font-medium text-gray-700 mb-2">Audit Scope *</label>
                        <select name="audit_scope" id="audit_scope" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select audit scope</option>
                            <option value="full" {{ old('audit_scope') == 'full' ? 'selected' : '' }}>Full Audit</option>
                            <option value="partial" {{ old('audit_scope') == 'partial' ? 'selected' : '' }}>Partial Audit</option>
                            <option value="specific_area" {{ old('audit_scope') == 'specific_area' ? 'selected' : '' }}>Specific Area</option>
                        </select>
                        @error('audit_scope')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">Priority *</label>
                        <select name="priority" id="priority" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select priority</option>
                            <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="normal" {{ old('priority') == 'normal' ? 'selected' : '' }}>Normal</option>
                            <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                            <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                        </select>
                        @error('priority')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Audit Details -->
            <div class="border-b border-gray-200 pb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Audit Details</h2>
                <div class="space-y-4">
                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">Subject *</label>
                        <input type="text" name="subject" id="subject" value="{{ old('subject') }}" required
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Enter audit subject">
                        @error('subject')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Message *</label>
                        <textarea name="message" id="message" rows="4" required
                                  class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Enter detailed audit message">{{ old('message') }}</textarea>
                        @error('message')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="requested_documents" class="block text-sm font-medium text-gray-700 mb-2">Requested Documents</label>
                        <div class="space-y-2">
                            @php
                                $documentTypes = [
                                    'gdpr_compliance_documentation',
                                    'data_processing_agreements',
                                    'security_policies',
                                    'incident_response_plans',
                                    'data_breach_procedures',
                                    'privacy_notices',
                                    'consent_management_records',
                                    'data_retention_policies',
                                    'third_party_agreements',
                                    'audit_reports',
                                    'certification_documents',
                                    'training_records'
                                ];
                            @endphp
                            @foreach($documentTypes as $docType)
                                <label class="flex items-center">
                                    <input type="checkbox" name="requested_documents[]" value="{{ $docType }}"
                                           {{ in_array($docType, old('requested_documents', [])) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">{{ ucwords(str_replace('_', ' ', $docType)) }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('requested_documents')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Scheduling -->
            <div class="border-b border-gray-200 pb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Scheduling</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="requested_deadline" class="block text-sm font-medium text-gray-700 mb-2">Requested Deadline</label>
                        <input type="date" name="requested_deadline" id="requested_deadline" value="{{ old('requested_deadline') }}"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('requested_deadline')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="scheduled_date" class="block text-sm font-medium text-gray-700 mb-2">Scheduled Date</label>
                        <input type="date" name="scheduled_date" id="scheduled_date" value="{{ old('scheduled_date') }}"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('scheduled_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="scheduled_time" class="block text-sm font-medium text-gray-700 mb-2">Scheduled Time</label>
                        <input type="time" name="scheduled_time" id="scheduled_time" value="{{ old('scheduled_time') }}"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('scheduled_time')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="meeting_type" class="block text-sm font-medium text-gray-700 mb-2">Meeting Type</label>
                        <select name="meeting_type" id="meeting_type" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select meeting type</option>
                            <option value="call" {{ old('meeting_type') == 'call' ? 'selected' : '' }}>Phone Call</option>
                            <option value="visit" {{ old('meeting_type') == 'visit' ? 'selected' : '' }}>On-site Visit</option>
                            <option value="video_conference" {{ old('meeting_type') == 'video_conference' ? 'selected' : '' }}>Video Conference</option>
                        </select>
                        @error('meeting_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="meeting_link" class="block text-sm font-medium text-gray-700 mb-2">Meeting Link</label>
                        <input type="url" name="meeting_link" id="meeting_link" value="{{ old('meeting_link') }}"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                               placeholder="https://meet.google.com/...">
                        @error('meeting_link')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="meeting_location" class="block text-sm font-medium text-gray-700 mb-2">Meeting Location</label>
                        <input type="text" name="meeting_location" id="meeting_location" value="{{ old('meeting_location') }}"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Enter meeting location">
                        @error('meeting_location')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Audit Configuration -->
            <div class="border-b border-gray-200 pb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Audit Configuration</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="auditor_assigned" class="block text-sm font-medium text-gray-700 mb-2">Assigned Auditor</label>
                        <select name="auditor_assigned" id="auditor_assigned" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select an auditor</option>
                            @foreach($auditors as $auditor)
                                <option value="{{ $auditor->id }}" {{ old('auditor_assigned') == $auditor->id ? 'selected' : '' }}>
                                    {{ $auditor->name }} ({{ $auditor->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('auditor_assigned')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="audit_frequency" class="block text-sm font-medium text-gray-700 mb-2">Audit Frequency</label>
                        <select name="audit_frequency" id="audit_frequency" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select frequency</option>
                            <option value="monthly" {{ old('audit_frequency') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                            <option value="quarterly" {{ old('audit_frequency') == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                            <option value="semi_annual" {{ old('audit_frequency') == 'semi_annual' ? 'selected' : '' }}>Semi-Annual</option>
                            <option value="annual" {{ old('audit_frequency') == 'annual' ? 'selected' : '' }}>Annual</option>
                            <option value="biennial" {{ old('audit_frequency') == 'biennial' ? 'selected' : '' }}>Biennial</option>
                        </select>
                        @error('audit_frequency')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="next_audit_date" class="block text-sm font-medium text-gray-700 mb-2">Next Audit Date</label>
                        <input type="date" name="next_audit_date" id="next_audit_date" value="{{ old('next_audit_date') }}"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('next_audit_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="supplier_response_deadline" class="block text-sm font-medium text-gray-700 mb-2">Supplier Response Deadline</label>
                        <input type="date" name="supplier_response_deadline" id="supplier_response_deadline" value="{{ old('supplier_response_deadline') }}"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('supplier_response_deadline')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="audit_cost" class="block text-sm font-medium text-gray-700 mb-2">Audit Cost (€)</label>
                        <input type="number" name="audit_cost" id="audit_cost" value="{{ old('audit_cost') }}" step="0.01" min="0"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                               placeholder="0.00">
                        @error('audit_cost')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="audit_duration_hours" class="block text-sm font-medium text-gray-700 mb-2">Duration (Hours)</label>
                        <input type="number" name="audit_duration_hours" id="audit_duration_hours" value="{{ old('audit_duration_hours') }}" step="0.5" min="0"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                               placeholder="0.0">
                        @error('audit_duration_hours')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="border-b border-gray-200 pb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Additional Information</h2>
                <div class="space-y-4">
                    <div>
                        <label for="certification_status" class="block text-sm font-medium text-gray-700 mb-2">Certification Status</label>
                        <input type="text" name="certification_status" id="certification_status" value="{{ old('certification_status') }}"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                               placeholder="e.g., ISO 27001, GDPR Certified">
                        @error('certification_status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="certification_expiry_date" class="block text-sm font-medium text-gray-700 mb-2">Certification Expiry Date</label>
                        <input type="date" name="certification_expiry_date" id="certification_expiry_date" value="{{ old('certification_expiry_date') }}"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('certification_expiry_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <textarea name="notes" id="notes" rows="3"
                                  class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Additional notes or comments">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Email Options -->
            <div class="pb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Email Options</h2>
                <div class="flex items-center">
                    <input type="checkbox" name="send_email" id="send_email" value="1" {{ old('send_email') ? 'checked' : '' }}
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <label for="send_email" class="ml-2 text-sm text-gray-700">Send email notification to supplier</label>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('audit-requests.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                    Cancel
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Create Audit Request
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set minimum dates for date inputs
    const today = new Date().toISOString().split('T')[0];

    const dateInputs = ['requested_deadline', 'scheduled_date', 'next_audit_date', 'supplier_response_deadline', 'certification_expiry_date'];
    dateInputs.forEach(inputId => {
        const input = document.getElementById(inputId);
        if (input) {
            input.min = today;
        }
    });
});
</script>
@endsection
