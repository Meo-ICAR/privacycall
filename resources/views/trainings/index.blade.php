@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Trainings</h1>
    <a href="{{ route('trainings.create') }}" class="btn btn-primary mb-3">Create Training</a>
    <table class="table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Type</th>
                <th>Date</th>
                <th>Provider</th>
                <th>Organized by Customer</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($trainings as $training)
                <tr>
                    <td>{{ $training->title }}</td>
                    <td>{{ ucfirst($training->type) }}</td>
                    <td>{{ $training->date }}</td>
                    <td>{{ $training->provider }}</td>
                    <td>{{ $training->customer ? $training->customer->first_name . ' ' . $training->customer->last_name : '-' }}</td>
                    <td>
                        <a href="{{ route('trainings.show', $training) }}" class="btn btn-info btn-sm">View</a>
                        <a href="{{ route('trainings.edit', $training) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('trainings.destroy', $training) }}" method="POST" style="display:inline-block">
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
