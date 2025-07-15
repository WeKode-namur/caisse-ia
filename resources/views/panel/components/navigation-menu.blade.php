<nav class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 shadow-lg z-20">
    <div class="lg:px-24 sm:px-8 px-4">
        <div class="flex justify-between h-16">
            <!-- Logo / Nom de l'application -->
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <a href="{{ route('dashboard') }}" class="text-xl font-bold text-gray-800 dark:text-white">
                        Caisse {{ config('app.name') }}
                    </a>
                </div>
            </div>

            <!-- Section droite : Sélecteur de thème + Dropdown utilisateur + Bouton hamburger -->
            <div class="flex items-center space-x-4">
                <!-- Sélecteur de thème -->
                <div class="flex items-center">
                    <x-dropdown align="right" width="40">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center p-2 border border-transparent text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150 rounded-md">
                                <!-- Icône du thème actuel -->
                                <i class="theme-icon light-icon fa-solid fa-sun text-lg hidden"></i>
                                <i class="theme-icon dark-icon fa-solid fa-moon text-lg hidden"></i>
                                <i class="theme-icon auto-icon fa-solid fa-circle-half-stroke text-lg hidden"></i>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="w-full">
                                <!-- Mode Clair -->
                                <button onclick="setTheme('light')" class="theme-option block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 hover:dark:bg-gray-900/20 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">
                                    <div class="flex items-center w-full">
                                        <i class="fa-solid fa-sun mr-3"></i>
                                        <span class="flex-1">{{ __('Clair') }}</span>
                                        <i class="theme-check light-check fa-solid fa-check hidden"></i>
                                    </div>
                                </button>

                                <!-- Mode Sombre -->
                                <button onclick="setTheme('dark')" class="theme-option block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 hover:dark:bg-gray-900/20 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">
                                    <div class="flex items-center w-full">
                                        <i class="fa-solid fa-moon mr-3"></i>
                                        <span class="flex-1">{{ __('Sombre') }}</span>
                                        <i class="theme-check dark-check fa-solid fa-check hidden"></i>
                                    </div>
                                </button>

                                <!-- Mode Auto -->
                                <button onclick="setTheme('auto')" class="theme-option block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 hover:dark:bg-gray-900/20 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">
                                    <div class="flex items-center w-full">
                                        <i class="fa-solid fa-circle-half-stroke mr-3"></i>
                                        <span class="flex-1">{{ __('Auto') }}</span>
                                        <i class="theme-check auto-check fa-solid fa-check hidden"></i>
                                    </div>
                                </button>
                            </div>
                        </x-slot>
                    </x-dropdown>
                </div>

                <!-- Dropdown utilisateur -->
                <div class="flex items-center">
                    <x-dropdown align="right" :width="$isMobile ?? false ? 'full' : '48'">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150">
                                <div class="flex items-center">
                                    <!-- Avatar -->
                                    <div class="w-8 h-8 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center mr-2">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                            {{ substr(Auth::user()->name, 0, 1) }}
                                        </span>
                                    </div>

                                    <!-- Nom de l'utilisateur (caché sur très petit écran) -->
                                    <div class="text-left hidden sm:block">
                                        <div class="font-medium text-sm text-gray-800 dark:text-gray-200">
                                            {{ Auth::user()->name }}
                                        </div>
                                        <div class="font-medium text-xs text-gray-500 dark:text-gray-400">
                                            {{ Auth::user()->email }}
                                        </div>
                                    </div>

                                    <!-- Icône dropdown -->
                                    <div class="ml-2">
                                        <i class="fa-solid fa-chevron-down fill-current"></i>
                                    </div>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="sm:w-48 w-screen sm:mr-0 -mr-4 max-w-full">
                                <!-- Profil -->
                                <x-dropdown-link href="{{ route('profile.show') }}">
                                    <div class="flex items-center w-full">
                                        <i class="fa-solid fa-user-gear mr-3"></i>
                                        <span class="flex-1">{{ __('Profil') }}</span>
                                    </div>
                                </x-dropdown-link>

                                <!-- Paramètres -->
                                <x-dropdown-link href="{{ route('settings.index') }}">
                                    <div class="flex items-center w-full">
                                        <i class="fa-solid fa-gear mr-3"></i>
                                        <span class="flex-1">{{ __('Paramètres') }}</span>
                                    </div>
                                </x-dropdown-link>

                                <!-- Déconnexion des paramètres (visible seulement si session settings active) -->
                                @if(session('settings_password_confirmed'))
                                    <form method="POST" action="{{ route('settings.reset-session') }}" x-data>
                                        @csrf
                                        <x-dropdown-link href="{{ route('settings.reset-session') }}"
                                                         @click.prevent="$root.submit();">
                                            <div class="flex items-center text-orange-600 dark:text-orange-400 w-full">
                                                <i class="fa-solid fa-lock mr-3"></i>
                                                <span class="flex-1">{{ __('Déconnexion paramètres') }}</span>
                                            </div>
                                        </x-dropdown-link>
                                    </form>
                                @endif

                                <!-- Support -->
{{--                                <x-dropdown-link href="{{ route('support') }}">--}}
{{--                                    <div class="flex items-center w-full">--}}
{{--                                        <i class="fas fa-headset mr-3"></i>--}}
{{--                                        <span class="flex-1">{{ __('Support') }}</span>--}}
{{--                                    </div>--}}
{{--                                </x-dropdown-link>--}}

                                <div class="border-t border-gray-200 dark:border-gray-600"></div>

                                <!-- Déconnexion -->
                                <form method="POST" action="{{ route('logout') }}" x-data>
                                    @csrf
                                    <x-dropdown-link href="{{ route('logout') }}"
                                                     @click.prevent="$root.submit();">
                                        <div class="flex items-center text-red-600 dark:text-red-400 w-full">
                                            <i class="fa-solid fa-arrow-right-from-bracket mr-3"></i>
                                            <span class="flex-1">{{ __('Déconnexion') }}</span>
                                        </div>
                                    </x-dropdown-link>
                                </form>
                            </div>
                        </x-slot>
                    </x-dropdown>
                </div>

                <!-- Bouton hamburger (visible seulement en dessous de lg) -->
                <button @click="$wire.call('toggleSidebar')"
                        class="lg:hidden p-2 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-300 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <i class="fas fa-bars text-lg"></i>
                </button>

            </div>
        </div>
    </div>
