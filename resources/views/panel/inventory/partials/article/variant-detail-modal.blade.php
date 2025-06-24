<!-- Modal détail du variant -->
<x-modal name="variant-detail"
         title="Détail du variant"
         size="4xl"
         icon="tag"
         icon-color="blue">

    <div x-data="{ activeTab: 'info' }" class="space-y-6">
        <!-- En-tête avec informations principales -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 p-4 rounded-lg border border-blue-200 dark:border-blue-800">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100" id="variant-modal-name">
                        Variant
                    </h3>
                    <div class="flex flex-wrap gap-2 mt-2" id="variant-modal-attributes">
                        <!-- Attributs dynamiques -->
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-600 dark:text-gray-400">Stock actuel</div>
                    <div class="text-2xl font-bold" id="variant-modal-stock-display">
                        0
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation par onglets -->
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="-mb-px flex space-x-8">
                <button @click="activeTab = 'info'"
                        :class="{ 'border-blue-500 text-blue-600 dark:text-blue-400': activeTab === 'info', 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300': activeTab !== 'info' }"
                        class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors">
                    <i class="fas fa-info-circle mr-2"></i>
                    Informations
                </button>
                <button @click="activeTab = 'stock'"
                        :class="{ 'border-blue-500 text-blue-600 dark:text-blue-400': activeTab === 'stock', 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300': activeTab !== 'stock' }"
                        class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors">
                    <i class="fas fa-boxes mr-2"></i>
                    Stock & Prix
                </button>
                <button @click="activeTab = 'images'"
                        :class="{ 'border-blue-500 text-blue-600 dark:text-blue-400': activeTab === 'images', 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300': activeTab !== 'images' }"
                        class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors">
                    <i class="fas fa-image mr-2"></i>
                    Images
                </button>
{{--                <button @click="activeTab = 'history'"--}}
{{--                        :class="{ 'border-blue-500 text-blue-600 dark:text-blue-400': activeTab === 'history', 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300': activeTab !== 'history' }"--}}
{{--                        class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors">--}}
{{--                    <i class="fas fa-history mr-2"></i>--}}
{{--                    Historique--}}
{{--                </button>--}}
                <div class="text-gray-300 dark:text-gray-600 cursor-not-allowed font-medium text-sm py-2 px-1 border-b-2 border-transparent whitespace-nowrap">
                    <i class="fas fa-history mr-2"></i>
                    Historique
                </div>
            </nav>
        </div>

        <!-- Contenu des onglets -->
        <div class="mt-4">
            <!-- Onglet Informations -->
            <div x-show="activeTab === 'info'" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-400 font-semibold">Référence</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100" id="variant-modal-reference">-</p>
                        </div>
                        <div>
                            <div class="block text-sm text-gray-600 dark:text-gray-400 font-semibold">
                                Code-barres
                                <button id="copy-barcode-btn"
                                        onclick="copyBarcode()"
                                        class="ms-2 text-blue-500 hover:opacity-75 hover:scale-105 duration-500 px-0.5 hidden"
                                        title="Copier le code-barres">
                                    <i class="far fa-clipboard"></i>
                                </button>
                            </div>
                            <p class="mt-1 text-sm font-mono text-gray-900 dark:text-gray-100" id="variant-modal-barcode">-</p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-400 font-semibold">Date de création</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100" id="variant-modal-created">-</p>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 dark:text-gray-400 font-semibold">Dernière modification</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-gray-100" id="variant-modal-updated">-</p>
                        </div>
                    </div>
                </div>

                <!-- Attributs détaillés -->
                <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-3">Attributs du variant</h4>
                    <div id="variant-modal-attributes-detail" class="space-y-2">
                        <!-- Attributs détaillés -->
                    </div>
                </div>
            </div>

            <!-- Onglet Stock & Prix -->
            <div x-show="activeTab === 'stock'" class="space-y-6" style="display: none;">
                <!-- Informations de prix -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400" id="variant-modal-buy-price">
                            0.00€
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Prix d'achat</div>
                    </div>
                    <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400" id="variant-modal-sell-price">
                            0.00€
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Prix de vente</div>
                    </div>
                    <div class="text-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                        <div class="text-2xl font-bold text-purple-600 dark:text-purple-400" id="variant-modal-margin">
                            0.00€
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Marge unitaire</div>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="text-center p-4 bg-amber-50 dark:bg-amber-900/20 rounded-lg">
                        <div class="text-2xl font-bold text-amber-600 dark:text-amber-400" id="variant-modal-stock">
                            0
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Quantité</div>
                    </div>
                    <div class="text-center p-4 bg-gray-50 dark:bg-gray-900/20 rounded-lg">
                        <div class="text-2xl font-bold text-gray-600 dark:text-gray-400" id="variant-modal-value">
                            0.00€
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Valeur du stock</div>
                    </div>
                </div>
            </div>

            <!-- Onglet Images -->
            <div x-show="activeTab === 'images'" class="space-y-4" style="display: none;">
                <div id="variant-images-grid" class="grid grid-cols-2 md:grid-cols-4 gap-4"></div>
                <div id="variant-images-empty" class="text-center text-gray-500 dark:text-gray-400 hidden">
                    <i class="fas fa-image text-3xl mb-2"></i>
                    <div>Aucune image pour ce variant</div>
                </div>
            </div>

            <!-- Onglet Historique -->
            <div x-show="activeTab === 'history'" class="space-y-4" style="display: none;">
                <div class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-3">Derniers mouvements</h4>
                    <div id="variant-modal-history" class="space-y-2">
                        <div class="text-center py-4 text-gray-500 dark:text-gray-400">
                            <i class="fas fa-spinner fa-spin mb-2"></i>
                            <div>Chargement de l'historique...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-slot:actions>
{{--        <button onclick="quickAdjustStock()" class="bg-green-600 dark:bg-green-700 text-white px-3 py-2 rounded-md hover:opacity-75 hover:scale-105 duration-500">--}}
{{--            <i class="fas fa-plus mr-2"></i>--}}
{{--            Ajuster stock--}}
{{--        </button>--}}
{{--        <button onclick="quickEdit()" class="bg-blue-600 dark:bg-blue-700 text-white px-3 py-2 rounded-md hover:opacity-75 hover:scale-105 duration-500">--}}
{{--            <i class="fas fa-edit mr-2"></i>--}}
{{--            Modifier--}}
{{--        </button>--}}
    </x-slot:actions>
</x-modal>

<!-- Modal image plein écran pour images du variant -->
<div id="variant-image-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/90"
     style="backdrop-filter: blur(2px);">
    <button onclick="closeVariantImageModal()"
            class="absolute top-6 right-8 text-white text-3xl focus:outline-none z-60" title="Fermer">
        <i class="fas fa-times"></i>
    </button>
    <img id="variant-image-modal-img" src="" alt="Image variant"
         class="max-h-[80vh] max-w-[90vw] rounded-lg shadow-2xl border-4 border-white/20"/>
</div>

<script>
    let currentVariantData = null;

    function openVariantModal(variantId, data) {
        currentVariantData = { id: variantId, ...data };

        // Remplir les informations
        document.getElementById('variant-modal-buy-price').textContent = '€' + parseFloat(data.buy_price).toFixed(2);
        document.getElementById('variant-modal-sell-price').textContent = '€' + parseFloat(data.sell_price).toFixed(2);
        document.getElementById('variant-modal-value').textContent = '€' + parseFloat(data.value).toFixed(2);
        document.getElementById('variant-modal-stock-display').textContent = data.stock;
        document.getElementById('variant-modal-stock').textContent = data.stock;
        document.getElementById('variant-modal-created').textContent = data.created_at || 'Non disponible';
        document.getElementById('variant-modal-updated').textContent = data.updated_at || 'Non disponible';
        document.getElementById('variant-modal-reference').textContent = data.reference || 'Non définie';
        document.getElementById('variant-modal-barcode').textContent = data.barcode || 'Non défini';

        // Afficher/masquer le bouton de copie selon si il y a un code-barres
        const copyBtn = document.getElementById('copy-barcode-btn');
        if (data.barcode && data.barcode.trim() !== '') {
            copyBtn.classList.remove('hidden');
        } else {
            copyBtn.classList.add('hidden');
        }

        // Calculer et afficher la marge
        const margin = data.sell_price - data.buy_price;
        document.getElementById('variant-modal-margin').textContent = '€' + margin.toFixed(2);

        // Couleur du stock selon le niveau
        const stockDisplay = document.getElementById('variant-modal-stock-display');
        if (data.stock === 0) {
            stockDisplay.className = 'text-2xl font-bold text-red-600 dark:text-red-400';
        } else if (data.stock <= 5) {
            stockDisplay.className = 'text-2xl font-bold text-orange-600 dark:text-orange-400';
        } else {
            stockDisplay.className = 'text-2xl font-bold text-green-600 dark:text-green-400';
        }

        // Afficher les attributs
        const attributesContainer = document.getElementById('variant-modal-attributes');
        const attributesDetailContainer = document.getElementById('variant-modal-attributes-detail');

        attributesContainer.innerHTML = '';
        attributesDetailContainer.innerHTML = '';

        if (data.attributes && data.attributes.length > 0) {
            data.attributes.forEach((attr, index) => {
                // Badge dans l'en-tête
                const badge = document.createElement('span');
                badge.className = `inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${getBadgeColor(index)}`;
                badge.textContent = attr;
                attributesContainer.appendChild(badge);

                // Détail dans l'onglet info
                const detail = document.createElement('div');
                detail.className = 'flex items-center space-x-2';
                detail.innerHTML = `
                <span class="w-2 h-2 rounded-full ${getBadgeColorDot(index)}"></span>
                <span class="text-sm text-gray-900 dark:text-gray-100">${attr}</span>
            `;
                attributesDetailContainer.appendChild(detail);
            });
        } else {
            attributesContainer.innerHTML = '<span class="text-xs text-gray-500 dark:text-gray-400">Aucun attribut</span>';
            attributesDetailContainer.innerHTML = '<span class="text-sm text-gray-500 dark:text-gray-400">Aucun attribut défini pour ce variant</span>';
        }
        loadVariantHistory(variantId);
        loadVariantImages(variantId);
        openModal('variant-detail');
    }
    function copyBarcode() {
        const barcode = document.getElementById('variant-modal-barcode').textContent;
        if (barcode && barcode !== 'Non défini') {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(barcode).then(() => {
                    showCopySuccess();
                }).catch(err => {
                    console.error('Erreur clipboard API:', err);
                    fallbackCopy(barcode);
                });
            } else {
                fallbackCopy(barcode);
            }
        }
    }

    function fallbackCopy(text) {
        const tempInput = document.createElement('input');
        tempInput.style.position = 'absolute';
        tempInput.style.left = '-9999px';
        tempInput.value = text;
        document.body.appendChild(tempInput);
        tempInput.select();
        tempInput.setSelectionRange(0, 99999);
        try {
            const successful = document.execCommand('copy');
            if (successful) {
                showCopySuccess();
            } else {
                throw new Error('execCommand failed');
            }
        } catch (err) {
            console.error('Erreur lors de la copie:', err);
            alert('Impossible de copier automatiquement. Code-barres: ' + text);
        } finally {
            document.body.removeChild(tempInput);
        }
    }

    function showCopySuccess() {
        const btn = document.getElementById('copy-barcode-btn');
        const icon = btn.querySelector('i');
        icon.className = 'fas fa-check text-green-500';
        setTimeout(() => {
            icon.className = 'far fa-clipboard';
        }, 2000);

        console.log('Code-barres copié !');
    }

    function getBadgeColor(index) {
        const colors = [
            'bg-purple-50 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
            'bg-pink-50 text-pink-800 dark:bg-pink-900 dark:text-pink-200',
            'bg-green-50 text-green-800 dark:bg-green-900 dark:text-green-200',
            'bg-orange-50 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
            'bg-blue-50 text-blue-800 dark:bg-blue-900 dark:text-blue-200'
        ];
        return colors[index % colors.length];
    }

    function getBadgeColorDot(index) {
        const colors = [
            'bg-purple-500',
            'bg-pink-500',
            'bg-green-500',
            'bg-orange-500',
            'bg-blue-500'
        ];
        return colors[index % colors.length];
    }

    function loadVariantHistory(variantId) {
        const historyContainer = document.getElementById('variant-modal-history');

        // Simuler le chargement de l'historique avec ta nouvelle URL
        fetch(`/inventory/variants/${variantId}/history`)
            .then(response => response.json())
            .then(data => {
                historyContainer.innerHTML = '';

                if (data.movements && data.movements.length > 0) {
                    data.movements.forEach(movement => {
                        const movementElement = document.createElement('div');
                        movementElement.className = 'flex items-center justify-between p-3 bg-white dark:bg-gray-600 rounded border';

                        const typeIcon = getMovementIcon(movement.type);
                        const typeColor = getMovementColor(movement.type);
                        const quantityClass = movement.quantity > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400';

                        movementElement.innerHTML = `
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center ${typeColor}">
                                <i class="fas fa-${typeIcon} text-xs"></i>
                            </div>
                            <div>
                                <div class="font-medium text-sm text-gray-900 dark:text-gray-100">${movement.type}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">${movement.reason || 'Aucune raison'}</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-medium text-sm ${quantityClass}">
                                ${movement.quantity > 0 ? '+' : ''}${movement.quantity}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">${movement.date}</div>
                        </div>
                    `;

                        historyContainer.appendChild(movementElement);
                    });
                } else {
                    historyContainer.innerHTML = `
                    <div class="text-center py-4 text-gray-500 dark:text-gray-400">
                        <i class="fas fa-history text-2xl mb-2"></i>
                        <div>Aucun mouvement enregistré</div>
                    </div>
                `;
                }
            })
            .catch(error => {
                console.error('Erreur lors du chargement de l\'historique:', error);
                historyContainer.innerHTML = `
                <div class="text-center py-4 text-red-500">
                    <i class="fas fa-exclamation-triangle text-2xl mb-2"></i>
                    <div>Erreur lors du chargement de l'historique</div>
                </div>
            `;
            });
    }

    function getMovementIcon(type) {
        const icons = {
            'entrée': 'arrow-up',
            'sortie': 'arrow-down',
            'vente': 'shopping-cart',
            'ajustement': 'edit',
            'retour': 'undo'
        };
        return icons[type] || 'exchange-alt';
    }

    function getMovementColor(type) {
        const colors = {
            'entrée': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            'sortie': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
            'vente': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
            'ajustement': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
            'retour': 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200'
        };
        return colors[type] || 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200';
    }

    // Actions rapides depuis le modal
    function quickAdjustStock() {
        closeModal('variant-detail');
        // Ouvrir le modal d'ajustement de stock avec les données pré-remplies
        setTimeout(() => {
            openStockAdjustmentModal(currentVariantData.id, currentVariantData.name, currentVariantData.stock);
        }, 300);
    }

    function quickEdit() {
        closeModal('variant-detail');
        // Ouvrir le modal d'édition avec les données pré-remplies
        setTimeout(() => {
            openEditVariantModal(currentVariantData.id);
        }, 300);
    }

    function duplicateVariant() {
        if (confirm(`Dupliquer le variant "${currentVariantData.name}" ?`)) {
            fetch(`/inventory/variants/${currentVariantData.id}/duplicate`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        closeModal('variant-detail');
                        loadVariantsTable(); // Recharger le tableau
                        showNotification('Variant dupliqué avec succès', 'success');
                    } else {
                        alert('Erreur lors de la duplication');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors de la duplication');
                });
        }
    }

    function editCurrentVariant() {
        closeModal('variant-detail');
        setTimeout(() => {
            openEditVariantModal(currentVariantData.id);
        }, 300);
    }

    // Fonctions pour ouvrir d'autres modals (à implémenter)
    function openStockAdjustmentModal(variantId, variantName, currentStock) {
        // TODO: Implémenter le modal d'ajustement de stock
        console.log('Ouvrir ajustement stock pour:', variantId, variantName, currentStock);
    }

    function openEditVariantModal(variantId) {
        // TODO: Implémenter le modal d'édition
        console.log('Ouvrir édition pour:', variantId);
    }

    function showNotification(message, type = 'info') {
        // TODO: Système de notifications toast
        console.log(`${type}: ${message}`);
    }

    function loadVariantImages(variantId) {
        const grid = document.getElementById('variant-images-grid');
        const empty = document.getElementById('variant-images-empty');
        grid.innerHTML = '';
        empty.classList.add('hidden');
        fetch(`/inventory/variants/${variantId}`)
            .then(response => response.json())
            .then(data => {
                if (data.medias && data.medias.length > 0) {
                    data.medias.filter(m => m.type === 'image').forEach(media => {
                        const img = document.createElement('img');
                        img.src = media.url;
                        img.alt = 'Image variant';
                        img.className = 'w-full h-32 object-cover rounded-lg cursor-pointer border border-gray-200 dark:border-gray-700 hover:scale-105 transition';
                        img.onclick = () => showVariantImageModal(media.url, media.filename || '');
                        grid.appendChild(img);
                    });
                } else {
                    empty.classList.remove('hidden');
                }
            })
            .catch(() => {
                empty.classList.remove('hidden');
            });
    }

    function showVariantImageModal(url, caption) {
        if (!url) return;
        const modal = document.getElementById('variant-image-modal');
        const img = document.getElementById('variant-image-modal-img');
        img.src = url;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeVariantImageModal() {
        const modal = document.getElementById('variant-image-modal');
        const img = document.getElementById('variant-image-modal-img');
        img.src = '';
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }

    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('variant-image-modal');
        if (modal) {
            modal.addEventListener('click', function (e) {
                if (e.target === modal) closeVariantImageModal();
            });
        }
    });
</script>
