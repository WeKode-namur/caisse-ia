<x-app-layout>
    <div class="lg:py-12">
        <div class="max-w-7xl mx-auto px-0 lg:px-8">
            <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg mb-6">
                <div class="text-gray-900 dark:text-gray-50 px-3 py-2 border-b border-gray-300 dark:border-gray-700 flex items-center justify-between">
                    <h1 class="font-bold lg:text-2xl text-xl">Transactions</h1>
                </div>
                <div class="p-4" x-data="{
            selectedDateFilter: '{{ request('date_filter', 'today') }}',
            showCustomDate: {{ request('date_filter') === 'custom' ? 'true' : 'false' }},

            updateDateFilter() {
                this.showCustomDate = this.selectedDateFilter === 'custom';
            }
        }">
                    {{-- Filtres --}}
                    <form method="GET" action="{{ route('transactions') }}">
                        <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date</label>
                                <select name="date_filter" x-model="selectedDateFilter"
                                        @change="updateDateFilter()"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                                    <option value="today">Aujourd'hui</option>
                                    <option value="week">Cette semaine</option>
                                    <option value="month">Ce mois</option>
                                    <option value="custom">Période personnalisée</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type</label>
                                <select name="type" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                                    <option value="">Tous</option>
                                    <option value="ticket" {{ request('type') === 'ticket' ? 'selected' : '' }}>Ticket</option>
                                    <option value="facture" {{ request('type') === 'facture' ? 'selected' : '' }}>Facture</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Statut</label>
                                <select name="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                                    <option value="">Tous</option>
                                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Payé</option>
                                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Annulé</option>
                                    <option value="refunded" {{ request('status') === 'refunded' ? 'selected' : '' }}>Remboursé</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Client</label>
                                <input type="text" name="client" value="{{ request('client') }}" placeholder="Rechercher un client..."
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Montant min.</label>
                                <input type="number" name="min_amount" value="{{ request('min_amount') }}" step="0.01" placeholder="0.00"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Montant max.</label>
                                <input type="number" name="max_amount" value="{{ request('max_amount') }}" step="0.01" placeholder="0.00"
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
                                <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date de fin</label>
                                <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                            </div>
                        </div>

                        <div class="flex justify-between items-center">
                            <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                                <i class="fas fa-search mr-2"></i>Filtrer
                            </button>
                            <a href="{{ route('transactions') }}" class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                                <i class="fas fa-times mr-2"></i>Réinitialiser
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Stats --}}
            <div id="transactions-stats-wrapper" class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
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
                    <div id="transactions-table-wrapper">
                        @include('panel.transactions._table', ['transactions' => $transactions])
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
