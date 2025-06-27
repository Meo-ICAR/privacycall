@extends("layouts.app")

@section("content")
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Employer Types</h1>
        <p class="mt-2 text-gray-600">Manage different types of employers in the system</p>
    </div>

    @if(session("success"))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded" role="alert">
            {{ session("success") }}
        </div>
    @endif

    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-medium text-gray-900">All Employer Types</h2>
                @if(auth()->user()->hasRole("superadmin"))
                    <a href="{{ route("employer-types.create") }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <i class="fas fa-plus mr-2"></i>
                        Add New Type
                    </a>
                @endif
            </div>

            @if($types->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                @if(auth()->user()->hasRole("superadmin"))
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($types as $type)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @if($type->icon)
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    @if(Str::startsWith($type->icon, "fa"))
                                                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                            <i class="{{ $type->icon }} text-blue-600"></i>
                                                        </div>
                                                    @else
                                                        <img src="{{ $type->icon }}" alt="icon" class="h-10 w-10 rounded-full object-cover" />
                                                    @endif
                                                </div>
                                            @else
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center">
                                                        <i class="fas fa-building text-gray-600"></i>
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $type->name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    @if(auth()->user()->hasRole("superadmin"))
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <a href="{{ route("employer-types.edit", $type) }}" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route("employer-types.destroy", $type) }}" method="POST" class="inline" onsubmit="return confirm(\"Are you sure you want to delete this type?\")">
                                                    @csrf
                                                    @method("DELETE")
                                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-building text-gray-400 text-6xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No employer types found</h3>
                    <p class="text-gray-500 mb-6">Get started by adding your first employer type.</p>
                    @if(auth()->user()->hasRole("superadmin"))
                        <div class="flex justify-center space-x-3">
                            <a href="{{ route("employer-types.create") }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                <i class="fas fa-plus mr-2"></i>
                                Add First Type
                            </a>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
