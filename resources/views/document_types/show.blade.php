@extends('layouts.app')

@section('content')
<div class="container">
    <div class="mb-4">
        <h1>Document Type Details</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Basic Information</h5>
                    <table class="table table-borderless">
                        <tr>
                            <th>Name:</th>
                            <td>{{ $documentType->name }}</td>
                        </tr>
                        <tr>
                            <th>Description:</th>
                            <td>{{ $documentType->description ?? 'No description provided' }}</td>
                        </tr>
                        <tr>
                            <th>Created:</th>
                            <td>{{ $documentType->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Last Updated:</th>
                            <td>{{ $documentType->updated_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="mt-4">
                <h5>Actions</h5>
                @if(auth()->user() && auth()->user()->hasRole('superadmin'))
                    <a href="{{ route('document-types.edit', $documentType) }}" class="btn btn-warning">Edit</a>
                    <form action="{{ route('document-types.destroy', $documentType) }}" method="POST" style="display:inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this document type?')">Delete</button>
                    </form>
                @endif
                <a href="{{ route('document-types.index') }}" class="btn btn-secondary">Back to List</a>
            </div>
        </div>
    </div>
</div>
@endsection
