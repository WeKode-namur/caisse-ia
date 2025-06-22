<x-app-layout>
    <div class="lg:py-12">
        <div class="max-w-7xl mx-auto px-0 lg:px-8">
            <!-- Header -->
            <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg mb-6">
                <div class="text-gray-900 dark:text-gray-50 px-3 py-2 border-b border-gray-300 dark:border-gray-700 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                            <i class="fab fa-wix text-purple-600 dark:text-purple-400 text-lg"></i>
                        </div>
                        <h1 class="font-bold lg:text-2xl text-xl">Sorties Wix</h1>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button class="px-4 py-2 bg-blue-600 dark:bg-blue-800 hover:opacity-75 text-white rounded-lg hover:scale-105 duration-500">
                            <i class="fas fa-download mr-2"></i>Exporter
                        </button>
                    </div>
                </div>

                <!-- Filtres -->
                <div class="p-4" x-data="{
                    selectedDateFilter: 'Aujourd\'hui',
                    showCustomDate: false,

                    updateDateFilter() {
                        this.showCustomDate = this.selectedDateFilter === 'Période personnalisée';
                    }
                }">
                    <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date de sortie</label>
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
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Catégorie</label>
                            <select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                                <option value="">Toutes</option>
                                <option value="electronique">Électronique</option>
                                <option value="vetement">Vêtement</option>
                                <option value="maison">Maison & Jardin</option>
                                <option value="sport">Sport & Loisirs</option>
                                <option value="beaute">Beauté & Santé</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Période</label>
                            <select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                                <option value="">Toutes</option>
                                <option value="morning">Matin (08h-12h)</option>
                                <option value="afternoon">Après-midi (12h-18h)</option>
                                <option value="evening">Soir (18h-22h)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Article / Code-barre</label>
                            <input type="text" placeholder="Nom ou code-barre..."
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

                    <!-- Période personnalisée -->
                    <div x-show="showCustomDate"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95"
                         class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4 bg-purple-50 p-4 rounded-lg border-l-4 border-purple-500">
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
                    ['label' => 'Sorties aujourd\'hui', 'value' => fake()->numberBetween(80, 150), 'icon' => 'fas fa-arrow-up', 'color' => 'purple'],
                    ['label' => 'Valeur totale sortie', 'value' => '€ ' . number_format(fake()->randomFloat(2, 2000, 5000), 2), 'icon' => 'fas fa-euro-sign', 'color' => 'green'],
                    ['label' => 'Articles différents', 'value' => fake()->numberBetween(30, 80), 'icon' => 'fas fa-box', 'color' => 'blue'],
                    ['label' => 'Valeur moyenne', 'value' => '€ ' . number_format(fake()->randomFloat(2, 15, 85), 2), 'icon' => 'fas fa-chart-line', 'color' => 'orange']
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

            {{-- Liste des sorties --}}
            <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg mb-6">
                <div class="p-4">
                    <div class="overflow-x-auto">
                        <div class="space-y-2">
                            <!-- En-tête -->
                            <div class="hidden md:grid grid-cols-6 gap-4 py-3 px-4 bg-gray-100 dark:bg-gray-700 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300">
                                <div>Article</div>
                                <div class="text-center">Code-barre</div>
                                <div class="text-center">Catégorie</div>
                                <div class="text-center">Quantité</div>
                                <div class="text-center">Prix unitaire</div>
                                <div class="text-center">Total</div>
                            </div>

                            <div class="space-y-2">
                                @php
                                    $categories = ['Électronique', 'Vêtement', 'Maison & Jardin', 'Sport & Loisirs', 'Beauté & Santé'];
                                @endphp

                                @for($i = 1; $i <= 25; $i++)
                                    @php
                                        $category = fake()->randomElement($categories);
                                        $articleName = fake()->words(2, true);
                                        $barcode = fake()->ean13();
                                        $quantity = fake()->numberBetween(1, 10);
                                        $unitPrice = fake()->randomFloat(2, 5, 200);
                                        $total = $quantity * $unitPrice;
                                        $createdAt = fake()->dateTimeBetween('-30 days', 'now');
                                    @endphp

                                    <div class="wix-release-row grid grid-cols-1 md:grid-cols-6 gap-4 py-4 px-4 bg-white dark:bg-gray-800 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors duration-200 border border-gray-200 dark:border-gray-600">

                                        <!-- Mobile Layout -->
                                        <div class="md:hidden space-y-3">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center space-x-3">
                                                    <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                                                        <i class="fab fa-wix text-purple-600 dark:text-purple-400 text-sm"></i>
                                                    </div>
                                                    <div>
                                                        <div class="font-medium capitalize">{{ $articleName }}</div>
                                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $barcode }}</div>
                                                    </div>
                                                </div>
                                                <div class="text-right">
                                                    <div class="font-bold text-lg">€ {{ number_format($total, 2) }}</div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $createdAt->format('d/m/Y H:i') }}</div>
                                                </div>
                                            </div>
                                            <div class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-400">
                                                <div class="flex items-center space-x-4">
                                                    <div class="flex items-center">
                                                        <i class="fas fa-tag mr-1 text-xs"></i>
                                                        {{ $category }}
                                                    </div>
                                                    <div class="flex items-center">
                                                        <i class="fas fa-boxes mr-1 text-xs"></i>
                                                        {{ $quantity }} × €{{ number_format($unitPrice, 2) }}
                                                    </div>
                                                </div>
                                                <div>{{ $createdAt->format('d/m/Y H:i') }}</div>
                                            </div>
                                        </div>

                                        <!-- Desktop Layout -->
                                        <div class="hidden md:flex items-center space-x-3">
                                            <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                                                <i class="fab fa-wix text-purple-600 dark:text-purple-400"></i>
                                            </div>
                                            <div>
                                                <div class="font-medium capitalize">{{ $articleName }}</div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">REF-{{ str_pad($i, 4, '0', STR_PAD_LEFT) }}</div>
                                            </div>
                                        </div>

                                        <div class="hidden md:flex items-center justify-center">
                                            <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-sm font-mono">
                                                {{ $barcode }}
                                            </span>
                                        </div>

                                        <div class="hidden md:flex items-center justify-center">
                                            <span class="px-3 py-1 text-sm rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                                {{ $category }}
                                            </span>
                                        </div>

                                        <div class="hidden md:flex items-center justify-center">
                                            <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded-full text-sm font-bold">
                                                {{ $quantity }}
                                            </span>
                                        </div>

                                        <div class="hidden md:flex items-center justify-center font-medium">
                                            € {{ number_format($unitPrice, 2) }}
                                        </div>

                                        <div class="hidden md:flex items-center justify-center font-bold text-lg">
                                            € {{ number_format($total, 2) }}
                                        </div>
                                    </div>
                                @endfor
                            </div>
                        </div>
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-6 flex items-center justify-between">
                        <div class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400">
                            <span>Affichage de 1 à 25 sur 247 résultats</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50" disabled>
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button class="px-3 py-2 bg-purple-600 text-white rounded-lg">1</button>
                            <button class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700">2</button>
                            <button class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700">3</button>
                            <span class="px-3 py-2 text-gray-400">...</span>
                            <button class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700">10</button>
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
