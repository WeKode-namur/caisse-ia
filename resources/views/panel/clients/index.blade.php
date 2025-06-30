<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestion des Clients') }}
        </h2>
    </x-slot>

    <div class="lg:py-12">
        <div class="max-w-7xl mx-auto px-0 lg:px-8">
            <!-- En-tête avec filtres -->
            <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg mb-6">
                <div class="text-gray-900 dark:text-gray-50 px-3 py-2 border-b border-gray-300 dark:border-gray-700 flex items-center justify-between">
                    <h1 class="font-bold lg:text-2xl text-xl">Clients</h1>
                    <a href="{{ route('clients.create') }}" class="bg-green-500 text-white rounded py-1 px-3 hover:opacity-75 hover:scale-105">
                        <i class="fas fa-plus"></i>
                    </a>
                </div>

                <!-- Filtres -->
                <div class="p-4">
                    <form id="filters-form" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                        <div>
                            <input type="text"
                                   name="search"
                                   id="search-input"
                                   placeholder="Rechercher un client..."
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>

                        <div>
                            <select name="status"
                                    id="status-select"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <option value="all">Tous les statuts</option>
                                <option value="active" selected>Actifs</option>
                                <option value="inactive">Inactifs</option>
                            </select>
                        </div>

                        <div>
                            <select name="type"
                                    id="type-select"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <option value="all">Tous les types</option>
                                <option value="customer">Clients particuliers</option>
                                <option value="company">Entreprises</option>
                            </select>
                        </div>

                        <div>
                            <select name="sort"
                                    id="sort-select"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <option value="name">Nom A-Z</option>
                                <option value="created_at">Plus récent</option>
                                <option value="loyalty_points">Points fidélité</option>
                            </select>
                        </div>

                        <div class="hidden">
                            <button type="button" id="filter-btn">Filtrer</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Statistiques -->
            <div id="stats-container" class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <x-loading-spinner message="Chargement des statistiques..." />
            </div>

            <!-- Tableau des clients -->
            <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg mb-6">
                <div class="p-4">
                    <!-- Container pour le tableau -->
                    <div id="clients-container">
                        <!-- Le tableau sera chargé ici via AJAX -->
                        <x-loading-spinner message="Chargement des clients..." />
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            class ClientManager {
                constructor() {
                    this.currentPage = 1;
                    this.isLoading = false;
                    this.searchTimeout = null;

                    // Par défaut, statut = actif si rien n'est sélectionné
                    const statusSelect = document.getElementById('status-select');
                    if (!statusSelect.value || statusSelect.value === 'all') {
                        statusSelect.value = 'active';
                    }

                    this.bindEvents();
                    this.loadClients(); // Chargement initial
                    this.loadStats(); // Chargement initial des stats
                }

                bindEvents() {
                    // Recherche avec debounce
                    document.getElementById('search-input').addEventListener('input', (e) => {
                        clearTimeout(this.searchTimeout);
                        this.searchTimeout = setTimeout(() => {
                            this.currentPage = 1;
                            this.loadClients();
                            this.loadStats();
                        }, 500);
                    });

                    // Changement des filtres
                    ['status-select', 'type-select', 'sort-select'].forEach(id => {
                        document.getElementById(id).addEventListener('change', () => {
                            this.currentPage = 1;
                            this.loadClients();
                            this.loadStats();
                        });
                    });

                    // Bouton filtrer
                    document.getElementById('filter-btn').addEventListener('click', () => {
                        this.currentPage = 1;
                        this.loadClients();
                        this.loadStats();
                    });

                    // Gérer les clics sur la pagination (délégation d'événements)
                    document.addEventListener('click', (e) => {
                        if (e.target.classList.contains('pagination-btn')) {
                            e.preventDefault();
                            this.currentPage = parseInt(e.target.dataset.page);
                            this.loadClients();
                        }
                    });

                    // Gérer les clics sur les clients (délégation d'événements)
                    document.addEventListener('click', (e) => {
                        const clientRow = e.target.closest('.article-row');
                        if (clientRow) {
                            const clientId = clientRow.dataset.articleId;
                            // Redirection vers la page de détails du client
                            // Note: Cette logique devra être adaptée selon le type de client
                            window.location.href = "{{ route('clients.customers.show', ':id') }}".replace(':id', clientId);
                        }
                    });
                }

                async loadClients() {
                    if (this.isLoading) return;

                    this.isLoading = true;
                    const container = document.getElementById('clients-container');

                    // Afficher le spinner
                    container.innerHTML = await LoadingUtils.getLoadingHtml('Chargement des clients...');

                    try {
                        const formData = new FormData(document.getElementById('filters-form'));
                        formData.append('page', this.currentPage);

                        const params = new URLSearchParams();
                        for (let [key, value] of formData.entries()) {
                            if (value) params.append(key, value);
                        }

                        const response = await fetch(`{{ route('clients.index') }}?${params}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'text/html'
                            }
                        });

                        if (!response.ok) {
                            throw new Error('Erreur lors du chargement des clients');
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
                                Impossible de charger les clients. Veuillez réessayer.
                            </p>
                            <button onclick="clientManager.loadClients()"
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

                async loadStats() {
                    try {
                        const formData = new FormData(document.getElementById('filters-form'));
                        const params = new URLSearchParams();
                        for (let [key, value] of formData.entries()) {
                            if (value) params.append(key, value);
                        }

                        const response = await fetch(`{{ route('clients.stats') }}?${params}`, {
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

                renderStats(stats) {
                    const statsContainer = document.getElementById('stats-container');

                    statsContainer.innerHTML = `
                        <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg">
                            <div class="p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                            ${stats.total_customers}
                                        </div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">Clients Particuliers</div>
                                    </div>
                                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-user text-blue-600 dark:text-blue-400"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg">
                            <div class="p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                                            ${stats.total_companies}
                                        </div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">Entreprises</div>
                                    </div>
                                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-building text-green-600 dark:text-green-400"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg">
                            <div class="p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">
                                            ${stats.new_this_week}
                                        </div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">Nouveaux cette semaine</div>
                                    </div>
                                    <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-clock text-yellow-600 dark:text-yellow-400"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg">
                            <div class="p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                                            ${new Intl.NumberFormat().format(stats.total_loyalty_points)}
                                        </div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400">Points Fidélité</div>
                                    </div>
                                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-star text-purple-600 dark:text-purple-400"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                }

                // Méthode pour rafraîchir les données
                refresh() {
                    this.currentPage = 1;
                    this.loadClients();
                    this.loadStats();
                }

                // Méthode pour réinitialiser les filtres
                resetFilters() {
                    document.getElementById('filters-form').reset();
                    this.currentPage = 1;
                    this.loadClients();
                    this.loadStats();
                }
            }

            // Initialiser le gestionnaire de clients
            let clientManager;
            document.addEventListener('DOMContentLoaded', function() {
                clientManager = new ClientManager();
            });

            // Rafraîchir automatiquement toutes les 5 minutes
            setInterval(() => {
                if (clientManager && !clientManager.isLoading) {
                    clientManager.loadClients();
                    clientManager.loadStats();
                }
            }, 5 * 60 * 1000);
        </script>
    @endpush
</x-app-layout>
