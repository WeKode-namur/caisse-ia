@if($mouvements->count() > 0)
    <div class="p-6">
        <div class="space-y-3">
        @foreach($mouvements as $mouvement)
            <div class="grid grid-cols-4 items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <div class="col-span-2 flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-medium
                        {{ $mouvement->quantity_used < 0 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
                           ($mouvement->quantity_used > 0 ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200') }}">
                        @if($mouvement->quantity_used < 0)
                            <i class="fas fa-arrow-up"></i>
                        @elseif($mouvement->quantity_used > 0)
                            <i class="fas fa-arrow-down"></i>
                        @else
                            <i class="fas fa-edit"></i>
                        @endif
                    </div>
                    <div>
                        <div class="font-medium text-sm text-gray-900 dark:text-gray-100">
                            {{ $mouvement->quantity_used < 0 ? 'Entrée' : ($mouvement->quantity_used > 0 ? 'Sortie' : 'Autre') }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            @php
                                $ref = $mouvement->transactionItem->variant->reference ?? null;
                                $id = $mouvement->transactionItem->variant->id ?? null;
                            @endphp
                            {{ $ref ? $ref : ($id ? 'Variant #'.$id : 'N/A') }}
                        </div>
                        <div class="text-xs text-gray-400 dark:text-gray-500">
                            @if($mouvement->transactionItem->transaction->is_wix_release)
                                E-Shop
                            @else
                                Caisse
                            @endif
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="font-medium text-sm
                        {{ $mouvement->quantity_used < 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                        {{ $mouvement->quantity_used > 0 ? '-' : '+' }}{{ abs($mouvement->quantity_used) }}
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        Stock: {{ $mouvement->stock?->quantity ?? 'N/A' }}
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-xs text-gray-400 dark:text-gray-500">
                        {{ $mouvement->created_at->format('d/m H:i') }}
                    </div>
                    <div class="text-xs text-gray-400 dark:text-gray-500">
                        par {{ $mouvement->transactionItem->transaction->cashier->name ?? 'N/A' }}
                    </div>
                </div>
            </div>
        @endforeach
        </div>
    </div>
    <div class="px-6 pb-6">
        {{ $mouvements->links() }}
    </div>
@else
    <div class="p-6 text-center">
        <i class="fas fa-history text-4xl text-gray-400 mb-3"></i>
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Aucun mouvement</h3>
        <p class="text-gray-500 dark:text-gray-400">Aucun mouvement trouvé pour ces filtres.</p>
    </div>
@endif 