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
    <div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <a href="{{ route('representatives.index') }}" class="text-blue-600 hover:text-blue-800 mr-4">
                        <i class="fas fa-arrow-left"></i> Back to Representatives
                    </a>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('representatives.edit', $representative) }}"
                       class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md">
                        <i class="fas fa-edit mr-2"></i>Edit
                    </a>
                    <form action="{{ route('representatives.destroy', $representative) }}"
                          method="POST"
                          class="inline"
                          onsubmit="return confirm('Are you sure you want to delete this representative?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md">
                            <i class="fas fa-trash mr-2"></i>Delete
                        </button>
                    </form>
                </div>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mt-2">Representative Details</h1>
            <p class="text-gray-600 mt-2">View detailed information about this representative</p>
        </div>

        <!-- Representative Information -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <!-- Header with Avatar -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-8">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-20 w-20 rounded-full bg-white bg-opacity-20 flex items-center justify-center">
                            <span class="text-white text-2xl font-bold">
                                {{ strtoupper(substr($representative->first_name, 0, 1) . substr($representative->last_name, 0, 1)) }}
                            </span>
                        </div>
                    </div>
                    <div class="ml-6">
                        <h2 class="text-2xl font-bold text-white">
                            {{ $representative->first_name }} {{ $representative->last_name }}
                        </h2>
                        <p class="text-blue-100">{{ $representative->position ?? 'No position specified' }}</p>
                        <p class="text-blue-100">{{ $representative->company->name }}</p>
                    </div>
                    <div class="ml-auto">
                        @if($representative->is_active)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i>
                                Active
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                <i class="fas fa-times-circle mr-1"></i>
                                Inactive
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="px-6 py-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Basic Information -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Full Name</dt>
                                <dd class="text-sm text-gray-900">{{ $representative->first_name }} {{ $representative->last_name }}</dd>
                            </div>
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
                                <dd class="text-sm text-gray-900 capitalize">{{ $representative->preferred_contact_method }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Email Notifications</dt>
                                <dd class="text-sm text-gray-900">
                                    @if($representative->email_notifications)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check mr-1"></i>Enabled
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            <i class="fas fa-times mr-1"></i>Disabled
                                        </span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">SMS Notifications</dt>
                                <dd class="text-sm text-gray-900">
                                    @if($representative->sms_notifications)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check mr-1"></i>Enabled
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            <i class="fas fa-times mr-1"></i>Disabled
                                        </span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Disclosure Subscriptions -->
                <div class="mt-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Disclosure Subscriptions</h3>
                    @if($representative->disclosure_subscriptions && count($representative->disclosure_subscriptions) > 0)
                        <div class="flex flex-wrap gap-2">
                            @foreach($representative->disclosure_subscriptions as $subscription)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-bell mr-2"></i>
                                    {{ ucwords(str_replace('_', ' ', $subscription)) }}
                                </span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500">No disclosure subscriptions configured</p>
                    @endif
                </div>

                <!-- Disclosure Summary -->
                <div class="mt-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Disclosure Summary</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="text-2xl font-bold text-blue-600">
                                {{ count($representative->disclosure_subscriptions ?? []) }}
                            </div>
                            <div class="text-sm text-gray-600">Total Subscriptions</div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="text-2xl font-bold text-green-600">
                                {{ $representative->last_disclosure_date ? 'Yes' : 'No' }}
                            </div>
                            <div class="text-sm text-gray-600">Last Disclosure Sent</div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="text-2xl font-bold text-purple-600">
                                {{ $representative->last_disclosure_date ? $representative->last_disclosure_date->diffInDays(now()) : 'N/A' }}
                            </div>
                            <div class="text-sm text-gray-600">Days Since Last Disclosure</div>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                @if($representative->notes)
                    <div class="mt-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Notes</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-gray-700">{{ $representative->notes }}</p>
                        </div>
                    </div>
                @endif

                <!-- Timestamps -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Timestamps</h3>
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <dt class="text-gray-500">Created</dt>
                            <dd class="text-gray-900">{{ $representative->created_at->format('M j, Y \a\t g:i A') }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Last Updated</dt>
                            <dd class="text-gray-900">{{ $representative->updated_at->format('M j, Y \a\t g:i A') }}</dd>
                        </div>
                        @if($representative->last_disclosure_date)
                            <div>
                                <dt class="text-gray-500">Last Disclosure Date</dt>
                                <dd class="text-gray-900">{{ $representative->last_disclosure_date->format('M j, Y \a\t g:i A') }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
