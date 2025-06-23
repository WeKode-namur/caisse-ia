<?php

namespace App\Helpers;

class TransactionHelper
{
    public static function translateStatus($status)
    {
        $translations = [
            'paid' => 'Payé',
            'pending' => 'En attente',
            'cancelled' => 'Annulé',
            'refunded' => 'Remboursé',
            'failed' => 'Échoué',
            'processing' => 'En cours',
            'completed' => 'Terminé',
            'active' => 'Actif',
            'inactive' => 'Inactif',
            'draft' => 'Brouillon',
            'published' => 'Publié',
            'archived' => 'Archivé'
        ];
        
        return $translations[strtolower($status)] ?? ucfirst($status);
    }
} 