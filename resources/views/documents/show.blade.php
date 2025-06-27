@extends('layouts.app')

@section('content')
<div class="container">
    <div class="mb-4">
        <h1>Document Details</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Document Information</h5>
                    <table class="table table-borderless">
                        <tr>
                            <th>File Name:</th>
                            <td>{{ $document->file_name }}</td>
                        </tr>
                        <tr>
                            <th>Document Type:</th>
                            <td>{{ $document->documentType->name ?? 'Unknown' }}</td>
                        </tr>
                        <tr>
                            <th>MIME Type:</th>
                            <td>{{ $document->mime_type }}</td>
                        </tr>
                        <tr>
                            <th>Uploaded By:</th>
                            <td>{{ $document->uploader->name ?? 'Unknown' }}</td>
                        </tr>
                        <tr>
                            <th>Upload Date:</th>
                            <td>{{ $document->created_at->format('Y-m-d H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>Last Updated:</th>
                            <td>{{ $document->updated_at->format('Y-m-d H:i:s') }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h5>Related Information</h5>
                    <table class="table table-borderless">
                        <tr>
                            <th>Related To:</th>
                            <td>
                                @if($document->documentable)
                                    {{ class_basename($document->documentable_type) }}: {{ $document->documentable->name ?? $document->documentable->id }}
                                @else
                                    Not assigned
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>File Size:</th>
                            <td>{{ number_format(filesize(storage_path('app/' . $document->file_path)) / 1024, 2) }} KB</td>
                        </tr>
                    </table>
                </div>
            </div>

            @if($document->description)
                <div class="mt-4">
                    <h5>Description</h5>
                    <p>{{ $document->description }}</p>
                </div>
            @endif

            <div class="mt-4">
                <h5>Actions</h5>
                <a href="{{ Storage::url($document->file_path) }}" class="btn btn-primary" target="_blank">Download</a>
                <a href="{{ route('documents.edit', $document) }}" class="btn btn-warning">Edit</a>
                <form action="{{ route('documents.destroy', $document) }}" method="POST" style="display:inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this document?')">Delete</button>
                </form>
                <a href="{{ route('documents.index') }}" class="btn btn-secondary">Back to List</a>
            </div>
        </div>
    </div>
</div>
@endsection
