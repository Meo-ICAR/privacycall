@extends('layouts.app')

@section('title', 'Audit Request Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Audit Request Details</h1>
        <div class="flex space-x-2">
            <a href="{{ route('audit-requests.edit', $auditRequest) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Edit
            </a>
            <a href="{{ route('audit-requests.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                Back to Audits
            </a>
        </div>
    </div>

    <!-- Status and Priority Badges -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <div class="flex flex-wrap items-center justify-between">
            <div class="flex items-center space-x-4">
                @php
                    $statusColors = [
                        'pending' => 'bg-yellow-100 text-yellow-800',
                        'in_progress' => 'bg-blue-100 text-blue-800',
                        'completed' => 'bg-green-100 text-green-800',
                        'cancelled' => 'bg-red-100 text-red-800'
                    ];

                    $priorityColors = [
                        'low' => 'bg-gray-100 text-gray-800',
                        'normal' => 'bg-blue-100 text-blue-800',
                        'high' => 'bg-orange-100 text-orange-800',
                        'urgent' => 'bg-red-100 text-red-800'
                    ];
                @endphp
                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $statusColors[$auditRequest->status] ?? 'bg-gray-100 text-gray-800' }}">
                    {{ ucfirst(str_replace('_', ' ', $auditRequest->status)) }}
                </span>
                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $priorityColors[$auditRequest->priority] ?? 'bg-gray-100 text-gray-800' }}">
                    {{ ucfirst($auditRequest->priority) }} Priority
                </span>
                @if($auditRequest->risk_level)
                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                        {{ $auditRequest->risk_level === 'high' || $auditRequest->risk_level === 'critical' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                        {{ ucfirst($auditRequest->risk_level) }} Risk
                    </span>
                @endif
            </div>
            <div class="text-sm text-gray-500">
                Created {{ $auditRequest->created_at->diffForHumans() }}
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Basic Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Supplier</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $auditRequest->supplier->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Audit Type</label>
                        <p class="mt-1 text-sm text-gray-900">{{ ucfirst($auditRequest->audit_type) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Audit Scope</label>
                        <p class="mt-1 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $auditRequest->audit_scope)) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Subject</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $auditRequest->subject }}</p>
                    </div>
                </div>
            </div>

            <!-- Message -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Message</h2>
                <div class="prose max-w-none">
                    <p class="text-gray-700 whitespace-pre-wrap">{{ $auditRequest->message }}</p>
                </div>
            </div>

            <!-- Requested Documents -->
            @if($auditRequest->requested_documents && count($auditRequest->requested_documents) > 0)
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Requested Documents</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    @foreach($auditRequest->requested_documents as $document)
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-gray-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm text-gray-700">{{ ucwords(str_replace('_', ' ', $document)) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Audit Findings -->
            @if($auditRequest->audit_findings && count($auditRequest->audit_findings) > 0)
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Audit Findings</h2>
                <div class="space-y-3">
                    @foreach($auditRequest->audit_findings as $finding)
                        <div class="border-l-4 border-red-500 pl-4">
                            <p class="text-sm text-gray-700">{{ $finding }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Corrective Actions -->
            @if($auditRequest->corrective_actions && count($auditRequest->corrective_actions) > 0)
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Corrective Actions</h2>
                <div class="space-y-3">
                    @foreach($auditRequest->corrective_actions as $action)
                        <div class="border-l-4 border-green-500 pl-4">
                            <p class="text-sm text-gray-700">{{ $action }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Notes -->
            @if($auditRequest->notes)
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Notes</h2>
                <div class="prose max-w-none">
                    <p class="text-gray-700 whitespace-pre-wrap">{{ $auditRequest->notes }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Scheduling Information -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Scheduling</h2>
                <div class="space-y-3">
                    @if($auditRequest->requested_deadline)
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Requested Deadline</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $auditRequest->requested_deadline->format('M d, Y') }}</p>
                        @if($auditRequest->isOverdue())
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 mt-1">
                                Overdue
                            </span>
                        @endif
                    </div>
                    @endif

                    @if($auditRequest->scheduled_date)
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Scheduled Date</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $auditRequest->scheduled_date->format('M d, Y') }}</p>
                    </div>
                    @endif

                    @if($auditRequest->scheduled_time)
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Scheduled Time</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $auditRequest->scheduled_time->format('H:i') }}</p>
                    </div>
                    @endif

                    @if($auditRequest->meeting_type)
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Meeting Type</label>
                        <p class="mt-1 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $auditRequest->meeting_type)) }}</p>
                    </div>
                    @endif

                    @if($auditRequest->meeting_location)
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Meeting Location</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $auditRequest->meeting_location }}</p>
                    </div>
                    @endif

                    @if($auditRequest->meeting_link)
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Meeting Link</label>
                        <a href="{{ $auditRequest->meeting_link }}" target="_blank" class="mt-1 text-sm text-blue-600 hover:text-blue-800">
                            Join Meeting
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Audit Configuration -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Audit Configuration</h2>
                <div class="space-y-3">
                    @if($auditRequest->auditor)
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Assigned Auditor</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $auditRequest->auditor->name }}</p>
                        <p class="text-xs text-gray-500">{{ $auditRequest->auditor->email }}</p>
                    </div>
                    @endif

                    @if($auditRequest->audit_frequency)
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Audit Frequency</label>
                        <p class="mt-1 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $auditRequest->audit_frequency)) }}</p>
                    </div>
                    @endif

                    @if($auditRequest->next_audit_date)
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Next Audit Date</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $auditRequest->next_audit_date->format('M d, Y') }}</p>
                    </div>
                    @endif

                    @if($auditRequest->audit_cost)
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Audit Cost</label>
                        <p class="mt-1 text-sm text-gray-900">€{{ number_format($auditRequest->audit_cost, 2) }}</p>
                    </div>
                    @endif

                    @if($auditRequest->audit_duration_hours)
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Duration</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $auditRequest->audit_duration_hours }} hours</p>
                    </div>
                    @endif

                    @if($auditRequest->compliance_score)
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Compliance Score</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $auditRequest->compliance_score }}%</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Supplier Response -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Supplier Response</h2>
                <div class="space-y-3">
                    @if($auditRequest->supplier_response_deadline)
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Response Deadline</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $auditRequest->supplier_response_deadline->format('M d, Y') }}</p>
                    </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Response Received</label>
                        <p class="mt-1 text-sm text-gray-900">
                            @if($auditRequest->supplier_response_received)
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    Yes
                                </span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    No
                                </span>
                            @endif
                        </p>
                    </div>

                    @if($auditRequest->audit_report_url)
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Audit Report</label>
                        <a href="{{ $auditRequest->audit_report_url }}" target="_blank" class="mt-1 text-sm text-blue-600 hover:text-blue-800">
                            View Report
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Certification -->
            @if($auditRequest->certification_status || $auditRequest->certification_expiry_date)
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Certification</h2>
                <div class="space-y-3">
                    @if($auditRequest->certification_status)
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Status</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $auditRequest->certification_status }}</p>
                    </div>
                    @endif

                    @if($auditRequest->certification_expiry_date)
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Expiry Date</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $auditRequest->certification_expiry_date->format('M d, Y') }}</p>
                        @if($auditRequest->certification_expiry_date->isPast())
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 mt-1">
                                Expired
                            </span>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mt-8 bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Actions</h2>
        <div class="flex flex-wrap gap-2">
            @if($auditRequest->status === 'pending')
                <form action="{{ route('audit-requests.mark-in-progress', $auditRequest) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Mark In Progress
                    </button>
                </form>
            @endif

            @if($auditRequest->status === 'in_progress')
                <form action="{{ route('audit-requests.mark-completed', $auditRequest) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        Mark Completed
                    </button>
                </form>
            @endif

            <a href="{{ route('audit-requests.send-email', $auditRequest) }}" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                Send Email
            </a>

            <form action="{{ route('audit-requests.destroy', $auditRequest) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this audit request?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                    Delete
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
