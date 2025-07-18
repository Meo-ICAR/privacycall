<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Font Awesome -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles

        @stack('modals')
        @stack('scripts')
        @livewireScripts
    </head>
    <body class="font-sans antialiased">
        @if(session('impersonate_original_id'))
            <div class="bg-yellow-200 border-b border-yellow-400 text-yellow-900 px-4 py-3 flex items-center justify-between">
                <div>
                    <i class="fas fa-user-secret mr-2"></i>
                    <strong>Impersonation Mode:</strong> You are impersonating another user.
                </div>
                <form method="POST" action="{{ route('impersonate.stop') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-3 py-1 border border-yellow-400 text-xs font-medium rounded-md text-yellow-900 bg-yellow-100 hover:bg-yellow-300">
                        <i class="fas fa-sign-out-alt mr-1"></i> Stop Impersonating
                    </button>
                </form>
            </div>
        @endif

        <x-banner />

        <div class="min-h-screen bg-gray-100">
            @livewire('navigation-menu')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                @yield('content')
            </main>
        </div>



    </body>
</html>
