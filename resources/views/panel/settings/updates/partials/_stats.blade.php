<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-code-branch text-2xl text-indigo-600"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Total des versions
                        </dt>
                        <dd class="text-lg font-medium text-gray-900">
                            {{ $totalVersions }}
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
                    <i class="fas fa-calendar-alt text-2xl text-green-600"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Dernière mise à jour
                        </dt>
                        <dd class="text-lg font-medium text-gray-900">
                            {{ $lastUpdateDate ?? 'Aucune' }}
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
                    <i class="fas fa-tag text-2xl text-purple-600"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            Version actuelle
                        </dt>
                        <dd class="text-lg font-medium text-gray-900">
                            {{ $currentVersion ?? 'v0.0.0' }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div> 