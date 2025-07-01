{{-- Partial pour le tableau des transactions + pagination --}}
<div class="overflow-x-auto">
    <div class="space-y-2">
        <!-- En-tête -->
        <div class="hidden md:grid grid-cols-6 gap-4 py-3 px-4 bg-gray-100 dark:bg-gray-700 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300">
            <div>N° Transaction</div>
            <div class="text-center">Type</div>
            <div class="text-center">Client</div>
            <div class="text-center">Articles</div>
            <div class="text-center">Montant</div>
            <div class="text-center">Date/Heure</div>
        </div>

        <div class="space-y-2">
            @forelse($transactions as $transaction)
                {{-- ... Copie du bloc de chaque transaction ... --}}
                @include('panel.transactions._row', ['transaction' => $transaction])
            @empty
                <div class="text-center text-gray-500 py-8">
                    Aucune transaction trouvée pour ces critères.
                </div>
            @endforelse
        </div>
    </div>
    <div class="mt-6">
        {{ $transactions->links() }}
    </div>
</div>
