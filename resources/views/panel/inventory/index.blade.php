@php
    // Fallback pour éviter l'erreur si la variable n'est pas transmise
    $fournisseurs = $fournisseurs ?? collect();
@endphp

<x-app-layout>
    <div class="lg:py-12">
        <div class="max-w-7xl mx-auto px-0 lg:px-8">
            <!-- En-tête -->
            <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg mb-6">
                <div class="text-gray-900 dark:text-gray-50 px-3 py-2 border-b border-gray-300 dark:border-gray-700 flex items-center justify-between">
                    <h1 class="font-bold lg:text-2xl text-xl">Inventaire</h1>
                    <a href="{{ route('inventory.create.index') }}" class="bg-green-500 text-white rounded py-1 px-3 hover:opacity-75 hover:scale-105">
                        <i class="fas fa-plus"></i>
                    </a>
                </div>

                <!-- Filtres -->
                <div class="p-4">
                    <form id="filters-form" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-4">
                        <div>
                            <input type="text"
                                   name="search"
                                   id="search-input"
                                   placeholder="Rechercher un article..."
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>

                        <div>
                            <select name="category_id"
                                    id="category-select"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <option value="">Toutes les catégories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <select name="stock_status"
                                    id="stock-select"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <option value="">Tous les stocks</option>
                                @foreach($stockStatuses as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <select name="status"
                                    id="status-select"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                @foreach($statusOptions as $key => $label)
                                    <option value="{{ $key }}" {{ $key === 'active_published' ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <select name="sort_by"
                                    id="sort-select"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <option value="created_at">Plus récent</option>
                                <option value="name">Nom A-Z</option>
                                <option value="updated_at">Dernière modification</option>
                            </select>
                        </div>

                        @suppliersEnabled
                        <div>
                            <select name="fournisseur_id" id="fournisseur-select" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <option value="">Tous les fournisseurs</option>
                                @foreach($fournisseurs as $fournisseur)
                                    <option value="{{ $fournisseur->id }}">{{ $fournisseur->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endsuppliersEnabled
                    </form>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                @foreach($stats as $stat)
                    <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg">
                        <div class="p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-2xl font-bold text-{{ $stat['color'] }}-600 dark:text-{{ $stat['color'] }}-400">
                                        {{ $stat['value'] }}
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ $stat['label'] }}</div>
                                </div>
                                <div class="w-12 h-12 bg-{{ $stat['color'] }}-100 dark:bg-{{ $stat['color'] }}-900/30 rounded-lg flex items-center justify-center">
                                    <i class="{{ $stat['icon'] }} text-{{ $stat['color'] }}-600 dark:text-{{ $stat['color'] }}-400"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Tableau des articles -->
            <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg mb-6">
                <div class="p-4">
                    <!-- Container pour le tableau -->
                    <div id="articles-container">
                        <!-- Le tableau sera chargé ici via AJAX -->
                        <x-loading-spinner message="Chargement des articles..." />
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            class InventoryManager {
                constructor() {
                    this.currentPage = 1;
                    this.isLoading = false;
                    this.searchTimeout = null;

                    this.bindEvents();
                    this.loadArticles(); // Chargement initial
                }

                bindEvents() {
                    // Recherche avec debounce
                    document.getElementById('search-input').addEventListener('input', (e) => {
                        clearTimeout(this.searchTimeout);
                        this.searchTimeout = setTimeout(() => {
                            this.currentPage = 1;
                            this.loadArticles();
                        }, 500);
                    });

                    // Changement des filtres
                    ['category-select', 'stock-select', 'status-select', 'sort-select'].forEach(id => {
                        document.getElementById(id).addEventListener('change', () => {
                            this.currentPage = 1;
                            this.loadArticles();
                            this.updateStats(); // Mettre à jour les statistiques
                        });
                    });

                    // Gérer les clics sur la pagination (délégation d'événements)
                    document.addEventListener('click', (e) => {
                        if (e.target.classList.contains('pagination-btn')) {
                            e.preventDefault();
                            this.currentPage = parseInt(e.target.dataset.page);
                            this.loadArticles();
                        }
                    });

                    // Gérer les clics sur les articles (délégation d'événements)
                    document.addEventListener('click', (e) => {
                        const articleRow = e.target.closest('.article-row');
                        if (articleRow) {
                            const articleId = articleRow.dataset.articleId;
                            window.location.href = "{{ route('inventory.show', ':id') }}".replace(':id', articleId);
                        }
                    });

                    if (document.getElementById('fournisseur-select')) {
                        document.getElementById('fournisseur-select').addEventListener('change', () => {
                            this.currentPage = 1;
                            this.loadArticles();
                            this.updateStats();
                        });
                    }
                }

                async loadArticles() {
                    if (this.isLoading) return;

                    this.isLoading = true;
                    const container = document.getElementById('articles-container');

                    // Afficher le spinner
                    container.innerHTML = await LoadingUtils.getLoadingHtml('Chargement des articles...');

                    try {
                        const formData = new FormData(document.getElementById('filters-form'));
                        formData.append('page', this.currentPage);

                        const params = new URLSearchParams();
                        for (let [key, value] of formData.entries()) {
                            if (value) params.append(key, value);
                        }

                        const response = await fetch(`{{ route('inventory.index') }}?${params}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'text/html'
                            }
                        });

                        if (!response.ok) {
                            throw new Error('Erreur lors du chargement des articles');
                        }

                        const html = await response.text();
                        container.innerHTML = html;

                    } catch (error) {
                        console.error('Erreur:', error);
                        container.innerHTML = `
                        <div class="text-center py-8">
                            <div class="text-red-500 mb-2">
                                <i class="fas fa-exclamation-triangle text-4xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">Erreur de chargement</h3>
                            <p class="text-gray-500 dark:text-gray-400 mb-4">
                                Impossible de charger les articles. Veuillez réessayer.
                            </p>
                            <button onclick="inventoryManager.loadArticles()"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-refresh mr-2"></i>
                                Réessayer
                            </button>
                        </div>
                    `;
                    } finally {
                        this.isLoading = false;
                    }
                }

                // Méthode pour mettre à jour les statistiques
                async updateStats() {
                    try {
                        const formData = new FormData(document.getElementById('filters-form'));
                        const params = new URLSearchParams();
                        for (let [key, value] of formData.entries()) {
                            if (value) params.append(key, value);
                        }

                        const response = await fetch(`{{ route('inventory.stats') }}?${params}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });

                        if (response.ok) {
                            const stats = await response.json();
                            this.renderStats(stats);
                        }
                    } catch (error) {
                        console.error('Erreur lors de la mise à jour des stats:', error);
                    }
                }

                // Méthode pour afficher les statistiques (version plus robuste)
                renderStats(stats) {
                    const statsContainer = document.querySelector('.grid.grid-cols-2.lg\\:grid-cols-4');
                    if (!statsContainer) return;

                    // Vider le container
                    statsContainer.innerHTML = '';

                    // Créer chaque stat
                    stats.forEach(stat => {
                        const statDiv = document.createElement('div');
                        statDiv.className = 'bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg';

                        statDiv.innerHTML = `
                        <div class="p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-2xl font-bold text-${stat.color}-600 dark:text-${stat.color}-400">
                                        ${stat.value}
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">${stat.label}</div>
                                </div>
                                <div class="w-12 h-12 bg-${stat.color}-100 dark:bg-${stat.color}-900/30 rounded-lg flex items-center justify-center">
                                    <i class="${stat.icon} text-${stat.color}-600 dark:text-${stat.color}-400"></i>
                                </div>
                            </div>
                        </div>
                    `;

                        statsContainer.appendChild(statDiv);
                    });
                }

                // Méthode pour rafraîchir les données
                refresh() {
                    this.currentPage = 1;
                    this.loadArticles();
                    this.updateStats();
                }

                // Méthode pour réinitialiser les filtres
                resetFilters() {
                    document.getElementById('filters-form').reset();
                    this.currentPage = 1;
                    this.loadArticles();
                }
            }

            // Initialiser le gestionnaire d'inventaire
            let inventoryManager;
            document.addEventListener('DOMContentLoaded', function() {
                inventoryManager = new InventoryManager();
            });

            // Rafraîchir automatiquement toutes les 5 minutes
            setInterval(() => {
                if (inventoryManager && !inventoryManager.isLoading) {
                    inventoryManager.loadArticles();
                }
            }, 5 * 60 * 1000);
        </script>
    @endpush
</x-app-layout>
