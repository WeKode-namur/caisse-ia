<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Icône</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
        </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
        @forelse($categories as $category)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-center">
                    @if($category->icon)
                        <i class="{{ $category->icon }} text-xl text-gray-700"></i>
                    @else
                        <span class="text-gray-400">-</span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="font-medium text-gray-900">{{ $category->name }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                    {{ $category->description ?? '-' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                    @if($category->actif)
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Actif</span>
                    @else
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-200 text-gray-600">Archivé</span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex items-center justify-end space-x-3">
                        <!-- Gérer les types -->
                        <button onclick="manageTypes({{ $category->id }}, @js($category->name))"
                                class="group relative p-2 text-indigo-600 hover:text-indigo-900 hover:bg-indigo-50 rounded-lg transition-colors duration-200"
                                title="Gérer les types de cette catégorie">
                            <i class="fa-solid fa-tags w-4 h-4"></i>
                            <!-- Tooltip -->
                            <div
                                class="hidden lg:block absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap z-10">
                                Gérer les types
                                <div
                                    class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900"></div>
                            </div>
                        </button>

                        <!-- Modifier -->
                        <button
                            onclick="editCategory({{ $category->id }}, @js($category->name), @js($category->description), @js($category->icon))"
                            class="group relative p-2 text-amber-600 hover:text-amber-900 hover:bg-amber-50 rounded-lg transition-colors duration-200"
                            title="Modifier cette catégorie">
                            <i class="fa-solid fa-pen w-4 h-4"></i>
                            <!-- Tooltip -->
                            <div
                                class="hidden lg:block absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap z-10">
                                Modifier
                                <div
                                    class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900"></div>
                            </div>
                        </button>

                        @if($category->actif)
                            <!-- Désactiver -->
                            <button onclick="toggleCategory({{ $category->id }})"
                                    class="group relative p-2 text-red-600 hover:text-red-900 hover:bg-red-50 rounded-lg transition-colors duration-200"
                                    title="Désactiver cette catégorie">
                                <i class="fa-solid fa-eye-slash w-4 h-4"></i>
                                <!-- Tooltip -->
                                <div
                                    class="hidden lg:block absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap z-10">
                                    Désactiver
                                    <div
                                        class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900"></div>
                                </div>
                            </button>
                        @else
                            <!-- Réactiver -->
                            <button onclick="toggleCategory({{ $category->id }})"
                                    class="group relative p-2 text-green-600 hover:text-green-900 hover:bg-green-50 rounded-lg transition-colors duration-200"
                                    title="Réactiver cette catégorie">
                                <i class="fa-solid fa-eye w-4 h-4"></i>
                                <!-- Tooltip -->
                                <div
                                    class="hidden lg:block absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap z-10">
                                    Réactiver
                                    <div
                                        class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900"></div>
                                </div>
                            </button>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center py-8 text-gray-400">
                    <i class="fas fa-folder-open text-3xl mb-2"></i><br>
                    Aucune catégorie trouvée.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>
