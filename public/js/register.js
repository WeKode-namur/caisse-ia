// ===== REGISTER MANAGEMENT SYSTEM =====

class RegisterManager {
    constructor() {
        this.cart = [];
        this.customer = null;
        this.discounts = [];
        this.totals = {};
        this.isProcessing = false;

        this.init();
    }

    init() {
        this.setupEventListeners();
        this.setupBarcodeScanner();
        this.loadCart();
        // Ne plus charger les produits ici car c'est géré par products-grid.blade.php
    }

    // ===== EVENT LISTENERS =====
    setupEventListeners() {
        // Bouton payer
        document.addEventListener('click', (e) => {
            if (e.target.closest('#pay-button')) {
                e.preventDefault();
                this.openPaymentModal();
            }
        });

        // Scan code-barres
        document.getElementById('search_barcode')?.addEventListener('click', () => {
            this.scanBarcode();
        });

        // Recherche par code-barres avec Enter
        document.getElementById('barcode')?.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                this.scanBarcode();
            }
        });

        // Recherche en temps réel
        document.getElementById('barcode')?.addEventListener('input', (e) => {
            const query = e.target.value.trim();
            if (query.length >= 2) {
                // Délai pour éviter trop de requêtes
                clearTimeout(this.searchTimeout);
                this.searchTimeout = setTimeout(() => {
                    if (window.searchProducts) {
                        window.searchProducts(query);
                    }
                }, 500);
            } else if (query.length === 0) {
                // Vider la recherche
                if (window.clearFilters) {
                    window.clearFilters();
                }
            }
        });

        // Clicks sur les produits
        document.addEventListener('click', (e) => {
            if (e.target.closest('.product-card')) {
                const productCard = e.target.closest('.product-card');
                const variantId = productCard.dataset.variantId;
                if (variantId) {
                    this.addProductToCart(variantId);
                }
            }
        });

        // Gestion du panier
        document.addEventListener('click', (e) => {
            // Boutons quantité
            if (e.target.classList.contains('qty-decrease')) {
                this.updateCartItemQuantity(e.target.dataset.itemId, -1);
            }
            if (e.target.classList.contains('qty-increase')) {
                this.updateCartItemQuantity(e.target.dataset.itemId, 1);
            }

            // Bouton supprimer
            if (e.target.closest('.remove-item')) {
                this.removeCartItem(e.target.closest('.remove-item').dataset.itemId);
            }
        });
    }

    setupBarcodeScanner() {
        // Configuration du scanner de code-barres (si nécessaire)
        const barcodeInput = document.getElementById('barcode');
        if (barcodeInput) {
            barcodeInput.focus();

            // Auto-focus sur le champ code-barres
            document.addEventListener('keydown', (e) => {
                if (!document.activeElement || document.activeElement.tagName !== 'INPUT') {
                    if (e.key.match(/[0-9]/)) {
                        barcodeInput.focus();
                        barcodeInput.value = e.key;
                    }
                }
            });
        }
    }

    // ===== PRODUCT MANAGEMENT =====
    // Les produits sont maintenant gérés directement dans products-grid.blade.php

    async scanBarcode() {
        const barcodeInput = document.getElementById('barcode');
        const barcode = barcodeInput.value.trim();

        if (!barcode) {
            this.showNotification('Veuillez saisir un code-barres', 'warning');
            return;
        }

        try {
            const response = await fetch(`/register/partials/products/barcode/${barcode}`, {
                headers: this.getHeaders()
            });

            const data = await response.json();

            if (data.success) {
                await this.addProductToCart(data.product.id);
                barcodeInput.value = '';
                barcodeInput.focus();
            } else {
                this.showNotification(data.message, 'error');
            }
        } catch (error) {
            this.showNotification('Erreur lors de la recherche', 'error');
        }
    }

    // ===== CART MANAGEMENT =====
    async addProductToCart(variantId, quantity = 1, priceOverride = null) {
        if (this.isProcessing) return;
        this.isProcessing = true;

        try {
            const response = await fetch('/register/partials/cart/add', {
                method: 'POST',
                headers: this.getHeaders(),
                body: JSON.stringify({
                    variant_id: variantId,
                    quantity: quantity,
                    price_override: priceOverride
                })
            });

            const data = await response.json();

            if (data.success) {
                this.showNotification('Article ajouté au panier', 'success');
                await this.loadCart();
            } else {
                this.showNotification(data.message, 'error');
            }
        } catch (error) {
            this.showNotification('Erreur lors de l\'ajout', 'error');
        } finally {
            this.isProcessing = false;
        }
    }

    async updateCartItemQuantity(itemId, change) {
        const currentItem = this.cart.find(item => item.id === itemId);
        if (!currentItem) return;

        const newQuantity = Math.max(0.001, parseFloat(currentItem.quantity) + change);

        try {
            const response = await fetch(`/register/partials/cart/update/${itemId}`, {
                method: 'PUT',
                headers: this.getHeaders(),
                body: JSON.stringify({
                    quantity: newQuantity
                })
            });

            const data = await response.json();

            if (data.success) {
                await this.loadCart();
            } else {
                this.showNotification(data.message, 'error');
            }
        } catch (error) {
            this.showNotification('Erreur lors de la mise à jour', 'error');
        }
    }

    async removeCartItem(itemId) {
        try {
            const response = await fetch(`/register/partials/cart/remove/${itemId}`, {
                method: 'DELETE',
                headers: this.getHeaders()
            });

            const data = await response.json();

            if (data.success) {
                this.showNotification('Article supprimé', 'success');
                await this.loadCart();
            } else {
                this.showNotification(data.message, 'error');
            }
        } catch (error) {
            this.showNotification('Erreur lors de la suppression', 'error');
        }
    }

    async clearCart() {
        if (!confirm('Êtes-vous sûr de vouloir vider le panier ?')) {
            return;
        }

        try {
            const response = await fetch('/register/partials/cart/clear', {
                method: 'DELETE',
                headers: this.getHeaders()
            });

            const data = await response.json();

            if (data.success) {
                this.showNotification('Panier vidé', 'success');
                await this.loadCart();
            }
        } catch (error) {
            this.showNotification('Erreur lors du vidage', 'error');
        }
    }

    async loadCart() {
        try {
            const response = await fetch('/register/partials/cart', {
                headers: this.getHeaders()
            });

            const data = await response.json();

            if (data.success) {
                this.cart = data.cart;
                this.customer = data.customer;
                this.discounts = data.discounts;
                this.totals = data.totals;

                this.renderCart();
                this.renderTotals();
                this.renderCustomer();
            }
        } catch (error) {
            console.error('Erreur lors du chargement du panier:', error);
        }
    }

    renderCart() {
        const cartContainer = document.getElementById('listing-items');
        if (!cartContainer) return;

        if (this.cart.length === 0) {
            cartContainer.innerHTML = `
                <div class="h-full flex justify-center items-center">
                    <div class="p-8 text-center text-gray-300 dark:text-gray-700">
                        <i class="fas fa-shopping-cart text-4xl mb-4"></i>
                        <p>Panier vide</p>
                        <p class="text-sm">Scannez un article pour commencer</p>
                    </div>
                </div>
            `;
            return;
        }

        cartContainer.innerHTML = this.cart.map(item => `
            <div class="border-b dark:border-gray-700 hover:scale-105 duration-300 bg-white dark:bg-gray-800 hover:border hover:shadow-lg p-3 flex gap-6 group">
                <div class="">
                    <div class="flex items-center gap-3">
                        <p>${item.article_name}</p>
                        ${item.variant_reference ? `<p class="text-gray-600 dark:text-gray-400 text-sm">- ${item.variant_reference}</p>` : ''}
                    </div>
                    <div class="flex items-center gap-3 text-gray-500 text-xs">
                        <i class="fas fa-barcode"></i>
                        <p>${item.barcode || 'N/A'}</p>
                    </div>
                </div>
                <div class="flex flex-1 gap-6 items-center">
                    <div class="w-full flex gap-6 items-center justify-end">
                        <p>${item.unit_price}€</p>
                        <div class="flex w-24">
                            <button type="button"
                                    class="qty-decrease border-y border-l dark:border-gray-700 py-1 px-2 rounded-l group-hover:bg-red-50 dark:group-hover:bg-green-950 group-hover:hover:bg-red-400 dark:group-hover:hover:bg-green-600 duration-300"
                                    data-item-id="${item.id}">-</button>
                            <div class="border dark:border-gray-700 py-1 px-2 text-center w-12">${item.quantity}</div>
                            <button type="button"
                                    class="qty-increase border-y border-r dark:border-gray-700 py-1 px-2 rounded-r group-hover:bg-green-50 dark:group-hover:bg-red-950 group-hover:hover:bg-green-400 dark:group-hover:hover:bg-red-600 duration-300"
                                    data-item-id="${item.id}">+</button>
                        </div>
                        <p>${item.total_price}€</p>
                    </div>
                    <button class="remove-item bg-red-300 dark:bg-red-800/50 group-hover:bg-red-500 text-white rounded w-8 h-8 text-sm text-center hover:bg-red-700 duration-300 hover:scale-105 hover:shadow-lg"
                            data-item-id="${item.id}">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `).join('');
    }

    renderTotals() {
        const totalsContainer = document.querySelector('.cart-totals');
        if (!totalsContainer || !this.totals) return;

        totalsContainer.innerHTML = `
            <table>
                <tbody class="*:*:px-2 text-gray-500">
                    <tr>
                        <td class="text-end">Articles</td>
                        <td class="font-semibold">${this.totals.items_count || 0}</td>
                    </tr>
                    <tr>
                        <td class="text-end">Prix TVAC</td>
                        <td class="font-semibold">${this.totals.subtotal_ttc || '0.00'}€</td>
                    </tr>
                    ${this.totals.discount_amount > 0 ? `
                    <tr>
                        <td class="text-end">Remise</td>
                        <td class="font-semibold text-red-500">-${this.totals.discount_amount}€</td>
                    </tr>
                    ` : ''}
                    <tr class="border-t">
                        <td class="text-end font-bold">Total</td>
                        <td class="font-bold text-lg">${this.totals.total_amount || '0.00'}€</td>
                    </tr>
                </tbody>
            </table>
        `;
    }

    renderCustomer() {
        const customerDisplay = document.querySelector('.customer-display');
        if (!customerDisplay) return;

        // Vérifier si la gestion des clients est activée
        const customerManagementEnabled = window.registerConfig?.customerManagement || false;

        if (!customerManagementEnabled) {
            customerDisplay.innerHTML = `
                <div class="p-2 text-center text-gray-500 text-sm">
                    <i class="fas fa-user-slash mr-2"></i>
                    Gestion des clients désactivée
                </div>
            `;
            return;
        }

        if (this.customer) {
            customerDisplay.innerHTML = `
                <div class="flex items-center justify-between p-2 bg-blue-50 dark:bg-blue-900/20 rounded">
                    <div>
                        <span class="font-medium">${this.customer.name}</span>
                        ${this.customer.email ? `<span class="text-sm text-gray-500 ml-2">${this.customer.email}</span>` : ''}
                    </div>
                    <button onclick="registerManager.removeCustomer()" class="text-red-500 hover:text-red-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
        } else {
            customerDisplay.innerHTML = `
                <button onclick="registerManager.selectCustomer()" class="w-full p-2 border border-dashed border-gray-300 rounded text-gray-500 hover:border-blue-500 hover:text-blue-500">
                    + Sélectionner un client
                </button>
            `;
        }
    }

    // ===== PAYMENT =====
    async openPaymentModal() {
        if (this.cart.length === 0) {
            this.showNotification('Le panier est vide', 'warning');
            return;
        }

        // Implémenter l'ouverture du modal de paiement
        console.log('Ouverture du modal de paiement');
        // TODO: Implémenter le modal de paiement
    }

    async processPayment(paymentData) {
        try {
            // Préparer les données de transaction
            const transactionData = {
                transaction_type: 'ticket',
                notes: paymentData.notes
            };

            // Ajouter les données client seulement si la gestion est activée
            const customerManagementEnabled = window.registerConfig?.customerManagement || false;
            if (customerManagementEnabled && this.customer) {
                if (this.customer.type === 'customer') {
                    transactionData.customer_id = this.customer.id;
                } else if (this.customer.type === 'company') {
                    transactionData.company_id = this.customer.id;
                }
            }

            // 1. Créer la transaction
            const transactionResponse = await fetch('/register/partials/transactions/create', {
                method: 'POST',
                headers: this.getHeaders(),
                body: JSON.stringify(transactionData)
            });

            const transactionResult = await transactionResponse.json();

            if (!transactionResult.success) {
                throw new Error(transactionResult.message);
            }

            // 2. Traiter le paiement
            const paymentResponse = await fetch('/register/partials/payment/process', {
                method: 'POST',
                headers: this.getHeaders(),
                body: JSON.stringify({
                    transaction_id: transactionResult.transaction.id,
                    payment_method_id: paymentData.method_id,
                    amount: paymentData.amount,
                    reference: paymentData.reference
                })
            });

            const paymentResult = await paymentResponse.json();

            if (paymentResult.success) {
                this.showNotification('Paiement traité avec succès!', 'success');
                await this.clearCart();
                // Rediriger vers le ticket ou l'imprimer
                window.open(`/register/partials/transactions/${transactionResult.transaction.id}/print`, '_blank');
            } else {
                throw new Error(paymentResult.message);
            }

        } catch (error) {
            this.showNotification('Erreur lors du paiement: ' + error.message, 'error');
        }
    }

    async removeCustomer() {
        // Vérifier si la gestion des clients est activée
        const customerManagementEnabled = window.registerConfig?.customerManagement || false;
        if (!customerManagementEnabled) {
            this.showNotification('La gestion des clients n\'est pas activée', 'warning');
            return;
        }

        try {
            const response = await fetch('/register/partials/customers/remove', {
                method: 'DELETE',
                headers: this.getHeaders()
            });

            const data = await response.json();
            if (data.success) {
                this.customer = null;
                this.renderCustomer();
                this.showNotification('Client retiré', 'success');
            } else {
                this.showNotification(data.message, 'error');
            }
        } catch (error) {
            this.showNotification('Erreur lors de la suppression du client', 'error');
        }
    }

    selectCustomer() {
        // Vérifier si la gestion des clients est activée
        const customerManagementEnabled = window.registerConfig?.customerManagement || false;
        if (!customerManagementEnabled) {
            this.showNotification('La gestion des clients n\'est pas activée', 'warning');
            return;
        }

        // Ouvrir modal de sélection client
        console.log('Ouverture modal sélection client');
        // TODO: Implémenter le modal de sélection client
    }

    // ===== UTILITIES =====
    getHeaders() {
        return {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
        };
    }

    showNotification(message, type = 'info') {
        // Créer une notification élégante en haut à droite
        this.createToastNotification(message, type);
    }

    createToastNotification(message, type = 'info') {
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
}

// Initialiser le gestionnaire de caisse
let registerManager;
document.addEventListener('DOMContentLoaded', function() {
    registerManager = new RegisterManager();
});
