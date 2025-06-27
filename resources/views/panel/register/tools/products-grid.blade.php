<div class="h-full max-h-[calc(100vh-135px)] bg-gradient-to-r from-gray-100 to-violet-100 dark:from-gray-900/50 z-10 dark:to-indigo-950 relative">
    <div class="absolute h-full w-full z-10 pointer-events-none shadow-inset-custom dark:shadow-inset-custom-dark"></div>

    <!-- Container pour les produits avec scroll infini -->
    <div id="products-container"
         class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-5 p-4 overflow-y-auto h-full"
         data-page="1"
         data-loading="false"
         data-has-more="true">

        <!-- Les produits seront chargés ici dynamiquement -->
        <div id="products-grid" class="contents"></div>

        <!-- Indicateur de chargement -->
        <div id="loading-indicator" class="col-span-full flex justify-center items-center py-8 hidden">
            <div class="flex items-center space-x-2 text-gray-500">
                <div class="animate-spin rounded-full h-6 w-6 border-2 border-blue-500 border-t-transparent"></div>
                <span class="text-sm">Chargement...</span>
            </div>
        </div>

        <!-- Message fin de liste -->
        <div id="end-message" class="col-span-full text-center py-8 text-gray-500 text-sm hidden">
            <i class="fas fa-check-circle mb-2"></i>
            <p>Tous les produits ont été chargés</p>
        </div>
    </div>
</div>

<!-- Modal pour les variants -->
<x-modal name="product-variants"
         title="Choisir un variant"
         size="4xl"
         icon="tags"
         iconColor="blue"
         :footer="false">

    <div id="variant-modal-content">
        <!-- Header avec info produit -->
        <div id="product-info" class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
            <h4 id="product-name" class="font-semibold text-lg dark:text-white mb-2"></h4>
            <p id="product-description" class="text-gray-600 dark:text-gray-400 text-sm"></p>
        </div>
        <!-- Liste des variants -->
        <div class="max-h-[65vh] h-full relative">
            <div class="absolute w-full h-full pointer-events-none rounded shadow-inset-custom dark:shadow-inset-custom-dark"></div>
            <div class="overflow-y-auto h-full max-h-[65vh]">
                <div id="variants-list" class="space-y-3 p-5"></div>
            </div>
        </div>

        <!-- État de chargement -->
        <div id="variants-loading" class="flex justify-center items-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-2 border-blue-500 border-t-transparent"></div>
        </div>
    </div>
