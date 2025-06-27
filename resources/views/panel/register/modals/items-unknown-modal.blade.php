<x-modal name="items-unknown-modal" title="Article Z" size="2xl" icon="question" iconColor="orange" :footer="false">
    <div class="flex-1 overflow-y-auto flex flex-col">
        <!-- Formulaire de création Article Z -->
        <div class="space-y-4">
            <!-- Nom de l'article -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    <i class="fas fa-tag mr-1"></i>
                    <span class="text-red-500">*</span>
                    Nom de l'article
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
                    <span class="text-red-500">*</span>
                    Prix de vente <sup class="text-gray-500 text-xs ms-3">TVAC</sup>
                </label>
                <div class="border rounded flex items-center text-base">
                    <input type="number" name="item_price" placeholder="0.00" class="border-0 px-3 py-2 flex-1 outline-0 focus:text-black" min="0" step="0.01" required>
                    <span class="px-3 text-gray-500">€</span>
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
            <button id="add-article-z-btn" type="button" class="w-full bg-orange-500 text-white py-2 rounded hover:bg-orange-600 transition-colors flex items-center justify-center">
                <i class="fas fa-plus mr-2"></i>
                Ajouter l'Article Z au panier
            </button>
            <p class="text-xs text-gray-500 text-center mt-1">
                L'article sera ajouté avec un identifiant temporaire
            </p>
        </div>
    </div>
</x-modal>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('add-article-z-btn');
    if (!btn) return;
    btn.addEventListener('click', async function(e) {
        e.preventDefault();
        const modal = document;
        const name = modal.querySelector('input[name="item_name"]').value.trim();
        const description = modal.querySelector('textarea[name="item_description"]').value.trim();
        const price = parseFloat(modal.querySelector('input[name="item_price"]').value);

        if (!name || isNaN(price) || price <= 0) {
            if (window.registerManager) registerManager.showNotification('Veuillez remplir le nom et un prix valide.', 'warning');
            return;
        }

        // Appel AJAX
        const response = await fetch('/register/partials/cart/add-temporary', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            },
            body: JSON.stringify({ name, description, price })
        });
        const data = await response.json();
        if (data.success) {
            if (window.registerManager) {
                registerManager.showNotification('Article Z ajouté au panier', 'success');
                registerManager.loadCart();
            }
            if (window.closeModal) window.closeModal('items-unknown-modal');
        } else {
            if (window.registerManager) registerManager.showNotification(data.message || 'Erreur lors de l\'ajout', 'error');
        }
    });
});
</script>
