@extends('layouts.app')

@section('title', 'DPIA Details')

@section('content')
<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">{{ $dpia->title ?? 'DPIA #' . $dpia->id }}</h2>
                    <a href="{{ route('data-protection-impact-assessments.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to DPIA List
                    </a>
                </div>
                <div class="mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Description</h3>
                    <p class="text-gray-700">{{ $dpia->description ?? 'No description provided.' }}</p>
                </div>
                <div class="mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Processing Activity</h3>
                    <p class="text-gray-700">{{ $dpia->processing_activity ?? 'No processing activity specified.' }}</p>
                </div>
                <div class="mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Status</h3>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $dpia->status === 'completed' ? 'bg-green-100 text-green-800' : ($dpia->status === 'in_progress' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                        {{ ucfirst(str_replace('_', ' ', $dpia->status ?? 'unknown')) }}
                    </span>
                </div>
                <div class="mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Risk Level</h3>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $dpia->risk_level === 'high' ? 'bg-red-100 text-red-800' : ($dpia->risk_level === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                        {{ ucfirst($dpia->risk_level ?? 'unknown') }} Risk
                    </span>
                </div>
                <div class="mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Assessor</h3>
                    <p class="text-gray-700">{{ $dpia->assessor ?? 'No assessor assigned.' }}</p>
                </div>
                <div class="mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Assessment Date</h3>
                    <p class="text-gray-700">{{ $dpia->assessment_date ? $dpia->assessment_date->format('M d, Y') : 'No date specified.' }}</p>
                </div>
                <div class="mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Created At</h3>
                    <p class="text-gray-700">{{ $dpia->created_at->format('M d, Y H:i') }}</p>
                </div>
                <div class="flex space-x-3 mt-6">
                    <a href="{{ route('data-protection-impact-assessments.edit', $dpia->id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">Edit</a>
                    <a href="{{ route('data-protection-impact-assessments.export', $dpia->id) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">Export</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
