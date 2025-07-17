<x-app-layout>
    <div class="lg:py-12">
        <div class="max-w-7xl mx-auto px-0 lg:px-8">
            <!-- En-tête -->
            <div
                class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg mb-6">
                <div
                    class="text-gray-900 dark:text-gray-50 px-3 py-2 border-b border-gray-300 dark:border-gray-700 flex items-center justify-between">
                    <h1 class="font-bold lg:text-2xl text-xl">Paramètres</h1>
                    <div class="flex items-center space-x-3">
                        <div class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400">
                            <i class="fas fa-shield-alt text-green-500"></i>
                            <span>Accès sécurisé aux paramètres</span>
                        </div>
                        <button onclick="resetPasswordConfirmation()"
                                class="bg-gray-500 text-white rounded py-1 px-3 hover:opacity-75 hover:scale-105 transition-all duration-200">
                            <i class="fas fa-lock mr-1"></i>
                            Verrouiller
                        </button>
                    </div>
                </div>
                <div class="p-4">
                    <p class="text-gray-600 dark:text-gray-400">
                        Gérez la configuration complète de votre système de caisse en toute sécurité.
                        Toutes les modifications sont tracées et protégées.
                    </p>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div
                    class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg">
                    <div class="p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                    {{ number_format($stats['total_articles']) }}
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Total Articles</div>
                            </div>
                            <div
                                class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                <i class="fas fa-boxes text-blue-600 dark:text-blue-400"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg">
                    <div class="p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-2xl font-bold text-red-600 dark:text-red-400">
                                    {{ number_format($stats['zero_stock_articles']) }}
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Stock Zéro</div>
                            </div>
                            <div
                                class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                                <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg">
                    <div class="p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                                    {{ number_format($stats['active_users']) }}
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Utilisateurs Actifs</div>
                                <div class="text-xs text-gray-500 dark:text-gray-500">
                                    / {{ number_format($stats['total_users']) }} total
                                </div>
                            </div>
                            <div
                                class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                                <i class="fas fa-users text-green-600 dark:text-green-400"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg">
                    <div class="p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                                    {{ number_format($stats['total_categories']) }}
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Catégories</div>
                            </div>
                            <div
                                class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                                <i class="fas fa-folder text-purple-600 dark:text-purple-400"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section Configuration -->
            <div
                class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg mb-6">
                <h2 class="font-semibold text-xl px-3 py-2 border-b border-gray-300 dark:border-gray-700 flex items-center">
                    <i class="fas fa-cogs mr-3 text-green-600"></i>
                    Configuration
                </h2>
                <div class="p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- Attributs -->
                        <div
                            class="bg-white dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600 hover:shadow-md transition-all duration-200">
                            <div class="flex items-center mb-3">
                                <div
                                    class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-tags text-purple-600 dark:text-purple-400"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-white">Attributs</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Caractéristiques produits</p>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <a href="{{ route('settings.attributes.index') }}"
                                   class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-600 rounded hover:bg-gray-100 dark:hover:bg-gray-500 transition-colors">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Liste des attributs</span>
                                    <i class="fas fa-chevron-right text-gray-400"></i>
                                </a>
                                <a href="{{ route('settings.attributes.create') }}"
                                   class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-600 rounded hover:bg-gray-100 dark:hover:bg-gray-500 transition-colors">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Nouvel attribut</span>
                                    <i class="fas fa-plus text-gray-400"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Catégories -->
                        <div
                            class="bg-white dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600 hover:shadow-md transition-all duration-200">
                            <div class="flex items-center mb-3">
                                <div
                                    class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-folder text-green-600 dark:text-green-400"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-white">Catégories</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Organisation produits</p>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <a href="{{ route('settings.categories.index') }}"
                                   class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-600 rounded hover:bg-gray-100 dark:hover:bg-gray-500 transition-colors">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Gestion des catégories</span>
                                    <i class="fas fa-chevron-right text-gray-400"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Articles inconnus -->
                        <div
                            class="bg-white dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600 hover:shadow-md transition-all duration-200">
                            <div class="flex items-center mb-3">
                                <div
                                    class="w-10 h-10 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-question-circle text-orange-600 dark:text-orange-400"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-white">Articles inconnus</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Gérez les articles non
                                        identifiés</p>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <a href="{{ route('settings.unknown-items.index') }}"
                                   class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-600 rounded hover:bg-gray-100 dark:hover:bg-gray-500 transition-colors">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Articles inconnus</span>
                                    <span
                                        class="text-xs bg-orange-100 text-orange-800 px-2 py-1 rounded-full font-medium">
                                        {{ $stats['unknown_items'] ?? 0 }}
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section Administration -->
            <div
                class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg mb-6">
                <h2 class="font-semibold text-xl px-3 py-2 border-b border-gray-300 dark:border-gray-700 flex items-center">
                    <i class="fas fa-user-shield mr-3 text-blue-600"></i>
                    Administration
                </h2>
                <div class="p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- Gestion des utilisateurs -->
                        <div
                            class="bg-white dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600 hover:shadow-md transition-all duration-200">
                            <div class="flex items-center mb-3">
                                <div
                                    class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-users-cog text-blue-600 dark:text-blue-400"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-white">Utilisateurs</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Gérer les accès</p>
                                </div>
                            </div>
                            <div class="space-y-2">
                                {{--                                <a href="{{ route('settings.users.index') }}"--}}
                                <button href="" disabled
                                        class="disabled:bg-gray-200 w-full disabled:text-gray-500 disabled:cursor-not-allowed flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-600 rounded hover:bg-gray-100 dark:hover:bg-gray-500 transition-colors">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Liste des utilisateurs</span>
                                    <i class="fas fa-chevron-right text-gray-400"></i>
                                </button>

                                {{--                                <a href="{{ route('settings.users.create') }}"--}}
                                <button href="" disabled
                                        class="disabled:bg-gray-200 w-full disabled:text-gray-500 disabled:cursor-not-allowed flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-600 rounded hover:bg-gray-100 dark:hover:bg-gray-500 transition-colors">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Nouvel utilisateur</span>
                                    <i class="fas fa-plus text-gray-400"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Logs système -->
                        <div
                            class="bg-white dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600 hover:shadow-md transition-all duration-200">
                            <div class="flex items-center mb-3">
                                <div
                                    class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-file-alt text-indigo-600 dark:text-indigo-400"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-white">Logs Système</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Historique des actions</p>
                                </div>
                            </div>
                            <div class="space-y-2">
                                {{--                                <a href="{{ route('settings.logs.index') }}"--}}
                                <button href="" disabled
                                        class="disabled:bg-gray-200 w-full disabled:text-gray-500 disabled:cursor-not-allowed flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-600 rounded hover:bg-gray-100 dark:hover:bg-gray-500 transition-colors">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Logs généraux</span>
                                    <i class="fas fa-chevron-right text-gray-400"></i>
                                </button>
                                {{--                                <a href="{{ route('settings.logs.cash-register') }}"--}}
                                <button href="" disabled
                                        class="disabled:bg-gray-200 w-full disabled:text-gray-500 disabled:cursor-not-allowed flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-600 rounded hover:bg-gray-100 dark:hover:bg-gray-500 transition-colors">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Logs caisse</span>
                                    <i class="fas fa-cash-register text-gray-400"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Mises à jour -->
                        <div
                            class="bg-white dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600 hover:shadow-md transition-all duration-200">
                            <div class="flex items-center mb-3">
                                <div
                                    class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-history text-yellow-600 dark:text-yellow-400"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-white">Mises à jour</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Historique versions</p>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <a href="{{ route('settings.updates.index') }}"
                                   class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-600 rounded hover:bg-gray-100 dark:hover:bg-gray-500 transition-colors">
                                    <span
                                        class="text-sm text-gray-700 dark:text-gray-300">Historique des versions</span>
                                    <i class="fas fa-chevron-right text-gray-400"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        function resetPasswordConfirmation() {
            if (confirm('Êtes-vous sûr de vouloir réinitialiser l\'accès aux paramètres ? Vous devrez saisir votre mot de passe à nouveau.')) {
                // Créer un formulaire temporaire pour envoyer la requête POST
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("settings.reset-session") }}';

                // Ajouter le token CSRF
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);

                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</x-app-layout>
