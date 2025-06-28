@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Email Templates</h1>
            <p class="mt-2 text-gray-600">Manage email templates for your organization</p>
        </div>
        @if(Auth::user()->hasRole('superadmin'))
            <a href="{{ route('email-templates.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i>
                Create Template
            </a>
        @endif
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
            @if($templates->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Template</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($templates as $template)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $template->name }}</div>
                                            <div class="text-sm text-gray-500">{{ Str::limit($template->subject, 50) }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($template->company)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $template->company->name }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                Global
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            {{ ucfirst($template->category) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($template->is_active)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Active
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $template->created_at->format('Y-m-d') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" style="border: 1px solid #e5e7eb; background-color: #f9fafb;">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('email-templates.show', $template) }}" class="text-blue-600 hover:text-blue-900" title="View" style="display: inline-block; padding: 4px;">
                                                <i class="fas fa-eye"></i>
                                                <span class="sr-only">View</span>
                                            </a>
                                            @if(Auth::user()->hasRole('superadmin'))
                                                <a href="{{ route('email-templates.edit', $template) }}" class="text-yellow-600 hover:text-yellow-900" title="Edit" style="display: inline-block; padding: 4px;">
                                                    <i class="fas fa-edit"></i>
                                                    <span class="sr-only">Edit</span>
                                                </a>
                                            @endif
                                            @if(Auth::user()->hasRole('superadmin') || $template->company_id === Auth::user()->company_id)
                                                <form action="{{ route('email-templates.destroy', $template) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this template?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Delete" style="display: inline-block; padding: 4px; background: none; border: none; cursor: pointer;">
                                                        <i class="fas fa-trash"></i>
                                                        <span class="sr-only">Delete</span>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-envelope text-gray-400 text-6xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No email templates found</h3>
                    <p class="text-gray-500 mb-6">Get started by creating your first email template.</p>
                    @if(Auth::user()->hasRole('superadmin'))
                        <a href="{{ route('email-templates.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            <i class="fas fa-plus mr-2"></i>
                            Create First Template
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
