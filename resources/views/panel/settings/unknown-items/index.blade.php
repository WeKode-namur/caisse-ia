<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- En-tête -->
            <div
                class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg mb-6">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Articles inconnus à
                                régulariser</h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Gérez les articles non identifiés
                                lors des transactions</p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <button onclick="genererRapport()"
                                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <i class="fas fa-file-pdf mr-2"></i>
                                Rapport PDF
                            </button>
                        </div>
                    </div>
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
                            <select name="status"
                                    id="status-select"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <option value="">Tous les statuts</option>
                                <option value="pending">À régulariser</option>
                                <option value="regularized">Régularisés</option>
                                <option value="non_identifiable">Non identifiables</option>
                            </select>
                        </div>

                        <div>
                            <input type="date"
                                   name="date_start"
                                   id="date-start"
                                   placeholder="Date début"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>

                        <div>
                            <input type="date"
                                   name="date_end"
                                   id="date-end"
                                   placeholder="Date fin"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none">
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

            <!-- Tableau des articles inconnus -->
            <div
                class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg mb-6">
                <div class="p-4">
                    <!-- Container pour le tableau -->
                    <div id="table-container">
                        <!-- Le tableau sera chargé ici via AJAX -->
                        <x-loading-spinner message="Chargement des articles inconnus..."/>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de régularisation -->
    <x-modal name="regularize-modal" size="4xl" :footer="false" icon="link" title="Régulariser l'article inconnu"
             iconColor="green">
        <form id="regularize-form" class="space-y-4">
            @csrf
            <input type="hidden" id="regularize-item-id" name="unknown_item_id">
            <input type="hidden" id="selected-variant-id" name="variant_id" required>

            <!-- Champ de recherche -->
            <div>
                <label for="search-input-modal" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Rechercher
                    un article *</label>
                <div class="relative">
                    <input type="text" id="search-input-modal"
                           class="w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 pl-10 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="Ex: Jordan xs, Chaussure Nike 42..."
                           autocomplete="off">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Tapez le nom de l'article avec la valeur
                    d'attribut (ex: Jordan xs)</p>
            </div>

            <!-- Résultats de recherche -->
            <div id="search-results" class="hidden">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Correspondances
                    trouvées</label>
                <div id="results-container"
                     class="space-y-2 max-h-64 overflow-y-auto border border-gray-200 dark:border-gray-600 rounded-lg p-3 bg-white dark:bg-gray-700">
                    <!-- Les résultats seront injectés ici -->
                </div>
            </div>

            <!-- Variant sélectionné -->
            <div id="selected-variant-info" class="hidden">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Variant
                    sélectionné</label>
                <div id="selected-variant-details"
                     class="bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 rounded-lg p-3">
                    <!-- Les détails du variant sélectionné seront affichés ici -->
                </div>

                <!-- Option de décompte de stock -->
                <div
                    class="mt-3 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="deduct-stock" name="deduct_stock" type="checkbox" checked
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="deduct-stock" class="font-medium text-blue-900 dark:text-blue-100">
                                Décompter le stock
                            </label>
                            <p class="text-blue-700 dark:text-blue-300 mt-1">
                                Si coché, le stock sera décompté du variant sélectionné selon la méthode FIFO.
                                Si décoché, la régularisation sera effectuée sans impact sur le stock.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <label for="regularize-notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes
                    (optionnel)</label>
                <textarea id="regularize-notes" name="note_interne" rows="3"
                          class="w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                          placeholder="Notes sur la régularisation ou explication pour article non identifiable"></textarea>
            </div>

            <!-- Messages d'erreur/succès -->
            <div id="modal-message" class="hidden">
                <div id="modal-message-content" class="p-3 rounded-lg text-sm"></div>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" id="non-identifiable-btn"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                    <i class="fas fa-times mr-2"></i>
                    Non identifiable
                </button>
                <button type="submit" id="submit-btn" disabled
                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Régulariser
                </button>
            </div>
        </form>
    </x-modal>

    <!-- Modal de rapport PDF -->
    <x-modal name="rapport-modal" size="lg" :footer="false" icon="file-pdf" title="Générer un rapport PDF"
             iconColor="blue">
        <form id="rapport-form" class="space-y-4">
            <div>
                <label for="rapport-date-start" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Date de début
                </label>
                <input type="date" id="rapport-date-start" name="date_start" required
                       class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
            </div>

            <div>
                <label for="rapport-date-end" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Date de fin
                </label>
                <input type="date" id="rapport-date-end" name="date_end" required
                       class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeModal('rapport-modal')"
                        class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    Annuler
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <i class="fas fa-file-pdf mr-2"></i>
                    Générer le rapport
                </button>
            </div>
        </form>
    </x-modal>

    @push('scripts')
        <script>
            class UnknownItemsManager {
                constructor() {
                    this.currentPage = 1;
                    this.isLoading = false;
                    this.searchTimeout = null;
                    this.searchTimeoutModal = null;
                    this.selectedVariant = null;
                    this.currentUnknownItemId = null;

                    this.bindEvents();
                    this.loadTableData();
                    this.loadStats();
                }

                bindEvents() {
                    // Recherche avec debounce
                    document.getElementById('search-input').addEventListener('input', (e) => {
                        clearTimeout(this.searchTimeout);
                        this.searchTimeout = setTimeout(() => {
                            this.currentPage = 1;
                            this.loadTableData();
                            this.loadStats();
                        }, 500);
                    });

                    // Changement des filtres
                    ['status-select', 'date-start', 'date-end'].forEach(id => {
                        const element = document.getElementById(id);
                        if (element) {
                            element.addEventListener('change', () => {
                                this.currentPage = 1;
                                this.loadTableData();
                                this.loadStats();
                            });
                        }
                    });

                    // Recherche dans le modal
                    document.getElementById('search-input-modal').addEventListener('input', (e) => {
                        const searchTerm = e.target.value.trim();

                        clearTimeout(this.searchTimeoutModal);

                        if (searchTerm.length < 2) {
                            document.getElementById('search-results').classList.add('hidden');
                            return;
                        }

                        this.searchTimeoutModal = setTimeout(() => {
                            this.searchArticles(searchTerm);
                        }, 300);
                    });

                    // Formulaires
                    document.getElementById('regularize-form').addEventListener('submit', (e) => {
                        e.preventDefault();
                        this.submitRegularize();
                    });

                    document.getElementById('rapport-form').addEventListener('submit', (e) => {
                        e.preventDefault();
                        this.generateReport();
                    });

                    // Bouton "Non identifiable" dans le modal
                    document.getElementById('non-identifiable-btn').addEventListener('click', (e) => {
                        e.preventDefault();
                        this.marquerNonIdentifiable();
                    });
                }

                loadTableData() {
                    if (this.isLoading) return;
                    this.isLoading = true;

                    const formData = new FormData(document.getElementById('filters-form'));
                    const params = new URLSearchParams(formData);
                    params.append('page', this.currentPage);

                    fetch(`{{ route('settings.unknown-items.index') }}?${params.toString()}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                        .then(response => response.text())
                        .then(html => {
                            document.getElementById('table-container').innerHTML = html;
                            this.isLoading = false;
                        })
                        .catch(error => {
                            console.error('Erreur lors du chargement:', error);
                            this.isLoading = false;
                        });
                }

                loadStats() {
                    const formData = new FormData(document.getElementById('filters-form'));
                    const params = new URLSearchParams(formData);

                    fetch(`{{ route('settings.unknown-items.index') }}?${params.toString()}&stats_only=1`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                        .then(response => response.text())
                        .then(html => {
                            document.getElementById('stats-container').innerHTML = html;
                        })
                        .catch(error => {
                            console.error('Erreur lors du chargement des stats:', error);
                        });
                }

                openRegularizeModal(unknownItemId) {
                    this.currentUnknownItemId = unknownItemId;
                    this.resetSearchForm();
                    openModal('regularize-modal');
                    // Remettre le bouton en mode normal
                    document.getElementById('submit-btn').textContent = 'Régulariser';
                    document.getElementById('submit-btn').onclick = null;
                }

                resetSearchForm() {
                    document.getElementById('search-input-modal').value = '';
                    document.getElementById('search-results').classList.add('hidden');
                    document.getElementById('selected-variant-info').classList.add('hidden');
                    document.getElementById('regularize-notes').value = '';
                    document.getElementById('submit-btn').disabled = true;
                    document.getElementById('regularize-item-id').value = this.currentUnknownItemId;
                    document.getElementById('modal-message').classList.add('hidden');
                    document.getElementById('deduct-stock').checked = true;
                    this.selectedVariant = null;
                }

                showModalMessage(message, type = 'error') {
                    const messageContainer = document.getElementById('modal-message');
                    const messageContent = document.getElementById('modal-message-content');

                    messageContainer.classList.remove('hidden');
                    messageContent.className = `p-3 rounded-lg text-sm ${
                        type === 'error'
                            ? 'bg-red-50 border border-red-200 text-red-700 dark:bg-red-900/20 dark:border-red-800 dark:text-red-400'
                            : 'bg-green-50 border border-green-200 text-green-700 dark:bg-green-900/20 dark:border-green-800 dark:text-green-400'
                    }`;
                    messageContent.innerHTML = `
                    <div class="flex items-start justify-between">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <i class="fas ${type === 'error' ? 'fa-exclamation-circle' : 'fa-check-circle'} mr-2"></i>
                            </div>
                            <div class="ml-2">
                                <p class="font-medium">${type === 'error' ? 'Erreur' : 'Succès'}</p>
                                <p class="mt-1">${message}</p>
                            </div>
                        </div>
                        <button type="button" onclick="unknownItemsManager.hideModalMessage()"
                                class="ml-3 flex-shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;

                    // Auto-hide après 5 secondes pour les succès
                    if (type === 'success') {
                        setTimeout(() => {
                            messageContainer.classList.add('hidden');
                        }, 5000);
                    }
                }

                hideModalMessage() {
                    document.getElementById('modal-message').classList.add('hidden');
                }

                searchArticles(searchTerm) {
                    fetch('{{ route("settings.unknown-items.search") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            search: searchTerm
                        }),
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                this.displaySearchResults(data.variants, searchTerm);
                            }
                        })
                        .catch(error => {
                            console.error('Erreur lors de la recherche:', error);
                        });
                }

                displaySearchResults(variants, searchTerm) {
                    const container = document.getElementById('results-container');
                    const resultsDiv = document.getElementById('search-results');

                    if (variants.length === 0) {
                        container.innerHTML = '<p class="text-gray-500 text-sm">Aucune correspondance trouvée</p>';
                        resultsDiv.classList.remove('hidden');
                        return;
                    }

                    container.innerHTML = variants.map(variant => {
                        const attributes = variant.attribute_values ? variant.attribute_values.map(attr =>
                            `<span class="inline-block bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded mr-1 mb-1">
                            ${attr.attribute.name}: ${attr.value}
                        </span>`
                        ).join('') : '';

                        return `
                        <div class="variant-result border border-gray-200 rounded-lg p-3 hover:bg-gray-50 cursor-pointer transition-colors"
                             onclick="unknownItemsManager.selectVariant('${variant.id}', ${JSON.stringify(variant).replace(/"/g, '&quot;')})">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900">${variant.article.name}</h4>
                                    <p class="text-sm text-gray-600">${variant.article.category ? variant.article.category.name : 'Aucune catégorie'}</p>
                                    <div class="mt-2 space-y-1">
                                        <p class="text-xs text-gray-500">
                                            <span class="font-medium">Référence:</span> ${variant.reference || 'N/A'}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            <span class="font-medium">Code-barres:</span> ${variant.barcode || 'N/A'}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            <span class="font-medium">Prix:</span> ${parseFloat(variant.effective_sell_price || 0).toFixed(2)} €
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            <span class="font-medium">Stock:</span> ${variant.total_stock || 0}
                                        </p>
                                    </div>
                                    ${attributes ? `<div class="mt-2">${attributes}</div>` : ''}
                                </div>
                                <div class="ml-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Score: ${variant.relevance_score}
                                    </span>
                                </div>
                            </div>
                        </div>
                    `;
                    }).join('');

                    resultsDiv.classList.remove('hidden');
                }

                selectVariant(variantId, variantData) {
                    this.selectedVariant = variantData;
                    document.getElementById('selected-variant-id').value = variantId;

                    // Masquer les résultats de recherche
                    document.getElementById('search-results').classList.add('hidden');

                    // Afficher les détails du variant sélectionné
                    const detailsContainer = document.getElementById('selected-variant-details');
                    const attributes = variantData.attribute_values ? variantData.attribute_values.map(attr =>
                        `<span class="inline-block bg-green-100 text-green-700 text-xs px-2 py-1 rounded mr-1 mb-1">
                        ${attr.attribute.name}: ${attr.value}
                    </span>`
                    ).join('') : '';

                    detailsContainer.innerHTML = `
                    <div class="space-y-2">
                        <h4 class="font-medium text-gray-900">${variantData.article.name}</h4>
                        <p class="text-sm text-gray-600">${variantData.article.category ? variantData.article.category.name : 'Aucune catégorie'}</p>
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div><span class="font-medium">Référence:</span> ${variantData.reference || 'N/A'}</div>
                            <div><span class="font-medium">Code-barres:</span> ${variantData.barcode || 'N/A'}</div>
                            <div><span class="font-medium">Prix:</span> ${parseFloat(variantData.effective_sell_price || 0).toFixed(2)} €</div>
                            <div><span class="font-medium">Stock:</span> ${variantData.total_stock || 0}</div>
                        </div>
                        ${attributes ? `<div class="mt-2">${attributes}</div>` : ''}
                    </div>
                `;

                    document.getElementById('selected-variant-info').classList.remove('hidden');
                    document.getElementById('submit-btn').disabled = false;
                }

                submitRegularize() {
                    const itemId = document.getElementById('regularize-item-id').value;
                    const variantId = document.getElementById('selected-variant-id').value;
                    const notes = document.getElementById('regularize-notes').value;
                    const deductStock = document.getElementById('deduct-stock').checked;

                    if (!variantId) {
                        this.showModalMessage('Veuillez sélectionner un variant');
                        return;
                    }

                    // Masquer les messages précédents
                    this.hideModalMessage();

                    fetch(`{{ route('settings.unknown-items.regularize', ':id') }}`.replace(':id', itemId), {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            variant_id: variantId,
                            note_interne: notes,
                            deduct_stock: deductStock
                        }),
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                closeModal('regularize-modal');
                                this.loadTableData();
                                this.loadStats();
                                this.showNotification('Article régularisé avec succès', 'success');
                            } else {
                                this.showModalMessage(data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Erreur:', error);
                            this.showModalMessage('Erreur lors de la régularisation. Veuillez réessayer.');
                        });
                }

                marquerNonIdentifiable() {
                    if (!this.currentUnknownItemId) return;

                    const note = document.getElementById('regularize-notes').value;

                    // Validation : note obligatoire pour marquer comme non identifiable
                    if (!note.trim()) {
                        this.showModalMessage('Veuillez saisir une note pour expliquer pourquoi cet article est non identifiable');
                        return;
                    }

                    // Masquer les messages précédents
                    this.hideModalMessage();

                    fetch(`{{ route('settings.unknown-items.mark-non-identifiable', ':id') }}`.replace(':id', this.currentUnknownItemId), {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            note_interne: note
                        }),
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                closeModal('regularize-modal');
                                this.loadTableData();
                                this.loadStats();
                                this.showNotification('Article marqué comme non identifiable', 'success');
                            } else {
                                this.showModalMessage(data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Erreur:', error);
                            this.showModalMessage('Erreur lors du marquage. Veuillez réessayer.');
                        });
                }

                generateReport() {
                    const formData = new FormData(document.getElementById('rapport-form'));

                    // Convertir FormData en objet pour ajouter les filtres actuels
                    const params = new URLSearchParams();
                    params.append('date_start', formData.get('date_start'));
                    params.append('date_end', formData.get('date_end'));

                    // Ajouter les filtres actuels de la page
                    const currentFilters = new FormData(document.getElementById('filters-form'));
                    if (currentFilters.get('search')) params.append('search', currentFilters.get('search'));
                    if (currentFilters.get('status')) params.append('status', currentFilters.get('status'));

                    fetch('{{ route("settings.unknown-items.report") }}?' + params.toString(), {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        }
                    })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Erreur HTTP: ' + response.status);
                            }
                            return response.blob();
                        })
                        .then(blob => {
                            const url = window.URL.createObjectURL(blob);
                            const a = document.createElement('a');
                            a.href = url;
                            a.download = 'rapport-articles-inconnus-' + new Date().toISOString().split('T')[0] + '.pdf';
                            document.body.appendChild(a);
                            a.click();
                            window.URL.revokeObjectURL(url);
                            document.body.removeChild(a);
                            closeModal('rapport-modal');
                            this.showNotification('Rapport généré avec succès', 'success');
                        })
                        .catch(error => {
                            console.error('Erreur:', error);
                            alert('Erreur lors de la génération du rapport: ' + error.message);
                        });
                }

                showNotification(message, type = 'info') {
                    // Implémentation simple de notification
                    const notification = document.createElement('div');
                    notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg text-white z-50 ${
                        type === 'success' ? 'bg-green-500' : 'bg-blue-500'
                    }`;
                    notification.textContent = message;
                    document.body.appendChild(notification);

                    setTimeout(() => {
                        notification.remove();
                    }, 3000);
                }
            }

            // Fonctions globales pour compatibilité
            function genererRapport() {
                openModal('rapport-modal');
            }

            function ouvrirModal(unknownItemId) {
                unknownItemsManager.openRegularizeModal(unknownItemId);
            }


            // Initialisation
            let unknownItemsManager;
            document.addEventListener('DOMContentLoaded', function () {
                unknownItemsManager = new UnknownItemsManager();
            });
        </script>
    @endpush
</x-app-layout>
