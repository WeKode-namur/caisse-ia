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
            if (!Schema::hasColumn('transaction_items', 'unit_price_ht')) {
                $table->decimal('unit_price_ht', 15, 4)->default(0)->after('quantity');
            }
            if (!Schema::hasColumn('transaction_items', 'unit_price_ttc')) {
                $table->decimal('unit_price_ttc', 15, 4)->default(0)->after('unit_price_ht');
            }
            if (!Schema::hasColumn('transaction_items', 'total_price_ht')) {
                $table->decimal('total_price_ht', 15, 4)->default(0)->after('unit_price_ttc');
            }
            if (!Schema::hasColumn('transaction_items', 'total_price_ttc')) {
                $table->decimal('total_price_ttc', 15, 4)->default(0)->after('total_price_ht');
            }
            if (!Schema::hasColumn('transaction_items', 'tax_amount')) {
                $table->decimal('tax_amount', 15, 4)->default(0)->after('tax_rate');
            }
            if (!Schema::hasColumn('transaction_items', 'discount_rate')) {
                $table->decimal('discount_rate', 5, 2)->default(0)->after('tax_amount');
            }
            if (!Schema::hasColumn('transaction_items', 'discount_amount')) {
                $table->decimal('discount_amount', 15, 4)->default(0)->after('discount_rate');
            }
            if (!Schema::hasColumn('transaction_items', 'total_cost')) {
                $table->decimal('total_cost', 15, 4)->default(0)->after('discount_amount');
            }
            if (!Schema::hasColumn('transaction_items', 'margin')) {
                $table->decimal('margin', 15, 4)->default(0)->after('total_cost');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction_items', function (Blueprint $table) {
            if (Schema::hasColumn('transaction_items', 'unit_price_ht')) {
                $table->dropColumn('unit_price_ht');
            }
            if (Schema::hasColumn('transaction_items', 'unit_price_ttc')) {
                $table->dropColumn('unit_price_ttc');
            }
            if (Schema::hasColumn('transaction_items', 'total_price_ht')) {
                $table->dropColumn('total_price_ht');
            }
            if (Schema::hasColumn('transaction_items', 'total_price_ttc')) {
                $table->dropColumn('total_price_ttc');
            }
            if (Schema::hasColumn('transaction_items', 'tax_amount')) {
                $table->dropColumn('tax_amount');
            }
            if (Schema::hasColumn('transaction_items', 'discount_rate')) {
                $table->dropColumn('discount_rate');
            }
            if (Schema::hasColumn('transaction_items', 'discount_amount')) {
                $table->dropColumn('discount_amount');
            }
            if (Schema::hasColumn('transaction_items', 'total_cost')) {
                $table->dropColumn('total_cost');
            }
            if (Schema::hasColumn('transaction_items', 'margin')) {
                $table->dropColumn('margin');
            }
        });
    }
};
