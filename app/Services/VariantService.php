<?php

namespace App\Services;

use App\Models\{Article, AttributeValue, Stock, Variant};
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\{DB};

class VariantService
{
    public function createVariant(Article $article, array $data): Variant
    {
        return DB::transaction(function () use ($article, $data) {
            // Créer le variant
            $variant = $article->variants()->create([
                'barcode' => $data['barcode'] ?? null,
                'reference' => $data['reference'] ?? null,
                'sell_price' => $data['sell_price'] ?? null,
                'buy_price' => $data['buy_price'] ?? null,
            ]);

            // Gérer les attributs
            $this->handleAttributes($variant, $data['attributes'] ?? []);

            // Gérer le stock
            if (isset($data['stock']) && $this->hasStockData($data['stock'])) {
                $this->handleStock($variant, $data['stock']);
            }

            // Gérer les images
            if (isset($data['images'])) {
                $this->handleImages($variant, $data['images']);
            }

            return $variant->load(['attributeValues.attribute', 'stocks', 'medias']);
        });
    }

    public function updateVariant(Variant $variant, array $data): Variant
    {
        return DB::transaction(function () use ($variant, $data) {
            // Mettre à jour les données de base
            $variant->update([
                'barcode' => $data['barcode'] ?? $variant->barcode,
                'reference' => $data['reference'] ?? $variant->reference,
                'sell_price' => $data['sell_price'] ?? $variant->sell_price,
                'buy_price' => $data['buy_price'] ?? $variant->buy_price,
            ]);

            // Mettre à jour les attributs
            if (isset($data['attributes'])) {
                $this->handleAttributes($variant, $data['attributes']);
            }

            // Mettre à jour le stock
            if (isset($data['stock']) && $this->hasStockData($data['stock'])) {
                $this->handleStock($variant, $data['stock']);
            }

            // Ajouter de nouvelles images
            if (isset($data['images'])) {
                $this->handleImages($variant, $data['images']);
            }

            return $variant->load(['attributeValues.attribute', 'stocks', 'medias']);
        });
    }

    private function handleAttributes(Variant $variant, array $attributes): void
    {
        // Supprimer les anciennes relations
        $variant->attributeValues()->detach();

        foreach ($attributes as $attrData) {
            if (empty($attrData['attribute_id']) || empty($attrData['value'])) {
                continue;
            }

            // Vérifier que l'attribut est actif
            $attribute = Attribute::active()->find($attrData['attribute_id']);
            if (!$attribute) {
                continue;
            }

            // Créer ou récupérer la valeur d'attribut (active)
            $attributeValue = AttributeValue::active()->firstOrCreate([
                'attribute_id' => $attrData['attribute_id'],
                'value' => $attrData['value'],
                'second_value' => $attrData['second_value'] ?? null,
            ], [
                'actif' => true
            ]);

            // Associer au variant
            $variant->attributeValues()->attach($attributeValue->id);
        }
    }

    private function handleStock(Variant $variant, array $stockData): void
    {
        if (empty($stockData['quantity']) || $stockData['quantity'] <= 0) {
            return;
        }

        Stock::updateOrCreate(
            ['variant_id' => $variant->id],
            [
                'buy_price' => $stockData['buy_price'] ?? 0,
                'quantity' => $stockData['quantity'],
                'lot_reference' => $stockData['lot_reference'] ?? null,
                'expiry_date' => isset($stockData['expiry_date']) ?
                    Carbon::parse($stockData['expiry_date']) : null,
            ]
        );
    }

    private function handleImages(Variant $variant, array $images): void
    {
        foreach ($images as $image) {
            if ($image instanceof UploadedFile) {
                $path = $image->store(
                    config('inventory.images.storage_path', 'variants'),
                    config('inventory.images.storage_disk', 'public')
                );

                $variant->medias()->create([
                    'path' => $path,
                    'type' => 'image'
                ]);
            }
        }
    }

    private function hasStockData(array $stockData): bool
    {
        return !empty($stockData['quantity']) && $stockData['quantity'] > 0;
    }

    public function deleteVariant(Variant $variant): bool
    {
        return DB::transaction(function () use ($variant) {
            // Supprimer les relations
            $variant->attributeValues()->detach();

            // Supprimer les stocks
            $variant->stocks()->delete();

            // Supprimer les médias (les fichiers seront supprimés automatiquement)
            $variant->medias()->delete();

            // Supprimer le variant
            return $variant->delete();
        });
    }

    public function generateBarcode(): string
    {
        $prefix = config('inventory.barcode.country_prefix', '541');
        $company = config('inventory.barcode.company_code', '007600');

        do {
            $product = str_pad(mt_rand(0, 99999), 5, '0', STR_PAD_LEFT);
            $code = $prefix . $company . $product;

            // Calcul du chiffre de contrôle EAN-13
            $sum = 0;
            for ($i = 0; $i < 12; $i++) {
                $sum += (int)$code[$i] * ($i % 2 === 0 ? 1 : 3);
            }
            $checkDigit = (10 - ($sum % 10)) % 10;
            $barcode = $code . $checkDigit;

        } while (Variant::where('barcode', $barcode)->exists());

        return $barcode;
    }

    /**
     * Génère un code-barres unique au format PREFIX1-PREFIX2-YYMMDD-XXXX
     */
    public static function generateCustomBarcode(): string
    {
        $prefix1 = config('custom.barcode.prefix_one', 'WK');
        $prefix2 = config('custom.barcode.prefix_two', 'NAM');
        $date = now()->format('ymd');

        $count = Variant::whereDate('created_at', now()->toDateString())->count() + 1;
        $compteur = str_pad($count, 4, '0', STR_PAD_LEFT);
        $barcode = "$prefix1$prefix2$date$compteur";

        while (Variant::where('barcode', $barcode)->exists()) {
            $count++;
            $compteur = str_pad($count, 4, '0', STR_PAD_LEFT);
            $barcode = "$prefix1$prefix2$date$compteur";
        }
        return $barcode;
    }
}
