@extends('layouts.app')

@section('title', 'Compare Register Versions')

@section('content')
<div class="py-12">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Compare Register Versions</h2>
                <form method="GET" action="{{ route('gdpr.register.versions.compare') }}" class="mb-8 flex space-x-4">
                    <div class="flex-1">
                        <label for="version1" class="block text-sm font-medium text-gray-700">Version 1</label>
                        <select name="version1" id="version1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            @foreach($allVersions as $v)
                            <option value="{{ $v->id }}" {{ request('version1') == $v->id ? 'selected' : '' }}>{{ $v->name ?? 'v' . $v->id }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex-1">
                        <label for="version2" class="block text-sm font-medium text-gray-700">Version 2</label>
                        <select name="version2" id="version2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            @foreach($allVersions as $v)
                            <option value="{{ $v->id }}" {{ request('version2') == $v->id ? 'selected' : '' }}>{{ $v->name ?? 'v' . $v->id }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">Compare</button>
                    </div>
                </form>
                @if(isset($diff))
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Field</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Version 1</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Version 2</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($diff as $field => $values)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $field }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $values[0] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $values[1] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center text-gray-500 py-8">Select two versions to compare.</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
