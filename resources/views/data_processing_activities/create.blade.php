@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Create Data Processing Activity</h1>
    <form action="{{ route('data-processing-activities.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="company_id" class="form-label">Company</label>
            <select class="form-control" id="company_id" name="company_id" required>
                @foreach($companies as $company)
                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="activity_name" class="form-label">Activity Name</label>
            <input type="text" class="form-control" id="activity_name" name="activity_name" required>
        </div>
        <div class="mb-3">
            <label for="processing_purpose" class="form-label">Processing Purpose</label>
            <input type="text" class="form-control" id="processing_purpose" name="processing_purpose">
        </div>
        <div class="mb-3">
            <label for="legal_basis" class="form-label">Legal Basis</label>
            <input type="text" class="form-control" id="legal_basis" name="legal_basis">
        </div>
        <button type="submit" class="btn btn-primary">Create</button>
    </form>
</div>
@endsection
