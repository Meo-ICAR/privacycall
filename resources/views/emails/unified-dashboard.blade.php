@extends('layouts.app')

@section('title', 'Unified Email Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Superadmin Impersonation Banner -->
        @if(auth()->user()->hasRole('superadmin') && !session('impersonate_original_id') && isset($company))
            @php
                $companyAdmin = \App\Models\User::where('company_id', $company->id)
                    ->whereHas('roles', function ($query) {
                        $query->where('name', 'admin');
                    })
                    ->first();
            @endphp
            @if($companyAdmin)
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-user-secret text-blue-600 mr-3"></i>
                            <div>
                                <h3 class="text-sm font-medium text-blue-900">View as Company Admin</h3>
                                <p class="text-sm text-blue-700">
                                    You can impersonate as <strong>{{ $companyAdmin->name }}</strong> to view emails from {{ $company->name }}'s perspective.
                                </p>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('emails.impersonate', $company) }}" class="ml-4">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-user-secret mr-2"></i>
                                Impersonate as Admin
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        @endif

        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Unified Email Dashboard</h1>
                @if(isset($company))
                    <p class="text-gray-600 mt-2">Manage all incoming and outgoing emails for <span class="font-semibold">{{ $company->name }}</span></p>
                @else
                    <p class="text-gray-600 mt-2">Overview of all emails across all companies</p>
                @endif
            </div>
            <div class="flex space-x-3">
                <button onclick="openSendEmailModal()" class="btn btn-primary">
                    <i class="fas fa-plus mr-2"></i>Send New Email
                </button>
                @if(isset($company))
                    <a href="{{ route('companies.show', $company) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Company
                    </a>
                @else
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                    </a>
                @endif
            </div>
        </div>

        <!-- Company Selection for Superadmin -->
        @if(!isset($company) && isset($companies) && $companies->count() > 0)
            <div class="bg-white shadow rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Select Company to View Specific Emails</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($companies as $companyOption)
                            <a href="{{ route('emails.dashboard', $companyOption) }}"
                               class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                <div class="font-medium text-gray-900">{{ $companyOption->name }}</div>
                                <div class="text-sm text-gray-500">{{ $companyOption->emails_count }} emails</div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

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
                                            @if(!isset($company) && $email->company)
                                                | {{ $email->company->name }}
                                            @endif
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
                                            @if(!isset($company) && $email->company)
                                                | {{ $email->company->name }}
                                            @endif
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
        @if($templates->count() > 0 && $suppliers->count() > 0)
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
                                <option value="{{ $supplier->id }}">
                                    {{ $supplier->name }} ({{ $supplier->email }})
                                    @if(!isset($company) && $supplier->company)
                                        - {{ $supplier->company->name }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="template_id" class="block text-sm font-medium text-gray-700 mb-2">Email Template</label>
                        <select id="template_id" name="template_id" class="form-select w-full" required>
                            <option value="">Select a template...</option>
                            @foreach($templates as $template)
                                <option value="{{ $template->id }}">
                                    {{ $template->name }}
                                    @if(!isset($company) && $template->company)
                                        - {{ $template->company->name }}
                                    @endif
                                </option>
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
        @endif
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

            <form action="{{ route('emails.send') }}" method="POST" enctype="multipart/form-data">
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
