{{-- resources/views/panel/inventory/drafts.blade.php --}}
<x-app-layout>
    <div class="lg:py-12">
        <div class="max-w-7xl mx-auto px-0 lg:px-8">
            <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg mb-6">
                <div class="flex items-center justify-between px-3 py-2 border-b border-gray-300 dark:border-gray-700">
                    <h1 class="text-gray-900 dark:text-gray-50 font-bold lg:text-2xl text-xl">Création d'articles</h1>
                    <a href="{{ route('inventory.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        <span>Retour à l'inventaire</span>
                    </a>
                </div>

                <div class="p-6">
                    <!-- En-tête avec bouton nouveau -->
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <p class="text-gray-600 dark:text-gray-400">Commencez un nouvel article ou continuez un brouillon existant</p>
                        </div>
                        <a href="{{ route('inventory.create.step.one') }}"
                           class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Nouvel article
                        </a>
                    </div>

                    <!-- Barre de recherche -->
                    <div class="mb-6">
                        <div class="flex items-center space-x-4">
                            <div class="flex-1">
                                <input type="text"
                                       id="drafts-search"
                                       placeholder="Rechercher dans les brouillons..."
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            </div>
                            <button onclick="draftsManager.refresh()" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-md hover:bg-gray-200 dark:hover:bg-gray-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Container pour les brouillons -->
                    <div id="drafts-container">
                        <x-loading-spinner message="Chargement des brouillons..." />
                    </div>

                    <!-- Instructions -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 mt-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">Processus de création</h3>
                                <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                                    <p>La création d'un article se fait en 2 étapes simples :</p>
                                    <ul class="list-disc list-inside mt-2 space-y-1">
                                        <li><strong>Étape 1</strong> : Informations générales (nom, catégorie, prix)</li>
                                        <li><strong>Étape 2</strong> : Configuration du stock et des variants</li>
                                    </ul>
                                    <p class="mt-2">Vos brouillons sont automatiquement sauvegardés et vous pouvez reprendre la création à tout moment.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // LoadingUtils pour gérer les états de chargement
            const LoadingUtils = {
                getLoadingHtml(message = 'Chargement...') {
                    return `
                        <div class="flex items-center justify-center py-8">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
                            <span class="ml-3 text-gray-600 dark:text-gray-400">${message}</span>
                        </div>
                    `;
                }
            };

            // Gestionnaire principal des brouillons
            const draftsManager = {
                container: null,
                searchInput: null,
                searchTimeout: null,

                async init() {
                    this.container = document.getElementById('drafts-container');
                    this.searchInput = document.getElementById('drafts-search');

                    // Recherche avec debounce
                    this.searchInput.addEventListener('input', (e) => {
                        clearTimeout(this.searchTimeout);
                        this.searchTimeout = setTimeout(() => {
                            this.loadDrafts(e.target.value);
                        }, 300);
                    });

                    // Chargement initial
                    await this.loadDrafts();
                },

                async loadDrafts(search = '') {
                    try {
                        // Afficher le loading
                        this.container.innerHTML = await LoadingUtils.getLoadingHtml('Chargement des brouillons...');

                        const url = new URL('{{ route("inventory.drafts.table") }}');
                        if (search) url.searchParams.set('search', search);

                        const response = await fetch(url);
                        const html = await response.text();

                        this.container.innerHTML = html;
                    } catch (error) {
                        console.error('Erreur lors du chargement des brouillons:', error);
                        this.showError();
                    }
                },

                refresh() {
                    this.loadDrafts(this.searchInput.value);
                },

                showError() {
                    this.container.innerHTML = `
                        <div class="text-center py-12 bg-white dark:bg-gray-800 rounded-lg shadow">
                            <div class="text-red-500 mb-2">
                                <i class="fas fa-exclamation-triangle text-4xl"></i>
                            </div>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">Erreur lors du chargement des brouillons</p>
                            <button onclick="draftsManager.refresh()" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                                <i class="fas fa-redo mr-2"></i>
                                Réessayer
                            </button>
                        </div>
                    `;
                }
            };

            // Fonctions globales pour les actions
            async function deleteDraft(draftId) {
                if (!confirm('Êtes-vous sûr de vouloir supprimer ce brouillon ?')) return;

                try {
                    const response = await fetch(`{{ route('inventory.drafts.destroy', 'DRAFT_ID') }}`.replace('DRAFT_ID', draftId), {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        }
                    });

                    if (response.ok) {
                        draftsManager.refresh();
                        showToast('Brouillon dupliqué avec succès', 'success');
                    } else {
                        showToast('Erreur lors de la duplication', 'error');
                    }
                } catch (error) {
                    console.error('Erreur lors de la duplication:', error);
                    showToast('Erreur lors de la duplication', 'error');
                }
            }

            async function loadDraftsPage(url) {
                try {
                    draftsManager.container.innerHTML = await LoadingUtils.getLoadingHtml('Chargement...');

                    const urlObj = new URL(url);
                    const search = draftsManager.searchInput.value;
                    if (search) urlObj.searchParams.set('search', search);

                    const response = await fetch(urlObj);
                    const html = await response.text();

                    draftsManager.container.innerHTML = html;
                } catch (error) {
                    console.error('Erreur pagination:', error);
                    draftsManager.showError();
                }
            }

            function clearSearch() {
                draftsManager.searchInput.value = '';
                draftsManager.loadDrafts();
            }

            function showToast(message, type = 'info') {
                const toast = document.createElement('div');
                toast.className = `fixed top-4 right-4 px-4 py-2 rounded-md text-white z-50 transition-all duration-300 ${
                    type === 'success' ? 'bg-green-500' :
                        type === 'error' ? 'bg-red-500' : 'bg-blue-500'
                }`;
                toast.innerHTML = `
                    <div class="flex items-center">
                        <i class="fas ${type === 'success' ? 'fa-check' : type === 'error' ? 'fa-times' : 'fa-info'} mr-2"></i>
                        ${message}
                    </div>
                `;
                document.body.appendChild(toast);

                setTimeout(() => {
                    toast.classList.add('opacity-0', 'translate-x-full');
                    setTimeout(() => toast.remove(), 300);
                }, 3000);
            }

            // Initialisation au chargement de la page
            document.addEventListener('DOMContentLoaded', () => {
                draftsManager.init();
            });
        </script>
    @endpush
</x-app-layout>
