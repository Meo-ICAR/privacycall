@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Training</h1>
    <form action="{{ route('trainings.update', $training) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $training->title) }}" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description">{{ old('description', $training->description) }}</textarea>
        </div>
        <div class="mb-3">
            <label for="type" class="form-label">Type</label>
            <select class="form-control" id="type" name="type" required>
                <option value="online" {{ old('type', $training->type) == 'online' ? 'selected' : '' }}>Online</option>
                <option value="in_person" {{ old('type', $training->type) == 'in_person' ? 'selected' : '' }}>In Person</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="date" class="form-label">Date</label>
            <input type="date" class="form-control" id="date" name="date" value="{{ old('date', $training->date) }}">
        </div>
        <div class="mb-3">
            <label for="duration" class="form-label">Duration</label>
            <input type="text" class="form-control" id="duration" name="duration" value="{{ old('duration', $training->duration) }}">
        </div>
        <div class="mb-3">
            <label for="provider" class="form-label">Provider</label>
            <input type="text" class="form-control" id="provider" name="provider" value="{{ old('provider', $training->provider) }}">
        </div>
        <div class="mb-3">
            <label for="location" class="form-label">Location</label>
            <input type="text" class="form-control" id="location" name="location" value="{{ old('location', $training->location) }}">
        </div>
        <div class="mb-3">
            <label for="customer_id" class="form-label">Organized by Customer (optional)</label>
            <select class="form-control" id="customer_id" name="customer_id">
                <option value="">-- None --</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}" {{ old('customer_id', $training->customer_id) == $customer->id ? 'selected' : '' }}>{{ $customer->first_name }} {{ $customer->last_name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('trainings.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
