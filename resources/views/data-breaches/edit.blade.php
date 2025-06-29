@extends('layouts.app')

@section('title', 'Edit Data Breach')

@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Edit Data Breach</h2>
                <form method="POST" action="{{ route('data-breaches.update', $dataBreach->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" id="description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>{{ old('description', $dataBreach->description) }}</textarea>
                    </div>
                    <div class="mb-4">
                        <label for="severity" class="block text-sm font-medium text-gray-700">Severity</label>
                        <select name="severity" id="severity" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                            <option value="low" {{ old('severity', $dataBreach->severity) === 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ old('severity', $dataBreach->severity) === 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ old('severity', $dataBreach->severity) === 'high' ? 'selected' : '' }}>High</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="discovered_at" class="block text-sm font-medium text-gray-700">Discovered At</label>
                        <input type="datetime-local" name="discovered_at" id="discovered_at" value="{{ old('discovered_at', $dataBreach->discovered_at ? $dataBreach->discovered_at->format('Y-m-d\TH:i') : '') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
