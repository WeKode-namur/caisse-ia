<!-- Modal pour créer/modifier un variant -->
<div x-show="isModalOpen"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;"
     @keydown.escape="closeModal()">

    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
             @click="closeModal()" aria-hidden="true"></div>

        <!-- Modal centré -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div x-show="isModalOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-6xl sm:w-full"
             @click.stop>

            <!-- Header du modal -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 border-b border-gray-200 dark:border-gray-600">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-white" x-text="editingVariant ? 'Modifier le variant' : 'Créer un variant'"></h3>
                            <p class="text-blue-100 text-sm">Définissez les caractéristiques spécifiques de ce variant</p>
                        </div>
                    </div>
                    <button type="button" @click="closeModal()"
                            class="text-white hover:text-blue-200 transition-colors focus:outline-none focus:ring-2 focus:ring-white focus:ring-opacity-50 rounded">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Contenu du modal -->
            <div class="bg-white dark:bg-gray-800 px-6 py-6">
                <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">

                    <!-- Section 1: Attributs et Identification -->
                    <div class="space-y-6">
                        <!-- Attributs du variant -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4" :class="validationErrors.attributes.hasError ? 'ring-2 ring-red-500' : ''">
                            <div class="flex items-center space-x-2 mb-4">
                                <i class="fas fa-tags text-lg text-blue-600"></i>
                                <h4 class="font-semibold text-gray-900 dark:text-gray-100">Attributs du variant</h4>
                            </div>

                            <!-- Formulaire d'ajout d'attribut -->
                            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-600 mb-4"
                                 x-data="{
                                         newAttribute: {
                                             attribute_id: '',
                                             attribute_value_id: '',
                                             _availableValues: []
                                         }
                                     }">

                                <div class="lg:flex gap-3 items-end">
                                    <!-- Sélection du type d'attribut -->
                                    <div class="lg:mb-0 mb-3">
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Type d'attribut
                                        </label>
                                        <select x-model="newAttribute.attribute_id"
                                                @change="loadAttributeValuesForNew()"
                                                class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                            <option value="">Choisir un type...</option>
                                            <template x-for="attribute in availableAttributes" :key="attribute.id">
                                                <option :value="attribute.id" x-text="attribute.name"></option>
                                            </template>
                                        </select>
                                    </div>

                                    <!-- Sélection de la valeur d'attribut -->
                                    <div class="lg:mb-0 mb-3">
                                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Valeur
                                        </label>
                                        <select x-model="newAttribute.attribute_value_id"
                                                :disabled="!newAttribute.attribute_id || newAttribute._availableValues.length === 0"
                                                class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed">
                                            <option value="">Choisir une valeur...</option>
                                            <template x-for="value in newAttribute._availableValues" :key="value.id">
                                                <option :value="value.id" x-text="value.value + (value.secondValue ? ' - ' + value.secondValue : '')"></option>
                                            </template>
                                        </select>

                                        <!-- Message de chargement -->
                                        <div x-show="newAttribute.attribute_id && newAttribute._availableValues.length === 0"
                                             class="text-xs text-gray-500 italic mt-1">
                                            Chargement des valeurs...
                                        </div>
                                    </div>

                                    <!-- Bouton d'ajout -->
                                    <div>
                                        <button type="button"
                                                @click="addAttributeFromForm()"
                                                :disabled="!newAttribute.attribute_id || !newAttribute.attribute_value_id"
                                                class="w-full px-4 py-1.5 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                                            <i class="fas fa-plus sm:mr-0 mr-2"></i>
                                            <span class="sm:hidden inline w-full">Ajouter</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <p x-show="validationErrors.attributes.hasError"
                               x-text="validationErrors.attributes.message"
                               class="text-red-600 dark:text-red-400 text-sm mt-2 font-medium"></p>

                            <!-- Liste des attributs sélectionnés (étiquettes) -->
                            <div class="space-y-2">
                                <!-- Message si aucun attribut -->
                                <div x-show="modalForm.attributes.length === 0"
                                     class="text-center py-4 text-gray-500 text-sm bg-gray-100 dark:bg-gray-600 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-500">
                                    <i class="fas fa-tags text-2xl mb-2 text-gray-400"></i>
                                    <div>Aucun attribut ajouté.</div>
                                    <div class="text-xs">Utilisez le formulaire ci-dessus pour ajouter des attributs.</div>
                                </div>

                                <!-- Étiquettes des attributs sélectionnés -->
                                <div x-show="modalForm.attributes.length > 0" class="flex flex-wrap gap-2">
                                    <template x-for="(attr, index) in modalForm.attributes" :key="index">
                                        <div class="inline-flex w-full items-center bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-200 text-sm px-3 py-2 rounded-lg border border-blue-200 dark:border-blue-700">
                                            <!-- Icône d'attribut -->
                                            <i class="fas fa-hashtag mr-2 text-gray-400 dark:text-gray-600"></i>

                                            <!-- Nom de l'attribut et valeur -->
                                            <span class="font-medium" x-text="getAttributeName(attr.attribute_id)"></span>
                                            <span class="mx-1">:</span>
                                            <span x-text="getAttributeValueName(attr.attribute_id, attr.attribute_value_id)"></span>

                                            <!-- Bouton de suppression -->
                                            <button type="button"
                                                    @click="removeAttribute(index)"
                                                    class="ml-auto p-1 hover:bg-red-100 w-6 h-6 dark:hover:bg-red-900/50 text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-200 rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-red-500">
                                                    <i class="fas fa-xmark"></i>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <!-- Aide sur les attributs -->
                            <div class="mt-4 p-3 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-200 dark:border-amber-800">
                                <div class="flex items-start space-x-2">
                                    <i class="far fa-lightbulb text-amber-600 mt-0.5 flex-shrink-0"></i>
                                    <div class="text-xs text-amber-700 dark:text-amber-300">
                                        <p class="font-medium mb-1">Système d'attributs :</p>
                                        <ul class="space-y-1">
                                            <li>• <strong>Type</strong> : Couleur, Taille, Matière, etc.</li>
                                            <li>• <strong>Valeur</strong> : Rouge, XL, Coton, etc.</li>
                                            <li>• <strong>Au moins un attribut</strong> est requis pour créer un variant</li>
                                            <li>• Chaque combinaison d'attributs crée un variant unique</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Section 2: Prix et Stock -->
                    <div class="space-y-6">
                        <!-- Prix -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <div class="flex items-center space-x-2 mb-4">
                                <i class="fas fa-euro-sign text-lg text-yellow-600"></i>
                                <h4 class="font-semibold text-gray-900 dark:text-gray-100">Tarification</h4>
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Prix de vente (TVAC)</label>
                                    <div class="relative">
                                        <input type="number" x-model="modalForm.sell_price" step="0.01" min="0"
                                               :class="getFieldClasses('sell_price')"
                                               @input="updateCalculations()"
                                               class="w-full px-3 py-2 pr-8 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                               placeholder="0.00">
                                        <span class="absolute right-3 top-2 text-gray-500 font-medium">€</span>
                                    </div>
                                    <p x-show="validationErrors.sell_price.hasError"
                                       x-text="validationErrors.sell_price.message"
                                       class="text-red-600 dark:text-red-400 text-xs mt-1"></p>
                                </div>

                                <!-- Calculs automatiques -->
                                <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded border border-blue-200 dark:border-blue-800">
                                    <h5 class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-2">Calculs automatiques</h5>
                                    <div class="grid grid-cols-2 gap-3 text-xs">
                                        <div>
                                            <span class="text-blue-600 dark:text-blue-300">Prix HTVA:</span>
                                            <div class="font-medium" x-text="modalForm.sell_price ? formatPrice(calculatePriceHT(modalForm.sell_price)) : '-'"></div>
                                        </div>
                                        <div>
                                            <span class="text-blue-600 dark:text-blue-300">Marge:</span>
                                            <div class="font-medium" x-text="calculateMargin() !== null ? formatPrice(calculateMargin()) : '-'"></div>
                                        </div>
                                    </div>
                                    <p class="text-xs text-blue-600 dark:text-blue-400 mt-2">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        TVA : <span x-text="tvaRate + '%'"></span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Stock -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <div class="flex items-center space-x-2 mb-4">
                                <i class="fas fa-cube text-lg text-purple-600"></i>
                                <h4 class="font-semibold text-gray-900 dark:text-gray-100">Stock initial</h4>
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Prix d'achat (HTVA)
                                        <span class="text-xs text-gray-500">- Prix payé pour ce stock</span>
                                    </label>
                                    <div class="relative">
                                        <input type="number" x-model="modalForm.stock.buy_price" step="0.01" min="0"
                                               @input="updateCalculations()"
                                               class="w-full px-3 py-2 pr-8 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                               placeholder="0.00">
                                        <span class="absolute right-3 top-2 text-gray-500 font-medium">€</span>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Quantité</label>
                                    <input type="number" x-model="modalForm.stock.quantity" min="0"
                                           @input="updateCalculations()"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                           placeholder="0">
                                </div>
                                <template x-if="$store.config && $store.config.referent_lot_optionnel">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Référent
                                            lot</label>
                                        <input type="text" x-model="modalForm.stock.lot_reference"
                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                               placeholder="ACHAT-2024-001">
                                    </div>
                                </template>
                                <template x-if="$store.config && $store.config.date_expiration_optionnel">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date
                                            d'expiration</label>
                                        <input type="date" x-model="modalForm.stock.expiry_date"
                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                        <p class="text-xs text-gray-500 mt-1">Optionnel - Pour les produits
                                            périssables</p>
                                    </div>
                                </template>
                                <!-- Valeur totale du stock -->
                                <div class="p-3 bg-purple-50 dark:bg-purple-900/20 rounded border border-purple-200 dark:border-purple-800">
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-purple-600 dark:text-purple-300 font-medium">Valeur totale du stock:</span>
                                        <span class="font-bold text-purple-800 dark:text-purple-200" x-text="formatPrice(calculateStockValue())"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Images -->
                    <div class="space-y-6">

                        <!-- Codes et références -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <div class="flex items-center space-x-2 mb-4">
                                <i class="far fa-file-lines text-lg text-green-600"></i>
                                <h4 class="font-semibold text-gray-900 dark:text-gray-100">Identification</h4>
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        <i class="fas fa-tag inline mr-1"></i>
                                        Référence
                                    </label>
                                    <input type="text" x-model="modalForm.reference"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                           placeholder="REF-001">
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4"
                             :class="imageError ? 'ring-2 ring-red-500' : ''"
                             x-data="{
                                imageLoading: false,
                                variantImageUrl: (modalForm.images && modalForm.images.length > 0) ? modalForm.images[0].url : null,
                                imageError: '',
                                syncImage() {
                                    this.variantImageUrl = (modalForm.images && modalForm.images.length > 0) ? modalForm.images[0].url : null;
                                },
                                uploadVariantImage(event) {
                                    this.imageError = '';
                                    const file = event.target.files[0];
                                    if (!file) return;
                                    // Vérification côté client
                                    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                                    if (!allowedTypes.includes(file.type)) {
                                        this.imageError = 'Format d\'image non supporté. Formats acceptés : JPG, PNG, GIF, WEBP.';
                                        return;
                                    }
                                    if (file.size > 25 * 1024 * 1024) {
                                        this.imageError = 'L\'image dépasse la taille maximale autorisée (25 Mo).';
                                        return;
                                    }
                                    // Déterminer la limite de compression selon la taille
                                    let compressLimit = null;
                                    if (file.size > 1 * 1024 * 1024 && file.size <= 15 * 1024 * 1024) {
                                        compressLimit = 2.5 * 1024 * 1024; // 2,5 Mo
                                    } else if (file.size > 15 * 1024 * 1024 && file.size <= 25 * 1024 * 1024) {
                                        compressLimit = 4 * 1024 * 1024; // 4 Mo
                                    }
                                    // Compression si nécessaire (JPEG/PNG uniquement)
                                    const compressIfNeeded = (file) => {
                                        return new Promise((resolve, reject) => {
                                            if ((file.type === 'image/jpeg' || file.type === 'image/png') && compressLimit && file.size > compressLimit) {
                                                const img = new Image();
                                                const reader = new FileReader();
                                                reader.onload = (e) => {
                                                    img.onload = () => {
                                                        const canvas = document.createElement('canvas');
                                                        canvas.width = img.width;
                                                        canvas.height = img.height;
                                                        const ctx = canvas.getContext('2d');
                                                        ctx.drawImage(img, 0, 0);
                                                        let quality = 0.92;
                                                        let blobFn = (cb) => canvas.toBlob(cb, file.type, quality);
                                                        const tryCompress = () => {
                                                            blobFn((blob) => {
                                                                if (blob.size <= compressLimit || quality < 0.5) {
                                                                    resolve(new File([blob], file.name, {type: file.type}));
                                                                } else {
                                                                    quality -= 0.07;
                                                                    tryCompress();
                                                                }
                                                            });
                                                        };
                                                        tryCompress();
                                                    };
                                                    img.onerror = () => reject('Erreur lors de la lecture de l\'image.');
                                                    img.src = e.target.result;
                                                };
                                                reader.onerror = () => reject('Erreur lors de la lecture du fichier.');
                                                reader.readAsDataURL(file);
                                            } else {
                                                resolve(file);
                                            }
                                        });
                                    };
                                    this.imageLoading = true;
                                    compressIfNeeded(file).then((finalFile) => {
                                        if (compressLimit && finalFile.size > compressLimit) {
                                            this.imageError = `Impossible de compresser l'image sous ${compressLimit / 1024 / 1024} Mo.`;
                                            this.imageLoading = false;
                                            return;
                                        }
                                        const formData = new FormData();
                                        formData.append('image', finalFile);
                                        fetch(`/inventory/create/step/2/${draftId}/variants/${modalForm.id}/image`, {
                                            method: 'POST',
                                            headers: {
                                                'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content')
                                            },
                                            body: formData
                                        })
                                        .then(response => response.json())
                                        .then(data => {
                                            if (data.success && data.url) {
                                                this.variantImageUrl = data.url;
                                                this.imageError = '';
                                            } else if (data.errors && data.errors.image && data.errors.image[0]) {
                                                this.imageError = data.errors.image[0];
                                            } else {
                                                this.imageError = 'Erreur lors de l\'upload de l\'image.';
                                            }
                                        })
                                        .catch(() => { this.imageError = 'Erreur lors de l\'upload de l\'image.'; })
                                        .finally(() => { this.imageLoading = false; });
                                    }).catch((err) => {
                                        this.imageError = typeof err === 'string' ? err : 'Erreur lors de la compression de l\'image.';
                                        this.imageLoading = false;
                                    });
                                },
                                deleteVariantImage() {
                                    this.imageError = '';
                                    if (!modalForm.id) return;
                                    this.imageLoading = true;
                                    fetch(`/inventory/create/step/2/${draftId}/variants/${modalForm.id}/image`, {
                                        method: 'DELETE',
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content')
                                        }
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            this.variantImageUrl = null;
                                            this.imageError = '';
                                        } else {
                                            this.imageError = 'Erreur lors de la suppression de l\'image.';
                                        }
                                    })
                                    .catch(() => { this.imageError = 'Erreur lors de la suppression de l\'image.'; })
                                    .finally(() => { this.imageLoading = false; });
                                }
                             }"
                             x-effect="syncImage()">
                            <div class="flex items-center space-x-2 mb-4">
                                <i class="far fa-image text-lg text-indigo-600"></i>
                                <h4 class="font-semibold text-gray-900 dark:text-gray-100">Photo du variant</h4>
                            </div>
                            <div class="text-center">
                                <template x-if="imageLoading">
                                    <div class="flex flex-col items-center justify-center py-6">
                                        <svg class="animate-spin h-8 w-8 text-blue-600 mb-2" fill="none"
                                             viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                    stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span class="text-blue-600">Chargement de l'image...</span>
                                    </div>
                                </template>
                                <template x-if="!imageLoading && variantImageUrl">
                                    <div class="relative inline-block">
                                        <img :src="variantImageUrl"
                                             class="w-32 h-32 object-cover rounded-lg border border-gray-200 dark:border-gray-600 mx-auto">
                                        <button type="button" @click="deleteVariantImage()"
                                                class="absolute top-1 right-1 bg-red-600 text-white rounded-full w-7 h-7 flex items-center justify-center shadow hover:bg-red-700 focus:outline-none">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </template>
                                <template x-if="!imageLoading && !variantImageUrl">
                                    <div class="flex flex-col items-center justify-center py-6">
                                        <input type="file" accept="image/*" @change="uploadVariantImage($event)"
                                               class="block mx-auto text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"/>
                                        <span class="text-xs text-gray-500 mt-2">PNG, JPG, GIF jusqu'à 2MB</span>
                                    </div>
                                </template>
                            </div>
                            <template x-if="imageError">
                                <div class="mt-2 text-red-600 text-sm font-semibold"><i
                                        class="fas fa-exclamation-circle mr-1"></i> <span x-text="imageError"></span>
                                </div>
                            </template>
                            <div
                                class="mt-4 p-3 bg-amber-50 dark:bg-amber-900/20 rounded-lg border border-amber-200 dark:border-amber-800">
                                <div class="flex items-start space-x-2">
                                    <i class="far fa-lightbulb text-amber-600 mt-0.5 flex-shrink-0"></i>
                                    <div class="text-xs text-amber-700 dark:text-amber-300">
                                        <p class="font-medium mb-1">Photo pour la caisse :</p>
                                        <ul class="space-y-1">
                                            <li>• <strong>Fond neutre</strong> : blanc ou transparent</li>
                                            <li>• <strong>Article centré</strong> et bien visible</li>
                                            <li>• <strong>Taille optimale</strong> : 300x300px minimum</li>
                                            <li>• <strong>Format carré</strong> recommandé</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer du modal -->
            <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 flex items-center justify-between border-t border-gray-200 dark:border-gray-600">
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Seuls les attributs sont requis
                </div>
                <div class="flex space-x-3">
                    <button type="button" @click="closeModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        Annuler
                    </button>
                    <button type="button" @click="saveVariant()"
                            :disabled="isLoading"
                            :class="isLoading ? 'opacity-50 cursor-not-allowed' : ''"
                            class="px-6 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors shadow-sm">
                        <!-- Spinner de chargement -->
                        <svg x-show="isLoading" class="animate-spin -ml-1 mr-3 h-4 w-4 text-white inline" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <!-- Icône de confirmation -->
                        <svg x-show="!isLoading" class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span x-text="editingVariant ? 'Modifier le variant' : 'Sauvegarder le variant'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Injection de la config PHP dans le JS global pour Alpine
    window.variantConfig = {
        referent_lot_optionnel: @json(config('custom.referent_lot_optionnel', true)),
        date_expiration_optionnel: @json(config('custom.date_expiration_optionnel', true)),
    };
    // ...
    // Dans l'init Alpine/variantManager, s'assurer que $store.config existe :
    document.addEventListener('alpine:init', () => {
        if (!Alpine.store('config')) {
            Alpine.store('config', window.variantConfig);
        }
    });
    // ... existing code ...
</script>
