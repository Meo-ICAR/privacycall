@extends('layouts.app')

@section('title', 'Export Email Provider')

@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Export Email Provider</h2>
                    <a href="{{ route('email-providers.show', $emailProvider->id) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Provider
                    </a>
                </div>
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Provider Information</h3>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600"><strong>Name:</strong> {{ $emailProvider->name ?? 'Provider #' . $emailProvider->id }}</p>
                        <p class="text-sm text-gray-600"><strong>Email:</strong> {{ $emailProvider->email ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-600"><strong>Type:</strong> {{ ucfirst($emailProvider->provider_type ?? 'N/A') }}</p>
                        <p class="text-sm text-gray-600"><strong>Status:</strong> {{ ucfirst($emailProvider->status ?? 'unknown') }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('email-providers.export', $emailProvider->id) }}">
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
                            <span class="ml-2 text-sm text-gray-700">Include provider details</span>
                        </label>
                    </div>
                    <div class="mb-4">
                        <label for="include_compliance" class="flex items-center">
                            <input type="checkbox" name="include_compliance" id="include_compliance" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" checked>
                            <span class="ml-2 text-sm text-gray-700">Include compliance information</span>
                        </label>
                    </div>
                    <div class="mb-4">
                        <label for="include_agreements" class="flex items-center">
                            <input type="checkbox" name="include_agreements" id="include_agreements" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-700">Include data processing agreements</span>
                        </label>
                    </div>
                    <div class="mb-4">
                        <label for="include_settings" class="flex items-center">
                            <input type="checkbox" name="include_settings" id="include_settings" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-700">Include configuration settings</span>
                        </label>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('email-providers.show', $emailProvider->id) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Cancel
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Export Provider
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
