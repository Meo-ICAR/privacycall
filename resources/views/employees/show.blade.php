@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route('employees.index') }}" class="text-blue-600 hover:text-blue-800 mr-4">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Employee Details</h1>
                    <p class="mt-2 text-gray-600">View and manage employee information</p>
                </div>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('employees.edit', $employee) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Employee
                </a>
                <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this employee?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                        <i class="fas fa-trash mr-2"></i>
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

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
        <!-- Employee Profile Card -->
        <div class="lg:col-span-1">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="text-center">
                        <div class="mx-auto h-24 w-24 rounded-full bg-blue-100 flex items-center justify-center mb-4">
                            <span class="text-2xl font-bold text-blue-800">
                                {{ strtoupper(substr($employee->first_name, 0, 1) . substr($employee->last_name, 0, 1)) }}
                            </span>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900">{{ $employee->first_name }} {{ $employee->last_name }}</h3>
                        <p class="text-sm text-gray-500">{{ $employee->position }}</p>
                        <div class="mt-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $employee->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                <i class="fas fa-circle mr-1 text-xs"></i>
                                {{ $employee->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="mt-6 border-t border-gray-200 pt-6">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Employee ID</dt>
                                <dd class="mt-1 text-sm text-gray-900">#{{ $employee->id }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Hire Date</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if($employee->hire_date)
                                        {{ $employee->hire_date->format('M d, Y') }}
                                    @else
                                        <span class="text-gray-400">Not set</span>
                                    @endif
                                </dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Department</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if($employee->department)
                                        {{ $employee->department }}
                                    @else
                                        <span class="text-gray-400">Not specified</span>
                                    @endif
                                </dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Salary</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if($employee->salary)
                                        ${{ number_format($employee->salary, 2) }}
                                    @else
                                        <span class="text-gray-400">Not set</span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Employee Details -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">Employee Information</h3>

                    <!-- Personal Information -->
                    <div class="border-b border-gray-200 pb-6 mb-6">
                        <h4 class="text-md font-medium text-gray-900 mb-4">Personal Information</h4>
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Full Name</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $employee->first_name }} {{ $employee->last_name }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Email Address</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if($employee->email)
                                        <a href="mailto:{{ $employee->email }}" class="text-blue-600 hover:text-blue-800">{{ $employee->email }}</a>
                                    @else
                                        <span class="text-gray-400">Not provided</span>
                                    @endif
                                </dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Phone Number</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if($employee->phone)
                                        <a href="tel:{{ $employee->phone }}" class="text-blue-600 hover:text-blue-800">{{ $employee->phone }}</a>
                                    @else
                                        <span class="text-gray-400">Not provided</span>
                                    @endif
                                </dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Address</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if($employee->address)
                                        {{ $employee->address }}
                                    @else
                                        <span class="text-gray-400">Not provided</span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Employment Information -->
                    <div class="border-b border-gray-200 pb-6 mb-6">
                        <h4 class="text-md font-medium text-gray-900 mb-4">Employment Information</h4>
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Company</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if($employee->company)
                                        <a href="{{ route('companies.show', $employee->company) }}" class="text-blue-600 hover:text-blue-800">{{ $employee->company->name }}</a>
                                    @else
                                        <span class="text-gray-400">Unknown</span>
                                    @endif
                                </dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Position</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $employee->position }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Department</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if($employee->department)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $employee->department }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">Not specified</span>
                                    @endif
                                </dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Hire Date</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if($employee->hire_date)
                                        {{ $employee->hire_date->format('F d, Y') }}
                                        <span class="text-gray-400">({{ $employee->hire_date->diffForHumans() }})</span>
                                    @else
                                        <span class="text-gray-400">Not set</span>
                                    @endif
                                </dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Salary</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if($employee->salary)
                                        ${{ number_format($employee->salary, 2) }}
                                    @else
                                        <span class="text-gray-400">Not set</span>
                                    @endif
                                </dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $employee->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        <i class="fas fa-circle mr-1 text-xs"></i>
                                        {{ $employee->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Additional Information -->
                    @if($employee->notes)
                        <div class="border-b border-gray-200 pb-6 mb-6">
                            <h4 class="text-md font-medium text-gray-900 mb-4">Notes</h4>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-sm text-gray-700">{{ $employee->notes }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- System Information -->
                    <div>
                        <h4 class="text-md font-medium text-gray-900 mb-4">System Information</h4>
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Created</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $employee->created_at->format('M d, Y \a\t g:i A') }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $employee->updated_at->format('M d, Y \a\t g:i A') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
