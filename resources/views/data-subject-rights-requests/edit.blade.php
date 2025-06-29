@extends('layouts.app')

@section('title', 'Edit Data Subject Rights Request')

@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Edit Data Subject Rights Request</h2>
                <form method="POST" action="{{ route('data-subject-rights-requests.update', $dataSubjectRightsRequest->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label for="data_subject_name" class="block text-sm font-medium text-gray-700">Data Subject Name</label>
                        <input type="text" name="data_subject_name" id="data_subject_name" value="{{ old('data_subject_name', $dataSubjectRightsRequest->data_subject_name) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                    </div>
                    <div class="mb-4">
                        <label for="data_subject_email" class="block text-sm font-medium text-gray-700">Data Subject Email</label>
                        <input type="email" name="data_subject_email" id="data_subject_email" value="{{ old('data_subject_email', $dataSubjectRightsRequest->data_subject_email) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                    </div>
                    <div class="mb-4">
                        <label for="right_type" class="block text-sm font-medium text-gray-700">Right Type</label>
                        <select name="right_type" id="right_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                            <option value="">Select a right type</option>
                            <option value="access" {{ old('right_type', $dataSubjectRightsRequest->right_type) === 'access' ? 'selected' : '' }}>Right of Access</option>
                            <option value="rectification" {{ old('right_type', $dataSubjectRightsRequest->right_type) === 'rectification' ? 'selected' : '' }}>Right of Rectification</option>
                            <option value="erasure" {{ old('right_type', $dataSubjectRightsRequest->right_type) === 'erasure' ? 'selected' : '' }}>Right of Erasure</option>
                            <option value="portability" {{ old('right_type', $dataSubjectRightsRequest->right_type) === 'portability' ? 'selected' : '' }}>Right of Data Portability</option>
                            <option value="restriction" {{ old('right_type', $dataSubjectRightsRequest->right_type) === 'restriction' ? 'selected' : '' }}>Right of Restriction</option>
                            <option value="objection" {{ old('right_type', $dataSubjectRightsRequest->right_type) === 'objection' ? 'selected' : '' }}>Right to Object</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" id="description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ old('description', $dataSubjectRightsRequest->description) }}</textarea>
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
