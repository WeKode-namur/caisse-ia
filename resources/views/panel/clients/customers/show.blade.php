<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $customer->full_name }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('clients.customers.edit', $customer) }}" 
                   class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                    Modifier
                </a>
                <a href="{{ route('clients.customers.index') }}" 
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Retour
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Informations principales -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Informations du client</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Informations personnelles</h4>
                            <div class="mt-2 space-y-2">
                                <div>
                                    <span class="text-sm font-medium text-gray-900">Nom complet:</span>
                                    <span class="text-sm text-gray-600 ml-2">{{ $customer->full_name }}</span>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-900">Numéro client:</span>
                                    <span class="text-sm text-gray-600 ml-2">{{ $customer->customer_number }}</span>
                                </div>
                                @if($customer->gender)
                                    <div>
                                        <span class="text-sm font-medium text-gray-900">Genre:</span>
                                        <span class="text-sm text-gray-600 ml-2">{{ \App\Models\Customer::GENDERS[$customer->gender] }}</span>
                                    </div>
                                @endif
                                @if($customer->birth_date)
                                    <div>
                                        <span class="text-sm font-medium text-gray-900">Date de naissance:</span>
                                        <span class="text-sm text-gray-600 ml-2">{{ $customer->birth_date->format('d/m/Y') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Contact</h4>
                            <div class="mt-2 space-y-2">
                                <div>
                                    <span class="text-sm font-medium text-gray-900">Email:</span>
                                    <span class="text-sm text-gray-600 ml-2">{{ $customer->email }}</span>
                                </div>
                                @if($customer->phone)
                                    <div>
                                        <span class="text-sm font-medium text-gray-900">Téléphone:</span>
                                        <span class="text-sm text-gray-600 ml-2">{{ $customer->phone }}</span>
                                    </div>
                                @endif
                                @if($customer->last_visit_at)
                                    <div>
                                        <span class="text-sm font-medium text-gray-900">Dernière visite:</span>
                                        <span class="text-sm text-gray-600 ml-2">{{ $customer->last_visit_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Statut</h4>
                            <div class="mt-2 space-y-2">
                                <div>
                                    <span class="text-sm font-medium text-gray-900">Statut:</span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ml-2
                                        {{ $customer->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $customer->is_active ? 'Actif' : 'Inactif' }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-900">Niveau fidélité:</span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ml-2
                                        @if($customer->loyalty_tier === 'platinum') bg-purple-100 text-purple-800
                                        @elseif($customer->loyalty_tier === 'gold') bg-yellow-100 text-yellow-800
                                        @elseif($customer->loyalty_tier === 'silver') bg-gray-100 text-gray-800
                                        @else bg-orange-100 text-orange-800
                                        @endif">
                                        {{ \App\Models\Customer::LOYALTY_TIERS[$customer->loyalty_tier] }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-900">Points fidélité:</span>
                                    <span class="text-sm text-gray-600 ml-2">{{ $customer->loyalty_points }}</span>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-900">Total achats:</span>
                                    <span class="text-sm text-gray-600 ml-2">{{ number_format($customer->total_purchases, 2) }} €</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($customer->notes)
                        <div class="mt-6">
                            <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Notes</h4>
                            <div class="mt-2 p-3 bg-gray-50 rounded-md">
                                <p class="text-sm text-gray-700">{{ $customer->notes }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Adresses -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">Adresses</h3>
                    <a href="{{ route('clients.addresses.create.customer', $customer) }}" 
                       class="bg-blue-500 hover:bg-blue-700 text-white text-sm font-bold py-1 px-3 rounded">
                        Ajouter une adresse
                    </a>
                </div>
                <div class="p-6">
                    @if($customer->addresses->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($customer->addresses as $address)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <h4 class="text-sm font-medium text-gray-900">
                                            {{ \App\Models\CustomerAddress::ADDRESS_TYPES[$address->type] }}
                                            @if($address->is_primary)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 ml-2">
                                                    Principale
                                                </span>
                                            @endif
                                        </h4>
                                        <div class="flex space-x-2">
                                            <a href="{{ route('clients.addresses.edit', $address) }}" 
                                               class="text-indigo-600 hover:text-indigo-900 text-sm">Modifier</a>
                                            @if(!$address->is_primary)
                                                <form action="{{ route('clients.addresses.primary', $address) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="text-blue-600 hover:text-blue-900 text-sm">
                                                        Définir comme principale
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        <p>{{ $address->street }} {{ $address->number }}</p>
                                        <p>{{ $address->postal_code }} {{ $address->city }}</p>
                                        <p>{{ $address->country }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">Aucune adresse enregistrée</p>
                    @endif
                </div>
            </div>

            <!-- Points de fidélité récents -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Points de fidélité récents</h3>
                </div>
                <div class="p-6">
                    @if($customer->loyaltyPoints->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Points</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expiration</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($customer->loyaltyPoints as $point)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $point->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    @if($point->type === 'earned') bg-green-100 text-green-800
                                                    @elseif($point->type === 'spent') bg-red-100 text-red-800
                                                    @elseif($point->type === 'expired') bg-gray-100 text-gray-800
                                                    @else bg-blue-100 text-blue-800
                                                    @endif">
                                                    {{ \App\Models\LoyaltyPoint::POINT_TYPES[$point->type] }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <span class="{{ $point->points >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                    {{ $point->points >= 0 ? '+' : '' }}{{ $point->points }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900">
                                                {{ $point->description ?: '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                @if($point->expires_at)
                                                    {{ $point->expires_at->format('d/m/Y') }}
                                                    @if($point->isExpired())
                                                        <span class="text-red-600 text-xs">(Expiré)</span>
                                                    @elseif($point->isExpiringSoon())
                                                        <span class="text-yellow-600 text-xs">(Expire bientôt)</span>
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
                        <p class="text-gray-500 text-center py-4">Aucun point de fidélité enregistré</p>
                    @endif
                </div>
            </div>

            <!-- Transactions récentes -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Transactions récentes</h3>
                </div>
                <div class="p-6">
                    @if($customer->transactions->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Numéro</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($customer->transactions as $transaction)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $transaction->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $transaction->transaction_number }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ number_format($transaction->total_amount, 2) }} €
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    @if($transaction->status === 'completed') bg-green-100 text-green-800
                                                    @elseif($transaction->status === 'pending') bg-yellow-100 text-yellow-800
                                                    @else bg-red-100 text-red-800
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
                        <p class="text-gray-500 text-center py-4">Aucune transaction enregistrée</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 