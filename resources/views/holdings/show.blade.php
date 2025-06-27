@extends('layouts.app')

@section('content')
<div class="container">
    <div class="mb-4">
        <h1>Holding Details</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Basic Information</h5>
                    <table class="table table-borderless">
                        <tr>
                            <th>Name:</th>
                            <td>{{ $holding->name }}</td>
                        </tr>
                        <tr>
                            <th>Description:</th>
                            <td>{{ $holding->description ?? 'No description provided' }}</td>
                        </tr>
                        <tr>
                            <th>Created:</th>
                            <td>{{ $holding->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Last Updated:</th>
                            <td>{{ $holding->updated_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h5>Companies in this Holding</h5>
                    @if($holding->companies->count() > 0)
                        <ul class="list-group">
                            @foreach($holding->companies as $company)
                                <li class="list-group-item">
                                    <a href="{{ route('companies.show', $company->id) }}">{{ $company->name }}</a>
                                    <span class="badge bg-secondary">{{ $company->company_type }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">No companies assigned to this holding.</p>
                    @endif
                </div>
            </div>

            <div class="mt-4">
                <h5>Actions</h5>
                @if(auth()->user() && auth()->user()->hasRole('superadmin'))
                    <a href="{{ route('holdings.edit', $holding) }}" class="btn btn-warning">Edit</a>
                    <form action="{{ route('holdings.destroy', $holding) }}" method="POST" style="display:inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this holding?')">Delete</button>
                    </form>
                @endif
                <a href="{{ route('holdings.index') }}" class="btn btn-secondary">Back to List</a>
            </div>
        </div>
    </div>
</div>
@endsection
