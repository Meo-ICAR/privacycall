@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold mb-6">Email Logs</h1>
    <div class="bg-white shadow rounded-lg p-6">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">To</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sent At</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($emailLogs as $log)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $log->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $log->subject }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $log->to_email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $log->created_at }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-4">
            {{ $emailLogs->links() }}
        </div>
    </div>
</div>
@endsection