</x-modal>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const productsContainer = document.getElementById('products-container');
        const productsGrid = document.getElementById('products-grid');
        const loadingIndicator = document.getElementById('loading-indicator');
        const endMessage = document.getElementById('end-message');

        let currentPage = 1;
        let isLoading = false;
        let hasMore = true;
        let currentCategory = null;
        let currentSearch = null;

        // Charger la première page
        loadProducts();

        // Écouter le scroll pour le chargement infini
        productsContainer.addEventListener('scroll', function() {
            const scrollTop = productsContainer.scrollTop;
            const scrollHeight = productsContainer.scrollHeight;
            const clientHeight = productsContainer.clientHeight;

            // Si on approche du bas (100px avant la fin)
            if (scrollTop + clientHeight >= scrollHeight - 100) {
                if (!isLoading && hasMore) {
                    loadProducts();
                }
            }
        });

        // Fonction pour charger les produits
        async function loadProducts(reset = false) {
            if (isLoading) return;

            isLoading = true;

            if (reset) {
                currentPage = 1;
                hasMore = true;
                productsGrid.innerHTML = '';
                endMessage.classList.add('hidden');
            }

            loadingIndicator.classList.remove('hidden');

            try {
                const params = new URLSearchParams({
                    page: currentPage,
                    per_page: 25
                });

                if (currentCategory) params.append('category_id', currentCategory);
                if (currentSearch) params.append('search', currentSearch);

                const response = await fetch(`/register/partials/products?${params}`, {
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    }
                });

                const data = await response.json();

                if (data.success) {
                    renderProducts(data.products, reset);

                    // Vérifier s'il y a encore des pages
                    if (data.pagination) {
                        hasMore = data.pagination.current_page < data.pagination.last_page;
                        currentPage = data.pagination.current_page + 1;
                    } else {
                        hasMore = data.products.length === 25;
                        currentPage++;
                    }

                    if (!hasMore) {
                        endMessage.classList.remove('hidden');
                    }
                }
            } catch (error) {
                console.error('Erreur lors du chargement des produits:', error);
            } finally {
                isLoading = false;
                loadingIndicator.classList.add('hidden');
            }
        }

        // Fonction pour rendre les produits
        function renderProducts(products, reset = false) {
            // Séparer les produits en stock et en rupture
            const produitsEnStock = products.filter(p => p.stock_quantity > 0);
            const produitsRupture = products.filter(p => p.stock_quantity === 0);

            // Générer le HTML pour chaque groupe
            const htmlEnStock = produitsEnStock.map(product => {
                const hasMultipleVariants = product.variants_count > 1;
                const variantsOutOfStock = product.variants_out_of_stock_count || 0;
                const variantsInStock = product.variants_count - variantsOutOfStock;
                // Si plusieurs variants et un seul en stock, ajout direct, sinon modal
                const canAddDirect = hasMultipleVariants && variantsInStock === 1;
                const openModalOnClick = (hasMultipleVariants && !canAddDirect)
                    ? `window.showArticleVariants(${product.id})`
                    : `window.addSingleVariantToCart(${product.id})`;
                return `
            <div class="product-card bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition-all group hover:scale-105 duration-300 cursor-pointer max-h-[235px]"
                 data-article-id="${product.id}"
                 data-variants-count="${product.variants_count}"
                 onclick="${openModalOnClick}">
                <div class="w-full h-36 bg-gradient-to-tr from-blue-200 dark:from-blue-950 to-violet-200 dark:to-violet-900 rounded-t-lg flex items-center justify-center text-white relative overflow-hidden">
                    <!-- Badge orange -->
                    ${hasMultipleVariants ? `
                        <div class=\"absolute top-1 left-1 bg-orange-500 text-white text-xs px-1 py-0.5 rounded z-20\">
                            ${product.variants_count} variants
                        </div>
                    ` : ''}
                    <!-- Badge gris si variants en rupture -->
                    ${variantsOutOfStock > 0 ? `
                        <div class=\"absolute bottom-1 w-full px-1\">
                            <div class=\"text-center bg-gray-500 text-white text-xs px-1 py-0.5 rounded z-20 opacity-75\">
                                ${variantsOutOfStock} variant${variantsOutOfStock > 1 ? 's' : ''} en rupture de stock
                            </div>
                        </div>
                    ` : ''}
                    <!-- IMAGE DU PRODUIT -->
                    ${product.primary_image ? `
                        <div class=\"w-full h-full absolute top-0 left-0 z-0\" style=\"background-image: url('${product.primary_image}'); background-size: cover; background-position: center;\"></div>
                        <div class=\"w-full h-full absolute top-0 left-0 z-0 backdrop-blur-sm backdrop-grayscale \" ></div>
                        <img src="${product.primary_image}" alt="Image produit" class="object-contain h-full w-full absolute top-0 left-0 z-10" />
                    ` : `
                        <div class=\"flex items-center justify-center w-full h-full absolute top-0 left-0 z-0\">
                            <i class=\"fas fa-image text-3xl text-gray-400\"></i>
                        </div>
                    `}
                    ${product.stock_quantity <= 5 ? `
                        <div class=\"absolute top-1 right-1 bg-red-500 text-white text-xs px-1 py-0.5 rounded z-20\">
                            Stock faible
                        </div>
                    ` : ''}
                </div>
                <!-- MINIATURES -->
                ${(product.thumbnails && product.thumbnails.length > 1) ? `
                    <div class=\"flex flex-row gap-1 px-2 py-1 bg-white/80 dark:bg-gray-900/80 rounded-b-lg justify-center\">
                        ${product.thumbnails.map(url => `
                            <img src=\"${url}\" alt=\"Miniature\" class=\"object-cover w-8 h-8 rounded shadow border border-gray-200 dark:border-gray-700\" />
                        `).join('')}
                    </div>
                ` : ''}
                <div class=\"p-3\">
                    <h3 class=\"font-medium text-sm dark:text-white capitalize truncate\">${product.name}</h3>
                    <p class=\"text-xs text-gray-500 dark:text-gray-400\">${product.category?.name || ''}</p>
                    <div class=\"flex justify-between items-center mt-2\">
                        <span class=\"text-lg font-bold text-blue-600 dark:text-blue-400\">${product.price_display}</span>
                        <span class=\"text-xs text-gray-400\">Stock: ${product.stock_quantity}</span>
                    </div>
                </div>
            </div>
        `;
            }).join('');

            const htmlRupture = produitsRupture.map(product => `
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition-all cursor-pointer group duration-300 opacity-60"
                 >
                <div class="w-full h-36 bg-gradient-to-bl from-blue-200 dark:from-blue-950 to-violet-200 dark:to-violet-900 rounded-t-lg flex items-center justify-center text-white relative overflow-hidden">
                    <!-- IMAGE DU PRODUIT -->
                    ${product.primary_image ? `
                        <div class=\"w-full h-full absolute top-0 left-0 z-0\" style=\"background-image: url('${product.primary_image}'); background-size: cover; background-position: center;\"></div>
                        <div class=\"w-full h-full absolute top-0 left-0 z-0 backdrop-blur-sm backdrop-grayscale \" ></div>
                        <img src="${product.primary_image}" alt="Image produit" class="object-contain h-full w-full absolute top-0 left-0 z-10" />
                    ` : `
                        <div class=\"flex items-center justify-center w-full h-full absolute top-0 left-0 z-0\">
                            <i class=\"fas fa-image text-3xl text-gray-400\"></i>
                        </div>
                    `}
                    <div class="absolute top-1 right-1 bg-gray-500 text-white text-xs px-1 py-0.5 rounded z-20">
                        Rupture de stock
                    </div>
                    ${product.has_multiple_variants ? `
                        <div class="absolute top-1 left-1 bg-orange-500 text-white text-xs px-1 py-0.5 rounded z-20">
                            ${product.variants_count} variants
                        </div>
                    ` : ''}
                </div>
                <!-- MINIATURES -->
                ${(product.thumbnails && product.thumbnails.length > 1) ? `
                    <div class=\"flex flex-row gap-1 px-2 py-1 bg-white/80 dark:bg-gray-900/80 rounded-b-lg justify-center\">
                        ${product.thumbnails.map(url => `
                            <img src=\"${url}\" alt=\"Miniature\" class=\"object-cover w-8 h-8 rounded shadow border border-gray-200 dark:border-gray-700\" />
                        `).join('')}
                    </div>
                ` : ''}
                <div class="p-3">
                    <h3 class="font-medium text-sm dark:text-white capitalize truncate">${product.name}</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">${product.category?.name || ''}</p>
                    <div class="flex justify-between items-center mt-2">
                        <span class="text-lg font-bold text-blue-600 dark:text-blue-400">${product.price_display}</span>
                    </div>
                </div>
            </div>
        `).join('');

            const productsHtml = htmlEnStock + htmlRupture;

            if (reset) {
                productsGrid.innerHTML = productsHtml;
            } else {
                productsGrid.insertAdjacentHTML('beforeend', productsHtml);
            }
        }

        // Fonction pour ajouter un variant unique au panier
        async function addSingleVariantToCart(articleId) {
            try {
                // showNotification('Ajout au panier...', 'info'); // SUPPRIMÉ pour éviter le doublon

                const response = await fetch(`/register/partials/products/article/${articleId}/variants`, {
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    }
                });

                const data = await response.json();

                if (data.success && data.single_variant) {
                    if (window.registerManager) {
                        await window.registerManager.addProductToCart(data.single_variant.id);
                    }
                } else {
                    showNotification('Erreur lors de l\'ajout', 'error');
                }
            } catch (error) {
                console.error('Erreur:', error);
                showNotification('Erreur lors de l\'ajout', 'error');
            }
        }
        window.addSingleVariantToCart = addSingleVariantToCart;

        // Fonction pour afficher les variants d'un article
        async function showArticleVariants(articleId) {
            try {
                // Afficher le modal avec loading
                document.getElementById('variants-loading').classList.remove('hidden');
                document.getElementById('variants-list').innerHTML = '';
                openModal('product-variants');

                const response = await fetch(`/register/partials/products/article/${articleId}/variants`, {
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    }
                });

                const data = await response.json();

                if (data.success) {
                    showVariantsModal(data.article, data.variants);
                } else {
                    showNotification('Erreur lors du chargement des variants', 'error');
                    closeModal('product-variants');
                }
            } catch (error) {
                console.error('Erreur lors de la récupération des variants:', error);
                showNotification('Erreur lors du chargement', 'error');
                closeModal('product-variants');
            }
        }
        window.showArticleVariants = showArticleVariants;

        // Fonction pour afficher le modal des variants
        function showVariantsModal(article, variants) {
            // Remplir les informations du produit
            document.getElementById('product-name').textContent = article.name;
            document.getElementById('product-description').textContent = article.description || 'Aucune description disponible';

            // Variants à afficher
            const allVariants = variants;
            const seuilVariant = {{ config('custom.article.seuil') }};

            // Remplir la liste des variants
            const variantsList = document.getElementById('variants-list');
            variantsList.innerHTML = allVariants.map(variant => {
                const isOutOfStock = variant.stock_quantity === 0;
                return `
            <div class="group variant-item p-4 border border-gray-200 dark:border-gray-600 rounded-lg transition-all flex items-center gap-4 ${isOutOfStock ? 'opacity-50 cursor-not-allowed pointer-events-none bg-gray-100 dark:bg-gray-800' : 'hover:bg-slate-100 dark:hover:bg-slate-800 hover:border-blue-500 dark:hover:border-blue-400 cursor-pointer'}"
                 data-variant-id="${variant.id}"
                 ${!isOutOfStock ? `onclick=\"selectVariant(${variant.id})\"` : ''}>
                <div class="flex-shrink-0">
                    ${variant.primary_image ? `
                        <img src="${variant.primary_image}" alt="Miniature variant" class="object-cover w-14 h-14 rounded shadow border border-gray-200 dark:border-gray-700 group-hover:scale-125 duration-500"/>
                    ` : `
                        <div class=\"flex items-center justify-center w-14 h-14 bg-gray-100 dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-700\">
                            <i class=\"fas fa-image text-xl text-gray-400\"></i>
                        </div>
                    `}
                </div>
                <div class="flex-1">
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 rounded-full ${variant.stock_quantity > 0 ? (variant.stock_quantity > seuilVariant ? 'bg-green-500' : 'bg-orange-500') : 'bg-red-500'}"></div>
                        <div>
                            <h5 class="font-medium dark:text-white">
                                ${variant.reference || 'Variant standard'}
                            </h5>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Stock: ${variant.stock_quantity} | Prix: ${variant.sell_price}€
                            </p>
                            <div class="flex flex-wrap gap-1 mt-2">
                                ${(variant.attributes || []).map((attr, idx) => `
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                        ${idx % 4 === 0 ? 'bg-purple-50 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : ''}
                                        ${idx % 4 === 1 ? 'bg-pink-50 text-pink-800 dark:bg-pink-900 dark:text-pink-200' : ''}
                                        ${idx % 4 === 2 ? 'bg-green-50 text-green-800 dark:bg-green-900 dark:text-green-200' : ''}
                                        ${idx % 4 === 3 ? 'bg-orange-50 text-orange-800 dark:bg-orange-900 dark:text-orange-200' : ''}">
                                        ${attr.name}: ${attr.value}
                                    </span>
                                `).join('')}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col items-end space-y-2">
                    <span class="text-lg font-bold text-blue-600 dark:text-blue-400">${variant.sell_price}€</span>
                    ${isOutOfStock ? `<span class='inline-block bg-gray-500 text-white text-xs px-2 py-0.5 rounded'>Rupture de stock</span>` : ''}
                </div>
            </div>
        `;
            }).join('');

            // Masquer le loading et ouvrir le modal
            document.getElementById('variants-loading').classList.add('hidden');
            openModal('product-variants');
        }

        // Fonction globale pour sélectionner un variant
        window.selectVariant = async function(variantId) {
            closeModal('product-variants');

            if (window.registerManager) {
                await window.registerManager.addProductToCart(variantId);
            }
        };

        // Fonctions pour filtrer les produits
        window.filterByCategory = function(categoryId) {
            currentCategory = categoryId;
            currentSearch = null;
            loadProducts(true);
        };

        window.searchProducts = function(query) {
            currentSearch = query;
            currentCategory = null;
            loadProducts(true);
        };

        window.clearFilters = function() {
            currentCategory = null;
            currentSearch = null;
            loadProducts(true);
        };

        // Fonction de notification globale
        window.showNotification = function(message, type = 'info') {
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
        };
    });
</script>
