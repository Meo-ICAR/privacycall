@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Email Management</h1>
                <p class="mt-2 text-gray-600">{{ $company->name }} - Data Controller Contact Emails</p>
            </div>
            <div class="flex space-x-3">
                <button id="fetchEmailsBtn" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <i class="fas fa-sync-alt mr-2"></i>
                    Fetch New Emails
                </button>
                <a href="{{ route('companies.emails.create', $company) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                    <i class="fas fa-plus mr-2"></i>
                    Send Email
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <!-- Email Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-envelope text-blue-600 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Emails</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['total'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-envelope-open text-green-600 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Unread</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['unread'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-reply text-purple-600 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Replied</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['replied'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-shield-alt text-red-600 text-2xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">GDPR Related</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['gdpr_related'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white shadow rounded-lg mb-6">
        <div class="px-4 py-5 sm:p-6">
            <form method="GET" action="{{ route('companies.emails.index', $company) }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                    <div class="md:col-span-2">
                        <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               placeholder="Search emails...">
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">All</option>
                            <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>Unread</option>
                            <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>Read</option>
                            <option value="replied" {{ request('status') == 'replied' ? 'selected' : '' }}>Replied</option>
                            <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                    </div>

                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700">Priority</label>
                        <select name="priority" id="priority" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">All</option>
                            <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="normal" {{ request('priority') == 'normal' ? 'selected' : '' }}>Normal</option>
                            <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                            <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                        </select>
                    </div>

                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                        <select name="category" id="category" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">All</option>
                            <option value="complaint" {{ request('category') == 'complaint' ? 'selected' : '' }}>Complaint</option>
                            <option value="inquiry" {{ request('category') == 'inquiry' ? 'selected' : '' }}>Inquiry</option>
                            <option value="notification" {{ request('category') == 'notification' ? 'selected' : '' }}>Notification</option>
                            <option value="general" {{ request('category') == 'general' ? 'selected' : '' }}>General</option>
                        </select>
                    </div>

                    <div class="flex items-end">
                        <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            <i class="fas fa-search mr-2"></i>
                            Filter
                        </button>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="gdpr_related" value="true" {{ request('gdpr_related') == 'true' ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">GDPR Related Only</span>
                    </label>
                </div>
            </form>
        </div>
    </div>

    <!-- Emails List -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        @if($emails->count())
            <ul class="divide-y divide-gray-200">
                @foreach($emails as $email)
                    <li class="px-6 py-4 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3 flex-1">
                                <!-- Status indicator -->
                                <div class="flex-shrink-0">
                                    @if($email->status === 'unread')
                                        <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                    @elseif($email->status === 'replied')
                                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                    @else
                                        <div class="w-3 h-3 bg-gray-400 rounded-full"></div>
                                    @endif
                                </div>

                                <!-- Email content -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center space-x-2">
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            {{ $email->subject }}
                                        </p>
                                        @if($email->is_gdpr_related)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                GDPR
                                            </span>
                                        @endif
                                        @if($email->hasAttachments())
                                            <i class="fas fa-paperclip text-gray-400"></i>
                                        @endif
                                    </div>
                                    <div class="flex items-center space-x-2 mt-1">
                                        <p class="text-sm text-gray-500">
                                            From: {{ $email->sender_display_name }}
                                        </p>
                                        <span class="text-gray-300">•</span>
                                        <p class="text-sm text-gray-500">{{ $email->age }}</p>
                                        <span class="text-gray-300">•</span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $email->priority_badge_class }}">
                                            {{ ucfirst($email->priority) }}
                                        </span>
                                        @if($email->category)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ ucfirst($email->category) }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-600 mt-1">{{ $email->excerpt }}</p>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('companies.emails.show', [$company, $email]) }}"
                                   class="inline-flex items-center px-3 py-1 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    <i class="fas fa-eye mr-1"></i> View
                                </a>
                                @if($email->status !== 'replied')
                                    <a href="{{ route('companies.emails.reply', [$company, $email]) }}"
                                       class="inline-flex items-center px-3 py-1 border border-blue-300 text-xs font-medium rounded-md text-blue-700 bg-white hover:bg-blue-50">
                                        <i class="fas fa-reply mr-1"></i> Reply
                                    </a>
                                @endif
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>

            <!-- Pagination -->
            <div class="px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $emails->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-envelope text-gray-400 text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No emails found</h3>
                <p class="text-gray-500 mb-4">
                    @if(request()->hasAny(['search', 'status', 'priority', 'category', 'gdpr_related']))
                        No emails match your current filters. Try adjusting your search criteria.
                    @else
                        No emails have been fetched yet. Click "Fetch New Emails" to get started.
                    @endif
                </p>
                <button id="fetchEmailsBtnEmpty" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <i class="fas fa-sync-alt mr-2"></i>
                    Fetch New Emails
                </button>
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fetchEmailsBtn = document.getElementById('fetchEmailsBtn');
    const fetchEmailsBtnEmpty = document.getElementById('fetchEmailsBtnEmpty');

    function fetchEmails() {
        const button = event.target;
        const originalText = button.innerHTML;

        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Fetching...';

        fetch('{{ route("companies.emails.fetch", $company) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while fetching emails.');
        })
        .finally(() => {
            button.disabled = false;
            button.innerHTML = originalText;
        });
    }

    if (fetchEmailsBtn) {
        fetchEmailsBtn.addEventListener('click', fetchEmails);
    }

    if (fetchEmailsBtnEmpty) {
        fetchEmailsBtnEmpty.addEventListener('click', fetchEmails);
    }
});
</script>
@endsection
