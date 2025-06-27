@extends('layouts.app')

@section('content')
<div class="container">
    <div class="mb-4">
        <h1>Upload Document</h1>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label for="document_type_id" class="form-label">Document Type</label>
                    <select name="document_type_id" id="document_type_id" class="form-control @error('document_type_id') is-invalid @enderror" required>
                        <option value="">Select Document Type</option>
                        @foreach($documentTypes as $type)
                            <option value="{{ $type->id }}" {{ old('document_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('document_type_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="file" class="form-label">File</label>
                    <input type="file" name="file" id="file" class="form-control @error('file') is-invalid @enderror" required>
                    @error('file')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="documentable_type" class="form-label">Related To</label>
                    <select name="documentable_type" id="documentable_type" class="form-control @error('documentable_type') is-invalid @enderror">
                        <option value="">Select Type</option>
                        <option value="App\Models\Company" {{ old('documentable_type') == 'App\Models\Company' ? 'selected' : '' }}>Company</option>
                        <option value="App\Models\Employee" {{ old('documentable_type') == 'App\Models\Employee' ? 'selected' : '' }}>Employee</option>
                        <option value="App\Models\Customer" {{ old('documentable_type') == 'App\Models\Customer' ? 'selected' : '' }}>Customer</option>
                        <option value="App\Models\Supplier" {{ old('documentable_type') == 'App\Models\Supplier' ? 'selected' : '' }}>Supplier</option>
                    </select>
                    @error('documentable_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="documentable_id" class="form-label">Select Item</label>
                    <select name="documentable_id" id="documentable_id" class="form-control @error('documentable_id') is-invalid @enderror">
                        <option value="">Select Item</option>
                    </select>
                    @error('documentable_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('documents.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Upload Document</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('documentable_type').addEventListener('change', function() {
    const type = this.value;
    const idSelect = document.getElementById('documentable_id');
    idSelect.innerHTML = '<option value="">Select Item</option>';

    if (type) {
        fetch(`/api/${type.split('\\').pop().toLowerCase()}s`)
            .then(response => response.json())
            .then(data => {
                data.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.id;
                    option.textContent = item.name || item.id;
                    idSelect.appendChild(option);
                });
            });
    }
});
</script>
@endsection
