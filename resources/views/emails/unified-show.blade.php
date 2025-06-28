@extends('layouts.app')

@section('title', 'Email Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    @if($type === 'incoming')
                        Incoming Email
                    @else
                        Outgoing Email
                    @endif
                </h1>
                <p class="text-gray-600 mt-1">{{ $company->name }}</p>
            </div>
            <a href="{{ route('emails.dashboard', $company) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
            </a>
        </div>

        <!-- Email Details -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">{{ $email->subject }}</h2>
                        <div class="mt-2 text-sm text-gray-600">
                            @if($type === 'incoming')
                                <div><strong>From:</strong> {{ $email->from_name ? $email->from_name . ' <' . $email->from_email . '>' : $email->from_email }}</div>
                                <div><strong>To:</strong> {{ $email->to_email }}</div>
                                <div><strong>Received:</strong> {{ $email->received_at->format('M j, Y g:i A') }}</div>
                            @else
                                <div><strong>To:</strong> {{ $email->recipient_name ? $email->recipient_name . ' <' . $email->recipient_email . '>' : $email->recipient_email }}</div>
                                <div><strong>Sent:</strong> {{ $email->sent_at ? $email->sent_at->format('M j, Y g:i A') : $email->created_at->format('M j, Y g:i A') }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="flex flex-col items-end space-y-2">
                        @if($type === 'incoming')
                            <div class="flex space-x-2">
                                <span class="inline-block px-2 py-1 rounded-full text-xs font-medium {{ $email->is_gdpr_related ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $email->is_gdpr_related ? 'GDPR' : ucfirst($email->category ?? 'General') }}
                                </span>
                                <span class="inline-block px-2 py-1 rounded-full text-xs font-medium {{ $email->priority === 'urgent' ? 'bg-red-100 text-red-800' : ($email->priority === 'high' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                    {{ ucfirst($email->priority) }}
                                </span>
                                <span class="inline-block px-2 py-1 rounded-full text-xs font-medium {{ $email->status === 'unread' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                    {{ ucfirst($email->status) }}
                                </span>
                            </div>
                        @else
                            <span class="inline-block px-2 py-1 rounded-full text-xs font-medium {{ $email->status === 'failed' ? 'bg-red-100 text-red-800' : ($email->status === 'delivered' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ ucfirst($email->status) }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Email Body -->
            <div class="p-6">
                <div class="prose max-w-none">
                    @if($type === 'incoming')
                        {!! $email->body !!}
                    @else
                        {!! $email->body !!}
                    @endif
                </div>
            </div>

            <!-- Attachments -->
            @if($type === 'incoming' && $email->documents->count() > 0)
                <div class="p-6 border-t border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Attachments</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach($email->documents as $document)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-paperclip text-gray-400 mr-3"></i>
                                    <div>
                                        <div class="font-medium text-sm">{{ $document->original_name }}</div>
                                        <div class="text-xs text-gray-500">{{ number_format($document->size / 1024, 1) }} KB</div>
                                    </div>
                                </div>
                                <a href="{{ route('emails.download-attachment', ['id' => $document->id, 'type' => 'incoming']) }}"
                                   class="btn btn-sm btn-outline">
                                    <i class="fas fa-download mr-1"></i>Download
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($type === 'outgoing' && $email->replyAttachments->count() > 0)
                <div class="p-6 border-t border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Attachments Sent</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach($email->replyAttachments as $attachment)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-paperclip text-gray-400 mr-3"></i>
                                    <div>
                                        <div class="font-medium text-sm">{{ $attachment->original_name }}</div>
                                        <div class="text-xs text-gray-500">{{ number_format($attachment->size / 1024, 1) }} KB</div>
                                    </div>
                                </div>
                                <a href="{{ route('emails.download-attachment', ['id' => $attachment->id, 'type' => 'outgoing']) }}"
                                   class="btn btn-sm btn-outline">
                                    <i class="fas fa-download mr-1"></i>Download
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Reply Form (only for incoming emails) -->
        @if($type === 'incoming')
            <div class="bg-white shadow rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Reply to Email</h3>
                </div>
                <form action="{{ route('emails.reply', $email) }}" method="POST" enctype="multipart/form-data" class="p-6">
                    @csrf
                    <div class="mb-4">
                        <label for="reply_body" class="block text-sm font-medium text-gray-700 mb-2">Reply Message</label>
                        <textarea id="reply_body" name="reply_body" rows="6" class="form-textarea w-full" required placeholder="Type your reply here...">{{ old('reply_body') }}</textarea>
                        @error('reply_body')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="attachments" class="block text-sm font-medium text-gray-700 mb-2">Attachments (optional)</label>
                        <input type="file" id="attachments" name="attachments[]" multiple class="form-input w-full" accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png,.gif">
                        <p class="text-xs text-gray-500 mt-1">You can select multiple files. Maximum 10MB per file.</p>
                        @error('attachments.*')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('emails.dashboard', $company) }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane mr-2"></i>Send Reply
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection
