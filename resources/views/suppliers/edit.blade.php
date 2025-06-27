<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Supplier - PrivacyCall</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <h1 class="text-xl font-bold text-gray-800">
                            <i class="fas fa-shield-alt text-blue-600 mr-2"></i>
                            PrivacyCall
                        </h1>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="/" class="text-gray-600 hover:text-gray-900">Home</a>
                    <a href="/dashboard" class="text-gray-600 hover:text-gray-900">Dashboard</a>
                    <a href="/companies" class="text-gray-600 hover:text-gray-900">Companies</a>
                    <a href="/suppliers" class="text-blue-600 font-medium">Suppliers</a>
                    <a href="/gdpr" class="text-gray-600 hover:text-gray-900">GDPR</a>
                    <a href="/api-docs" class="text-gray-600 hover:text-gray-900">API Docs</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-3xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center">
                <a href="/suppliers" class="text-blue-600 hover:text-blue-800 mr-4">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Edit Supplier</h1>
                    <p class="mt-2 text-gray-600">Update supplier details and logo</p>
                </div>
            </div>
        </div>

        <!-- Supplier Edit Form -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form id="supplierEditForm" class="space-y-6" enctype="multipart/form-data" method="POST" action="{{ route('suppliers.update', $supplier->id) }}">
                    @csrf
                    @method('PUT')
                    <!-- Logo Upload -->
                    <div>
                        <label for="logo" class="block text-sm font-medium text-gray-700">Supplier Logo</label>
                        <input type="file" name="logo" id="logo" accept="image/*"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <div class="mt-2">
                            <img id="logoPreview" src="{{ $supplier->logo_url ?? '#' }}" alt="Logo Preview" class="h-24 w-24 object-contain rounded border border-gray-200 @if(!$supplier->logo_url) hidden @endif" />
                        </div>
                    </div>
                    <!-- Supplier Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Supplier Name</label>
                        <input type="text" name="name" id="name" required value="{{ old('name', $supplier->name) }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               placeholder="Enter supplier name">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $supplier->email) }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               placeholder="Enter supplier email">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $supplier->phone) }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               placeholder="Enter supplier phone">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <!-- Address -->
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                        <textarea name="address" id="address" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                  placeholder="Enter supplier address">{{ old('address', $supplier->address) }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <!-- Company -->
                    <div>
                        <label for="company_id" class="block text-sm font-medium text-gray-700">Company</label>
                        <select name="company_id" id="company_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                            <option value="">Select Company</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ old('company_id', $supplier->company_id) == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                            @endforeach
                        </select>
                        @error('company_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <!-- Supplier Type -->
                    <div>
                        <label for="supplier_type_id" class="block text-sm font-medium text-gray-700">Supplier Type</label>
                        <select name="supplier_type_id" id="supplier_type_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                            <option value="">Select Type</option>
                            @foreach($supplierTypes as $type)
                                <option value="{{ $type->id }}" {{ old('supplier_type_id', $supplier->supplier_type_id) == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                            @endforeach
                        </select>
                        @error('supplier_type_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <!-- Active Status -->
                    <div>
                        <div class="flex items-center">
                            <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ old('is_active', $supplier->is_active) ? 'checked' : '' }}>
                            <label for="is_active" class="form-check-label ml-2">Active Supplier</label>
                        </div>
                    </div>
                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-3">
                        <a href="/suppliers" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Cancel
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            <i class="fas fa-save mr-2"></i>
                            Update Supplier
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="text-center text-sm text-gray-500">
                <p>&copy; 2024 PrivacyCall. Built with ❤️ for privacy and data protection.</p>
            </div>
        </div>
    </footer>

    <script>
        // Logo preview
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
    </script>
</body>
</html>
