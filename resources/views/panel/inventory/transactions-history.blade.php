<x-app-layout>
    <div class="lg:py-12">
        <div class="max-w-7xl mx-auto px-0 lg:px-8">
            <!-- Header -->
            <div
                class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg mb-6">
                <div class="flex items-center space-x-3 px-3 py-2 border-b border-gray-300 dark:border-gray-700">
                    <a href="{{ route('inventory.show', $article) }}"
                       class="text-gray-600 px-1.5 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 hover:scale-105 duration-500"><i
                            class="fas fa-arrow-left text-xl"></i></a>
                    <div>
                        <h1 class="text-gray-900 dark:text-gray-50 font-bold lg:text-2xl text-xl">Historique des
                            transactions - {{ $article->name }}</h1>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            <i class="fas fa-infinity text-blue-500 mr-1"></i>
                            Article avec stock illimité - Affichage des transactions de vente
                        </p>
                    </div>
                </div>
            </div>

            <!-- Section Filtres -->
            <div
                class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg mb-6">
                <div class="px-4 py-3 border-b border-gray-300 dark:border-gray-700">
                    <h2 class="font-semibold text-lg text-gray-900 dark:text-gray-100">Filtres</h2>
                </div>
                <div class="p-6">
                    <form id="filters-form" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400">Date début</label>
                            <input type="date" name="date_debut" class="form-input w-full"/>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400">Date fin</label>
                            <input type="date" name="date_fin" class="form-input w-full"/>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400">Origine</label>
                            <select name="origine" class="form-select w-full">
                                <option value="">Toutes</option>
                                <option value="caisse">Caisse</option>
                                <option value="eshop">E-Shop</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400">Membre</label>
                            <select name="membre" class="form-select w-full">
                                <option value="">Tous</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400">Quantité
                                min</label>
                            <input type="number" name="quantite_min" class="form-input w-full" step="0.001"/>
                        </div>
                    </form>

                    <div class="mt-4 flex justify-between items-center">
                        <div class="flex space-x-2">
                            <button onclick="applyFilters()"
                                    class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                                <i class="fas fa-filter mr-2"></i>Filtrer
                            </button>
                            <button onclick="clearFilters()"
                                    class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                                <i class="fas fa-times mr-2"></i>Effacer
                            </button>
                        </div>
                        <div class="flex items-center space-x-2">
                            <label class="text-sm text-gray-600 dark:text-gray-400">Tri:</label>
                            <select id="sort-select" onchange="applyFilters()" class="form-select text-sm">
                                <option value="created_at_desc">Plus récent</option>
                                <option value="created_at_asc">Plus ancien</option>
                                <option value="quantite_desc">Quantité décroissante</option>
                                <option value="quantite_asc">Quantité croissante</option>
                                <option value="prix_desc">Prix décroissant</option>
                                <option value="prix_asc">Prix croissant</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tableau des transactions -->
            <div id="transactions-table-container"
                 class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg">
                <!-- Le contenu sera chargé dynamiquement -->
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            loadTransactionsTable();
        });

        function loadTransactionsTable() {
            const container = document.getElementById('transactions-table-container');
            const form = document.getElementById('filters-form');
            const sortSelect = document.getElementById('sort-select');

            const formData = new FormData(form);
            formData.append('sort', sortSelect.value);

            const params = new URLSearchParams(formData);

            fetch(`/inventory/{{ $article->id }}/transactions/history/table?${params}`)
                .then(response => response.text())
                .then(html => {
                    container.innerHTML = html;
                })
                .catch(error => {
                    console.error('Erreur lors du chargement:', error);
                    container.innerHTML = '<div class="p-6 text-center text-gray-500">Erreur lors du chargement des données</div>';
                });
        }

        function applyFilters() {
            loadTransactionsTable();
        }

        function clearFilters() {
            const form = document.getElementById('filters-form');
            form.reset();
            document.getElementById('sort-select').value = 'created_at_desc';
            loadTransactionsTable();
        }
    </script>
</x-app-layout>
