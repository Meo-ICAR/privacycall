<h1>New Subsupplier Authorization Request</h1>
<p>A new authorization request has been submitted.</p>
<ul>
    <li><strong>Supplier:</strong> {{ $authorizationRequest->supplier->name ?? '-' }}</li>
    <li><strong>Subsupplier:</strong> {{ $authorizationRequest->subsupplier->service_description ?? '-' }}</li>
    <li><strong>Company:</strong> {{ $authorizationRequest->company->name ?? '-' }}</li>
    <li><strong>Justification:</strong> {{ $authorizationRequest->justification ?? '-' }}</li>
</ul>
<p>
    <a href="{{ route('authorization-requests.show', $authorizationRequest) }}">Review this request</a>
</p>
