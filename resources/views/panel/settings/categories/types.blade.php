<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <!-- En-tête -->
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="flex items-center space-x-3">
                                <a href="{{ route('settings.categories.index') }}"
                                   class="text-indigo-600 hover:text-indigo-900 transition-colors">
                                    <i class="fas fa-arrow-left"></i>
                                </a>
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900">Types de la catégorie
                                        : {{ $category->name }}</h3>
                                    <p class="mt-1 text-sm text-gray-500">Gérez les sous-catégories de cette
                                        catégorie</p>
                                </div>
                            </div>
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
                                NOUVEAU TYPE
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

                    <!-- Tableau des types -->
                    <div id="typesContainer">
                        <!-- Le tableau sera chargé en AJAX -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal d'ajout de type -->
    <x-modal name="add-type" size="2xl" :footer="false" icon="plus" title="Nouveau type" iconColor="blue">
        {{--        <x-slot name="title">--}}
        {{--            <i class="fas fa-plus mr-2 text-indigo-600"></i>--}}
        {{--            Nouveau type--}}
        {{--        </x-slot>--}}

        <form id="addTypeForm" class="space-y-4">
            @csrf
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nom du type *</label>
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


            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="closeModal('add-type')"
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

    <!-- Modal d'édition de type -->
    <x-modal name="edit-type" size="2xl" :footer="false" icon="edit" title="Modifier le type" iconColor="amber">
        <form id="editTypeForm" class="space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit_type_id" name="type_id">

            <div>
                <label for="edit_name" class="block text-sm font-medium text-gray-700 mb-1">Nom du type *</label>
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


            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                <div class="flex">
                    <i class="fas fa-exclamation-triangle text-yellow-600 mt-0.5 mr-2"></i>
                    <p class="text-sm text-yellow-800">
                        <strong>Attention :</strong> La modification d'un type peut affecter l'organisation de vos
                        produits.
                    </p>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="closeModal('edit-type')"
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


    <!-- Modal de confirmation de suppression -->
    <x-modal name="delete-confirmation" size="md" :footer="false">
        <x-slot name="title">
            <i class="fas fa-exclamation-triangle mr-2 text-red-600"></i>
            Confirmer la suppression
        </x-slot>

        <div class="space-y-4">
            <p class="text-gray-700">
                Êtes-vous sûr de vouloir supprimer ce type ? Cette action est irréversible.
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
            let currentTypeId = null;

            // Initialisation
            document.addEventListener('DOMContentLoaded', function () {
                // Charger les actifs par défaut
                document.getElementById('statusFilter').value = 'active';
                loadTypes();
                loadStats();

                // Gestionnaire de filtre
                document.getElementById('statusFilter').addEventListener('change', function () {
                    loadTypes();
                });
            });

            // Charger les types
            function loadTypes() {
                const status = document.getElementById('statusFilter').value;
                const url = status === 'all' ? '{{ route("settings.categories.types.table", ["category" => $category->id]) }}' : `{{ route("settings.categories.types.table", ["category" => $category->id]) }}?status=${status}`;

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
                            document.getElementById('typesContainer').innerHTML = html;
                        }
                    })
                    .catch(error => {
                        console.error('Erreur lors du chargement des types:', error);
                        showNotification('Erreur lors du chargement des types', 'error');
                    });
            }

            // Charger les stats
            function loadStats() {
                fetch('{{ route("settings.categories.types.stats", ["category" => $category->id]) }}', {
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
                document.getElementById('addTypeForm').reset();
                window.openModal('add-type');
            }

            // Ouvrir le modal d'édition
            function editType(typeId, name, description) {
                clearFormErrors();
                currentTypeId = typeId;

                document.getElementById('edit_type_id').value = typeId;
                document.getElementById('edit_name').value = name;
                document.getElementById('edit_description').value = description || '';

                window.openModal('edit-type');
            }


            // Basculer le statut d'un type
            function toggleType(typeId) {
                fetch(`{{ route("settings.categories.types.toggle", ["category" => $category->id, "type" => ":id"]) }}`.replace(':id', typeId), {
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
                            loadTypes();
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

            // Supprimer un type
            function deleteType(typeId) {
                currentTypeId = typeId;
                window.openModal('delete-confirmation');
            }

            function confirmDelete() {
                fetch(`{{ route("settings.categories.types.destroy", ["category" => $category->id, "type" => ":id"]) }}`.replace(':id', currentTypeId), {
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
                            loadTypes();
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
            document.getElementById('addTypeForm').addEventListener('submit', function (e) {
                e.preventDefault();
                submitAddForm();
            });

            function submitAddForm() {
                const formData = new FormData(document.getElementById('addTypeForm'));

                fetch('{{ route("settings.categories.types.store", ["category" => $category->id]) }}', {
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
                            window.closeModal('add-type');
                            loadTypes();
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
            document.getElementById('editTypeForm').addEventListener('submit', function (e) {
                e.preventDefault();
                submitEditForm();
            });

            function submitEditForm() {
                const formData = new FormData(document.getElementById('editTypeForm'));

                fetch(`{{ route("settings.categories.types.update", ["category" => $category->id, "type" => ":id"]) }}`.replace(':id', currentTypeId), {
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
                            window.closeModal('edit-type');
                            loadTypes();
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
