@extends('layouts.app')

@section('title', 'Export DPIA')

@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Export DPIA Assessment</h2>
                    <a href="{{ route('data-protection-i-as.show', $dpia->id) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Assessment
                    </a>
                </div>
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Assessment Information</h3>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600"><strong>Title:</strong> {{ $dpia->title ?? 'DPIA #' . $dpia->id }}</p>
                        <p class="text-sm text-gray-600"><strong>Processing Activity:</strong> {{ Str::limit($dpia->processing_activity ?? 'N/A', 100) }}</p>
                        <p class="text-sm text-gray-600"><strong>Risk Level:</strong> {{ ucfirst($dpia->risk_level ?? 'unknown') }}</p>
                        <p class="text-sm text-gray-600"><strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $dpia->status ?? 'unknown')) }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('data-protection-i-as.export', $dpia->id) }}">
                    @csrf
                    <div class="mb-4">
                        <label for="format" class="block text-sm font-medium text-gray-700">Export Format</label>
                        <select name="format" id="format" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                            <option value="pdf">PDF Document</option>
                            <option value="csv">CSV File</option>
                            <option value="json">JSON Data</option>
                            <option value="xml">XML Document</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="include_details" class="flex items-center">
                            <input type="checkbox" name="include_details" id="include_details" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" checked>
                            <span class="ml-2 text-sm text-gray-700">Include detailed assessment information</span>
                        </label>
                    </div>
                    <div class="mb-4">
                        <label for="include_risks" class="flex items-center">
                            <input type="checkbox" name="include_risks" id="include_risks" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" checked>
                            <span class="ml-2 text-sm text-gray-700">Include risk analysis</span>
                        </label>
                    </div>
                    <div class="mb-4">
                        <label for="include_recommendations" class="flex items-center">
                            <input type="checkbox" name="include_recommendations" id="include_recommendations" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-700">Include recommendations</span>
                        </label>
                    </div>
                    <div class="mb-4">
                        <label for="include_mitigation" class="flex items-center">
                            <input type="checkbox" name="include_mitigation" id="include_mitigation" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-700">Include mitigation measures</span>
                        </label>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('data-protection-i-as.show', $dpia->id) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Cancel
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Export DPIA
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
