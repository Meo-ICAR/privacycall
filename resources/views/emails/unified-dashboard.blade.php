@extends('layouts.app')

@section('title', 'Unified Email Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Unified Email Dashboard</h1>
                <p class="text-gray-600 mt-2">Manage all incoming and outgoing emails for <span class="font-semibold">{{ $company->name }}</span></p>
            </div>
            <a href="{{ route('companies.show', $company) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Back to Company
            </a>
        </div>

        <!-- Email Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white shadow rounded-lg p-6 text-center">
                <div class="text-2xl font-bold text-blue-600">{{ $stats['total'] }}</div>
                <div class="text-sm text-gray-500">Total Emails</div>
            </div>
            <div class="bg-white shadow rounded-lg p-6 text-center">
                <div class="text-2xl font-bold text-green-600">{{ $stats['unread'] }}</div>
                <div class="text-sm text-gray-500">Unread Incoming</div>
            </div>
            <div class="bg-white shadow rounded-lg p-6 text-center">
                <div class="text-2xl font-bold text-yellow-600">{{ $stats['gdpr_related'] }}</div>
                <div class="text-sm text-gray-500">GDPR Related</div>
            </div>
            <div class="bg-white shadow rounded-lg p-6 text-center">
                <div class="text-2xl font-bold text-red-600">{{ $stats['high_priority'] }}</div>
                <div class="text-sm text-gray-500">High/Urgent Priority</div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Recent Incoming Emails -->
            <div class="bg-white shadow rounded-lg">
                <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-900">Recent Incoming Emails</h2>
                    <a href="{{ route('emails.index', ['type' => 'incoming']) }}" class="text-blue-600 hover:underline text-sm">View All</a>
                </div>
                <div class="p-6">
                    @if($recentIncoming->count())
                        <ul class="divide-y divide-gray-200">
                            @foreach($recentIncoming as $email)
                                <li class="py-3 flex items-start justify-between">
                                    <div>
                                        <div class="font-medium text-gray-900">
                                            <a href="{{ route('emails.show', ['id' => $email->id, 'type' => 'incoming']) }}" class="hover:underline">
                                                {{ $email->subject }}
                                            </a>
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            From: {{ $email->from_email }} | {{ $email->received_at->diffForHumans() }}
                                        </div>
                                        <div class="text-xs mt-1">
                                            <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium {{ $email->is_gdpr_related ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ $email->is_gdpr_related ? 'GDPR' : ucfirst($email->category ?? 'General') }}
                                            </span>
                                            <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium {{ $email->priority === 'urgent' ? 'bg-red-100 text-red-800' : ($email->priority === 'high' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                                {{ ucfirst($email->priority) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4 flex-shrink-0">
                                        <a href="{{ route('emails.show', ['id' => $email->id, 'type' => 'incoming']) }}" class="btn btn-sm btn-primary">View</a>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-gray-500 text-center py-8">No recent incoming emails.</div>
                    @endif
                </div>
            </div>

            <!-- Recent Outgoing Emails -->
            <div class="bg-white shadow rounded-lg">
                <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-900">Recent Outgoing Emails</h2>
                    <a href="{{ route('emails.index', ['type' => 'outgoing']) }}" class="text-blue-600 hover:underline text-sm">View All</a>
                </div>
                <div class="p-6">
                    @if($recentOutgoing->count())
                        <ul class="divide-y divide-gray-200">
                            @foreach($recentOutgoing as $email)
                                <li class="py-3 flex items-start justify-between">
                                    <div>
                                        <div class="font-medium text-gray-900">
                                            <a href="{{ route('emails.show', ['id' => $email->id, 'type' => 'outgoing']) }}" class="hover:underline">
                                                {{ $email->subject }}
                                            </a>
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            To: {{ $email->recipient_email }} | {{ $email->created_at->diffForHumans() }}
                                        </div>
                                        <div class="text-xs mt-1">
                                            <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium {{ $email->status === 'failed' ? 'bg-red-100 text-red-800' : ($email->status === 'delivered' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800') }}">
                                                {{ ucfirst($email->status) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4 flex-shrink-0">
                                        <a href="{{ route('emails.show', ['id' => $email->id, 'type' => 'outgoing']) }}" class="btn btn-sm btn-primary">View</a>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-gray-500 text-center py-8">No recent outgoing emails.</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Mail Merge -->
        <div class="bg-white shadow rounded-lg mt-10">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Quick Mail Merge</h2>
                <p class="text-gray-600 text-sm mt-1">Send a template email to multiple suppliers at once</p>
            </div>
            <form action="{{ route('emails.quick-mail-merge') }}" method="POST" class="p-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="supplier_ids" class="block text-sm font-medium text-gray-700 mb-2">Suppliers</label>
                        <select id="supplier_ids" name="supplier_ids[]" class="form-select w-full" multiple required>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }} ({{ $supplier->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="template_id" class="block text-sm font-medium text-gray-700 mb-2">Email Template</label>
                        <select id="template_id" name="template_id" class="form-select w-full" required>
                            <option value="">Select a template...</option>
                            @foreach($templates as $template)
                                <option value="{{ $template->id }}">{{ $template->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="custom_message" class="block text-sm font-medium text-gray-700 mb-2">Custom Message (optional)</label>
                        <textarea id="custom_message" name="custom_message" rows="2" class="form-input w-full"></textarea>
                    </div>
                </div>
                <div class="mt-6 flex justify-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane mr-2"></i>Send Mail Merge
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
