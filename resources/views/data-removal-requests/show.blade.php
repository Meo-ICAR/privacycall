<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Data Removal Request') }} - {{ $dataRemovalRequest->request_number }}
            </h2>
            <div class="flex space-x-2">
                @if(!$dataRemovalRequest->isCompleted())
                    <a href="{{ route('data-removal-requests.edit', $dataRemovalRequest) }}"
                       class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                        Edit
                    </a>
                @endif
                <a href="{{ route('data-removal-requests.index') }}"
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to List
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2">
                    <!-- Status and Priority -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900">Request Details</h3>
                                    <p class="text-sm text-gray-500">Created on {{ $dataRemovalRequest->request_date->format('M d, Y') }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                                        bg-{{ $dataRemovalRequest->status_color }}-100 text-{{ $dataRemovalRequest->status_color }}-800">
                                        {{ ucfirst(str_replace('_', ' ', $dataRemovalRequest->status)) }}
                                    </span>
                                    <div class="mt-1">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                            bg-{{ $dataRemovalRequest->priority_color }}-100 text-{{ $dataRemovalRequest->priority_color }}-800">
                                            {{ ucfirst($dataRemovalRequest->priority) }} Priority
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Subject Information -->
                            <div class="mb-6">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Subject</h4>
                                @if($dataRemovalRequest->customer)
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $dataRemovalRequest->customer->full_name }}</div>
                                                <div class="text-sm text-gray-500">{{ $dataRemovalRequest->customer->email }}</div>
                                                @if($dataRemovalRequest->customer->phone)
                                                    <div class="text-sm text-gray-500">{{ $dataRemovalRequest->customer->phone }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @elseif($dataRemovalRequest->mandator)
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                </svg>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $dataRemovalRequest->mandator->full_name }}</div>
                                                <div class="text-sm text-gray-500">{{ $dataRemovalRequest->mandator->email }}</div>
                                                @if($dataRemovalRequest->mandator->phone)
                                                    <div class="text-sm text-gray-500">{{ $dataRemovalRequest->mandator->phone }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Request Information -->
                            <div class="mb-6">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Request Information</h4>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <span class="text-sm font-medium text-gray-700">Type:</span>
                                            <span class="ml-2 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $dataRemovalRequest->request_type)) }}</span>
                                        </div>
                                        <div>
                                            <span class="text-sm font-medium text-gray-700">Requested By:</span>
                                            <span class="ml-2 text-sm text-gray-900">{{ $dataRemovalRequest->requestedByUser->name ?? 'System' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Reason for Removal -->
                            <div class="mb-6">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Reason for Removal</h4>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <p class="text-sm text-gray-900">{{ $dataRemovalRequest->reason_for_removal }}</p>
                                </div>
                            </div>

                            <!-- Data Categories -->
                            @if($dataRemovalRequest->data_categories_to_remove)
                                <div class="mb-6">
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">Data Categories to Remove</h4>
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($dataRemovalRequest->data_categories_to_remove as $category)
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    {{ ucfirst(str_replace('_', ' ', $category)) }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Verification Information -->
                            <div class="mb-6">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Identity Verification</h4>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="flex items-center mb-2">
                                        <span class="text-sm font-medium text-gray-700">Verified:</span>
                                        <span class="ml-2">
                                            @if($dataRemovalRequest->identity_verified)
                                                <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                    Yes
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                    No
                                                </span>
                                            @endif
                                        </span>
                                    </div>
                                    @if($dataRemovalRequest->verification_method)
                                        <div class="mb-2">
                                            <span class="text-sm font-medium text-gray-700">Method:</span>
                                            <span class="ml-2 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $dataRemovalRequest->verification_method)) }}</span>
                                        </div>
                                    @endif
                                    @if($dataRemovalRequest->verification_notes)
                                        <div>
                                            <span class="text-sm font-medium text-gray-700">Notes:</span>
                                            <p class="mt-1 text-sm text-gray-900">{{ $dataRemovalRequest->verification_notes }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Processing Information -->
                            @if($dataRemovalRequest->review_date || $dataRemovalRequest->completion_date)
                                <div class="mb-6">
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">Processing Information</h4>
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        @if($dataRemovalRequest->review_date)
                                            <div class="mb-2">
                                                <span class="text-sm font-medium text-gray-700">Review Date:</span>
                                                <span class="ml-2 text-sm text-gray-900">{{ $dataRemovalRequest->review_date->format('M d, Y') }}</span>
                                                @if($dataRemovalRequest->reviewedByUser)
                                                    <span class="ml-2 text-sm text-gray-500">by {{ $dataRemovalRequest->reviewedByUser->name }}</span>
                                                @endif
                                            </div>
                                        @endif
                                        @if($dataRemovalRequest->completion_date)
                                            <div class="mb-2">
                                                <span class="text-sm font-medium text-gray-700">Completion Date:</span>
                                                <span class="ml-2 text-sm text-gray-900">{{ $dataRemovalRequest->completion_date->format('M d, Y') }}</span>
                                                @if($dataRemovalRequest->completedByUser)
                                                    <span class="ml-2 text-sm text-gray-500">by {{ $dataRemovalRequest->completedByUser->name }}</span>
                                                @endif
                                            </div>
                                        @endif
                                        @if($dataRemovalRequest->data_removal_method)
                                            <div class="mb-2">
                                                <span class="text-sm font-medium text-gray-700">Removal Method:</span>
                                                <span class="ml-2 text-sm text-gray-900">{{ $dataRemovalRequest->data_removal_method }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <!-- Notes and Comments -->
                            @if($dataRemovalRequest->review_notes || $dataRemovalRequest->rejection_reason || $dataRemovalRequest->completion_notes)
                                <div class="mb-6">
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">Notes & Comments</h4>
                                    <div class="bg-gray-50 p-4 rounded-lg space-y-3">
                                        @if($dataRemovalRequest->review_notes)
                                            <div>
                                                <span class="text-sm font-medium text-gray-700">Review Notes:</span>
                                                <p class="mt-1 text-sm text-gray-900">{{ $dataRemovalRequest->review_notes }}</p>
                                            </div>
                                        @endif
                                        @if($dataRemovalRequest->rejection_reason)
                                            <div>
                                                <span class="text-sm font-medium text-red-700">Rejection Reason:</span>
                                                <p class="mt-1 text-sm text-red-900">{{ $dataRemovalRequest->rejection_reason }}</p>
                                            </div>
                                        @endif
                                        @if($dataRemovalRequest->completion_notes)
                                            <div>
                                                <span class="text-sm font-medium text-gray-700">Completion Notes:</span>
                                                <p class="mt-1 text-sm text-gray-900">{{ $dataRemovalRequest->completion_notes }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <!-- File Upload -->
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                                <div class="p-6">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Upload Document</h3>
                                    <form method="POST" action="{{ route('data-removal-requests.upload-document', $dataRemovalRequest) }}" enctype="multipart/form-data">
                                        @csrf
                                        <div class="flex items-center space-x-4">
                                            <input type="file" name="file" required class="block w-full text-sm text-gray-500">
                                            <input type="text" name="description" placeholder="Description (optional)" class="block w-full text-sm text-gray-500">
                                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Upload</button>
                                        </div>
                                        @error('file')<p class="text-red-600 text-sm mt-2">{{ $message }}</p>@enderror
                                        @error('description')<p class="text-red-600 text-sm mt-2">{{ $message }}</p>@enderror
                                    </form>
                                </div>
                            </div>

                            <!-- Uploaded Documents -->
                            @if($dataRemovalRequest->documents->count())
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                                <div class="p-6">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Uploaded Documents</h3>
                                    <ul class="divide-y divide-gray-200">
                                        @foreach($dataRemovalRequest->documents as $doc)
                                            <li class="py-2 flex items-center justify-between">
                                                <div>
                                                    <a href="{{ Storage::url($doc->path) }}" target="_blank" class="text-blue-600 hover:underline">{{ basename($doc->path) }}</a>
                                                    @if($doc->description)
                                                        <span class="ml-2 text-gray-500 text-xs">({{ $doc->description }})</span>
                                                    @endif
                                                    <span class="ml-2 text-gray-400 text-xs">uploaded by {{ $doc->user->name ?? 'N/A' }} on {{ $doc->created_at->format('Y-m-d H:i') }}</span>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <!-- Actions -->
                    @if(!$dataRemovalRequest->isCompleted())
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Actions</h3>

                                @if($dataRemovalRequest->requiresReview())
                                    <form method="POST" action="{{ route('data-removal-requests.mark-in-review', $dataRemovalRequest) }}" class="mb-3">
                                        @csrf
                                        <button type="submit" class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-2">
                                            Mark as In Review
                                        </button>
                                    </form>
                                @endif

                                @if($dataRemovalRequest->status === 'in_review')
                                    <button onclick="openApproveModal()" class="w-full bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mb-2">
                                        Approve Request
                                    </button>
                                    <button onclick="openRejectModal()" class="w-full bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded mb-2">
                                        Reject Request
                                    </button>
                                @endif

                                @if($dataRemovalRequest->status === 'approved')
                                    <button onclick="openCompleteModal()" class="w-full bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mb-2">
                                        Mark as Completed
                                    </button>
                                @endif

                                @if(in_array($dataRemovalRequest->status, ['pending', 'in_review']))
                                    <button onclick="openCancelModal()" class="w-full bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                        Cancel Request
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Timeline -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Timeline</h3>
                            <div class="space-y-4">
                                @foreach($dataRemovalRequest->auditLogs as $log)
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">{{ ucfirst($log->action) }}</p>
                                            <p class="text-sm text-gray-500">{{ $log->created_at->format('M d, Y H:i') }}</p>
                                            <p class="text-sm text-gray-500">by {{ $log->user->name ?? 'System' }}</p>
                                            @if($log->notes)
                                                <p class="text-xs text-gray-700">{{ $log->notes }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Due Date Warning -->
                    @if($dataRemovalRequest->due_date)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Due Date</h3>
                                <div class="text-center">
                                    @if($dataRemovalRequest->isOverdue)
                                        <div class="text-red-600 font-semibold">OVERDUE</div>
                                        <div class="text-sm text-gray-500">{{ $dataRemovalRequest->due_date->format('M d, Y') }}</div>
                                        <div class="text-xs text-red-500 mt-1">{{ $dataRemovalRequest->days_until_due }} days overdue</div>
                                    @elseif($dataRemovalRequest->isDueSoon)
                                        <div class="text-orange-600 font-semibold">DUE SOON</div>
                                        <div class="text-sm text-gray-500">{{ $dataRemovalRequest->due_date->format('M d, Y') }}</div>
                                        <div class="text-xs text-orange-500 mt-1">{{ $dataRemovalRequest->days_until_due }} days remaining</div>
                                    @else
                                        <div class="text-green-600 font-semibold">ON TRACK</div>
                                        <div class="text-sm text-gray-500">{{ $dataRemovalRequest->due_date->format('M d, Y') }}</div>
                                        <div class="text-xs text-green-500 mt-1">{{ $dataRemovalRequest->days_until_due }} days remaining</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    @include('data-removal-requests.partials.approve-modal')
    @include('data-removal-requests.partials.reject-modal')
    @include('data-removal-requests.partials.complete-modal')
    @include('data-removal-requests.partials.cancel-modal')
</x-app-layout>
