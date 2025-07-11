<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 *:bg-white/90 *:dark:bg-gray-800/75">
            <div class="overflow-hidden shadow-xl sm:rounded-lg mb-6">
                <div class="flex justify-between items-center px-6 py-4">
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        {{ $customer->full_name }}
                    </h2>
                    <div class="flex space-x-2">
                        <a href="{{ route('clients.customers.edit', $customer) }}"
                           class="bg-indigo-500 dark:bg-indigo-600 hover:bg-indigo-700 dark:hover:bg-indigo-900 text-white font-bold py-2 px-4 rounded hover:scale-105 duration-500">
                            Modifier
                        </a>
                        <a href="{{ route('clients.index') }}"
                           class="bg-gray-500 dark:bg-gray-600 hover:bg-gray-700 dark:hover:bg-gray-800 text-white font-bold py-2 px-4 rounded hover:scale-105 duration-500">
                            Retour
                        </a>
                    </div>
                </div>
            </div>
            <!-- Informations principales -->
            <div class="overflow-hidden shadow-xl sm:rounded-lg mb-6">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Informations du client</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 text-gray-900 dark:text-white">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Informations personnelles</h4>
                            <div class="mt-2 space-y-2">
                                <div>
                                    <span class="text-sm font-medium text-gray-800 dark:text-gray-400">Nom complet:</span>
                                    <span class="text-sm ml-2">{{ $customer->full_name }}</span>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-800 dark:text-gray-400">Numéro client:</span>
                                    <span class="text-sm ml-2">{{ $customer->customer_number }}</span>
                                </div>
                                @if($customer->gender)
                                    <div>
                                        <span class="text-sm font-medium text-gray-800 dark:text-gray-400">Genre:</span>
                                        <span class="text-sm ml-2">{{ \App\Models\Customer::GENDERS[$customer->gender] }}</span>
                                    </div>
                                @endif
                                @if($customer->birth_date)
                                    <div>
                                        <span class="text-sm font-medium text-gray-800 dark:text-gray-400">Date de naissance:</span>
                                        <span class="text-sm ml-2">{{ $customer->birth_date->format('d/m/Y') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Contact</h4>
                            <div class="mt-2 space-y-2">
                                <div>
                                    <span class="text-sm font-medium text-gray-800 dark:text-gray-400">Email:</span>
                                    <span class="text-sm ml-2">{{ $customer->email }}</span>
                                </div>
                                @if($customer->phone)
                                    <div>
                                        <span class="text-sm font-medium text-gray-800 dark:text-gray-400">Téléphone:</span>
                                        <span class="text-sm ml-2">{{ $customer->phone }}</span>
                                    </div>
                                @endif
                                @if($customer->last_visit_at)
                                    <div>
                                        <span class="text-sm font-medium text-gray-800 dark:text-gray-400">Dernière visite:</span>
                                        <span class="text-sm ml-2">{{ $customer->last_visit_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Statut</h4>
                            <div class="mt-2 space-y-2 text-gray-900 dark:text-white">
                                <div>
                                    <span class="text-sm font-medium text-gray-800 dark:text-gray-400">Statut:</span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ml-2
                                        {{ $customer->is_active ? 'bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-100' : 'bg-red-100 dark:bg-red-800 text-red-800 dark:text-red-100' }}">
                                        {{ $customer->is_active ? 'Actif' : 'Inactif' }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-800 dark:text-gray-400">Points fidélité:</span>
                                    <span class="text-sm ml-2">{{ $customer->loyalty_points }}</span>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-800 dark:text-gray-400">Total achats:</span>
                                    <span class="text-sm ml-2">{{ number_format($customer->total_purchases, 2) }} €</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($customer->notes)
                        <div class="mt-6">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Notes</h4>
                            <div class="mt-2 p-3 bg-gray-50 dark:bg-gray-900/75 rounded-sm border-l-2 border-violet-300 dark:border-violet-700">
                                <p class="text-sm text-gray-700 dark:text-gray-200">{{ $customer->notes }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Adresses -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6 dark:bg-gray-800/75">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Adresses</h3>
                    <button onclick="openAddressModal('customer', {{ $customer->id }})"
                            class="bg-blue-500 dark:bg-blue-700 hover:bg-blue-700 dark:hover:bg-blue-900 text-white text-sm font-bold py-1 px-3 rounded hover:scale-105 duration-500">
                        <i class="fas fa-plus mr-1"></i>
                        Ajouter une adresse
                    </button>
                </div>
                <div id="addresses-container" class="p-6">
                    <!-- Les adresses seront chargées ici en AJAX -->
                </div>
            </div>

            <!-- Points de fidélité récents -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6 dark:bg-gray-800/75">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Points de fidélité récents</h3>
                </div>
                <div class="p-6">
                    @if($customer->loyaltyPoints->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-900">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Points</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Description</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Expiration</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($customer->loyaltyPoints as $point)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ $point->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    @if($point->type === 'earned') bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-100
                                                    @elseif($point->type === 'spent') bg-red-100 dark:bg-red-800 text-red-800 dark:text-red-100
                                                    @elseif($point->type === 'expired') bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                                    @else bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-100
                                                    @endif">
                                                    {{ \App\Models\LoyaltyPoint::POINT_TYPES[$point->type] }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                <span class="{{ $point->points >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                                    {{ $point->points >= 0 ? '+' : '' }}{{ $point->points }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                                {{ $point->description ?: '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                @if($point->expires_at)
                                                    {{ $point->expires_at->format('d/m/Y') }}
                                                    @if($point->isExpired())
                                                        <span class="text-red-600 dark:text-red-400 text-xs">(Expiré)</span>
                                                    @elseif($point->isExpiringSoon())
                                                        <span class="text-yellow-600 dark:text-yellow-400 text-xs">(Expire bientôt)</span>
                                                    @endif
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 dark:text-gray-400 text-center py-4">Aucun point de fidélité enregistré</p>
                    @endif
                </div>
            </div>

            <!-- Transactions récentes -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg dark:bg-gray-800/75">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Transactions récentes</h3>
                </div>
                <div class="p-6">
                    @if($customer->transactions->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-900">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Numéro</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Montant</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Statut</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($customer->transactions as $transaction)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ $transaction->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ $transaction->transaction_number }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ number_format($transaction->total_amount, 2) }} €
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    @if($transaction->status === 'completed') bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-100
                                                    @elseif($transaction->status === 'pending') bg-yellow-100 dark:bg-yellow-800 text-yellow-800 dark:text-yellow-100
                                                    @elseif($transaction->status === 'cancelled') bg-red-100 dark:bg-red-800 text-red-800 dark:text-red-100
                                                    @else bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200
                                                    @endif">
                                                    {{ ucfirst($transaction->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 dark:text-gray-400 text-center py-4">Aucune transaction enregistrée</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<!-- Modal pour ajouter une adresse -->
@include('panel.clients.partials.address-modal')

<!-- Modal pour éditer une adresse -->
@include('panel.clients.partials.address-edit-modal')

<!-- Modal de confirmation de suppression -->
@include('panel.clients.partials.delete-confirmation-modal')

<script>
// Variables globales
let currentClientType = 'customer';
let currentClientId = {{ $customer->id }};
let addressToDelete = null;

// Fonctions pour les modals (si elles ne sont pas définies globalement)
if (typeof window.openModal === 'undefined') {
    window.openModal = function(name) {
        window.dispatchEvent(new CustomEvent('open-modal-' + name));
    }
}

if (typeof window.closeModal === 'undefined') {
    window.closeModal = function(name) {
        window.dispatchEvent(new CustomEvent('close-modal-' + name));
    }
}

// Charger les adresses au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    loadAddresses();
});

// Fonction pour charger les adresses
async function loadAddresses() {
    const container = document.getElementById('addresses-container');
    container.innerHTML = await LoadingUtils.getLoadingHtml('Chargement des adresses...');

    try {
        const response = await fetch(`/clients/addresses/${currentClientType === 'company' ? 'companies' : 'customers'}/${currentClientId}`);
        const data = await response.json();

        if (response.ok) {
            // Générer le HTML directement
            if (data.addresses && data.addresses.length > 0) {
                const addressesHtml = data.addresses.map(address => `
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-white/90 dark:bg-gray-800/75 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                ${data.address_types[address.type]}
                                ${address.is_primary ? '<span class=\"inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 ml-2\">Principale</span>' : ''}
                            </h4>
                            <div class="flex space-x-2">
                                <button title="Modifier"
                                    class="group py-1 px-1.5 rounded transition-all duration-500 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-600 hover:text-white dark:hover:text-white dark:hover:bg-indigo-500 scale-100 hover:scale-105"
                                    onclick="editAddress(${address.id})">
                                    <i class="fas fa-pen"></i>
                                </button>
                                ${!address.is_primary ? `
                                    <button title="Définir comme principale"
                                        class="group py-1 px-1.5 rounded transition-all duration-500 text-blue-600 dark:text-blue-400 hover:bg-blue-600 hover:text-white dark:hover:text-white dark:hover:bg-blue-500 scale-100 hover:scale-105"
                                        onclick="setPrimaryAddress(${address.id})">
                                        <i class="fas fa-star"></i>
                                    </button>
                                ` : ''}
                                <button title="Supprimer"
                                    class="group py-1 px-1.5 rounded transition-all duration-500 text-red-600 dark:text-red-400 hover:bg-red-600 hover:text-white dark:hover:text-white dark:hover:bg-red-500 scale-100 hover:scale-105"
                                    onclick="showDeleteConfirmation(${address.id})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-300">
                            <p>${address.street} ${address.number}</p>
                            <p>${address.postal_code} ${address.city}</p>
                            <p>${address.country}</p>
                        </div>
                    </div>
                `).join('');

                container.innerHTML = `<div class="grid grid-cols-1 md:grid-cols-2 gap-6">${addressesHtml}</div>`;
            } else {
                container.innerHTML = '<p class="text-gray-500 text-center py-4">Aucune adresse enregistrée</p>';
            }
        } else {
            container.innerHTML = '<p class="text-red-500 text-center py-4">Erreur lors du chargement des adresses</p>';
        }
    } catch (error) {
        container.innerHTML = '<p class="text-red-500 text-center py-4">Erreur lors du chargement des adresses</p>';
    }
}

// Fonction pour ouvrir le modal d'adresse
function openAddressModal(clientType, clientId) {
    currentClientType = clientType;
    currentClientId = clientId;

    // Remplir les champs cachés
    document.getElementById('client_type').value = clientType;
    document.getElementById('client_id').value = clientId;

    // Réinitialiser le formulaire
    document.getElementById('addressForm').reset();

    // Ouvrir le modal
    openModal('address-modal');
}

// Fonction pour fermer le modal d'adresse
function closeAddressModal() {
    closeModal('address-modal');
}

// Fonction pour ouvrir le modal d'édition d'adresse
async function editAddress(addressId) {
    try {
        const response = await fetch(`/clients/addresses/${addressId}/edit`);
        const data = await response.json();

        if (response.ok && data.success && data.address) {
            // Remplir le formulaire avec les données de l'adresse
            document.getElementById('edit_address_id').value = addressId;
            document.getElementById('edit_type').value = data.address.type;
            document.getElementById('edit_street').value = data.address.street;
            document.getElementById('edit_number').value = data.address.number;
            document.getElementById('edit_postal_code').value = data.address.postal_code;
            document.getElementById('edit_city').value = data.address.city;
            document.getElementById('edit_country').value = data.address.country;
            document.getElementById('edit_is_primary').checked = data.address.is_primary;

            // Ouvrir le modal
            openModal('address-edit-modal');
        } else {
            showNotification('Erreur lors du chargement de l\'adresse', 'error');
        }
    } catch (error) {
        showNotification('Erreur lors du chargement de l\'adresse', 'error');
    }
}

// Fonction pour fermer le modal d'édition d'adresse
function closeAddressEditModal() {
    closeModal('address-edit-modal');
}

// Fonction pour afficher la confirmation de suppression
function showDeleteConfirmation(addressId) {
    addressToDelete = addressId;
    openModal('delete-confirmation-modal');
}

// Fonction pour fermer le modal de confirmation
function closeDeleteConfirmationModal() {
    closeModal('delete-confirmation-modal');
    addressToDelete = null;
}

// Fonction pour confirmer la suppression
async function confirmDeleteAddress() {
    if (!addressToDelete) return;

    try {
        const response = await fetch(`/clients/addresses/${addressToDelete}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        const data = await response.json();

        if (response.ok && data.success) {
            closeDeleteConfirmationModal();
            await loadAddresses();
            showNotification('Adresse supprimée avec succès', 'success');
        } else {
            showNotification(data.message || 'Erreur lors de la suppression', 'error');
        }
    } catch (error) {
        showNotification('Erreur lors de la suppression', 'error');
    }
}

// Gestionnaire de soumission du formulaire d'adresse
document.getElementById('addressForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.textContent;

    submitButton.textContent = 'Ajout en cours...';
    submitButton.disabled = true;

    try {
        const response = await fetch('/clients/addresses', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        });

        const data = await response.json();

        if (response.ok && data.success) {
            // Fermer le modal
            closeAddressModal();

            // Recharger les adresses
            await loadAddresses();

            // Afficher un message de succès
            showNotification('Adresse ajoutée avec succès', 'success');
        } else {
            showNotification(data.message || 'Erreur lors de l\'ajout de l\'adresse', 'error');
        }
    } catch (error) {
        showNotification('Erreur lors de l\'ajout de l\'adresse', 'error');
    } finally {
        submitButton.textContent = originalText;
        submitButton.disabled = false;
    }
});

// Gestionnaire de soumission du formulaire d'édition d'adresse
document.getElementById('addressEditForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const addressId = document.getElementById('edit_address_id').value;
    const formData = new FormData(this);
    formData.append('_method', 'PUT'); // Ajout pour compatibilité Laravel
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.textContent;

    submitButton.textContent = 'Mise à jour en cours...';
    submitButton.disabled = true;

    try {
        const response = await fetch(`/clients/addresses/${addressId}`, {
            method: 'POST', // On utilise POST, Laravel détectera _method=PUT
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        });

        const data = await response.json();

        if (response.ok && data.success) {
            // Fermer le modal
            closeAddressEditModal();

            // Recharger les adresses
            await loadAddresses();

            // Afficher un message de succès
            showNotification('Adresse mise à jour avec succès', 'success');
        } else {
            showNotification(data.message || 'Erreur lors de la mise à jour de l\'adresse', 'error');
        }
    } catch (error) {
        showNotification('Erreur lors de la mise à jour de l\'adresse', 'error');
    } finally {
        submitButton.textContent = originalText;
        submitButton.disabled = false;
    }
});

// Fonction pour définir une adresse comme principale
async function setPrimaryAddress(addressId) {
    try {
        const response = await fetch(`/clients/addresses/${addressId}/primary`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        const data = await response.json();

        if (response.ok && data.success) {
            await loadAddresses();
            showNotification('Adresse définie comme principale', 'success');
        } else {
            showNotification(data.message || 'Erreur lors de la modification', 'error');
        }
    } catch (error) {
        showNotification('Erreur lors de la modification', 'error');
    }
}

// Fonction utilitaire pour afficher les notifications
function showNotification(message, type = 'info') {
    // Vous pouvez utiliser votre système de notification existant
    console.log(`${type.toUpperCase()}: ${message}`);
}
</script>
