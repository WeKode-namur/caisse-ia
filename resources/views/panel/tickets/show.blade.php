<x-app-layout>
    @php
        // Helper function pour formater les nombres sans zéros inutiles
        function formatNumber($number, $decimals = 2, $dec_point = ',', $thousands_sep = ' ') {
            return rtrim(rtrim(number_format($number, $decimals, $dec_point, $thousands_sep), '0'), $dec_point);
        }

        // Génération des données faker pour le ticket
        $ticketNumber = 'T-2025/' . str_pad($id, 6, '0', STR_PAD_LEFT);
        $createdAt = fake()->dateTimeBetween('-30 days', 'now');
        $cashier = fake()->name();

        // Type de client
        $clientTypes = ['company', 'individual', 'anonymous'];
        $clientType = fake()->randomElement($clientTypes);

        // Articles du ticket
        $items = [];
        $itemCount = fake()->numberBetween(1, 8);
        $subtotalHT = 0;
        $totalTVA = 0;

        for($i = 1; $i <= $itemCount; $i++) {
            $quantity = fake()->numberBetween(4, 1);
            $unitPriceHT = fake()->randomFloat(2, 3, 45);
            $tvaRate = fake()->randomElement([6, 12, 21]);
            $unitPriceTTC = $unitPriceHT * (1 + $tvaRate/100);
            $totalPriceHT = $unitPriceHT * $quantity;
            $totalPriceTTC = $unitPriceTTC * $quantity;
            $tvaAmount = $totalPriceTTC - $totalPriceHT;

            $items[] = [
                'name' => fake()->words(fake()->numberBetween(1, 3), true),
                'barcode' => fake()->ean13(),
                'quantity' => $quantity,
                'unit_price_ht' => $unitPriceHT,
                'unit_price_ttc' => $unitPriceTTC,
                'total_price_ht' => $totalPriceHT,
                'total_price_ttc' => $totalPriceTTC,
                'tva_rate' => $tvaRate,
                'tva_amount' => $tvaAmount,
                'attributes' => fake()->boolean(30) ? fake()->randomElements(['S', 'M', 'L', 'Rouge', 'Bleu', '500ml'], fake()->numberBetween(1, 2)) : []
            ];

            $subtotalHT += $totalPriceHT;
            $totalTVA += $tvaAmount;
        }

        // Remises
        $hasDiscount = fake()->boolean(25);
        $discountAmount = $hasDiscount ? fake()->randomFloat(2, 2, 15) : 0;

        // Totaux
        $subtotalTTC = $subtotalHT + $totalTVA;
        $finalTotal = $subtotalTTC - $discountAmount;

        // Paiements
        $paymentMethods = ['cash', 'card', 'transfer'];
        $paymentMethod = fake()->randomElement($paymentMethods);
        $paymentLabels = [
            'cash' => 'Espèces',
            'card' => 'Bancontact',
            'transfer' => 'Virement'
        ];

        // Notes optionnelles
        $hasNotes = fake()->boolean(20);
        $notes = $hasNotes ? fake()->sentence(fake()->numberBetween(5, 15)) : null;
    @endphp

    <div class="lg:py-12">
        <div class="max-w-7xl mx-auto px-0 lg:px-8">

            {{-- Header avec actions --}}
            <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg mb-6">
                <div class="text-gray-900 dark:text-gray-50 px-4 py-3 flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('transactions') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 hover:scale-105 duration-500 px-1.5">
                            <i class="fas fa-arrow-left text-lg"></i>
                        </a>
                        <div>
                            <h1 class="font-bold lg:text-2xl text-xl">Ticket {{ $ticketNumber }}</h1>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $createdAt->format('d/m/Y à H:i') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button class="bg-blue-500 dark:bg-blue-800 hover:opacity-75 hover:scale-105 duration-500 text-white rounded-lg px-4 py-2">
                            <i class="fas fa-print"></i>
                        </button>
                        <button class="bg-green-500 dark:bg-green-800 hover:opacity-75 hover:scale-105 duration-500 text-white rounded-lg px-4 py-2">
                            <i class="fas fa-envelope"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Colonne principale - Détails du ticket --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Informations générales --}}
                    <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg">
                        <div class="text-gray-900 dark:text-gray-50 px-4 py-3 border-b border-gray-300 dark:border-gray-700">
                            <h2 class="font-semibold text-lg">Informations générales</h2>
                        </div>
                        <div class="p-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Numéro de ticket</label>
                                    <p class="text-lg font-mono bg-gray-100 dark:bg-gray-700 px-3 py-2 rounded">{{ $ticketNumber }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date et heure</label>
                                    <p class="text-lg bg-gray-100 dark:bg-gray-700 px-3 py-2 rounded">{{ $createdAt->format('d/m/Y à H:i:s') }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Caissier</label>
                                    <p class="text-lg bg-gray-100 dark:bg-gray-700 px-3 py-2 rounded flex items-center">
                                        <i class="fas fa-user mr-2 text-gray-500"></i>{{ $cashier }}
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Client</label>
                                    <p class="text-lg bg-gray-100 dark:bg-gray-700 px-3 py-2 rounded flex items-center">
                                        @if($clientType === 'company')
                                            <i class="fas fa-briefcase mr-2 text-gray-500"></i>{{ fake()->company() }}
                                        @elseif($clientType === 'individual')
                                            <i class="fas fa-user mr-2 text-gray-500"></i>{{ fake()->firstName() }} {{ fake()->lastName() }}
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">Non défini</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Articles vendus --}}
                    <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg">
                        <div class="text-gray-900 dark:text-gray-50 px-4 py-3 border-b border-gray-300 dark:border-gray-700">
                            <h2 class="font-semibold text-lg">Articles vendus ({{ count($items) }})</h2>
                        </div>
                        <div class="p-4">
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                    <tr class="border-b border-gray-200 dark:border-gray-700">
                                        <th class="text-left py-3 px-2 font-medium text-gray-700 dark:text-gray-300">Article</th>
                                        <th class="text-center py-3 px-2 font-medium text-gray-700 dark:text-gray-300">Qté</th>
                                        <th class="text-right py-3 px-2 font-medium text-gray-700 dark:text-gray-300">P.U. HT</th>
                                        <th class="text-right py-3 px-2 font-medium text-gray-700 dark:text-gray-300">P.U. TTC</th>
                                        <th class="text-center py-3 px-2 font-medium text-gray-700 dark:text-gray-300">TVA</th>
                                        <th class="text-right py-3 px-2 font-medium text-gray-700 dark:text-gray-300">Total TTC</th>
                                    </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($items as $item)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                            <td class="py-3 px-2 flex items-center gap-4">
                                                    <div class="rounded bg-gradient-to-tr from-purple-300 dark:from-purple-800 to-blue-100 dark:to-blue-800 text-purple-500 dark:text-cyan-600 w-8 h-8 flex items-center justify-center">
                                                        <i class="fas fa-t-shirt text-lg"></i>
                                                    </div>
                                                <div>
                                                    <p class="font-medium">{{ ucfirst($item['name']) }}</p>
                                                    <p class="text-sm text-gray-500 font-mono">{{ $item['barcode'] }}</p>
                                                    @if(!empty($item['attributes']))
                                                        <div class="flex flex-wrap gap-1 mt-1">
                                                            @foreach($item['attributes'] as $attr)
                                                                <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-400 text-xs rounded">{{ $attr }}</span>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="py-3 px-2 text-center font-mono">{{ formatNumber($item['quantity']) }}</td>
                                            <td class="py-3 px-2 text-right font-mono">€ {{ formatNumber($item['unit_price_ht']) }}</td>
                                            <td class="py-3 px-2 text-right font-mono">€ {{ formatNumber($item['unit_price_ttc']) }}</td>
                                            <td class="py-3 px-2 text-center">
                                                <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-sm">{{ $item['tva_rate'] }}%</span>
                                            </td>
                                            <td class="py-3 px-2 text-right font-mono font-medium">€ {{ formatNumber($item['total_price_ttc']) }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Notes (si présentes) --}}
                    @if($notes)
                        <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg">
                            <div class="text-gray-900 dark:text-gray-50 px-4 py-3 border-b border-gray-300 dark:border-gray-700">
                                <h2 class="font-semibold text-lg">Notes</h2>
                            </div>
                            <div class="p-4">
                                <p class="text-gray-700 dark:text-gray-300 bg-yellow-50 dark:bg-yellow-900/20 p-3 rounded-lg border-l-4 border-yellow-400">
                                    <i class="fas fa-sticky-note mr-2 text-yellow-600"></i>{{ $notes }}
                                </p>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Sidebar - Totaux et Paiement --}}
                <div class="space-y-6">

                    {{-- Totaux --}}
                    <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg">
                        <div class="text-gray-900 dark:text-gray-50 px-4 py-3 border-b border-gray-300 dark:border-gray-700">
                            <h2 class="font-semibold text-lg">Totaux</h2>
                        </div>
                        <div class="p-4 space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 dark:text-gray-400">Sous-total HT</span>
                                <span class="font-mono">€ {{ formatNumber($subtotalHT) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 dark:text-gray-400">TVA</span>
                                <span class="font-mono">€ {{ formatNumber($totalTVA) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 dark:text-gray-400">Sous-total TTC</span>
                                <span class="font-mono">€ {{ formatNumber($subtotalTTC) }}</span>
                            </div>
                            @if($hasDiscount)
                                <div class="flex justify-between items-center text-red-600 dark:text-red-400">
                                    <span>Remise</span>
                                    <span class="font-mono">- € {{ formatNumber($discountAmount) }}</span>
                                </div>
                            @endif
                            <hr class="border-gray-300 dark:border-gray-600">
                            <div class="flex justify-between items-center text-lg font-bold">
                                <span>Total final</span>
                                <span class="font-mono text-green-600 dark:text-green-400">€ {{ formatNumber($finalTotal) }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Paiement --}}
                    <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg">
                        <div class="text-gray-900 dark:text-gray-50 px-4 py-3 border-b border-gray-300 dark:border-gray-700">
                            <h2 class="font-semibold text-lg">Paiement</h2>
                        </div>
                        <div class="p-4">
                            <div class="flex items-center justify-between p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-{{ $paymentMethod === 'cash' ? 'money-bill' : ($paymentMethod === 'card' ? 'credit-card' : 'university') }} text-green-600 dark:text-green-400"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-green-800 dark:text-green-300">{{ $paymentLabels[$paymentMethod] }}</p>
                                        <p class="text-sm text-green-600 dark:text-green-400">Paiement validé</p>
                                    </div>
                                </div>
                                <span class="font-mono font-bold text-green-800 dark:text-green-300">€ {{ formatNumber($finalTotal) }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Statut --}}
                    <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg">
                        <div class="text-gray-900 dark:text-gray-50 px-4 py-3 border-b border-gray-300 dark:border-gray-700">
                            <h2 class="font-semibold text-lg">Statut</h2>
                        </div>
                        <div class="p-4">
                            <div class="flex items-center justify-center p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-xl mr-3"></i>
                                <span class="font-medium text-green-800 dark:text-green-300">Transaction terminée</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
