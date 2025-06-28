@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Suppliers</h1>
        <p class="mt-2 text-gray-600">Manage your company's suppliers and vendor relationships</p>
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

    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-medium text-gray-900">All Suppliers</h2>
                <div class="flex space-x-3">
                    <a href="{{ route('supplier-mail-merge.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700">
                        <i class="fas fa-envelope-open-text mr-2"></i>
                        Mail Merge
                    </a>
                    <a href="{{ route('suppliers.export') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                        <i class="fas fa-download mr-2"></i>
                        Export to Excel
                    </a>
                    <a href="{{ route('suppliers.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <i class="fas fa-plus mr-2"></i>
                        Add New Supplier
                    </a>
                </div>
            </div>

            <!-- Import Section -->
            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <h3 class="text-sm font-medium text-gray-900 mb-3">Import Suppliers</h3>
                <form action="{{ route('suppliers.import') }}" method="POST" enctype="multipart/form-data" class="flex items-center space-x-3">
                    @csrf
                    <input type="file" name="file" accept=".xlsx,.xls" required class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <i class="fas fa-upload mr-2"></i>
                        Import from Excel
                    </button>
                </form>
            </div>

            @if($suppliers->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Website</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                @if(auth()->user()->hasRole('superadmin'))
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company</th>
                                @endif
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($suppliers as $supplier)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                @if($supplier->logo_url)
                                                    <img class="h-10 w-10 rounded-full object-cover" src="{{ $supplier->logo_url }}" alt="{{ $supplier->name }}">
                                                @else
                                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                        <i class="fas fa-building text-gray-600"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $supplier->name }}</div>
                                                @if($supplier->address)
                                                    <div class="text-sm text-gray-500 truncate max-w-xs">{{ $supplier->address }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($supplier->supplierType)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $supplier->supplierType->name }}
                                            </span>
                                        @else
                                            <span class="text-sm text-gray-500">No type</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            @if($supplier->email)
                                                <div>{{ $supplier->email }}</div>
                                            @endif
                                            @if($supplier->phone)
                                                <div class="text-gray-500">{{ $supplier->phone }}</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($supplier->website)
                                            <a href="{{ $supplier->website }}" target="_blank" class="text-sm text-blue-600 hover:text-blue-900 truncate block max-w-xs">
                                                {{ $supplier->website }}
                                            </a>
                                        @else
                                            <span class="text-sm text-gray-500">No website</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    </td>
                                    @if(auth()->user()->hasRole('superadmin'))
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ $supplier->company->name ?? '' }}
                                        </td>
                                    @endif
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('suppliers.show', $supplier) }}" class="text-blue-600 hover:text-blue-900" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('suppliers.edit', $supplier) }}" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this supplier?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                                    <i class="fas fa-trash"></i>
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
                <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-3">
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-building text-blue-600 text-2xl"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Total Suppliers</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ $suppliers->count() }}</dd>
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
                                        <dd class="text-lg font-medium text-gray-900">{{ $suppliers->whereNotNull('email')->count() }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-globe text-purple-600 text-2xl"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">With Website</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ $suppliers->whereNotNull('website')->count() }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-building text-gray-400 text-6xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No suppliers found</h3>
                    <p class="text-gray-500 mb-6">Get started by adding your first supplier or importing from Excel.</p>
                    <div class="flex justify-center space-x-3">
                        <a href="{{ route('suppliers.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            <i class="fas fa-plus mr-2"></i>
                            Add First Supplier
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
