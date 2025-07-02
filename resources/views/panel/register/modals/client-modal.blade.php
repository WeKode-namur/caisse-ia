<x-modal name="clients-list-modal" title="Rechercher un client" size="2xl" icon="users" iconColor="violet" :footer="false">
    <div class="flex-1 overflow-y-auto flex flex-col">
        <div class="space-y-4">

            <div class="p-4 flex-1 overflow-hidden flex flex-col">
                <!-- Barre de recherche -->
                <div class="border rounded flex items-center text-base">
                    <i class="fas fa-search px-3 text-gray-400"></i>
                    <input type="text" name="search_client" id="search_client" placeholder="Rechercher un client..." class="border-0 px-3 py-2 flex-1 outline-0 focus:text-black">
                </div>

                    <!-- Liste des clients -->
                <div class="mt-4 flex-1 overflow-y-auto">
                    <ul id="clients-results-list" role="list" class="divide-y divide-slate-200">
                        <!-- Résultats AJAX ici -->
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <script>
    let searchTimeout;
    const input = document.getElementById('search_client');
    const resultsList = document.getElementById('clients-results-list');

    input.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const q = this.value.trim();
        if (q.length < 2) {
            resultsList.innerHTML = '';
            return;
        }
        searchTimeout = setTimeout(() => {
            fetch(`/clients/search?q=${encodeURIComponent(q)}`)
                .then(r => r.json())
                .then(data => {
                    resultsList.innerHTML = '';
                    if (!data.length) {
                        resultsList.innerHTML = '<li class="py-4 text-center text-gray-400">Aucun client trouvé</li>';
                        return;
                    }
                    data.forEach(client => {
                        const li = document.createElement('li');
                        li.className = 'flex py-3 hover:bg-gray-50 cursor-pointer rounded px-2 transition-all group hover:shadow-sm';
                        li.innerHTML = `
                            <div class="h-12 w-12 ${client.type === 'company' ? 'bg-green-500' : 'bg-blue-500'} rounded-full flex items-center justify-center text-white font-medium">
                                ${client.name.split(' ').map(n => n[0]).join('').substring(0,2)}
                            </div>
                            <div class="ml-3 overflow-hidden flex-1">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-slate-900">${client.name}</p>
                                        <p class="text-sm text-slate-500 truncate">${client.email ?? ''}</p>
                                        ${client.company_number_be ? `<p class='text-xs text-slate-400 flex items-center'><i class='fas fa-building text-xs mr-1'></i>${client.company_number_be}</p>` : ''}
                                        <p class="text-xs text-slate-400 mt-1">${client.type === 'company' ? 'Entreprise' : 'Particulier'}</p>
                                    </div>
                                    <div class="text-right ml-4">
                                        <p class="text-sm font-medium text-green-600">${client.loyalty_points ?? 0}</p>
                                        <p class="text-xs text-slate-400">Points</p>
                                    </div>
                                </div>
                            </div>
                        `;
                        li.onclick = () => addClientToRegister(client);
                        resultsList.appendChild(li);
                    });
                });
        }, 250);
    });

    // Fonction à implémenter côté caisse pour lier le client à la session
    function addClientToRegister(client) {
        // Utilisation de la route nommée Laravel pour plus de robustesse
        fetch("{{ route('register.partials.customers.select') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                client_id: client.id,
                client_type: client.type
            })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                // Fermer le modal, afficher le client sur l'UI caisse, etc.
                window.dispatchEvent(new CustomEvent('client-selected', { detail: client }));
                closeModal('clients-list-modal');
            } else {
                alert(data.message || 'Erreur lors de l\'ajout du client');
            }
        });
    }
    </script>
</x-modal>
