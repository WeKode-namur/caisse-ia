<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transaction_items', function (Blueprint $table) {
            $table->decimal('unit_price_ht', 15, 4)->default(0)->change();
            $table->decimal('unit_price_ttc', 15, 4)->default(0)->change();
            $table->decimal('total_price_ht', 15, 4)->default(0)->change();
            $table->decimal('total_price_ttc', 15, 4)->default(0)->change();
            $table->decimal('tax_amount', 15, 4)->default(0)->change();
            $table->decimal('discount_rate', 5, 2)->default(0)->change();
            $table->decimal('discount_amount', 15, 4)->default(0)->change();
            $table->decimal('total_cost', 15, 4)->default(0)->change();
            $table->decimal('margin', 15, 4)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Pas de rollback automatique pour les defaults
    }
};
