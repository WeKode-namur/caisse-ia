<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <!-- En-tête -->
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-800">Gestion des attributs</h2>
                            <p class="text-sm text-gray-600 mt-1">Gérez les attributs et leurs valeurs pour vos
                                articles</p>
                        </div>
                        <a href="{{ route('settings.attributes.create') }}"
                           class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Nouvel attribut
                        </a>
                    </div>
                </div>

                <!-- Messages de succès/erreur -->
                @if(session('success'))
                    <div class="px-6 py-3 bg-green-100 border-l-4 border-green-500">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                          d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                          clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-green-700">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="px-6 py-3 bg-red-100 border-l-4 border-red-500">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                          d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                          clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700">{{ session('error') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Tableau des attributs -->
                <div class="overflow-x-auto lg:overflow-visible">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nom
                            </th>
                            <th class="px-2 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Type
                            </th>
                            <th class="px-2 lg:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Valeurs
                            </th>
                            <th class="hidden lg:table-cell px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Propriétés
                            </th>
                            <th class="px-2 lg:px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($attributes as $attribute)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 lg:px-6 py-3 lg:py-4">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $attribute->name }}</div>
                                        @if($attribute->description)
                                            <div
                                                class="hidden lg:block text-sm text-gray-500">{{ Str::limit($attribute->description, 50) }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-2 lg:px-6 py-3 lg:py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        @switch($attribute->type)
                                            @case('text')
                                                bg-blue-100 text-blue-800
                                                @break
                                            @case('number')
                                                bg-green-100 text-green-800
                                                @break
                                            @case('select')
                                                bg-purple-100 text-purple-800
                                                @break
                                            @case('boolean')
                                                bg-yellow-100 text-yellow-800
                                                @break
                                            @case('date')
                                                bg-red-100 text-red-800
                                                @break
                                            @default
                                                bg-gray-100 text-gray-800
                                        @endswitch">
                                        {{ ucfirst($attribute->type) }}
                                    </span>
                                </td>
                                <td class="px-2 lg:px-6 py-3 lg:py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1 lg:mr-2 text-gray-400" fill="none"
                                             stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                        </svg>
                                        <span class="font-medium">{{ $attribute->values_count }}</span>
                                        <span
                                            class="hidden lg:inline text-gray-500 ml-1">valeur{{ $attribute->values_count > 1 ? 's' : '' }}</span>
                                    </div>
                                </td>
                                <td class="hidden lg:table-cell px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="flex items-center space-x-3">
                                        @if($attribute->is_required)
                                            <span
                                                class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800"
                                                title="Cet attribut est obligatoire">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                          d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                          clip-rule="evenodd"/>
                                                </svg>
                                                Requis
                                            </span>
                                        @endif
                                        @if($attribute->is_searchable)
                                            <span
                                                class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800"
                                                title="Cet attribut est utilisé dans la recherche">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                          d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                                          clip-rule="evenodd"/>
                                                </svg>
                                                Recherchable
                                            </span>
                                        @endif
                                        @if(!$attribute->is_required && !$attribute->is_searchable)
                                            <span class="text-gray-400 text-xs">Aucune propriété spéciale</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-2 lg:px-6 py-3 lg:py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-1 lg:space-x-3">
                                        <!-- Gérer les valeurs -->
                                        <a href="{{ route('settings.attributes.values', $attribute) }}"
                                           class="group relative p-1 lg:p-2 text-indigo-600 hover:text-indigo-900 hover:bg-indigo-50 rounded-lg transition-colors duration-200"
                                           title="Gérer les valeurs de cet attribut">
                                            <i class="fa-solid fa-tags w-4 h-4 lg:w-5 lg:h-5"></i>
                                            <!-- Tooltip -->
                                            <div
                                                class="hidden lg:block absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap z-10">
                                                Gérer les valeurs
                                                <div
                                                    class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900"></div>
                                            </div>
                                        </a>

                                        <!-- Modifier -->
                                        <a href="{{ route('settings.attributes.edit', $attribute) }}"
                                           class="group relative p-1 lg:p-2 text-blue-600 hover:text-blue-900 hover:bg-blue-50 rounded-lg transition-colors duration-200"
                                           title="Modifier cet attribut">
                                            <i class="fa-solid fa-pen w-4 h-4 lg:w-5 lg:h-5"></i>
                                            <!-- Tooltip -->
                                            <div
                                                class="hidden lg:block absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap z-10">
                                                Modifier
                                                <div
                                                    class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900"></div>
                                            </div>
                                        </a>

                                        <!-- Supprimer -->
                                        <form action="{{ route('settings.attributes.destroy', $attribute) }}"
                                              method="POST"
                                              class="inline"
                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet attribut ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="group relative p-1 lg:p-2 text-red-600 hover:text-red-900 hover:bg-red-50 rounded-lg transition-colors duration-200"
                                                    title="Supprimer cet attribut">
                                                <i class="fa-solid fa-trash w-4 h-4 lg:w-5 lg:h-5"></i>
                                                <!-- Tooltip -->
                                                <div
                                                    class="hidden lg:block absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap z-10">
                                                    Supprimer
                                                    <div
                                                        class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900"></div>
                                                </div>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="lg:colspan-5 px-6 py-4 text-center text-gray-500">
                                    Aucun attribut trouvé.
                                    <a href="{{ route('settings.attributes.create') }}"
                                       class="text-indigo-600 hover:text-indigo-900">
                                        Créer le premier attribut
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
