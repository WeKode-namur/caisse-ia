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

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles

    <script>
        // Détection immédiate du thème
        (function() {
            const theme = localStorage.getItem('theme') ||
                (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');

            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
</head>
<body class="font-sans antialiased">
<x-banner />

<div class="min-h-screen bg-gradient-to-r from-violet-50 to-blue-50 dark:from-blue-950 dark:to-violet-950 relative">
    <x-animated-grid-pattern
        :width="40"
        :height="40"
        :numSquares="50"
        :maxOpacity="0.5"
        :duration="4"
        :repeatDelay="0.1"
        class="pointer-events-none lg:block hidden"
    />
    <div class="relative z-10">
        @if (!request()->routeIs('register.*'))
            @livewire('panel.components.navigation-menu')
        @endif
        <div class="flex items-stretch relative">
            @livewire('panel.components.sidebar')
            <div class="max-w-full flex-1 flex flex-col lg:min-h-auto min-h-[calc(100vh-65px)]">
                @if(session('module_debug'))
                    {!! session('module_debug') !!}
                @endif

                <main class="flex-1 ">
                    {{ $slot }}
                </main>

                <footer class="py-1.5 xl:px-12 lg:px-8 md:px-4 px-1.5 text-center bottom-0 mt-auto bg-white/80 dark:bg-gray-800/80 shadow text-gray-400 dark:text-gray-500 text-sm">
                    <div class="flex justify-between">
                        <p><i class="far fa-copyright mr-3"></i> 2025. Tous droits réservés.</p>
                        <p>Logiciel créer par <a href="https://www.wekode.be" target="_blank" class="underline hover:no-underline hover:scale-110 hover:bg-white dark:hover:bg-gray-800 hover:text-black dark:hover:text-white px-0.5 rounded inline-block duration-300">WeKode</a></p>
                    </div>
                </footer>
            </div>
        </div>
    </div>
</div>

@php
    $user = Auth::user();
    $currentVersion = config('custom.version.current');
    $checkFrom = config('custom.version.check_from');
    $showChangelog = $user && version_compare($currentVersion, $user->last_seen_version ?? '0.0.0', '>') && version_compare($currentVersion, $checkFrom, '>=');

@endphp
@if(isset($showChangelog) && $showChangelog)
    <x-changelog-modal :version="$changelogVersion" />
@endif

@stack('modals')

@stack('scripts')
@livewireScripts
</body>
</html>
