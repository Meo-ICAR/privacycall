<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Company - PrivacyCall</title>
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
                    <a href="/companies" class="text-blue-600 font-medium">Companies</a>
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
                <a href="/companies" class="text-blue-600 hover:text-blue-800 mr-4">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Create New Company</h1>
                    <p class="mt-2 text-gray-600">Add a new company to your GDPR-compliant management system</p>
                </div>
            </div>
        </div>

        <!-- Company Form -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <form action="{{ route('companies.store') }}" method="POST" class="space-y-6" enctype="multipart/form-data">
                    @csrf
                    <!-- Logo Upload -->
                    <div>
                        <label for="logo" class="block text-sm font-medium text-gray-700">Company Logo</label>
                        <input type="file" name="logo" id="logo" accept="image/*"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <div class="mt-2">
                            <img id="logoPreview" src="#" alt="Logo Preview" class="h-24 w-24 object-contain rounded border border-gray-200 hidden" />
                        </div>
                    </div>

                    <!-- Company Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Company Name</label>
                        <input type="text" name="name" id="name" required value="{{ old('name') }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('name') border-red-500 @enderror"
                               placeholder="Enter company name">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Company Type -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">Company Type</label>
                        <select name="type" id="type" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('type') border-red-500 @enderror">
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

                    <!-- GDPR Compliance -->
                    <div>
                        <div class="flex items-center">
                            <input type="checkbox" name="gdpr_compliant" id="gdpr_compliant" value="1" {{ old('gdpr_compliant') ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="gdpr_compliant" class="ml-2 block text-sm text-gray-900">
                                GDPR Compliant
                            </label>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">This company follows GDPR data protection regulations</p>
                    </div>

                    <!-- Data Retention Policy -->
                    <div>
                        <label for="data_retention_policy" class="block text-sm font-medium text-gray-700">Data Retention Policy</label>
                        <select name="data_retention_policy" id="data_retention_policy" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">Select retention period</option>
                            <option value="1 year" {{ old('data_retention_policy') == '1 year' ? 'selected' : '' }}>1 year</option>
                            <option value="3 years" {{ old('data_retention_policy') == '3 years' ? 'selected' : '' }}>3 years</option>
                            <option value="5 years" {{ old('data_retention_policy') == '5 years' ? 'selected' : '' }}>5 years</option>
                            <option value="7 years" {{ old('data_retention_policy') == '7 years' || !old('data_retention_policy') ? 'selected' : '' }}>7 years</option>
                            <option value="10 years" {{ old('data_retention_policy') == '10 years' ? 'selected' : '' }}>10 years</option>
                            <option value="indefinite" {{ old('data_retention_policy') == 'indefinite' ? 'selected' : '' }}>Indefinite (with consent)</option>
                        </select>
                    </div>

                    <!-- Contact Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('email') border-red-500 @enderror"
                                   placeholder="contact@company.com">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                            <input type="tel" name="phone" id="phone" value="{{ old('phone') }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('phone') border-red-500 @enderror"
                                   placeholder="+1 (555) 123-4567">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Address -->
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                        <textarea name="address" id="address" rows="3"
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('address') border-red-500 @enderror"
                                  placeholder="Enter company address">{{ old('address') }}</textarea>
                        @error('address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Additional Notes -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700">Additional Notes</label>
                        <textarea name="notes" id="notes" rows="3"
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('notes') border-red-500 @enderror"
                                  placeholder="Any additional information about the company">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Holding -->
                    @if(auth()->user() && auth()->user()->hasRole('superadmin'))
                        <div class="mb-4">
                            <label for="holding_id" class="block text-sm font-medium text-gray-700">Holding</label>
                            <select name="holding_id" id="holding_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">-- None --</option>
                                @foreach(App\Models\Holding::all() as $holding)
                                    <option value="{{ $holding->id }}" {{ old('holding_id') == $holding->id ? 'selected' : '' }}>{{ $holding->name }}</option>
                                @endforeach
                            </select>
                            @error('holding_id')
                                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('companies.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
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

        <!-- GDPR Information -->
        <!-- (Removed demo/placeholder message. Only the form and relevant UI remain.) -->
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
