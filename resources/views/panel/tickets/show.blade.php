<x-app-layout>
    @php
        // Helper function pour formater les nombres sans zéros inutiles
        function formatNumber($number, $decimals = 2, $dec_point = ',', $thousands_sep = ' ') {
            return rtrim(rtrim(number_format($number, $decimals, $dec_point, $thousands_sep), '0'), $dec_point);
        }

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
                                                <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-sm">{{ $item->tax_rate }}%</span>
                                            </td>
                                            <td class="py-3 px-2 text-right font-mono font-medium">€ {{ formatNumber($item->total_price_ttc) }}</td>
                                        </tr>
                                    @endforeach
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
                                <span class="font-mono">€ {{ formatNumber($totals['subtotal_ht']) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 dark:text-gray-400">TVA</span>
                                <span class="font-mono">€ {{ formatNumber($totals['total_tva']) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 dark:text-gray-400">Sous-total TTC</span>
                                <span class="font-mono">€ {{ formatNumber($totals['subtotal_ttc']) }}</span>
                            </div>
                            @if($totals['total_discount'] > 0)
                                <div class="flex justify-between items-center text-red-600 dark:text-red-400">
                                    <span>Remise</span>
                                    <span class="font-mono">- € {{ formatNumber($totals['total_discount']) }}</span>
                                </div>
                            @endif
                            <hr class="border-gray-300 dark:border-gray-600">
                            <div class="flex justify-between items-center text-lg font-bold">
                                <span>Total final</span>
                                <span class="font-mono text-green-600 dark:text-green-400">€ {{ formatNumber($totals['final_total']) }}</span>
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
                                    <span class="font-mono font-bold text-green-800 dark:text-green-300">€ {{ formatNumber($payment->amount) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Monnaie rendue --}}
                    @php
                        $totalPaid = $transaction->payments->sum('amount');
                        $changeAmount = $totalPaid - $totals['final_total'];
                    @endphp
                    @if($changeAmount > 0)
                        <div class="bg-white/50 backdrop-blur dark:bg-gray-800/50 dark:text-gray-200 overflow-hidden shadow-xl lg:rounded-lg">
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
                                    <span class="font-mono font-bold text-blue-800 dark:text-blue-300">€ {{ formatNumber($changeAmount) }}</span>
                                </div>
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

    {{-- Modal Impression --}}
    <div id="printModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
                <div class="flex items-center justify-between p-4 border-b border-gray-300 dark:border-gray-600">
                    <h3 class="text-lg font-semibold">Aperçu d'impression - Ticket #{{ $transaction->transaction_number }}</h3>
                    <div class="flex space-x-2">
                        <button onclick="printTicket()" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                            <i class="fas fa-print mr-2"></i>Imprimer
                        </button>
                        <button onclick="closePrintModal()" class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                            <i class="fas fa-times mr-2"></i>Fermer
                        </button>
                    </div>
                </div>
                <div class="p-4 overflow-auto max-h-[calc(90vh-80px)]">
                    <div id="printContent" class="bg-white border border-gray-300 rounded-lg p-6 max-w-sm mx-auto">
                        <div class="text-center border-b border-dashed border-gray-400 pb-4 mb-4">
                            <div class="text-lg font-bold mb-2">{{ config('app.name') }}</div>
                            <div class="text-xs text-gray-600">
                                Ticket #{{ $transaction->transaction_number }}<br>
                                {{ $transaction->created_at->format('d/m/Y H:i') }}<br>
                                {{ $transaction->user->name ?? 'Vendeur' }}
                            </div>
                        </div>

                        <div class="mb-4">
                            @foreach($transaction->items as $item)
                                <div class="flex justify-between items-start mb-2">
                                    <div class="flex-1">
                                        <div class="text-sm">{{ $item->quantity }}x {{ $item->article_name }}</div>
                                        @if($item->variant_name)
                                            <div class="text-xs text-gray-600">{{ $item->variant_name }}</div>
                                        @endif
                                    </div>
                                    <div class="text-right ml-4">
                                        <div class="text-sm">€{{ number_format($item->unit_price_ttc, 2, ',', ' ') }}</div>
                                        <div class="text-sm font-semibold">€{{ number_format($item->total_price_ttc, 2, ',', ' ') }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="border-t border-dashed border-gray-400 pt-4 mb-4">
                            <div class="flex justify-between text-sm mb-1">
                                <span>Sous-total HT:</span>
                                <span>€{{ number_format($totals['subtotal_ht'], 2, ',', ' ') }}</span>
                            </div>
                            <div class="flex justify-between text-sm mb-1">
                                <span>TVA:</span>
                                <span>€{{ number_format($totals['total_tva'], 2, ',', ' ') }}</span>
                            </div>
                            <div class="flex justify-between text-sm mb-1">
                                <span>Sous-total TTC:</span>
                                <span>€{{ number_format($totals['subtotal_ttc'], 2, ',', ' ') }}</span>
                            </div>
                            @if($totals['total_discount'] > 0)
                                <div class="flex justify-between text-sm mb-1 text-red-600">
                                    <span>Remise:</span>
                                    <span>-€{{ number_format($totals['total_discount'], 2, ',', ' ') }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between text-base font-bold border-t border-gray-400 pt-2 mt-2">
                                <span>TOTAL:</span>
                                <span>€{{ number_format($totals['final_total'], 2, ',', ' ') }}</span>
                            </div>
                        </div>

                        <div class="border-t border-dashed border-gray-400 pt-4 mb-4">
                            @foreach($transaction->payments as $payment)
                                <div class="flex justify-between text-sm mb-1">
                                    <span>{{ $payment->paymentMethod->name }}:</span>
                                    <span>€{{ number_format($payment->amount, 2, ',', ' ') }}</span>
                                </div>
                            @endforeach
                        </div>

                        @php
                            $totalPaid = $transaction->payments->sum('amount');
                            $changeAmount = $totalPaid - $totals['final_total'];
                        @endphp

                        @if($changeAmount > 0)
                            <div class="border-t border-dashed border-gray-400 pt-4 mb-4">
                                <div class="flex justify-between text-sm">
                                    <span>Monnaie rendue:</span>
                                    <span>€{{ number_format($changeAmount, 2, ',', ' ') }}</span>
                                </div>
                            </div>
                        @endif

                        @if($transaction->notes)
                            <div class="border-t border-dashed border-gray-400 pt-4 mb-4">
                                <div class="text-sm">
                                    <strong>Note:</strong><br>
                                    {{ $transaction->notes }}
                                </div>
                            </div>
                        @endif

                        <div class="text-center border-t border-dashed border-gray-400 pt-4 text-xs text-gray-600">
                            Merci de votre visite !<br>
                            {{ config('app.name') }} - {{ date('Y') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
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

        function openPrintModal() {
            document.getElementById('printModal').classList.remove('hidden');
        }

        function closePrintModal() {
            document.getElementById('printModal').classList.add('hidden');
        }

        function printTicket() {
            // Créer une nouvelle fenêtre pour l'impression
            const printWindow = window.open('', '_blank');
            const printContent = document.getElementById('printContent').innerHTML;

            printWindow.document.write(`
                <html>
                <head>
                    <title>Ticket #${{ $transaction->transaction_number }}</title>
                    <style>
                        @media print {
                            @page {
                                size: 80mm auto;
                                margin: 0;
                            }
                            body {
                                margin: 0;
                            }
                        }
                        body {
                            font-family: 'Courier New', monospace;
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
                            width: 80mm;
                            padding: 3mm;
                            box-sizing: border-box;
                        }
                        .header { text-align: center; padding-bottom: 5px; margin-bottom: 5px; }
                        .company-name { font-size: 16px; font-weight: bold; margin-bottom: 5px; }
                        .ticket-info { font-size: 11px; margin-bottom: 5px; }
                        .items, .totals, .payments, .change, .footer { margin-top: 8px; border-top: 1px dashed #000; padding-top: 8px; }
                        .item, .total-line, .payment { display: flex; justify-content: space-between; margin-bottom: 4px; font-size: 12px; }
                        .item-name { flex: 1; word-break: break-word; }
                        .item-price { text-align: right; min-width: 60px; }
                        .final-total { font-weight: bold; font-size: 15px; border-top: 1px solid #000; padding-top: 5px; margin-top: 5px; }
                        .footer { text-align: center; margin-top: 10px; font-size: 10px; border-top: 1px dashed #000; padding-top: 10px; }
                    </style>
                </head>
                <body>
                    <div class="ticket">
                        <div class="header">
                            <div class="company-name">${{ config('app.name') }}</div>
                            <div class="ticket-info">
                                Ticket #${{ $transaction->transaction_number }}<br>
                                ${{ $transaction->created_at->format('d/m/Y H:i') }}<br>
                                ${{ $transaction->user->name ?? 'Vendeur' }}
                            </div>
                        </div>
                        <div class="items">${printContent}</div>
                        <div class="totals">
                            <div class="total-line"><span>Sous-total HT:</span><span>€${{ number_format($totals['subtotal_ht'], 2, ',', ' ') }}</span></div>
                            <div class="total-line"><span>TVA:</span><span>€${{ number_format($totals['total_tva'], 2, ',', ' ') }}</span></div>
                            <div class="total-line"><span>Sous-total TTC:</span><span>€${{ number_format($totals['subtotal_ttc'], 2, ',', ' ') }}</span></div>
                            @if($totals['total_discount'] > 0)
                                <div class="total-line"><span>Remise:</span><span>-€${{ number_format($totals['total_discount'], 2, ',', ' ') }}</span></div>
                            @endif
                            <div class="total-line final-total"><span>TOTAL:</span><span>€${{ number_format($totals['final_total'], 2, ',', ' ') }}</span></div>
                        </div>
                        <div class="payments">
                            @foreach($transaction->payments as $payment)
                                <div class="payment">
                                    <span>${{ $payment->paymentMethod->name }}:</span>
                                    <span>€${{ number_format($payment->amount, 2, ',', ' ') }}</span>
                                </div>
                            @endforeach
                        </div>
                        @php
                            $totalPaid = $transaction->payments->sum('amount');
                            $changeAmount = $totalPaid - $totals['final_total'];
                        @endphp
                        @if($changeAmount > 0)
                            <div class="change">
                                <div class="payment">
                                    <span>Monnaie rendue:</span>
                                    <span>€${{ number_format($changeAmount, 2, ',', ' ') }}</span>
                                </div>
                            </div>
                        @endif
                        @if($transaction->notes)
                            <div class="note">
                                <div class="text-sm">
                                    <strong>Note:</strong><br>
                                    {{ $transaction->notes }}
                                </div>
                            </div>
                        @endif
                        <div class="footer">Merci de votre visite !<br>${{ config('app.name') }} - {{ date('Y') }}</div>
                    </div>
                </body>
                </html>
            `);

            printWindow.document.close();
        }

        function printTicketDirect() {
            // Rassembler les données nécessaires depuis le PHP
            const transaction = @json($transaction);
            const totals = @json($totals);
            const appName = @json(config('app.name'));
            const changeAmount = {{ $transaction->payments->sum('amount') - $totals['final_total'] }};

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
                        <div class="item-name">
                            <span>${formattedQuantity}x ${item.article_name}</span>
                            ${attributesHtml}
                            ${barcodeHtml}
                        </div>
                        <div class="item-price">€${new Intl.NumberFormat('fr-FR', { style: 'decimal', minimumFractionDigits: 2 }).format(item.total_price_ttc)}</div>
                    </div>
                `;

                if (quantity > 1) {
                    itemsHtml += `
                        <div class="item" style="font-size: 10px; color: #555;">
                            <div class="item-name" style="padding-left: 10px;">Prix unitaire</div>
                            <div class="item-price">€${new Intl.NumberFormat('fr-FR', { style: 'decimal', minimumFractionDigits: 2 }).format(item.unit_price_ttc)}</div>
                        </div>
                    `;
                }
            });

            // Construire les lignes de paiement
            let paymentsHtml = '';
            transaction.payments.forEach(payment => {
                paymentsHtml += `
                    <div class="payment">
                        <span>${payment.payment_method.name}:</span>
                        <span>€${new Intl.NumberFormat('fr-FR', { style: 'decimal', minimumFractionDigits: 2 }).format(payment.amount)}</span>
                    </div>
                `;
            });

            // Construire la section "Monnaie rendue"
            let changeHtml = '';
            if (changeAmount > 0) {
                changeHtml = `
                    <div class="change">
                        <div class="payment">
                            <span>Monnaie rendue:</span>
                            <span>€${new Intl.NumberFormat('fr-FR', { style: 'decimal', minimumFractionDigits: 2 }).format(changeAmount)}</span>
                        </div>
                    </div>
                `;
            }

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
                            font-family: 'Courier New', monospace;
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
                        .items, .totals, .payments, .change, .footer { margin-top: 8px; border-top: 1px dashed #000; padding-top: 8px; }
                        .item, .total-line, .payment { display: flex; justify-content: space-between; margin-bottom: 4px; font-size: 12px; }
                        .item-name { flex: 1; word-break: break-word; }
                        .item-price { text-align: right; min-width: 60px; }
                        .final-total { font-weight: bold; font-size: 15px; border-top: 1px solid #000; padding-top: 5px; margin-top: 5px; }
                        .footer { text-align: center; margin-top: 10px; font-size: 10px; border-top: 1px dashed #000; padding-top: 10px; }
                    </style>
                </head>
                <body>
                    <div class="ticket">
                        <div class="header">
                            <div class="company-name">${appName}</div>
                            <div class="ticket-info">
                                Ticket #${transaction.transaction_number}<br>
                                ${new Date(transaction.created_at).toLocaleString('fr-FR')}<br>
                                ${transaction.cashier ? transaction.cashier.name : 'Vendeur'}
                            </div>
                        </div>
                        <div class="items">${itemsHtml}</div>
                        <div class="totals">
                            <div class="total-line"><span>Sous-total HT:</span><span>€${new Intl.NumberFormat('fr-FR', { style: 'decimal', minimumFractionDigits: 2 }).format(totals.subtotal_ht)}</span></div>
                            <div class="total-line"><span>TVA:</span><span>€${new Intl.NumberFormat('fr-FR', { style: 'decimal', minimumFractionDigits: 2 }).format(totals.total_tva)}</span></div>
                            <div class="total-line"><span>Sous-total TTC:</span><span>€${new Intl.NumberFormat('fr-FR', { style: 'decimal', minimumFractionDigits: 2 }).format(totals.subtotal_ttc)}</span></div>
                            ${totals.total_discount > 0 ? `<div class="total-line"><span>Remise:</span><span>-€${new Intl.NumberFormat('fr-FR', { style: 'decimal', minimumFractionDigits: 2 }).format(totals.total_discount)}</span></div>` : ''}
                            <div class="total-line final-total"><span>TOTAL:</span><span>€${new Intl.NumberFormat('fr-FR', { style: 'decimal', minimumFractionDigits: 2 }).format(totals.final_total)}</span></div>
                        </div>
                        <div class="payments">${paymentsHtml}</div>
                        ${changeHtml}
                        <div class="footer">Merci de votre visite !<br>${appName} - ${new Date().getFullYear()}</div>
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

        // Fermer le modal en cliquant à l'extérieur
        document.getElementById('printModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closePrintModal();
            }
        });
    </script>
</x-app-layout>
