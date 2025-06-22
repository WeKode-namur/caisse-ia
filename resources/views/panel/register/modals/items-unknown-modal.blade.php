<div x-show="showUnknownItemModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
    <!-- Contenu modal Article Z -->
    <div class="bg-black bg-opacity-50 flex items-center justify-center p-4 h-full w-full backdrop-blur-sm">
        <div class="bg-white rounded xl:w-2/5 md:w-2/3 w-full max-h-[90vh] flex flex-col">
            <div class="border-b flex gap-2 items-center py-2 px-3">
                <h2 class="text-xl">Article Z</h2>
                <span class="bg-orange-100 text-orange-800 text-xs px-2 py-1 rounded-full">Sans code-barres</span>
                <button type="button" class="ms-auto bg-gray-500 text-white hover:opacity-75 rounded py-1 px-2 text-sm hover:shadow hover:scale-105 duration-300" title="Paramètres">
                    <i class="fas fa-gears"></i>
                </button>
                <button @click="closeAllModals()" class="bg-red-500 text-white hover:opacity-75 rounded py-1 px-2 text-sm hover:shadow hover:scale-105 duration-300">
                    <i class="fas fa-x"></i>
                </button>
            </div>
            <div class="p-4 flex-1 overflow-y-auto flex flex-col">
                <!-- Formulaire de création Article Z -->
                <div class="space-y-4">
                    <!-- Nom de l'article -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-tag mr-1"></i>
                            Nom de l'article *
                        </label>
                        <div class="border rounded flex items-center text-base">
                            <input type="text" name="item_name" placeholder="Ex: Réparation express, Article personnalisé..." class="border-0 px-3 py-2 flex-1 outline-0 focus:text-black" required>
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-align-left mr-1"></i>
                            Description
                        </label>
                        <div class="border rounded text-base">
                            <textarea name="item_description" placeholder="Description détaillée de l'article ou du service..." class="border-0 px-3 py-2 w-full outline-0 focus:text-black resize-none" rows="3"></textarea>
                        </div>
                    </div>

                    <!-- Prix -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-euro-sign mr-1"></i>
                            Prix de vente *
                        </label>
                        <div class="border rounded flex items-center text-base">
                            <input type="number" name="item_price" placeholder="0.00" class="border-0 px-3 py-2 flex-1 outline-0 focus:text-black" min="0" step="0.01" required>
                            <span class="px-3 text-gray-500">€</span>
                        </div>
                    </div>

                    <!-- Boutons rapides pour prix courants -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-bolt mr-1"></i>
                            Prix fréquents
                        </label>
                        <div class="grid grid-cols-4 gap-2">
                            <button type="button" onclick="document.querySelector('input[name=item_price]').value = '5.00'" class="bg-blue-100 hover:bg-blue-200 text-blue-800 py-2 rounded font-medium transition-colors text-sm">
                                5€
                            </button>
                            <button type="button" onclick="document.querySelector('input[name=item_price]').value = '10.00'" class="bg-blue-100 hover:bg-blue-200 text-blue-800 py-2 rounded font-medium transition-colors text-sm">
                                10€
                            </button>
                            <button type="button" onclick="document.querySelector('input[name=item_price]').value = '15.00'" class="bg-blue-100 hover:bg-blue-200 text-blue-800 py-2 rounded font-medium transition-colors text-sm">
                                15€
                            </button>
                            <button type="button" onclick="document.querySelector('input[name=item_price]').value = '25.00'" class="bg-blue-100 hover:bg-blue-200 text-blue-800 py-2 rounded font-medium transition-colors text-sm">
                                25€
                            </button>
                        </div>
                    </div>

                    <!-- Type/Catégorie simplifié -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-folder mr-1"></i>
                            Type d'article
                        </label>
                        <div class="grid grid-cols-3 gap-2">
                            <button type="button" class="border-2 border-gray-200 hover:border-orange-400 rounded-lg p-3 text-center transition-all group">
                                <div class="h-8 w-8 bg-orange-500 rounded-full flex items-center justify-center text-white mx-auto mb-1">
                                    <i class="fas fa-cogs text-sm"></i>
                                </div>
                                <span class="text-xs font-medium">Service</span>
                            </button>
                            <button type="button" class="border-2 border-gray-200 hover:border-green-400 rounded-lg p-3 text-center transition-all group">
                                <div class="h-8 w-8 bg-green-500 rounded-full flex items-center justify-center text-white mx-auto mb-1">
                                    <i class="fas fa-box text-sm"></i>
                                </div>
                                <span class="text-xs font-medium">Produit</span>
                            </button>
                            <button type="button" class="border-2 border-gray-200 hover:border-red-400 rounded-lg p-3 text-center transition-all group">
                                <div class="h-8 w-8 bg-red-500 rounded-full flex items-center justify-center text-white mx-auto mb-1">
                                    <i class="fas fa-exclamation-triangle text-sm"></i>
                                </div>
                                <span class="text-xs font-medium">Abîmé</span>
                            </button>
                        </div>
                    </div>

                    <!-- Templates rapides -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-magic mr-1"></i>
                            Templates fréquents
                        </label>
                        <div class="space-y-2">
                            <button type="button" onclick="fillTemplate('Réparation express', 'Service de réparation minute', '15.00')" class="w-full text-left border rounded p-2 hover:bg-gray-50 transition-colors">
                                <div class="flex items-center">
                                    <i class="fas fa-tools text-orange-500 mr-2"></i>
                                    <div>
                                        <div class="text-sm font-medium">Réparation express</div>
                                        <div class="text-xs text-gray-500">15€ - Service</div>
                                    </div>
                                </div>
                            </button>
                            <button type="button" onclick="fillTemplate('Emballage cadeau', 'Service d\'emballage premium', '3.50')" class="w-full text-left border rounded p-2 hover:bg-gray-50 transition-colors">
                                <div class="flex items-center">
                                    <i class="fas fa-gift text-blue-500 mr-2"></i>
                                    <div>
                                        <div class="text-sm font-medium">Emballage cadeau</div>
                                        <div class="text-xs text-gray-500">3.50€ - Service</div>
                                    </div>
                                </div>
                            </button>
                            <button type="button" onclick="fillTemplate('Article défectueux', 'Vente avec défaut apparent', '0.00')" class="w-full text-left border rounded p-2 hover:bg-gray-50 transition-colors">
                                <div class="flex items-center">
                                    <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                                    <div>
                                        <div class="text-sm font-medium">Article défectueux</div>
                                        <div class="text-xs text-gray-500">Prix à définir - Produit abîmé</div>
                                    </div>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Note d'information -->
                <div class="mt-6 bg-orange-50 border border-orange-200 rounded-lg p-3">
                    <div class="flex">
                        <i class="fas fa-info-circle text-orange-500 mt-0.5 mr-2"></i>
                        <div class="text-sm">
                            <p class="font-medium text-orange-800">Article Z</p>
                            <p class="text-orange-700 mt-1">
                                Article temporaire sans code-barres. Sera tracé dans le système pour les inventaires futurs.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Footer avec actions -->
                <div class="border-t pt-3 mt-3">
                    <button class="w-full bg-orange-500 text-white py-2 rounded hover:bg-orange-600 transition-colors flex items-center justify-center">
                        <i class="fas fa-plus mr-2"></i>
                        Ajouter l'Article Z au panier
                    </button>
                    <p class="text-xs text-gray-500 text-center mt-1">
                        L'article sera ajouté avec un identifiant temporaire
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function fillTemplate(name, description, price) {
        document.querySelector('input[name="item_name"]').value = name;
        document.querySelector('textarea[name="item_description"]').value = description;
        document.querySelector('input[name="item_price"]').value = price;
    }
</script>

<style>
    [x-cloak] { display: none !important; }
</style>
