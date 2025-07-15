<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- En-tête -->
            <div
                class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg mb-6">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Gestion des
                                attributs</h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Gérez les attributs et leurs
                                valeurs pour vos articles</p>
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

                <!-- Filtres -->
                <div class="p-4">
                    <form id="filters-form" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                        <div>
                            <input type="text"
                                   name="search"
                                   id="search-input"
                                   placeholder="Rechercher un attribut..."
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>

                        <div>
                            <select name="type"
                                    id="type-select"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <option value="all">Tous les types</option>
                                <option value="number">Nombre</option>
                                <option value="select">Sélection</option>
                                <option value="color">Couleur</option>
                            </select>
                        </div>

                        <div>
                            <select name="status"
                                    id="status-select"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <option value="active" selected>Actifs</option>
                                <option value="inactive">Inactifs</option>
                                <option value="all">Tous les statuts</option>
                            </select>
                        </div>

                        <div>
                            <select name="sort"
                                    id="sort-select"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <option value="name">Nom A-Z</option>
                                <option value="type">Type</option>
                                <option value="created_at">Plus récent</option>
                                <option value="values_count">Plus de valeurs</option>
                            </select>
                        </div>

                        <div class="hidden">
                            <button type="button" id="filter-btn">Filtrer</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Messages de succès/erreur -->
            @if(session('success'))
                <div class="mb-6 px-6 py-3 bg-green-100 border-l-4 border-green-500">
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
                <div class="mb-6 px-6 py-3 bg-red-100 border-l-4 border-red-500">
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

            <!-- Statistiques -->
            <div id="stats-container" class="mb-6">
                <x-loading-spinner message="Chargement des statistiques..."/>
            </div>

            <!-- Tableau des attributs -->
            <div
                class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg mb-6">
                <div class="p-4">
                    <!-- Container pour le tableau -->
                    <div id="attributes-container">
                        <!-- Le tableau sera chargé ici via AJAX -->
                        <x-loading-spinner message="Chargement des attributs..."/>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmation de désactivation -->
    <x-modal name="deactivate-attribute" title="Confirmer la désactivation" icon="exclamation-triangle" iconColor="red">
        <div class="text-center">
            <p class="text-sm text-gray-500" id="deactivateMessage">
                Êtes-vous sûr de vouloir désactiver cet attribut ?
            </p>
        </div>

        <x-slot name="actions">
            <form id="deactivateForm" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-24 mr-2 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-300">
                    Désactiver
                </button>
            </form>
            <button onclick="closeModal('deactivate-attribute')"
                    class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-24 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
                Annuler
            </button>
        </x-slot>
    </x-modal>

    @push('scripts')
        <script>
            class AttributeManager {
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
                    this.loadAttributes(); // Chargement initial
                    this.loadStats(); // Chargement initial des stats
                }

                bindEvents() {
                    // Recherche avec debounce
                    document.getElementById('search-input').addEventListener('input', (e) => {
                        clearTimeout(this.searchTimeout);
                        this.searchTimeout = setTimeout(() => {
                            this.currentPage = 1;
                            this.loadAttributes();
                            this.loadStats();
                        }, 500);
                    });

                    // Changement des filtres
                    ['type-select', 'status-select', 'sort-select'].forEach(id => {
                        document.getElementById(id).addEventListener('change', () => {
                            this.currentPage = 1;
                            this.loadAttributes();
                            this.loadStats();
                        });
                    });

                    // Bouton filtrer
                    document.getElementById('filter-btn').addEventListener('click', () => {
                        this.currentPage = 1;
                        this.loadAttributes();
                        this.loadStats();
                    });
                }

                async loadAttributes() {
                    if (this.isLoading) return;

                    this.isLoading = true;
                    const container = document.getElementById('attributes-container');

                    // Afficher le spinner
                    container.innerHTML = await LoadingUtils.getLoadingHtml('Chargement des attributs...');

                    try {
                        const formData = new FormData(document.getElementById('filters-form'));
                        formData.append('page', this.currentPage);

                        const params = new URLSearchParams();
                        for (let [key, value] of formData.entries()) {
                            if (value) params.append(key, value);
                        }

                        const response = await fetch(`{{ route('settings.attributes.index') }}?${params}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'text/html'
                            }
                        });

                        if (!response.ok) {
                            throw new Error('Erreur lors du chargement des attributs');
                        }

                        const html = await response.text();
                        container.innerHTML = html;

                        // Réattacher les event listeners pour les boutons de désactivation
                        this.attachDeactivateListeners();

                    } catch (error) {
                        console.error('Erreur lors du chargement des attributs:', error);
                        container.innerHTML = '<div class="text-center text-red-500 py-4">Erreur lors du chargement des attributs</div>';
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

                        const response = await fetch(`{{ route('settings.attributes.stats') }}?${params}`, {
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
                    const container = document.getElementById('stats-container');
                    container.innerHTML = `
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- Total des attributs -->
                            <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 rounded-lg p-4 shadow-sm">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fa-solid fa-tags w-6 h-6 text-blue-500"></i>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total</p>
                                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">${stats.total}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Attributs actifs -->
                            <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 rounded-lg p-4 shadow-sm">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fa-solid fa-eye w-6 h-6 text-green-500"></i>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Actifs</p>
                                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">${stats.active}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Attributs inactifs -->
                            <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 rounded-lg p-4 shadow-sm">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fa-solid fa-eye-slash w-6 h-6 text-gray-500"></i>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Inactifs</p>
                                        <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">${stats.inactive}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Répartition par type -->
                            <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 rounded-lg p-4 shadow-sm">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fa-solid fa-chart-pie w-6 h-6 text-purple-500"></i>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Types</p>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            <div class="flex items-center justify-between">
                                                <span>Nombre: ${stats.by_type.number}</span>
                                                <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                            </div>
                                            <div class="flex items-center justify-between">
                                                <span>Sélection: ${stats.by_type.select}</span>
                                                <span class="w-2 h-2 bg-purple-500 rounded-full"></span>
                                            </div>
                                            <div class="flex items-center justify-between">
                                                <span>Couleur: ${stats.by_type.color}</span>
                                                <span class="w-2 h-2 bg-pink-500 rounded-full"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                }

                attachDeactivateListeners() {
                    document.querySelectorAll('.deactivate-btn').forEach(button => {
                        button.addEventListener('click', function () {
                            const attributeId = this.getAttribute('data-attribute-id');
                            const attributeName = this.getAttribute('data-attribute-name');
                            const articlesCount = parseInt(this.getAttribute('data-articles-count')) || 0;
                            const variantsCount = parseInt(this.getAttribute('data-variants-count')) || 0;

                            showDeactivateModal(attributeId, attributeName, articlesCount, variantsCount);
                        });
                    });
                }
            }

            function showDeactivateModal(attributeId, attributeName, articlesCount, variantsCount) {
                console.log('showDeactivateModal appelé avec:', {
                    attributeId,
                    attributeName,
                    articlesCount,
                    variantsCount
                });

                const form = document.getElementById('deactivateForm');
                const message = document.getElementById('deactivateMessage');

                if (!form || !message) {
                    console.error('Éléments du modal non trouvés');
                    return;
                }

                // Mettre à jour le formulaire
                form.action = `/settings/attributes/${attributeId}`;

                // Mettre à jour le message
                if (articlesCount > 0 || variantsCount > 0) {
                    message.innerHTML = `Êtes-vous sûr de vouloir désactiver l'attribut <strong>${attributeName}</strong> ?<br><br>Vous avez actuellement <strong>${articlesCount} articles</strong> et <strong>${variantsCount} variants</strong> encore liés à cet attribut.<br><br><em>Note : Vous pourrez réactiver cet attribut depuis la section archives.</em>`;
                } else {
                    message.innerHTML = `Êtes-vous sûr de vouloir désactiver l'attribut <strong>${attributeName}</strong> ?<br><br><em>Note : Vous pourrez réactiver cet attribut depuis la section archives.</em>`;
                }

                openModal('deactivate-attribute');
            }

            // Initialisation
            document.addEventListener('DOMContentLoaded', function () {
                new AttributeManager();
            });
        </script>
    @endpush
</x-app-layout>
