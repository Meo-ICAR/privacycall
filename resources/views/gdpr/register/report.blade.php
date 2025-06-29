@extends('layouts.app')

@section('title', 'GDPR Register Report')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">GDPR Compliance Report</h2>
                    <div class="flex space-x-3">
                        <a href="{{ route('gdpr.register.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Register
                        </a>
                        <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                            </svg>
                            Print Report
                        </button>
                    </div>
                </div>

                <!-- Report Header -->
                <div class="mb-8 bg-gray-50 p-6 rounded-lg">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Report Generated</h3>
                            <p class="text-sm text-gray-500">{{ now()->format('F d, Y \a\t g:i A') }}</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Organization</h3>
                            <p class="text-sm text-gray-500">{{ $company->name ?? 'Your Organization' }}</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Report Period</h3>
                            <p class="text-sm text-gray-500">{{ $reportPeriod ?? 'All Time' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Executive Summary -->
                <div class="mb-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Executive Summary</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate">Compliance Score</dt>
                                            <dd class="text-lg font-medium text-gray-900">{{ $complianceScore ?? '85%' }}</dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate">Total Activities</dt>
                                            <dd class="text-lg font-medium text-gray-900">{{ $totalActivities ?? 0 }}</dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate">Issues Found</dt>
                                            <dd class="text-lg font-medium text-gray-900">{{ $issuesFound ?? 0 }}</dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate">Risk Level</dt>
                                            <dd class="text-lg font-medium text-gray-900">{{ $riskLevel ?? 'Medium' }}</dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Compliance Analysis -->
                <div class="mb-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Compliance Analysis</h3>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Legal Basis Distribution -->
                        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                            <div class="px-4 py-5 sm:px-6">
                                <h4 class="text-lg leading-6 font-medium text-gray-900">Legal Basis Distribution</h4>
                                <p class="mt-1 max-w-2xl text-sm text-gray-500">Breakdown of legal bases used for processing</p>
                            </div>
                            <div class="border-t border-gray-200">
                                <div class="p-6">
                                    @forelse($legalBasisDistribution ?? [] as $basis => $count)
                                    <div class="flex items-center justify-between py-2">
                                        <span class="text-sm font-medium text-gray-700">{{ $basis }}</span>
                                        <div class="flex items-center">
                                            <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($count / ($totalActivities ?? 1)) * 100 }}%"></div>
                                            </div>
                                            <span class="text-sm text-gray-900">{{ $count }}</span>
                                        </div>
                                    </div>
                                    @empty
                                    <p class="text-sm text-gray-500">No legal basis data available</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- Data Categories -->
                        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                            <div class="px-4 py-5 sm:px-6">
                                <h4 class="text-lg leading-6 font-medium text-gray-900">Data Categories Processed</h4>
                                <p class="mt-1 max-w-2xl text-sm text-gray-500">Types of personal data being processed</p>
                            </div>
                            <div class="border-t border-gray-200">
                                <div class="p-6">
                                    @forelse($dataCategories ?? [] as $category => $count)
                                    <div class="flex items-center justify-between py-2">
                                        <span class="text-sm font-medium text-gray-700">{{ $category }}</span>
                                        <span class="text-sm text-gray-900">{{ $count }}</span>
                                    </div>
                                    @empty
                                    <p class="text-sm text-gray-500">No data category information available</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Risk Assessment -->
                <div class="mb-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Risk Assessment</h3>
                    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                        <div class="px-4 py-5 sm:px-6">
                            <h4 class="text-lg leading-6 font-medium text-gray-900">Identified Risks</h4>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500">Potential compliance risks and recommendations</p>
                        </div>
                        <div class="border-t border-gray-200">
                            <div class="p-6">
                                @forelse($risks ?? [] as $risk)
                                <div class="border-l-4 border-red-400 bg-red-50 p-4 mb-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-red-800">{{ $risk->title ?? 'Risk Title' }}</h3>
                                            <div class="mt-2 text-sm text-red-700">
                                                <p>{{ $risk->description ?? 'Risk description' }}</p>
                                            </div>
                                            @if(isset($risk->recommendation))
                                            <div class="mt-2 text-sm text-red-700">
                                                <strong>Recommendation:</strong> {{ $risk->recommendation }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="border-l-4 border-green-400 bg-green-50 p-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-green-800">No High-Risk Issues Found</h3>
                                            <div class="mt-2 text-sm text-green-700">
                                                <p>Your processing register appears to be compliant with current requirements.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recommendations -->
                <div class="mb-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Recommendations</h3>
                    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                        <div class="px-4 py-5 sm:px-6">
                            <h4 class="text-lg leading-6 font-medium text-gray-900">Action Items</h4>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500">Recommended actions to improve compliance</p>
                        </div>
                        <div class="border-t border-gray-200">
                            <div class="p-6">
                                <div class="space-y-4">
                                    @forelse($recommendations ?? [] as $recommendation)
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <h4 class="text-sm font-medium text-gray-900">{{ $recommendation->title ?? 'Recommendation Title' }}</h4>
                                            <p class="text-sm text-gray-500 mt-1">{{ $recommendation->description ?? 'Recommendation description' }}</p>
                                            @if(isset($recommendation->priority))
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $recommendation->priority === 'high' ? 'bg-red-100 text-red-800' : ($recommendation->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }} mt-2">
                                                {{ ucfirst($recommendation->priority ?? 'medium') }} Priority
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                    @empty
                                    <div class="text-center py-8">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <h3 class="mt-2 text-sm font-medium text-gray-900">No specific recommendations</h3>
                                        <p class="mt-1 text-sm text-gray-500">Your current compliance status is satisfactory.</p>
                                    </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Report Footer -->
                <div class="mt-8 pt-8 border-t border-gray-200">
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">Report Generated By</h4>
                                <p class="text-sm text-gray-500">{{ auth()->user()->name ?? 'System User' }}</p>
                                <p class="text-sm text-gray-500">{{ auth()->user()->email ?? 'system@example.com' }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">Next Review Date</h4>
                                <p class="text-sm text-gray-500">{{ now()->addMonths(6)->format('F d, Y') }}</p>
                                <p class="text-sm text-gray-500">Recommended quarterly review</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print {
        display: none !important;
    }
}
</style>
@endsection
