@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Authorization Request #{{ $authorizationRequest->id }}</h1>
    <ul>
        <li><strong>Supplier:</strong> {{ $authorizationRequest->supplier->name ?? '-' }}</li>
        <li><strong>Subsupplier:</strong> {{ $authorizationRequest->subsupplier->service_description ?? '-' }}</li>
        <li><strong>Company:</strong> {{ $authorizationRequest->company->name ?? '-' }}</li>
        <li><strong>Status:</strong> {{ ucfirst($authorizationRequest->status) }}</li>
        <li><strong>Justification:</strong> {{ $authorizationRequest->justification ?? '-' }}</li>
        <li><strong>Review Notes:</strong> {{ $authorizationRequest->review_notes ?? '-' }}</li>
        <li><strong>Reviewed By:</strong> {{ $authorizationRequest->reviewer->name ?? '-' }}</li>
        <li><strong>Reviewed At:</strong> {{ $authorizationRequest->reviewed_at ?? '-' }}</li>
    </ul>
    @if($authorizationRequest->status === 'pending')
    <form action="{{ route('authorization-requests.approve', $authorizationRequest) }}" method="POST" style="display:inline-block;">
        @csrf
        <div class="mb-3">
            <label for="review_notes" class="form-label">Review Notes</label>
            <textarea name="review_notes" id="review_notes" class="form-control"></textarea>
        </div>
        <button class="btn btn-success" onclick="return confirm('Approve this request?')">Approve</button>
    </form>
    <form action="{{ route('authorization-requests.deny', $authorizationRequest) }}" method="POST" style="display:inline-block;">
        @csrf
        <div class="mb-3">
            <label for="review_notes" class="form-label">Review Notes</label>
            <textarea name="review_notes" id="review_notes" class="form-control"></textarea>
        </div>
        <button class="btn btn-danger" onclick="return confirm('Deny this request?')">Deny</button>
    </form>
    @endif
    <a href="{{ route('authorization-requests.index') }}" class="btn btn-secondary mt-3">Back to List</a>
</div>
@endsection
