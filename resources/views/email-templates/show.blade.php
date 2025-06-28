@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $emailTemplate->name }}</h1>
            <p class="mt-2 text-gray-600">Email Template Details</p>
        </div>
        <div class="flex space-x-3">
            @if(Auth::user()->hasRole('superadmin'))
                <a href="{{ route('email-templates.edit', $emailTemplate) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Template
                </a>
            @endif
            <a href="{{ route('email-templates.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to List
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Template Information -->
        <div class="lg:col-span-1">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Template Information</h3>

                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Name</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $emailTemplate->name }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Company</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                @if($emailTemplate->company)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $emailTemplate->company->name }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Global Template
                                    </span>
                                @endif
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Category</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    {{ ucfirst($emailTemplate->category) }}
                                </span>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                @if($emailTemplate->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Inactive
                                    </span>
                                @endif
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Created</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $emailTemplate->created_at->format('F j, Y \a\t g:i A') }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $emailTemplate->updated_at->format('F j, Y \a\t g:i A') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Available Variables -->
            <div class="bg-white shadow rounded-lg mt-6">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Available Variables</h3>
                    <div class="space-y-2">
                        @foreach($emailTemplate->getAvailableVariables() as $variable => $description)
                            <div class="flex justify-between items-center">
                                <code class="text-sm bg-gray-100 px-2 py-1 rounded">{{ $variable }}</code>
                                <span class="text-sm text-gray-600">{{ $description }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Email Content -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Email Content</h3>

                    <div class="space-y-6">
                        <!-- Subject -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                            <div class="bg-gray-50 p-3 rounded-md border">
                                <p class="text-sm text-gray-900">{{ $emailTemplate->subject }}</p>
                            </div>
                        </div>

                        <!-- Body -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Body</label>
                            <div class="bg-gray-50 p-4 rounded-md border">
                                <div class="prose prose-sm max-w-none">
                                    {!! nl2br(e($emailTemplate->body)) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preview with Sample Data -->
            <div class="bg-white shadow rounded-lg mt-6">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Preview with Sample Data</h3>

                    <div class="bg-gray-50 p-4 rounded-md border">
                        <div class="mb-4">
                            <strong>Subject:</strong> {{ $emailTemplate->replaceVariables([
                                'supplier_name' => 'Sample Supplier',
                                'supplier_email' => 'supplier@example.com',
                                'company_name' => 'Your Company',
                                'user_name' => 'John Doe',
                                'current_date' => now()->format('Y-m-d'),
                                'custom_message' => 'This is a sample message',
                                'recipient_name' => 'Recipient Name',
                                'recipient_email' => 'recipient@example.com',
                                'template_name' => $emailTemplate->name
                            ]) }}
                        </div>

                        <div class="prose prose-sm max-w-none">
                            {!! nl2br(e($emailTemplate->replaceVariables([
                                'supplier_name' => 'Sample Supplier',
                                'supplier_email' => 'supplier@example.com',
                                'company_name' => 'Your Company',
                                'user_name' => 'John Doe',
                                'current_date' => now()->format('Y-m-d'),
                                'custom_message' => 'This is a sample message',
                                'recipient_name' => 'Recipient Name',
                                'recipient_email' => 'recipient@example.com',
                                'template_name' => $emailTemplate->name
                            ]))) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
