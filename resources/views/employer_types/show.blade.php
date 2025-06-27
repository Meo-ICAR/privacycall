@extends('layouts.app')

@section('content')
<div class="container">
    <div class="mb-4">
        <h1>Employer Type Details</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Basic Information</h5>
                    <table class="table table-borderless">
                        <tr>
                            <th>Name:</th>
                            <td>{{ $employerType->name }}</td>
                        </tr>
                        <tr>
                            <th>Description:</th>
                            <td>{{ $employerType->description ?? 'No description provided' }}</td>
                        </tr>
                        <tr>
                            <th>Icon:</th>
                            <td>
                                @if($employerType->icon)
                                    @if(Str::startsWith($employerType->icon, 'fa'))
                                        <i class="{{ $employerType->icon }} text-xl"></i>
                                    @else
                                        <img src="{{ $employerType->icon }}" alt="icon" class="h-6 w-6 object-contain inline-block" />
                                    @endif
                                @else
                                    No icon
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Created:</th>
                            <td>{{ $employerType->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Last Updated:</th>
                            <td>{{ $employerType->updated_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="mt-4">
                <h5>Actions</h5>
                @if(auth()->user() && auth()->user()->hasRole('superadmin'))
                    <a href="{{ route('employer-types.edit', $employerType) }}" class="btn btn-warning">Edit</a>
                    <form action="{{ route('employer-types.destroy', $employerType) }}" method="POST" style="display:inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this employer type?')">Delete</button>
                    </form>
                @endif
                <a href="{{ route('employer-types.index') }}" class="btn btn-secondary">Back to List</a>
            </div>
        </div>
    </div>
</div>
@endsection
