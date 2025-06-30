<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 mb-2">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-mark class="block h-9 w-auto" />
                    </a>
                </div>
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('holdings.index') }}" :active="request()->routeIs('holdings.index')">
                        <i class="fas fa-sitemap mr-2"></i>{{ __('Holdings') }}
                    </x-nav-link>
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
                    <x-nav-link href="{{ route('security-measures.index') }}" :active="request()->routeIs('security-measures.index')">
                        <i class="fas fa-shield-alt mr-2"></i>{{ __('Security Measures') }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('data-categories.index') }}" :active="request()->routeIs('data-categories.index')">
                        <i class="fas fa-database mr-2"></i>{{ __('Data Categories') }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('roles.permissions.index') }}" :active="request()->routeIs('roles.permissions.index')">
                        <i class="fas fa-user-shield mr-2"></i>{{ __('Roles & Permissions') }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('email-providers.index') }}" :active="request()->routeIs('email-providers.index')">
                        <i class="fas fa-envelope-open-text mr-2"></i>{{ __('Email Providers') }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('email-templates.index') }}" :active="request()->routeIs('email-templates.index')">
                        <i class="fas fa-envelope mr-2"></i>{{ __('Email Templates') }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('companies.index') }}" :active="request()->routeIs('companies.index')">
                        <i class="fas fa-building mr-2"></i>{{ __('Companies') }}
                    </x-nav-link>

                    <x-nav-link href="{{ route('third-countries.index') }}" :active="request()->routeIs('third-countries.index')">
                        <i class="fas fa-globe mr-2"></i>{{ __('Third Countries') }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('legal-basis-types.index') }}" :active="request()->routeIs('legal-basis-types.index')">
                        <i class="fas fa-gavel mr-2"></i>{{ __('Legal Basis Types') }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('disclosure-types.index') }}" :active="request()->routeIs('disclosure-types.index')">
                        <i class="fas fa-bullhorn mr-2"></i>{{ __('Disclosure Types') }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('users.index') }}" :active="request()->routeIs('users.index')">
                        <i class="fas fa-users-cog mr-2"></i>{{ __('Users') }}
                    </x-nav-link>
                </div>
            </div>
        </div>
    </div>
</nav>
