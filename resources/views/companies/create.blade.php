@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <div class="flex items-center">
            <a href="{{ route('companies.index') }}" class="text-blue-600 hover:text-blue-800 mr-4">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Create New Company</h1>
                <p class="mt-2 text-gray-600">Add a new company to your GDPR-compliant management system</p>
                @if($selectedHolding)
                    <div class="mt-2 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        <i class="fas fa-building mr-2"></i>
                        Creating for holding: {{ $selectedHolding->name }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form action="{{ route('companies.store') }}" method="POST" class="space-y-6" enctype="multipart/form-data">
                @csrf

                <!-- Company Logo -->
                <div>
                    <label for="logo" class="block text-sm font-medium text-gray-700">Company Logo</label>
                    <input type="file" name="logo" id="logo" accept="image/*" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <div class="mt-2">
                        <img id="logoPreview" src="#" alt="Logo Preview" class="h-24 w-24 object-contain rounded border border-gray-200 hidden" />
                    </div>
                    @error('logo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Administrator Signature -->
                <div>
                    <label for="signature" class="block text-sm font-medium text-gray-700">Administrator Signature</label>
                    <input type="file" name="signature" id="signature" accept="image/*" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <div class="mt-2">
                        <img id="signaturePreview" src="#" alt="Signature Preview" class="h-32 w-auto object-contain rounded border border-gray-200 hidden" />
                    </div>
                    @error('signature')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Basic Information -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Company Name *</label>
                        <input type="text" name="name" id="name" required value="{{ old('name') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Enter company name">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">Company Type *</label>
                        <select name="type" id="type" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">Select company type</option>
                            <option value="employer" {{ old('type') == 'employer' ? 'selected' : '' }}>Employer</option>
                            <option value="customer" {{ old('type') == 'customer' ? 'selected' : '' }}>Customer</option>
                            <option value="supplier" {{ old('type') == 'supplier' ? 'selected' : '' }}>Supplier</option>
                            <option value="partner" {{ old('type') == 'partner' ? 'selected' : '' }}>Partner</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Contact Information</h3>
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="contact@company.com">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                            <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="+1 (555) 123-4567">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Address Information -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Address Information</h3>
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                        <textarea name="address" id="address" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Enter company address">{{ old('address') }}</textarea>
                        @error('address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- GDPR Compliance -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">GDPR Compliance</h3>
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <input type="checkbox" name="gdpr_compliant" id="gdpr_compliant" value="1" {{ old('gdpr_compliant') ? 'checked' : '' }} class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="gdpr_compliant" class="ml-2 block text-sm text-gray-900">
                                GDPR Compliant
                            </label>
                        </div>
                        <p class="text-sm text-gray-500">This company follows GDPR data protection regulations</p>

                        <div>
                            <label for="data_retention_policy" class="block text-sm font-medium text-gray-700">Data Retention Policy</label>
                            <select name="data_retention_policy" id="data_retention_policy" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="">Select retention period</option>
                                <option value="1 year" {{ old('data_retention_policy') == '1 year' ? 'selected' : '' }}>1 year</option>
                                <option value="3 years" {{ old('data_retention_policy') == '3 years' ? 'selected' : '' }}>3 years</option>
                                <option value="5 years" {{ old('data_retention_policy') == '5 years' ? 'selected' : '' }}>5 years</option>
                                <option value="7 years" {{ old('data_retention_policy') == '7 years' || !old('data_retention_policy') ? 'selected' : '' }}>7 years</option>
                                <option value="10 years" {{ old('data_retention_policy') == '10 years' ? 'selected' : '' }}>10 years</option>
                                <option value="indefinite" {{ old('data_retention_policy') == 'indefinite' ? 'selected' : '' }}>Indefinite (with consent)</option>
                            </select>
                            @error('data_retention_policy')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Holding Information -->
                @if(auth()->user() && auth()->user()->hasRole('superadmin'))
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Holding Information</h3>
                    <div>
                        <label for="holding_id" class="block text-sm font-medium text-gray-700">Holding</label>
                        <select name="holding_id" id="holding_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">-- None --</option>
                            @if($holdings)
                                @foreach($holdings as $holding)
                                    <option value="{{ $holding->id }}" {{ (old('holding_id', $selectedHolding ? $selectedHolding->id : '') == $holding->id) ? 'selected' : '' }}>{{ $holding->name }}</option>
                                @endforeach
                            @endif
                        </select>
                        @error('holding_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                @endif

                <!-- Additional Notes -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Information</h3>
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700">Additional Notes</label>
                        <textarea name="notes" id="notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Any additional information about the company">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('companies.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <i class="fas fa-times mr-2"></i>
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <i class="fas fa-save mr-2"></i>
                        Create Company
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Logo preview functionality
    const logoInput = document.getElementById('logo');
    const logoPreview = document.getElementById('logoPreview');

    logoInput.addEventListener('change', function(e) {
        const [file] = logoInput.files;
        if (file) {
            logoPreview.src = URL.createObjectURL(file);
            logoPreview.classList.remove('hidden');
        } else {
            logoPreview.src = '#';
            logoPreview.classList.add('hidden');
        }
    });

    // Signature preview functionality
    const signatureInput = document.getElementById('signature');
    const signaturePreview = document.getElementById('signaturePreview');

    signatureInput.addEventListener('change', function(e) {
        const [file] = signatureInput.files;
        if (file) {
            signaturePreview.src = URL.createObjectURL(file);
            signaturePreview.classList.remove('hidden');
        } else {
            signaturePreview.src = '#';
            signaturePreview.classList.add('hidden');
        }
    });
</script>
@endsection
