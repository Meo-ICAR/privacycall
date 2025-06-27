@extends('layouts.app')

@section('content')
<div class="container">
    <div class="mb-4">
        <h1>Customer Details</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Personal Information</h5>
                    <table class="table table-borderless">
                        <tr>
                            <th>Name:</th>
                            <td>{{ $customer->first_name }} {{ $customer->last_name }}</td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td>{{ $customer->email }}</td>
                        </tr>
                        <tr>
                            <th>Phone:</th>
                            <td>{{ $customer->phone ?? 'Not provided' }}</td>
                        </tr>
                        <tr>
                            <th>Address:</th>
                            <td>{{ $customer->address ?? 'Not provided' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h5>Customer Information</h5>
                    <table class="table table-borderless">
                        <tr>
                            <th>Company:</th>
                            <td>{{ $customer->company->name ?? 'Unknown' }}</td>
                        </tr>
                        <tr>
                            <th>Customer Type:</th>
                            <td>{{ $customer->customerType->name ?? 'Unknown' }}</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                <span class="badge {{ $customer->is_active ? 'bg-success' : 'bg-danger' }}">
                                    {{ $customer->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Created:</th>
                            <td>{{ $customer->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="mt-4">
                <h5>Actions</h5>
                <a href="{{ route('customers.edit', $customer) }}" class="btn btn-warning">Edit</a>
                <form action="{{ route('customers.destroy', $customer) }}" method="POST" style="display:inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this customer?')">Delete</button>
                </form>
                <a href="{{ route('customers.index') }}" class="btn btn-secondary">Back to List</a>
            </div>
        </div>
    </div>
</div>
@endsection
