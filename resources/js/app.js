import './bootstrap';
import { LoadingUtils } from './utils/loading.js';

// Rendre disponible globalement
window.LoadingUtils = LoadingUtils;

// Gestion AJAX du tableau des transactions
window.addEventListener('DOMContentLoaded', () => {
    const wrapper = document.getElementById('transactions-table-wrapper');
    const statsWrapper = document.getElementById('transactions-stats-wrapper');
    if (!wrapper) return;

    // Fonction pour charger le tableau en AJAX
    async function loadTransactionsTable(url, formData = null) {
        wrapper.innerHTML = LoadingUtils.getLoadingHtmlSync('Chargement des transactions...');
        let fetchOptions = { method: 'GET', headers: { 'X-Requested-With': 'XMLHttpRequest' } };
        if (formData) {
            url += (url.includes('?') ? '&' : '?') + new URLSearchParams(formData).toString();
        }
        const response = await fetch(url, fetchOptions);
        const html = await response.text();
        wrapper.innerHTML = html;
        attachPaginationLinks();
    }

    // Fonction pour charger les stats en AJAX
    async function loadStats(url, formData = null) {
        if (!statsWrapper) return;
        
        let fetchOptions = { method: 'GET', headers: { 'X-Requested-With': 'XMLHttpRequest' } };
        if (formData) {
            url += (url.includes('?') ? '&' : '?') + new URLSearchParams(formData).toString();
        }
        url += (url.includes('?') ? '&' : '?') + 'stats_only=1';
        
        const response = await fetch(url, fetchOptions);
        const html = await response.text();
        statsWrapper.innerHTML = html;
    }

    // Fonction pour charger tableau et stats
    async function loadData(url, formData = null) {
        await Promise.all([
            loadTransactionsTable(url, formData),
            loadStats(url, formData)
        ]);
    }

    // Intercepter le formulaire de filtre (même s'il est en dehors du wrapper)
    const form = document.querySelector('form[action*="transactions"]');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(form);
            loadData(form.action, formData);
        });
        // Intercepter le bouton Réinitialiser
        const resetBtn = form.querySelector('a[href*="transactions"]');
        if (resetBtn) {
            resetBtn.addEventListener('click', function(e) {
                e.preventDefault();
                loadData(this.href);
            });
        }
    }

    // Intercepter la pagination
    function attachPaginationLinks() {
        wrapper.querySelectorAll('.pagination a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                loadTransactionsTable(this.href);
            });
        });
    }
    attachPaginationLinks();
});

