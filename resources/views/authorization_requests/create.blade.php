@extends('layouts.app')

@section('content')
<div class="container">
    <h1>New Authorization Request</h1>
    <form action="{{ route('authorization-requests.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="supplier_id" class="form-label">Supplier</label>
            <select name="supplier_id" id="supplier_id" class="form-control" required>
                @foreach(App\Models\Supplier::all() as $supplier)
                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="subsupplier_id" class="form-label">Subsupplier</label>
            <select name="subsupplier_id" id="subsupplier_id" class="form-control" required>
                @foreach($subsuppliers as $subsupplier)
                    <option value="{{ $subsupplier->id }}">{{ $subsupplier->service_description }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="company_id" class="form-label">Company</label>
            <select name="company_id" id="company_id" class="form-control" required>
                @foreach(App\Models\Company::all() as $company)
                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="justification" class="form-label">Justification</label>
            <textarea name="justification" id="justification" class="form-control"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
@endsection
