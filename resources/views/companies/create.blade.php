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
                <form id="companyForm" class="space-y-6" enctype="multipart/form-data">
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
                        <input type="text" name="name" id="name" required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                               placeholder="Enter company name">
                    </div>

                    <!-- Company Type -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">Company Type</label>
                        <select name="type" id="type" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">Select company type</option>
                            <option value="employer">Employer</option>
                            <option value="customer">Customer</option>
                            <option value="supplier">Supplier</option>
                            <option value="partner">Partner</option>
                        </select>
                    </div>

                    <!-- GDPR Compliance -->
                    <div>
                        <div class="flex items-center">
                            <input type="checkbox" name="gdpr_compliant" id="gdpr_compliant" checked
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
                            <option value="1 year">1 year</option>
                            <option value="3 years">3 years</option>
                            <option value="5 years">5 years</option>
                            <option value="7 years" selected>7 years</option>
                            <option value="10 years">10 years</option>
                            <option value="indefinite">Indefinite (with consent)</option>
                        </select>
                    </div>

                    <!-- Contact Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                            <input type="email" name="email" id="email"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="contact@company.com">
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                            <input type="tel" name="phone" id="phone"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                   placeholder="+1 (555) 123-4567">
                        </div>
                    </div>

                    <!-- Address -->
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                        <textarea name="address" id="address" rows="3"
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                  placeholder="Enter company address"></textarea>
                    </div>

                    <!-- Additional Notes -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700">Additional Notes</label>
                        <textarea name="notes" id="notes" rows="3"
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                  placeholder="Any additional information about the company"></textarea>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-3">
                        <a href="/companies" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
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
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">GDPR Compliance Information</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>When you create a company in this system, it will be automatically configured with GDPR compliance features including:</p>
                        <ul class="list-disc list-inside mt-2 space-y-1">
                            <li>Consent management tracking</li>
                            <li>Data processing activity logging</li>
                            <li>Data subject rights management</li>
                            <li>Data retention policy enforcement</li>
                        </ul>
                    </div>
                </div>
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
        document.getElementById('companyForm').addEventListener('submit', function(e) {
            e.preventDefault();

            // Get form data
            const formData = new FormData(this);
            const data = Object.fromEntries(formData);

            // Show success message (in a real app, this would make an API call)
            alert('Company created successfully! (This is a demo - no actual API call made)');

            // Redirect to companies list
            window.location.href = '/companies';
        });

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
