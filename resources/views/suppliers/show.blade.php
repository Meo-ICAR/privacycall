@extends('layouts.app')

@section('content')
<div class="container">
    <div class="mb-4">
        <h1>Supplier Details</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Basic Information</h5>
                    <table class="table table-borderless">
                        <tr>
                            <th>Name:</th>
                            <td>{{ $supplier->name }}</td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td>{{ $supplier->email ?? 'Not provided' }}</td>
                        </tr>
                        <tr>
                            <th>Phone:</th>
                            <td>{{ $supplier->phone ?? 'Not provided' }}</td>
                        </tr>
                        <tr>
                            <th>Address:</th>
                            <td>{{ $supplier->address ?? 'Not provided' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h5>Company Information</h5>
                    <table class="table table-borderless">
                        <tr>
                            <th>Company:</th>
                            <td>{{ $supplier->company->name ?? 'Unknown' }}</td>
                        </tr>
                        <tr>
                            <th>Supplier Type:</th>
                            <td>{{ $supplier->supplierType->name ?? 'Unknown' }}</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                <span class="badge {{ $supplier->is_active ? 'bg-success' : 'bg-danger' }}">
                                    {{ $supplier->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Created:</th>
                            <td>{{ $supplier->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="mt-4">
                <h5>Actions</h5>
                <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-warning">Edit</a>
                <a href="{{ route('suppliers.audit-dashboard', $supplier) }}" class="btn btn-info">
                    <i class="fas fa-chart-line mr-2"></i>Audit Dashboard
                </a>
                <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" style="display:inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this supplier?')">Delete</button>
                </form>
                <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Back to List</a>
            </div>
        </div>
    </div>
</div>
@endsection
