<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 mb-2">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-mark class="block h-9 w-auto" />
                    </a>
                </div>
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex items-center">
                    @role('admin')
                        @php
                            $company = auth()->user()->company;
                        @endphp
                        <div class="flex items-center mr-4">
                            <a href="{{ route('companies.edit', $company) }}" title="Edit company info & email provider">
                                @if($company && $company->logo_url)
                                    <img src="{{ $company->logo_url }}" alt="{{ $company->name }}" class="h-8 w-8 rounded-full object-cover mr-2">
                                @elseif($company)
                                    <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center mr-2">
                                        <span class="text-xs font-bold text-gray-700">{{ Str::limit($company->name, 10, '') }}</span>
                                    </div>
                                @endif
                            </a>
                        </div>
                        <!-- People Dropdown -->
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="inline-flex items-center px-3 py-2 text-gray-700 hover:text-gray-900 focus:outline-none">
                                <i class="fas fa-users mr-2"></i>{{ __('People') }}
                                <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </button>
                            <div x-show="open" @click.away="open = false" class="absolute left-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                <div class="py-1">
                                    <x-nav-link href="{{ route('employees.index') }}" :active="request()->routeIs('employees.index')">
                                        <i class="fas fa-users mr-2"></i>{{ __('Employees') }}
                                    </x-nav-link>
                                    <x-nav-link href="{{ route('customers.index') }}" :active="request()->routeIs('customers.index')">
                                        <i class="fas fa-user-tie mr-2"></i>{{ __('Customers') }}
                                    </x-nav-link>
                                    <x-nav-link href="{{ route('suppliers.index') }}" :active="request()->routeIs('suppliers.index')">
                                        <i class="fas fa-handshake mr-2"></i>{{ __('Suppliers') }}
                                    </x-nav-link>
                                    <x-nav-link href="{{ route('mandators.index') }}" :active="request()->routeIs('mandators.index')">
                                        <i class="fas fa-user-shield mr-2"></i>{{ __('Mandators') }}
                                    </x-nav-link>
                                </div>
                            </div>
                        </div>
                        <!-- Activities Dropdown -->
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="inline-flex items-center px-3 py-2 text-gray-700 hover:text-gray-900 focus:outline-none">
                                <i class="fas fa-tasks mr-2"></i>{{ __('Activities') }}
                                <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </button>
                            <div x-show="open" @click.away="open = false" class="absolute left-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                <div class="py-1">
                                    <x-nav-link href="{{ route('trainings.index') }}" :active="request()->routeIs('trainings.index')">
                                        <i class="fas fa-graduation-cap mr-2"></i>{{ __('Trainings') }}
                                    </x-nav-link>
                                    <x-nav-link href="{{ route('data-processing-activities.index') }}" :active="request()->routeIs('data-processing-activities.index')">
                                        <i class="fas fa-database mr-2"></i>{{ __('Data Processing Activities') }}
                                    </x-nav-link>
                                    <x-nav-link href="{{ route('data-protection-i-as.index') }}" :active="request()->routeIs('data-protection-i-as.index')">
                                        <i class="fas fa-shield-alt mr-2"></i>{{ __('Data Protection IAs') }}
                                    </x-nav-link>
                                </div>
                            </div>
                        </div>
                        <!-- Compliance Dropdown -->
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="inline-flex items-center px-3 py-2 text-gray-700 hover:text-gray-900 focus:outline-none">
                                <i class="fas fa-clipboard-check mr-2"></i>{{ __('Compliance') }}
                                <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </button>
                            <div x-show="open" @click.away="open = false" class="absolute left-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                <div class="py-1">
                                    <x-nav-link href="{{ route('compliance-requests.index') }}" :active="request()->routeIs('compliance-requests.index')">
                                        <i class="fas fa-clipboard-check mr-2"></i>{{ __('Compliance Requests') }}
                                    </x-nav-link>
                                    <x-nav-link href="{{ route('consent-records.index') }}" :active="request()->routeIs('consent-records.index')">
                                        <i class="fas fa-clipboard-list mr-2"></i>{{ __('Consent Records') }}
                                    </x-nav-link>
                                    <x-nav-link href="{{ route('data-breaches.index') }}" :active="request()->routeIs('data-breaches.index')">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>{{ __('Data Breaches') }}
                                    </x-nav-link>
                                </div>
                            </div>
                        </div>
                        <!-- Communication Dropdown -->
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="inline-flex items-center px-3 py-2 text-gray-700 hover:text-gray-900 focus:outline-none">
                                <i class="fas fa-envelope mr-2"></i>{{ __('Communication') }}
                                <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </button>
                            <div x-show="open" @click.away="open = false" class="absolute left-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                <div class="py-1">
                                    <x-nav-link href="{{ route('companies.emails.index', auth()->user()->company) }}" :active="request()->routeIs('companies.emails.index')">
                                        <i class="fas fa-envelope mr-2"></i>{{ __('Company Emails') }}
                                    </x-nav-link>
                                    <x-nav-link href="{{ route('email-logs.index') }}" :active="request()->routeIs('email-logs.index')">
                                        <i class="fas fa-envelope-open-text mr-2"></i>{{ __('Email Logs') }}
                                    </x-nav-link>
                                </div>
                            </div>
                        </div>
                    @endrole
                    @role('superadmin')
                    <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                        <x-nav-link href="{{ route('companies.index') }}" :active="request()->routeIs('companies.index')">
                            <i class="fas fa-building mr-2"></i>{{ __('Companies') }}
                        </x-nav-link>


                         <!-- Settings Dropdown -->
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="inline-flex items-center px-3 py-2 text-gray-700 hover:text-gray-900 focus:outline-none">
                                <i class="fas fa-gears mr-2"></i>{{ __('Settings') }}
                                <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </button>
                            <div x-show="open" @click.away="open = false" class="absolute left-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                <div class="py-1">
                                       <x-nav-link href="{{ route('holdings.index') }}" :active="request()->routeIs('holdings.index')">
                        <i class="fas fa-sitemap mr-2"></i>{{ __('Holdings') }}
                    </x-nav-link>
                     <x-nav-link href="{{ route('email-templates.index') }}" :active="request()->routeIs('email-templates.index')">
                            <i class="fas fa-envelope mr-2"></i>{{ __('Email Templates') }}
                        </x-nav-link>
                         <x-nav-link href="{{ route('roles.permissions.index') }}" :active="request()->routeIs('roles.permissions.index')">
                            <i class="fas fa-user-shield mr-2"></i>{{ __('Roles & Permissions') }}
                        </x-nav-link>
                         <x-nav-link href="{{ route('email-providers.index') }}" :active="request()->routeIs('email-providers.index')">
                        <i class="fas fa-envelope-open-text mr-2"></i>{{ __('Email Providers') }}
                    </x-nav-link>
                     <x-nav-link href="{{ route('admin.test-email.form') }}" :active="request()->routeIs('admin.test-email.form')">
                        <i class="fas fa-envelope mr-2"></i>{{ __('Test email') }}
                    </x-nav-link>
                                </div>
                            </div>
                        </div>
                        <!-- GDPR Types Dropdown -->
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="inline-flex items-center px-3 py-2 text-gray-700 hover:text-gray-900 focus:outline-none">
                                <i class="fas fa-lock mr-2"></i>{{ __('GDPR Types') }}
                                <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </button>
                            <div x-show="open" @click.away="open = false" class="absolute left-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                <div class="py-1">
                                    <x-nav-link href="{{ route('security-measures.index') }}" :active="request()->routeIs('security-measures.index')">
                                        <i class="fas fa-shield-alt mr-2"></i>{{ __('Security Measures') }}
                                    </x-nav-link>
                                    <x-nav-link href="{{ route('disclosure-types.index') }}" :active="request()->routeIs('disclosure-types.index')">
                                        <i class="fas fa-bullhorn mr-2"></i>{{ __('Disclosure Types') }}
                                    </x-nav-link>
                                    <x-nav-link href="{{ route('legal-basis-types.index') }}" :active="request()->routeIs('legal-basis-types.index')">
                                        <i class="fas fa-gavel mr-2"></i>{{ __('Legal Basis Types') }}
                                    </x-nav-link>
                                </div>
                            </div>
                        </div>
                        <!-- Types Dropdown -->
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="inline-flex items-center px-3 py-2 text-gray-700 hover:text-gray-900 focus:outline-none">
                                <i class="fas fa-list mr-2"></i>{{ __('Types') }}
                                <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                            </button>
                            <div x-show="open" @click.away="open = false" class="absolute left-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                <div class="py-1">
                    <x-nav-link href="{{ route('employer-types.index') }}" :active="request()->routeIs('employer-types.index')">
                        <i class="fas fa-briefcase mr-2"></i>{{ __('Employer Types') }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('customer-types.index') }}" :active="request()->routeIs('customer-types.index')">
                        <i class="fas fa-user-tag mr-2"></i>{{ __('Customer Types') }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('supplier-types.index') }}" :active="request()->routeIs('supplier-types.index')">
                        <i class="fas fa-truck mr-2"></i>{{ __('Supplier Types') }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('document-types.index') }}" :active="request()->routeIs('document-types.index')">
                        <i class="fas fa-file-alt mr-2"></i>{{ __('Document Types') }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('data-categories.index') }}" :active="request()->routeIs('data-categories.index')">
                        <i class="fas fa-database mr-2"></i>{{ __('Data Categories') }}
                    </x-nav-link>
                                    <x-nav-link href="{{ route('third-countries.index') }}" :active="request()->routeIs('third-countries.index')">
                                        <i class="fas fa-globe mr-2"></i>{{ __('Third Countries') }}
                    </x-nav-link>
                                </div>
                            </div>
                        </div>

                        <!-- Logout -->
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-700 hover:text-gray-900 px-3 py-2 inline-flex items-center">
                                <i class="fas fa-sign-out-alt mr-2"></i>{{ __('Logout') }}
                            </button>
                        </form>
                    @endrole
                </div>
            </div>
        </div>
    </div>
</nav>
