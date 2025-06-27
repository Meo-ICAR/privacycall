@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Holdings</h1>
        <p class="mt-2 text-gray-600">Manage company holdings and create companies</p>
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
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-medium text-gray-900">All Holdings</h2>
                @if(auth()->user()->hasRole('superadmin'))
                    <a href="{{ route('holdings.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <i class="fas fa-plus mr-2"></i>
                        Add New Holding
                    </a>
                @endif
            </div>

            @if($holdings->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Companies</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                @if(auth()->user()->hasRole('superadmin'))
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($holdings as $holding)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $holding->name }}</div>
                                        @if($holding->description)
                                            <div class="text-sm text-gray-500">{{ $holding->description }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $holding->companies->count() }} companies</div>
                                        @if($holding->companies->count() > 0)
                                            <div class="text-xs text-gray-500">
                                                @foreach($holding->companies->take(3) as $company)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 mr-1">
                                                        {{ $company->name }}
                                                    </span>
                                                @endforeach
                                                @if($holding->companies->count() > 3)
                                                    <span class="text-gray-400">+{{ $holding->companies->count() - 3 }} more</span>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $holding->created_at->format('Y-m-d') }}
                                    </td>
                                    @if(auth()->user()->hasRole('superadmin'))
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('companies.create', ['holding_id' => $holding->id]) }}" class="text-green-600 hover:text-green-900" title="Create Company">
                                                <i class="fas fa-plus-circle"></i>
                                            </a>
                                            <a href="{{ route('holdings.show', $holding) }}" class="text-blue-600 hover:text-blue-900" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('holdings.edit', $holding) }}" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('holdings.destroy', $holding) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this holding?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-building text-gray-400 text-4xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No holdings found</h3>
                    <p class="text-gray-500 mb-4">Get started by creating your first holding.</p>
                    @if(auth()->user()->hasRole('superadmin'))
                        <a href="{{ route('holdings.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            <i class="fas fa-plus mr-2"></i>
                            Create First Holding
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
