<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Data Removal Requests Dashboard') }}
            </h2>
            <a href="{{ route('data-removal-requests.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                {{ __('New Request') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-500">Total Requests</div>
                                <div class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-500">Pending</div>
                                <div class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-500">Overdue</div>
                                <div class="text-2xl font-bold text-red-600">{{ $stats['overdue'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-500">Completed</div>
                                <div class="text-2xl font-bold text-green-600">{{ $stats['completed'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Urgent Requests -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Urgent Requests</h3>
                        @if($urgentRequests->count() > 0)
                            <div class="space-y-3">
                                @foreach($urgentRequests as $request)
                                    <div class="border-l-4 border-red-500 pl-4 py-2">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">
                                                    <a href="{{ route('data-removal-requests.show', $request) }}" class="hover:text-blue-600">
                                                        {{ $request->request_number }}
                                                    </a>
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    @if($request->customer)
                                                        {{ $request->customer->full_name }}
                                                    @elseif($request->mandator)
                                                        {{ $request->mandator->full_name }}
                                                    @endif
                                                </div>
                                                <div class="text-xs text-gray-400">
                                                    Due: {{ $request->due_date?->format('M d, Y') ?? 'No due date' }}
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                                    bg-{{ $request->priority_color }}-100 text-{{ $request->priority_color }}-800">
                                                    {{ ucfirst($request->priority) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('data-removal-requests.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    View all requests →
                                </a>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No urgent requests</h3>
                                <p class="mt-1 text-sm text-gray-500">All requests are up to date.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Activity</h3>
                        @if($recentRequests->count() > 0)
                            <div class="space-y-3">
                                @foreach($recentRequests as $request)
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0">
                                            <div class="w-2 h-2 bg-{{ $request->status_color }}-500 rounded-full"></div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-sm font-medium text-gray-900">
                                                <a href="{{ route('data-removal-requests.show', $request) }}" class="hover:text-blue-600">
                                                    {{ $request->request_number }}
                                                </a>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-xs text-gray-400">
                                                {{ $request->request_date->format('M d') }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('data-removal-requests.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    View all requests →
                                </a>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No recent activity</h3>
                                <p class="mt-1 text-sm text-gray-500">No data removal requests have been created yet.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <a href="{{ route('data-removal-requests.create') }}"
                           class="flex items-center p-4 border border-gray-300 rounded-lg hover:bg-gray-50">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">Create New Request</div>
                                <div class="text-sm text-gray-500">Start a new data removal request</div>
                            </div>
                        </a>

                        <a href="{{ route('data-removal-requests.index', ['status' => 'pending']) }}"
                           class="flex items-center p-4 border border-gray-300 rounded-lg hover:bg-gray-50">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">Review Pending</div>
                                <div class="text-sm text-gray-500">Review pending requests</div>
                            </div>
                        </a>

                        <a href="{{ route('data-removal-requests.index', ['status' => 'overdue']) }}"
                           class="flex items-center p-4 border border-gray-300 rounded-lg hover:bg-gray-50">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">Handle Overdue</div>
                                <div class="text-sm text-gray-500">Address overdue requests</div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
