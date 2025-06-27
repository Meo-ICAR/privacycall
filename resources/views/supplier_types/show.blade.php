@extends('layouts.app')

@section('content')
<div class="container">
    <div class="mb-4">
        <h1>Supplier Type Details</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Basic Information</h5>
                    <table class="table table-borderless">
                        <tr>
                            <th>Name:</th>
                            <td>{{ $supplierType->name }}</td>
                        </tr>
                        <tr>
                            <th>Description:</th>
                            <td>{{ $supplierType->description ?? 'No description provided' }}</td>
                        </tr>
                        <tr>
                            <th>Created:</th>
                            <td>{{ $supplierType->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Last Updated:</th>
                            <td>{{ $supplierType->updated_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="mt-4">
                <h5>Actions</h5>
                @if(auth()->user() && auth()->user()->hasRole('superadmin'))
                    <a href="{{ route('supplier-types.edit', $supplierType) }}" class="btn btn-warning">Edit</a>
                    <form action="{{ route('supplier-types.destroy', $supplierType) }}" method="POST" style="display:inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this supplier type?')">Delete</button>
                    </form>
                @endif
                <a href="{{ route('supplier-types.index') }}" class="btn btn-secondary">Back to List</a>
            </div>
        </div>
    </div>
</div>
@endsection
