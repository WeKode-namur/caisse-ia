@if($transactions->count() > 0)
    <div class="p-6">
        <div class="space-y-3">
            @foreach($transactions as $transaction)
                <div class="grid grid-cols-4 items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="col-span-2 flex items-center space-x-3">
                        <div
                            class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div>
                            <div class="font-medium text-sm text-gray-900 dark:text-gray-100">
                                Transaction #{{ $transaction->transaction->id ?? 'N/A' }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $transaction->variant->reference ?? 'Variant standard' }}
                            </div>
                            <div class="text-xs text-gray-400 dark:text-gray-500">
                                @if($transaction->transaction->is_wix_release)
                                    <i class="fas fa-globe text-green-500 mr-1"></i>E-Shop
                                @else
                                    <i class="fas fa-cash-register text-blue-500 mr-1"></i>Caisse
                                @endif
                                @if($transaction->transaction->cashier)
                                    - {{ $transaction->transaction->cashier->name }}
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="font-medium text-sm text-gray-900 dark:text-gray-100">
                            {{ $transaction->quantity }} × {{ number_format($transaction->unit_price_ttc, 2) }}€
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            Total: {{ number_format($transaction->total_price_ttc, 2) }}€
                        </div>
                        <div class="text-xs text-gray-400 dark:text-gray-500">
                            {{ $transaction->created_at->format('d/m/Y H:i') }}
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            @if($transaction->discount_amount > 0)
                                <span
                                    class="text-red-500">-{{ number_format($transaction->discount_amount, 2) }}€</span>
                            @endif
                        </div>
                        @if($transaction->variant_attributes)
                            <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                @foreach($transaction->variant_attributes as $attr)
                                    <span class="inline-block bg-gray-200 dark:bg-gray-600 px-1 rounded text-xs mr-1">
                                    {{ $attr['name'] }}: {{ $attr['value'] }}
                                </span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach

        </div>
    </div>
    <!-- Pagination -->
    @if($transactions->hasPages())
        <div class="px-6 pb-6">
            {{ $transactions->links() }}
        </div>
    @endif
@else
    <div class="p-6 text-center">
        <div class="text-gray-500 dark:text-gray-400">
            <i class="fas fa-inbox text-4xl mb-4"></i>
            <p class="text-lg font-medium">Aucune transaction trouvée</p>
            <p class="text-sm">Cet article avec stock illimité n'a pas encore été vendu.</p>
        </div>
    </div>
@endif
