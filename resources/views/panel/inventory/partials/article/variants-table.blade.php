<!-- Tableau des variants -->
<div class="overflow-x-auto">
    @if($variants->count() > 0)
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Variant
                </th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Code-barres
                </th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Prix de vente
                </th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Stock
                    <div class="text-gray-400 dark:text-gray-600 block" style="font-size: 0.6rem">
                        Seuil: {{ config('custom.article.seuil') }}
                    </div>
                </th>
            </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            @foreach($variants as $variant)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors cursor-pointer"
                    onclick="openVariantModal({{ $variant->id }}, {{ json_encode([
                        'name' => $variant->reference ?? 'Variant #' . $variant->id,
                        'reference' => $variant->reference ?? '',
                        'barcode' => $variant->barcode ?? '',
                        'buy_price' => $variant->buy_price ?? $variant->article->buy_price ?? 0,
                        'sell_price' => $variant->sell_price ?? $variant->article->sell_price ?? 0,
                        'stock' => $variant->total_stock ?? 0,
                        'value' => $variant->stock_value ?? 0,
                        'created_at' => $variant->created_at ? $variant->created_at->format('d/m/Y H:i') : null,
                        'updated_at' => $variant->updated_at ? $variant->updated_at->diffForHumans() : null,
                        'attributes' => $variant->attributeValues->map(function($av) {
                            return $av->attribute->name . ': ' . $av->value;
                        })->toArray()
                    ]) }})">
                    <td class="px-4 py-4">
                        <div class="flex items-center gap-3">
                            <div>
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-blue-200 dark:from-blue-900 dark:to-blue-800 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-tag text-blue-400 text-xl"></i>
                                </div>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $variant->reference ?? 'Variant #' . $variant->id }}
                                </div>
                                <div class="flex flex-wrap gap-1 mt-1">
                                    @foreach($variant->attributeValues as $attributeValue)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                            {{ $loop->index % 4 == 0 ? 'bg-purple-50 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : '' }}
                                            {{ $loop->index % 4 == 1 ? 'bg-pink-50 text-pink-800 dark:bg-pink-900 dark:text-pink-200' : '' }}
                                            {{ $loop->index % 4 == 2 ? 'bg-green-50 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                            {{ $loop->index % 4 == 3 ? 'bg-orange-50 text-orange-800 dark:bg-orange-900 dark:text-orange-200' : '' }}">
                                            {{ $attributeValue->attribute->name }}: {{ $attributeValue->value }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4 text-center">
                        <span class="font-mono text-sm text-gray-600 dark:text-gray-400">
                            {{ $variant->barcode ?? 'N/A' }}
                        </span>
                    </td>
                    <td class="px-4 py-4 text-center">
                        <span class="text-sm font-medium text-green-600 dark:text-green-400">
                            {{ number_format($variant->sell_price ?? $variant->article->sell_price ?? 0, 2) }}€
                        </span>
                    </td>
                    <td class="px-4 py-4 text-center">
                        <div class="flex items-center justify-center space-x-2">
                            <span class="text-sm font-bold
                                {{ $variant->total_stock == 0 ? 'text-red-600 dark:text-red-400' :
                                   ($variant->total_stock <= ($variant->seuil_alerte ?? 5) ? 'text-orange-600 dark:text-orange-400' : 'text-green-600 dark:text-green-400') }}">
                                {{ $variant->total_stock ?? 0 }}
                            </span>
                            @if($variant->total_stock <= ($variant->seuil_alerte ?? 5))
                                <i class="fas fa-exclamation-triangle text-{{ $variant->total_stock == 0 ? 'red' : 'orange' }}-500 text-xs"></i>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @else
        <!-- Message si aucun variant -->
        <div class="text-center py-8">
            <i class="fas fa-layer-group text-4xl text-gray-400 mb-3"></i>
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Aucun variant</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-4">Créez votre premier variant pour cet article</p>
            <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm">
                <i class="fas fa-plus mr-2"></i>Créer un variant
            </button>
        </div>
    @endif
</div>

<script>
    function editVariant(variantId) {
        // TODO: Ouvrir modal d'édition du variant
        console.log('Éditer variant:', variantId);
    }

    function adjustStock(variantId) {
        // TODO: Ouvrir modal d'ajustement de stock
        console.log('Ajuster stock variant:', variantId);
    }

    function deleteVariant(variantId) {
        if (confirm('Êtes-vous sûr de vouloir supprimer ce variant ?')) {
            // TODO: Suppression AJAX du variant
            console.log('Supprimer variant:', variantId);
        }
    }
</script>
