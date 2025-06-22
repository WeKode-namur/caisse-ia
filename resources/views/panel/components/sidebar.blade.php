@php
    use App\Models\Module;
    $user = auth()->user();

    // Logique personnalisée pour les niveaux visibles
    $maxVisibleLevel = match($user->is_admin) {
        0 => 80,
        80 => 90,    // Utilisateur normal voit jusqu'à niveau 90 (grisé)
        90 => 100,  // Admin voit jusqu'à niveau 100 (grisé)
        100 => 999, // Super admin voit tout
        default => $user->is_admin + 10
    };

    $allSidebarModules = Module::where('is_enabled', true)
        ->where('is_visible_sidebar', true)
        ->where('min_admin_level', '<=', $maxVisibleLevel)
        ->whereNull('parent_module_id')
        ->with(['children' => function ($query) use ($maxVisibleLevel) {
            $query->where('is_enabled', true)
                  ->where('is_visible_sidebar', true)
                  ->where('min_admin_level', '<=', $maxVisibleLevel)
                  ->orderBy('sort_order');
        }])
        ->orderBy('sort_order')
        ->get();
@endphp

<div x-data="{
    mobileOpen: false,
    expanded: false,
    isMobile: window.innerWidth < 1024,
    toggleMobile() {
        this.mobileOpen = !this.mobileOpen;
        if (this.mobileOpen && this.isMobile) {
            this.expanded = true;
        }
    },
    closeMobile() { this.mobileOpen = false; },
    toggleExpanded() { this.expanded = !this.expanded; },
    checkScreenSize() {
        this.isMobile = window.innerWidth < 1024;
        if (this.isMobile) {
            this.mobileOpen = false;
        }
    }
}"
     @resize.window="checkScreenSize()"
     @toggle-sidebar.window="toggleMobile()"
     x-init="checkScreenSize()"
     class="md:z-0 z-50 relative"
     x-cloak>

    <!-- Overlay pour mobile -->
    <div x-show="mobileOpen"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-900 bg-opacity-50 lg:hidden"
         @click="closeMobile()">
    </div>

    <!-- Sidebar -->
    <div x-show="mobileOpen || !isMobile"
         x-transition:enter="transition ease-in-out duration-300 transform"
         x-transition:enter-start="-translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in-out duration-300 transform"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="-translate-x-full"
         :class="[
             isMobile ? 'w-full fixed inset-y-0 left-0' : (expanded ? 'w-64' : 'w-16'),
             !isMobile ? 'relative' : '',
             isMobile ? 'h-screen' : 'h-full min-h-screen'
         ]"
         class="z-0 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transform transition-all duration-300 flex flex-col">

        <!-- Sidebar Header Mobile -->
        <div x-show="mobileOpen" class="flex items-center justify-between h-16 px-6 border-b border-gray-200 dark:border-gray-700 lg:hidden flex-shrink-0">
            <span class="text-lg font-semibold text-gray-800 dark:text-white">Navigation</span>
            <button @click="closeMobile()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 focus:outline-none">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Bouton Expand/Collapse (Desktop uniquement) -->
        <div class="hidden lg:flex justify-end p-4 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
            <button @click="toggleExpanded()"
                    class="p-2 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-300 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <i x-show="!expanded" class="fas fa-chevron-right"></i>
                <i x-show="expanded" class="fas fa-chevron-left" style="display: none;"></i>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 pt-4 px-2 pb-4 space-y-2 overflow-y-auto">
            @foreach($allSidebarModules as $module)
                @php
                    $hasAccess = $module->canAccess(auth()->user());
                    $isDisabled = !$hasAccess;
                @endphp

                @if($module->children->isNotEmpty())
                    <!-- Module avec sous-modules -->
                    <div x-data="{ open: {{ str_contains(request()->route()->getName() ?? '', explode(',', $module->route_prefix)[0]) ? 'true' : 'false' }} }">
                        @if($hasAccess)
                            <button @click="(expanded || isMobile) ? open = !open : (expanded = true, open = true)"
                                    x-tooltip="!expanded && !isMobile ? '{{ $module->display_name }}' : null"
                                    class="flex items-center justify-between w-full px-3 py-3 text-sm font-medium text-gray-700 rounded-lg hover:text-gray-900 hover:bg-gray-50 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700/50 transition-all duration-200">
                                <div class="flex items-center min-w-0">
                                    <i class="{{ $module->icon }} w-5 text-center text-gray-400 flex-shrink-0" :class="(expanded || isMobile) ? 'mr-3' : ''"></i>
                                    <span x-show="expanded || isMobile" x-transition class="whitespace-nowrap" style="display: none;">{{ $module->display_name }}</span>
                                </div>
                                <i x-show="expanded || isMobile" class="fas fa-chevron-down text-xs transition-transform duration-200 text-gray-400 flex-shrink-0" :class="{ 'rotate-180': open }" style="display: none;"></i>
                            </button>
                        @else
                            <!-- Module non accessible - grisé -->
                            <div x-tooltip="!expanded && !isMobile ? '{{ $module->display_name }} (Non accessible)' : null"
                                 class="flex items-center justify-between w-full px-3 py-3 text-sm font-medium opacity-40 cursor-not-allowed">
                                <div class="flex items-center min-w-0">
                                    <i class="{{ $module->icon }} w-5 text-center text-gray-400 flex-shrink-0" :class="(expanded || isMobile) ? 'mr-3' : ''"></i>
                                    <span x-show="expanded || isMobile" x-transition class="whitespace-nowrap text-gray-400" style="display: none;">
                                        {{ $module->display_name }}
                                        <span class="text-xs ml-1">(Bientôt)</span>
                                    </span>
                                </div>
                                <i x-show="expanded || isMobile" class="fas fa-lock text-xs text-gray-400 flex-shrink-0" style="display: none;"></i>
                            </div>
                        @endif

                        @if($hasAccess)
                            <div x-show="open && (expanded || isMobile)" x-transition class="ml-8 mt-2 space-y-1" style="display: none;">
                                @foreach($module->children as $child)
                                    @php
                                        $childHasAccess = $child->canAccess(auth()->user());
                                        $childRoutes = explode(',', $child->route_prefix);
                                        $routeName = trim($childRoutes[0]);
                                        $isActive = str_contains(request()->route()->getName() ?? '', $routeName);
                                    @endphp

                                    @if($childHasAccess)
                                        <a href="{{ route($routeName) }}"
                                           @click="closeMobile()"
                                           class="flex items-center px-3 py-2 text-sm rounded-lg transition-all duration-200 {{ $isActive ? 'bg-blue-50 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50 dark:text-gray-400 dark:hover:text-gray-300 dark:hover:bg-gray-700/50' }}">
                                            <i class="{{ $child->icon }} mr-2 w-4 text-center"></i>
                                            {{ $child->display_name }}
                                        </a>
                                    @else
                                        <!-- Sous-module non accessible -->
                                        <div class="flex items-center px-3 py-2 text-sm opacity-40 cursor-not-allowed">
                                            <i class="{{ $child->icon }} mr-2 w-4 text-center text-gray-400"></i>
                                            <span class="text-gray-400">{{ $child->display_name }} <span class="text-xs">(Bientôt)</span></span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                @else
                    <!-- Module simple -->
                    @php
                        $moduleRoutes = explode(',', $module->route_prefix);
                        $routeName = trim($moduleRoutes[0]);
                        $isActive = str_contains(request()->route()->getName() ?? '', $routeName);
                    @endphp

                    @if($hasAccess)
                        <a href="{{ route($routeName) }}"
                           @click="closeMobile()"
                           x-tooltip="!expanded && !isMobile ? '{{ $module->display_name }}' : null"
                           class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 {{ $isActive ? 'bg-blue-50 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300' : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700/50' }}">
                            <i class="{{ $module->icon }} w-5 text-center {{ $isActive ? 'text-blue-500' : 'text-gray-400' }}" :class="(expanded || isMobile) ? 'mr-3' : ''"></i>
                            <span x-show="expanded || isMobile" x-transition class="whitespace-nowrap" style="display: none;">{{ $module->display_name }}</span>
                        </a>
                    @else
                        <!-- Module non accessible - grisé -->
                        <div x-tooltip="!expanded && !isMobile ? '{{ $module->display_name }} (Non accessible)' : null"
                             class="flex items-center px-3 py-3 text-sm font-medium opacity-40 cursor-not-allowed">
                            <i class="{{ $module->icon }} w-5 text-center text-gray-400" :class="(expanded || isMobile) ? 'mr-3' : ''"></i>
                            <span x-show="expanded || isMobile" x-transition class="whitespace-nowrap text-gray-400" style="display: none;">
                                {{ $module->display_name }}
                                <span class="text-xs ml-1">(Bientôt)</span>
                            </span>
                        </div>
                    @endif
                @endif

                @if($module->name === 'clients')
                    <!-- Séparateur après clients -->
                    <div x-show="expanded || isMobile" class="border-t border-gray-200 dark:border-gray-700 my-4" style="display: none;"></div>
                @endif

                @if($module->name === 'wix')
                    <!-- Séparateur après wix -->
                    <div x-show="expanded || isMobile" class="border-t border-gray-200 dark:border-gray-700 my-4" style="display: none;"></div>
                @endif
            @endforeach
        </nav>
    </div>
</div>
