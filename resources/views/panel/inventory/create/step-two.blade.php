<x-app-layout>
    <div class="lg:py-12">
        <div class="max-w-6xl mx-auto px-0 lg:px-8">
            <!-- Header -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl lg:rounded-lg mb-6">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('inventory.create.step.one.edit', $draftId ?? '') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                        </a>
                        <div>
                            <h1 class="text-xl font-bold text-gray-900 dark:text-gray-50">
                                {{ $draftId ? 'Modifier l\'article' : 'Nouvel article' }}
                            </h1>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Étape 2/2 - Variants et stock</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contenu principal -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl lg:rounded-lg mb-6"
                 x-data="variantManager({
                        draftId: {{ $draftId }},
                        attributes: @js($attributes),
                        existingVariants: @js($existingVariants),
                        tvaRate: {{ $draft->tva ?? 21 }},
                        draft: @js($draft)  // AJOUTER CETTE LIGNE
                    })">

                <form method="POST" action="{{ $formAction }}" id="variants-form">
                    @csrf
                    @if(isset($draft) && $draft->id)
                        @method('PUT')
                    @endif

                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Gestion des Variants</h2>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Créez les différentes déclinaisons de votre produit</p>
                            </div>
                            <button type="button" @click="openModal()" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Créer un variant
                            </button>
                        </div>

                        <!-- Informations article -->
                        @if(isset($draft))
                            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
                                <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-2">Article : {{ $draft->name }}</h3>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                    <div>
                                        <span class="text-blue-600 dark:text-blue-300">Catégorie:</span>
                                        <div class="font-medium">{{ $draft->category->name ?? '-' }}</div>
                                    </div>
                                    <div>
                                        <span class="text-blue-600 dark:text-blue-300">Prix par défaut:</span>
                                        <div class="font-medium">
                                            Achat: {{ $draft->buy_price ? number_format($draft->buy_price, 2) . '€' : '-' }} /
                                            Vente: {{ $draft->sell_price ? number_format($draft->sell_price, 2) . '€' : '-' }}
                                        </div>
                                    </div>
                                    <div>
                                        <span class="text-blue-600 dark:text-blue-300">TVA:</span>
                                        <div class="font-medium">{{ $draft->tva }}%</div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Liste des variants -->
                        <div x-show="variants.length > 0" class="space-y-4">
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg overflow-hidden">
                                <!-- Header du tableau -->
                                <div class="bg-gray-100 dark:bg-gray-600 px-6 py-3 border-b border-gray-200 dark:border-gray-600">
                                    <div class="grid grid-cols-12 gap-4 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        <div class="col-span-4">Variant (Attributs)</div>
                                        <div class="col-span-2">Code-barres</div>
                                        <div class="col-span-2">Prix de vente</div>
                                        <div class="col-span-2">Stock</div>
                                        <div class="col-span-2">Actions</div>
                                    </div>
                                </div>

                                <!-- Corps du tableau -->
                                <div class="divide-y divide-gray-200 dark:divide-gray-600">
                                    <template x-for="variant in variants" :key="variant.id">
                                        <div class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-600/50 transition-colors">
                                            <div class="grid grid-cols-12 gap-4 items-center">
                                                <!-- Attributs -->
                                                <div class="col-span-4">
                                                    <div class="flex items-center space-x-3">
                                                        <!-- Image miniature si disponible -->
                                                        <template x-if="variant.images && variant.images.length > 0">
                                                            <img :src="variant.images[0].url"
                                                                 class="w-10 h-10 rounded-lg object-cover border border-gray-200 dark:border-gray-600"
                                                                 :alt="variant.attributes_display">
                                                        </template>
                                                        <template x-if="!variant.images || variant.images.length === 0">
                                                            <div class="w-10 h-10 rounded-lg bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                                                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                                </svg>
                                                            </div>
                                                        </template>
                                                        <div>
                                                            <div class="font-medium text-gray-900 dark:text-gray-100" x-text="variant.attributes_display"></div>
                                                            <div class="text-sm text-gray-500" x-text="variant.reference || 'Pas de référence'"></div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Code-barres (lecture seule) -->
                                                <div class="col-span-2">
                                                    <span class="font-mono text-sm" x-text="variant.barcode || '-'"
                                                          style="pointer-events:none;"></span>
                                                </div>

                                                <!-- Prix -->
                                                <div class="col-span-2">
                                                    <span class="font-medium"
                                                          x-text="variant.sell_price ? formatPrice(variant.sell_price) : '-'"
                                                          style="pointer-events:none;"></span>
                                                </div>

                                                <!-- Stock -->
                                                <div class="col-span-2">
                                                    <template x-if="variant.stock">
                                                        <div>
                                                            <div class="font-medium"
                                                                 x-text="variant.stock.quantity + ' unité(s)'"
                                                                 style="pointer-events:none;"></div>
                                                            <div class="text-sm text-gray-500"
                                                                 x-text="'Valeur: ' + formatPrice(variant.stock.total_value || 0)"
                                                                 style="pointer-events:none;"></div>
                                                        </div>
                                                    </template>
                                                    <template x-if="!variant.stock">
                                                        <span class="text-gray-400">Pas de stock</span>
                                                    </template>
                                                </div>

                                                <!-- Actions -->
                                                <div class="col-span-2">
                                                    <div class="flex space-x-2">
                                                        <button type="button" @click="editVariant(variant.id)"
                                                                class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-200">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                            </svg>
                                                        </button>
                                                        <button type="button" @click="deleteVariant(variant.id)"
                                                                class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-200">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Message si aucun variant -->
                        <div x-show="variants.length === 0" class="text-center py-12 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Aucun variant</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Commencez par créer votre premier variant.</p>
                            <button type="button" @click="openModal()" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                Créer le premier variant
                            </button>
                        </div>
                    </div>

                    <!-- Boutons de navigation -->
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600">
                        <div class="flex items-center justify-between">
                            <div class="flex space-x-3">
                                <a href="{{ route('inventory.create.step.one.edit', $draftId ?? '') }}"
                                   class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    Étape précédente
                                </a>

                                <button type="submit" name="action" value="save_exit"
                                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    Sauvegarder et quitter
                                </button>
                            </div>

                            <div class="flex space-x-3">
                                <button type="submit" name="action" value="finish"
                                        :disabled="variants.length === 0"
                                        :class="variants.length === 0 ? 'opacity-50 cursor-not-allowed' : ''"
                                        class="px-6 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                                    Finaliser l'article
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Modal pour créer/modifier un variant -->
                @include('panel.inventory.partials.variant-modal')
            </div>
        </div>
    </div>

    <!-- Scripts JavaScript -->
    <script>
        function variantManager(config) {
            return {
                // Configuration
                draftId: config.draftId,
                draft: config.draft,
                availableAttributes: [], // Ajouter cette ligne
                attributeValues: {}, // Ajouter cette ligne
                tvaRate: config.tvaRate,

                // État
                variants: config.existingVariants || [],
                isModalOpen: false,
                isLoading: false,
                editingVariant: null,

                // Modal state
                modalForm: {
                    id: null,
                    attributes: [],
                    barcode: '',
                    reference: '',
                    sell_price: '',
                    buy_price: '',
                    stock: {
                        quantity: '',
                        buy_price: '',
                        lot_reference: '',
                        expiry_date: ''
                    }
                },

                // Images
                previewImages: [], // Ajouter cette ligne
                selectedFiles: [], // Ajouter cette ligne

                // Attribut temporaire pour le formulaire
                newAttribute: {
                    attribute_id: '',
                    attribute_value_id: '',
                    _availableValues: []
                },

                validationErrors: {
                    attributes: { hasError: false, message: '' },
                    barcode: { hasError: false, message: '' },
                    sell_price: { hasError: false, message: '' },
                    buy_price: { hasError: false, message: '' },
                    stock: { hasError: false, message: '' }
                },

                // Initialisation
                async init() {
                    await this.loadAvailableAttributes(); // Ajouter cette ligne
                    this.loadVariants();
                },

                async loadAvailableAttributes() {
                    try {
                        const response = await fetch('/api/attributes/');
                        const data = await response.json();
                        if (response.ok) {
                            this.availableAttributes = data.attributes || data;
                        }
                    } catch (error) {
                        console.error('Erreur lors du chargement des attributs:', error);
                    }
                },

                async loadAttributeValues(attributeId) {
                    if (this.attributeValues[attributeId]) {
                        return this.attributeValues[attributeId];
                    }

                    try {
                        const response = await fetch(`/api/attributes/${attributeId}/values`);
                        const data = await response.json();
                        if (response.ok && data.success) {
                            this.attributeValues[attributeId] = data.values || [];
                            return this.attributeValues[attributeId];
                        }
                    } catch (error) {
                        console.error('Erreur:', error);
                    }
                    return [];
                },

                // AJOUTER ces méthodes pour les attributs
                getAttributeName(attributeId) {
                    const attr = this.availableAttributes.find(a => a.id == attributeId);
                    return attr ? attr.name : '';
                },

                getAttributeValueName(attributeId, valueId) {
                    const values = this.attributeValues[attributeId] || [];
                    const value = values.find(v => v.id == valueId);
                    return value ? value.value : '';
                },

                // AJOUTER les méthodes pour le nouveau système
                async loadAttributeValuesForNew() {
                    const newAttr = this.newAttribute;
                    if (!newAttr.attribute_id) {
                        newAttr.attribute_value_id = '';
                        newAttr._availableValues = [];
                        return;
                    }

                    newAttr.attribute_value_id = '';
                    try {
                        const values = await this.loadAttributeValues(newAttr.attribute_id);
                        newAttr._availableValues = values;
                    } catch (error) {
                        console.error('Erreur:', error);
                        newAttr._availableValues = [];
                    }
                },

                addAttributeFromForm() {
                    const newAttr = this.newAttribute;

                    if (!newAttr.attribute_id || !newAttr.attribute_value_id) {
                        alert('Veuillez sélectionner un type d\'attribut et une valeur');
                        return;
                    }

                    // Vérifier les doublons
                    const exists = this.modalForm.attributes.some(attr =>
                        attr.attribute_id == newAttr.attribute_id &&
                        attr.attribute_value_id == newAttr.attribute_value_id
                    );

                    if (exists) {
                        alert('Cette combinaison d\'attribut est déjà ajoutée');
                        return;
                    }

                    // Ajouter l'attribut
                    this.modalForm.attributes.push({
                        attribute_id: newAttr.attribute_id.toString(),
                        attribute_value_id: newAttr.attribute_value_id.toString(),
                        _availableValues: [...newAttr._availableValues]
                    });

                    // Réinitialiser
                    this.newAttribute = {
                        attribute_id: '',
                        attribute_value_id: '',
                        _availableValues: []
                    };
                },
                calculateMargin() {
                    const sellPriceTvac = parseFloat(this.modalForm.sell_price) || 0;
                    const buyPriceHtva  = parseFloat(this.modalForm.buy_price)
                        || parseFloat(this.modalForm.stock.buy_price) || 0;
                    const tvaRate       = parseFloat(this.tvaRate) || 0;

                    console.log({
                        sellPriceTvac,
                        buyPriceHtva,
                        tvaRate,
                        sellPriceHtva: sellPriceTvac / (1 + tvaRate / 100),
                        marge: (sellPriceTvac / (1 + tvaRate / 100)) - buyPriceHtva
                    });

                    if (sellPriceTvac > 0 && buyPriceHtva > 0) {
                        const sellPriceHtva = sellPriceTvac / (1 + tvaRate / 100);
                        return +(sellPriceHtva - buyPriceHtva).toFixed(2);
                    }
                    return null;
                },

                formatPrice(price) {
                    return new Intl.NumberFormat('fr-BE', {
                        style: 'currency',
                        currency: 'EUR'
                    }).format(price);
                },

                // Gestion du modal
                openModal(variantId = null) {
                    this.editingVariant = variantId;
                    this.resetModalForm();

                    if (!variantId) {
                        // Pour un nouveau variant, utiliser les prix de l'article par défaut
                        this.modalForm.sell_price = config.draft?.sell_price || '';
                        this.modalForm.buy_price = config.draft?.buy_price || '';
                        this.modalForm.stock.buy_price = this.draft?.buy_price || '';
                    }

                    if (variantId) {
                        this.loadVariantForEdit(variantId);
                    }

                    this.isModalOpen = true;
                },

                closeModal() {
                    this.isModalOpen = false;
                    this.editingVariant = null;
                    this.resetModalForm();
                },

                resetModalForm() {
                    this.modalForm = {
                        id: null,
                        attributes: [],
                        barcode: '',
                        reference: '',
                        sell_price: '',
                        buy_price: '',
                        stock: {
                            quantity: '',
                            buy_price: '',
                            lot_reference: '',
                            expiry_date: ''
                        }
                    };
                },

                // Chargement des données
                async loadVariants() {
                    try {
                        const response = await fetch(`/inventory/create/step/2/${this.draftId}/variants`);
                        const data = await response.json();

                        if (data.success) {
                            this.variants = data.variants;
                        }
                    } catch (error) {
                        console.error('Erreur lors du chargement des variants:', error);
                    }
                },

                async loadVariantForEdit(variantId) {
                    try {
                        const response = await fetch(`/inventory/create/step/2/${this.draftId}/variants/${variantId}`);
                        const data = await response.json();

                        if (data.success) {
                            const variant = data.variant;
                            this.modalForm = {
                                id: variant.id,
                                attributes: [], // Garder vide ici
                                barcode: variant.barcode || '',
                                reference: variant.reference || '',
                                sell_price: variant.sell_price || '',
                                buy_price: variant.buy_price || '',
                                stock: variant.stock || {
                                    quantity: '',
                                    buy_price: '',
                                    lot_reference: '',
                                    expiry_date: ''
                                },
                                images: variant.images || [] // <-- AJOUT pour la preview image
                            };

                            // MAINTENANT remplir les attributs
                            if (variant.attributes && variant.attributes.length > 0) {
                                for (const attr of variant.attributes) {
                                    // Charger d'abord les valeurs disponibles
                                    const availableValues = await this.loadAttributeValues(attr.attribute_id);

                                    // Mettre en cache
                                    this.attributeValues[attr.attribute_id] = availableValues;

                                    const attributeData = {
                                        attribute_id: attr.attribute_id.toString(),
                                        attribute_value_id: attr.attribute_value_id.toString(),
                                        _availableValues: availableValues
                                    };

                                    this.modalForm.attributes.push(attributeData);
                                }
                            }
                        }
                    } catch (error) {
                        console.error('Erreur lors du chargement du variant:', error);
                    }
                },

                // Actions CRUD
                async saveVariant() {
                    if (!this.validateForm()) {
                        return;
                    }

                    this.isLoading = true;

                    try {
                        const formData = new FormData();

                        // Données de base
                        if (this.modalForm.id) {
                            formData.append('variant_id', this.modalForm.id);
                        }
                        formData.append('barcode', this.modalForm.barcode);
                        formData.append('reference', this.modalForm.reference);
                        formData.append('sell_price', this.modalForm.sell_price);
                        formData.append('buy_price', this.modalForm.buy_price);

                        // Attributs
                        this.modalForm.attributes.forEach((attr, index) => {
                            formData.append(`attributes[${index}][attribute_value_id]`, attr.attribute_value_id);
                        });

                        // Stock
                        Object.keys(this.modalForm.stock).forEach(key => {
                            if (this.modalForm.stock[key]) {
                                formData.append(`stock[${key}]`, this.modalForm.stock[key]);
                            }
                        });

                        // Images (si présentes)
                        const imageInput = document.getElementById('variant_images');
                        if (imageInput && imageInput.files.length > 0) {
                            Array.from(imageInput.files).forEach(file => {
                                formData.append('images[]', file);
                            });
                        }

                        const response = await fetch(`/inventory/create/step/2/${this.draftId}/variants`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: formData
                        });

                        const data = await response.json();

                        if (data.success) {
                            await this.loadVariants();
                            this.closeModal();
                            this.showSuccess(data.message);
                        } else {
                            this.showError(data.message);
                        }
                    } catch (error) {
                        console.error('Erreur lors de la sauvegarde:', error);
                        this.showError('Erreur lors de la sauvegarde du variant');
                    } finally {
                        this.isLoading = false;
                    }
                },

                async deleteVariant(variantId) {
                    if (!confirm('Êtes-vous sûr de vouloir supprimer ce variant ?')) {
                        return;
                    }

                    try {
                        const response = await fetch(`/inventory/create/step/2/${this.draftId}/variants/${variantId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            await this.loadVariants();
                            this.showSuccess(data.message);
                        } else {
                            this.showError(data.message);
                        }
                    } catch (error) {
                        console.error('Erreur lors de la suppression:', error);
                        this.showError('Erreur lors de la suppression du variant');
                    }
                },

                editVariant(variantId) {
                    this.openModal(variantId);
                },

                // Gestion des attributs dans le modal
                addAttribute() {
                    this.modalForm.attributes.push({
                        attribute_id: '',
                        value: '',
                        second_value: ''
                    });
                },

                removeAttribute(index) {
                    this.modalForm.attributes.splice(index, 1);
                },

                // Validation
                async validateForm() {
                    // Reset des erreurs
                    this.validationErrors = {
                        attributes: { hasError: false, message: '' },
                        barcode: { hasError: false, message: '' },
                        sell_price: { hasError: false, message: '' },
                        buy_price: { hasError: false, message: '' },
                        stock: { hasError: false, message: '' }
                    };

                    let hasErrors = false;

                    // Vérifier les attributs
                    if (this.modalForm.attributes.length === 0) {
                        this.validationErrors.attributes = { hasError: true, message: 'Au moins un attribut est requis' };
                        hasErrors = true;
                    }

                    // Vérifier le prix de vente
                    if (!this.modalForm.sell_price || parseFloat(this.modalForm.sell_price) <= 0) {
                        this.validationErrors.sell_price = { hasError: true, message: 'Le prix de vente est obligatoire et doit être positif' };
                        hasErrors = true;
                    }

                    // Vérifier le prix d'achat
                    if (!this.modalForm.buy_price || parseFloat(this.modalForm.buy_price) <= 0) {
                        this.validationErrors.buy_price = { hasError: true, message: 'Le prix d\'achat est obligatoire et doit être positif' };
                        hasErrors = true;
                    }

                    return !hasErrors;
                },

                getFieldClasses(fieldName) {
                    return this.validationErrors[fieldName].hasError
                        ? 'border-red-500 dark:border-red-400 focus:ring-red-500'
                        : 'border-gray-300 dark:border-gray-600 focus:ring-blue-500';
                },

                calculatePriceHT(priceTTC) {
                    return priceTTC / (1 + this.tvaRate / 100);
                },

                calculateStockValue() {
                    const quantity = parseFloat(this.modalForm.stock.quantity) || 0;
                    const buyPrice = parseFloat(this.modalForm.stock.buy_price) || 0;
                    return quantity * buyPrice;
                },

                // Notifications
                showSuccess(message) {
                    // Vous pouvez implémenter votre système de notification ici
                    console.log('Success:', message);
                },

                showError(message) {
                    // Vous pouvez implémenter votre système de notification ici
                    console.error('Error:', message);
                }
            };
        }
    </script>
</x-app-layout>
