<div class="overflow-x-auto rounded-lg shadow">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
        <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0 z-10">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Article
            </th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Transaction
            </th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Prix
            </th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                TVA
            </th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Date
            </th>
            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Statut
            </th>
            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Actions
            </th>
        </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
        @forelse($unknownItems as $item)
            <tr class="hover:bg-blue-50 dark:hover:bg-gray-700/50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="font-medium text-gray-900 dark:text-gray-100">{{ $item->nom }}</div>
                    @if($item->description)
                        <div
                            class="text-xs text-gray-500 dark:text-gray-400">{{ Str::limit($item->description, 50) }}</div>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div
                        class="text-sm text-gray-900 dark:text-gray-100">{{ $item->transactionItem->transaction->transaction_number ?? 'N/A' }}</div>
                    <div
                        class="text-xs text-gray-500 dark:text-gray-400">{{ $item->transactionItem->transaction->cashier->name ?? 'N/A' }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="font-semibold text-gray-900 dark:text-gray-100">{{ number_format($item->prix, 2, ',', ' ') }} €</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="text-gray-900 dark:text-gray-100">{{ $item->tva }}%</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900 dark:text-gray-100">{{ $item->created_at->format('d/m/Y') }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $item->created_at->format('H:i') }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @if($item->est_regularise)
                        @if($item->note_interne && str_starts_with($item->note_interne, 'Non identifiable'))
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                    <i class="fas fa-times mr-1"></i> Non identifiable
                                </span>
                        @else
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    <i class="fas fa-check mr-1"></i> Régularisé
                                </span>
                        @endif
                    @else
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                <i class="fas fa-exclamation-triangle mr-1"></i> À régulariser
                            </span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                    @if(!$item->est_regularise)
                        <button onclick="ouvrirModal({{ $item->id }})"
                                class="inline-flex items-center px-2 py-1 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 rounded hover:bg-green-200 dark:hover:bg-green-800 transition-colors text-xs font-semibold">
                            <i class="fas fa-link mr-1"></i> Régulariser
                        </button>
                    @endif
                    <a href="{{ route('settings.unknown-items.show', $item) }}"
                       class="inline-flex items-center px-2 py-1 bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors text-xs font-semibold">
                        <i class="fas fa-eye mr-1"></i> Voir
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                    Aucun article inconnu trouvé
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>
@if($unknownItems->hasPages())
    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 rounded-b-lg">
        {{ $unknownItems->links() }}
    </div>
@endif
