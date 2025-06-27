@extends('layouts.app')

@section('content')
<div class="container">
    <div class="mb-4">
        <h1>Documents</h1>
        <a href="{{ route('documents.create') }}" class="btn btn-primary mb-3">Upload Document</a>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger" role="alert">
            {{ session('error') }}
        </div>
    @endif

    @if($documents->count() > 0)
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>File Name</th>
                    <th>Type</th>
                    <th>Uploaded By</th>
                    <th>Related To</th>
                    <th>Upload Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($documents as $document)
                    <tr>
                        <td>{{ $document->id }}</td>
                        <td>{{ $document->file_name }}</td>
                        <td>{{ $document->documentType->name ?? 'Unknown' }}</td>
                        <td>{{ $document->uploader->name ?? 'Unknown' }}</td>
                        <td>
                            @if($document->documentable)
                                {{ class_basename($document->documentable_type) }}: {{ $document->documentable->name ?? $document->documentable->id }}
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $document->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            <a href="{{ route('documents.show', $document) }}" class="btn btn-info btn-sm">View</a>
                            <a href="{{ route('documents.edit', $document) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('documents.destroy', $document) }}" method="POST" style="display:inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="text-center py-4">
            <p>No documents found.</p>
            <a href="{{ route('documents.create') }}" class="btn btn-primary">Upload First Document</a>
        </div>
    @endif
</div>
@endsection
