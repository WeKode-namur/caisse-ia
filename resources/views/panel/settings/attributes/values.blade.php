<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <!-- En-tête -->
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-800">Valeurs de l'attribut
                                "{{ $attribute->name }}"</h2>
                            <p class="text-sm text-gray-600 mt-1">Gérez les valeurs possibles pour cet attribut</p>
                        </div>
                        <div class="flex space-x-3">
                            <button onclick="openModal('add-value')"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Ajouter une valeur
                            </button>
                            <a href="{{ route('settings.attributes.index') }}"
                               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Retour
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Messages de succès/erreur -->
                @if(session('success'))
                    <div class="px-6 py-3 bg-green-100 border-l-4 border-green-500">
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
                    <div class="px-6 py-3 bg-red-100 border-l-4 border-red-500">
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

                <!-- Informations sur l'attribut -->
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <span class="text-sm font-medium text-gray-500">Type:</span>
                            <span class="ml-2 inline-flex px-2 py-1 text-xs font-semibold rounded-full
                            @switch($attribute->type)
                                @case('text')
                                    bg-blue-100 text-blue-800
                                    @break
                                @case('number')
                                    bg-green-100 text-green-800
                                    @break
                                @case('select')
                                    bg-purple-100 text-purple-800
                                    @break
                                @case('boolean')
                                    bg-yellow-100 text-yellow-800
                                    @break
                                @case('date')
                                    bg-red-100 text-red-800
                                    @break
                                @default
                                    bg-gray-100 text-gray-800
                            @endswitch">
                            {{ ucfirst($attribute->type) }}
                        </span>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">Valeurs:</span>
                            <span class="ml-2 text-sm text-gray-900">{{ $values->count() }}</span>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">Options:</span>
                            <div class="ml-2 inline-flex space-x-2">
                                @if($attribute->is_required)
                                    <span
                                        class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    Requis
                                </span>
                                @endif
                                @if($attribute->is_searchable)
                                    <span
                                        class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    Recherchable
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tableau des valeurs -->
                <div id="values-table">
                    {{-- Le tableau sera chargé ici en AJAX --}}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal pour ajouter une valeur -->
    <x-modal name="add-value" title="Ajouter une valeur" size="md">
        <form action="{{ route('settings.attributes.values.store', $attribute) }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="value" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Valeur
                        *</label>
                    <input type="text"
                           name="value"
                           id="value"
                           required
                           class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                           placeholder="Entrez la valeur...">
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                    <textarea name="description"
                              id="description"
                              rows="2"
                              class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                              placeholder="Description optionnelle..."></textarea>
                </div>
            </div>

            <x-slot name="actions">
                <button type="button"
                        onclick="closeModal('add-value')"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md hover:bg-gray-50 dark:hover:bg-gray-500 transition-colors">
                    Annuler
                </button>
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 transition-colors">
                    Ajouter
                </button>
            </x-slot>
        </form>
    </x-modal>

    <!-- Modal pour modifier une valeur -->
    <x-modal name="edit-value" title="Modifier la valeur" size="lg">
        <form id="editValueForm" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Formulaire -->
                <div class="lg:col-span-2 space-y-4">
                    <div>
                        <label for="edit_value" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Valeur
                            *</label>
                        <input type="text"
                               name="value"
                               id="edit_value"
                               required
                               class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                               placeholder="Entrez la valeur...">
                    </div>
                    <div>
                        <label for="edit_description"
                               class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                        <textarea name="description"
                                  id="edit_description"
                                  rows="3"
                                  class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                  placeholder="Description optionnelle..."></textarea>
                    </div>
                </div>

                <!-- Box d'informations -->
                <div class="lg:col-span-1">
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                        <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-3 flex items-center">
                            <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                            Informations
                        </h4>

                        <div class="space-y-3">
                            <div>
                                <span
                                    class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Position</span>
                                <p class="text-sm text-gray-900 dark:text-gray-100" id="value-position">-</p>
                            </div>

                            <div>
                                <span
                                    class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Variants liés</span>
                                <p class="text-sm text-gray-900 dark:text-gray-100" id="variants-count">-</p>
                            </div>

                            <div>
                                <span
                                    class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Articles liés</span>
                                <p class="text-sm text-gray-900 dark:text-gray-100" id="articles-count">-</p>
                            </div>

                            <div>
                                <span
                                    class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Créé le</span>
                                <p class="text-sm text-gray-900 dark:text-gray-100" id="created-at">-</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <x-slot name="actions">
                <button type="button"
                        onclick="closeModal('edit-value')"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md hover:bg-gray-50 dark:hover:bg-gray-500 transition-colors">
                    Annuler
                </button>
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 transition-colors">
                    Mettre à jour
                </button>
            </x-slot>
        </form>
    </x-modal>

    <script>
        function editValue(valueId, value, description, order) {
            // Remplir le formulaire
            document.getElementById('edit_value').value = value;
            document.getElementById('edit_description').value = description || '';

            // Mettre à jour l'action du formulaire
            const form = document.getElementById('editValueForm');
            form.action = '{{ route("settings.attributes.values.update", [$attribute, ":value"]) }}'.replace(':value', valueId);

            // Récupérer les informations de la valeur via AJAX
            fetch(`{{ route('settings.attributes.values.show', [$attribute, ':value']) }}`.replace(':value', valueId))
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const valueData = data.value;

                        // Mettre à jour les informations dans la box
                        document.getElementById('value-position').textContent = valueData.order || 'Non définie';
                        document.getElementById('variants-count').textContent = valueData.variants_count || '0';
                        document.getElementById('articles-count').textContent = valueData.articles_count || '0';
                        document.getElementById('created-at').textContent = valueData.created_at_formatted || '-';
                    }
                })
                .catch(error => {
                    console.error('Erreur lors de la récupération des informations:', error);
                    // Valeurs par défaut en cas d'erreur
                    document.getElementById('value-position').textContent = order || 'Non définie';
                    document.getElementById('variants-count').textContent = '-';
                    document.getElementById('articles-count').textContent = '-';
                    document.getElementById('created-at').textContent = '-';
                });

            // Ouvrir le modal
            openModal('edit-value');
        }
    </script>

    <!-- Sortable.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        function loadValuesTable() {
            fetch("{{ route('settings.attributes.values.table', $attribute) }}")
                .then(res => res.text())
                .then(html => {
                    document.getElementById('values-table').innerHTML = html;
                    // Réactiver le drag & drop après chargement
                    enableSortable();
                });
        }

        document.addEventListener('DOMContentLoaded', function () {
            loadValuesTable();
        });

        function enableSortable() {
            const el = document.getElementById('sortable-values');
            if (!el) return;
            new Sortable(el, {
                handle: '.fa-grip-vertical',
                animation: 150,
                onEnd: function () {
                    const order = Array.from(el.querySelectorAll('tr')).map(tr => tr.dataset.id);
                    fetch("{{ route('settings.attributes.values.updateOrder', $attribute) }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({order})
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                showNotif('Nouvel ordre enregistré !');
                                loadValuesTable();
                            }
                        });
                }
            });
        }

        function showNotif(msg, isError = false) {
            let notif = document.createElement('div');
            notif.className = `fixed top-4 right-4 bg-${isError ? 'red' : 'green'}-500 text-white px-4 py-2 rounded shadow z-50`;
            notif.innerText = msg;
            document.body.appendChild(notif);
            setTimeout(() => notif.remove(), 2000);
        }
    </script>
</x-app-layout>
