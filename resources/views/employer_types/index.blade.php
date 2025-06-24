@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-4">Employer Types</h1>
    @if(auth()->user()->hasRole('superadmin'))
        <a href="{{ route('employer-types.create') }}" class="mb-4 inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Add New Type</a>
    @endif
    @if(session('success'))
        <div class="mb-4 p-2 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
    @endif
    <table class="min-w-full bg-white border rounded">
        <thead>
            <tr>
                <th class="px-4 py-2 border-b">Name</th>
                @if(auth()->user()->hasRole('superadmin'))
                <th class="px-4 py-2 border-b">Actions</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($types as $type)
                <tr>
                    <td class="px-4 py-2 border-b flex items-center gap-2">
                        @if($type->icon)
                            @if(Str::startsWith($type->icon, 'fa'))
                                <i class="{{ $type->icon }} text-xl"></i>
                            @else
                                <img src="{{ $type->icon }}" alt="icon" class="h-6 w-6 object-contain inline-block" />
                            @endif
                        @endif
                        {{ $type->name }}
                    </td>
                    @if(auth()->user()->hasRole('superadmin'))
                    <td class="px-4 py-2 border-b">
                        <a href="{{ route('employer-types.edit', $type) }}" class="text-blue-600 hover:underline mr-2">Edit</a>
                        <form action="{{ route('employer-types.destroy', $type) }}" method="POST" class="inline" onsubmit="return confirm('Delete this type?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">Delete</button>
                        </form>
                    </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
