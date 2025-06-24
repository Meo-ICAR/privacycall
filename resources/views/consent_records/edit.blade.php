@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Consent Record</h1>
    <form action="{{ route('consent-records.update', $record) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="company_id" class="form-label">Company</label>
            <select class="form-control" id="company_id" name="company_id" required>
                @foreach($companies as $company)
                    <option value="{{ $company->id }}" {{ old('company_id', $record->company_id) == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="consent_type" class="form-label">Consent Type</label>
            <input type="text" class="form-control" id="consent_type" name="consent_type" value="{{ old('consent_type', $record->consent_type) }}" required>
        </div>
        <div class="mb-3">
            <label for="consent_status" class="form-label">Consent Status</label>
            <input type="text" class="form-control" id="consent_status" name="consent_status" value="{{ old('consent_status', $record->consent_status) }}" required>
        </div>
        <div class="mb-3">
            <label for="consent_date" class="form-label">Consent Date</label>
            <input type="datetime-local" class="form-control" id="consent_date" name="consent_date" value="{{ old('consent_date', $record->consent_date ? $record->consent_date->format('Y-m-d\TH:i') : '') }}">
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('consent-records.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
