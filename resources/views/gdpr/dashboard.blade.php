<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GDPR Dashboard - PrivacyCall</title>
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
                    <a href="/gdpr" class="text-blue-600 font-medium">GDPR</a>
                    <a href="/api-docs" class="text-gray-600 hover:text-gray-900">API Docs</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">GDPR Compliance Center</h1>
            <p class="mt-2 text-gray-600">Manage data protection and privacy compliance</p>
        </div>

        <!-- GDPR Features Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <!-- Consent Management -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-clipboard-check text-green-600 text-3xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-lg font-medium text-gray-900">Consent Management</dt>
                                <dd class="text-sm text-gray-500 mt-1">Track and manage data processing consent</dd>
                            </dl>
                        </div>
                    </div>
                    <div class="mt-6">
                        <a href="/gdpr/consent-management" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                            Manage Consent
                        </a>
                    </div>
                </div>
            </div>

            <!-- Data Processing Activities -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-database text-purple-600 text-3xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-lg font-medium text-gray-900">Processing Activities</dt>
                                <dd class="text-sm text-gray-500 mt-1">Register of data processing activities</dd>
                            </dl>
                        </div>
                    </div>
                    <div class="mt-6">
                        <a href="/gdpr/data-processing-activities" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700">
                            View Activities
                        </a>
                    </div>
                </div>
            </div>

            <!-- Data Subject Rights -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-user-shield text-orange-600 text-3xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-lg font-medium text-gray-900">Data Subject Rights</dt>
                                <dd class="text-sm text-gray-500 mt-1">Handle data subject requests</dd>
                            </dl>
                        </div>
                    </div>
                    <div class="mt-6">
                        <a href="/gdpr/data-subject-rights" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700">
                            Manage Rights
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Compliance Status -->
        <div class="bg-white shadow rounded-lg mb-8">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">GDPR Compliance Status</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Consent Management</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check mr-1"></i>
                                Compliant
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Data Processing Register</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check mr-1"></i>
                                Compliant
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Data Subject Rights</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check mr-1"></i>
                                Compliant
                            </span>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Data Retention</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check mr-1"></i>
                                Compliant
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Data Breach Notification</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check mr-1"></i>
                                Compliant
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Privacy by Design</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check mr-1"></i>
                                Compliant
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- API Endpoints -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">GDPR API Endpoints</h3>
                <div class="bg-gray-50 rounded-md p-4">
                    <div class="space-y-3 text-sm">
                        <div class="flex items-center">
                            <span class="font-mono bg-blue-100 text-blue-800 px-2 py-1 rounded">POST</span>
                            <span class="ml-2 font-mono text-gray-700">/api/v1/gdpr/right-to-be-forgotten</span>
                            <span class="ml-2 text-gray-500">- Request data deletion</span>
                        </div>
                        <div class="flex items-center">
                            <span class="font-mono bg-blue-100 text-blue-800 px-2 py-1 rounded">POST</span>
                            <span class="ml-2 font-mono text-gray-700">/api/v1/gdpr/data-portability</span>
                            <span class="ml-2 text-gray-500">- Request data export</span>
                        </div>
                        <div class="flex items-center">
                            <span class="font-mono bg-green-100 text-green-800 px-2 py-1 rounded">GET</span>
                            <span class="ml-2 font-mono text-gray-700">/api/v1/gdpr/consent-records</span>
                            <span class="ml-2 text-gray-500">- List consent records</span>
                        </div>
                        <div class="flex items-center">
                            <span class="font-mono bg-green-100 text-green-800 px-2 py-1 rounded">GET</span>
                            <span class="ml-2 font-mono text-gray-700">/api/v1/gdpr/processing-activities</span>
                            <span class="ml-2 text-gray-500">- List processing activities</span>
                        </div>
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
