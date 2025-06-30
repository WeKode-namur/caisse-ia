
<div class="border-r dark:border-gray-700 lg:w-1/3 flex flex-col">
    <div class="border-b dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-0.5 flex gap-6 text-gray-400 dark:text-gray-500 text-xs">
        <div class="">Nom de l'article</div>
        <div class="flex flex-1 gap-6">
            <div class="w-full flex gap-6 justify-end">
                <p>Prix U.</p>
                <div class="w-24 text-center">Nombre</div>
                <p>Prix T.</p>
            </div>
            <div class="w-8 text-end"></div>
        </div>
    </div>
    <div id="listing-items" class="h-full">
        <!-- Les articles du panier seront injectés ici par register.js -->
    </div>
    <div id="cart-column-customers-view" class="border-t border-gray-200 dark:border-gray-800 hidden"> <!-- s'affiche dynamiquement si un client est lié -->
        <div class="px-4 py-2 gap-6 items-center grid grid-cols-4">
            <div class="flex-1 flex gap-6 items-center col-span-2">
                <div id="cart-client-avatar" class="rounded-full flex items-center justify-center w-8 h-8 text-xs">
                    <!-- Icône dynamique -->
                </div>
                <div>
                    <div id="cart-client-name" class="font-semibold"></div> <!-- Nom/prénom ou nom entreprise -->
                    <div class="text-xs text-gray-400">
                        <div id="cart-client-email"></div><!-- Adresse mail -->
                        <div id="cart-client-vat" class="italic"></div> <!-- Numéro TVA si entreprise -->
                    </div>
                </div>
            </div>
            <div class="flex flex-col gap-1 items-center justify-center">
                <span id="cart-client-points" class="rounded-full py-1 px-1.5 text-xs w-auto"></span> <!-- Points -->
                <span id="cart-client-transactions" class="bg-blue-200 rounded-full py-1 px-1.5 text-xs w-auto"></span> <!-- Transactions -->
            </div>
            <div class="text-right">
                <button type="button"
                        id="trash-customers-cart"
                        class="rounded duration-500 hover:scale-105 hover:bg-red-600 text-red-500 hover:text-white py-1 px-1.5">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    </div>
    <div class="border-y border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-800 duration-300">
        <div class="p-4 flex gap-6 items-center">
            <div class="flex-1 cart-totals">
                <!-- Totaux du panier générés dynamiquement -->
            </div>
            <div class="text-right">
                <button id="pay-button" class="bg-green-500 dark:bg-green-700 text-white rounded px-4 py-2 hover:bg-green-600 duration-300 hover:scale-105 hover:shadow-lg">
                    <i class="fas fa-cash-register mr-3"></i>
                    Payer
                </button>
            </div>
        </div>
    </div>
</div>
<script>
// Fonction pour afficher dynamiquement le client dans la caisse
function renderCartCustomer(client) {
    const view = document.getElementById('cart-column-customers-view');
    const avatar = document.getElementById('cart-client-avatar');
    const name = document.getElementById('cart-client-name');
    const email = document.getElementById('cart-client-email');
    const vat = document.getElementById('cart-client-vat');
    const points = document.getElementById('cart-client-points');
    const transactions = document.getElementById('cart-client-transactions');
    view.classList.remove('hidden');
    if (client.type === 'company') {
        avatar.className = 'rounded-full bg-blue-200 text-blue-800 flex items-center justify-center w-8 h-8 text-xs';
        avatar.innerHTML = '<i class="fas fa-building"></i>';
    } else {
        avatar.className = 'rounded-full bg-green-200 text-green-800 flex items-center justify-center w-8 h-8 text-xs';
        avatar.innerHTML = '<i class="fas fa-user"></i>';
    }
    name.textContent = client.name;
    email.textContent = client.email || '';
    vat.textContent = client.company_number_be ? client.company_number_be : '';
    points.className = 'bg-green-200 rounded-full py-1 px-1.5 text-xs w-auto';
    points.textContent = 'Points : ' + (client.points ?? client.loyalty_points ?? 0);
    transactions.textContent = 'Transactions : ' + (client.transactions_count ?? '-');
}
// Affichage dynamique lors de la sélection
window.addEventListener('client-selected', function(e) {
    renderCartCustomer(e.detail);
});
// Affichage au chargement via AJAX
function fetchAndRenderCartCustomer() {
    fetch("{{ route('register.partials.customers.show') }}", {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content')
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data && data.customer) {
            renderCartCustomer(data.customer);
        } else {
            document.getElementById('cart-column-customers-view').classList.add('hidden');
        }
    })
    .catch(() => {
        document.getElementById('cart-column-customers-view').classList.add('hidden');
    });
}
document.addEventListener('DOMContentLoaded', fetchAndRenderCartCustomer);
// Suppression du client
const trashBtn = document.getElementById('trash-customers-cart');
trashBtn.addEventListener('click', function() {
    fetch("{{ route('register.partials.customers.remove') }}", {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content')
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById('cart-column-customers-view').classList.add('hidden');
            // Optionnel : notifier ou rafraîchir le panier
        } else {
            alert(data.message || 'Erreur lors de la suppression du client');
        }
    });
});
</script>
