<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Representative Details - PrivacyCall</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <h1 class="text-xl font-bold text-gray-800">
                            <i class="fas fa-shield-alt text-blue-600 mr-2"></i>
                            PrivacyCall
                        </h1>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="/" class="text-gray-600 hover:text-gray-900">Home</a>
                    <a href="/dashboard" class="text-gray-600 hover:text-gray-900">Dashboard</a>
                    <a href="/companies" class="text-gray-600 hover:text-gray-900">Companies</a>
                    <a href="/representatives" class="text-blue-600 font-medium">Representatives</a>
                    <a href="/gdpr" class="text-gray-600 hover:text-gray-900">GDPR</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Representative Details</h1>
                        <p class="text-gray-600 mt-2">{{ $representative->company->name }}</p>
                    </div>
                    <div class="flex space-x-3">
                        @if(Auth::user()->role === 'superadmin')
                            <a href="{{ route('representatives.clone-form', $representative) }}"
                               class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-copy mr-2"></i>Clone
                            </a>
                        @endif
                        <a href="{{ route('representatives.edit', $representative) }}"
                           class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Edit
                        </a>
                        <a href="{{ route('representatives.index') }}"
                           class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                            Back to List
                        </a>
                    </div>
                </div>
            </div>

            <!-- Clone Status Alert -->
            @if($representative->isClone())
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Cloned Representative</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p>This representative was cloned from
                                    <a href="{{ route('representatives.show', $representative->original) }}"
                                       class="font-medium underline hover:text-yellow-600">
                                        {{ $representative->original->full_name }}
                                    </a>
                                    at {{ $representative->original->company->name }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Main Content -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Representative Info -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow">
                        <div class="p-6">
                            <div class="flex items-start space-x-6">
                                <!-- Logo/Avatar -->
                                <div class="flex-shrink-0">
                                    <img src="{{ $representative->logo_url }}"
                                         alt="{{ $representative->full_name }}"
                                         class="w-24 h-24 rounded-full object-cover border-4 border-gray-200">
                                </div>

                                <!-- Basic Info -->
                                <div class="flex-1">
                                    <h2 class="text-2xl font-bold text-gray-900">{{ $representative->full_name }}</h2>
                                    @if($representative->position)
                                        <p class="text-lg text-gray-600">{{ $representative->position }}</p>
                                    @endif
                                    @if($representative->department)
                                        <p class="text-gray-500">{{ $representative->department }}</p>
                                    @endif

                                    <!-- Status Badge -->
                                    <div class="mt-3">
                                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                                            {{ $representative->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $representative->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Contact Information</h3>
                                    <dl class="space-y-3">
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                                            <dd class="text-sm text-gray-900">
                                                <a href="mailto:{{ $representative->email }}" class="text-blue-600 hover:text-blue-800">
                                                    {{ $representative->email }}
                                                </a>
                                            </dd>
                                        </div>
                                        @if($representative->phone)
                                            <div>
                                                <dt class="text-sm font-medium text-gray-500">Phone</dt>
                                                <dd class="text-sm text-gray-900">
                                                    <a href="tel:{{ $representative->phone }}" class="text-blue-600 hover:text-blue-800">
                                                        {{ $representative->phone }}
                                                    </a>
                                                </dd>
                                            </div>
                                        @endif
                                        @if($representative->position)
                                            <div>
                                                <dt class="text-sm font-medium text-gray-500">Position</dt>
                                                <dd class="text-sm text-gray-900">{{ $representative->position }}</dd>
                                            </div>
                                        @endif
                                        @if($representative->department)
                                            <div>
                                                <dt class="text-sm font-medium text-gray-500">Department</dt>
                                                <dd class="text-sm text-gray-900">{{ $representative->department }}</dd>
                                            </div>
                                        @endif
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Company</dt>
                                            <dd class="text-sm text-gray-900">{{ $representative->company->name }}</dd>
                                        </div>
                                    </dl>
                                </div>

                                <!-- Contact Preferences -->
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Contact Preferences</h3>
                                    <dl class="space-y-3">
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Preferred Contact Method</dt>
                                            <dd class="text-sm text-gray-900">{{ ucfirst($representative->preferred_contact_method) }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Email Notifications</dt>
                                            <dd class="text-sm text-gray-900">
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                    {{ $representative->email_notifications ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $representative->email_notifications ? 'Enabled' : 'Disabled' }}
                                                </span>
                                            </dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">SMS Notifications</dt>
                                            <dd class="text-sm text-gray-900">
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                    {{ $representative->sms_notifications ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $representative->sms_notifications ? 'Enabled' : 'Disabled' }}
                                                </span>
                                            </dd>
                                        </div>
                                    </dl>
                                </div>
                            </div>

                            <!-- Notes -->
                            @if($representative->notes)
                                <div class="mt-8">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Notes</h3>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <p class="text-sm text-gray-700">{{ $representative->notes }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Disclosure Subscriptions -->
                    <div class="bg-white rounded-lg shadow">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Disclosure Subscriptions</h3>

                            @if(!empty($representative->disclosure_subscriptions))
                                <div class="space-y-2">
                                    @foreach($representative->disclosure_subscriptions as $subscription)
                                        <div class="flex items-center">
                                            <svg class="h-4 w-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            <span class="text-sm text-gray-700">{{ ucwords(str_replace('_', ' ', $subscription)) }}</span>
                                        </div>
                                    @endforeach
                                </div>

                                @if($representative->last_disclosure_date)
                                    <div class="mt-4 pt-4 border-t border-gray-200">
                                        <p class="text-sm text-gray-500">
                                            Last disclosure: {{ $representative->last_disclosure_date->format('M d, Y H:i') }}
                                        </p>
                                    </div>
                                @endif
                            @else
                                <p class="text-sm text-gray-500">No disclosure subscriptions</p>
                            @endif
                        </div>
                    </div>

                    <!-- Related Representatives -->
                    @if($representative->hasClones() || $representative->isClone())
                        <div class="bg-white rounded-lg shadow">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Related Representatives</h3>

                                @if($representative->isClone())
                                    <div class="mb-4">
                                        <p class="text-sm text-gray-600 mb-2">Original Representative:</p>
                                        <a href="{{ route('representatives.show', $representative->original) }}"
                                           class="block p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                                            <div class="flex items-center">
                                                <img src="{{ $representative->original->logo_url }}"
                                                     alt="{{ $representative->original->full_name }}"
                                                     class="w-8 h-8 rounded-full object-cover mr-3">
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900">{{ $representative->original->full_name }}</p>
                                                    <p class="text-xs text-gray-500">{{ $representative->original->company->name }}</p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endif

                                @if($representative->hasClones())
                                    <div>
                                        <p class="text-sm text-gray-600 mb-2">Clones ({{ $representative->clones->count() }}):</p>
                                        <div class="space-y-2">
                                            @foreach($representative->clones as $clone)
                                                <a href="{{ route('representatives.show', $clone) }}"
                                                   class="block p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                                    <div class="flex items-center">
                                                        <img src="{{ $clone->logo_url }}"
                                                             alt="{{ $clone->full_name }}"
                                                             class="w-8 h-8 rounded-full object-cover mr-3">
                                                        <div>
                                                            <p class="text-sm font-medium text-gray-900">{{ $clone->full_name }}</p>
                                                            <p class="text-xs text-gray-500">{{ $clone->company->name }}</p>
                                                        </div>
                                                    </div>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Quick Actions -->
                    <div class="bg-white rounded-lg shadow">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
                            <div class="space-y-3">
                                <a href="{{ route('representatives.edit', $representative) }}"
                                   class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded">
                                    Edit Representative
                                </a>
                                @if(Auth::user()->role === 'superadmin')
                                    <a href="{{ route('representatives.clone-form', $representative) }}"
                                       class="block w-full text-center bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded">
                                        Clone to Another Company
                                    </a>
                                @endif
                                <a href="{{ route('representatives.index') }}"
                                   class="block w-full text-center bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded">
                                    Back to List
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
