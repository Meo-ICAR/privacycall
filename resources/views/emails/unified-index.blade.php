@extends('layouts.app')

@section('title', 'Email Management')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Email Management</h1>
                <p class="text-gray-600 mt-2">Manage all incoming and outgoing emails for <span class="font-semibold">{{ $company->name }}</span></p>
            </div>
            <div class="flex space-x-3">
                <button onclick="openSendEmailModal()" class="btn btn-primary">
                    <i class="fas fa-plus mr-2"></i>Send New Email
                </button>
                <a href="{{ route('emails.dashboard', $company) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="p-6">
                <form method="GET" action="{{ route('emails.index', $company) }}" class="grid grid-cols-1 md:grid-cols-6 gap-4">
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                        <select id="type" name="type" class="form-select w-full">
                            <option value="all" {{ $type === 'all' ? 'selected' : '' }}>All Emails</option>
                            <option value="incoming" {{ $type === 'incoming' ? 'selected' : '' }}>Incoming</option>
                            <option value="outgoing" {{ $type === 'outgoing' ? 'selected' : '' }}>Outgoing</option>
                        </select>
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select id="status" name="status" class="form-select w-full">
                            <option value="">All Statuses</option>
                            @foreach($statusOptions as $value => $label)
                                <option value="{{ $value }}" {{ $status === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                        <select id="priority" name="priority" class="form-select w-full">
                            <option value="">All Priorities</option>
                            @foreach($priorityOptions as $value => $label)
                                <option value="{{ $value }}" {{ $priority === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <select id="category" name="category" class="form-select w-full">
                            <option value="">All Categories</option>
                            @foreach($categoryOptions as $value => $label)
                                <option value="{{ $value }}" {{ $category === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <input type="text" id="search" name="search" value="{{ $search }}" placeholder="Subject, email, content..." class="form-input w-full">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="btn btn-primary w-full">
                            <i class="fas fa-search mr-2"></i>Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Email Lists -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Incoming Emails -->
            @if($type !== 'outgoing')
            <div class="bg-white shadow rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">Incoming Emails</h2>
                    <p class="text-gray-600 text-sm mt-1">Emails received by {{ $company->name }}</p>
                </div>
                <div class="p-6">
                    @if($incomingEmails && $incomingEmails->count())
                        <div class="space-y-4">
                            @foreach($incomingEmails as $email)
                                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="font-medium text-gray-900">
                                                <a href="{{ route('emails.show', ['id' => $email->id, 'type' => 'incoming']) }}" class="hover:underline">
                                                    {{ $email->subject }}
                                                </a>
                                            </div>
                                            <div class="text-sm text-gray-500 mt-1">
                                                From: {{ $email->from_email }} | {{ $email->received_at->diffForHumans() }}
                                            </div>
                                            <div class="flex flex-wrap gap-2 mt-2">
                                                <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium {{ $email->is_gdpr_related ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800' }}">
                                                    {{ $email->is_gdpr_related ? 'GDPR' : ucfirst($email->category ?? 'General') }}
                                                </span>
                                                <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium {{ $email->priority === 'urgent' ? 'bg-red-100 text-red-800' : ($email->priority === 'high' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                                    {{ ucfirst($email->priority) }}
                                                </span>
                                                <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium {{ $email->status === 'unread' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                                    {{ ucfirst($email->status) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4 flex-shrink-0">
                                            <a href="{{ route('emails.show', ['id' => $email->id, 'type' => 'incoming']) }}" class="btn btn-sm btn-primary">View</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($incomingEmails->hasPages())
                            <div class="mt-6">
                                {{ $incomingEmails->appends(request()->query())->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-gray-500 text-center py-8">No incoming emails found.</div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Outgoing Emails -->
            @if($type !== 'incoming')
            <div class="bg-white shadow rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">Outgoing Emails</h2>
                    <p class="text-gray-600 text-sm mt-1">Emails sent by {{ $company->name }}</p>
                </div>
                <div class="p-6">
                    @if($outgoingEmails && $outgoingEmails->count())
                        <div class="space-y-4">
                            @foreach($outgoingEmails as $email)
                                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="font-medium text-gray-900">
                                                <a href="{{ route('emails.show', ['id' => $email->id, 'type' => 'outgoing']) }}" class="hover:underline">
                                                    {{ $email->subject }}
                                                </a>
                                            </div>
                                            <div class="text-sm text-gray-500 mt-1">
                                                To: {{ $email->recipient_email }} | {{ $email->created_at->diffForHumans() }}
                                            </div>
                                            <div class="flex flex-wrap gap-2 mt-2">
                                                <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium {{ $email->status === 'failed' ? 'bg-red-100 text-red-800' : ($email->status === 'delivered' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800') }}">
                                                    {{ ucfirst($email->status) }}
                                                </span>
                                                @if($email->template_name)
                                                    <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        Template: {{ $email->template_name }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="ml-4 flex-shrink-0">
                                            <a href="{{ route('emails.show', ['id' => $email->id, 'type' => 'outgoing']) }}" class="btn btn-sm btn-primary">View</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($outgoingEmails->hasPages())
                            <div class="mt-6">
                                {{ $outgoingEmails->appends(request()->query())->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-gray-500 text-center py-8">No outgoing emails found.</div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Send Email Modal -->
<div id="sendEmailModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Send New Email</h3>
                <button onclick="closeSendEmailModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form action="{{ route('emails.send', $company) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="to_email" class="block text-sm font-medium text-gray-700">To Email *</label>
                        <input type="email" id="to_email" name="to_email" required class="form-input w-full mt-1">
                    </div>

                    <div>
                        <label for="to_name" class="block text-sm font-medium text-gray-700">To Name</label>
                        <input type="text" id="to_name" name="to_name" class="form-input w-full mt-1">
                    </div>

                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700">Subject *</label>
                        <input type="text" id="subject" name="subject" required class="form-input w-full mt-1">
                    </div>

                    <div>
                        <label for="body" class="block text-sm font-medium text-gray-700">Message *</label>
                        <textarea id="body" name="body" rows="6" required class="form-textarea w-full mt-1"></textarea>
                    </div>

                    <div>
                        <label for="attachments" class="block text-sm font-medium text-gray-700">Attachments</label>
                        <input type="file" id="attachments" name="attachments[]" multiple class="form-input w-full mt-1" accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png,.gif">
                        <p class="text-xs text-gray-500 mt-1">You can select multiple files. Maximum 10MB per file.</p>
                    </div>

                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700">Priority</label>
                        <select id="priority" name="priority" class="form-select w-full mt-1">
                            <option value="normal">Normal</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                            <option value="low">Low</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeSendEmailModal()" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane mr-2"></i>Send Email
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openSendEmailModal() {
    document.getElementById('sendEmailModal').classList.remove('hidden');
}

function closeSendEmailModal() {
    document.getElementById('sendEmailModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('sendEmailModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeSendEmailModal();
    }
});
</script>

@endsection
