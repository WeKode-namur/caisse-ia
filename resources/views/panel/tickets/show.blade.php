<x-app-layout>
    @php
        // Helper function pour formater les nombres sans zéros inutiles
        function formatNumber($number, $decimals = 2, $dec_point = ',', $thousands_sep = ' ') {
            return rtrim(rtrim(number_format($number, $decimals, $dec_point, $thousands_sep), '0'), $dec_point);
        }
        // Helper function d'arrondi belge (0,05€)
        function belgianRound($amount) {
            return round($amount * 20) / 20;
        }
        $arrondissementEnabled = config('custom.register.arrondissementMethod');

        // Helper function pour traduire les statuts
        function translateStatus($status) {
            $translations = [
                'paid' => 'Payé',
                'pending' => 'En attente',
                'cancelled' => 'Annulé',
                'refunded' => 'Remboursé',
                'failed' => 'Échoué',
                'processing' => 'En cours',
                'completed' => 'Terminé',
                'active' => 'Actif',
                'inactive' => 'Inactif',
                'draft' => 'Brouillon',
                'published' => 'Publié',
                'archived' => 'Archivé'
            ];

            return $translations[strtolower($status)] ?? ucfirst($status);
        }
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
                            <h1 class="font-bold lg:text-2xl text-xl">Ticket {{ $transaction->reference }}</h1>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $transaction->created_at->format('d/m/Y à H:i') }}</p>
                        </div>
                    </div>
                    <div class="flex space-x-3">
                        <button onclick="printTicketDirect()" class="bg-blue-500 dark:bg-blue-800 hover:opacity-75 hover:scale-105 duration-500 text-white rounded-lg px-4 py-2">
                            <i class="fas fa-print mr-2"></i>Imprimer
                        </button>
                        <button onclick="printTicketNoPrice()" class="bg-gray-500 dark:bg-gray-700 hover:opacity-75 hover:scale-105 duration-500 text-white rounded-lg px-4 py-2">
                            <i class="fas fa-print mr-2"></i>Imprimer sans prix
                        </button>
                        @if(config('custom.email.active'))
                        <button onclick="openEmailModal()" class="bg-green-500 dark:bg-green-800 hover:opacity-75 hover:scale-105 duration-500 text-white rounded-lg px-4 py-2">
                            <i class="fas fa-envelope mr-2"></i>Email
                        </button>
                        @else
                        <button disabled class="bg-gray-400 dark:bg-gray-600 cursor-not-allowed text-white rounded-lg px-4 py-2" title="Envoi d'email désactivé">
                            <i class="fas fa-envelope mr-2"></i>Email
                        </button>
                        @endif
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
                                    <p class="text-lg font-mono bg-gray-100 dark:bg-gray-700 px-3 py-2 rounded">{{ $transaction->transaction_number }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date et heure</label>
                                    <p class="text-lg bg-gray-100 dark:bg-gray-700 px-3 py-2 rounded">{{ $transaction->created_at->format('d/m/Y à H:i:s') }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Caissier</label>
                                    <p class="text-lg bg-gray-100 dark:bg-gray-700 px-3 py-2 rounded flex items-center">
                                        <i class="fas fa-user mr-2 text-gray-500"></i>{{ $transaction->user->name ?? 'Non défini' }}
                                    </p>
                                </div>
                                @if(config('custom.register.customer_management'))
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Client</label>
                                    <p class="text-lg bg-gray-100 dark:bg-gray-700 px-3 py-2 rounded flex items-center">
                                        @if($transaction->customer)
                                            <i class="fas fa-user mr-2 text-gray-500"></i>{{ $transaction->customer->name ?? 'Client non défini' }}
                                        @elseif($transaction->company)
                                            <i class="fas fa-briefcase mr-2 text-gray-500"></i>{{ $transaction->company->name ?? 'Entreprise non définie' }}
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">Non défini</span>
                                        @endif
                                    </p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Articles vendus --}}
                    <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg">
                        <div class="text-gray-900 dark:text-gray-50 px-4 py-3 border-b border-gray-300 dark:border-gray-700">
                            <h2 class="font-semibold text-lg">Articles vendus ({{ $transaction->items->count() }})</h2>
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
                                    @foreach($transaction->items as $item)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                            <td class="py-3 px-2 flex items-center gap-4">
                                                <div class="rounded bg-gradient-to-tr from-purple-300 dark:from-purple-800 to-blue-100 dark:to-blue-800 text-purple-500 dark:text-cyan-600 w-8 h-8 flex items-center justify-center">
                                                    <i class="fas fa-t-shirt text-lg"></i>
                                                </div>
                                                <div>
                                                    <p class="font-medium">{{ $item->article_name }}</p>
                                                    @if($item->barcode)
                                                    <p class="text-sm text-gray-500 font-mono">{{ $item->barcode }}</p>
                                                    @endif
                                                    @if($item->variant_reference)
                                                        <p class="text-sm text-gray-500">Réf: {{ $item->variant_reference }}</p>
                                                    @endif
                                                    @if($item->variant && $item->variant->attributeValues->count() > 0)
                                                        <div class="flex flex-wrap gap-1 mt-1">
                                                            @foreach($item->variant->attributeValues as $attributeValue)
                                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                                                    {{ $loop->index % 4 == 0 ? 'bg-purple-50 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : '' }}
                                                                    {{ $loop->index % 4 == 1 ? 'bg-pink-50 text-pink-800 dark:bg-pink-900 dark:text-pink-200' : '' }}
                                                                    {{ $loop->index % 4 == 2 ? 'bg-green-50 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                                                    {{ $loop->index % 4 == 3 ? 'bg-orange-50 text-orange-800 dark:bg-orange-900 dark:text-orange-200' : '' }}">
                                                                    {{ $attributeValue->attribute->name }}: {{ $attributeValue->value }}
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="py-3 px-2 text-center font-mono">{{ formatNumber($item->quantity) }}</td>
                                            <td class="py-3 px-2 text-right font-mono">€ {{ formatNumber($item->unit_price_ht) }}</td>
                                            <td class="py-3 px-2 text-right font-mono">€ {{ formatNumber($item->unit_price_ttc) }}</td>
                                            <td class="py-3 px-2 text-center">
                                                <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-sm">{{ formatNumber($item->tax_rate)}}%</span>
                                            </td>
                                            <td class="py-3 px-2 text-right font-mono font-medium">€ {{ formatNumber($item->total_price_ttc) }}</td>
                                        </tr>
                                    @endforeach

                                    {{-- Affichage des remises --}}
                                    @if(isset($discounts) && count($discounts))
                                        @foreach($discounts as $discount)
                                            <tr class="bg-blue-50 dark:bg-blue-900/30">
                                                <td class="py-3 px-2 font-semibold text-blue-700 dark:text-blue-300 flex items-center gap-2">
                                                    <i class="fas fa-percent"></i>
                                                    {{ $discount['name'] ?? 'Remise' }}
                                                    @if(($discount['type'] ?? null) === 'percentage')
                                                        <span class="ml-2">-{{ $discount['value'] }}%</span>
                                                    @elseif(($discount['type'] ?? null) === 'fixed')
                                                        <span class="ml-2">-{{ number_format($discount['amount'], 2, ',', ' ') }} €</span>
                                                    @endif
                                                </td>
                                                <td colspan="4"></td>
                                                <td class="py-3 px-2 text-right font-mono font-bold text-blue-700 dark:text-blue-300">
                                                    -€ {{ number_format($discount['amount'], 2, ',', ' ') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Notes (si présentes) --}}
                    @if($transaction->notes)
                        <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg">
                            <div class="text-gray-900 dark:text-gray-50 px-4 py-3 border-b border-gray-300 dark:border-gray-700">
                                <h2 class="font-semibold text-lg">Notes</h2>
                            </div>
                            <div class="p-4">
                                <p class="text-gray-700 dark:text-gray-300 bg-yellow-50 dark:bg-yellow-900/20 p-3 rounded-lg border-l-4 border-yellow-400">
                                    <i class="fas fa-sticky-note mr-2 text-yellow-600"></i>{{ $transaction->notes }}
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
                                <span class="font-mono">€ {{ formatNumber($arrondissementEnabled ? belgianRound($totals['subtotal_ht']) : $totals['subtotal_ht']) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 dark:text-gray-400">TVA</span>
                                <span class="font-mono">€ {{ formatNumber($arrondissementEnabled ? belgianRound($totals['total_tva']) : $totals['total_tva']) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 dark:text-gray-400">Sous-total TTC</span>
                                <span class="font-mono">€ {{ formatNumber($arrondissementEnabled ? belgianRound($totals['subtotal_ttc']) : $totals['subtotal_ttc']) }}</span>
                            </div>
                            @if(isset($discounts) && count($discounts))
                                @foreach($discounts as $discount)
                                    <div class="flex justify-between items-center text-blue-700 dark:text-blue-300">
                                        <span>
                                            {{ $discount['name'] ?? 'Remise' }}
                                            @if(($discount['type'] ?? null) === 'percentage')
                                                ({{ $discount['value'] }}%)
                                            @endif
                                        </span>
                                        <span class="font-mono">-€ {{ number_format($discount['amount'], 2, ',', ' ') }}</span>
                                    </div>
                                @endforeach
                            @endif
                            <hr class="border-gray-300 dark:border-gray-600">
                            <div class="flex justify-between items-center text-lg font-bold">
                                <span>Total final</span>
                                <span class="font-mono text-green-600 dark:text-green-400">€ {{ formatNumber($arrondissementEnabled ? belgianRound($totals['final_total']) : $totals['final_total']) }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Paiements --}}
                    <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg">
                        <div class="text-gray-900 dark:text-gray-50 px-4 py-3 border-b border-gray-300 dark:border-gray-700">
                            <h2 class="font-semibold text-lg">Paiements</h2>
                        </div>
                        <div class="p-4 space-y-3">
                            @foreach($transaction->payments as $payment)
                                <div class="flex items-center justify-between p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mr-3">
                                            <i class="fas fa-{{ $payment->paymentMethod->icon ?? 'credit-card' }} text-green-600 dark:text-green-400"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium text-green-800 dark:text-green-300">{{ $payment->paymentMethod->name }}</p>
                                            <p class="text-sm text-green-600 dark:text-green-400">{{ translateStatus($payment->status) }}</p>
                                        </div>
                                    </div>
                                    <span class="font-mono font-bold text-green-800 dark:text-green-300">€ {{ formatNumber($arrondissementEnabled ? belgianRound($payment->amount) : $payment->amount) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Monnaie rendue --}}
                    @php
                        $totalPaid = $transaction->payments->sum('amount');
                        $montantArrondi = $totals['final_total'];
                        if ($arrondissementEnabled) {
                            $montantArrondi = round($totals['final_total'] * 20) / 20;
                        }
                        $changeAmount = $totalPaid - $montantArrondi;
                        $arrondiValue = 0;
                        if ($arrondissementEnabled) {
                            $arrondiTotal = round($totals['final_total'] * 20) / 20;
                            $arrondiValue = $arrondiTotal - $totals['final_total'];
                        }
                    @endphp
                    @if($changeAmount > 0)
                        <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg group">
                            <div class="text-gray-900 dark:text-gray-50 px-4 py-3 border-b border-gray-300 dark:border-gray-700">
                                <h2 class="font-semibold text-lg">Monnaie rendue</h2>
                            </div>
                            <div class="p-4">
                                <div class="flex items-center justify-between p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800 mb-3">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mr-3">
                                            <i class="fas fa-coins text-blue-600 dark:text-blue-400"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium text-blue-800 dark:text-blue-300">Monnaie rendue</p>
                                            <p class="text-sm text-blue-600 dark:text-blue-400">Montant total</p>
                                        </div>
                                    </div>
                                    <span class="font-mono font-bold text-blue-800 dark:text-blue-300">€ {{ formatNumber($arrondissementEnabled ? belgianRound($changeAmount) : $changeAmount) }}</span>
                                </div>
                                @if(abs($arrondiValue) >= 0.009)
                                    <div class="text-gray-300 group-hover:text-gray-500 dark:text-gray-700 dark:group-hover:text-gray-500">
                                        <span>Arrondi :</span>
                                        <span>{{ $arrondiValue > 0 ? '+' : '' }}{{ number_format($arrondiValue, 2, ',', ' ') }} EUR</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Statut --}}
                    <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg">
                        <div class="text-gray-900 dark:text-gray-50 px-4 py-3 border-b border-gray-300 dark:border-gray-700">
                            <h2 class="font-semibold text-lg">Statut</h2>
                        </div>
                        <div class="p-4">
                            <div class="flex items-center justify-center p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-xl mr-3"></i>
                                <span class="font-medium text-green-800 dark:text-green-300">{{ translateStatus($transaction->payment_status) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Email --}}
    <div id="emailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Envoyer le ticket par email</h3>
                    <form action="{{ route('tickets.email', $transaction->id) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Adresse email</label>
                            <input type="email" name="email" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button type="button" onclick="closeEmailModal()" class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                                Annuler
                            </button>
                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                                Envoyer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const ARRONDISSEMENT_ENABLED = @json(config('custom.register.arrondissementMethod'));

        function openEmailModal() {
            document.getElementById('emailModal').classList.remove('hidden');
        }

        function closeEmailModal() {
            document.getElementById('emailModal').classList.add('hidden');
        }

        // Fermer le modal en cliquant à l'extérieur
        document.getElementById('emailModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEmailModal();
            }
        });

        window.printTicketDirect = function() {
            // Rassembler les données nécessaires depuis le PHP
            const transaction = @json($transaction);
            const totals = @json($totals);
            const appName = @json(config('app.name'));
            let totalPaid = transaction.payments.reduce((sum, p) => sum + parseFloat(p.amount), 0);
            let montantArrondi = totals.final_total;
            if (ARRONDISSEMENT_ENABLED) {
                montantArrondi = Math.round(totals.final_total * 20) / 20;
            }

            // Fonction d'arrondi à 2 décimales
            const arrondi2 = v => Math.round((parseFloat(v) + Number.EPSILON) * 100) / 100;

            const client = [
                name = null
            ];

            // Construire les lignes d'articles
            let itemsHtml = '';
            transaction.items.forEach(item => {
                const quantity = parseFloat(item.quantity);
                const formattedQuantity = new Intl.NumberFormat('fr-FR', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 3
                }).format(quantity);

                let attributesHtml = '';
                if (item.variant && item.variant.attributeValues && item.variant.attributeValues.length > 0) {
                    const values = item.variant.attributeValues.map(attr => attr.value).join(' - ');
                    attributesHtml = `<div style="font-size: 10px; color: #555; padding-left: 10px;">${values}</div>`;
                }

                let barcodeHtml = '';
                if (item.barcode) {
                    barcodeHtml = `<div style="font-size: 10px; color: #555; padding-left: 10px;">EAN: ${item.barcode}</div>`;
                }

                itemsHtml += `
                    <div class="item">
                        <div class="item-name" style="width: 40%;">
                            <span><b style="margin-right: 1mm;">${formattedQuantity}</b> ${item.article_name}</span>
                            ${attributesHtml}
                            ${barcodeHtml}
                        </div>
                        <div style="font-size: 10px; color: #555; width: 20%; text-align: right;">${new Intl.NumberFormat('fr-FR', {minimumFractionDigits: 2 }).format(arrondi2(item.unit_price_ttc))}</div>
                        <div class="item-price" style="width: 20%;">${new Intl.NumberFormat('fr-FR', {minimumFractionDigits: 2 }).format(arrondi2(item.total_price_ttc))}</div>
                    </div>
                `;
            });

            // Construire les lignes de paiement
            let paymentsHtml = '';
            transaction.payments.forEach(payment => {
                paymentsHtml += `
                    <div class="payment">
                        <span>${payment.payment_method.name}:</span>
                        <span>${new Intl.NumberFormat('fr-FR', {minimumFractionDigits: 2 }).format(arrondi2(payment.amount))} EUR</span>
                    </div>
                `;
            });

            // Calcul des montants pour l'affichage
            let arrondiValue = 0;
            if (ARRONDISSEMENT_ENABLED) {
                montantArrondi = Math.round(totals.final_total * 20) / 20;
                arrondiValue = montantArrondi - totals.final_total;
            }
            const changeAmount = totalPaid - montantArrondi; // monnaie rendue réelle
            const changeAmountDiff = totalPaid - totals.final_total; // monnaie rendue sans arrondi

            // Construire la section "Monnaie rendue" et "Arrondi"
            let changeHtml = '';
            if (Math.abs(changeAmount) > 0.009 || Math.abs(arrondiValue) > 0.009) {
                changeHtml = `<div class="">
                    ${Math.abs(changeAmount) > 0.009 ? `<div class="payment">
                        <span>Monnaie rendue :</span>
                        <span>${new Intl.NumberFormat('fr-FR', {minimumFractionDigits: 2 }).format(arrondi2(changeAmount))} EUR</span>
                    </div>` : ''}
                    ${Math.abs(arrondiValue) > 0.009 ? `<div class="payment">
                        <span>Arrondi :</span>
                        <span>${arrondiValue > 0 ? '+' : ''}${arrondiValue.toFixed(2)} EUR</span>
                    </div>` : ''}
                </div>`;
            }

            // Construction au visuel des notes
            let notesHtml = '';
            if (transaction.notes) {
                notesHtml = `
                        <div class="footer">
                            <div style="font-weight: bold; font-style: italic; margin-bottom: 1mm;">
                                Notes<br />
                                - - - - -
                            </div>
                            ${transaction.notes}
                        </div>
                        <hr />
                    `;
            }

            const date = new Date(transaction.created_at);
            const formatted =
                date.toLocaleDateString('fr-FR') + ' ' +
                date.getHours().toString().padStart(2, '0') + ':' +
                date.getMinutes().toString().padStart(2, '0');

            // Template HTML complet du ticket
            const ticketContent = `
                <html>
                <head>
                    <title>Ticket #${transaction.transaction_number}</title>
                    <style>
                        @media print {
                            @page {
                                size: auto auto;
                                margin: 0;
                            }
                            body {
                                margin: 0;
                            }
                        }
                        body {
                            font-family: monospace;
                            font-size: 12px;
                            line-height: 1.3;
                            margin: 0;
                            padding: 0;
                            background: white;
                            color: black;
                            -webkit-font-smoothing: antialiased;
                            -moz-osx-font-smoothing: grayscale;
                        }
                        .ticket {
                            width: 100%;
                            padding: 3mm;
                            box-sizing: border-box;
                        }
                        .header { text-align: center; padding-bottom: 5px; margin-bottom: 5px; }
                        .company-name { font-size: 16px; font-weight: bold; margin-bottom: 5px; }
                        .ticket-info { font-size: 11px; margin-bottom: 5px; }
                        .items, .totals, .payments, .change, .footer { margin-top: 8px; padding-top: 8px; }
                        .item, .total-line, .payment { display: flex; justify-content: space-between; margin-bottom: 4px; font-size: 12px; }
                        .item-name { word-break: break-word; }
                        .item-price { text-align: right; min-width: 60px; }
                        .final-total { font-weight: bold; font-size: 15px; border-top: 1px solid #000; padding-top: 5px; margin-top: 5px; }
                        .footer { text-align: center; margin-top: 10px; font-size: 10px; padding-top: 10px; }
                        hr { border: 0; border-top: 1px dashed #000; margin: 2mm 6mm; }
                    </style>
                </head>
                <body>
                    <div class="ticket">
                        <div class="header">
                            <div class="company-name">${appName}</div>
                            <div>- - - - - - - -</div>
                            <div class="ticket-info">
                                Ticket #${transaction.transaction_number}<br>
                                ${formatted}<br>
                                Vendeur : ${transaction.cashier ? transaction.cashier.name : 'Vendeur'}<br />
                                Client : ${ client.name ? client.name : 'Client comptoir' }
                            </div>
                            <hr />
                        </div>
                        <div class="item" style="font-style: italic;">
                            <div style="width: 40%;">Qte Description</div>
                            <div style="width: 20%; text-align: right;">PU</div>
                            <div style="width: 20%; text-align: right;">Total</div>
                        </div>
                        <hr />
                        <div class="items">${itemsHtml}</div>
                        @if(isset($discounts) && count($discounts))
                            @foreach($discounts as $discount)
                                <div class="item">
                                    <div style="width: 40%;">{{ $discount['name'] ?? 'Remise' }}</div>
                                    <div style="width: 20%; text-align: right;">
                                    @if(($discount['type'] ?? null) === 'percentage')
                                        {{ $discount['value'] }} %
                                    @endif
                                    </div>
                                    <div style="width: 20%; text-align: right;">-{{ number_format($discount['amount'], 2, ',', ' ') }} EUR</div>
                                </div>
                            @endforeach
                        @endif
                        <hr />
                        <div class="totals">
                            <div class="total-line"><span>Sous-total HT:</span><span>${new Intl.NumberFormat('fr-FR', {minimumFractionDigits: 2 }).format(arrondi2(totals.subtotal_ht))} EUR</span></div>
                            <div class="total-line"><span>TVA:</span><span>${new Intl.NumberFormat('fr-FR', {minimumFractionDigits: 2 }).format(arrondi2(totals.total_tva))} EUR</span></div>
                            <div class="total-line"><span>Sous-total TTC:</span><span>${new Intl.NumberFormat('fr-FR', {minimumFractionDigits: 2 }).format(arrondi2(totals.subtotal_ttc))} EUR</span></div>
                            @if(isset($discounts) && count($discounts))
                                @php
                                    $totalRemise = 0;
                                    if (isset($discounts) && is_array($discounts)) {
                                        foreach ($discounts as $discount) {
                                            $totalRemise += $discount['amount'] ?? 0;
                                        }
                                    }
                                @endphp
                                <div class="total-line"><span>Total remise:</span><span>{{ number_format($totalRemise, 2, ',', ' ') }}  EUR</span></div>
                            @endif
                            <div class="total-line final-total"><span>TOTAL:</span><span>${new Intl.NumberFormat('fr-FR', {minimumFractionDigits: 2 }).format(arrondi2(totals.final_total))} EUR</span></div>
                        </div>
                        <hr />
                        <div class="payments">${paymentsHtml}</div>
                        ${changeHtml}
                        <hr />
                        ${notesHtml}
                        <div class="footer">
                            <p>Merci de votre visite !</p>
                            <p>Échange possible endéans 8 jours sur présentation du ticket de caisse.</p>
                            <p>Pas d'échanges sur les articles soldes</p>
                            <p>
                                {{ config('custom.address.street') }}<br />
                                {{ config('custom.address.postal') . ' ' . config('custom.address.city') }}<br />
                                {{ config('custom.tva') }}
                            </p>
                        </div>
                    </div>
                </body>
                </html>
            `;

            const printWindow = window.open('', '_blank', 'width=320,height=480,scrollbars=yes,resizable=yes');
            printWindow.document.write(ticketContent);
            printWindow.document.close();
            printWindow.focus();
            setTimeout(() => {
                printWindow.print();
                printWindow.close();
            }, 250);
        }

        function printTicketNoPrice() {
            const transaction = @json($transaction);
            const appName = @json(config('app.name'));
            const client = [
                name = null
            ];
            // Construire les lignes d'articles sans prix
            let itemsHtml = '';
            transaction.items.forEach(item => {
                const quantity = parseFloat(item.quantity);
                const formattedQuantity = new Intl.NumberFormat('fr-FR', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 3
                }).format(quantity);
                let attributesHtml = '';
                if (item.variant && item.variant.attributeValues && item.variant.attributeValues.length > 0) {
                    const values = item.variant.attributeValues.map(attr => attr.value).join(' - ');
                    attributesHtml = `<div style="font-size: 10px; color: #555; padding-left: 10px;">${values}</div>`;
                }
                let barcodeHtml = '';
                if (item.barcode) {
                    barcodeHtml = `<div style="font-size: 10px; color: #555; padding-left: 10px;">EAN: ${item.barcode}</div>`;
                }
                itemsHtml += `
                    <div class="item">
                        <div class="item-name" style="width: 100%;">
                            <span><b style="margin-right: 1mm;">${formattedQuantity}</b> ${item.article_name}</span>
                            ${attributesHtml}
                            ${barcodeHtml}
                        </div>
                    </div>
                `;
            });

            let notesHtml = '';
            if (transaction.notes) {
                notesHtml = `
                        <div class="footer">
                            <div style="font-weight: bold; font-style: italic; margin-bottom: 1mm;">
                                Notes<br />
                                - - - - -
                            </div>
                            ${transaction.notes}
                        </div>
                        <hr />
                    `;
            }

            const date = new Date(transaction.created_at);
            const formatted =
                date.toLocaleDateString('fr-FR') + ' ' +
                date.getHours().toString().padStart(2, '0') + ':' +
                date.getMinutes().toString().padStart(2, '0');
            const ticketContent = `
                <html>
                <head>
                    <title>Ticket #${transaction.transaction_number}</title>
                    <style>
                        @media print {
                            @page {
                                size: auto auto;
                                margin: 0;
                            }
                            body {
                                margin: 0;
                            }
                        }
                        body {
                            font-family: monospace;
                            font-size: 12px;
                            line-height: 1.3;
                            margin: 0;
                            padding: 0;
                            background: white;
                            color: black;
                            -webkit-font-smoothing: antialiased;
                            -moz-osx-font-smoothing: grayscale;
                        }
                        .ticket {
                            width: 100%;
                            padding: 3mm;
                            box-sizing: border-box;
                        }
                        .header { text-align: center; padding-bottom: 5px; margin-bottom: 5px; }
                        .company-name { font-size: 16px; font-weight: bold; margin-bottom: 5px; }
                        .ticket-info { font-size: 11px; margin-bottom: 5px; }
                        .items, .footer { margin-top: 8px; padding-top: 8px; }
                        .item { display: flex; justify-content: flex-start; margin-bottom: 4px; font-size: 12px; }
                        .item-name { word-break: break-word; width: 100%; }
                        .footer { text-align: center; margin-top: 10px; font-size: 10px; padding-top: 10px; }
                        hr { border: 0; border-top: 1px dashed #000; margin: 2mm 6mm; }
                    </style>
                </head>
                <body>
                    <div class="ticket">
                        <div class="header">
                            <div class="company-name">${appName}</div>
                            <div>- - - - - - - -</div>
                            <div class="ticket-info">
                                Ticket #${transaction.transaction_number}<br>
                                ${formatted}<br>
                                Vendeur : ${transaction.cashier ? transaction.cashier.name : 'Vendeur'}<br />
                                Client : ${ client.name ? client.name : 'Client comptoir' }
                            </div>
                            <hr />
                        </div>
                        <div class="item" style="font-style: italic;">
                            <div style="width: 100%;">Qte Description</div>
                        </div>
                        <hr />
                        <div class="items">${itemsHtml}</div>
                        <hr />
                        ${notesHtml}
                        <div class="footer">
                            <p>Merci de votre visite !</p>
                            <p>Échange possible endéans 8 jours sur présentation du ticket de caisse.</p>
                            <p>Pas d'échanges sur les articles soldes</p>
                            <p>
                                {{ config('custom.address.street') }}<br />
                                {{ config('custom.address.postal') . ' ' . config('custom.address.city') }}<br />
                                {{ config('custom.tva') }}
                            </p>
                        </div>
                    </div>
                </body>
                </html>
            `;
            const printWindow = window.open('', '_blank', 'width=320,height=480,scrollbars=yes,resizable=yes');
            printWindow.document.write(ticketContent);
            printWindow.document.close();
            printWindow.focus();
            setTimeout(() => {
                printWindow.print();
                printWindow.close();
            }, 250);
        }
    </script>
</x-app-layout>
