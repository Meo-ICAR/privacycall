@props(['model'])
@php
    $docTypes = \App\Models\DocumentType::all();
@endphp
<div class="mt-8">
    <h3 class="text-lg font-semibold mb-2">Documents</h3>
    <!-- Upload Form -->
    <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data" class="flex items-center space-x-2 mb-4">
        @csrf
        <input type="hidden" name="documentable_type" value="{{ get_class($model) }}">
        <input type="hidden" name="documentable_id" value="{{ $model->id }}">
        <select name="document_type_id" class="border rounded p-2" required>
            <option value="">Select type</option>
            @foreach($docTypes as $type)
                <option value="{{ $type->id }}">{{ $type->name }}</option>
            @endforeach
        </select>
        <input type="file" name="file" accept=".txt,.pdf" required class="border rounded p-2">
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Upload</button>
    </form>
    <!-- List Documents -->
    <ul class="divide-y divide-gray-200">
        @forelse($model->documents as $document)
            <li class="py-2 flex items-center justify-between">
                <div>
                    <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank" class="text-blue-700 hover:underline">
                        {{ $document->file_name }}
                    </a>
                    <span class="text-xs text-gray-500 ml-2">({{ $document->mime_type }})</span>
                    <span class="text-xs text-gray-400 ml-2">uploaded by {{ $document->uploader->name ?? 'Unknown' }}</span>
                    <span class="text-xs text-gray-600 ml-2">[{{ $document->documentType->name ?? 'No Type' }}]</span>
                </div>
                <form action="{{ route('documents.destroy', $document) }}" method="POST" onsubmit="return confirm('Delete this document?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-800 ml-4">Delete</button>
                </form>
            </li>
        @empty
            <li class="py-2 text-gray-500">No documents uploaded.</li>
        @endforelse
    </ul>
</div>
