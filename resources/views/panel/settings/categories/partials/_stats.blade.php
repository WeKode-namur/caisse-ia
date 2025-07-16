<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-folder text-2xl text-indigo-600"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Total catégories
                        </dt>
                        <dd class="text-lg font-medium text-gray-900">
                            {{ $totalCategories }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-2xl text-green-600"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Actives
                        </dt>
                        <dd class="text-lg font-medium text-gray-900">
                            {{ $activeCategories }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-archive text-2xl text-gray-400"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Archivées
                        </dt>
                        <dd class="text-lg font-medium text-gray-900">
                            {{ $inactiveCategories }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div> 