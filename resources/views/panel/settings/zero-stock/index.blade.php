<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- En-tête -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                            <i class="fas fa-exclamation-triangle mr-3 text-red-600"></i>
                            Articles en stock zéro
                        </h1>
                        <p class="mt-2 text-gray-600 dark:text-gray-400">
                            Gérez les articles qui n'ont plus de stock disponible
                        </p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('settings.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-200 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Retour aux paramètres
                        </a>
                    </div>
                </div>
            </div>

            <!-- Actions en masse -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                    Actions en masse
                </h3>

                <form method="POST" action="{{ route('settings.zero-stock.bulk-update') }}" id="bulk-form">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Action
                            </label>
                            <select name="action" id="bulk-action"
                                    class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Sélectionner une action</option>
                                <option value="delete">Supprimer les articles</option>
                                <option value="archive">Archiver les articles</option>
                                <option value="update_stock">Mettre à jour le stock</option>
                            </select>
                        </div>

                        <div id="stock-quantity-field" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Nouvelle quantité
                            </label>
                            <input type="number" name="new_stock" min="0" step="1"
                                   class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Quantité">
                        </div>

                        <div class="flex items-end">
                            <button type="submit"
                                    class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md font-medium focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                                <i class="fas fa-play mr-2"></i>
                                Appliquer
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Liste des articles -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                            Articles en stock zéro ({{ $articles->total() }})
                        </h3>
                        <div class="flex items-center space-x-2">
                            <input type="checkbox" id="select-all"
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <label for="select-all" class="text-sm text-gray-700 dark:text-gray-300">Tout
                                sélectionner</label>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                <input type="checkbox" id="select-all-header"
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Article
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Catégorie
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Variants
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Prix
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($articles as $article)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" name="article_ids[]" value="{{ $article->id }}"
                                           class="article-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div
                                                class="h-10 w-10 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                                <i class="fas fa-box text-gray-600 dark:text-gray-400"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $article->name }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $article->reference ?? 'Aucune référence' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            {{ $article->category->name ?? 'Aucune catégorie' }}
                                        </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $article->variants->count() }} variant(s)
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    @if($article->sell_price)
                                        {{ number_format($article->sell_price, 2) }} €
                                    @else
                                        <span class="text-gray-500">Non défini</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('inventory.show', $article->id) }}"
                                           class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('inventory.edit', $article->id) }}"
                                           class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-check-circle text-3xl text-green-500 mb-2"></i>
                                        <p>Aucun article en stock zéro trouvé</p>
                                        <p class="text-sm">Tous vos articles ont du stock disponible</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($articles->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $articles->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        // Gestion de la sélection multiple
        document.getElementById('select-all').addEventListener('change', function () {
            const checkboxes = document.querySelectorAll('.article-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        document.getElementById('select-all-header').addEventListener('change', function () {
            const checkboxes = document.querySelectorAll('.article-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        // Gestion de l'affichage du champ quantité
        document.getElementById('bulk-action').addEventListener('change', function () {
            const stockField = document.getElementById('stock-quantity-field');
            if (this.value === 'update_stock') {
                stockField.classList.remove('hidden');
            } else {
                stockField.classList.add('hidden');
            }
        });

        // Validation du formulaire
        document.getElementById('bulk-form').addEventListener('submit', function (e) {
            const action = document.getElementById('bulk-action').value;
            const checkboxes = document.querySelectorAll('.article-checkbox:checked');

            if (!action) {
                e.preventDefault();
                alert('Veuillez sélectionner une action');
                return;
            }

            if (checkboxes.length === 0) {
                e.preventDefault();
                alert('Veuillez sélectionner au moins un article');
                return;
            }

            if (action === 'update_stock') {
                const newStock = document.querySelector('input[name="new_stock"]').value;
                if (!newStock || newStock < 0) {
                    e.preventDefault();
                    alert('Veuillez saisir une quantité valide');
                    return;
                }
            }

            if (action === 'delete') {
                if (!confirm('Êtes-vous sûr de vouloir supprimer définitivement ces articles ? Cette action est irréversible.')) {
                    e.preventDefault();

                }
            }
        });
    </script>
</x-app-layout>
