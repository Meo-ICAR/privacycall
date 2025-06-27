@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <div class="flex items-center">
            <a href="{{ route('holdings.index') }}" class="text-blue-600 hover:text-blue-800 mr-4">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Holding Details</h1>
                <p class="mt-2 text-gray-600">View and manage holding information</p>
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

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Basic Information -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                <div class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Name</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $holding->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Description</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $holding->description ?? 'No description provided' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Created</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $holding->created_at->format('Y-m-d H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $holding->updated_at->format('Y-m-d H:i') }}</dd>
                    </div>
                </div>
            </div>
        </div>

        <!-- Companies in this Holding -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Companies in this Holding</h3>
                    @if(auth()->user() && auth()->user()->hasRole('superadmin'))
                        <a href="{{ route('companies.create', ['holding_id' => $holding->id]) }}" class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                            <i class="fas fa-plus mr-1"></i>
                            Add Company
                        </a>
                    @endif
                </div>

                @if($holding->companies->count() > 0)
                    <div class="space-y-3">
                        @foreach($holding->companies as $company)
                            <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        @if($company->logo_url)
                                            <img class="h-8 w-8 rounded-full object-cover" src="{{ $company->logo_url }}" alt="{{ $company->name }}">
                                        @else
                                            <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                                <i class="fas fa-building text-gray-600 text-sm"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-3">
                                        <a href="{{ route('companies.show', $company) }}" class="text-sm font-medium text-blue-600 hover:text-blue-900">{{ $company->name }}</a>
                                        <div class="flex items-center mt-1">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ ucfirst($company->company_type ?? 'Unknown') }}
                                            </span>
                                            @if($company->is_active)
                                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                    Active
                                                </span>
                                            @else
                                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                    Inactive
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('companies.show', $company) }}" class="text-blue-600 hover:text-blue-900" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(auth()->user() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin')))
                                        <a href="{{ route('companies.edit', $company) }}" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-building text-gray-400 text-4xl mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No companies found</h3>
                        <p class="text-gray-500 mb-4">This holding doesn't have any companies yet.</p>
                        @if(auth()->user() && auth()->user()->hasRole('superadmin'))
                            <a href="{{ route('companies.create', ['holding_id' => $holding->id]) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                <i class="fas fa-plus mr-2"></i>
                                Create First Company
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="mt-6 bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Actions</h3>
            <div class="flex space-x-3">
                @if(auth()->user() && auth()->user()->hasRole('superadmin'))
                    <a href="{{ route('holdings.edit', $holding) }}" class="inline-flex items-center px-4 py-2 border border-yellow-300 text-sm font-medium rounded-md text-yellow-700 bg-white hover:bg-yellow-50">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Holding
                    </a>
                    <form action="{{ route('holdings.destroy', $holding) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this holding?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50">
                            <i class="fas fa-trash mr-2"></i>
                            Delete Holding
                        </button>
                    </form>
                @endif
                <a href="{{ route('holdings.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to List
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
