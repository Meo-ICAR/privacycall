@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Authorization Requests</h1>
    <a href="{{ route('authorization-requests.create') }}" class="btn btn-primary mb-3">New Request</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Supplier</th>
                <th>Subsupplier</th>
                <th>Company</th>
                <th>Status</th>
                <th>Reviewed By</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($requests as $request)
                <tr>
                    <td>{{ $request->id }}</td>
                    <td>{{ $request->supplier->name ?? '-' }}</td>
                    <td>{{ $request->subsupplier->service_description ?? '-' }}</td>
                    <td>{{ $request->company->name ?? '-' }}</td>
                    <td>{{ ucfirst($request->status) }}</td>
                    <td>{{ $request->reviewer->name ?? '-' }}</td>
                    <td>
                        <a href="{{ route('authorization-requests.show', $request) }}" class="btn btn-sm btn-info">View</a>
                        @if($request->status === 'pending')
                        <form action="{{ route('authorization-requests.approve', $request) }}" method="POST" style="display:inline-block;">
                            @csrf
                            <button class="btn btn-sm btn-success" onclick="return confirm('Approve this request?')">Approve</button>
                        </form>
                        <form action="{{ route('authorization-requests.deny', $request) }}" method="POST" style="display:inline-block;">
                            @csrf
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Deny this request?')">Deny</button>
                        </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $requests->links() }}
</div>
@endsection
