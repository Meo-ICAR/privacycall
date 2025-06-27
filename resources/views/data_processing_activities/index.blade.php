@extends('layouts.app')

@section('content')
<div class="container">
    <div class="mb-4">
        <h1>Data Processing Activities</h1>
        <a href="{{ route('data-processing-activities.create') }}" class="btn btn-primary mb-3">Create Activity</a>
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

    @if($activities->count() > 0)
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Company</th>
                    <th>Name</th>
                    <th>Purpose</th>
                    <th>Legal Basis</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($activities as $activity)
                    <tr>
                        <td>{{ $activity->id }}</td>
                        <td>{{ $activity->company->name ?? '-' }}</td>
                        <td>{{ $activity->activity_name }}</td>
                        <td>{{ $activity->processing_purpose }}</td>
                        <td>{{ $activity->legal_basis }}</td>
                        <td>{{ $activity->is_active ? 'Active' : 'Inactive' }}</td>
                        <td>
                            <a href="{{ route('data-processing-activities.show', $activity) }}" class="btn btn-info btn-sm">View</a>
                            <a href="{{ route('data-processing-activities.edit', $activity) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('data-processing-activities.destroy', $activity) }}" method="POST" style="display:inline-block">
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
            <p>No data processing activities found.</p>
            <a href="{{ route('data-processing-activities.create') }}" class="btn btn-primary">Create First Activity</a>
        </div>
    @endif
</div>
@endsection
