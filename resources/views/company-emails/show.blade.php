@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <div class="flex items-center">
            <a href="{{ route('companies.emails.index', $company) }}" class="text-blue-600 hover:text-blue-800 mr-4">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $email->subject }}</h1>
                <p class="mt-2 text-gray-600">Email from {{ $email->sender_display_name }}</p>
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

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Main Email Content -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <!-- Email Header -->
                    <div class="border-b border-gray-200 pb-4 mb-4">
                        <div class="flex items-center justify-between mb-2">
                            <h2 class="text-lg font-medium text-gray-900">{{ $email->subject }}</h2>
                            <div class="flex items-center space-x-2">
                                @if($email->is_gdpr_related)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        GDPR Related
                                    </span>
                                @endif
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $email->priority_badge_class }}">
                                    {{ ucfirst($email->priority) }}
                                </span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $email->status_badge_class }}">
                                    {{ ucfirst($email->status) }}
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                            <div>
                                <dt class="font-medium text-gray-500">From:</dt>
                                <dd class="text-gray-900">{{ $email->from_name ? $email->from_name . ' <' . $email->from_email . '>' : $email->from_email }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-500">To:</dt>
                                <dd class="text-gray-900">{{ $email->to_email }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-500">Received:</dt>
                                <dd class="text-gray-900">{{ $email->received_at->format('M j, Y g:i A') }}</dd>
                            </div>
                            @if($email->read_at)
                            <div>
                                <dt class="font-medium text-gray-500">Read:</dt>
                                <dd class="text-gray-900">{{ $email->read_at->format('M j, Y g:i A') }}</dd>
                            </div>
                            @endif
                            @if($email->replied_at)
                            <div>
                                <dt class="font-medium text-gray-500">Replied:</dt>
                                <dd class="text-gray-900">{{ $email->replied_at->format('M j, Y g:i A') }}</dd>
                            </div>
                            @endif
                            @if($email->category)
                            <div>
                                <dt class="font-medium text-gray-500">Category:</dt>
                                <dd class="text-gray-900">{{ ucfirst($email->category) }}</dd>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Email Body -->
                    <div class="prose max-w-none">
                        @if($email->body_plain)
                            <div class="whitespace-pre-wrap text-gray-900">{{ $email->body_plain }}</div>
                        @else
                            <div class="text-gray-900">{!! $email->body !!}</div>
                        @endif
                    </div>

                    <!-- Attachments -->
                    @if($email->hasAttachments())
                        <div class="mt-6 border-t border-gray-200 pt-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-3">Attachments</h3>
                            <div class="space-y-2">
                                @foreach($email->attachments as $attachment)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div class="flex items-center">
                                            <i class="fas fa-paperclip text-gray-400 mr-3"></i>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $attachment['name'] }}</p>
                                                <p class="text-xs text-gray-500">{{ number_format($attachment['size'] / 1024, 1) }} KB</p>
                                            </div>
                                        </div>
                                        <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                            Download
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Thread Emails -->
            @if($threadEmails->count() > 1)
                <div class="bg-white shadow rounded-lg mt-6">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Email Thread</h3>
                        <div class="space-y-4">
                            @foreach($threadEmails as $threadEmail)
                                @if($threadEmail->id !== $email->id)
                                    <div class="border-l-4 border-gray-200 pl-4 py-2">
                                        <div class="flex items-center justify-between mb-1">
                                            <p class="text-sm font-medium text-gray-900">{{ $threadEmail->subject }}</p>
                                            <span class="text-xs text-gray-500">{{ $threadEmail->received_at->format('M j, Y g:i A') }}</span>
                                        </div>
                                        <p class="text-sm text-gray-600">{{ $threadEmail->sender_display_name }}</p>
                                        <p class="text-sm text-gray-500 mt-1">{{ Str::limit(strip_tags($threadEmail->body_plain ?: $threadEmail->body), 100) }}</p>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Email Actions -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Actions</h3>
                    <div class="space-y-3">
                        @if($email->status !== 'replied')
                            <a href="{{ route('companies.emails.reply', [$company, $email]) }}"
                               class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                <i class="fas fa-reply mr-2"></i>
                                Reply
                            </a>
                        @endif

                        <form method="POST" action="{{ route('companies.emails.update', [$company, $email]) }}" class="space-y-2">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="action" value="mark_read">
                            <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                <i class="fas fa-check mr-2"></i>
                                Mark as Read
                            </button>
                        </form>

                        <form method="POST" action="{{ route('companies.emails.update', [$company, $email]) }}" class="space-y-2">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="action" value="archive">
                            <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                <i class="fas fa-archive mr-2"></i>
                                Archive
                            </button>
                        </form>

                        <a href="{{ route('companies.emails.index', $company) }}"
                           class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back to List
                        </a>
                    </div>
                </div>
            </div>

            <!-- Email Properties -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Properties</h3>
                    <div class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Email ID</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $email->email_id }}</dd>
                        </div>
                        @if($email->thread_id)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Thread ID</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $email->thread_id }}</dd>
                        </div>
                        @endif
                        @if($email->user)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Handled By</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $email->user->name }}</dd>
                        </div>
                        @endif
                        @if($email->notes)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Notes</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $email->notes }}</dd>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <form method="POST" action="{{ route('companies.emails.update', [$company, $email]) }}" class="space-y-2">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="action" value="update_priority">
                            <label class="block text-sm font-medium text-gray-700">Priority</label>
                            <select name="priority" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="low" {{ $email->priority === 'low' ? 'selected' : '' }}>Low</option>
                                <option value="normal" {{ $email->priority === 'normal' ? 'selected' : '' }}>Normal</option>
                                <option value="high" {{ $email->priority === 'high' ? 'selected' : '' }}>High</option>
                                <option value="urgent" {{ $email->priority === 'urgent' ? 'selected' : '' }}>Urgent</option>
                            </select>
                            <button type="submit" class="w-full inline-flex items-center justify-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                Update Priority
                            </button>
                        </form>

                        <form method="POST" action="{{ route('companies.emails.update', [$company, $email]) }}" class="space-y-2">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="action" value="update_category">
                            <label class="block text-sm font-medium text-gray-700">Category</label>
                            <select name="category" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="">None</option>
                                <option value="complaint" {{ $email->category === 'complaint' ? 'selected' : '' }}>Complaint</option>
                                <option value="inquiry" {{ $email->category === 'inquiry' ? 'selected' : '' }}>Inquiry</option>
                                <option value="notification" {{ $email->category === 'notification' ? 'selected' : '' }}>Notification</option>
                                <option value="general" {{ $email->category === 'general' ? 'selected' : '' }}>General</option>
                            </select>
                            <button type="submit" class="w-full inline-flex items-center justify-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                Update Category
                            </button>
                        </form>

                        <form method="POST" action="{{ route('companies.emails.update', [$company, $email]) }}" class="space-y-2">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="action" value="add_notes">
                            <label class="block text-sm font-medium text-gray-700">Add Notes</label>
                            <textarea name="notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Add notes about this email...">{{ $email->notes }}</textarea>
                            <button type="submit" class="w-full inline-flex items-center justify-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                Save Notes
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
