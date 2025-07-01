{{-- Partial pour une ligne de transaction --}}
<div class="transaction-row grid grid-cols-1 md:grid-cols-6 gap-4 py-4 px-4 bg-white dark:bg-gray-800 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors duration-200 border border-gray-200 dark:border-gray-600"
     data-transaction-id="{{ $transaction->id }}"
     onclick="window.location.href='{{ $transaction->transaction_type === 'ticket' ? route('tickets.index', $transaction->id) : route('factures.index', $transaction->id) }}'">

    <!-- Mobile Layout -->
    <div class="md:hidden space-y-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-{{ $transaction->transaction_type === 'ticket' ? 'blue' : 'purple' }}-100 dark:bg-{{ $transaction->transaction_type === 'ticket' ? 'blue' : 'purple' }}-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-{{ $transaction->transaction_type === 'ticket' ? 'ticket' : 'file-invoice' }} text-{{ $transaction->transaction_type === 'ticket' ? 'blue' : 'purple' }}-600 dark:text-{{ $transaction->transaction_type === 'ticket' ? 'blue' : 'purple' }}-400 text-sm"></i>
                </div>
                <div>
                    <div class="font-medium">{{ $transaction->transaction_number }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400 capitalize">{{ $transaction->transaction_type }}</div>
                </div>
            </div>
            <div class="text-right">
                <div class="font-bold text-lg">€ {{ number_format($transaction->total_amount, 2, ',', ' ') }}</div>
                <span class="px-2 py-1 text-xs rounded-full bg-{{ $transaction->payment_status === 'paid' ? 'green' : ($transaction->payment_status === 'cancelled' ? 'red' : 'orange') }}-100 text-{{ $transaction->payment_status === 'paid' ? 'green' : ($transaction->payment_status === 'cancelled' ? 'red' : 'orange') }}-800 dark:bg-{{ $transaction->payment_status === 'paid' ? 'green' : ($transaction->payment_status === 'cancelled' ? 'red' : 'orange') }}-900/30 dark:text-{{ $transaction->payment_status === 'paid' ? 'green' : ($transaction->payment_status === 'cancelled' ? 'red' : 'orange') }}-400">
                    @translateStatus($transaction->payment_status)
                </span>
            </div>
        </div>
        <div class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-400">
            <div class="flex items-center space-x-4">
                @if(class_exists('App\\Models\\Customer') && $transaction->customer)
                    <div class="flex items-center">
                        <i class="fas fa-user mr-1 text-xs"></i>
                        {{ $transaction->customer->last_name . ', ' ?? '' }}
                        {{ $transaction->customer->first_name ?? '' }}
                    </div>
                @elseif(class_exists('App\\Models\\Company') && $transaction->company)
                    <div class="flex items-center">
                        <i class="fas fa-briefcase mr-1 text-xs"></i>
                        {{ $transaction->company->name }}
                    </div>
                @else
                    <div class="text-gray-400 dark:text-gray-500">
                        /
                    </div>
                @endif
                <div class="flex items-center">
                    <i class="fas fa-shopping-bag mr-1 text-xs"></i>
                    {{ $transaction->items_count ?? $transaction->items->count() }} article{{ ($transaction->items_count ?? $transaction->items->count()) > 1 ? 's' : '' }}
                </div>
            </div>
            <div>{{ $transaction->created_at->format('d/m/Y H:i') }}</div>
        </div>
    </div>

    <!-- Desktop Layout -->
    <div class="hidden md:flex items-center">{{ $transaction->transaction_number }}</div>
    <div class="hidden md:flex items-center justify-center">
        <span class="px-2 py-1 text-xs rounded-full bg-{{ $transaction->transaction_type === 'ticket' ? 'blue' : 'purple' }}-100 text-{{ $transaction->transaction_type === 'ticket' ? 'blue' : 'purple' }}-800 dark:bg-{{ $transaction->transaction_type === 'ticket' ? 'blue' : 'purple' }}-900/30 dark:text-{{ $transaction->transaction_type === 'ticket' ? 'blue' : 'purple' }}-400 capitalize">
            {{ $transaction->transaction_type }}
        </span>
    </div>
    <div class="hidden md:flex items-center justify-center">
        @if(class_exists('App\\Models\\Customer') && $transaction->customer)
            <div class="flex items-center">
                <i class="fas fa-user mr-2 text-gray-400 text-sm"></i>
                <span class="text-sm">
                    {{ $transaction->customer->last_name . ', ' ?? '' }}
                    <small class="text-gray-600 dark:text-gray-400">{{ $transaction->customer->first_name ?? '' }}</small>
                </span>
            </div>
        @elseif(class_exists('App\\Models\\Company') && $transaction->company)
            <div class="flex items-center">
                <i class="fas fa-briefcase mr-2 text-gray-400 text-sm"></i>
                <span class="text-sm">{{ $transaction->company->name }}</span>
            </div>
        @else
            <div class="text-gray-400 dark:text-gray-500">
                /
            </div>
        @endif
    </div>
    <div class="hidden md:flex items-center justify-center">
        {{ $transaction->items_count ?? $transaction->items->count() }}
    </div>
    <div class="hidden md:flex items-center justify-center font-bold">
        € {{ number_format($transaction->total_amount, 2, ',', ' ') }}
    </div>
    <div class="hidden md:flex items-center justify-center">
        {{ $transaction->created_at->format('d/m/Y H:i') }}
    </div>
</div>
