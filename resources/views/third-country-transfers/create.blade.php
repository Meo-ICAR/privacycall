@extends('layouts.app')

@section('title', 'Create Third Country Transfer')

@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Create New Third Country Transfer</h2>
                <form method="POST" action="{{ route('third-country-transfers.store') }}">
                    @csrf
                    <div class="mb-4">
                        <label for="transfer_name" class="block text-sm font-medium text-gray-700">Transfer Name</label>
                        <input type="text" name="transfer_name" id="transfer_name" value="{{ old('transfer_name') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                    </div>
                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" id="description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>{{ old('description') }}</textarea>
                    </div>
                    <div class="mb-4">
                        <label for="destination_country" class="block text-sm font-medium text-gray-700">Destination Country</label>
                        <input type="text" name="destination_country" id="destination_country" value="{{ old('destination_country') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                    </div>
                    <div class="mb-4">
                        <label for="legal_basis" class="block text-sm font-medium text-gray-700">Legal Basis</label>
                        <select name="legal_basis" id="legal_basis" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                            <option value="">Select legal basis</option>
                            <option value="adequacy_decision" {{ old('legal_basis') === 'adequacy_decision' ? 'selected' : '' }}>Adequacy Decision</option>
                            <option value="standard_contractual_clauses" {{ old('legal_basis') === 'standard_contractual_clauses' ? 'selected' : '' }}>Standard Contractual Clauses</option>
                            <option value="binding_corporate_rules" {{ old('legal_basis') === 'binding_corporate_rules' ? 'selected' : '' }}>Binding Corporate Rules</option>
                            <option value="certification_mechanism" {{ old('legal_basis') === 'certification_mechanism' ? 'selected' : '' }}>Certification Mechanism</option>
                            <option value="code_of_conduct" {{ old('legal_basis') === 'code_of_conduct' ? 'selected' : '' }}>Code of Conduct</option>
                            <option value="derogations" {{ old('legal_basis') === 'derogations' ? 'selected' : '' }}>Derogations</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="adequacy_decision" class="block text-sm font-medium text-gray-700">Adequacy Decision</label>
                        <select name="adequacy_decision" id="adequacy_decision" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                            <option value="adequate" {{ old('adequacy_decision') === 'adequate' ? 'selected' : '' }}>Adequate</option>
                            <option value="inadequate" {{ old('adequacy_decision') === 'inadequate' ? 'selected' : '' }}>Inadequate</option>
                            <option value="pending" {{ old('adequacy_decision') === 'pending' ? 'selected' : '' }}>Pending</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="transfer_date" class="block text-sm font-medium text-gray-700">Transfer Date</label>
                        <input type="date" name="transfer_date" id="transfer_date" value="{{ old('transfer_date') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                    </div>
                    <div class="mb-4">
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                            <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ old('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ old('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="suspended" {{ old('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                        </select>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('third-country-transfers.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Cancel
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Create Transfer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
