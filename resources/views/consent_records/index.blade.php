@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Consent Records</h1>
    <a href="{{ route('consent-records.create') }}" class="btn btn-primary mb-3">Create Consent Record</a>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Company</th>
                <th>Type</th>
                <th>Status</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($consentRecords as $record)
                <tr>
                    <td>{{ $record->id }}</td>
                    <td>{{ $record->company->name ?? '-' }}</td>
                    <td>{{ $record->consent_type }}</td>
                    <td>{{ $record->consent_status }}</td>
                    <td>{{ $record->consent_date }}</td>
                    <td>
                        <a href="{{ route('consent-records.show', $record) }}" class="btn btn-info btn-sm">View</a>
                        <a href="{{ route('consent-records.edit', $record) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('consent-records.destroy', $record) }}" method="POST" style="display:inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
