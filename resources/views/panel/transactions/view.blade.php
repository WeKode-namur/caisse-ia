<x-app-layout>
    <div class="lg:py-12">
        <div class="max-w-7xl mx-auto px-0 lg:px-8">
            <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg mb-6">
                <div class="text-gray-900 dark:text-gray-50 px-3 py-2 border-b border-gray-300 dark:border-gray-700 flex items-center justify-between">
                    <h1 class="font-bold lg:text-2xl text-xl">Transactions</h1>
                    <div class="flex items-center space-x-2">
                        <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-download mr-2"></i>Exporter
                        </button>
                    </div>
                </div>
                <div class="p-4" x-data="{
            selectedDateFilter: 'Aujourd\'hui',
            showCustomDate: false,

            updateDateFilter() {
                this.showCustomDate = this.selectedDateFilter === 'Période personnalisée';
            }
        }">
                    {{-- Filtres --}}
                    <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date</label>
                            <select x-model="selectedDateFilter"
                                    @change="updateDateFilter()"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                                <option>Aujourd'hui</option>
                                <option>Cette semaine</option>
                                <option>Ce mois</option>
                                <option>Période personnalisée</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type</label>
                            <select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                                <option value="">Tous</option>
                                <option value="ticket">Ticket</option>
                                <option value="facture">Facture</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Statut</label>
                            <select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                                <option value="">Tous</option>
                                <option value="completed">Payé</option>
                                <option value="cancelled">Annulé</option>
                                <option value="refunded">Remboursé</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Client</label>
                            <input type="text" placeholder="Rechercher un client..."
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Montant min.</label>
                            <input type="number" placeholder="0.00"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Montant max.</label>
                            <input type="number" placeholder="0.00"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400">
                        </div>
                    </div>

                    <!-- Période personnalisée - Apparaît seulement quand sélectionnée -->
                    <div x-show="showCustomDate"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95"
                         class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4 bg-blue-50 p-4 rounded-lg border-l-4 border-blue-500">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date de début</label>
                            <input type="date" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date de fin</label>
                            <input type="date" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                        </div>
                    </div>

                    <div class="flex justify-between items-center">
                        <button class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                            <i class="fas fa-search mr-2"></i>Filtrer
                        </button>
                        <button class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200"
                                @click="selectedDateFilter = 'Aujourd\'hui'; updateDateFilter()">
                            <i class="fas fa-times mr-2"></i>Réinitialiser
                        </button>
                    </div>
                </div>
            </div>

            {{-- Stats --}}
            @php
                $stats = [
                    ['label' => 'Transactions aujourd\'hui', 'value' => fake()->numberBetween(15, 50), 'icon' => 'fas fa-receipt', 'color' => 'blue'],
                    ['label' => 'Chiffre d\'affaires', 'value' => '€ ' . number_format(fake()->randomFloat(2, 500, 3000), 2), 'icon' => 'fas fa-euro-sign', 'color' => 'green'],
                    ['label' => 'Tickets moyens', 'value' => '€ ' . number_format(fake()->randomFloat(2, 15, 85), 2), 'icon' => 'fas fa-chart-line', 'color' => 'purple'],
                    ['label' => 'Clients uniques', 'value' => fake()->numberBetween(8, 25), 'icon' => 'fas fa-users', 'color' => 'orange']
                ];
            @endphp

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                @foreach($stats as $stat)
                    <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg">
                        <div class="p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-2xl font-bold text-{{ $stat['color'] }}-600 dark:text-{{ $stat['color'] }}-400">
                                        {{ $stat['value'] }}
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ $stat['label'] }}</div>
                                </div>
                                <div class="w-12 h-12 bg-{{ $stat['color'] }}-100 dark:bg-{{ $stat['color'] }}-900/30 rounded-lg flex items-center justify-center">
                                    <i class="{{ $stat['icon'] }} text-{{ $stat['color'] }}-600 dark:text-{{ $stat['color'] }}-400"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg mb-6">
                <div class="p-4">
                    {{-- Liste des transactions --}}
                    <div class="overflow-x-auto">
                        <div class="space-y-2">
                            <!-- En-tête -->
                            <div class="hidden md:grid grid-cols-6 gap-4 py-3 px-4 bg-gray-100 dark:bg-gray-700 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300">
                                <div>N° Transaction</div>
                                <div class="text-center">Type</div>
                                <div class="text-center">Client</div>
                                <div class="text-center">Articles</div>
                                <div class="text-center">Montant</div>
                                <div class="text-center">Date/Heure</div>
                            </div>

                            <div class="space-y-2">
                                @php
                                    $transactionTypes = ['ticket', 'facture'];
                                    $statuses = ['completed', 'cancelled', 'refunded'];
                                    $statusLabels = [
                                        'completed' => 'Payé',
                                        'cancelled' => 'Annulé',
                                        'refunded' => 'Remboursé'
                                    ];
                                    $statusColors = [
                                        'completed' => 'green',
                                        'cancelled' => 'red',
                                        'refunded' => 'orange'
                                    ];
                                @endphp

                                @for($i = 1; $i <= 20; $i++)
                                    @php
                                        $transactionType = fake()->randomElement($transactionTypes);
                                        $status = fake()->randomElement($statuses);
                                        $clientTypes = ['company', 'individual', 'anonymous'];
                                        $clientType = fake()->randomElement($clientTypes);
                                        $transactionNumber = $transactionType === 'ticket' ? 'T-2025/' . str_pad($i, 5, '0', STR_PAD_LEFT) : 'F-2025/' . str_pad($i, 5, '0', STR_PAD_LEFT);
                                        $articleCount = fake()->numberBetween(1, 18);
                                        $amount = fake()->randomFloat(2, 5.50, 250.99);
                                        $createdAt = fake()->dateTimeBetween('-90 days', 'now');
                                    @endphp

                                    <div class="transaction-row grid grid-cols-1 md:grid-cols-6 gap-4 py-4 px-4 bg-white dark:bg-gray-800 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors duration-200 border border-gray-200 dark:border-gray-600"
                                         data-transaction-id="{{ $i }}"
                                         onclick="window.location.href='{{ $transactionType === 'ticket' ? route('tickets.show', $i) : route('factures.show', $i) }}'">

                                        <!-- Mobile Layout -->
                                        <div class="md:hidden space-y-3">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center space-x-3">
                                                    <div class="w-10 h-10 bg-{{ $transactionType === 'ticket' ? 'blue' : 'purple' }}-100 dark:bg-{{ $transactionType === 'ticket' ? 'blue' : 'purple' }}-900/30 rounded-lg flex items-center justify-center">
                                                        <i class="fas fa-{{ $transactionType === 'ticket' ? 'ticket' : 'file-invoice' }} text-{{ $transactionType === 'ticket' ? 'blue' : 'purple' }}-600 dark:text-{{ $transactionType === 'ticket' ? 'blue' : 'purple' }}-400 text-sm"></i>
                                                    </div>
                                                    <div>
                                                        <div class="font-medium">{{ $transactionNumber }}</div>
                                                        <div class="text-sm text-gray-500 dark:text-gray-400 capitalize">{{ $transactionType }}</div>
                                                    </div>
                                                </div>
                                                <div class="text-right">
                                                    <div class="font-bold text-lg">€ {{ number_format($amount, 2) }}</div>
                                                    <span class="px-2 py-1 text-xs rounded-full bg-{{ $statusColors[$status] }}-100 text-{{ $statusColors[$status] }}-800 dark:bg-{{ $statusColors[$status] }}-900/30 dark:text-{{ $statusColors[$status] }}-400">
                                                        {{ $statusLabels[$status] }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-400">
                                                <div class="flex items-center space-x-4">
                                                    @if($clientType === 'company')
                                                        <div class="flex items-center">
                                                            <i class="fas fa-briefcase mr-1 text-xs"></i>
                                                            {{ fake()->company() }}
                                                        </div>
                                                    @elseif($clientType === 'individual')
                                                        <div class="flex items-center">
                                                            <i class="fas fa-user mr-1 text-xs"></i>
                                                            {{ fake()->firstName() }} {{ fake()->lastName() }}
                                                        </div>
                                                    @else
                                                        <div class="text-gray-400 dark:text-gray-500">
                                                            /
                                                        </div>
                                                    @endif
                                                    <div class="flex items-center">
                                                        <i class="fas fa-shopping-bag mr-1 text-xs"></i>
                                                        {{ $articleCount }} article{{ $articleCount > 1 ? 's' : '' }}
                                                    </div>
                                                </div>
                                                <div>{{ $createdAt->format('d/m/Y H:i') }}</div>
                                            </div>
                                        </div>

                                        <!-- Desktop Layout -->
                                        <div class="hidden md:flex items-center space-x-3">
                                            <div class="w-12 h-12 bg-{{ $transactionType === 'ticket' ? 'blue' : 'purple' }}-100 dark:bg-{{ $transactionType === 'ticket' ? 'blue' : 'purple' }}-900/30 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-{{ $transactionType === 'ticket' ? 'ticket' : 'file-invoice' }} text-{{ $transactionType === 'ticket' ? 'blue' : 'purple' }}-600 dark:text-{{ $transactionType === 'ticket' ? 'blue' : 'purple' }}-400"></i>
                                            </div>
                                            <div>
                                                <div class="font-medium">{{ $transactionNumber }}</div>
                                            </div>
                                        </div>

                                        <div class="hidden md:flex items-center justify-center">
                                            <span class="px-3 py-1 text-sm rounded-full capitalize bg-{{ $transactionType === 'ticket' ? 'blue' : 'purple' }}-100 text-{{ $transactionType === 'ticket' ? 'blue' : 'purple' }}-800 dark:bg-{{ $transactionType === 'ticket' ? 'blue' : 'purple' }}-900/30 dark:text-{{ $transactionType === 'ticket' ? 'blue' : 'purple' }}-400">
                                                {{ $transactionType }}
                                            </span>
                                        </div>

                                        <div class="hidden md:flex items-center justify-center">
                                            @if($clientType === 'company')
                                                <div class="flex items-center">
                                                    <i class="fas fa-briefcase mr-2 text-gray-400 text-sm"></i>
                                                    <span class="text-sm">{{ fake()->company() }}</span>
                                                </div>
                                            @elseif($clientType === 'individual')
                                                <div class="flex items-center">
                                                    <i class="fas fa-user mr-2 text-gray-400 text-sm"></i>
                                                    <span class="text-sm">{{ fake()->firstName() }} {{ fake()->lastName() }}</span>
                                                </div>
                                            @else
                                                <div class="text-gray-400 dark:text-gray-500">
                                                    /
                                                </div>
                                            @endif
                                        </div>

                                        <div class="hidden md:flex items-center justify-center">
                                            <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded-full text-sm">
                                                {{ $articleCount }}
                                            </span>
                                        </div>

                                        <div class="hidden md:flex items-center justify-center font-bold text-lg">
                                            € {{ number_format($amount, 2) }}
                                        </div>

                                        <div class="hidden md:flex items-center justify-between">
                                            <div class="text-center">
                                                <p class="text-sm">{{ $createdAt->format('d/m/Y') }}</p>
                                                <small class="text-gray-400">{{ $createdAt->format('H:i') }}</small>
                                            </div>

                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </div>
                                    </div>
                                @endfor
                            </div>
                        </div>
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-6 flex items-center justify-between">
                        <div class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400">
                            <span>Affichage de 1 à 20 sur 156 résultats</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50" disabled>
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button class="px-3 py-2 bg-blue-600 text-white rounded-lg">1</button>
                            <button class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700">2</button>
                            <button class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700">3</button>
                            <span class="px-3 py-2 text-gray-400">...</span>
                            <button class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700">8</button>
                            <button class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
