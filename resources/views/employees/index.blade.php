@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
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

                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-gray-900">Employees</h1>
                    <p class="mt-2 text-gray-600">Manage your company's employees and staff information</p>
                </div>

                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-lg font-medium text-gray-900">All Employees</h2>
                            <div class="flex space-x-3">
                                <a href="{{ route('employees.export') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                    <i class="fas fa-download mr-2"></i>
                                    Export to Excel
                                </a>
                                <a href="{{ route('employees.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    <i class="fas fa-plus mr-2"></i>
                                    Add New Employee
                                </a>
                            </div>
                        </div>

                        <!-- Import Section -->
                        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                            <h3 class="text-sm font-medium text-gray-900 mb-3">Import Employees</h3>
                            <form action="{{ route('employees.import') }}" method="POST" enctype="multipart/form-data" class="flex items-center space-x-3">
                                @csrf
                                <input type="file" name="file" accept=".xlsx,.xls" required class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    <i class="fas fa-upload mr-2"></i>
                                    Import from Excel
                                </button>
                            </form>
                        </div>

                        @if($employees->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hire Date</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            @if(auth()->user()->hasRole('superadmin'))
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company</th>
                                            @endif
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($employees as $employee)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0 h-10 w-10">
                                                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                                <span class="text-sm font-medium text-blue-800">
                                                                    {{ strtoupper(substr($employee->first_name, 0, 1) . substr($employee->last_name, 0, 1)) }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="ml-4">
                                                            <div class="text-sm font-medium text-gray-900">{{ $employee->first_name }} {{ $employee->last_name }}</div>
                                                            <div class="text-sm text-gray-500">ID: {{ $employee->id }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900">
                                                        @if($employee->email)
                                                            <div>{{ $employee->email }}</div>
                                                        @endif
                                                        @if($employee->phone)
                                                            <div class="text-gray-500">{{ $employee->phone }}</div>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900">{{ $employee->position }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @if($employee->department)
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                            {{ $employee->department }}
                                                        </span>
                                                    @else
                                                        <span class="text-sm text-gray-500">Not specified</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    @if($employee->hire_date)
                                                        {{ $employee->hire_date->format('Y-m-d') }}
                                                    @else
                                                        <span class="text-gray-400">Not specified</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        Active
                                                    </span>
                                                </td>
                                                @if(auth()->user()->hasRole('superadmin'))
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        {{ $employee->company->name ?? '' }}
                                                    </td>
                                                @endif
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" style="border: 1px solid #e5e7eb; background-color: #f9fafb;">
                                                    <div class="flex space-x-2">
                                                        <a href="{{ route('employees.show', $employee) }}" class="text-blue-600 hover:text-blue-900" title="View" style="display: inline-block; padding: 4px;">
                                                            <i class="fas fa-eye"></i>
                                                            <span class="sr-only">View</span>
                                                        </a>
                                                        <a href="{{ route('employees.edit', $employee) }}" class="text-yellow-600 hover:text-yellow-900" title="Edit" style="display: inline-block; padding: 4px;">
                                                            <i class="fas fa-edit"></i>
                                                            <span class="sr-only">Edit</span>
                                                        </a>
                                                        <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this employee?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-600 hover:text-red-900" title="Delete" style="display: inline-block; padding: 4px; background: none; border: none; cursor: pointer;">
                                                                <i class="fas fa-trash"></i>
                                                                <span class="sr-only">Delete</span>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Statistics -->
                            <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-4">
                                <div class="bg-white overflow-hidden shadow rounded-lg">
                                    <div class="p-5">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-users text-blue-600 text-2xl"></i>
                                            </div>
                                            <div class="ml-5 w-0 flex-1">
                                                <dl>
                                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Employees</dt>
                                                    <dd class="text-lg font-medium text-gray-900">{{ $employees->count() }}</dd>
                                                </dl>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-white overflow-hidden shadow rounded-lg">
                                    <div class="p-5">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-envelope text-green-600 text-2xl"></i>
                                            </div>
                                            <div class="ml-5 w-0 flex-1">
                                                <dl>
                                                    <dt class="text-sm font-medium text-gray-500 truncate">With Email</dt>
                                                    <dd class="text-lg font-medium text-gray-900">{{ $employees->whereNotNull('email')->count() }}</dd>
                                                </dl>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-white overflow-hidden shadow rounded-lg">
                                    <div class="p-5">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-phone text-purple-600 text-2xl"></i>
                                            </div>
                                            <div class="ml-5 w-0 flex-1">
                                                <dl>
                                                    <dt class="text-sm font-medium text-gray-500 truncate">With Phone</dt>
                                                    <dd class="text-lg font-medium text-gray-900">{{ $employees->whereNotNull('phone')->count() }}</dd>
                                                </dl>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-white overflow-hidden shadow rounded-lg">
                                    <div class="p-5">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-calendar text-orange-600 text-2xl"></i>
                                            </div>
                                            <div class="ml-5 w-0 flex-1">
                                                <dl>
                                                    <dt class="text-sm font-medium text-gray-500 truncate">This Year</dt>
                                                    <dd class="text-lg font-medium text-gray-900">{{ $employees->where('hire_date', '>=', now()->startOfYear())->count() }}</dd>
                                                </dl>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-12">
                                <i class="fas fa-users text-gray-400 text-6xl mb-4"></i>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">No employees found</h3>
                                <p class="text-gray-500 mb-6">Get started by adding your first employee or importing from Excel.</p>
                                <div class="flex justify-center space-x-3">
                                    <a href="{{ route('employees.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                        <i class="fas fa-plus mr-2"></i>
                                        Add First Employee
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
