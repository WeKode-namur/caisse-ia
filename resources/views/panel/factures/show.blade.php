<x-app-layout>
    @php
        // Helper function pour formater les nombres sans zéros inutiles
        function formatNumber($number, $decimals = 2, $dec_point = ',', $thousands_sep = ' ') {
            return rtrim(rtrim(number_format($number, $decimals, $dec_point, $thousands_sep), '0'), $dec_point);
        }

        // Génération des données faker pour la facture
        $factureNumber = 'F-2025/' . str_pad(fake()->numberBetween(2, 12354), 6, '0', STR_PAD_LEFT);
        $createdAt = fake()->dateTimeBetween('-30 days', 'now');
        $cashier = fake()->name();

        // Pour les factures, le client est OBLIGATOIRE (entreprise ou particulier)
        $clientTypes = ['company', 'individual'];
        $clientType = fake()->randomElement($clientTypes);

        // Données client complètes pour facture
        if($clientType === 'company') {
            $clientData = [
                'name' => fake()->company(),
                'email' => fake()->companyEmail(),
                'phone' => fake()->phoneNumber(),
                'address' => fake()->streetAddress(),
                'city' => fake()->city(),
                'postal_code' => fake()->postcode(),
                'country' => 'Belgique',
                'vat_number' => 'BE' . fake()->numerify('##########'),
                'company_number' => fake()->numerify('BE ##########')
            ];
        } else {
            $clientData = [
                'name' => fake()->firstName() . ' ' . fake()->lastName(),
                'email' => fake()->email(),
                'phone' => fake()->phoneNumber(),
                'address' => fake()->streetAddress(),
                'city' => fake()->city(),
                'postal_code' => fake()->postcode(),
                'country' => 'Belgique'
            ];
        }

        // Articles de la facture
        $items = [];
        $itemCount = fake()->numberBetween(1, 8);
        $subtotalHT = 0;
        $totalTVA = 0;

        for($i = 1; $i <= $itemCount; $i++) {
            $quantity = fake()->numberBetween(1, 4);
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

        // Paiements (pour factures, souvent virement ou à crédit)
        $paymentMethods = ['transfer', 'card', 'credit'];
        $paymentMethod = fake()->randomElement($paymentMethods);
        $paymentLabels = [
            'transfer' => 'Virement bancaire',
            'card' => 'Bancontact',
            'credit' => 'À crédit (30 jours)'
        ];

        // Échéance pour les factures
        $dueDate = (clone $createdAt)->add(new DateInterval('P30D'));

        // Notes optionnelles
        $hasNotes = fake()->boolean(20);
        $notes = $hasNotes ? fake()->sentence(fake()->numberBetween(5, 15)) : null;

        // Communication structurée belge
        function generateStructuredCommunication($factureNumber) {
            // Extraire la partie numérique du numéro de facture (F-2025/000002 -> 2025000002)
            $cleanNumber = preg_replace('/[^0-9]/', '', $factureNumber);

            // S'assurer qu'on a exactement 10 chiffres
            $baseNumber = str_pad($cleanNumber, 10, '0', STR_PAD_LEFT);

            // Calculer la clé de contrôle (modulo 97)
            $checksum = intval($baseNumber) % 97;

            // Si le résultat est 0, on utilise 97
            if ($checksum == 0) {
                $checksum = 97;
            }

            // Formater avec des zéros à gauche pour avoir 2 chiffres
            $checksumFormatted = str_pad($checksum, 2, '0', STR_PAD_LEFT);

            // Assembler la communication structurée
            $fullNumber = $baseNumber . $checksumFormatted;

            // Formater avec des slashes : XXX/XXXX/XXXXX
            return '+++' . substr($fullNumber, 0, 3) . '/' . substr($fullNumber, 3, 4) . '/' . substr($fullNumber, 7, 5) . '+++';
        }

        $structuredCommunication = generateStructuredCommunication($factureNumber);

        // Informations légales de l'entreprise émettrice
        $companyInfo = [
            'name' => 'WeKode SPRL',
            'address' => 'Rue de l\'Innovation 123',
            'city' => '5000 Namur',
            'country' => 'Belgique',
            'vat_number' => 'BE 0123.456.789',
            'company_number' => 'RPM Namur 0123.456.789',
            'iban' => 'BE68 5390 0754 7034'
        ];
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
                            <h1 class="font-bold lg:text-2xl text-xl">Facture {{ $factureNumber }}</h1>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $createdAt->format('d/m/Y à H:i') }} - Échéance: {{ $dueDate->format('d/m/Y') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button class="bg-blue-500 dark:bg-blue-800 hover:opacity-75 hover:scale-105 duration-500 text-white rounded-lg px-4 py-2">
                            <i class="fas fa-print"></i>
                        </button>
                        <button class="bg-green-500 dark:bg-green-800 hover:opacity-75 hover:scale-105 duration-500 text-white rounded-lg px-4 py-2">
                            <i class="fas fa-envelope"></i>
                        </button>
                        <button class="bg-purple-500 dark:bg-purple-800 hover:opacity-75 hover:scale-105 duration-500 text-white rounded-lg px-4 py-2">
                            <i class="fas fa-download"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Colonne principale - Détails de la facture --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Informations entreprise et client --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Entreprise émettrice --}}
                        <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg">
                            <div class="text-gray-900 dark:text-gray-50 px-4 py-3 border-b border-gray-300 dark:border-gray-700">
                                <h2 class="font-semibold text-lg flex items-center">
                                    <i class="fas fa-building mr-2 text-blue-600"></i>Émetteur
                                </h2>
                            </div>
                            <div class="p-4">
                                <div class="space-y-2">
                                    <p class="font-bold text-lg">{{ $companyInfo['name'] }}</p>
                                    <p class="text-gray-600 dark:text-gray-400">{{ $companyInfo['address'] }}</p>
                                    <p class="text-gray-600 dark:text-gray-400">{{ $companyInfo['city'] }}</p>
                                    <p class="text-gray-600 dark:text-gray-400">{{ $companyInfo['country'] }}</p>
                                    <div class="pt-2 border-t border-gray-200 dark:border-gray-600">
                                        <p class="text-sm text-gray-500 dark:text-gray-400">TVA: {{ $companyInfo['vat_number'] }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $companyInfo['company_number'] }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Client facturé --}}
                        <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg">
                            <div class="text-gray-900 dark:text-gray-50 px-4 py-3 border-b border-gray-300 dark:border-gray-700">
                                <h2 class="font-semibold text-lg flex items-center">
                                    @if($clientType === 'company')
                                        <i class="fas fa-briefcase mr-2 text-purple-600"></i>Client entreprise
                                    @else
                                        <i class="fas fa-user mr-2 text-green-600"></i>Client particulier
                                    @endif
                                </h2>
                            </div>
                            <div class="p-4">
                                <div class="space-y-2">
                                    <p class="font-bold text-lg">{{ $clientData['name'] }}</p>
                                    <p class="text-gray-600 dark:text-gray-400">{{ $clientData['address'] }}</p>
                                    <p class="text-gray-600 dark:text-gray-400">{{ $clientData['postal_code'] }} {{ $clientData['city'] }}</p>
                                    <p class="text-gray-600 dark:text-gray-400">{{ $clientData['country'] }}</p>
                                    <div class="pt-2 border-t border-gray-200 dark:border-gray-600">
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $clientData['email'] }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $clientData['phone'] }}</p>
                                        @if($clientType === 'company')
                                            <p class="text-sm text-gray-500 dark:text-gray-400">TVA: {{ $clientData['vat_number'] }}</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $clientData['company_number'] }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Informations facture --}}
                    <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg">
                        <div class="text-gray-900 dark:text-gray-50 px-4 py-3 border-b border-gray-300 dark:border-gray-700">
                            <h2 class="font-semibold text-lg">Informations facture</h2>
                        </div>
                        <div class="p-4">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Numéro de facture</label>
                                    <p class="text-lg font-mono bg-purple-50 dark:bg-purple-900/20 text-purple-800 dark:text-purple-300 px-3 py-2 rounded border border-purple-200 dark:border-purple-800">{{ $factureNumber }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date d'émission</label>
                                    <p class="text-lg bg-gray-100 dark:bg-gray-700 px-3 py-2 rounded">{{ $createdAt->format('d/m/Y') }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date d'échéance</label>
                                    <p class="text-lg bg-orange-50 dark:bg-orange-900/20 text-orange-800 dark:text-orange-300 px-3 py-2 rounded border border-orange-200 dark:border-orange-800">{{ $dueDate->format('d/m/Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Articles facturés --}}
                    <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg">
                        <div class="text-gray-900 dark:text-gray-50 px-4 py-3 border-b border-gray-300 dark:border-gray-700">
                            <h2 class="font-semibold text-lg">Articles facturés ({{ count($items) }})</h2>
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
                                        <th class="text-right py-3 px-2 font-medium text-gray-700 dark:text-gray-300">Total HT</th>
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
                                            <td class="py-3 px-2 text-right font-mono">€ {{ formatNumber($item['total_price_ht']) }}</td>
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

                    {{-- Informations légales --}}
                    <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg">
                        <div class="text-gray-900 dark:text-gray-50 px-4 py-3 border-b border-gray-300 dark:border-gray-700">
                            <h2 class="font-semibold text-lg">Informations légales</h2>
                        </div>
                        <div class="p-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600 dark:text-gray-400">
                                <div>
                                    <p><strong>Conditions de paiement:</strong> 30 jours net</p>
                                    <p><strong>IBAN:</strong> {{ $companyInfo['iban'] }}</p>
                                    <p><strong>Communication:</strong> {{ $structuredCommunication }}</p>
                                </div>
                                <div>
                                    <p><strong>En cas de retard de paiement:</strong></p>
                                    <p>Intérêts de retard: 12% l'an</p>
                                    <p>Indemnité forfaitaire: 40€ minimum</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sidebar - Totaux et Paiement --}}
                <div class="space-y-6">

                    {{-- Totaux --}}
                    <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg">
                        <div class="text-gray-900 dark:text-gray-50 px-4 py-3 border-b border-gray-300 dark:border-gray-700">
                            <h2 class="font-semibold text-lg">Totaux facture</h2>
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
                                <span>Total à payer</span>
                                <span class="font-mono text-purple-600 dark:text-purple-400">€ {{ formatNumber($finalTotal) }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Paiement --}}
                    <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg">
                        <div class="text-gray-900 dark:text-gray-50 px-4 py-3 border-b border-gray-300 dark:border-gray-700">
                            <h2 class="font-semibold text-lg">Modalités de paiement</h2>
                        </div>
                        <div class="p-4">
                            @if($paymentMethod === 'credit')
                                <div class="flex items-center justify-between p-3 bg-orange-50 dark:bg-orange-900/20 rounded-lg border border-orange-200 dark:border-orange-800">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/30 rounded-full flex items-center justify-center mr-3">
                                            <i class="fas fa-clock text-orange-600 dark:text-orange-400"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium text-orange-800 dark:text-orange-300">{{ $paymentLabels[$paymentMethod] }}</p>
                                            <p class="text-sm text-orange-600 dark:text-orange-400">Échéance: {{ $dueDate->format('d/m/Y') }}</p>
                                        </div>
                                    </div>
                                    <span class="font-mono font-bold text-orange-800 dark:text-orange-300">€ {{ formatNumber($finalTotal) }}</span>
                                </div>
                            @else
                                <div class="flex items-center justify-between p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mr-3">
                                            <i class="fas fa-{{ $paymentMethod === 'transfer' ? 'university' : 'credit-card' }} text-green-600 dark:text-green-400"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium text-green-800 dark:text-green-300">{{ $paymentLabels[$paymentMethod] }}</p>
                                            <p class="text-sm text-green-600 dark:text-green-400">Paiement reçu</p>
                                        </div>
                                    </div>
                                    <span class="font-mono font-bold text-green-800 dark:text-green-300">€ {{ formatNumber($finalTotal) }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Statut --}}
                    <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg">
                        <div class="text-gray-900 dark:text-gray-50 px-4 py-3 border-b border-gray-300 dark:border-gray-700">
                            <h2 class="font-semibold text-lg">Statut facture</h2>
                        </div>
                        <div class="p-4">
                            @if($paymentMethod === 'credit')
                                <div class="flex items-center justify-center p-3 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
                                    <i class="fas fa-hourglass-half text-orange-600 dark:text-orange-400 text-xl mr-3"></i>
                                    <span class="font-medium text-orange-800 dark:text-orange-300">En attente de paiement</span>
                                </div>
                            @else
                                <div class="flex items-center justify-center p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                    <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-xl mr-3"></i>
                                    <span class="font-medium text-green-800 dark:text-green-300">Facture payée</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Communication de paiement --}}
                    <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg">
                        <div class="text-gray-900 dark:text-gray-50 px-4 py-3 border-b border-gray-300 dark:border-gray-700">
                            <h2 class="font-semibold text-lg">Virement bancaire</h2>
                        </div>
                        <div class="p-4 space-y-2">
                            <div class="bg-blue-50 dark:bg-blue-900/20 p-3 rounded border border-blue-200 dark:border-blue-800">
                                <p class="text-sm text-blue-800 dark:text-blue-300 font-medium">IBAN: {{ $companyInfo['iban'] }}</p>
                                <p class="text-sm text-blue-600 dark:text-blue-400">Communication: {{ $structuredCommunication }}</p>
                                <p class="text-sm text-blue-600 dark:text-blue-400">Montant: € {{ formatNumber($finalTotal) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
