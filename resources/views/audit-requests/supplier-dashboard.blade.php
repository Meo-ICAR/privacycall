@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Supplier Audit Dashboard</h1>
            <p class="text-gray-600 mt-2">{{ $supplier->name }} - Audit History & Compliance Overview</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('audit-requests.create') }}?supplier_id={{ $supplier->id }}"
               class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                New Audit
            </a>
            <a href="{{ route('suppliers.show', $supplier) }}"
               class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                Back to Supplier
            </a>
        </div>
    </div>

    <!-- Supplier Information -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-16 w-16">
                    <div class="h-16 w-16 rounded-full bg-gray-300 flex items-center justify-center">
                        <span class="text-xl font-medium text-gray-700">
                            {{ substr($supplier->name, 0, 2) }}
                        </span>
                    </div>
                </div>
                <div class="ml-6">
                    <h2 class="text-2xl font-bold text-gray-900">{{ $supplier->name }}</h2>
                    <div class="mt-2 grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
                        <div>
                            <span class="font-medium">Email:</span> {{ $supplier->email }}
                        </div>
                        <div>
                            <span class="font-medium">Phone:</span> {{ $supplier->phone ?? 'N/A' }}
                        </div>
                        <div>
                            <span class="font-medium">Status:</span>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                {{ $supplier->supplier_status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($supplier->supplier_status ?? 'unknown') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Audits</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_audits'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Completed</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['completed_audits'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">In Progress</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['in_progress_audits'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">High Risk</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['high_risk_audits'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Compliance Score Trend -->
    @if($stats['average_compliance_score'])
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Average Compliance Score</h3>
            <div class="flex items-center">
                <div class="flex-1 bg-gray-200 rounded-full h-4 mr-4">
                    <div class="bg-blue-600 h-4 rounded-full" style="width: {{ $stats['average_compliance_score'] }}%"></div>
                </div>
                <span class="text-2xl font-bold text-gray-900">{{ round($stats['average_compliance_score'], 1) }}%</span>
            </div>
            <div class="mt-2 text-sm text-gray-600">
                @if($stats['average_compliance_score'] >= 90)
                    <span class="text-green-600 font-medium">Excellent compliance level</span>
                @elseif($stats['average_compliance_score'] >= 70)
                    <span class="text-yellow-600 font-medium">Good compliance level</span>
                @elseif($stats['average_compliance_score'] >= 50)
                    <span class="text-orange-600 font-medium">Needs improvement</span>
                @else
                    <span class="text-red-600 font-medium">Critical compliance issues</span>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Audit History Table -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Audit History</h3>
        </div>
        <div class="overflow-x-auto">
            @if($audits->count())
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Risk Level</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Compliance Score</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Auditor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($audits as $audit)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $audit->created_at->format('M d, Y') }}</div>
                                    <div class="text-sm text-gray-500">{{ $audit->created_at->format('H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ ucfirst($audit->audit_type) }}</div>
                                    <div class="text-sm text-gray-500">{{ ucfirst(str_replace('_', ' ', $audit->audit_scope)) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusColors = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'in_progress' => 'bg-blue-100 text-blue-800',
                                            'completed' => 'bg-green-100 text-green-800',
                                            'cancelled' => 'bg-red-100 text-red-800'
                                        ];
                                    @endphp
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$audit->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst(str_replace('_', ' ', $audit->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($audit->risk_level)
                                        @php
                                            $riskColors = [
                                                'low' => 'bg-green-100 text-green-800',
                                                'medium' => 'bg-yellow-100 text-yellow-800',
                                                'high' => 'bg-orange-100 text-orange-800',
                                                'critical' => 'bg-red-100 text-red-800'
                                            ];
                                        @endphp
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $riskColors[$audit->risk_level] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ ucfirst($audit->risk_level) }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($audit->compliance_score !== null)
                                        <div class="flex items-center">
                                            <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $audit->compliance_score }}%"></div>
                                            </div>
                                            <span class="text-sm text-gray-900">{{ $audit->compliance_score }}%</span>
                                        </div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($audit->auditor)
                                        <div class="text-sm text-gray-900">{{ $audit->auditor->name }}</div>
                                    @else
                                        <span class="text-gray-400">Not assigned</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('audit-requests.show', $audit) }}"
                                       class="text-blue-600 hover:text-blue-900">View Details</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No audits yet</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating the first audit for this supplier.</p>
                    <div class="mt-6">
                        <a href="{{ route('audit-requests.create') }}?supplier_id={{ $supplier->id }}"
                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            Create First Audit
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Risk Assessment Summary -->
    @if($audits->where('risk_level', 'high')->count() > 0 || $audits->where('risk_level', 'critical')->count() > 0)
    <div class="bg-white rounded-lg shadow mt-6">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Risk Assessment Summary</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-2">High Risk Audits</h4>
                    <div class="space-y-2">
                        @foreach($audits->where('risk_level', 'high') as $audit)
                            <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ ucfirst($audit->audit_type) }} Audit</div>
                                    <div class="text-sm text-gray-500">{{ $audit->created_at->format('M d, Y') }}</div>
                                </div>
                                <a href="{{ route('audit-requests.show', $audit) }}"
                                   class="text-orange-600 hover:text-orange-900 text-sm font-medium">View</a>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Critical Risk Audits</h4>
                    <div class="space-y-2">
                        @foreach($audits->where('risk_level', 'critical') as $audit)
                            <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ ucfirst($audit->audit_type) }} Audit</div>
                                    <div class="text-sm text-gray-500">{{ $audit->created_at->format('M d, Y') }}</div>
                                </div>
                                <a href="{{ route('audit-requests.show', $audit) }}"
                                   class="text-red-600 hover:text-red-900 text-sm font-medium">View</a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
