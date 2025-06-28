@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Edit Compliance Request</h1>
                <p class="text-gray-600 mt-2">Update the incoming compliance request from a mandator</p>
            </div>
            <a href="{{ route('compliance-requests.show', $complianceRequest) }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Details
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-lg">
            <form action="{{ route('compliance-requests.update', $complianceRequest) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Mandator Selection -->
                    <div class="md:col-span-2">
                        <label for="mandator_id" class="block text-sm font-medium text-gray-700 mb-2">Mandator *</label>
                        <select name="mandator_id" id="mandator_id" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select mandator</option>
                            @foreach($mandators as $mandator)
                                <option value="{{ $mandator->id }}" {{ old('mandator_id', $complianceRequest->mandator_id) == $mandator->id ? 'selected' : '' }}>
                                    {{ $mandator->first_name }} {{ $mandator->last_name }} ({{ $mandator->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('mandator_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Request Type -->
                    <div>
                        <label for="request_type" class="block text-sm font-medium text-gray-700 mb-2">Request Type *</label>
                        <select name="request_type" id="request_type" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select request type</option>
                            <option value="compliance" {{ old('request_type', $complianceRequest->request_type) == 'compliance' ? 'selected' : '' }}>Compliance</option>
                            <option value="security" {{ old('request_type', $complianceRequest->request_type) == 'security' ? 'selected' : '' }}>Security</option>
                            <option value="gdpr" {{ old('request_type', $complianceRequest->request_type) == 'gdpr' ? 'selected' : '' }}>GDPR</option>
                            <option value="financial" {{ old('request_type', $complianceRequest->request_type) == 'financial' ? 'selected' : '' }}>Financial</option>
                            <option value="operational" {{ old('request_type', $complianceRequest->request_type) == 'operational' ? 'selected' : '' }}>Operational</option>
                            <option value="data_processing" {{ old('request_type', $complianceRequest->request_type) == 'data_processing' ? 'selected' : '' }}>Data Processing</option>
                        </select>
                        @error('request_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Request Scope -->
                    <div>
                        <label for="request_scope" class="block text-sm font-medium text-gray-700 mb-2">Request Scope *</label>
                        <select name="request_scope" id="request_scope" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select scope</option>
                            <option value="full" {{ old('request_scope', $complianceRequest->request_scope) == 'full' ? 'selected' : '' }}>Full Audit</option>
                            <option value="partial" {{ old('request_scope', $complianceRequest->request_scope) == 'partial' ? 'selected' : '' }}>Partial Audit</option>
                            <option value="specific_area" {{ old('request_scope', $complianceRequest->request_scope) == 'specific_area' ? 'selected' : '' }}>Specific Area</option>
                            <option value="document_only" {{ old('request_scope', $complianceRequest->request_scope) == 'document_only' ? 'selected' : '' }}>Document Only</option>
                        </select>
                        @error('request_scope')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Priority -->
                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">Priority *</label>
                        <select name="priority" id="priority" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select priority</option>
                            <option value="low" {{ old('priority', $complianceRequest->priority) == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="normal" {{ old('priority', $complianceRequest->priority) == 'normal' ? 'selected' : '' }}>Normal</option>
                            <option value="high" {{ old('priority', $complianceRequest->priority) == 'high' ? 'selected' : '' }}>High</option>
                            <option value="urgent" {{ old('priority', $complianceRequest->priority) == 'urgent' ? 'selected' : '' }}>Urgent</option>
                        </select>
                        @error('priority')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Assigned To -->
                    <div>
                        <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-2">Assign To</label>
                        <select name="assigned_to" id="assigned_to" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Unassigned</option>
                            @foreach($assignedUsers as $user)
                                <option value="{{ $user->id }}" {{ old('assigned_to', $complianceRequest->assigned_to) == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('assigned_to')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Subject -->
                    <div class="md:col-span-2">
                        <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">Subject *</label>
                        <input type="text" name="subject" id="subject" value="{{ old('subject', $complianceRequest->subject) }}" required
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Enter request subject">
                        @error('subject')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Message -->
                    <div class="md:col-span-2">
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Message *</label>
                        <textarea name="message" id="message" rows="6" required
                                  class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Enter the mandator's request message">{{ old('message', $complianceRequest->message) }}</textarea>
                        @error('message')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Requested Documents -->
                    <div class="md:col-span-2">
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
                                    'training_records',
                                    'financial_statements',
                                    'operational_procedures',
                                    'risk_assessments'
                                ];
                                $selectedDocs = old('requested_documents', $complianceRequest->requested_documents ?? []);
                            @endphp
                            @foreach($documentTypes as $docType)
                                <label class="flex items-center">
                                    <input type="checkbox" name="requested_documents[]" value="{{ $docType }}"
                                           {{ in_array($docType, $selectedDocs) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">{{ ucwords(str_replace('_', ' ', $docType)) }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('requested_documents')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Requested Deadline -->
                    <div>
                        <label for="requested_deadline" class="block text-sm font-medium text-gray-700 mb-2">Requested Deadline</label>
                        <input type="date" name="requested_deadline" id="requested_deadline" value="{{ old('requested_deadline', $complianceRequest->requested_deadline ? $complianceRequest->requested_deadline->format('Y-m-d') : '') }}"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('requested_deadline')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Scheduled Date -->
                    <div>
                        <label for="scheduled_date" class="block text-sm font-medium text-gray-700 mb-2">Scheduled Date</label>
                        <input type="date" name="scheduled_date" id="scheduled_date" value="{{ old('scheduled_date', $complianceRequest->scheduled_date ? $complianceRequest->scheduled_date->format('Y-m-d') : '') }}"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('scheduled_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Scheduled Time -->
                    <div>
                        <label for="scheduled_time" class="block text-sm font-medium text-gray-700 mb-2">Scheduled Time</label>
                        <input type="time" name="scheduled_time" id="scheduled_time" value="{{ old('scheduled_time', $complianceRequest->scheduled_time ? $complianceRequest->scheduled_time->format('H:i') : '') }}"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('scheduled_time')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Meeting Type -->
                    <div>
                        <label for="meeting_type" class="block text-sm font-medium text-gray-700 mb-2">Meeting Type</label>
                        <select name="meeting_type" id="meeting_type" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">No meeting</option>
                            <option value="call" {{ old('meeting_type', $complianceRequest->meeting_type) == 'call' ? 'selected' : '' }}>Phone Call</option>
                            <option value="visit" {{ old('meeting_type', $complianceRequest->meeting_type) == 'visit' ? 'selected' : '' }}>On-site Visit</option>
                            <option value="video_conference" {{ old('meeting_type', $complianceRequest->meeting_type) == 'video_conference' ? 'selected' : '' }}>Video Conference</option>
                            <option value="document_review" {{ old('meeting_type', $complianceRequest->meeting_type) == 'document_review' ? 'selected' : '' }}>Document Review</option>
                        </select>
                        @error('meeting_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Meeting Link -->
                    <div>
                        <label for="meeting_link" class="block text-sm font-medium text-gray-700 mb-2">Meeting Link</label>
                        <input type="url" name="meeting_link" id="meeting_link" value="{{ old('meeting_link', $complianceRequest->meeting_link) }}"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                               placeholder="https://meet.google.com/...">
                        @error('meeting_link')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Meeting Location -->
                    <div class="md:col-span-2">
                        <label for="meeting_location" class="block text-sm font-medium text-gray-700 mb-2">Meeting Location</label>
                        <input type="text" name="meeting_location" id="meeting_location" value="{{ old('meeting_location', $complianceRequest->meeting_location) }}"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Enter meeting location">
                        @error('meeting_location')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div class="md:col-span-2">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <textarea name="notes" id="notes" rows="3"
                                  class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Additional notes or comments">{{ old('notes', $complianceRequest->notes) }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                    <a href="{{ route('compliance-requests.show', $complianceRequest) }}"
                       class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                        Cancel
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-save mr-2"></i>Update Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
