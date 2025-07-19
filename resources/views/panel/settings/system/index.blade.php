<x-app-layout>
    <div class="lg:py-12">
        <div class="max-w-7xl mx-auto px-0 lg:px-8">
            <!-- En-tête -->
            <div
                class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg mb-6">
                <div
                    class="text-gray-900 dark:text-gray-50 px-3 py-2 border-b border-gray-300 dark:border-gray-700 flex items-center justify-between">
                    <div class="flex items-center">
                        <a href="{{ route('settings.index') }}"
                           class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 mr-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                        </a>
                        <h1 class="font-bold lg:text-2xl text-xl">Paramètres système</h1>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400">
                            <i class="fas fa-shield-alt text-green-500"></i>
                            <span>Niveau {{ $userLevel >= 100 ? 'Administrateur' : 'Manager' }}</span>
                        </div>
                    </div>
                </div>
                <div class="p-4">
                    <p class="text-gray-600 dark:text-gray-400">
                        Configurez les paramètres système de votre application selon votre niveau d'accès.
                    </p>
                </div>
            </div>

            <!-- Messages -->
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('info'))
                <div class="mb-4 p-4 bg-blue-100 border border-blue-400 text-blue-700 rounded">
                    {{ session('info') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Onglets -->
            <div
                class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg mb-6">
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <nav class="flex space-x-8 px-6" aria-label="Tabs">
                        <button onclick="showTab('basic')" id="tab-basic"
                                class="tab-button active py-4 px-1 border-b-2 border-blue-500 font-medium text-sm text-blue-600 dark:text-blue-400">
                            <i class="fas fa-cog mr-2"></i>
                            Paramètres de base
                        </button>
                        <button onclick="showTab('company')" id="tab-company"
                                class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                            <i class="fas fa-building mr-2"></i>
                            Coordonnées entreprise
                        </button>
                        @if($userLevel >= 100)
                            <button onclick="showTab('advanced')" id="tab-advanced"
                                    class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                                <i class="fas fa-sliders-h mr-2"></i>
                                Paramètres avancés
                            </button>
                        @endif
                    </nav>
                </div>

                <div class="p-6">
                    <form action="{{ route('settings.system.update') }}" method="POST" id="settings-form">
                        @csrf

                        <!-- Onglet Paramètres de base -->
                        <div id="tab-content-basic" class="tab-content active">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- TVA par défaut -->
                                <div>
                                    <label for="register.tva_default"
                                           class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        TVA par défaut
                                    </label>
                                    <select name="register[tva_default]" id="register_tva_default"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                        <option
                                            value="" {{ $settings['register']['tva_default'] === null || $settings['register']['tva_default'] === '' ? 'selected' : '' }}>
                                            Aucune valeur par défaut
                                        </option>
                                        <option
                                            value="0" {{ $settings['register']['tva_default'] === 0 ? 'selected' : '' }}>
                                            0%
                                        </option>
                                        <option
                                            value="6" {{ $settings['register']['tva_default'] === 6 ? 'selected' : '' }}>
                                            6%
                                        </option>
                                        <option
                                            value="12" {{ $settings['register']['tva_default'] === 12 ? 'selected' : '' }}>
                                            12%
                                        </option>
                                        <option
                                            value="21" {{ $settings['register']['tva_default'] === 21 ? 'selected' : '' }}>
                                            21%
                                        </option>
                                    </select>
                                    <p class="text-xs text-gray-500 mt-1">TVA pré-sélectionnée lors de la création
                                        d'articles</p>
                                </div>

                                <!-- Pas des points de fidélité -->
                                <div>
                                    <label for="loyalty_point_step"
                                           class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Pas des points de fidélité
                                    </label>
                                    <input type="number" name="loyalty_point_step" id="loyalty_point_step"
                                           value="{{ $settings['loyalty_point_step'] }}" min="1" max="100"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                    <p class="text-xs text-gray-500 mt-1">Pas pour l'attribution des points de
                                        fidélité</p>
                                </div>
                            </div>
                        </div>

                        <!-- Onglet Coordonnées entreprise -->
                        <div id="tab-content-company" class="tab-content hidden">
                            <div class="space-y-6">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center">
                                        <i class="fas fa-building mr-2 text-indigo-600"></i>
                                        Coordonnées de l'entreprise
                                    </h3>
                                    <button type="button" onclick="fillFromEnv()"
                                            class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium py-2 px-3 rounded-md transition-colors duration-200">
                                        <i class="fas fa-sync-alt mr-1"></i>
                                        Remplir depuis .env
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Adresse -->
                                    <div>
                                        <label for="company.address_street"
                                               class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Adresse
                                        </label>
                                        <input type="text" name="company[address_street]" id="company_address_street"
                                               value="{{ is_string($settings['company']['address_street'] ?? '') ? ($settings['company']['address_street'] ?? '') : '' }}"
                                               maxlength="255"
                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                               placeholder="Ex: 1, Rue du Bureau">
                                    </div>

                                    <!-- Code postal -->
                                    <div>
                                        <label for="company.address_postal"
                                               class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Code postal
                                        </label>
                                        <input type="text" name="company[address_postal]" id="company_address_postal"
                                               value="{{ is_string($settings['company']['address_postal'] ?? '') ? ($settings['company']['address_postal'] ?? '') : '' }}"
                                               maxlength="20"
                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                               placeholder="Ex: 1234">
                                    </div>

                                    <!-- Ville -->
                                    <div>
                                        <label for="company.address_city"
                                               class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Ville
                                        </label>
                                        <input type="text" name="company[address_city]" id="company_address_city"
                                               value="{{ is_string($settings['company']['address_city'] ?? '') ? ($settings['company']['address_city'] ?? '') : '' }}"
                                               maxlength="100"
                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                               placeholder="Ex: Ville">
                                    </div>

                                    <!-- Pays -->
                                    <div>
                                        <label for="company.address_country"
                                               class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Pays
                                        </label>
                                        <input type="text" name="company[address_country]" id="company_address_country"
                                               value="{{ is_string($settings['company']['address_country'] ?? '') ? ($settings['company']['address_country'] ?? '') : '' }}"
                                               maxlength="100"
                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                               placeholder="Ex: Belgique">
                                    </div>

                                    <!-- Numéro de TVA -->
                                    <div>
                                        <label for="company.tva_number"
                                               class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Numéro de TVA
                                        </label>
                                        <input type="text" name="company[tva_number]" id="company_tva_number"
                                               value="{{ is_string($settings['company']['tva_number'] ?? '') ? ($settings['company']['tva_number'] ?? '') : '' }}"
                                               maxlength="50"
                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                               placeholder="Ex: BE 0844.111.222">
                                    </div>

                                    <!-- Téléphone -->
                                    <div>
                                        <label for="company.phone"
                                               class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Téléphone
                                        </label>
                                        <input type="text" name="company[phone]" id="company_phone"
                                               value="{{ is_string($settings['company']['phone'] ?? '') ? ($settings['company']['phone'] ?? '') : '' }}"
                                               maxlength="20"
                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                               placeholder="Ex: 081 22 33 44">
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($userLevel >= 100)
                            <!-- Onglet Paramètres avancés -->
                            <div id="tab-content-advanced" class="tab-content hidden">
                                <div class="space-y-8">
                                    <!-- Section Caisse -->
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                                            <i class="fas fa-cash-register mr-2 text-blue-600"></i>
                                            Configuration de la caisse
                                        </h3>
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                            <!-- Gestion des clients -->
                                            <div>
                                                <label class="flex items-center">
                                                    <input type="checkbox" name="register[customer_management]"
                                                           value="1"
                                                           {{ $settings['register']['customer_management'] ? 'checked' : '' }}
                                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Gestion des clients</span>
                                                </label>
                                                <p class="text-xs text-gray-500 mt-1">Activer la gestion des clients
                                                    dans la caisse</p>
                                            </div>

                                            <!-- Méthode d'arrondissement -->
                                            <div>
                                                <label class="flex items-center">
                                                    <input type="checkbox" name="register[arrondissement_method]"
                                                           value="1"
                                                           {{ $settings['register']['arrondissement_method'] ? 'checked' : '' }}
                                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Arrondissement</span>
                                                </label>
                                                <p class="text-xs text-gray-500 mt-1">Activer l'arrondissement des
                                                    prix</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Section Articles -->
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                                            <i class="fas fa-box mr-2 text-green-600"></i>
                                            Configuration des articles
                                        </h3>
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                            <!-- Seuil d'alerte -->
                                            <div>
                                                <label for="article.seuil"
                                                       class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                    Seuil d'alerte stock
                                                </label>
                                                <input type="number" name="article[seuil]" id="article_seuil"
                                                       value="{{ $settings['article']['seuil'] }}" min="0" max="1000"
                                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                                <p class="text-xs text-gray-500 mt-1">Seuil pour les alertes de stock
                                                    bas</p>
                                            </div>

                                            <!-- Sous-types -->
                                            <div>
                                                <label class="flex items-center">
                                                    <input type="checkbox" name="items[sous_type]" value="1"
                                                           {{ $settings['items']['sous_type'] ? 'checked' : '' }}
                                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Sous-types</span>
                                                </label>
                                                <p class="text-xs text-gray-500 mt-1">Activer les sous-types pour les
                                                    articles</p>
                                            </div>

                                            <!-- Fournisseurs -->
                                            <div>
                                                <label class="flex items-center">
                                                    <input type="checkbox" name="suppliers_enabled" value="1"
                                                           {{ $settings['suppliers_enabled'] ? 'checked' : '' }}
                                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Fournisseurs</span>
                                                </label>
                                                <p class="text-xs text-gray-500 mt-1">Activer la gestion des
                                                    fournisseurs</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Section Codes-barres -->
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                                            <i class="fas fa-barcode mr-2 text-purple-600"></i>
                                            Configuration des codes-barres
                                        </h3>
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                            <!-- Générateur de codes-barres -->
                                            <div>
                                                <label class="flex items-center">
                                                    <input type="checkbox" name="generator[barcode]" value="1"
                                                           {{ $settings['generator']['barcode'] ? 'checked' : '' }}
                                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Générateur automatique</span>
                                                </label>
                                                <p class="text-xs text-gray-500 mt-1">Générer automatiquement les
                                                    codes-barres</p>
                                            </div>

                                            <!-- Préfixe 1 -->
                                            <div>
                                                <label for="barcode.prefix_one"
                                                       class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                    Préfixe 1
                                                </label>
                                                <input type="text" name="barcode[prefix_one]" id="barcode_prefix_one"
                                                       value="{{ $settings['barcode']['prefix_one'] }}" maxlength="10"
                                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                                <p class="text-xs text-gray-500 mt-1">Premier préfixe pour les
                                                    codes-barres</p>
                                            </div>

                                            <!-- Préfixe 2 -->
                                            <div>
                                                <label for="barcode.prefix_two"
                                                       class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                    Préfixe 2
                                                </label>
                                                <input type="text" name="barcode[prefix_two]" id="barcode_prefix_two"
                                                       value="{{ $settings['barcode']['prefix_two'] }}" maxlength="10"
                                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                                <p class="text-xs text-gray-500 mt-1">Deuxième préfixe pour les
                                                    codes-barres</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Section Options -->
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                                            <i class="fas fa-cogs mr-2 text-orange-600"></i>
                                            Options générales
                                        </h3>
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                            <!-- Email actif -->
                                            <div>
                                                <label class="flex items-center">
                                                    <input type="checkbox" name="email[active]" value="1"
                                                           {{ $settings['email']['active'] ? 'checked' : '' }}
                                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Notifications email</span>
                                                </label>
                                                <p class="text-xs text-gray-500 mt-1">Activer les notifications par
                                                    email</p>
                                            </div>

                                            <!-- Référent lot optionnel -->
                                            <div>
                                                <label class="flex items-center">
                                                    <input type="checkbox" name="referent_lot_optionnel" value="1"
                                                           {{ $settings['referent_lot_optionnel'] ? 'checked' : '' }}
                                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Référent lot optionnel</span>
                                                </label>
                                                <p class="text-xs text-gray-500 mt-1">Rendre le référent de lot
                                                    optionnel</p>
                                            </div>

                                            <!-- Date expiration optionnelle -->
                                            <div>
                                                <label class="flex items-center">
                                                    <input type="checkbox" name="date_expiration_optionnel" value="1"
                                                           {{ $settings['date_expiration_optionnel'] ? 'checked' : '' }}
                                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Date d'expiration optionnelle</span>
                                                </label>
                                                <p class="text-xs text-gray-500 mt-1">Rendre la date d'expiration
                                                    optionnelle</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Boutons d'action -->
                        <div
                            class="flex justify-end items-center pt-6 border-t border-gray-200 dark:border-gray-600 mt-8">
                            <div class="flex space-x-3">
                                <button type="button" onclick="handleSave()"
                                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-200">
                                    <i class="fas fa-save mr-2"></i>
                                    Sauvegarder
                                </button>
                                <button type="button" onclick="openModal('reset')"
                                        class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-md transition-colors duration-200">
                                    <i class="fas fa-undo mr-2"></i>
                                    Réinitialiser
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmation des modifications -->
    <x-modal name="changes" title="Modifications effectuées" subtitle="Les modifications suivantes ont été appliquées :"
             size="4xl" icon="check-circle" iconColor="green">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Paramètre
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Ancienne valeur
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Nouvelle valeur
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Action
                    </th>
                </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"
                       id="changes-table-body">
                <!-- Le contenu sera rempli dynamiquement -->
                </tbody>
            </table>
        </div>

        <x-slot name="actions">
            <button type="button" onclick="closeModal('changes')"
                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md hover:bg-gray-50 dark:hover:bg-gray-500 transition-colors">
                Continuer les modifications
            </button>
            <button type="button" onclick="confirmChanges()"
                    class="px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 transition-colors">
                <i class="fas fa-check mr-2"></i>
                Confirmer
            </button>
        </x-slot>
    </x-modal>

    <!-- Modal de réinitialisation -->
    <x-modal name="reset" title="Réinitialisation des paramètres"
             subtitle="Êtes-vous sûr de vouloir réinitialiser tous les paramètres aux valeurs par défaut ?"
             icon="exclamation-triangle" iconColor="orange">
        <p class="text-gray-600 dark:text-gray-400">
            Cette action réinitialisera tous les paramètres système aux valeurs par défaut. Cette opération est
            irréversible.
        </p>

        <x-slot name="actions">
            <button type="button" onclick="closeModal('reset')"
                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md hover:bg-gray-50 dark:hover:bg-gray-500 transition-colors">
                Annuler
            </button>
            <a href="{{ route('settings.system.reset') }}"
               class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 transition-colors">
                <i class="fas fa-undo mr-2"></i>
                Réinitialiser
            </a>
        </x-slot>
    </x-modal>

    <script>
        // Token CSRF pour les requêtes AJAX
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
            document.querySelector('input[name="_token"]')?.value;

        // Gestion des onglets
        function showTab(tabName) {
            // Masquer tous les contenus d'onglets
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
                content.classList.remove('active');
            });

            // Désactiver tous les boutons d'onglets
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('active', 'border-blue-500', 'text-blue-600', 'dark:text-blue-400');
                button.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
            });

            // Afficher le contenu de l'onglet sélectionné
            document.getElementById('tab-content-' + tabName).classList.remove('hidden');
            document.getElementById('tab-content-' + tabName).classList.add('active');

            // Activer le bouton de l'onglet sélectionné
            document.getElementById('tab-' + tabName).classList.add('active', 'border-blue-500', 'text-blue-600', 'dark:text-blue-400');
            document.getElementById('tab-' + tabName).classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
        }

        // Fonction pour annuler une modification
        function cancelChange(field) {
            fetch('{{ route("settings.system.cancel-change") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({field: field})
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Mettre à jour les valeurs dans le formulaire
                        if (data.updatedSettings) {
                            updateFormValues(data.updatedSettings);
                        }

                        // Afficher une notification de succès
                        showNotification(data.message, 'success');

                        // Mettre à jour le modal si il y a encore des modifications
                        if (data.remaining_changes > 0) {
                            // Recharger les modifications dans le modal
                            fetch('{{ route("settings.system.get-changes") }}', {
                                method: 'GET',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken
                                }
                            })
                                .then(response => response.json())
                                .then(changeData => {
                                    if (changeData.success && changeData.changes) {
                                        const realChanges = filterRealChanges(changeData.changes);
                                        displayChanges(realChanges);
                                    }
                                });
                        } else {
                            // Fermer le modal s'il n'y a plus de modifications
                            closeModal('changes');
                        }
                    } else {
                        showNotification('Erreur lors de l\'annulation : ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    showNotification('Erreur lors de l\'annulation', 'error');
                });
        }

        // Fonction pour gérer le clic sur Sauvegarder
        function handleSave() {
            // Soumettre le formulaire via AJAX pour détecter les modifications
            const form = document.getElementById('settings-form');
            const formData = new FormData(form);

            fetch('{{ route("settings.system.update") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    console.log('DEBUG: Réponse du serveur:', data);

                    if (data.success && data.changes && Object.keys(data.changes).length > 0) {
                        // Filtrer les modifications qui ont vraiment changé
                        const realChanges = filterRealChanges(data.changes);
                        console.log('DEBUG: Modifications réelles après filtrage:', realChanges);

                        if (Object.keys(realChanges).length > 0) {
                            displayChanges(realChanges);
                            openModal('changes');
                        } else {
                            // Pas de vraies modifications
                            alert('Aucune modification détectée.');
                        }
                    } else {
                        // Pas de modifications
                        alert(data.message || 'Aucune modification détectée.');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors de la détection des modifications');
                });
        }

        // Fonction pour filtrer les vraies modifications
        function filterRealChanges(changes) {
            const realChanges = {};

            Object.entries(changes).forEach(([field, change]) => {
                const oldValue = normalizeValue(change.old);
                const newValue = normalizeValue(change.new);

                console.log(`DEBUG: ${field} - Old: '${oldValue}' (${typeof oldValue}) New: '${newValue}' (${typeof newValue})`);

                if (oldValue !== newValue) {
                    realChanges[field] = change;
                    console.log(`DEBUG: ${field} - VRAI CHANGEMENT détecté`);
                } else {
                    console.log(`DEBUG: ${field} - Pas de changement réel`);
                }
            });

            return realChanges;
        }

        // Fonction pour normaliser les valeurs côté client
        function normalizeValue(value) {
            if (value === null || value === undefined || value === '') {
                return null;
            }
            if (value === 'null' || value === 'undefined') {
                return null;
            }
            if (typeof value === 'string') {
                return value.trim();
            }
            if (typeof value === 'number') {
                return String(value);
            }
            if (typeof value === 'boolean') {
                return value ? '1' : '0';
            }
            return value;
        }

        // Fonction pour afficher les modifications dans le modal
        function displayChanges(changes) {
            const tbody = document.getElementById('changes-table-body');
            tbody.innerHTML = '';

            const labels = {
                'register.tva_default': 'TVA par défaut',
                'register.customer_management': 'Gestion des clients',
                'register.arrondissement_method': 'Méthode d\'arrondissement',
                'article.seuil': 'Seuil d\'alerte stock',
                'generator.barcode': 'Générateur de codes-barres',
                'email.active': 'Notifications email',
                'barcode.prefix_one': 'Préfixe 1',
                'barcode.prefix_two': 'Préfixe 2',
                'referent_lot_optionnel': 'Référent lot optionnel',
                'date_expiration_optionnel': 'Date d\'expiration optionnelle',
                'loyalty_point_step': 'Pas des points de fidélité',
                'items.sous_type': 'Sous-types',
                'suppliers_enabled': 'Fournisseurs',
                'company.address_street': 'Adresse',
                'company.address_postal': 'Code postal',
                'company.address_city': 'Ville',
                'company.address_country': 'Pays',
                'company.tva_number': 'Numéro de TVA',
                'company.phone': 'Téléphone',
            };

            Object.entries(changes).forEach(([field, change]) => {
                // Vérifier si c'est un auto-remplissage depuis le .env
                const envMapping = {
                    'company.address_street': 'CUSTOM_ADDRESS_STREET',
                    'company.address_postal': 'CUSTOM_ADDRESS_POSTAL',
                    'company.address_city': 'CUSTOM_ADDRESS_CITY',
                    'company.address_country': 'CUSTOM_ADDRESS_COUNTRY',
                    'company.tva_number': 'CUSTOM_TVA_NUMBER',
                    'company.phone': 'CUSTOM_PHONE',
                };

                let isAutoFill = false;
                if (envMapping[field]) {
                    const oldValue = change.old || null;
                    const newValue = change.new || null;
                    if (empty(oldValue) && !empty(newValue)) {
                        isAutoFill = true;
                    }
                }

                if (!isAutoFill) {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                            ${labels[field] || field}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            ${change.old || 'Non défini'}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            ${change.new || 'Non défini'}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            <button onclick="cancelChange('${field}')"
                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                <i class="fas fa-undo mr-1"></i>
                                Annuler
                            </button>
                        </td>
                    `;
                    tbody.appendChild(row);
                }
            });
        }

        function confirmChanges() {
            console.log('DEBUG: Fonction confirmChanges appelée');

            // Appeler la route de confirmation pour sauvegarder les modifications
            fetch('{{ route("settings.system.confirm") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
                .then(response => {
                    console.log('DEBUG: Réponse brute:', response);
                    return response.json();
                })
                .then(data => {
                    console.log('DEBUG: Réponse de confirmation:', data);

                    if (data.success) {
                        // Fermer le modal
                        if (typeof closeModal === 'function') {
                            closeModal('changes');
                        } else {
                            console.log('DEBUG: closeModal n\'est pas disponible');
                        }

                        // Afficher un message de succès avec notification toast
                        showNotification(data.message, 'success');

                        // Mettre à jour les valeurs dans le formulaire sans recharger la page
                        if (data.updatedSettings) {
                            updateFormValues(data.updatedSettings);
                        }
                    } else {
                        // Afficher les erreurs
                        let errorMessage = data.message;
                        if (data.errors && data.errors.length > 0) {
                            errorMessage += '\n\nErreurs:\n' + data.errors.join('\n');
                        }
                        showNotification(errorMessage, 'error');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    showNotification('Erreur lors de la confirmation des modifications', 'error');
                });
        }

        // Fonction pour mettre à jour les valeurs du formulaire sans recharger la page
        function updateFormValues(settings) {
            console.log('DEBUG: Mise à jour des valeurs du formulaire:', settings);

            // Mettre à jour les champs de base
            if (settings.register && settings.register.tva_default !== undefined) {
                const tvaSelect = document.getElementById('register_tva_default');
                if (tvaSelect) {
                    tvaSelect.value = settings.register.tva_default;
                }
            }

            if (settings.loyalty_point_step !== undefined) {
                const loyaltyInput = document.getElementById('loyalty_point_step');
                if (loyaltyInput) {
                    loyaltyInput.value = settings.loyalty_point_step;
                }
            }

            // Mettre à jour les champs de l'entreprise
            if (settings.company) {
                const companyFields = [
                    'address_street', 'address_postal', 'address_city',
                    'address_country', 'tva_number', 'phone'
                ];

                companyFields.forEach(field => {
                    if (settings.company[field] !== undefined) {
                        const input = document.getElementById(`company_${field}`);
                        if (input) {
                            input.value = settings.company[field] || '';
                        }
                    }
                });
            }

            // Mettre à jour les champs avancés
            if (settings.register && settings.register.customer_management !== undefined) {
                const customerCheckbox = document.querySelector('input[name="register[customer_management]"]');
                if (customerCheckbox) {
                    customerCheckbox.checked = settings.register.customer_management;
                }
            }

            if (settings.register && settings.register.arrondissement_method !== undefined) {
                const arrondissementSelect = document.getElementById('register_arrondissement_method');
                if (arrondissementSelect) {
                    arrondissementSelect.value = settings.register.arrondissement_method;
                }
            }

            if (settings.article && settings.article.seuil !== undefined) {
                const seuilInput = document.getElementById('article_seuil');
                if (seuilInput) {
                    seuilInput.value = settings.article.seuil;
                }
            }

            if (settings.generator && settings.generator.barcode !== undefined) {
                const barcodeSelect = document.getElementById('generator_barcode');
                if (barcodeSelect) {
                    barcodeSelect.value = settings.generator.barcode;
                }
            }

            if (settings.barcode) {
                if (settings.barcode.prefix_one !== undefined) {
                    const prefixOneInput = document.getElementById('barcode_prefix_one');
                    if (prefixOneInput) {
                        prefixOneInput.value = settings.barcode.prefix_one;
                    }
                }

                if (settings.barcode.prefix_two !== undefined) {
                    const prefixTwoInput = document.getElementById('barcode_prefix_two');
                    if (prefixTwoInput) {
                        prefixTwoInput.value = settings.barcode.prefix_two;
                    }
                }
            }

            // Mettre à jour les checkboxes
            if (settings.referent_lot_optionnel !== undefined) {
                const referentCheckbox = document.querySelector('input[name="referent_lot_optionnel"]');
                if (referentCheckbox) {
                    referentCheckbox.checked = settings.referent_lot_optionnel;
                }
            }

            if (settings.date_expiration_optionnel !== undefined) {
                const expirationCheckbox = document.querySelector('input[name="date_expiration_optionnel"]');
                if (expirationCheckbox) {
                    expirationCheckbox.checked = settings.date_expiration_optionnel;
                }
            }

            if (settings.email && settings.email.active !== undefined) {
                const emailCheckbox = document.querySelector('input[name="email[active]"]');
                if (emailCheckbox) {
                    emailCheckbox.checked = settings.email.active;
                }
            }

            if (settings.items && settings.items.sous_type !== undefined) {
                const sousTypeCheckbox = document.querySelector('input[name="items[sous_type]"]');
                if (sousTypeCheckbox) {
                    sousTypeCheckbox.checked = settings.items.sous_type;
                }
            }

            if (settings.suppliers_enabled !== undefined) {
                const suppliersCheckbox = document.querySelector('input[name="suppliers_enabled"]');
                if (suppliersCheckbox) {
                    suppliersCheckbox.checked = settings.suppliers_enabled;
                }
            }

            console.log('DEBUG: Mise à jour du formulaire terminée');
        }

        // Fonction de notification toast style caisse
        function showNotification(message, type = 'info') {
            // Créer le container de notifications s'il n'existe pas
            let container = document.getElementById('notifications-container');
            if (!container) {
                container = document.createElement('div');
                container.id = 'notifications-container';
                container.className = 'fixed top-4 right-4 z-50 space-y-2';
                document.body.appendChild(container);
            }

            // Créer la notification
            const notification = document.createElement('div');
            const notificationId = 'notification-' + Date.now();
            notification.id = notificationId;

            const bgColor = {
                'success': 'bg-green-500',
                'error': 'bg-red-500',
                'warning': 'bg-orange-500',
                'info': 'bg-blue-500'
            }[type] || 'bg-blue-500';

            const icon = {
                'success': 'fas fa-check-circle',
                'error': 'fas fa-exclamation-circle',
                'warning': 'fas fa-exclamation-triangle',
                'info': 'fas fa-info-circle'
            }[type] || 'fas fa-info-circle';

            notification.className = `${bgColor} text-white px-4 py-3 rounded-lg shadow-lg transform translate-x-full transition-transform duration-300 flex items-center space-x-3 min-w-72`;

            notification.innerHTML = `
                <i class="${icon}"></i>
                <span class="flex-1">${message}</span>
                <button onclick="this.parentElement.remove()" class="text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            `;

            container.appendChild(notification);

            // Animation d'entrée
            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
            }, 100);

            // Auto-suppression après 4 secondes
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.style.transform = 'translateX(100%)';
                    setTimeout(() => {
                        if (notification.parentElement) {
                            notification.remove();
                        }
                    }, 300);
                }
            }, 4000);
        }

        // Fonction pour remplir les coordonnées depuis le .env
        function fillFromEnv() {
            if (confirm('Voulez-vous remplir les coordonnées de l\'entreprise avec les valeurs du fichier .env ? Cela remplacera les valeurs actuelles.')) {
                fetch('{{ route("settings.system.fill-from-env") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Mettre à jour les valeurs dans le formulaire
                            if (data.updatedSettings) {
                                updateFormValues(data.updatedSettings);
                            }

                            // Afficher une notification de succès
                            showNotification('Coordonnées remplies depuis le fichier .env avec succès', 'success');
                        } else {
                            showNotification('Erreur lors du remplissage : ' + data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        showNotification('Erreur lors du remplissage depuis le .env', 'error');
                    });
            }
        }

        // Fonction helper pour vérifier si une valeur est vide
        function empty(value) {
            return value === null || value === undefined || value === '';
        }

        // Fonction pour ouvrir le modal de modifications
        function openChangesModal() {
            openModal('changes');
        }

        // Fonction pour fermer le modal de modifications
        function closeChangesModal() {
            closeModal('changes');
        }

        // Vérifier au chargement de la page s'il faut afficher le modal
        document.addEventListener('DOMContentLoaded', function () {
            // Vérifier si il y a des modifications en session
            fetch('{{ route("settings.system.get-changes") }}', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            })
                .then(response => response.json())
                .then(data => {
                    console.log('DEBUG: Vérification des modifications au chargement:', data);

                    if (data.success && data.changes && Object.keys(data.changes).length > 0) {
                        // Filtrer les modifications qui ont vraiment changé
                        const realChanges = filterRealChanges(data.changes);
                        console.log('DEBUG: Modifications réelles au chargement:', realChanges);

                        if (Object.keys(realChanges).length > 0) {
                            displayChanges(realChanges);
                            openModal('changes');
                        }
                    }
                })
                .catch(error => {
                    console.error('Erreur lors de la vérification des modifications:', error);
                });
        });
    </script>
</x-app-layout>
