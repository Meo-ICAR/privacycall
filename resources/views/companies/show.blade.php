@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">{{ $company->name }}</h1>
        <p class="mt-2 text-gray-600">Company details and information</p>
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

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Main Company Information -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Company Information</h3>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Company Name</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $company->name }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Company Type</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($company->company_type ?? 'Unknown') }}</dd>
                        </div>

                        @if($company->legal_name)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Legal Name</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $company->legal_name }}</dd>
                        </div>
                        @endif

                        @if($company->registration_number)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Registration Number</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $company->registration_number }}</dd>
                        </div>
                        @endif

                        @if($company->vat_number)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">VAT Number</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $company->vat_number }}</dd>
                        </div>
                        @endif

                        @if($company->email)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $company->email }}</dd>
                        </div>
                        @endif

                        @if($company->phone)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Phone</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $company->phone }}</dd>
                        </div>
                        @endif

                        @if($company->website)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Website</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <a href="{{ $company->website }}" target="_blank" class="text-blue-600 hover:text-blue-900">{{ $company->website }}</a>
                            </dd>
                        </div>
                        @endif

                        @if($company->industry)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Industry</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $company->industry }}</dd>
                        </div>
                        @endif

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Company Size</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($company->size ?? 'Unknown') }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $company->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $company->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </dd>
                        </div>
                    </div>

                    @if($company->address_line_1)
                    <div class="mt-6">
                        <dt class="text-sm font-medium text-gray-500">Address</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $company->address_line_1 }}<br>
                            @if($company->address_line_2){{ $company->address_line_2 }}<br>@endif
                            {{ $company->city }}{{ $company->state ? ', ' . $company->state : '' }} {{ $company->postal_code }}<br>
                            {{ $company->country }}
                        </dd>
                    </div>
                    @endif

                    @if($company->notes)
                    <div class="mt-6">
                        <dt class="text-sm font-medium text-gray-500">Notes</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $company->notes }}</dd>
                    </div>
                    @endif
                </div>
            </div>

            <!-- GDPR Compliance Section -->
            @if($company->gdpr_consent_date || $company->data_retention_period)
            <div class="bg-white shadow rounded-lg mt-6">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">GDPR Compliance</h3>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        @if($company->gdpr_consent_date)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">GDPR Consent Date</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $company->gdpr_consent_date->format('Y-m-d') }}</dd>
                        </div>
                        @endif

                        @if($company->data_retention_period)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Data Retention Period</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $company->data_retention_period }} years</dd>
                        </div>
                        @endif

                        @if($company->data_processing_purpose)
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Data Processing Purpose</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $company->data_processing_purpose }}</dd>
                        </div>
                        @endif

                        @if($company->data_controller_contact)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Data Controller Contact</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $company->data_controller_contact }}</dd>
                        </div>
                        @endif

                        @if($company->data_protection_officer)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Data Protection Officer</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $company->data_protection_officer }}</dd>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Company Logo -->
            @if($company->logo_url)
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Company Logo</h3>
                    <img src="{{ $company->logo_url }}" alt="{{ $company->name }} Logo" class="w-full h-32 object-contain rounded">
                </div>
            </div>
            @endif

            <!-- Administrator Signature -->
            @if($company->signature)
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Administrator Signature</h3>
                    <img src="{{ $company->signature }}" alt="Administrator Signature" class="w-full h-auto object-contain rounded border border-gray-200">
                </div>
            </div>
            @endif

            <!-- Holding Information -->
            @if($company->holding)
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Holding Information</h3>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Holding</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $company->holding->name }}</dd>
                    </div>
                </div>
            </div>
            @endif

            <!-- Statistics -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Statistics</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ $company->employees->count() }}</div>
                            <div class="text-sm text-gray-500">Employees</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">{{ $company->customers->count() }}</div>
                            <div class="text-sm text-gray-500">Customers</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-purple-600">{{ $company->suppliers->count() }}</div>
                            <div class="text-sm text-gray-500">Suppliers</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-orange-600">{{ $company->created_at->format('Y') }}</div>
                            <div class="text-sm text-gray-500">Founded</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Company Admins (Impersonation) -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Company Admins</h3>
                    @php
                        $admins = $company->users->filter(fn($u) => $u->hasRole('admin'));
                    @endphp
                    @if($admins->count())
                        <ul class="divide-y divide-gray-200">
                            @foreach($admins as $admin)
                                <li class="py-2 flex items-center justify-between">
                                    <div>
                                        <span class="font-medium text-gray-900">{{ $admin->name }}</span>
                                        <span class="block text-xs text-gray-500">{{ $admin->email }}</span>
                                    </div>
                                    @if(auth()->user()->hasRole('superadmin'))
                                    <form method="POST" action="{{ route('impersonate.start', $admin) }}" onsubmit="return confirm('Impersonate this admin?');">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center px-3 py-1 border border-blue-300 text-xs font-medium rounded-md text-blue-700 bg-blue-50 hover:bg-blue-100">
                                            <i class="fas fa-user-secret mr-1"></i> Impersonate
                                        </button>
                                    </form>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-gray-500">No admin users found for this company.</p>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Actions</h3>
                    <div class="space-y-3">
                        @if(auth()->check() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin')))
                        <a href="{{ route('companies.edit', $company) }}" class="w-full inline-flex items-center justify-center px-4 py-2 border border-yellow-300 text-sm font-medium rounded-md text-yellow-700 bg-white hover:bg-yellow-50">
                            <i class="fas fa-edit mr-2"></i>
                            Edit Company
                        </a>
                        @endif

                        <!-- Add Employee -->
                        @if(auth()->check() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin')))
                        <a href="{{ route('employees.create', ['company_id' => $company->id]) }}" class="w-full inline-flex items-center justify-center px-4 py-2 border border-blue-300 text-sm font-medium rounded-md text-blue-700 bg-white hover:bg-blue-50">
                            <i class="fas fa-user-plus mr-2"></i>
                            Add Employee
                        </a>
                        @endif

                        <!-- Add Customer -->
                        @if(auth()->check() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin')))
                        <a href="{{ route('customers.create', ['company_id' => $company->id]) }}" class="w-full inline-flex items-center justify-center px-4 py-2 border border-green-300 text-sm font-medium rounded-md text-green-700 bg-white hover:bg-green-50">
                            <i class="fas fa-user-tie mr-2"></i>
                            Add Customer
                        </a>
                        @endif

                        <!-- Add Supplier -->
                        @if(auth()->check() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin')))
                        <a href="{{ route('suppliers.create', ['company_id' => $company->id]) }}" class="w-full inline-flex items-center justify-center px-4 py-2 border border-purple-300 text-sm font-medium rounded-md text-purple-700 bg-white hover:bg-purple-50">
                            <i class="fas fa-truck mr-2"></i>
                            Add Supplier
                        </a>
                        @endif

                        <!-- Add Mandator -->
                        @if(auth()->check() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin')))
                        <a href="{{ route('mandators.create', ['company_id' => $company->id]) }}" class="w-full inline-flex items-center justify-center px-4 py-2 border border-purple-300 text-sm font-medium rounded-md text-purple-700 bg-white hover:bg-purple-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                            <i class="fas fa-plus mr-2"></i>
                            Add Mandator
                        </a>
                        @endif

                        <!-- Manage Emails -->
                        @if(auth()->check() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin')) && $company->data_controller_contact)
                        <a href="{{ route('companies.emails.index', $company) }}" class="w-full inline-flex items-center justify-center px-4 py-2 border border-indigo-300 text-sm font-medium rounded-md text-indigo-700 bg-white hover:bg-indigo-50">
                            <i class="fas fa-envelope mr-2"></i>
                            Manage Emails
                        </a>
                        @endif

                        <!-- Email Configuration -->
                        @if(auth()->check() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin')))
                        <a href="{{ route('companies.email-config.show', $company) }}" class="w-full inline-flex items-center justify-center px-4 py-2 border border-teal-300 text-sm font-medium rounded-md text-teal-700 bg-white hover:bg-teal-50">
                            <i class="fas fa-cog mr-2"></i>
                            @if($company->hasEmailConfigured())
                                Email Configuration
                            @else
                                Configure Email
                            @endif
                        </a>
                        @endif

                        <a href="{{ route('companies.index') }}" class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
