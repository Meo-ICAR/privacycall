@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route('customers.index') }}" class="text-blue-600 hover:text-blue-800 mr-4">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Customer Details</h1>
                    <p class="mt-2 text-sm text-gray-600">View and manage customer information</p>
                </div>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('customers.edit', $customer) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Customer
                </a>
                <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this customer?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                        <i class="fas fa-trash mr-2"></i>
                        Delete Customer
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Customer Summary Card -->
        <div class="lg:col-span-1">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="text-center">
                        <div class="mx-auto h-24 w-24 rounded-full bg-blue-100 flex items-center justify-center mb-4">
                            <i class="fas fa-user text-blue-600 text-3xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900">{{ $customer->first_name }} {{ $customer->last_name }}</h3>
                        <p class="text-sm text-gray-500">Customer #{{ $customer->customer_number ?? 'N/A' }}</p>
                        <div class="mt-2">
                            @if($customer->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-circle mr-1 text-xs"></i>
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-circle mr-1 text-xs"></i>
                                    Inactive
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="mt-6 border-t border-gray-200 pt-6">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Customer Type</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $customer->customerType->name ?? 'Not specified' }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Created</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $customer->created_at->format('M d, Y') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Details -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">Customer Information</h3>
                    
                    <!-- Personal Information -->
                    <div class="border-b border-gray-200 pb-6 mb-6">
                        <h4 class="text-md font-medium text-gray-900 mb-4">Personal Information</h4>
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Full Name</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $customer->first_name }} {{ $customer->last_name }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Email</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $customer->email ?? 'Not provided' }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Phone</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $customer->phone ?? 'Not provided' }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Date of Birth</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $customer->date_of_birth ? $customer->date_of_birth->format('M d, Y') : 'Not provided' }}</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Company Information -->
                    <div class="border-b border-gray-200 pb-6 mb-6">
                        <h4 class="text-md font-medium text-gray-900 mb-4">Company Information</h4>
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Company</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if($customer->company)
                                        <a href="{{ route('companies.show', $customer->company) }}" class="text-blue-600 hover:text-blue-800">{{ $customer->company->name }}</a>
                                    @else
                                        <span class="text-gray-400">Unknown</span>
                                    @endif
                                </dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Customer Type</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $customer->customerType->name ?? 'Not specified' }}</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Address Information -->
                    @if($customer->address_line_1 || $customer->city || $customer->state || $customer->postal_code || $customer->country)
                        <div class="border-b border-gray-200 pb-6 mb-6">
                            <h4 class="text-md font-medium text-gray-900 mb-4">Address Information</h4>
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                @if($customer->address_line_1)
                                    <div class="sm:col-span-2">
                                        <dt class="text-sm font-medium text-gray-500">Address</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            {{ $customer->address_line_1 }}
                                            @if($customer->address_line_2)
                                                <br>{{ $customer->address_line_2 }}
                                            @endif
                                        </dd>
                                    </div>
                                @endif
                                @if($customer->city)
                                    <div class="sm:col-span-1">
                                        <dt class="text-sm font-medium text-gray-500">City</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $customer->city }}</dd>
                                    </div>
                                @endif
                                @if($customer->state)
                                    <div class="sm:col-span-1">
                                        <dt class="text-sm font-medium text-gray-500">State/Province</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $customer->state }}</dd>
                                    </div>
                                @endif
                                @if($customer->postal_code)
                                    <div class="sm:col-span-1">
                                        <dt class="text-sm font-medium text-gray-500">Postal Code</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $customer->postal_code }}</dd>
                                    </div>
                                @endif
                                @if($customer->country)
                                    <div class="sm:col-span-1">
                                        <dt class="text-sm font-medium text-gray-500">Country</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $customer->country }}</dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    @endif

                    <!-- Additional Information -->
                    @if($customer->notes)
                        <div class="border-b border-gray-200 pb-6 mb-6">
                            <h4 class="text-md font-medium text-gray-900 mb-4">Notes</h4>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-sm text-gray-900">{{ $customer->notes }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- System Information -->
                    <div>
                        <h4 class="text-md font-medium text-gray-900 mb-4">System Information</h4>
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Created</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $customer->created_at->format('M d, Y \a\t g:i A') }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $customer->updated_at->format('M d, Y \a\t g:i A') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