</nav>

<script>
    // Gestion des thèmes
    function setTheme(theme) {
        // Sauvegarder la préférence
        localStorage.setItem('theme', theme);

        // Appliquer le thème
        applyTheme(theme);

        // Mettre à jour l'interface
        updateThemeUI(theme);
    }

    function applyTheme(theme) {
        const html = document.documentElement;

        if (theme === 'dark') {
            html.classList.add('dark');
        } else if (theme === 'light') {
            html.classList.remove('dark');
        } else if (theme === 'auto') {
            // Mode auto : suivre les préférences système
            if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                html.classList.add('dark');
            } else {
                html.classList.remove('dark');
            }
        }
    }

    function updateThemeUI(currentTheme) {
        // Masquer toutes les icônes
        document.querySelectorAll('.theme-icon').forEach(icon => {
            icon.classList.add('hidden');
        });

        // Masquer toutes les coches
        document.querySelectorAll('.theme-check').forEach(check => {
            check.classList.add('hidden');
        });

        // Afficher l'icône et la coche correspondantes
        const iconClass = currentTheme + '-icon';
        const checkClass = currentTheme + '-check';

        document.querySelector('.' + iconClass)?.classList.remove('hidden');
        document.querySelector('.' + checkClass)?.classList.remove('hidden');
    }

    // Initialisation au chargement de la page
    document.addEventListener('DOMContentLoaded', function() {
        const savedTheme = localStorage.getItem('theme') || 'auto';
        applyTheme(savedTheme);
        updateThemeUI(savedTheme);

        // Écouter les changements de préférences système pour le mode auto
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function() {
            const currentTheme = localStorage.getItem('theme') || 'auto';
            if (currentTheme === 'auto') {
                applyTheme('auto');
            }
        });
    });
</script>
