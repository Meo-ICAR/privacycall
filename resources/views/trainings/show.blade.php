@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ $training->title }}</h1>
    <p><strong>Description:</strong> {{ $training->description }}</p>
    <p><strong>Type:</strong> {{ ucfirst($training->type) }}</p>
    <p><strong>Date:</strong> {{ $training->date }}</p>
    <p><strong>Duration:</strong> {{ $training->duration }}</p>
    <p><strong>Provider:</strong> {{ $training->provider }}</p>
    <p><strong>Location:</strong> {{ $training->location }}</p>
    <p><strong>Organized by Customer:</strong> {{ $training->customer ? $training->customer->first_name . ' ' . $training->customer->last_name : '-' }}</p>
    <a href="{{ route('trainings.edit', $training) }}" class="btn btn-warning">Edit</a>
    <a href="{{ route('trainings.index') }}" class="btn btn-secondary">Back to List</a>
</div>
@endsection
