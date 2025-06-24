@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Data Processing Activity #{{ $activity->id }}</h1>
    <p><strong>Company:</strong> {{ $activity->company->name ?? '-' }}</p>
    <p><strong>Name:</strong> {{ $activity->activity_name }}</p>
    <p><strong>Purpose:</strong> {{ $activity->processing_purpose }}</p>
    <p><strong>Legal Basis:</strong> {{ $activity->legal_basis }}</p>
    <p><strong>Status:</strong> {{ $activity->is_active ? 'Active' : 'Inactive' }}</p>
    <a href="{{ route('data-processing-activities.edit', $activity) }}" class="btn btn-warning">Edit</a>
    <a href="{{ route('data-processing-activities.index') }}" class="btn btn-secondary">Back to List</a>
</div>
@endsection
