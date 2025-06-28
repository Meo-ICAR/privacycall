@extends('layouts.app')

@section('title', 'Email Providers')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">Email Providers</h1>
            <a href="{{ route('email-providers.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-2"></i>Add Email Provider
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 text-green-700 bg-green-100 border border-green-200 rounded p-3">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 text-red-700 bg-red-100 border border-red-200 rounded p-3">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white shadow rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Active</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($emailProviders as $provider)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                                <a href="{{ route('email-providers.show', $provider) }}" class="hover:underline">
                                    {{ $provider->display_name ?? $provider->name }}
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ ucfirst($provider->type) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($provider->is_active)
                                    <span class="inline-block px-2 py-1 text-xs bg-green-100 text-green-800 rounded">Active</span>
                                @else
                                    <span class="inline-block px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded">Inactive</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('email-providers.edit', $provider) }}" class="text-blue-600 hover:underline mr-3">Edit</a>
                                <form action="{{ route('email-providers.destroy', $provider) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">No email providers found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $emailProviders->links() }}
        </div>
    </div>
</div>
@endsection
