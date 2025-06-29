@extends('layouts.app')

@section('title', 'Export GDPR Register')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Export GDPR Processing Register</h2>
                    <a href="{{ route('gdpr.register.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Register
                    </a>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Export Options -->
                    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                        <div class="px-4 py-5 sm:px-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Export Options</h3>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500">Choose the format and scope for your export</p>
                        </div>
                        <div class="border-t border-gray-200">
                            <form method="POST" action="{{ route('gdpr.register.export') }}" class="p-6 space-y-6">
                                @csrf

                                <!-- Export Format -->
                                <div>
                                    <label for="format" class="block text-sm font-medium text-gray-700">Export Format</label>
                                    <select name="format" id="format" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        <option value="pdf">PDF Document</option>
                                        <option value="excel">Excel Spreadsheet (.xlsx)</option>
                                        <option value="csv">CSV File (.csv)</option>
                                        <option value="json">JSON Data (.json)</option>
                                    </select>
                                </div>

                                <!-- Export Scope -->
                                <div>
                                    <label for="scope" class="block text-sm font-medium text-gray-700">Export Scope</label>
                                    <select name="scope" id="scope" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        <option value="all">All Processing Activities</option>
                                        <option value="active">Active Activities Only</option>
                                        <option value="inactive">Inactive Activities Only</option>
                                        <option value="pending">Pending Review Activities</option>
                                    </select>
                                </div>

                                <!-- Date Range -->
                                <div>
                                    <label for="date_range" class="block text-sm font-medium text-gray-700">Date Range</label>
                                    <select name="date_range" id="date_range" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        <option value="all">All Time</option>
                                        <option value="last_30_days">Last 30 Days</option>
                                        <option value="last_90_days">Last 90 Days</option>
                                        <option value="last_year">Last Year</option>
                                        <option value="custom">Custom Range</option>
                                    </select>
                                </div>

                                <!-- Custom Date Range (hidden by default) -->
                                <div id="custom_date_range" class="hidden space-y-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                                            <input type="date" name="start_date" id="start_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        </div>
                                        <div>
                                            <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                                            <input type="date" name="end_date" id="end_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        </div>
                                    </div>
                                </div>

                                <!-- Include Details -->
                                <div>
                                    <fieldset>
                                        <legend class="text-sm font-medium text-gray-700">Include Details</legend>
                                        <div class="mt-4 space-y-4">
                                            <div class="flex items-start">
                                                <div class="flex items-center h-5">
                                                    <input id="include_descriptions" name="include_descriptions" type="checkbox" value="1" checked class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                                </div>
                                                <div class="ml-3 text-sm">
                                                    <label for="include_descriptions" class="font-medium text-gray-700">Processing Descriptions</label>
                                                    <p class="text-gray-500">Include detailed descriptions of processing activities</p>
                                                </div>
                                            </div>
                                            <div class="flex items-start">
                                                <div class="flex items-center h-5">
                                                    <input id="include_legal_basis" name="include_legal_basis" type="checkbox" value="1" checked class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                                </div>
                                                <div class="ml-3 text-sm">
                                                    <label for="include_legal_basis" class="font-medium text-gray-700">Legal Basis</label>
                                                    <p class="text-gray-500">Include legal basis for processing</p>
                                                </div>
                                            </div>
                                            <div class="flex items-start">
                                                <div class="flex items-center h-5">
                                                    <input id="include_data_categories" name="include_data_categories" type="checkbox" value="1" checked class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                                </div>
                                                <div class="ml-3 text-sm">
                                                    <label for="include_data_categories" class="font-medium text-gray-700">Data Categories</label>
                                                    <p class="text-gray-500">Include categories of personal data processed</p>
                                                </div>
                                            </div>
                                            <div class="flex items-start">
                                                <div class="flex items-center h-5">
                                                    <input id="include_recipients" name="include_recipients" type="checkbox" value="1" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                                </div>
                                                <div class="ml-3 text-sm">
                                                    <label for="include_recipients" class="font-medium text-gray-700">Data Recipients</label>
                                                    <p class="text-gray-500">Include information about data recipients</p>
                                                </div>
                                            </div>
                                            <div class="flex items-start">
                                                <div class="flex items-center h-5">
                                                    <input id="include_retention" name="include_retention" type="checkbox" value="1" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                                </div>
                                                <div class="ml-3 text-sm">
                                                    <label for="include_retention" class="font-medium text-gray-700">Retention Periods</label>
                                                    <p class="text-gray-500">Include data retention periods</p>
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>

                                <!-- Export Button -->
                                <div class="pt-4">
                                    <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-3 bg-green-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Generate Export
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Export Preview -->
                    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                        <div class="px-4 py-5 sm:px-6">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Export Preview</h3>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500">Preview of what will be included in your export</p>
                        </div>
                        <div class="border-t border-gray-200">
                            <div class="p-6">
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-700">Total Activities:</span>
                                        <span class="text-sm text-gray-900">{{ $totalActivities ?? 0 }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-700">Active Activities:</span>
                                        <span class="text-sm text-gray-900">{{ $activeActivities ?? 0 }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-700">Inactive Activities:</span>
                                        <span class="text-sm text-gray-900">{{ $inactiveActivities ?? 0 }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-700">Pending Review:</span>
                                        <span class="text-sm text-gray-900">{{ $pendingActivities ?? 0 }}</span>
                                    </div>
                                </div>

                                <div class="mt-6 pt-6 border-t border-gray-200">
                                    <h4 class="text-sm font-medium text-gray-700 mb-3">Recent Export History</h4>
                                    <div class="space-y-3">
                                        @forelse($recentExports ?? [] as $export)
                                        <div class="flex items-center justify-between text-sm">
                                            <div>
                                                <span class="font-medium text-gray-900">{{ $export->format ?? 'PDF' }}</span>
                                                <span class="text-gray-500"> - {{ $export->created_at ? $export->created_at->diffForHumans() : 'Recently' }}</span>
                                            </div>
                                            <a href="{{ $export->download_url ?? '#' }}" class="text-blue-600 hover:text-blue-900">Download</a>
                                        </div>
                                        @empty
                                        <p class="text-sm text-gray-500">No recent exports</p>
                                        @endforelse
                                    </div>
                                </div>

                                <div class="mt-6 pt-6 border-t border-gray-200">
                                    <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <h3 class="text-sm font-medium text-blue-800">Export Information</h3>
                                                <div class="mt-2 text-sm text-blue-700">
                                                    <ul class="list-disc pl-5 space-y-1">
                                                        <li>Exports are generated asynchronously and may take a few minutes</li>
                                                        <li>You will receive an email notification when ready</li>
                                                        <li>Exports are automatically deleted after 30 days</li>
                                                        <li>All exports include a timestamp and version information</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateRangeSelect = document.getElementById('date_range');
    const customDateRange = document.getElementById('custom_date_range');

    dateRangeSelect.addEventListener('change', function() {
        if (this.value === 'custom') {
            customDateRange.classList.remove('hidden');
        } else {
            customDateRange.classList.add('hidden');
        }
    });
});
</script>
@endsection
