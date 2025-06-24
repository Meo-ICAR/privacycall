@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-4">Holdings</h1>
    @if(auth()->user()->hasRole('superadmin'))
        <a href="{{ route('holdings.create') }}" class="mb-4 inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Add New Holding</a>
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
            @foreach($holdings as $holding)
                <tr>
                    <td class="px-4 py-2 border-b">{{ $holding->name }}</td>
                    @if(auth()->user()->hasRole('superadmin'))
                    <td class="px-4 py-2 border-b">
                        <a href="{{ route('holdings.edit', $holding) }}" class="text-blue-600 hover:underline mr-2">Edit</a>
                        <form action="{{ route('holdings.destroy', $holding) }}" method="POST" class="inline" onsubmit="return confirm('Delete this holding?')">
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
