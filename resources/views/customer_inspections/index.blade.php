@extends('layouts.app')

@section('content')
<div class="container">
    <div class="mb-4">
        <h1>Customer Inspections</h1>
        <a href="{{ route('customer-inspections.create') }}" class="btn btn-primary mb-3">Create Inspection</a>
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

    @if($inspections->count() > 0)
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Company</th>
                    <th>Customer</th>
                    <th>Inspection Date</th>
                    <th>Status</th>
                    <th>Inspector</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($inspections as $inspection)
                    <tr>
                        <td>{{ $inspection->id }}</td>
                        <td>{{ $inspection->company->name ?? 'Unknown' }}</td>
                        <td>{{ $inspection->customer->first_name ?? '' }} {{ $inspection->customer->last_name ?? '' }}</td>
                        <td>{{ $inspection->inspection_date ? $inspection->inspection_date->format('Y-m-d') : 'Not set' }}</td>
                        <td>
                            <span class="badge bg-{{ $inspection->status === 'completed' ? 'success' : ($inspection->status === 'pending' ? 'warning' : 'secondary') }}">
                                {{ ucfirst($inspection->status) }}
                            </span>
                        </td>
                        <td>{{ $inspection->inspector->name ?? 'Unknown' }}</td>
                        <td>
                            <a href="{{ route('customer-inspections.show', $inspection) }}" class="btn btn-info btn-sm">View</a>
                            <a href="{{ route('customer-inspections.edit', $inspection) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('customer-inspections.destroy', $inspection) }}" method="POST" style="display:inline-block">
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
            <p>No customer inspections found.</p>
            <a href="{{ route('customer-inspections.create') }}" class="btn btn-primary">Create First Inspection</a>
        </div>
    @endif
</div>
@endsection
