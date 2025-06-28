@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Companies</h1>
                <p class="mt-2 text-gray-600">Manage your company relationships and GDPR compliance</p>
            </div>
            <a href="/companies/create" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i>
                Add Company
            </a>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Companies List -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Company Directory</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">All companies in your system</p>
        </div>
        <div class="border-t border-gray-200">
            <div class="px-4 py-5 sm:p-6">
                @if($companies->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Holding</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($companies as $company)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $company->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $company->email }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ ucfirst($company->company_type) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $company->holding ? $company->holding->name : '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="{{ route('companies.show', $company->id) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-12">
                        <i class="fas fa-building text-gray-400 text-4xl mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No companies yet</h3>
                        <p class="text-gray-500 mb-4">Get started by adding your first company to the system.</p>
                        <a href="/companies/create" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            <i class="fas fa-plus mr-2"></i>
                            Add Your First Company
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- API Information -->
    <div class="mt-8 bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">API Access</h3>
            <div class="bg-gray-50 rounded-md p-4">
                <p class="text-sm text-gray-600 mb-2">Manage companies via API:</p>
                <div class="space-y-2 text-sm">
                    <div class="flex items-center">
                        <span class="font-mono bg-gray-200 px-2 py-1 rounded">GET</span>
                        <span class="ml-2 font-mono text-gray-700">/api/v1/companies</span>
                        <span class="ml-2 text-gray-500">- List all companies</span>
                    </div>
                    <div class="flex items-center">
                        <span class="font-mono bg-gray-200 px-2 py-1 rounded">POST</span>
                        <span class="ml-2 font-mono text-gray-700">/api/v1/companies</span>
                        <span class="ml-2 text-gray-500">- Create new company</span>
                    </div>
                    <div class="flex items-center">
                        <span class="font-mono bg-gray-200 px-2 py-1 rounded">GET</span>
                        <span class="ml-2 font-mono text-gray-700">/api/v1/companies/{id}</span>
                        <span class="ml-2 text-gray-500">- Get company details</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
