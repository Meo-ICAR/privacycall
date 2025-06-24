@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Data Processing Activity</h1>
    <form action="{{ route('data-processing-activities.update', $activity) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="company_id" class="form-label">Company</label>
            <select class="form-control" id="company_id" name="company_id" required>
                @foreach($companies as $company)
                    <option value="{{ $company->id }}" {{ old('company_id', $activity->company_id) == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="activity_name" class="form-label">Activity Name</label>
            <input type="text" class="form-control" id="activity_name" name="activity_name" value="{{ old('activity_name', $activity->activity_name) }}" required>
        </div>
        <div class="mb-3">
            <label for="processing_purpose" class="form-label">Processing Purpose</label>
            <input type="text" class="form-control" id="processing_purpose" name="processing_purpose" value="{{ old('processing_purpose', $activity->processing_purpose) }}">
        </div>
        <div class="mb-3">
            <label for="legal_basis" class="form-label">Legal Basis</label>
            <input type="text" class="form-control" id="legal_basis" name="legal_basis" value="{{ old('legal_basis', $activity->legal_basis) }}">
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('data-processing-activities.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
