<?php

namespace App\Http\Controllers;

use App\Models\Variant;
use App\Models\Stock;
use Illuminate\Http\Request;

class StockController extends Controller
{
    /**
     * Display a listing of stocks for a variant
     */
    public function index(Variant $variant)
    {
        $stocks = $variant->stocks()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('panel.stocks.index', compact('variant', 'stocks'));
    }

    /**
     * Store a newly created stock entry
     */
    public function store(Request $request, Variant $variant)
    {
        $validated = $request->validate([
            'buy_price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'lot_reference' => 'nullable|string|max:100',
            'expiry_date' => 'nullable|date|after:today'
        ]);

        try {
            $stock = $variant->stocks()->create($validated);

            // Log de l'ajout de stock (à implémenter plus tard)
            // $this->logStockMovement($variant, 'add', $validated['quantity'], 'Ajout manuel');

            return redirect()
                ->back()
                ->with('success', 'Stock ajouté avec succès');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Erreur lors de l\'ajout: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified stock entry
     */
    public function update(Request $request, Variant $variant, Stock $stock)
    {
        $validated = $request->validate([
            'buy_price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'lot_reference' => 'nullable|string|max:100',
            'expiry_date' => 'nullable|date'
        ]);

        try {
            $oldQuantity = $stock->quantity;
            $stock->update($validated);

            // Log de la modification de stock (à implémenter plus tard)
            $difference = $validated['quantity'] - $oldQuantity;
            if ($difference != 0) {
                $type = $difference > 0 ? 'add' : 'remove';
                // $this->logStockMovement($variant, $type, abs($difference), 'Modification manuelle');
            }

            return redirect()
                ->back()
                ->with('success', 'Stock mis à jour avec succès');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified stock entry
     */
    public function destroy(Variant $variant, Stock $stock)
    {
        try {
            $quantity = $stock->quantity;
            $stock->delete();

            // Log de la suppression de stock (à implémenter plus tard)
            // $this->logStockMovement($variant, 'remove', $quantity, 'Suppression du lot');

            return redirect()
                ->back()
                ->with('success', 'Lot de stock supprimé avec succès');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Adjust stock quantity for a variant
     */
    public function adjust(Request $request, Variant $variant)
    {
        $validated = $request->validate([
            'adjustment_type' => 'required|in:add,remove,set',
            'quantity' => 'required|integer|min:0',
            'reason' => 'required|string|max:255',
            'buy_price' => 'required_if:adjustment_type,add|nullable|numeric|min:0',
            'lot_reference' => 'nullable|string|max:100'
        ]);

        try {
            $currentStock = $variant->total_stock;
            $newQuantity = $validated['quantity'];

            switch ($validated['adjustment_type']) {
                case 'add':
                    // Ajouter du stock
                    $variant->stocks()->create([
                        'buy_price' => $validated['buy_price'],
                        'quantity' => $newQuantity,
                        'lot_reference' => $validated['lot_reference'] ?? 'ADJ-' . now()->format('YmdHis')
                    ]);
                    break;

                case 'remove':
                    // Retirer du stock (FIFO)
                    $this->removeStock($variant, $newQuantity);
                    break;

                case 'set':
                    // Définir le stock exact
                    $difference = $newQuantity - $currentStock;
                    if ($difference > 0) {
                        // Ajouter la différence
                        $variant->stocks()->create([
                            'buy_price' => $variant->article->buy_price ?? 0,
                            'quantity' => $difference,
                            'lot_reference' => 'SET-' . now()->format('YmdHis')
                        ]);
                    } elseif ($difference < 0) {
                        // Retirer la différence
                        $this->removeStock($variant, abs($difference));
                    }
                    break;
            }

            // Log de l'ajustement (à implémenter plus tard)
            // $this->logStockMovement($variant, $validated['adjustment_type'], $newQuantity, $validated['reason']);

            return redirect()
                ->back()
                ->with('success', 'Stock ajusté avec succès');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Erreur lors de l\'ajustement: ' . $e->getMessage());
        }
    }

    /**
     * Get stock alerts (low stock, expired, expiring soon)
     */
    public function alerts()
    {
        // Stock faible (moins de 5 unités)
        $lowStock = Variant::whereHas('stocks', function ($query) {
            $query->selectRaw('SUM(quantity) as total')
                ->groupBy('variant_id')
                ->havingRaw('SUM(quantity) <= 5 AND SUM(quantity) > 0');
        })->with(['article', 'stocks'])->get();

        // Stock expiré
        $expiredStock = Stock::expired()
            ->with(['variant.article'])
            ->get();

        // Stock qui expire bientôt
        $expiringSoonStock = Stock::expiringSoon()
            ->with(['variant.article'])
            ->get();

        return view('panel.stocks.alerts', compact('lowStock', 'expiredStock', 'expiringSoonStock'));
    }

    /**
     * Export stock report
     */
    public function export(Request $request)
    {
        $stocks = Stock::with(['variant.article.category'])
            ->orderBy('created_at', 'desc')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="stock-report-' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($stocks) {
            $file = fopen('php://output', 'w');

            // En-têtes CSV
            fputcsv($file, [
                'Article',
                'Variant',
                'Code-barres',
                'Quantité',
                'Prix d\'achat',
                'Valeur totale',
                'Lot',
                'Date d\'expiration',
                'Statut',
                'Date création'
            ]);

            // Données
            foreach ($stocks as $stock) {
                $status = 'Actif';
                if ($stock->is_expired) {
                    $status = 'Expiré';
                } elseif ($stock->is_expiring_soon) {
                    $status = 'Expire bientôt';
                } elseif ($stock->quantity <= 0) {
                    $status = 'Épuisé';
                }

                fputcsv($file, [
                    $stock->variant->article->name,
                    $stock->variant->full_name,
                    $stock->variant->barcode,
                    $stock->quantity,
                    $stock->buy_price,
                    $stock->total_value,
                    $stock->lot_reference,
                    $stock->expiry_date?->format('d/m/Y'),
                    $status,
                    $stock->created_at->format('d/m/Y H:i')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Remove stock using FIFO method
     */
    private function removeStock(Variant $variant, int $quantityToRemove)
    {
        $stocks = $variant->stocks()
            ->where('quantity', '>', 0)
            ->orderBy('created_at', 'asc') // FIFO
            ->get();

        $remaining = $quantityToRemove;

        foreach ($stocks as $stock) {
            if ($remaining <= 0) {
                break;
            }

            if ($stock->quantity <= $remaining) {
                // Consommer tout ce stock
                $remaining -= $stock->quantity;
                $stock->update(['quantity' => 0]);
            } else {
                // Consommer partiellement ce stock
                $stock->update(['quantity' => $stock->quantity - $remaining]);
                $remaining = 0;
            }
        }

        if ($remaining > 0) {
            throw new \Exception("Stock insuffisant. Il manque {$remaining} unités.");
        }
    }

    /**
     * Log stock movement (à implémenter plus tard avec une table dédiée)
     */
    private function logStockMovement(Variant $variant, string $type, int $quantity, string $reason)
    {
        // TODO: Implémenter le logging des mouvements de stock
        // Créer une table stock_movements avec:
        // - variant_id, type, quantity, reason, user_id, created_at
    }
}
