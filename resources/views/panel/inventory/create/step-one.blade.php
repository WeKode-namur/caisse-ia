<x-app-layout>
    <div class="lg:py-12">
        <div class="max-w-4xl mx-auto px-0 lg:px-8">
            <!-- En-tête avec navigation -->
            <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg mb-6">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-300 dark:border-gray-700">
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('inventory.create.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                        </a>
                        <div>
                            <h1 class="text-xl font-bold text-gray-900 dark:text-gray-50">
                                {{ $draftId ? 'Modifier l\'article' : 'Nouvel article' }}
                            </h1>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Étape 1/2 - Informations générales</p>
                        </div>
                    </div>

                    <!-- Indicateur de progression -->
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-blue-300 text-white rounded-full flex items-center justify-center text-sm font-medium">1</div>
                        <div class="w-8 h-1 bg-gray-300 dark:bg-gray-600"></div>
                        <div class="w-8 h-8 bg-gray-300 dark:bg-gray-600 text-gray-600 dark:text-gray-400 rounded-full flex items-center justify-center text-sm font-medium">2</div>
                    </div>
                </div>
            </div>

            <!-- Formulaire -->
            <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg">
                <div class="p-6">
                    <form action="{{ $formAction }}" method="POST" id="article-form">
                        @csrf
                        @if($draftId)
                            @method('PUT')

                            <input type="hidden" name="draft_id" value="{{ $draftId }}">
                        @endif
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nom de l'article -->
                            <div class="md:col-span-2">
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Nom de l'article <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       name="name"
                                       id="name"
                                       value="{{ old('name', $draft->name ?? '') }}"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none @error('name') border-red-500 @enderror"
                                       placeholder="Ex: T-shirt basique coton"
                                       required>
                                @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="md:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Description
                                </label>
                                <textarea name="description"
                                          id="description"
                                          rows="3"
                                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none @error('description') border-red-500 @enderror"
                                          placeholder="Description détaillée de l'article...">{{ old('description', $draft->description ?? '') }}</textarea>
                                @error('description')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            @suppliersEnabled
                                <div class="md:col-span-2">
                                    <label for="fournisseur_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Fournisseur
                                    </label>
                                    <select name="fournisseur_id" id="fournisseur_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                        <option value="">Aucun</option>
                                        @foreach($fournisseurs as $fournisseur)
                                            <option value="{{ $fournisseur->id }}" {{ old('fournisseur_id', $draft->fournisseur_id ?? '') == $fournisseur->id ? 'selected' : '' }}>
                                                {{ $fournisseur->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('fournisseur_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                    <div id="fournisseur-autocomplete"></div>
                                </div>
                            @endsuppliersEnabled


                            <!-- Catégorie -->
                            <div>
                                <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Catégorie <span class="text-red-500">*</span>
                                </label>
                                <select name="category_id"
                                        id="category_id"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none @error('category_id') border-red-500 @enderror"
                                        required>
                                    <option value="">Sélectionner une catégorie</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('category_id', $draft->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Type -->
                            <div>
                                <label for="type_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Type
                                </label>
                                <select name="type_id"
                                        id="type_id"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none @error('type_id') border-red-500 @enderror">
                                    <option value="">Sélectionner un type</option>
                                    <!-- Les options seront chargées dynamiquement -->
                                </select>
                                @error('type_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Sous-type -->
                            <div class="absolute" style="{{ config('custom.items.sousType') ? '' : 'left: -200%;' }}">
                                <label for="subtype_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Sous-type
                                </label>
                                <select name="subtype_id"
                                        id="subtype_id"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none @error('subtype_id') border-red-500 @enderror">
                                    <option value="">Sélectionner un sous-type</option>
                                    <!-- Les options seront chargées dynamiquement -->
                                </select>
                                @error('subtype_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Référence d'article -->
                            <div>
                                <label for="reference" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Référence article
{{--                                    <span class="text-xs text-gray-500">(auto-générée si vide)</span>--}}
                                </label>
                                <input type="text"
                                       name="reference"
                                       id="reference"
                                       value="{{ old('reference', $draft->reference ?? '') }}"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none @error('reference') border-red-500 @enderror"
                                       placeholder="Ex: VET-0001">
                                @error('reference')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
{{--                                <p class="text-xs text-gray-500 mt-1">Si vous laissez vide, une référence sera automatiquement générée basée sur la catégorie.</p>--}}
                            </div>

                            <!-- Info sur les codes-barres -->
                            <div class="md:col-span-2">
                                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                                                Code-barres et variants
                                            </h3>
                                            <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                                                <p>Les codes-barres sont gérés au niveau des variants (étape 2). Si votre article n'a qu'une seule version, un variant par défaut sera créé automatiquement.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TVA -->
                            <div class="md:col-span-2">
                                <label for="tva" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    TVA <span class="text-red-500">*</span>
                                </label>
                                <select name="tva"
                                        id="tva"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none @error('tva') border-red-500 @enderror"
                                        required>
                                    <option value="">Sélectionner un taux</option>
                                    @foreach($tvaRates as $rate)
                                        <option value="{{ $rate }}"
                                            {{ old('tva', $draft->tva ?? '') == $rate ? 'selected' : '' }}>
                                            {{ $rate }}%
                                        </option>
                                    @endforeach
                                </select>
                                @error('tva')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Section Prix par défaut -->
                            @php
                                $hasPrices = $draft && ($draft->buy_price || $draft->sell_price);
                                $showPrices = old('prix_unique', $hasPrices ? '1' : '');
                            @endphp
                            <div class="md:col-span-2 border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                                <div class="flex items-center space-x-3 mb-4">
                                    <input type="checkbox"
                                           id="prix_unique"
                                           name="prix_unique"
                                           value="1"
                                           {{ $showPrices ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600 rounded">
                                    <label for="prix_unique" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Définir des prix par défaut
                                    </label>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">
                                    Si coché, ces prix seront appliqués par défaut à tous les variants. Vous pourrez les personnaliser individuellement dans l'étape suivante.
                                </p>

                                <div id="prix_section" class="{{ $showPrices ? 'grid' : 'hidden' }} grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Prix d'achat -->
                                    <div>
                                        <label for="buy_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Prix d'achat (€)
                                        </label>
                                        <input type="number"
                                               name="buy_price"
                                               id="buy_price"
                                               value="{{ old('buy_price', $draft->buy_price ?? '') }}"
                                               step="0.01"
                                               min="0"
                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none @error('buy_price') border-red-500 @enderror"
                                               placeholder="0.00">
                                        @error('buy_price')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Prix de vente -->
                                    <div>
                                        <label for="sell_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Prix de vente TTC (€)
                                        </label>
                                        <input type="number"
                                               name="sell_price"
                                               id="sell_price"
                                               value="{{ old('sell_price', $draft->sell_price ?? '') }}"
                                               step="0.01"
                                               min="0"
                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none @error('sell_price') border-red-500 @enderror"
                                               placeholder="0.00">
                                        @error('sell_price')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Calcul automatique de marge -->
                                <div id="calculs_section" class="{{ $showPrices ? 'block' : 'hidden' }} mt-6">
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Calculs automatiques</h4>
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                            <div>
                                                <span class="text-gray-600 dark:text-gray-400">Prix HTVA:</span>
                                                <div class="font-medium" id="price-without-tax">-</div>
                                            </div>
                                            <div>
                                                <span class="text-gray-600 dark:text-gray-400">Marge unitaire:</span>
                                                <div class="font-medium" id="margin-amount">-</div>
                                            </div>
                                            <div>
                                                <span class="text-gray-600 dark:text-gray-400">Marge %:</span>
                                                <div class="font-medium" id="margin-percentage">-</div>
                                            </div>
                                            <div>
                                                <span class="text-gray-600 dark:text-gray-400">TVA:</span>
                                                <div class="font-medium" id="tax-amount">-</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-200 dark:border-gray-600">
                            <a href="{{ route('inventory.create.index') }}"
                               class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Annuler
                            </a>

                            <div class="flex items-center space-x-3">
                                <button type="submit"
                                        name="action"
                                        value="save_exit"
                                        class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                                    </svg>
                                    Sauvegarder et quitter
                                </button>

                                <button type="submit"
                                        name="action"
                                        value="save_continue"
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Continuer vers l'étape 2
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Gestion des listes déroulantes dépendantes
                const categorySelect = document.getElementById('category_id');
                const typeSelect = document.getElementById('type_id');
                const subtypeSelect = document.getElementById('subtype_id');

                // Charger les types lors du changement de catégorie
                categorySelect.addEventListener('change', async function() {
                    const categoryId = this.value;
                    typeSelect.innerHTML = '<option value="">Sélectionner un type</option>';
                    subtypeSelect.innerHTML = '<option value="">Sélectionner un sous-type</option>';

                    if (categoryId) {
                        try {
                            const response = await fetch(`/api/catalog/categories/${categoryId}/types`);
                            const types = await response.json();

                            types.forEach(type => {
                                const option = document.createElement('option');
                                option.value = type.id;
                                option.textContent = type.name;
                                option.selected = '{{ old('type_id', $draft->type_id ?? '') }}' == type.id;
                                typeSelect.appendChild(option);
                            });

                            if (typeSelect.value) {
                                typeSelect.dispatchEvent(new Event('change'));
                            }
                        } catch (error) {
                            console.error('Erreur lors du chargement des types:', error);
                        }
                    }
                });

// Charger les sous-types lors du changement de type
                typeSelect.addEventListener('change', async function() {
                    const typeId = this.value;
                    subtypeSelect.innerHTML = '<option value="">Sélectionner un sous-type</option>';

                    if (typeId) {
                        try {
                            const response = await fetch(`/api/catalog/types/${typeId}/subtypes`);
                            const subtypes = await response.json();

                            subtypes.forEach(subtype => {
                                const option = document.createElement('option');
                                option.value = subtype.id;
                                option.textContent = subtype.name;
                                option.selected = '{{ old('subtype_id', $draft->subtype_id ?? '') }}' == subtype.id;
                                subtypeSelect.appendChild(option);
                            });
                        } catch (error) {
                            console.error('Erreur lors du chargement des sous-types:', error);
                        }
                    }
                });

// Déclencher le chargement initial si une catégorie est sélectionnée
                if (categorySelect.value) {
                    categorySelect.dispatchEvent(new Event('change'));
                }

                // Le reste du code pour les prix reste identique...
                // Gestion des prix et calculs
                const prixUniqueCheckbox = document.getElementById('prix_unique');
                const prixSection = document.getElementById('prix_section');
                const calculsSection = document.getElementById('calculs_section');
                const buyPriceInput = document.getElementById('buy_price');
                const sellPriceInput = document.getElementById('sell_price');
                const tvaSelect = document.getElementById('tva');

                // Toggle de la section prix et calculs
                prixUniqueCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        prixSection.classList.remove('hidden');
                        prixSection.classList.add('grid');
                        calculsSection.classList.remove('hidden');
                        calculsSection.classList.add('block');
                    } else {
                        prixSection.classList.add('hidden');
                        prixSection.classList.remove('grid');
                        calculsSection.classList.add('hidden');
                        calculsSection.classList.remove('block');
                        // Vider les champs prix si on décoche
                        buyPriceInput.value = '';
                        sellPriceInput.value = '';
                        updateCalculations();
                    }
                });

                // Sécurité supplémentaire : avant soumission du formulaire
                document.getElementById('article-form').addEventListener('submit', function(e) {
                    // Si la checkbox n'est pas cochée, s'assurer que les prix sont vides
                    if (!prixUniqueCheckbox.checked) {
                        buyPriceInput.value = '';
                        sellPriceInput.value = '';
                    }
                });

                // Fonction de calculs automatiques
                function updateCalculations() {
                    if (!prixUniqueCheckbox.checked) {
                        document.getElementById('price-without-tax').textContent = '-';
                        document.getElementById('tax-amount').textContent = '-';
                        document.getElementById('margin-amount').textContent = '-';
                        document.getElementById('margin-percentage').textContent = '-';
                        return;
                    }

                    const buyPrice = parseFloat(buyPriceInput.value) || 0;
                    const sellPriceTTC = parseFloat(sellPriceInput.value) || 0;
                    const tvaRate = parseFloat(tvaSelect.value) || 0;

                    if (sellPriceTTC > 0 && tvaRate > 0) {
                        // Calcul du prix de vente HTVA (depuis TTC)
                        const sellPriceHTVA = sellPriceTTC / (1 + tvaRate / 100);
                        const taxAmount = sellPriceTTC - sellPriceHTVA;

                        // Calcul de la marge sur base HTVA
                        const margin = sellPriceHTVA - buyPrice;
                        const marginPercentage = buyPrice > 0 ? (margin / buyPrice) * 100 : 0;

                        document.getElementById('price-without-tax').textContent = sellPriceHTVA.toFixed(2) + ' €';
                        document.getElementById('tax-amount').textContent = taxAmount.toFixed(2) + ' €';
                        document.getElementById('margin-amount').textContent = margin.toFixed(2) + ' €';
                        document.getElementById('margin-percentage').textContent = marginPercentage.toFixed(1) + ' %';
                    } else {
                        document.getElementById('price-without-tax').textContent = '-';
                        document.getElementById('tax-amount').textContent = '-';
                        document.getElementById('margin-amount').textContent = '-';
                        document.getElementById('margin-percentage').textContent = '-';
                    }
                }

                // Événements pour les calculs
                buyPriceInput.addEventListener('input', updateCalculations);
                sellPriceInput.addEventListener('input', updateCalculations);
                tvaSelect.addEventListener('change', updateCalculations);

                // Calcul initial
                updateCalculations();
            });
        </script>
    @endpush
</x-app-layout>
