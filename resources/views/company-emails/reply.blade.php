@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <div class="flex items-center">
            <a href="{{ route('companies.emails.show', [$company, $email]) }}" class="text-blue-600 hover:text-blue-800 mr-4">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Reply to Email</h1>
                <p class="mt-2 text-gray-600">Reply to: {{ $email->sender_display_name }}</p>
            </div>
        </div>
    </div>

    @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Reply Form -->
        <div>
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Compose Reply</h3>

                    <form method="POST" action="{{ route('companies.emails.send-reply', [$company, $email]) }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <div>
                            <label class="block text-sm font-medium text-gray-700">To</label>
                            <div class="mt-1 p-3 bg-gray-50 rounded-md">
                                <p class="text-sm text-gray-900">{{ $email->from_name ? $email->from_name . ' <' . $email->from_email . '>' : $email->from_email }}</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Subject</label>
                            <div class="mt-1 p-3 bg-gray-50 rounded-md">
                                <p class="text-sm text-gray-900">Re: {{ $email->subject }}</p>
                            </div>
                        </div>

                        <div>
                            <label for="reply_body" class="block text-sm font-medium text-gray-700">Message</label>
                            <textarea name="reply_body" id="reply_body" rows="12" required
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                      placeholder="Type your reply here...">{{ old('reply_body') }}</textarea>
                            @error('reply_body')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="attachments" class="block text-sm font-medium text-gray-700">Attachments</label>
                            <input type="file" name="attachments[]" id="attachments" multiple
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <p class="mt-1 text-sm text-gray-500">You can select multiple files. Maximum 10MB per file.</p>
                            @error('attachments.*')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('companies.emails.show', [$company, $email]) }}"
                               class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                <i class="fas fa-times mr-2"></i>
                                Cancel
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                <i class="fas fa-paper-plane mr-2"></i>
                                Send Reply
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Original Email -->
        <div>
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Original Email</h3>

                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="mb-4">
                            <h4 class="font-medium text-gray-900">{{ $email->subject }}</h4>
                            <div class="mt-1 text-sm text-gray-500">
                                <p>From: {{ $email->sender_display_name }}</p>
                                <p>Date: {{ $email->received_at->format('M j, Y g:i A') }}</p>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 pt-4">
                            <div class="prose prose-sm max-w-none">
                                @if($email->body_plain)
                                    <div class="whitespace-pre-wrap text-gray-900 text-sm">{{ $email->body_plain }}</div>
                                @else
                                    <div class="text-gray-900 text-sm">{!! $email->body !!}</div>
                                @endif
                            </div>
                        </div>

                        @if($email->hasAttachments())
                            <div class="border-t border-gray-200 pt-4 mt-4">
                                <h5 class="text-sm font-medium text-gray-900 mb-2">Attachments</h5>
                                <div class="space-y-1">
                                    @foreach($email->attachments as $attachment)
                                        <div class="flex items-center text-sm text-gray-600">
                                            <i class="fas fa-paperclip mr-2"></i>
                                            <span>{{ $attachment['name'] }}</span>
                                            <span class="ml-2 text-gray-400">({{ number_format($attachment['size'] / 1024, 1) }} KB)</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Email Thread -->
            @if($email->thread_id)
                <div class="bg-white shadow rounded-lg mt-6">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Email Thread</h3>

                        @php
                            $threadEmails = \App\Models\CompanyEmail::where('thread_id', $email->thread_id)
                                ->where('company_id', $company->id)
                                ->where('id', '!=', $email->id)
                                ->orderBy('received_at', 'asc')
                                ->get();
                        @endphp

                        @if($threadEmails->count())
                            <div class="space-y-3">
                                @foreach($threadEmails as $threadEmail)
                                    <div class="border-l-4 border-gray-200 pl-4 py-2">
                                        <div class="flex items-center justify-between mb-1">
                                            <p class="text-sm font-medium text-gray-900">{{ $threadEmail->subject }}</p>
                                            <span class="text-xs text-gray-500">{{ $threadEmail->received_at->format('M j, Y') }}</span>
                                        </div>
                                        <p class="text-sm text-gray-600">{{ $threadEmail->sender_display_name }}</p>
                                        <p class="text-sm text-gray-500 mt-1">{{ Str::limit(strip_tags($threadEmail->body_plain ?: $threadEmail->body), 80) }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500">No other emails in this thread.</p>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('reply_body');
    const originalSubject = '{{ $email->subject }}';

    // Auto-resize textarea
    textarea.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = this.scrollHeight + 'px';
    });

    // Set initial height
    textarea.style.height = textarea.scrollHeight + 'px';
});
</script>
@endsection
