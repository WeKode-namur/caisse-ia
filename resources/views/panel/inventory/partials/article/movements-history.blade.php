<!-- Historique des mouvements -->
@if($mouvements->count() > 0)
    <div class="space-y-3">
        @foreach($mouvements as $mouvement)
            <div class="grid grid-cols-4 items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <div class="col-span-2 flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-medium
                        {{ $mouvement->type == 'entrée' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
                           ($mouvement->type == 'sortie' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' :
                           ($mouvement->type == 'vente' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200')) }}">
                        @if($mouvement->type == 'entrée')
                            <i class="fas fa-arrow-up"></i>
                        @elseif($mouvement->type == 'sortie')
                            <i class="fas fa-arrow-down"></i>
                        @elseif($mouvement->type == 'vente')
                            <i class="fas fa-shopping-cart"></i>
                        @else
                            <i class="fas fa-edit"></i>
                        @endif
                    </div>
                    <div>
                        <div class="font-medium text-sm text-gray-900 dark:text-gray-100">
                            {{ ucfirst($mouvement->type) }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $mouvement->motif ?? 'Aucune description' }}
                        </div>
                        @if($mouvement->variant_info ?? false)
                            <div class="text-xs text-gray-400 dark:text-gray-500">
                                Variant: {{ $mouvement->variant_info }}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="text-right">
                    <div class="font-medium text-sm
                        {{ $mouvement->quantite > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                        {{ $mouvement->quantite > 0 ? '+' : '' }}{{ $mouvement->quantite }}
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        Stock: {{ $mouvement->stock_resultant ?? 'N/A' }}
                    </div>
                    @if($mouvement->prix_unitaire ?? false)
                        <div class="text-xs text-gray-400 dark:text-gray-500">
                            €{{ number_format($mouvement->prix_unitaire, 2) }}/u
                        </div>
                    @endif
                </div>
                <div class="text-right">
                    <div class="text-xs text-gray-400 dark:text-gray-500">
                        {{ $mouvement->created_at->format('d/m H:i') }}
                    </div>
                    @if($mouvement->user_name ?? false)
                        <div class="text-xs text-gray-400 dark:text-gray-500">
                            par {{ $mouvement->user_name }}
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@else
    <!-- Message si aucun mouvement -->
    <div class="text-center py-8">
        <i class="fas fa-history text-4xl text-gray-400 mb-3"></i>
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Aucun mouvement</h3>
        <p class="text-gray-500 dark:text-gray-400">L'historique des mouvements de stock apparaîtra ici</p>
    </div>
@endif
