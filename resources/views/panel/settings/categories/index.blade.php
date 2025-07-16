<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <!-- En-tête -->
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Gestion des catégories</h3>
                            <p class="mt-1 text-sm text-gray-500">Organisez vos produits avec des catégories et types
                                personnalisés</p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <!-- Filtres -->
                            <select id="statusFilter"
                                    class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="all">Tous les statuts</option>
                                <option value="active">Actifs uniquement</option>
                                <option value="inactive">Inactifs uniquement</option>
                            </select>

                            <!-- Bouton d'ajout -->
                            <button onclick="openAddModal()"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                NOUVELLE CATÉGORIE
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Contenu principal -->
                <div class="p-6">
                    <!-- Stats -->
                    <div id="statsContainer" class="mb-6">
                        <!-- Les stats seront chargées en AJAX -->
                    </div>

                    <!-- Tableau des catégories -->
                    <div id="categoriesContainer">
                        <!-- Le tableau sera chargé en AJAX -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal d'ajout de catégorie -->
    <x-modal name="add-category" size="2xl" :footer="false" icon="plus" title="Nouvelle catégorie" iconColor="blue">

        <form id="addCategoryForm" class="space-y-4">
            @csrf
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nom de la catégorie *</label>
                <input type="text" id="name" name="name" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <div id="name-error" class="text-red-500 text-sm mt-1 hidden"></div>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea id="description" name="description" rows="3"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                <div id="description-error" class="text-red-500 text-sm mt-1 hidden"></div>
            </div>

            <div>
                <label for="icon" class="block text-sm font-medium text-gray-700 mb-1">Icône</label>
                <div class="flex space-x-2">
                    <input type="text" id="icon" name="icon" readonly
                           class="flex-1 border border-gray-300 rounded-lg px-3 py-2 bg-gray-50">
                    <button type="button" onclick="openIconSelector()"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                        <i class="fas fa-icons"></i>
                    </button>
                </div>
                <div id="icon-error" class="text-red-500 text-sm mt-1 hidden"></div>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="closeModal('add-category')"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                    Annuler
                </button>
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Créer
                </button>
            </div>
        </form>
    </x-modal>

    <!-- Modal d'édition de catégorie -->
    <x-modal name="edit-category" size="2xl" :footer="false" icon="edit" title="Modifier la catégorie"
             iconColor="amber">

        <form id="editCategoryForm" class="space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit_category_id" name="category_id">

            <div>
                <label for="edit_name" class="block text-sm font-medium text-gray-700 mb-1">Nom de la catégorie
                    *</label>
                <input type="text" id="edit_name" name="name" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <div id="edit_name-error" class="text-red-500 text-sm mt-1 hidden"></div>
            </div>

            <div>
                <label for="edit_description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea id="edit_description" name="description" rows="3"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                <div id="edit_description-error" class="text-red-500 text-sm mt-1 hidden"></div>
            </div>

            <div>
                <label for="edit_icon" class="block text-sm font-medium text-gray-700 mb-1">Icône</label>
                <div class="flex space-x-2">
                    <input type="text" id="edit_icon" name="icon" readonly
                           class="flex-1 border border-gray-300 rounded-lg px-3 py-2 bg-gray-50">
                    <button type="button" onclick="openIconSelector('edit')"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                        <i class="fas fa-icons"></i>
                    </button>
                </div>
                <div id="edit_icon-error" class="text-red-500 text-sm mt-1 hidden"></div>
            </div>

            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                <div class="flex">
                    <i class="fas fa-exclamation-triangle text-yellow-600 mt-0.5 mr-2"></i>
                    <p class="text-sm text-yellow-800">
                        <strong>Attention :</strong> La modification d'une catégorie peut affecter l'organisation de vos
                        produits.
                    </p>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="closeModal('edit-category')"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                    Annuler
                </button>
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-amber-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-600 focus:bg-amber-600 active:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Modifier
                </button>
            </div>
        </form>
    </x-modal>

    <!-- Modal de sélection d'icône -->
    <x-modal name="icon-selector" size="4xl" :footer="false">
        <x-slot name="title">
            <i class="fas fa-icons mr-2 text-indigo-600"></i>
            Sélectionner une icône
        </x-slot>

        <div class="space-y-4">
            <div class="flex space-x-2">
                <input type="text" id="iconSearch" placeholder="Rechercher une icône..."
                       class="flex-1 border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div id="iconsContainer" class="max-h-96 overflow-y-auto">
                <!-- Les icônes seront chargées ici -->
            </div>
        </div>
    </x-modal>

    <!-- Modal de confirmation de suppression -->
    <x-modal name="delete-confirmation" size="md" :footer="false">
        <x-slot name="title">
            <i class="fas fa-exclamation-triangle mr-2 text-red-600"></i>
            Confirmer la suppression
        </x-slot>

        <div class="space-y-4">
            <p class="text-gray-700">
                Êtes-vous sûr de vouloir supprimer cette catégorie ? Cette action est irréversible.
            </p>

            <div class="flex justify-end space-x-3">
                <button onclick="closeModal('delete-confirmation')"
                        class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors">
                    Annuler
                </button>
                <button onclick="confirmDelete()"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    Supprimer
                </button>
            </div>
        </div>
    </x-modal>

    @push('scripts')
        <script>
            let currentCategoryId = null;
            let currentIconTarget = null;

            // Initialisation
            document.addEventListener('DOMContentLoaded', function () {
                // Charger les actifs par défaut
                document.getElementById('statusFilter').value = 'active';
                loadCategories();
                loadStats();

                // Gestionnaire de filtre
                document.getElementById('statusFilter').addEventListener('change', function () {
                    loadCategories();
                });
            });

            // Charger les catégories
            function loadCategories() {
                const status = document.getElementById('statusFilter').value;
                const url = status === 'all' ? '{{ route("settings.categories.table") }}' : `{{ route("settings.categories.table") }}?status=${status}`;

                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => {
                        if (response.status === 401 || response.status === 403) {
                            window.location.href = '{{ route("settings.index") }}';
                            return;
                        }
                        return response.text();
                    })
                    .then(html => {
                        if (html) {
                            document.getElementById('categoriesContainer').innerHTML = html;
                        }
                    })
                    .catch(error => {
                        console.error('Erreur lors du chargement des catégories:', error);
                        showNotification('Erreur lors du chargement des catégories', 'error');
                    });
            }

            // Charger les stats
            function loadStats() {
                fetch('{{ route("settings.categories.stats") }}', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => {
                        if (response.status === 401 || response.status === 403) {
                            window.location.href = '{{ route("settings.index") }}';
                            return;
                        }
                        return response.text();
                    })
                    .then(html => {
                        if (html) {
                            document.getElementById('statsContainer').innerHTML = html;
                        }
                    })
                    .catch(error => {
                        console.error('Erreur lors du chargement des stats:', error);
                    });
            }

            // Ouvrir le modal d'ajout
            function openAddModal() {
                clearFormErrors();
                document.getElementById('addCategoryForm').reset();
                window.openModal('add-category');
            }

            // Ouvrir le modal d'édition
            function editCategory(categoryId, name, description, icon) {
                clearFormErrors();
                currentCategoryId = categoryId;

                document.getElementById('edit_category_id').value = categoryId;
                document.getElementById('edit_name').value = name;
                document.getElementById('edit_description').value = description || '';
                document.getElementById('edit_icon').value = icon || '';

                window.openModal('edit-category');
            }

            // Ouvrir le sélecteur d'icônes
            function openIconSelector(target = 'add') {
                currentIconTarget = target;
                loadIcons();
                window.openModal('icon-selector');
            }

            // Charger les icônes
            function loadIcons() {
                fetch('{{ route("settings.categories.icons") }}', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => {
                        if (response.status === 401 || response.status === 403) {
                            window.location.href = '{{ route("settings.index") }}';
                            return;
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            renderIcons(data.icons);
                        }
                    })
                    .catch(error => {
                        console.error('Erreur lors du chargement des icônes:', error);
                    });
            }

            // Rendre les icônes
            function renderIcons(groupedIcons) {
                const container = document.getElementById('iconsContainer');
                let html = '';

                for (const [category, icons] of Object.entries(groupedIcons)) {
                    html += `
                    <div class="mb-6">
                        <h4 class="font-medium text-gray-900 mb-3">${category}</h4>
                        <div class="grid grid-cols-6 gap-2">
                `;

                    icons.forEach(icon => {
                        html += `
                        <button onclick="selectIcon('${icon.class}', '${icon.name}')"
                                class="p-3 border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-indigo-300 transition-colors text-center">
                            <i class="${icon.class} text-lg text-gray-600"></i>
                            <div class="text-xs text-gray-500 mt-1 truncate">${icon.name}</div>
                        </button>
                    `;
                    });

                    html += `
                        </div>
                    </div>
                `;
                }

                container.innerHTML = html;
            }

            // Sélectionner une icône
            function selectIcon(iconClass, iconName) {
                const targetInput = currentIconTarget === 'edit' ? 'edit_icon' : 'icon';
                document.getElementById(targetInput).value = iconClass;
                window.closeModal('icon-selector');
            }

            // Basculer le statut d'une catégorie
            function toggleCategory(categoryId) {
                fetch(`{{ route("settings.categories.toggle", ["category" => ":id"]) }}`.replace(':id', categoryId), {
                    method: 'PATCH',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                    .then(response => {
                        if (response.status === 401 || response.status === 403) {
                            window.location.href = '{{ route("settings.index") }}';
                            return;
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            showNotification(data.message, 'success');
                            loadCategories();
                            loadStats();
                        } else {
                            showNotification(data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Erreur lors du basculement:', error);
                        showNotification('Erreur lors du basculement', 'error');
                    });
            }

            // Gérer les types d'une catégorie
            function manageTypes(categoryId, categoryName) {
                // Rediriger vers la page de gestion des types
                window.location.href = `{{ route("settings.categories.types", ["category" => ":id"]) }}`.replace(':id', categoryId);
            }

            // Supprimer une catégorie
            function deleteCategory(categoryId) {
                currentCategoryId = categoryId;
                window.openModal('delete-confirmation');
            }

            function confirmDelete() {
                fetch(`{{ route("settings.categories.destroy", ["category" => ":id"]) }}`.replace(':id', currentCategoryId), {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                    .then(response => {
                        if (response.status === 401 || response.status === 403) {
                            window.location.href = '{{ route("settings.index") }}';
                            return;
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            showNotification(data.message, 'success');
                            window.closeModal('delete-confirmation');
                            loadCategories();
                            loadStats();
                        } else {
                            showNotification(data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Erreur lors de la suppression:', error);
                        showNotification('Erreur lors de la suppression', 'error');
                    });
            }

            // Soumettre le formulaire d'ajout
            document.getElementById('addCategoryForm').addEventListener('submit', function (e) {
                e.preventDefault();
                submitAddForm();
            });

            function submitAddForm() {
                const formData = new FormData(document.getElementById('addCategoryForm'));

                fetch('{{ route("settings.categories.store") }}', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                })
                    .then(response => {
                        if (response.status === 401 || response.status === 403) {
                            window.location.href = '{{ route("settings.index") }}';
                            return;
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            showNotification(data.message, 'success');
                            window.closeModal('add-category');
                            loadCategories();
                            loadStats();
                        } else {
                            displayFormErrors(data.errors);
                        }
                    })
                    .catch(error => {
                        console.error('Erreur lors de l\'ajout:', error);
                        showNotification('Erreur lors de l\'ajout', 'error');
                    });
            }

            // Soumettre le formulaire d'édition
            document.getElementById('editCategoryForm').addEventListener('submit', function (e) {
                e.preventDefault();
                submitEditForm();
            });

            function submitEditForm() {
                const formData = new FormData(document.getElementById('editCategoryForm'));

                fetch(`{{ route("settings.categories.update", ["category" => ":id"]) }}`.replace(':id', currentCategoryId), {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                })
                    .then(response => {
                        if (response.status === 401 || response.status === 403) {
                            window.location.href = '{{ route("settings.index") }}';
                            return;
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            showNotification(data.message, 'success');
                            window.closeModal('edit-category');
                            loadCategories();
                        } else {
                            displayFormErrors(data.errors, 'edit_');
                        }
                    })
                    .catch(error => {
                        console.error('Erreur lors de la modification:', error);
                        showNotification('Erreur lors de la modification', 'error');
                    });
            }

            // Afficher les erreurs de formulaire
            function displayFormErrors(errors, prefix = '') {
                clearFormErrors(prefix);

                for (const [field, messages] of Object.entries(errors)) {
                    const errorElement = document.getElementById(prefix + field + '-error');
                    if (errorElement) {
                        errorElement.textContent = messages[0];
                        errorElement.classList.remove('hidden');
                    }
                }
            }

            // Nettoyer les erreurs de formulaire
            function clearFormErrors(prefix = '') {
                const errorElements = document.querySelectorAll('[id$="-error"]');
                errorElements.forEach(element => {
                    if (element.id.startsWith(prefix)) {
                        element.classList.add('hidden');
                        element.textContent = '';
                    }
                });
            }

            // Fonction de notification
            function showNotification(message, type = 'success') {
                // Créer une notification simple
                const notification = document.createElement('div');
                notification.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full ${
                    type === 'success' ? 'bg-green-500 text-white' :
                        type === 'error' ? 'bg-red-500 text-white' :
                            'bg-blue-500 text-white'
                }`;
                notification.textContent = message;

                document.body.appendChild(notification);

                // Animation d'entrée
                setTimeout(() => {
                    notification.classList.remove('translate-x-full');
                }, 100);

                // Animation de sortie
                setTimeout(() => {
                    notification.classList.add('translate-x-full');
                    setTimeout(() => {
                        if (notification.parentNode) {
                            notification.parentNode.removeChild(notification);
                        }
                    }, 300);
                }, 3000);
            }
        </script>
    @endpush
</x-app-layout>
