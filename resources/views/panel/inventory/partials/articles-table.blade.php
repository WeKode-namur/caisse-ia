<div class="overflow-x-auto">
    <div class="space-y-2">
        <!-- En-tête -->
        <div class="hidden md:grid gap-4 py-3 px-4 bg-gray-100 dark:bg-gray-700 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300"
             style="grid-template-columns: 2fr 1fr 1fr 1fr {{ config('custom.suppliers_enabled') ? '1fr' : ''}} 1fr 40px;">
            <div>Article</div>
            <div class="text-center">Catégorie</div>
            <div class="text-center">Prix de vente</div>
            <div class="text-center">Stock</div>
            @suppliersEnabled
            <div class="text-center">Fournisseur</div>
            @endsuppliersEnabled
            <div class="text-center">Statut</div>
            <div></div>
        </div>

        <!-- Articles -->
        <div class="space-y-1">
            @forelse($articles as $article)
                <div class="article-row gap-4 py-4 px-4 bg-white dark:bg-gray-800 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors duration-200 border border-gray-200 dark:border-gray-600"
                     data-article-id="{{ $article->id }}"
                     style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr {{ config('custom.suppliers_enabled') ? '1fr' : ''}} 1fr 40px;">

                    <!-- Nom et code-barres -->
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-gray-200 dark:bg-gray-600 rounded-lg flex items-center justify-center">
                            @if($article->variants->first()?->medias->first())
                                <img src="{{ $article->variants->first()->medias->first()->url }}"
                                     alt="{{ $article->name }}"
                                     class="w-full h-full object-cover rounded-lg">
                            @else
                                <span class="text-xs text-gray-500">IMG</span>
                            @endif
                        </div>
                        <div>
                            <div class="font-medium">{{ $article->name }}</div>
                            <div class="text-sm text-gray-500">
                                @if($article->variants->first()?->barcode)
                                    {{ $article->variants->first()->barcode }}
                                @else
                                    <span class="italic">Aucun code-barres</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Catégorie -->
                    <div class="flex items-center justify-center">
                        @if($article->category)
                            <span class="px-2 py-1 text-xs bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded-full">
                                {{ $article->category->name }}
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded-full">
                                Non classé
                            </span>
                        @endif
                    </div>

                    <!-- Prix de vente -->
                    <div class="flex items-center justify-center font-medium">
                        @if($article->sell_price)
                            € {{ number_format($article->sell_price, 2) }}
                        @else
                            <span class="text-gray-400 italic">Non défini</span>
                        @endif
                    </div>

                    <!-- Stock -->
                    <div class="flex items-center justify-center">
                        @php
                            $totalStock = $article->total_stock;
                        @endphp

                        @if($totalStock == 0)
                            <span class="text-red-600 dark:text-red-400 font-medium">{{ $totalStock }}</span>
                        @elseif($totalStock <= 5)
                            <span class="text-orange-600 dark:text-orange-400 font-medium">{{ $totalStock }}</span>
                        @else
                            <span class="text-green-600 dark:text-green-400 font-medium">{{ $totalStock }}</span>
                        @endif
                    </div>

                    <!-- Fournisseur -->
                    @suppliersEnabled
                        <div class="flex items-center justify-center">
                                @if($article->fournisseur)
                                    <span class="px-2 py-1 text-xs bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 rounded-full">
                                        {{ $article->fournisseur->name }}
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded-full">
                                        Aucun
                                    </span>
                                @endif
                        </div>
                    @endsuppliersEnabled

                    <!-- Statut -->
                    <div class="flex items-center justify-center">
                        @if($article->is_active ?? true)
                            <span class="px-2 py-1 text-xs bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-full">
                                Actif
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-full">
                                Inactif
                            </span>
                        @endif
                    </div>

                    <!-- Flèche -->
                    <div class="flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </div>
            @empty
                <div class="text-center py-8">
                    <div class="text-gray-500 dark:text-gray-400 mb-2">
                        <i class="fas fa-inbox text-4xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">Aucun article trouvé</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-4">
                        Aucun article ne correspond à vos critères de recherche.
                    </p>
                    <a href="{{ route('inventory.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Ajouter un article
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Pagination -->
@if($articles->hasPages())
    <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-200 dark:border-gray-600">
        <div class="text-sm text-gray-600 dark:text-gray-400">
            Affichage de {{ $articles->firstItem() ?? 0 }} à {{ $articles->lastItem() ?? 0 }} sur {{ $articles->total() }} articles
        </div>

        <div class="flex space-x-2">
            @if ($articles->onFirstPage())
                <span class="px-3 py-1 text-sm bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500 rounded cursor-not-allowed">
                    Précédent
                </span>
            @else
                <button class="px-3 py-1 text-sm bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded hover:bg-gray-200 dark:hover:bg-gray-600 pagination-btn"
                        data-page="{{ $articles->currentPage() - 1 }}">
                    Précédent
                </button>
            @endif

            @foreach ($articles->getUrlRange(max(1, $articles->currentPage() - 2), min($articles->lastPage(), $articles->currentPage() + 2)) as $page => $url)
                @if ($page == $articles->currentPage())
                    <span class="px-3 py-1 text-sm bg-blue-500 text-white rounded">{{ $page }}</span>
                @else
                    <button class="px-3 py-1 text-sm bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded hover:bg-gray-200 dark:hover:bg-gray-600 pagination-btn"
                            data-page="{{ $page }}">
                        {{ $page }}
                    </button>
                @endif
            @endforeach

            @if ($articles->hasMorePages())
                <button class="px-3 py-1 text-sm bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded hover:bg-gray-200 dark:hover:bg-gray-600 pagination-btn"
                        data-page="{{ $articles->currentPage() + 1 }}">
                    Suivant
                </button>
            @else
                <span class="px-3 py-1 text-sm bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500 rounded cursor-not-allowed">
                    Suivant
                </span>
            @endif
        </div>
    </div>
@endif
