@if($paginator->count() > 0)
    <div class="space-y-4">
        @foreach($paginator as $client)
            <a href="{{ $client->type === 'customer' ? route('clients.customers.show', $client) : route('clients.companies.show', $client) }}" class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700/50 rounded-lg transition-colors group article-row hover:bg-gray-100 dark:hover:bg-violet-950 cursor-pointer">
                <!-- Avatar -->
                <div class="w-12 h-12 rounded-full flex items-center justify-center mr-4 duration-500
                    {{ $client->type === 'customer' ? 'bg-blue-100 group-hover:bg-blue-200 dark:bg-blue-800 dark:group-hover:bg-blue-700 text-blue-600 dark:text-blue-400' : 'bg-green-100 group-hover:bg-green-200 dark:bg-green-800 dark:group-hover:bg-green-700 text-green-600 dark:text-green-400' }}">
                    @if($client->type === 'customer')
                        <span class="font-medium text-sm">
                            {{ substr($client->first_name, 0, 1) }}{{ substr($client->last_name, 0, 1) }}
                        </span>
                    @else
                        <span class="font-medium text-sm">
                            {{ substr($client->name, 0, 2) }}
                        </span>
                    @endif
                </div>

                <!-- Informations -->
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <h4 class="font-medium text-gray-900 dark:text-white">{{ $client->display_name }}</h4>
                        <span class="px-2 py-1 text-xs rounded-full
                            {{ $client->type === 'customer' ? 'bg-blue-100 dark:bg-blue-800 text-blue-600 dark:text-blue-400' : 'bg-green-100 dark:bg-green-800 text-green-600 dark:text-green-400' }}">
                            {{ $client->type === 'customer' ? 'Particulier' : 'Entreprise' }}
                        </span>
                        @if(!$client->is_active)
                            <span class="px-2 py-1 text-xs rounded-full bg-red-100 dark:bg-red-800 text-red-800 dark:text-red-200">
                                Inactif
                            </span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $client->number }}</p>
                    @if($client->email)
                        <p class="text-sm text-gray-500">{{ $client->email }}</p>
                    @endif
                    @if($client->phone)
                        <p class="text-sm text-gray-500">{{ $client->phone }}</p>
                    @endif
                </div>

                <!-- Points fidélité -->
                <div class="text-right mr-4">
                    <p class="text-sm font-medium text-gray-900 dark:text-gray-200">{{ number_format($client->loyalty_points) }}</p>
                    <p class="text-xs text-gray-500">points</p>
                </div>
            </a>
        @endforeach
    </div>

    <!-- Pagination AJAX -->
    <x-pagination :currentPage="$paginator->currentPage()" :lastPage="$paginator->lastPage()" />
@else
    <div class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun client trouvé</h3>
        <p class="mt-1 text-sm text-gray-500">
            @if($search || $status !== 'all' || $type !== 'all')
                Essayez de modifier vos critères de recherche.
            @else
                Commencez par créer votre premier client.
            @endif
        </p>
        <div class="mt-6">
            <a href="{{ route('clients.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Nouveau Client
            </a>
        </div>
    </div>
@endif
 