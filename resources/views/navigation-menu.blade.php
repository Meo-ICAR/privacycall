<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-mark class="block h-9 w-auto" />
                    </a>
                    @php
                        $company = Auth::user()->company ?? null;
                    @endphp
                    @if($company && $company->logo_url)
                        <img src="{{ $company->logo_url }}" alt="Company Logo" class="h-9 w-9 object-contain ml-4 rounded shadow border border-gray-200" />
                    @endif
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    <!-- Management Section -->
                    @if(auth()->check() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('manager')))
                        <div class="hidden sm:flex sm:items-center sm:ms-6">
                            <x-dropdown align="left" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                        {{ __('Management') }}
                                        <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <x-dropdown-link href="{{ route('companies.index') }}" :active="request()->routeIs('companies.index')">
                                        {{ __('Companies') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link href="{{ route('employees.index') }}" :active="request()->routeIs('employees.index')">
                                        {{ __('Employees') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link href="{{ route('customers.index') }}" :active="request()->routeIs('customers.index')">
                                        {{ __('Customers') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link href="{{ route('suppliers.index') }}" :active="request()->routeIs('suppliers.index')">
                                        {{ __('Suppliers') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link href="{{ route('representatives.index') }}" :active="request()->routeIs('representatives.index')">
                                        {{ __('Representatives') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link href="{{ route('holdings.index') }}" :active="request()->routeIs('holdings.index')">
                                        {{ __('Holdings') }}
                                    </x-dropdown-link>
                                    @if(auth()->user()->company && auth()->user()->company->data_controller_contact)
                                        <x-dropdown-link href="{{ route('companies.emails.index', auth()->user()->company) }}" :active="request()->routeIs('companies.emails.*')">
                                            {{ __('Email Management') }}
                                        </x-dropdown-link>
                                    @endif
                                </x-slot>
                            </x-dropdown>
                        </div>

                        <!-- Inspections Section -->
                        <div class="hidden sm:flex sm:items-center sm:ms-6">
                            <x-dropdown align="left" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                        {{ __('Inspections') }}
                                        <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <x-dropdown-link href="{{ route('inspections.index') }}" :active="request()->routeIs('inspections.index')">
                                        {{ __('Inspections') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link href="{{ route('supplier-inspections.index') }}" :active="request()->routeIs('supplier-inspections.index')">
                                        {{ __('Supplier Inspections') }}
                                    </x-dropdown-link>
                                </x-slot>
                            </x-dropdown>
                        </div>

                        <!-- GDPR Section -->
                        <div class="hidden sm:flex sm:items-center sm:ms-6">
                            <x-dropdown align="left" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                        {{ __('GDPR') }}
                                        <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <x-dropdown-link href="{{ route('consent-records.index') }}" :active="request()->routeIs('consent-records.index')">
                                        {{ __('Consent Records') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link href="{{ route('data-processing-activities.index') }}" :active="request()->routeIs('data-processing-activities.index')">
                                        {{ __('Data Processing Activities') }}
                                    </x-dropdown-link>
                                </x-slot>
                            </x-dropdown>
                        </div>

                        <!-- Training Section -->
                        <x-nav-link href="{{ route('trainings.index') }}" :active="request()->routeIs('trainings.index')">
                            {{ __('Training') }}
                        </x-nav-link>
                    @endif

                    <!-- Settings Section (Admin/Superadmin only) -->
                    @if(auth()->check() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin')))
                        <div class="hidden sm:flex sm:items-center sm:ms-6">
                            <x-dropdown align="left" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                        {{ __('Settings') }}
                                        <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <x-dropdown-link href="{{ route('roles.permissions.index') }}" :active="request()->routeIs('roles.permissions.index')">
                                        {{ __('Roles & Permissions') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link href="{{ route('document-types.index') }}" :active="request()->routeIs('document-types.index')">
                                        {{ __('Document Types') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link href="{{ route('employer-types.index') }}" :active="request()->routeIs('employer-types.index')">
                                        {{ __('Employer Types') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link href="{{ route('customer-types.index') }}" :active="request()->routeIs('customer-types.index')">
                                        {{ __('Customer Types') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link href="{{ route('supplier-types.index') }}" :active="request()->routeIs('supplier-types.index')">
                                        {{ __('Supplier Types') }}
                                    </x-dropdown-link>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Teams Dropdown -->
                @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                    <div class="ms-3 relative">
                        <x-dropdown align="right" width="60">
                            <x-slot name="trigger">
                                <span class="inline-flex rounded-md">
                                    <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                        @if(Auth::user() && Auth::user()->currentTeam)
                                            {{ Auth::user()->currentTeam->name }}
                                        @endif

                                        <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                        </svg>
                                    </button>
                                </span>
                            </x-slot>

                            <x-slot name="content">
                                <div class="w-60">
                                    <!-- Team Management -->
                                    <div class="block px-4 py-2 text-xs text-gray-400">
                                        {{ __('Manage Team') }}
                                    </div>

                                    <!-- Team Settings -->
                                    <x-dropdown-link href="{{ route('teams.show', Auth::user() && Auth::user()->currentTeam ? Auth::user()->currentTeam->id : '' ) }}">
                                        {{ __('Team Settings') }}
                                    </x-dropdown-link>

                                    @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                                        <x-dropdown-link href="{{ route('teams.create') }}">
                                            {{ __('Create New Team') }}
                                        </x-dropdown-link>
                                    @endcan

                                    <!-- Team Switcher -->
                                    @if (Auth::user() && Auth::user()->allTeams() && Auth::user()->allTeams()->count() > 1)
                                        <div class="border-t border-gray-200"></div>

                                        <div class="block px-4 py-2 text-xs text-gray-400">
                                            {{ __('Switch Teams') }}
                                        </div>

                                        @foreach (Auth::user()->allTeams() as $team)
                                            <x-switchable-team :team="$team" />
                                        @endforeach
                                    @endif
                                </div>
                            </x-slot>
                        </x-dropdown>
                    </div>
                @endif

                <!-- Settings Dropdown -->
                <div class="ms-3 relative">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos() && Auth::user())
                                <button class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                    <img class="size-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                                </button>
                            @elseif(Auth::user())
                                <span class="inline-flex rounded-md">
                                    <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                        {{ Auth::user()->name }}

                                        <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </button>
                                </span>
                            @endif
                        </x-slot>

                        <x-slot name="content">
                            <!-- Account Management -->
                            <div class="block px-4 py-2 text-xs text-gray-400">
                                {{ __('Manage Account') }}
                            </div>

                            <x-dropdown-link href="{{ route('profile.show') }}">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                <x-dropdown-link href="{{ route('api-tokens.index') }}">
                                    {{ __('API Tokens') }}
                                </x-dropdown-link>
                            @endif

                            <div class="border-t border-gray-200"></div>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}" x-data>
                                @csrf

                                <x-dropdown-link href="{{ route('logout') }}"
                                         @click.prevent="$root.submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="size-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            <!-- Management Section -->
            @if(auth()->check() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('manager')))
                <div class="border-t border-gray-200"></div>
                <div class="block px-4 py-2 text-xs text-gray-400">
                    {{ __('Management') }}
                </div>
                <x-responsive-nav-link href="{{ route('companies.index') }}" :active="request()->routeIs('companies.index')">
                    {{ __('Companies') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('employees.index') }}" :active="request()->routeIs('employees.index')">
                    {{ __('Employees') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('customers.index') }}" :active="request()->routeIs('customers.index')">
                    {{ __('Customers') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('suppliers.index') }}" :active="request()->routeIs('suppliers.index')">
                    {{ __('Suppliers') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('representatives.index') }}" :active="request()->routeIs('representatives.index')">
                    {{ __('Representatives') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('holdings.index') }}" :active="request()->routeIs('holdings.index')">
                    {{ __('Holdings') }}
                </x-responsive-nav-link>
                @if(auth()->user()->company && auth()->user()->company->data_controller_contact)
                    <x-responsive-nav-link href="{{ route('companies.emails.index', auth()->user()->company) }}" :active="request()->routeIs('companies.emails.*')">
                        {{ __('Email Management') }}
                    </x-responsive-nav-link>
                @endif

                <!-- Inspections Section -->
                <div class="border-t border-gray-200"></div>
                <div class="block px-4 py-2 text-xs text-gray-400">
                    {{ __('Inspections') }}
                </div>
                <x-responsive-nav-link href="{{ route('inspections.index') }}" :active="request()->routeIs('inspections.index')">
                    {{ __('Inspections') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('supplier-inspections.index') }}" :active="request()->routeIs('supplier-inspections.index')">
                    {{ __('Supplier Inspections') }}
                </x-responsive-nav-link>

                <!-- GDPR Section -->
                <div class="border-t border-gray-200"></div>
                <div class="block px-4 py-2 text-xs text-gray-400">
                    {{ __('GDPR') }}
                </div>
                <x-responsive-nav-link href="{{ route('consent-records.index') }}" :active="request()->routeIs('consent-records.index')">
                    {{ __('Consent Records') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('data-processing-activities.index') }}" :active="request()->routeIs('data-processing-activities.index')">
                    {{ __('Data Processing Activities') }}
                </x-responsive-nav-link>

                <!-- Training Section -->
                <div class="border-t border-gray-200"></div>
                <x-responsive-nav-link href="{{ route('trainings.index') }}" :active="request()->routeIs('trainings.index')">
                    {{ __('Training') }}
                </x-responsive-nav-link>
            @endif

            <!-- Settings Section (Admin/Superadmin only) -->
            @if(auth()->check() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin')))
                <div class="border-t border-gray-200"></div>
                <div class="block px-4 py-2 text-xs text-gray-400">
                    {{ __('Settings') }}
                </div>
                <x-responsive-nav-link href="{{ route('roles.permissions.index') }}" :active="request()->routeIs('roles.permissions.index')">
                    {{ __('Roles & Permissions') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('document-types.index') }}" :active="request()->routeIs('document-types.index')">
                    {{ __('Document Types') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('employer-types.index') }}" :active="request()->routeIs('employer-types.index')">
                    {{ __('Employer Types') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('customer-types.index') }}" :active="request()->routeIs('customer-types.index')">
                    {{ __('Customer Types') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('supplier-types.index') }}" :active="request()->routeIs('supplier-types.index')">
                    {{ __('Supplier Types') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="flex items-center px-4">
                @if (Laravel\Jetstream\Jetstream::managesProfilePhotos() && Auth::user())
                    <div class="shrink-0 me-3">
                        <img class="size-10 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                    </div>
                @endif

                <div>
                    @if(Auth::user())
                        <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                    @endif
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <!-- Account Management -->
                <x-responsive-nav-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                    <x-responsive-nav-link href="{{ route('api-tokens.index') }}" :active="request()->routeIs('api-tokens.index')">
                        {{ __('API Tokens') }}
                    </x-responsive-nav-link>
                @endif

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}" x-data>
                    @csrf

                    <x-responsive-nav-link href="{{ route('logout') }}"
                                   @click.prevent="$root.submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>

                <!-- Team Management -->
                @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                    <div class="border-t border-gray-200"></div>

                    <div class="block px-4 py-2 text-xs text-gray-400">
                        {{ __('Manage Team') }}
                    </div>

                    <!-- Team Settings -->
                    <x-responsive-nav-link href="{{ route('teams.show', Auth::user() && Auth::user()->currentTeam ? Auth::user()->currentTeam->id : '' ) }}" :active="request()->routeIs('teams.show')">
                        {{ __('Team Settings') }}
                    </x-responsive-nav-link>

                    @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                        <x-responsive-nav-link href="{{ route('teams.create') }}" :active="request()->routeIs('teams.create')">
                            {{ __('Create New Team') }}
                        </x-responsive-nav-link>
                    @endcan

                    <!-- Team Switcher -->
                    @if (Auth::user() && Auth::user()->allTeams() && Auth::user()->allTeams()->count() > 1)
                        <div class="border-t border-gray-200"></div>

                        <div class="block px-4 py-2 text-xs text-gray-400">
                            {{ __('Switch Teams') }}
                        </div>

                        @foreach (Auth::user()->allTeams() as $team)
                            <x-switchable-team :team="$team" component="responsive-nav-link" />
                        @endforeach
                    @endif
                @endif
            </div>
        </div>
    </div>
</nav>
