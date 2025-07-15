<div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    <!-- Total des attributs -->
    <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 rounded-lg p-4 shadow-sm">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fa-solid fa-tags w-6 h-6 text-blue-500"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['total'] }}</p>
            </div>
        </div>
    </div>

    <!-- Attributs actifs -->
    <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 rounded-lg p-4 shadow-sm">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fa-solid fa-eye w-6 h-6 text-green-500"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Actifs</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['active'] }}</p>
            </div>
        </div>
    </div>

    <!-- Attributs inactifs -->
    <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 rounded-lg p-4 shadow-sm">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fa-solid fa-eye-slash w-6 h-6 text-gray-500"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Inactifs</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['inactive'] }}</p>
            </div>
        </div>
    </div>

    <!-- Répartition par type -->
    <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 rounded-lg p-4 shadow-sm">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fa-solid fa-chart-pie w-6 h-6 text-purple-500"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Types</p>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    <div class="flex items-center">
                        <span class="w-2 h-2 bg-green-500 rounded-full mr-1.5"></span>
                        <span>Nombre:&nbsp; {{ $stats['by_type']['number'] }}</span>
                    </div>
                    <div class="flex items-center">
                        <span class="w-2 h-2 bg-purple-500 rounded-full mr-1.5"></span>
                        <span>Sélection:&nbsp; {{ $stats['by_type']['select'] }}</span>
                    </div>
                    <div class="flex items-center">
                        <span class="w-2 h-2 bg-pink-500 rounded-full mr-1.5"></span>
                        <span>Couleur:&nbsp; {{ $stats['by_type']['color'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
