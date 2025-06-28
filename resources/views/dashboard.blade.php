<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Section -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                <div class="p-6 lg:p-8 bg-white border-b border-gray-200">
                    <div class="flex items-center">
                        <i class="fas fa-shield-alt text-blue-600 text-3xl mr-4"></i>
                        <div>
                            <h1 class="text-2xl font-medium text-gray-900">
                                Welcome to PrivacyCall, {{ Auth::user()->name }}!
                            </h1>
                            <p class="mt-2 text-gray-500">
                                GDPR Compliant Company Management System
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                @if(Auth::user()->company)
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-building text-blue-600 text-2xl"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-500">Company</div>
                                    <div class="text-lg font-semibold text-gray-900">{{ Auth::user()->company->name }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-users text-green-600 text-2xl"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-500">Employees</div>
                                    <div class="text-lg font-semibold text-gray-900">{{ Auth::user()->company->employees()->count() }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-handshake text-purple-600 text-2xl"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-500">Suppliers</div>
                                    <div class="text-lg font-semibold text-gray-900">{{ Auth::user()->company->suppliers()->count() }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-user-tie text-orange-600 text-2xl"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-500">Customers</div>
                                    <div class="text-lg font-semibold text-gray-900">{{ Auth::user()->company->customers()->count() }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif(!Auth::user()->hasRole('superadmin'))
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg col-span-full">
                        <div class="p-6">
                            <div class="text-center">
                                <i class="fas fa-info-circle text-blue-600 text-3xl mb-4"></i>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">No Company Associated</h3>
                                <p class="text-gray-500">Your account is not associated with any company. Please contact your administrator.</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Quick Actions -->
            @if(Auth::user()->company)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
                        <div class="space-y-3">
                            @role('admin')
                                <a href="{{ route('employees.index') }}" class="flex items-center p-3 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                                    <i class="fas fa-users text-blue-600 mr-3"></i>
                                    <span>Manage Employees</span>
                                </a>
                                <a href="{{ route('suppliers.index') }}" class="flex items-center p-3 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                                    <i class="fas fa-handshake text-green-600 mr-3"></i>
                                    <span>Manage Suppliers</span>
                                </a>
                                <a href="{{ route('customers.index') }}" class="flex items-center p-3 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                                    <i class="fas fa-user-tie text-purple-600 mr-3"></i>
                                    <span>Manage Customers</span>
                                </a>
                                @if(auth()->user()->company && auth()->user()->company->data_controller_contact)
                                    <a href="{{ route('companies.emails.index', auth()->user()->company) }}" class="flex items-center p-3 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                                        <i class="fas fa-envelope text-orange-600 mr-3"></i>
                                        <span>Email Management</span>
                                    </a>
                                @endif
                            @endrole
                            @role('superadmin')
                                <a href="{{ route('companies.index') }}" class="flex items-center p-3 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                                    <i class="fas fa-building text-blue-600 mr-3"></i>
                                    <span>Manage Companies</span>
                                </a>
                                <a href="{{ route('emails.dashboard') }}" class="flex items-center p-3 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                                    <i class="fas fa-envelope text-green-600 mr-3"></i>
                                    <span>Email Dashboard</span>
                                </a>
                                <a href="{{ route('holdings.index') }}" class="flex items-center p-3 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                                    <i class="fas fa-sitemap text-purple-600 mr-3"></i>
                                    <span>Manage Holdings</span>
                                </a>
                            @endrole
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">GDPR Compliance</h3>
                        <div class="space-y-3">
                            <a href="{{ route('consent-records.index') }}" class="flex items-center p-3 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                                <i class="fas fa-clipboard-check text-blue-600 mr-3"></i>
                                <span>Consent Records</span>
                            </a>
                            <a href="{{ route('data-processing-activities.index') }}" class="flex items-center p-3 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                                <i class="fas fa-database text-green-600 mr-3"></i>
                                <span>Processing Activities</span>
                            </a>
                            <a href="{{ route('audit-requests.index') }}" class="flex items-center p-3 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                                <i class="fas fa-search text-purple-600 mr-3"></i>
                                <span>Audit Requests</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Training & Inspections</h3>
                        <div class="space-y-3">
                            <a href="{{ route('trainings.index') }}" class="flex items-center p-3 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                                <i class="fas fa-graduation-cap text-blue-600 mr-3"></i>
                                <span>Training Programs</span>
                            </a>
                            <a href="{{ route('employee-training.index') }}" class="flex items-center p-3 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                                <i class="fas fa-user-graduate text-green-600 mr-3"></i>
                                <span>Employee Training</span>
                            </a>
                            <a href="{{ route('inspections.index') }}" class="flex items-center p-3 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                                <i class="fas fa-clipboard-list text-purple-600 mr-3"></i>
                                <span>Inspections</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
