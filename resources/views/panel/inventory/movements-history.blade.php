<x-app-layout>
    <div class="lg:py-12">
        <div class="max-w-7xl mx-auto py-8 px-4">
            <div class="mb-6 flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Historique des mouvements - {{ $article->name }}</h1>
                <a href="{{ route('inventory.show', $article) }}" class="text-blue-600 hover:underline">&larr; Retour à l'article</a>
            </div>
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
                <form id="filters-form" class="grid grid-cols-1 md:grid-cols-6 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400">Date début</label>
                        <input type="date" name="date_debut" class="form-input w-full" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400">Date fin</label>
                        <input type="date" name="date_fin" class="form-input w-full" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400">Type</label>
                        <select name="type" class="form-select w-full">
                            <option value="">Tous</option>
                            <option value="entree">Entrée</option>
                            <option value="sortie">Sortie</option>
                        </select>
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
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400">Trier par</label>
                        <select name="sort" class="form-select w-full">
                            <option value="created_at_desc">Date ↓</option>
                            <option value="created_at_asc">Date ↑</option>
                            <option value="quantite_desc">Quantité ↓</option>
                            <option value="quantite_asc">Quantité ↑</option>
                        </select>
                    </div>
                </form>
            </div>
            <div id="movements-table-wrapper">
                <div class="flex justify-center py-8 text-gray-400">
                    <i class="fas fa-spinner fa-spin text-2xl"></i> Chargement...
                </div>
            </div>
        </div>
    </div>
    <script>
        function loadMovementsTable() {
            const form = document.getElementById('filters-form');
            const wrapper = document.getElementById('movements-table-wrapper');
            const params = new URLSearchParams(new FormData(form)).toString();
            wrapper.innerHTML = '<div class="flex justify-center py-8 text-gray-400"><i class="fas fa-spinner fa-spin text-2xl"></i> Chargement...</div>';
            fetch(`{{ route('inventory.movements.history.table', $article) }}?${params}`)
                .then(r => r.text())
                .then(html => wrapper.innerHTML = html);
        }
        document.getElementById('filters-form').addEventListener('change', loadMovementsTable);
        document.addEventListener('DOMContentLoaded', loadMovementsTable);
    </script>
</x-app-layout>
