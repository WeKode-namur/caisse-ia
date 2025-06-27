// ===== REGISTER MANAGEMENT SYSTEM =====

class RegisterManager {
    constructor() {
        this.cart = [];
        this.customer = null;
        this.discounts = [];
        this.totals = {};
        this.isProcessing = false;
        this.currentTransaction = null;
        this.pendingPayments = [];
    }

    init() {
        this.setupEventListeners();
        this.setupBarcodeScanner();
        this.loadCart();
        // Ne plus charger les produits ici car c'est géré par products-grid.blade.php
    }

    // ===== EVENT LISTENERS =====
    setupEventListeners() {
        // Protection anti-double attachement
        if (window.__registerManagerListenersAdded) return;
        window.__registerManagerListenersAdded = true;

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
                const articleId = productCard.dataset.articleId;
                const hasVariants = productCard.dataset.hasVariants === 'true';

                if (hasVariants) {
                    if (window.showArticleVariants) {
                        window.showArticleVariants(articleId);
                    }
                } else if (window.addSingleVariantToCart) {
                    window.addSingleVariantToCart(articleId);
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

        // Gestion du modal de paiement
        this.setupPaymentModalListeners();

        document.addEventListener('click', (e) => {
            if (e.target.closest('.remove-discount')) {
                const discountId = e.target.closest('.remove-discount').dataset.discountId;
                this.removeDiscount(discountId);
            }
        });
    }

    setupPaymentModalListeners() {
        const container = document.getElementById('payment-inputs-container');
        if (container) {
            container.addEventListener('input', () => {
                this.updatePaymentStateFromInputs();
            });
        }

        const addPaymentForm = document.getElementById('add-payment-form');
        if(addPaymentForm) {
            addPaymentForm.addEventListener('submit', (e) => {
                e.preventDefault();
                // Cette fonction n'existe plus, nous laissons le bouton de finalisation gérer la soumission.
                // this.addPendingPayment();
            });
        }
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
            const response = await this.request(`/register/partials/products/barcode/${barcode}`);

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
            const response = await this.request('/register/partials/cart/add', {
                method: 'POST',
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
            const response = await this.request(`/register/partials/cart/update/${itemId}`, {
                method: 'PUT',
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
            const response = await this.request(`/register/partials/cart/remove/${itemId}`, {
                method: 'DELETE'
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
            const response = await this.request('/register/partials/cart/clear', {
                method: 'DELETE'
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
            const response = await this.request('/register/partials/cart');
            const data = await response.json();

            if (data.success) {
                this.cart = Object.values(data.cart || {});
                this.customer = data.customer || null;
                this.discounts = Array.isArray(data.discounts) ? data.discounts : Object.values(data.discounts || {});
                this.totals = data.totals || {};
                this.renderCart();
                this.renderTotals();
                this.renderCustomer();
            } else {
                this.showNotification('Erreur lors du chargement du panier', 'error');
            }
        } catch (error) {
            this.showNotification('Erreur réseau', 'error');
        }
    }

    renderCart() {
        const cartContainer = document.getElementById('listing-items');
        if (!cartContainer) return;

        if (this.cart.length === 0 && (!this.discounts || this.discounts.length === 0)) {
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

        const cartItemsHtml = this.cart.map(item => {
            // Calcul du prix TTC unitaire
            const unitPriceTTC = parseFloat(item.unit_price) * (1 + (parseFloat(item.tax_rate || 0) / 100));
            const costPrice = parseFloat(item.cost_price || 0);
            const isLoss = unitPriceTTC < costPrice && costPrice > 0;
            const priceClass = isLoss ? 'text-orange-500 font-bold animate-pulse' : '';
            const warningIcon = isLoss ? `<span title="Prix en dessous du coût !" class="me-1 text-sm text-orange-500"><i class="fas fa-exclamation-triangle"></i></span>` : '';
            return `
            <div class="border-b dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-900 duration-300 bg-white dark:bg-gray-800 hover:shadow p-3 flex gap-6 group z-20" data-item-id="${item.id}">
                <div class="w-1/3">
                    <div class="flex items-center gap-3">
                        <p class="truncate">
                            <span class="${priceClass}">${warningIcon}</span>
                            ${item.article_name}
                        </p>
                    </div>
                    <div class="flex items-center gap-3 text-gray-500 text-xs">
                        <i class="fas fa-barcode"></i>
                        <p>${item.barcode || 'N/A'}</p>
                    </div>
                </div>
                <div class="flex flex-1 gap-6 items-center">
                    <div class="w-full grid grid-cols-3 gap-6 items-center justify-end">
                        <p class="editable-unit-price text-end ${priceClass}" data-item-id="${item.id}" tabindex="0" style="cursor:pointer;">${item.unit_price}€</p>
                        <div class="flex w-24">
                            <button type="button"
                                    class="qty-decrease border-y border-l dark:border-gray-700 py-1 px-2 rounded-l group-hover:bg-red-50 dark:group-hover:bg-green-950 group-hover:hover:bg-red-400 dark:group-hover:hover:bg-green-600 duration-300"
                                    data-item-id="${item.id}">-</button>
                            <div class="border dark:border-gray-700 py-1 px-2 text-center w-12">${item.quantity}</div>
                            <button type="button"
                                    class="qty-increase border-y border-r dark:border-gray-700 py-1 px-2 rounded-r group-hover:bg-green-50 dark:group-hover:bg-red-950 group-hover:hover:bg-green-400 dark:group-hover:hover:bg-red-600 duration-300"
                                    data-item-id="${item.id}">+</button>
                        </div>
                        <p class="editable-total-price text-end ${priceClass}" data-item-id="${item.id}" tabindex="0" style="cursor:pointer;">${item.total_price}€</p>
                    </div>
                    <button class="remove-item bg-red-300 dark:bg-red-800/50 group-hover:bg-red-500 text-white rounded w-8 h-8 text-sm text-center hover:bg-red-700 duration-300 hover:scale-105 hover:shadow-lg"
                            data-item-id="${item.id}">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `}).join('');

        // Ajout des remises en bas du panier
        let discountsHtml = '';
        if (this.discounts && this.discounts.length > 0) {
            this.discounts.forEach(discount => {
                let valueLabel = '';
                if (discount.type === 'percentage') {
                    valueLabel = `-${discount.value}%`;
                } else if (discount.type === 'fixed') {
                    valueLabel = `-${discount.amount.toFixed(2)} €`;
                }
                discountsHtml += `
                    <div class="border-b dark:border-gray-700 bg-blue-50 dark:bg-blue-900/30 p-3 flex gap-6 group z-10 items-center">
                        <div class="flex-1 flex items-center gap-2">
                            <i class="fas fa-percent text-blue-600"></i>
                            <span class="font-semibold text-blue-700 dark:text-blue-300">${discount.name || 'Remise'}</span>
                            <span class="ml-2 text-blue-600 dark:text-blue-300 text-sm">${valueLabel}</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="font-bold text-blue-700 dark:text-blue-300">
                                -${discount.amount.toFixed(2)} €
                            </span>
                            <button class="remove-discount bg-red-100 hover:bg-red-300 text-red-700 rounded px-2 py-1 ml-2"
                                data-discount-id="${discount.id}" title="Supprimer la remise">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                `;
            });
        }

        cartContainer.innerHTML = cartItemsHtml + discountsHtml;

        // Ajout de l'édition inline après le rendu
        this.setupInlinePriceEdit();
    }

    setupInlinePriceEdit() {
        // Double-clic sur prix unitaire
        document.querySelectorAll('.editable-unit-price').forEach(el => {
            el.ondblclick = (e) => {
                this.makePriceEditable(el, 'unit');
            };
        });
        // Double-clic sur prix total
        document.querySelectorAll('.editable-total-price').forEach(el => {
            el.ondblclick = (e) => {
                this.makePriceEditable(el, 'total');
            };
        });
    }

    makePriceEditable(el, type) {
        const itemId = el.dataset.itemId;
        const oldValue = parseFloat(el.textContent);
        const input = document.createElement('input');
        input.type = 'number';
        input.step = '0.01';
        input.min = '0';
        input.value = oldValue;
        input.className = 'border rounded px-1 py-0.5 w-20';
        input.style.fontWeight = 'bold';
        input.style.color = el.style.color;
        el.replaceWith(input);
        input.focus();
        input.select();
        const save = async () => {
            let newValue = parseFloat(input.value);
            if (isNaN(newValue) || newValue <= 0) {
                this.showNotification('Prix invalide', 'warning');
                input.replaceWith(el);
                return;
            }
            if (newValue === oldValue) {
                input.replaceWith(el);
                return;
            }
            // Si édition du total, recalculer le prix unitaire
            let priceToSend = newValue;
            const item = this.cart.find(i => i.id === itemId);
            if (!item) return;
            if (type === 'total') {
                priceToSend = newValue / parseFloat(item.quantity);
            }
            // Appel API pour mettre à jour le prix ET la quantité actuelle
            try {
                const response = await this.request(`/register/partials/cart/update/${itemId}`, {
                    method: 'PUT',
                    body: JSON.stringify({ price: priceToSend, quantity: item.quantity })
                });
                const data = await response.json();
                if (data.success) {
                    await this.loadCart();
                } else {
                    this.showNotification(data.message || 'Erreur lors de la mise à jour', 'error');
                    input.replaceWith(el);
                }
            } catch (error) {
                this.showNotification('Erreur réseau', 'error');
                input.replaceWith(el);
            }
        };
        input.onblur = save;
        input.onkeydown = (e) => {
            if (e.key === 'Enter') save();
            if (e.key === 'Escape') input.replaceWith(el);
        };
    }

    renderTotals() {
        const totalsContainer = document.querySelector('.cart-totals');
        if (!totalsContainer) return;

        const itemsCount = this.totals.items_count || 0;
        const total = this.totals.total || 0;
        // const totalDiscount = this.totals.total_discount || 0;

        let totalsHtml = `
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-500 dark:text-gray-400">Articles: ${itemsCount}</span>
                <span class="text-lg font-bold dark:text-white">Prix: ${total.toFixed(2)} €</span>
            </div>
        `;
        // Suppression de l'affichage de la remise appliquée dans le footer
        totalsContainer.innerHTML = totalsHtml;
    }

    renderCustomer() {
        const customerContainer = document.querySelector('.customer-display');
        if (!customerContainer) return;

        // Vérifier si la gestion des clients est activée
        const customerManagementEnabled = window.registerConfig?.customerManagement || false;

        if (!customerManagementEnabled) {
            customerContainer.innerHTML = `
                <div class="p-2 text-center text-gray-500 text-sm">
                    <i class="fas fa-user-slash mr-2"></i>
                    Gestion des clients désactivée
                </div>
            `;
            return;
        }

        if (this.customer) {
            customerContainer.innerHTML = `
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
            customerContainer.innerHTML = `
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

        const paymentInputsContainer = document.getElementById('payment-inputs-container');
        const paymentMethods = window.registerConfig.paymentMethods || [];

        paymentInputsContainer.innerHTML = paymentMethods
            .filter(method => method.is_active)
            .map(method => `
            <div class="relative">
                <label for="payment-method-${method.id}" class="flex items-center text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    <i class="fas fa-${method.icon || 'credit-card'} w-6 text-center mr-2"></i>
                    ${method.name}
                </label>
                <input type="number" step="0.01" min="0" id="payment-method-${method.id}"
                       data-method-id="${method.id}" data-method-name="${method.name}"
                       class="payment-input w-full pl-3 pr-10 py-2 border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="0.00">
                <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 pt-6">€</span>
            </div>
        `).join('');

        this.updatePaymentStateFromInputs(); // Initialiser le récapitulatif
        window.openModal('payment-modal');
    }

    updatePaymentStateFromInputs() {
        this.pendingPayments = [];
        document.querySelectorAll('.payment-input').forEach(input => {
            const amount = parseFloat(input.value) || 0;
            if (amount > 0) {
                this.pendingPayments.push({
                    payment_method_id: input.dataset.methodId,
                    amount: amount,
                    methodName: input.dataset.methodName
                });
            }
        });
        this.updatePaymentRecap();
    }

    // Nouvelle logique d'arrondi belge pour paiement mixte ou tout espèces
    belgianRound(amount) {
        return Math.round(amount * 20) / 20;
    }

    updatePaymentRecap() {
        const total = this.totals.total || 0;
        const arrondissementEnabled = window.registerConfig?.arrondissementMethod;
        const paymentMethods = window.registerConfig?.paymentMethods || [];
        const cashMethod = paymentMethods.find(m => m.code === 'cash');
        const paidExceptCash = this.pendingPayments
            .filter(p => !(cashMethod && p.payment_method_id == cashMethod.id))
            .reduce((sum, p) => sum + p.amount, 0);
        let cashInput = null;
        let cashPending = 0;
        if (cashMethod) {
            cashInput = document.getElementById(`payment-method-${cashMethod.id}`);
            const cashPayment = this.pendingPayments.find(p => p.payment_method_id == cashMethod.id);
            cashPending = cashPayment ? cashPayment.amount : 0;
        }
        // Montant à payer restant après autres moyens de paiement
        let remainingForCash = total - paidExceptCash;
        let arrondiCash = remainingForCash;
        if (arrondissementEnabled && cashMethod) {
            arrondiCash = this.belgianRound(remainingForCash);
        }
        // Calcul du total payé (tous moyens)
        const paid = this.pendingPayments.reduce((sum, p) => sum + p.amount, 0);
        // Calcul du reste à payer (pour le bouton)
        let remaining = total - paid;
        let change = 0;
        // Calcul de la monnaie à rendre (spécifique espèces)
        let cashChange = 0;
        if (arrondissementEnabled && cashMethod && cashPending > 0) {
            // On arrondit la partie espèces
            const cashToPay = arrondiCash;
            const otherPaid = paidExceptCash;
            const totalPaid = otherPaid + cashPending;
            const totalToPay = otherPaid + cashToPay;
            if (totalPaid > totalToPay) {
                cashChange = totalPaid - totalToPay;
            }
        } else if (remaining < 0) {
            change = -remaining;
            remaining = 0;
        }
        // Nouvelle logique d'arrondi belge pour paiement mixte ou tout espèces
        let allCash = false;
        if (
            arrondissementEnabled && cashMethod &&
            this.pendingPayments.length === 1 &&
            this.pendingPayments[0].payment_method_id === cashMethod.id
        ) {
            allCash = true;
        }
        let displayTotal = total;
        let displayRemaining = 0;
        let displayChange = 0;
        let paidCash = cashPending;
        let arrondiCashToPay = arrondiCash;
        if (allCash) {
            // Toutes espèces : arrondi sur le total
            displayTotal = this.belgianRound(total);
            displayRemaining = displayTotal - paidCash;
            if (displayRemaining < 0) {
                displayChange = -displayRemaining;
                displayRemaining = 0;
            }
        } else if (arrondissementEnabled && cashMethod && paidCash > 0) {
            // Paiement mixte avec espèces : arrondi sur la partie espèces
            arrondiCashToPay = this.belgianRound(remainingForCash);
            displayTotal = total;
            displayRemaining = arrondiCashToPay - paidCash;
            if (displayRemaining < 0) {
                displayChange = -displayRemaining;
                displayRemaining = 0;
            }
        } else {
            // Aucun arrondi
            displayTotal = total;
            displayRemaining = total - paid;
            if (displayRemaining < 0) {
                displayChange = -displayRemaining;
                displayRemaining = 0;
            }
        }
        // Affichage du récapitulatif
        const breakdownContainer = document.getElementById('payments-breakdown-list');
        breakdownContainer.innerHTML = `
            <div class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-400">À Payer :</span>
                <span class="font-semibold dark:text-white">${displayTotal.toFixed(2)} €</span>
            </div>
            ${this.pendingPayments.map(p => {
                const method = paymentMethods.find(m => m.id == p.payment_method_id);
                return `<div class="flex justify-between pl-4">
                    <span class="text-gray-500 dark:text-gray-400">${p.methodName} :</span>
                    <span class="font-semibold dark:text-white">${p.amount.toFixed(2)} €</span>
                </div>`;
            }).join('')}
        `;
        const remainingLabel = document.getElementById('recap-remaining-label');
        const remainingAmountEl = document.getElementById('recap-remaining');
        // Affichage du reste à payer ou monnaie à rendre
        if (displayChange > 0) {
            remainingLabel.textContent = 'Monnaie à rendre :';
            remainingAmountEl.textContent = `${displayChange.toFixed(2)} €`;
            remainingAmountEl.className = 'font-bold text-green-500';
        } else {
            remainingLabel.textContent = 'Reste à payer :';
            remainingAmountEl.textContent = `${displayRemaining.toFixed(2)} €`;
            remainingAmountEl.className = displayRemaining > 0 ? 'font-bold text-red-600' : 'font-bold';
        }
        // Affichage du message 'Tout est payé' si le restant à payer ET la monnaie à rendre sont 0.00 €
        if (Math.abs(displayRemaining) < 0.009 && Math.abs(displayChange) < 0.009) {
            remainingLabel.style.display = 'none';
            remainingAmountEl.style.display = 'none';
            let paidMsg = document.getElementById('recap-paid-msg');
            if (!paidMsg) {
                paidMsg = document.createElement('div');
                paidMsg.id = 'recap-paid-msg';
                paidMsg.className = 'flex items-center justify-center text-green-600 font-bold py-2';
                remainingLabel.parentElement.appendChild(paidMsg);
            }
            paidMsg.innerHTML = "<i class='fas fa-check-circle text-green-500 me-2'></i> Tout est payé";
        } else {
            remainingLabel.style.display = '';
            remainingAmountEl.style.display = '';
            const paidMsg = document.getElementById('recap-paid-msg');
            if (paidMsg) paidMsg.remove();
        }
        // Mettre à jour le placeholder de tous les champs de paiement
        paymentMethods.forEach(method => {
            const input = document.getElementById(`payment-method-${method.id}`);
            if (!input) return;
            if (arrondissementEnabled && cashMethod && method.code === 'cash') {
                input.placeholder = arrondiCashToPay.toFixed(2);
            } else {
                let paidExceptCurrent = this.pendingPayments
                    .filter(p => p.payment_method_id != method.id)
                    .reduce((sum, p) => sum + p.amount, 0);
                let remainingForThis = total - paidExceptCurrent;
                input.placeholder = remainingForThis > 0 ? remainingForThis.toFixed(2) : '0.00';
            }
        });
        const finalizeBtn = document.getElementById('finalize-payment-btn');
        if (finalizeBtn) {
            finalizeBtn.disabled = displayRemaining > 0;
        }
    }

    async finalizePayment() {
        if (this.isProcessing) return;
        this.isProcessing = true;

        const notes = document.getElementById('payment_notes')?.value || '';

        try {
            const response = await this.request('/register/partials/payment/finalize', {
                method: 'POST',
                body: JSON.stringify({
                    payments: this.pendingPayments,
                    notes: notes
                })
            });

            // Si la réponse est une redirection (succès), le navigateur suivra
            if(response.ok && response.redirected) {
                 window.location.href = response.url;
            } else {
                 const result = await response.json();
                 throw new Error(result.message || 'Une erreur est survenue.');
                 console.error(result);
            }

        } catch (error) {
            console.error(error.message);
            this.showNotification(error.message, 'error');
        } finally {
            this.isProcessing = false;
        }
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
            const transactionResponse = await this.request('/register/partials/transactions/create', {
                method: 'POST',
                body: JSON.stringify(transactionData)
            });

            const transactionResult = await transactionResponse.json();

            if (!transactionResult.success) {
                throw new Error(transactionResult.message);
            }

            // 2. Traiter le paiement
            const paymentResponse = await this.request('/register/partials/payment/process', {
                method: 'POST',
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
            const response = await this.request('/register/partials/customers/remove', {
                method: 'DELETE'
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

    async removeDiscount(discountId) {
        try {
            const response = await this.request(`/register/partials/discounts/remove/${discountId}`, {
                method: 'DELETE'
            });
            const data = await response.json();
            if (data.success) {
                this.showNotification('Remise supprimée', 'success');
                await this.loadCart();
            } else {
                this.showNotification(data.message || 'Erreur lors de la suppression', 'error');
            }
        } catch (error) {
            this.showNotification('Erreur réseau', 'error');
        }
    }

    // ===== UTILITIES =====
    getHeaders() {
        return {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
        };
    }

    async request(url, options = {}) {
        const defaultOptions = {
            credentials: 'same-origin',
            headers: this.getHeaders()
        };
        if (options.headers) {
            defaultOptions.headers = { ...defaultOptions.headers, ...options.headers };
        }
        return fetch(url, { ...defaultOptions, ...options });
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
window.addEventListener('load', function() {
    if (!window.registerManager) {
        window.registerManager = new RegisterManager();
        window.registerManager.init();
    }
});
