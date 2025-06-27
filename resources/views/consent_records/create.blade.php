@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Create Consent Record</h1>
        <p class="mt-2 text-gray-600">Add a new data subject consent record</p>
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
            <form method="POST" action="{{ route('consent-records.store') }}">
                @csrf

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="consent_type" class="block text-sm font-medium text-gray-700">Consent Type</label>
                        <select id="consent_type" name="consent_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select consent type</option>
                            <option value="marketing" {{ old('consent_type') == 'marketing' ? 'selected' : '' }}>Marketing</option>
                            <option value="analytics" {{ old('consent_type') == 'analytics' ? 'selected' : '' }}>Analytics</option>
                            <option value="necessary" {{ old('consent_type') == 'necessary' ? 'selected' : '' }}>Necessary</option>
                            <option value="preferences" {{ old('consent_type') == 'preferences' ? 'selected' : '' }}>Preferences</option>
                        </select>
                        @error('consent_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="consent_status" class="block text-sm font-medium text-gray-700">Consent Status</label>
                        <select id="consent_status" name="consent_status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select status</option>
                            <option value="granted" {{ old('consent_status') == 'granted' ? 'selected' : '' }}>Granted</option>
                            <option value="denied" {{ old('consent_status') == 'denied' ? 'selected' : '' }}>Denied</option>
                            <option value="withdrawn" {{ old('consent_status') == 'withdrawn' ? 'selected' : '' }}>Withdrawn</option>
                        </select>
                        @error('consent_status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="consent_date" class="block text-sm font-medium text-gray-700">Consent Date</label>
                        <input type="date" id="consent_date" name="consent_date" value="{{ old('consent_date') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('consent_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="expiry_date" class="block text-sm font-medium text-gray-700">Expiry Date (Optional)</label>
                        <input type="date" id="expiry_date" name="expiry_date" value="{{ old('expiry_date') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        @error('expiry_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="notes" class="block text-sm font-medium text-gray-700">Notes (Optional)</label>
                        <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <a href="{{ route('consent-records.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        Create Consent Record
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
