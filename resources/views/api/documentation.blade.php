<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Documentation - PrivacyCall</title>
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
                    <a href="/gdpr" class="text-gray-600 hover:text-gray-900">GDPR</a>
                    <a href="/api-docs" class="text-blue-600 font-medium">API Docs</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">API Documentation</h1>
            <p class="mt-2 text-gray-600">Complete API reference for the GDPR-compliant company management system</p>
        </div>

        <!-- Base URL -->
        <div class="bg-white shadow rounded-lg mb-8">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Base URL</h3>
                <div class="bg-gray-50 rounded-md p-4">
                    <code class="text-sm font-mono text-gray-700">https://your-domain.com/api/v1</code>
                </div>
            </div>
        </div>

        <!-- Authentication -->
        <div class="bg-white shadow rounded-lg mb-8">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Authentication</h3>
                <p class="text-sm text-gray-600 mb-4">All API requests require authentication using Bearer tokens.</p>
                <div class="bg-gray-50 rounded-md p-4">
                    <code class="text-sm font-mono text-gray-700">Authorization: Bearer YOUR_API_TOKEN</code>
                </div>
            </div>
        </div>

        <!-- Company Endpoints -->
        <div class="bg-white shadow rounded-lg mb-8">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Company Management</h3>

                <!-- List Companies -->
                <div class="mb-6">
                    <div class="flex items-center mb-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mr-3">GET</span>
                        <span class="font-mono text-sm text-gray-700">/companies</span>
                    </div>
                    <p class="text-sm text-gray-600 mb-2">Retrieve a list of all companies</p>
                    <div class="bg-gray-50 rounded-md p-3">
                        <p class="text-xs text-gray-500 mb-1">Response:</p>
                        <pre class="text-xs text-gray-700"><code>{
  "data": [
    {
      "id": 1,
      "name": "Example Corp",
      "type": "employer",
      "gdpr_compliant": true,
      "created_at": "2024-01-01T00:00:00Z"
    }
  ]
}</code></pre>
                    </div>
                </div>

                <!-- Create Company -->
                <div class="mb-6">
                    <div class="flex items-center mb-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-3">POST</span>
                        <span class="font-mono text-sm text-gray-700">/companies</span>
                    </div>
                    <p class="text-sm text-gray-600 mb-2">Create a new company</p>
                    <div class="bg-gray-50 rounded-md p-3">
                        <p class="text-xs text-gray-500 mb-1">Request Body:</p>
                        <pre class="text-xs text-gray-700"><code>{
  "name": "New Company",
  "type": "employer",
  "gdpr_compliant": true,
  "data_retention_policy": "7 years"
}</code></pre>
                    </div>
                </div>

                <!-- Get Company -->
                <div class="mb-6">
                    <div class="flex items-center mb-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mr-3">GET</span>
                        <span class="font-mono text-sm text-gray-700">/companies/{id}</span>
                    </div>
                    <p class="text-sm text-gray-600 mb-2">Retrieve a specific company</p>
                </div>
            </div>
        </div>

        <!-- GDPR Endpoints -->
        <div class="bg-white shadow rounded-lg mb-8">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">GDPR Compliance</h3>

                <!-- Right to be Forgotten -->
                <div class="mb-6">
                    <div class="flex items-center mb-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-3">POST</span>
                        <span class="font-mono text-sm text-gray-700">/gdpr/right-to-be-forgotten</span>
                    </div>
                    <p class="text-sm text-gray-600 mb-2">Request data deletion for a data subject</p>
                    <div class="bg-gray-50 rounded-md p-3">
                        <p class="text-xs text-gray-500 mb-1">Request Body:</p>
                        <pre class="text-xs text-gray-700"><code>{
  "data_subject_id": "user123",
  "reason": "User requested deletion",
  "company_id": 1
}</code></pre>
                    </div>
                </div>

                <!-- Data Portability -->
                <div class="mb-6">
                    <div class="flex items-center mb-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-3">POST</span>
                        <span class="font-mono text-sm text-gray-700">/gdpr/data-portability</span>
                    </div>
                    <p class="text-sm text-gray-600 mb-2">Request data export for a data subject</p>
                    <div class="bg-gray-50 rounded-md p-3">
                        <p class="text-xs text-gray-500 mb-1">Request Body:</p>
                        <pre class="text-xs text-gray-700"><code>{
  "data_subject_id": "user123",
  "format": "json",
  "company_id": 1
}</code></pre>
                    </div>
                </div>

                <!-- Consent Records -->
                <div class="mb-6">
                    <div class="flex items-center mb-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mr-3">GET</span>
                        <span class="font-mono text-sm text-gray-700">/gdpr/consent-records</span>
                    </div>
                    <p class="text-sm text-gray-600 mb-2">Retrieve consent records for data subjects</p>
                </div>

                <!-- Processing Activities -->
                <div class="mb-6">
                    <div class="flex items-center mb-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mr-3">GET</span>
                        <span class="font-mono text-sm text-gray-700">/gdpr/processing-activities</span>
                    </div>
                    <p class="text-sm text-gray-600 mb-2">Retrieve data processing activities register</p>
                </div>
            </div>
        </div>

        <!-- Error Responses -->
        <div class="bg-white shadow rounded-lg mb-8">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Error Responses</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-sm font-medium text-gray-900">400 Bad Request</p>
                        <p class="text-sm text-gray-600">Invalid request parameters</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">401 Unauthorized</p>
                        <p class="text-sm text-gray-600">Invalid or missing authentication</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">403 Forbidden</p>
                        <p class="text-sm text-gray-600">Insufficient permissions</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">404 Not Found</p>
                        <p class="text-sm text-gray-600">Resource not found</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">422 Unprocessable Entity</p>
                        <p class="text-sm text-gray-600">Validation errors</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rate Limiting -->
        <div class="bg-white shadow rounded-lg mb-8">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Rate Limiting</h3>
                <p class="text-sm text-gray-600 mb-4">API requests are limited to 1000 requests per hour per API key.</p>
                <div class="bg-gray-50 rounded-md p-4">
                    <p class="text-xs text-gray-500 mb-1">Rate limit headers:</p>
                    <pre class="text-xs text-gray-700"><code>X-RateLimit-Limit: 1000
X-RateLimit-Remaining: 999
X-RateLimit-Reset: 1640995200</code></pre>
                </div>
            </div>
        </div>

        <!-- SDKs and Libraries -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">SDKs and Libraries</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="border border-gray-200 rounded-md p-4">
                        <h4 class="font-medium text-gray-900 mb-2">PHP</h4>
                        <p class="text-sm text-gray-600 mb-2">Official PHP SDK</p>
                        <code class="text-xs text-gray-700">composer require privacycall/php-sdk</code>
                    </div>
                    <div class="border border-gray-200 rounded-md p-4">
                        <h4 class="font-medium text-gray-900 mb-2">JavaScript</h4>
                        <p class="text-sm text-gray-600 mb-2">Official JavaScript SDK</p>
                        <code class="text-xs text-gray-700">npm install @privacycall/js-sdk</code>
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
</body>
</html>
