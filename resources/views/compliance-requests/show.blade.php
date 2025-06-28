@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $complianceRequest->subject }}</h1>
                <p class="text-gray-600 mt-2">Compliance Request from {{ $complianceRequest->mandator->first_name }} {{ $complianceRequest->mandator->last_name }}</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('compliance-requests.edit', $complianceRequest) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg flex items-center">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
                <a href="{{ route('compliance-requests.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
            </div>
        </div>

        <!-- Status and Priority Badges -->
        <div class="flex flex-wrap gap-4 mb-6">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $complianceRequest->getStatusBadgeClass() }}">
                {{ ucfirst(str_replace('_', ' ', $complianceRequest->status)) }}
            </span>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $complianceRequest->getPriorityBadgeClass() }}">
                {{ ucfirst($complianceRequest->priority) }} Priority
            </span>
            @if($complianceRequest->risk_level)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $complianceRequest->getRiskLevelBadgeClass() }}">
                    {{ ucfirst($complianceRequest->risk_level) }} Risk
                </span>
            @endif
            @if($complianceRequest->compliance_score)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                    {{ $complianceRequest->compliance_score }}% Compliance
                </span>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Request Details -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Request Details</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Request Type</label>
                            <p class="mt-1 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $complianceRequest->request_type)) }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Request Scope</label>
                            <p class="mt-1 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $complianceRequest->request_scope)) }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Created</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $complianceRequest->created_at->format('M d, Y H:i') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Last Updated</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $complianceRequest->updated_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-500 mb-2">Message</label>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $complianceRequest->message }}</p>
                        </div>
                    </div>

                    @if($complianceRequest->notes)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-2">Notes</label>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $complianceRequest->notes }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Requested Documents -->
                @if(!empty($complianceRequest->requested_documents))
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Requested Documents</h2>
                        <div class="space-y-2">
                            @foreach($complianceRequest->requested_documents as $document)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <span class="text-sm text-gray-900">{{ ucwords(str_replace('_', ' ', $document)) }}</span>
                                    @if($complianceRequest->documents_uploaded && in_array($document, $complianceRequest->provided_documents ?? []))
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check mr-1"></i>Provided
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-clock mr-1"></i>Pending
                                        </span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Response Section -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Response</h2>

                    @if($complianceRequest->response_sent)
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-500 mb-2">Response Sent</label>
                            <div class="bg-green-50 rounded-lg p-4">
                                <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $complianceRequest->response_message }}</p>
                                <p class="text-xs text-gray-500 mt-2">Sent on {{ $complianceRequest->response_sent_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                    @else
                        <form action="{{ route('compliance-requests.send-response', $complianceRequest) }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label for="response_message" class="block text-sm font-medium text-gray-700 mb-2">Response Message</label>
                                <textarea name="response_message" id="response_message" rows="4" required
                                          class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                          placeholder="Enter your response to the mandator"></textarea>
                            </div>
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-paper-plane mr-2"></i>Send Response
                            </button>
                        </form>
                    @endif
                </div>

                <!-- Document Upload Section -->
                @if(!$complianceRequest->documents_uploaded)
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Upload Documents</h2>
                        <form action="{{ route('compliance-requests.upload-documents', $complianceRequest) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <div>
                                <label for="documents" class="block text-sm font-medium text-gray-700 mb-2">Select Documents</label>
                                <input type="file" name="documents[]" id="documents" multiple
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                       accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.jpg,.jpeg,.png">
                                <p class="text-xs text-gray-500 mt-1">Accepted formats: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT, JPG, JPEG, PNG (max 10MB each)</p>
                            </div>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-upload mr-2"></i>Upload Documents
                            </button>
                        </form>
                    </div>
                @endif

                <!-- Provided Documents -->
                @if($complianceRequest->documents_uploaded && !empty($complianceRequest->provided_documents))
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Provided Documents</h2>
                        <div class="space-y-2">
                            @foreach($complianceRequest->provided_documents as $document)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <span class="text-sm text-gray-900">{{ $document['name'] }}</span>
                                        <span class="text-xs text-gray-500 ml-2">{{ number_format($document['size'] / 1024, 1) }} KB</span>
                                    </div>
                                    <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($document['uploaded_at'])->format('M d, Y H:i') }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Compliance Findings -->
                @if(!empty($complianceRequest->compliance_findings))
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Compliance Findings</h2>
                        <div class="space-y-4">
                            @foreach($complianceRequest->compliance_findings as $finding)
                                <div class="border-l-4 border-{{ $finding['severity'] === 'critical' ? 'red' : ($finding['severity'] === 'major' ? 'orange' : 'yellow') }}-500 pl-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-medium text-gray-900">{{ $finding['category'] }}</span>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                            {{ $finding['severity'] === 'critical' ? 'bg-red-100 text-red-800' :
                                               ($finding['severity'] === 'major' ? 'bg-orange-100 text-orange-800' : 'bg-yellow-100 text-yellow-800') }}">
                                            {{ ucfirst($finding['severity']) }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-700 mb-2">{{ $finding['description'] }}</p>
                                    @if($finding['recommendation'])
                                        <p class="text-sm text-gray-600"><strong>Recommendation:</strong> {{ $finding['recommendation'] }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Mandator Information -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Mandator Information</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Name</label>
                            <p class="text-sm text-gray-900">{{ $complianceRequest->mandator->first_name }} {{ $complianceRequest->mandator->last_name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Email</label>
                            <p class="text-sm text-gray-900">{{ $complianceRequest->mandator->email }}</p>
                        </div>
                        @if($complianceRequest->mandator->phone)
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Phone</label>
                                <p class="text-sm text-gray-900">{{ $complianceRequest->mandator->phone }}</p>
                            </div>
                        @endif
                        @if($complianceRequest->mandator->position)
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Position</label>
                                <p class="text-sm text-gray-900">{{ $complianceRequest->mandator->position }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Timeline -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Timeline</h3>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                            <div class="ml-3">
                                <p class="text-sm text-gray-900">Request received</p>
                                <p class="text-xs text-gray-500">{{ $complianceRequest->created_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>

                        @if($complianceRequest->requested_deadline)
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-2 h-2 {{ $complianceRequest->isOverdue() ? 'bg-red-500' : 'bg-yellow-500' }} rounded-full mt-2"></div>
                                <div class="ml-3">
                                    <p class="text-sm text-gray-900">Deadline: {{ $complianceRequest->requested_deadline->format('M d, Y') }}</p>
                                    @if($complianceRequest->isOverdue())
                                        <p class="text-xs text-red-600 font-medium">Overdue</p>
                                    @else
                                        <p class="text-xs text-gray-500">{{ $complianceRequest->getDaysUntilDeadline() }} days left</p>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if($complianceRequest->response_sent)
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                                <div class="ml-3">
                                    <p class="text-sm text-gray-900">Response sent</p>
                                    <p class="text-xs text-gray-500">{{ $complianceRequest->response_sent_at->format('M d, Y H:i') }}</p>
                                </div>
                            </div>
                        @endif

                        @if($complianceRequest->documents_uploaded)
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                                <div class="ml-3">
                                    <p class="text-sm text-gray-900">Documents uploaded</p>
                                    <p class="text-xs text-gray-500">{{ $complianceRequest->documents_uploaded_at->format('M d, Y H:i') }}</p>
                                </div>
                            </div>
                        @endif

                        @if($complianceRequest->completed_at)
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                                <div class="ml-3">
                                    <p class="text-sm text-gray-900">Request completed</p>
                                    <p class="text-xs text-gray-500">{{ $complianceRequest->completed_at->format('M d, Y H:i') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        @if($complianceRequest->status === 'pending')
                            <form action="{{ route('compliance-requests.mark-in-progress', $complianceRequest) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                                    <i class="fas fa-play mr-2"></i>Mark In Progress
                                </button>
                            </form>
                        @endif

                        @if($complianceRequest->status === 'in_progress')
                            <form action="{{ route('compliance-requests.mark-completed', $complianceRequest) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                    <i class="fas fa-check mr-2"></i>Mark Completed
                                </button>
                            </form>
                        @endif

                        <a href="{{ route('compliance-requests.edit', $complianceRequest) }}" class="block w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded text-center">
                            <i class="fas fa-edit mr-2"></i>Edit Request
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
