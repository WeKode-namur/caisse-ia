{{-- resources/views/panel/inventory/partials/drafts-table.blade.php --}}

@if($drafts->count() > 0)
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Article
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Catégorie
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Référence
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Stock
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Modification
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($drafts as $draft)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150" data-draft-id="{{ $draft->id }}">
                        <!-- Nom de l'article -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-8 w-8">
                                    <div class="h-8 w-8 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                                        <i class="fas fa-file-alt text-gray-500 dark:text-gray-400 text-sm"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $draft->name ?: 'Article sans nom' }}
                                    </div>
                                    @if($draft->description)
                                        <div class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-xs">
                                            {{ Str::limit($draft->description, 50) }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <!-- Catégorie/Type/Sous-type -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-gray-100">
                                {{ $draft->category_path }}
                            </div>
                        </td>

                        <!-- Référence -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-gray-100">
                                {{ $draft->reference ?: '-' }}
                            </div>
                        </td>

                        <!-- Stock total -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-gray-100">
                                @if($draft->total_stock > 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            {{ $draft->total_stock }} articles
                                        </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                            Aucun stock
                                        </span>
                                @endif
                            </div>
                        </td>

                        <!-- Dernière modification -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $draft->formatted_updated_at }}
                            </div>
                        </td>

                        <!-- Actions -->
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                <!-- Bouton Continuer -->
                                <a href="{{ route('inventory.create.step.one', $draft->id) }}"
                                   class="inline-flex items-center px-3 py-1 border border-transparent text-xs rounded-md text-blue-600 bg-blue-100 hover:bg-blue-200 dark:bg-blue-900 dark:text-blue-300 dark:hover:bg-blue-800 transition-colors duration-150">
                                    <i class="fas fa-edit mr-1"></i>
                                    Continuer
                                </a>

                                <!-- Dropdown Actions -->
                                <div class="relative" x-data="{ open: false }">
                                    <button @click="open = !open"
                                            class="inline-flex items-center p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-150">
                                        <i class="fas fa-ellipsis-v text-sm"></i>
                                    </button>

                                    <div x-show="open"
                                         @click.away="open = false"
                                         x-transition:enter="transition ease-out duration-100"
                                         x-transition:enter-start="transform opacity-0 scale-95"
                                         x-transition:enter-end="transform opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-75"
                                         x-transition:leave-start="transform opacity-100 scale-100"
                                         x-transition:leave-end="transform opacity-0 scale-95"
                                         class="absolute right-0 mt-2 w-40 bg-white dark:bg-gray-700 rounded-md shadow-lg z-10 border border-gray-200 dark:border-gray-600"
                                         style="display: none;">

                                        <button @click="duplicateDraft({{ $draft->id }}); open = false"
                                                class="flex items-center w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-150">
                                            <i class="fas fa-copy mr-2 text-xs"></i>
                                            Dupliquer
                                        </button>

                                        <button @click="deleteDraft({{ $draft->id }}); open = false"
                                                class="flex items-center w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-150">
                                            <i class="fas fa-trash mr-2 text-xs"></i>
                                            Supprimer
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($drafts->hasPages())
            <div class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div class="flex-1 flex sm:hidden {{ (!$drafts->previousPageUrl()) ? 'justify-end' : ((!$drafts->nextPageUrl()) ? 'justify-start' : 'justify-between') }}">
                        @if($drafts->previousPageUrl())
                            <a href="#" onclick="loadDraftsPage('{{ $drafts->previousPageUrl() }}')"
                               class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Précédent
                            </a>
                        @endif
                        @if($drafts->nextPageUrl())
                            <a href="#" onclick="loadDraftsPage('{{ $drafts->nextPageUrl() }}')"
                               class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Suivant
                            </a>
                        @endif
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                Affichage de
                                <span class="font-medium">{{ $drafts->firstItem() }}</span>
                                à
                                <span class="font-medium">{{ $drafts->lastItem() }}</span>
                                sur
                                <span class="font-medium">{{ $drafts->total() }}</span>
                                résultats
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                @foreach($drafts->getUrlRange(1, $drafts->lastPage()) as $page => $url)
                                    @if($page == $drafts->currentPage())
                                        <span class="relative inline-flex items-center px-4 py-2 border border-blue-500 bg-blue-50 text-sm font-medium text-blue-600">
                                            {{ $page }}
                                        </span>
                                    @else
                                        <a href="#" onclick="loadDraftsPage('{{ $url }}')"
                                           class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                            {{ $page }}
                                        </a>
                                    @endif
                                @endforeach
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@else
    <!-- État vide -->
    <div class="text-center py-12 bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="mx-auto h-12 w-12 text-gray-400 mb-4">
            <i class="fas fa-file-alt text-4xl"></i>
        </div>
        <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Aucun brouillon trouvé</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
            @if(request('search'))
                Aucun brouillon ne correspond à votre recherche "{{ request('search') }}".
            @else
                Commencez par créer un nouvel article pour voir vos brouillons ici.
            @endif
        </p>
        @if(request('search'))
            <button onclick="clearSearch()"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-blue-600 bg-blue-100 hover:bg-blue-200">
                <i class="fas fa-times mr-2"></i>
                Effacer la recherche
            </button>
        @endif
    </div>
@endif
