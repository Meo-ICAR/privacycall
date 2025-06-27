@extends('layouts.app')

@section('content')
<div class="container">
    <div class="mb-4">
        <h1>Employee Details</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Personal Information</h5>
                    <table class="table table-borderless">
                        <tr>
                            <th>Name:</th>
                            <td>{{ $employee->first_name }} {{ $employee->last_name }}</td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td>{{ $employee->email }}</td>
                        </tr>
                        <tr>
                            <th>Phone:</th>
                            <td>{{ $employee->phone ?? 'Not provided' }}</td>
                        </tr>
                        <tr>
                            <th>Address:</th>
                            <td>{{ $employee->address ?? 'Not provided' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h5>Employment Information</h5>
                    <table class="table table-borderless">
                        <tr>
                            <th>Company:</th>
                            <td>{{ $employee->company->name ?? 'Unknown' }}</td>
                        </tr>
                        <tr>
                            <th>Position:</th>
                            <td>{{ $employee->position }}</td>
                        </tr>
                        <tr>
                            <th>Hire Date:</th>
                            <td>{{ $employee->hire_date ? $employee->hire_date->format('Y-m-d') : 'Not set' }}</td>
                        </tr>
                        <tr>
                            <th>Salary:</th>
                            <td>{{ $employee->salary ? '$' . number_format($employee->salary, 2) : 'Not set' }}</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                <span class="badge {{ $employee->is_active ? 'bg-success' : 'bg-danger' }}">
                                    {{ $employee->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="mt-4">
                <h5>Actions</h5>
                <a href="{{ route('employees.edit', $employee) }}" class="btn btn-warning">Edit</a>
                <form action="{{ route('employees.destroy', $employee) }}" method="POST" style="display:inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this employee?')">Delete</button>
                </form>
                <a href="{{ route('employees.index') }}" class="btn btn-secondary">Back to List</a>
            </div>
        </div>
    </div>
</div>
@endsection
