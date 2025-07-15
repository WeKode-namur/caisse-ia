@if($attributes->count() > 0)
    <div class="overflow-x-auto">
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
                    Utilisation
                </th>
                <th class="px-2 lg:px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                </th>
            </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
            @foreach($attributes as $attribute)
                <tr class="hover:bg-gray-50 {{ !$attribute->actif ? 'bg-gray-50 opacity-75' : '' }}">
                    <td class="px-3 lg:px-6 py-3 lg:py-4">
                        <div class="flex items-center">
                            @if(!$attribute->actif)
                                <span
                                    class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 mr-2">
                                        <i class="fa-solid fa-eye-slash w-3 h-3 mr-1"></i>
                                        Inactif
                                    </span>
                            @endif
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $attribute->name }}</div>
                                @if($attribute->unit)
                                    <div class="hidden lg:block text-sm text-gray-500">
                                        Unité: {{ $attribute->unit }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-2 lg:px-6 py-3 lg:py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                @switch($attribute->type)
                                    @case('number')
                                        bg-green-100 text-green-800
                                        @break
                                    @case('select')
                                        bg-purple-100 text-purple-800
                                        @break
                                    @case('color')
                                        bg-pink-100 text-pink-800
                                        @break
                                    @default
                                        bg-gray-100 text-gray-800
                                @endswitch">
                                @switch($attribute->type)
                                    @case('number')
                                        Nombre
                                        @break
                                    @case('select')
                                        Sélection
                                        @break
                                    @case('color')
                                        Couleur
                                        @break
                                    @default
                                        {{ ucfirst($attribute->type) }}
                                @endswitch
                            </span>
                    </td>
                    <td class="px-2 lg:px-6 py-3 lg:py-4 whitespace-nowrap text-sm text-gray-900">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-1 lg:mr-2 text-gray-400" fill="none"
                                 stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            <span class="font-medium">{{ $attribute->active_values_count }}</span>
                            <span class="text-gray-500 ml-1">/ {{ $attribute->total_values_count }}</span>
                            <span
                                class="hidden lg:inline text-gray-500 ml-1">valeur{{ $attribute->total_values_count > 1 ? 's' : '' }}</span>
                        </div>
                    </td>
                    <td class="hidden lg:table-cell px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <div class="flex items-center space-x-3">
                            <div class="flex items-center">
                                <i class="fa-solid fa-boxes w-4 h-4 mr-1 text-blue-500"></i>
                                <span class="font-medium">{{ $attribute->articles_count ?? 0 }}</span>
                                <span class="text-gray-500 ml-1">articles</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fa-solid fa-tags w-4 h-4 mr-1 text-green-500"></i>
                                <span class="font-medium">{{ $attribute->variants_count ?? 0 }}</span>
                                <span class="text-gray-500 ml-1">variants</span>
                            </div>
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

                            @if($attribute->actif)
                                <!-- Désactiver -->
                                <button type="button"
                                        data-attribute-id="{{ $attribute->id }}"
                                        data-attribute-name="{{ $attribute->name }}"
                                        data-articles-count="{{ $attribute->articles_count ?? 0 }}"
                                        data-variants-count="{{ $attribute->variants_count ?? 0 }}"
                                        class="deactivate-btn group relative p-1 lg:p-2 text-red-600 hover:text-red-900 hover:bg-red-50 rounded-lg transition-colors duration-200"
                                        title="Désactiver cet attribut">
                                    <i class="fa-solid fa-eye-slash w-4 h-4 lg:w-5 lg:h-5"></i>
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
                                <form action="{{ route('settings.attributes.activate', $attribute) }}"
                                      method="POST"
                                      class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="group relative p-1 lg:p-2 text-green-600 hover:text-green-900 hover:bg-green-50 rounded-lg transition-colors duration-200"
                                            title="Réactiver cet attribut">
                                        <i class="fa-solid fa-eye w-4 h-4 lg:w-5 lg:h-5"></i>
                                        <!-- Tooltip -->
                                        <div
                                            class="hidden lg:block absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap z-10">
                                            Réactiver
                                            <div
                                                class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-900"></div>
                                        </div>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($attributes->hasPages())
        <div class="px-6 py-3 border-t border-gray-200">
            {{ $attributes->appends(request()->query())->links() }}
        </div>
    @endif
@else
    <div class="px-6 py-4 text-center text-gray-500">
        Aucun attribut trouvé.
        <a href="{{ route('settings.attributes.create') }}"
           class="text-indigo-600 hover:text-indigo-900">
            Créer le premier attribut
        </a>
    </div>
@endif

<script>
    // Réattacher les event listeners pour les boutons de désactivation
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.deactivate-btn').forEach(button => {
            button.addEventListener('click', function () {
                const attributeId = this.getAttribute('data-attribute-id');
                const attributeName = this.getAttribute('data-attribute-name');
                const articlesCount = parseInt(this.getAttribute('data-articles-count')) || 0;
                const variantsCount = parseInt(this.getAttribute('data-variants-count')) || 0;

                showDeactivateModal(attributeId, attributeName, articlesCount, variantsCount);
            });
        });
    });
</script>
