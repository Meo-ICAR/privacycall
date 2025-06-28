@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('data-processing-activities.index') }}" class="text-blue-600 hover:text-blue-800 mr-4">
            <i class="fas fa-arrow-left"></i> Back to Activities
        </a>
        <h1 class="text-3xl font-bold text-gray-900 mt-4">Data Processing Activity</h1>
        <p class="text-gray-600 mt-2">View activity details and information</p>
    </div>

    <!-- Activity Details -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                @if(auth()->user()->hasRole('superadmin'))
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Company</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $dataProcessingActivity->company->name ?? 'Unknown' }}</dd>
                    </div>
                @endif

                <div>
                    <dt class="text-sm font-medium text-gray-500">Activity Name</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $dataProcessingActivity->activity_name }}</dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd class="mt-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $dataProcessingActivity->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $dataProcessingActivity->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </dd>
                </div>

                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Processing Purpose</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $dataProcessingActivity->processing_purpose }}</dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Legal Basis</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $dataProcessingActivity->legal_basis }}</dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Retention Period</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $dataProcessingActivity->retention_period }}</dd>
                </div>

                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Data Categories</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $dataProcessingActivity->data_categories }}</dd>
                </div>

                @if($dataProcessingActivity->data_subjects)
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Data Subjects</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $dataProcessingActivity->data_subjects }}</dd>
                    </div>
                @endif

                @if($dataProcessingActivity->data_recipients)
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Data Recipients</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $dataProcessingActivity->data_recipients }}</dd>
                    </div>
                @endif

                @if($dataProcessingActivity->security_measures)
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Security Measures</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $dataProcessingActivity->security_measures }}</dd>
                    </div>
                @endif

                @if($dataProcessingActivity->notes)
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Notes</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $dataProcessingActivity->notes }}</dd>
                    </div>
                @endif
            </dl>

            <!-- Actions -->
            <div class="mt-8 flex space-x-3">
                @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin'))
                    <a href="{{ route('data-processing-activities.edit', $dataProcessingActivity) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Activity
                    </a>
                @endif
                <a href="{{ route('data-processing-activities.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to List
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
