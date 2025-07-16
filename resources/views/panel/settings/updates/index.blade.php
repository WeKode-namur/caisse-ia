<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <!-- En-tête avec recherche -->
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Historique des mises à jour</h3>
                            <p class="mt-1 text-sm text-gray-500">Consultez l'historique complet des mises à jour de
                                votre système</p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <!-- Barre de recherche -->
                            <div class="relative">
                                <input type="text"
                                       id="searchInput"
                                       placeholder="Rechercher une version ou un contenu..."
                                       class="w-80 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contenu principal -->
                <div class="p-6">
                    <!-- Stats -->
                    <div id="statsContainer" class="mb-6">
                        <!-- Les stats seront chargées en AJAX -->
                    </div>

                    <!-- Tableau des versions -->
                    <div id="versionsContainer">
                        <!-- Le tableau sera chargé en AJAX -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal version (x-modal) -->
    <x-modal name="update-version" size="7xl" :footer="false" :showHeader="true">
        <x-slot name="title">
            <span id="modalTitle">Détails de la mise à jour</span>
        </x-slot>
        <div id="modalContent" class="prose prose-indigo max-w-none">
            <!-- Le contenu markdown sera injecté ici -->
        </div>
    </x-modal>

    @push('scripts')
        <script>
            let currentSearch = '';
            let searchTimeout;

            document.addEventListener('DOMContentLoaded', function () {
                loadVersions();
                loadStats();
                document.getElementById('searchInput').addEventListener('input', function (e) {
                    currentSearch = e.target.value;
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        if (currentSearch.trim() === '') {
                            loadVersions();
                        } else {
                            searchVersions(currentSearch);
                        }
                    }, 300);
                });
            });

            function loadVersions() {
                fetch('{{ route("settings.updates.versions") }}', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => {
                        if (response.status === 401 || response.status === 403) {
                            window.location.href = '{{ route("settings.index") }}';
                            return;
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data) {
                            renderVersionsTable(data);
                        }
                    })
                    .catch(error => {
                        console.error('Erreur lors du chargement des versions:', error);
                        showNotification('Erreur lors du chargement des versions', 'error');
                    });
            }

            function searchVersions(search) {
                fetch(`{{ route("settings.updates.search") }}?search=${encodeURIComponent(search)}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => {
                        if (response.status === 401 || response.status === 403) {
                            window.location.href = '{{ route("settings.index") }}';
                            return;
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data) {
                            renderVersionsTable(data);
                        }
                    })
                    .catch(error => {
                        console.error('Erreur lors de la recherche:', error);
                        showNotification('Erreur lors de la recherche', 'error');
                    });
            }

            function loadStats() {
                fetch('{{ route("settings.updates.stats") }}', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => {
                        if (response.status === 401 || response.status === 403) {
                            window.location.href = '{{ route("settings.index") }}';
                            return;
                        }
                        return response.text();
                    })
                    .then(html => {
                        if (html) {
                            document.getElementById('statsContainer').innerHTML = html;
                        }
                    })
                    .catch(error => {
                        console.error('Erreur lors du chargement des stats:', error);
                    });
            }

            function renderVersionsTable(versions) {
                const container = document.getElementById('versionsContainer');
                if (versions.length === 0) {
                    container.innerHTML = `
                    <div class="text-center py-12">
                        <i class="fas fa-file-alt text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500">Aucune mise à jour trouvée</p>
                    </div>
                `;
                    return;
                }
                let html = `
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Version
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Titre
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
            `;
                versions.forEach(version => {
                    html += `
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                ${version.version}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">${version.title}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            ${version.modified_at}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button onclick="openVersionModal('${version.version}', b64DecodeUnicode('${btoa(unescape(encodeURIComponent(version.title)))}'))"
                                    class="text-indigo-600 hover:text-indigo-900 transition-colors">
                                <i class="fas fa-eye mr-1"></i>
                                Voir
                            </button>
                        </td>
                    </tr>
                `;
                });
                html += `
                        </tbody>
                    </table>
                </div>
            `;
                container.innerHTML = html;
            }

            function openVersionModal(version, title) {
                document.getElementById('modalTitle').textContent = title;
                document.getElementById('modalContent').innerHTML = '<div class="text-center py-8"><i class="fas fa-spinner fa-spin text-2xl text-gray-400"></i><p class="mt-2 text-gray-500">Chargement...</p></div>';
                window.openModal('update-version');
                fetch(`{{ route("settings.updates.content") }}?version=${encodeURIComponent(version)}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => {
                        if (response.status === 401 || response.status === 403) {
                            window.location.href = '{{ route("settings.index") }}';
                            return;
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data && data.content) {
                            document.getElementById('modalContent').innerHTML = data.content;
                        } else {
                            document.getElementById('modalContent').innerHTML = '<p class="text-red-500">Erreur lors du chargement du contenu</p>';
                        }
                    })
                    .catch(error => {
                        console.error('Erreur lors du chargement du contenu:', error);
                        document.getElementById('modalContent').innerHTML = '<p class="text-red-500">Erreur lors du chargement du contenu</p>';
                    });
            }

            function showNotification(message, type = 'success') {
                if (typeof window.showNotification === 'function') {
                    window.showNotification(message, type);
                } else {
                    alert(message);
                }
            }

            // Ajoute la fonction de décodage base64 unicode
            function b64DecodeUnicode(str) {
                return decodeURIComponent(Array.prototype.map.call(atob(str), function (c) {
                    return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
                }).join(''));
            }
        </script>
    @endpush
</x-app-layout>
