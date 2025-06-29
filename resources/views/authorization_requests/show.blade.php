@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Authorization Request #{{ $authorizationRequest->id }}</h2>
                    <a href="{{ route('authorization-requests.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Requests
                    </a>
                </div>
                <div class="mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Supplier</h3>
                    <p class="text-gray-700">{{ $authorizationRequest->supplier->name ?? '-' }}</p>
                </div>
                <div class="mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Subsupplier</h3>
                    <p class="text-gray-700">{{ $authorizationRequest->subsupplier->service_description ?? '-' }}</p>
                </div>
                <div class="mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Company</h3>
                    <p class="text-gray-700">{{ $authorizationRequest->company->name ?? '-' }}</p>
                </div>
                <div class="mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Status</h3>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $authorizationRequest->status === 'approved' ? 'bg-green-100 text-green-800' : ($authorizationRequest->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                        {{ ucfirst($authorizationRequest->status) }}
                    </span>
                </div>
                <div class="mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Justification</h3>
                    <p class="text-gray-700">{{ $authorizationRequest->justification ?? '-' }}</p>
                </div>
                <div class="mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Review Notes</h3>
                    <p class="text-gray-700">{{ $authorizationRequest->review_notes ?? '-' }}</p>
                </div>
                <div class="mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Reviewed By</h3>
                    <p class="text-gray-700">{{ $authorizationRequest->reviewer->name ?? '-' }}</p>
                </div>
                <div class="mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Reviewed At</h3>
                    <p class="text-gray-700">{{ $authorizationRequest->reviewed_at ?? '-' }}</p>
                </div>
                @if($authorizationRequest->status === 'pending')
                <div class="flex space-x-4 mt-6">
                    <form action="{{ route('authorization-requests.approve', $authorizationRequest) }}" method="POST" class="flex-1">
                        @csrf
                        <div class="mb-3">
                            <label for="review_notes_approve" class="block text-sm font-medium text-gray-700">Review Notes</label>
                            <textarea name="review_notes" id="review_notes_approve" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm"></textarea>
                        </div>
                        <button class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150" onclick="return confirm('Approve this request?')">Approve</button>
                    </form>
                    <form action="{{ route('authorization-requests.deny', $authorizationRequest) }}" method="POST" class="flex-1">
                        @csrf
                        <div class="mb-3">
                            <label for="review_notes_deny" class="block text-sm font-medium text-gray-700">Review Notes</label>
                            <textarea name="review_notes" id="review_notes_deny" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm"></textarea>
                        </div>
                        <button class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150" onclick="return confirm('Deny this request?')">Deny</button>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
