@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Consent Record #{{ $record->id }}</h1>
    <p><strong>Company:</strong> {{ $record->company->name ?? '-' }}</p>
    <p><strong>Consent Type:</strong> {{ $record->consent_type }}</p>
    <p><strong>Status:</strong> {{ $record->consent_status }}</p>
    <p><strong>Date:</strong> {{ $record->consent_date }}</p>
    <a href="{{ route('consent-records.edit', $record) }}" class="btn btn-warning">Edit</a>
    <a href="{{ route('consent-records.index') }}" class="btn btn-secondary">Back to List</a>
</div>
@endsection
