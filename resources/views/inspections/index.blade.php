@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-4">Inspections</h1>
    <a href="{{ route('inspections.create') }}" class="mb-4 inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Add Inspection</a>
    @if(session('success'))
        <div class="mb-4 p-2 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
    @endif
    <table class="min-w-full bg-white border rounded">
        <thead>
            <tr>
                <th class="px-4 py-2 border-b">Company</th>
                <th class="px-4 py-2 border-b">Customer</th>
                <th class="px-4 py-2 border-b">Date</th>
                <th class="px-4 py-2 border-b">Status</th>
                <th class="px-4 py-2 border-b">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inspections as $inspection)
                <tr>
                    <td class="px-4 py-2 border-b">{{ $inspection->company->name }}</td>
                    <td class="px-4 py-2 border-b">{{ $inspection->customer->first_name }} {{ $inspection->customer->last_name }}</td>
                    <td class="px-4 py-2 border-b">{{ $inspection->inspection_date }}</td>
                    <td class="px-4 py-2 border-b">{{ ucfirst($inspection->status) }}</td>
                    <td class="px-4 py-2 border-b">
                        <a href="{{ route('inspections.show', $inspection) }}" class="text-blue-600 hover:underline mr-2">View</a>
                        <a href="{{ route('inspections.edit', $inspection) }}" class="text-yellow-600 hover:underline mr-2">Edit</a>
                        <form action="{{ route('inspections.destroy', $inspection) }}" method="POST" class="inline" onsubmit="return confirm('Delete this inspection?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="mt-4">{{ $inspections->links() }}</div>
</div>
@endsection
