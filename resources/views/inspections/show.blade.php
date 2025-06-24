@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-4">Inspection Details</h1>
    <div class="mb-4">
        <strong>Company:</strong> {{ $inspection->company->name }}<br>
        <strong>Customer:</strong> {{ $inspection->customer->first_name }} {{ $inspection->customer->last_name }}<br>
        <strong>Date:</strong> {{ $inspection->inspection_date }}<br>
        <strong>Status:</strong> {{ ucfirst($inspection->status) }}<br>
        <strong>Notes:</strong> {{ $inspection->notes }}
    </div>
    <div class="mb-6">
        <form action="{{ route('inspections.update', $inspection) }}" method="POST" class="inline-block">
            @csrf
            @method('PUT')
            <label for="status" class="mr-2 font-medium">Update Status:</label>
            <select name="status" id="status" class="border rounded p-1">
                <option value="pending" {{ $inspection->status == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="sent" {{ $inspection->status == 'sent' ? 'selected' : '' }}>Sent</option>
                <option value="acknowledged" {{ $inspection->status == 'acknowledged' ? 'selected' : '' }}>Acknowledged</option>
            </select>
            <button type="submit" class="ml-2 px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">Update</button>
        </form>
    </div>
    <div class="mb-6">
        <h2 class="text-xl font-semibold mb-2">Documents</h2>
        <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data" class="mb-4 flex items-center space-x-2">
            @csrf
            <input type="hidden" name="documentable_type" value="App\\Models\\Inspection">
            <input type="hidden" name="documentable_id" value="{{ $inspection->id }}">
            <input type="file" name="file" required class="border rounded p-1">
            <select name="document_type_id" required class="border rounded p-1">
                <option value="">-- Select Type --</option>
                @foreach(App\Models\DocumentType::all() as $type)
                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Upload</button>
        </form>
        <ul>
            @foreach($inspection->documents as $doc)
                <li class="mb-2">
                    <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="text-blue-600 hover:underline">{{ $doc->file_name }}</a>
                    ({{ $doc->mime_type }})
                    <form action="{{ route('documents.destroy', $doc) }}" method="POST" class="inline" onsubmit="return confirm('Delete this document?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline ml-2">Delete</button>
                    </form>
                </li>
            @endforeach
        </ul>
    </div>
    <a href="{{ route('inspections.index') }}" class="px-4 py-2 bg-gray-200 rounded">Back to Inspections</a>
</div>
@endsection
