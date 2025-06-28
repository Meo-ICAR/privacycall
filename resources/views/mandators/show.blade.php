@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('mandators.index') }}" class="text-blue-600 hover:text-blue-800 mr-4">
            <i class="fas fa-arrow-left"></i> Back to Mandators
        </a>
        <div class="flex justify-between items-start mt-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $mandator->full_name }}</h1>
                <p class="text-gray-600 mt-2">{{ $mandator->position }} at {{ $mandator->company->name }}</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('mandators.edit', $mandator) }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
                <form action="{{ route('mandators.destroy', $mandator) }}"
                      method="POST"
                      class="inline"
                      onsubmit="return confirm('Are you sure you want to delete this mandator?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md">
                        <i class="fas fa-trash mr-2"></i>Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Basic Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Full Name</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $mandator->full_name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Email</label>
                        <p class="mt-1 text-sm text-gray-900">
                            <a href="mailto:{{ $mandator->email }}" class="text-blue-600 hover:text-blue-800">
                                {{ $mandator->email }}
                            </a>
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Phone</label>
                        <p class="mt-1 text-sm text-gray-900">
                            @if($mandator->phone)
                                <a href="tel:{{ $mandator->phone }}" class="text-blue-600 hover:text-blue-800">
                                    {{ $mandator->phone }}
                                </a>
                            @else
                                <span class="text-gray-400">Not provided</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Position</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $mandator->position ?? 'Not specified' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Department</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $mandator->department ?? 'Not specified' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Status</label>
                        <p class="mt-1 text-sm text-gray-900">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $mandator->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $mandator->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Contact Preferences -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Contact Preferences</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Email Notifications</label>
                        <p class="mt-1 text-sm text-gray-900">
                            <span class="inline-flex items-center">
                                @if($mandator->email_notifications)
                                    <i class="fas fa-check text-green-600 mr-2"></i>Enabled
                                @else
                                    <i class="fas fa-times text-red-600 mr-2"></i>Disabled
                                @endif
                            </span>
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">SMS Notifications</label>
                        <p class="mt-1 text-sm text-gray-900">
                            <span class="inline-flex items-center">
                                @if($mandator->sms_notifications)
                                    <i class="fas fa-check text-green-600 mr-2"></i>Enabled
                                @else
                                    <i class="fas fa-times text-red-600 mr-2"></i>Disabled
                                @endif
                            </span>
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Preferred Contact Method</label>
                        <p class="mt-1 text-sm text-gray-900 capitalize">{{ $mandator->preferred_contact_method }}</p>
                    </div>
                </div>
            </div>

            <!-- Disclosure Subscriptions -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Disclosure Subscriptions</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($mandator->disclosure_subscriptions as $subscription)
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-600 mr-2"></i>
                            <span class="text-sm text-gray-900">{{ $subscription }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Notes -->
            @if($mandator->notes)
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Notes</h2>
                    <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $mandator->notes }}</p>
                </div>
            @endif

            <!-- Related Mandators -->
            @if($mandator->clones && $mandator->clones->count() > 0)
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Related Mandators</h2>
                    <div class="space-y-3">
                        @foreach($mandator->clones as $clone)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $clone->full_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $clone->company->name }}</p>
                                </div>
                                <a href="{{ route('mandators.show', $clone) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                    View
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Profile Picture -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Profile Picture</h3>
                <div class="flex justify-center">
                    <img src="{{ $mandator->logo_url }}"
                         alt="{{ $mandator->full_name }}"
                         class="w-32 h-32 rounded-full object-cover">
                </div>
            </div>

            <!-- Company Information -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Company</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Company Name</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $mandator->company->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Company Email</label>
                        <p class="mt-1 text-sm text-gray-900">
                            <a href="mailto:{{ $mandator->company->email }}" class="text-blue-600 hover:text-blue-800">
                                {{ $mandator->company->email }}
                            </a>
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Company Phone</label>
                        <p class="mt-1 text-sm text-gray-900">
                            @if($mandator->company->phone)
                                <a href="tel:{{ $mandator->company->phone }}" class="text-blue-600 hover:text-blue-800">
                                    {{ $mandator->company->phone }}
                                </a>
                            @else
                                <span class="text-gray-400">Not provided</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <a href="mailto:{{ $mandator->email }}" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <i class="fas fa-envelope mr-2"></i>Send Email
                    </a>
                    @if($mandator->phone)
                        <a href="tel:{{ $mandator->phone }}" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                            <i class="fas fa-phone mr-2"></i>Call
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
