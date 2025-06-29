<?php

namespace App\Http\Controllers;

use App\Models\AuthorizationRequest;
use App\Models\Subsupplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthorizationRequestController extends Controller
{
    public function index()
    {
        $requests = AuthorizationRequest::with(['supplier', 'subsupplier', 'company', 'reviewer'])->latest()->paginate(20);
        return view('authorization_requests.index', compact('requests'));
    }

    public function create()
    {
        $subsuppliers = Subsupplier::all();
        return view('authorization_requests.create', compact('subsuppliers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'subsupplier_id' => 'required|exists:subsuppliers,id',
            'company_id' => 'required|exists:companies,id',
            'justification' => 'nullable|string',
        ]);
        $data['status'] = 'pending';
        $authorizationRequest = AuthorizationRequest::create($data);
        // TODO: Send email notification
        return redirect()->route('authorization-requests.index')->with('success', 'Authorization request created.');
    }

    public function show(AuthorizationRequest $authorizationRequest)
    {
        return view('authorization_requests.show', compact('authorizationRequest'));
    }

    public function approve(Request $request, AuthorizationRequest $authorizationRequest)
    {
        $authorizationRequest->update([
            'status' => 'approved',
            'review_notes' => $request->input('review_notes'),
            'reviewed_at' => now(),
            'reviewed_by' => Auth::id(),
        ]);
        // TODO: Send approval email
        return redirect()->route('authorization-requests.index')->with('success', 'Request approved.');
    }

    public function deny(Request $request, AuthorizationRequest $authorizationRequest)
    {
        $authorizationRequest->update([
            'status' => 'denied',
            'review_notes' => $request->input('review_notes'),
            'reviewed_at' => now(),
            'reviewed_by' => Auth::id(),
        ]);
        // TODO: Send denial email
        return redirect()->route('authorization-requests.index')->with('success', 'Request denied.');
    }
}
