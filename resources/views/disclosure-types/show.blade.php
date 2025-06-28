@extends('layouts.app')

@section('title', 'Disclosure Type Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center">
                <a href="{{ route('disclosure-types.index') }}" class="text-blue-600 hover:text-blue-800 mr-4">
                    <i class="fas fa-arrow-left"></i> Back to Disclosure Types
                </a>
                <h1 class="text-3xl font-bold text-gray-900">{{ $disclosureType->display_name }}</h1>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('disclosure-types.edit', $disclosureType) }}"
                   class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-edit mr-2"></i> Edit
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Information -->
            <div class="lg:col-span-2">
                <div class="bg-white shadow-md rounded-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Details</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Internal Name</label>
                            <p class="text-sm text-gray-900 font-mono">{{ $disclosureType->name }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Display Name</label>
                            <p class="text-sm text-gray-900">{{ $disclosureType->display_name }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Category</label>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($disclosureType->category === 'compliance') bg-blue-100 text-blue-800
                                @elseif($disclosureType->category === 'security') bg-red-100 text-red-800
                                @elseif($disclosureType->category === 'privacy') bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($disclosureType->category) }}
                            </span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Status</label>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($disclosureType->is_active) bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif">
                                @if($disclosureType->is_active)
                                    <i class="fas fa-check-circle mr-1"></i> Active
                                @else
                                    <i class="fas fa-times-circle mr-1"></i> Inactive
                                @endif
                            </span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Sort Order</label>
                            <p class="text-sm text-gray-900">{{ $disclosureType->sort_order }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Created</label>
                            <p class="text-sm text-gray-900">{{ $disclosureType->created_at->format('M j, Y g:i A') }}</p>
                        </div>
                    </div>

                    @if($disclosureType->description)
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-500 mb-2">Description</label>
                            <p class="text-sm text-gray-900">{{ $disclosureType->description }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Statistics -->
            <div class="lg:col-span-1">
                <div class="bg-white shadow-md rounded-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Statistics</h2>

                    <div class="space-y-4">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-blue-600">{{ $mandators->count() }}</div>
                            <div class="text-sm text-gray-500">Subscribed Mandators</div>
                        </div>

                        <div class="text-center">
                            <div class="text-3xl font-bold text-green-600">{{ $disclosureType->created_at->diffForHumans() }}</div>
                            <div class="text-sm text-gray-500">Created</div>
                        </div>

                        <div class="text-center">
                            <div class="text-3xl font-bold text-purple-600">{{ $disclosureType->updated_at->diffForHumans() }}</div>
                            <div class="text-sm text-gray-500">Last Updated</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subscribers List -->
        <div class="mt-8">
            <div class="bg-white shadow-md rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">Subscribed Mandators</h2>
                </div>

                @if($mandators->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Mandator
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Company
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Email
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($mandators as $mandator)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                @if($mandator->logo_url)
                                                    <img class="h-8 w-8 rounded-full mr-3" src="{{ $mandator->logo_url }}" alt="Logo">
                                                @else
                                                    <div class="h-8 w-8 rounded-full bg-gray-300 mr-3 flex items-center justify-center">
                                                        <i class="fas fa-user text-gray-600"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $mandator->first_name }} {{ $mandator->last_name }}
                                                    </div>
                                                    @if($mandator->position)
                                                        <div class="text-sm text-gray-500">{{ $mandator->position }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $mandator->company->name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $mandator->email }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($mandator->is_active) bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif">
                                                @if($mandator->is_active)
                                                    <i class="fas fa-check-circle mr-1"></i> Active
                                                @else
                                                    <i class="fas fa-times-circle mr-1"></i> Inactive
                                                @endif
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="px-6 py-8 text-center text-gray-500">
                        <i class="fas fa-users text-4xl mb-4 text-gray-300"></i>
                        <p>No mandators are currently subscribed to this disclosure type.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
