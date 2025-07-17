<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <!-- Total -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                        <i class="fas fa-question text-white text-sm"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Non régularisés -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-red-500 rounded-md flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-white text-sm"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">À régulariser</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['pending'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Régularisés -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                        <i class="fas fa-check text-white text-sm"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Régularisés</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['regularized'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Non identifiables -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                        <i class="fas fa-times text-white text-sm"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Non identifiables</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['non_identifiable'] }}</p>
                </div>
            </div>
        </div>
    </div>
</div> 