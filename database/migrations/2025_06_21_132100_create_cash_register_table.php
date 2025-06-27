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
        // ===== MOYENS DE PAIEMENT =====

        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('code', 20)->unique();
            $table->string('icon', 100)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('order_column')->default(0);
            $table->boolean('requires_reference')->default(false);
            $table->boolean('allows_partial_payment')->default(true);
            $table->decimal('processing_fee_percentage', 5, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('cash_registers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location')->nullable();
            $table->string('mac_address', 17)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ===== REMISES ET PROMOTIONS =====

        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 50)->unique()->nullable();
            $table->string('type', 20); // 'percentage', 'fixed_amount', 'buy_x_get_y'
            $table->decimal('value', 10, 2);
            $table->decimal('min_amount', 15, 4)->default(0);
            $table->decimal('max_discount', 15, 4)->nullable();
            $table->integer('max_uses')->nullable();
            $table->integer('current_uses')->default(0);

            // Conditions
            $table->string('applicable_to', 20)->default('total'); // 'total', 'category', 'product'
            $table->unsignedBigInteger('target_category_id')->nullable();
            $table->unsignedBigInteger('target_variant_id')->nullable();

            // Période de validité
            $table->boolean('is_active')->default(true);
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();

            $table->timestamps();
        });

        // ===== CARTES CADEAUX =====

        Schema::create('gift_cards', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->decimal('initial_amount', 15, 4);
            $table->decimal('remaining_amount', 15, 4);

            // Propriétaire
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();

            // Gestion
            $table->unsignedBigInteger('issued_by');
            $table->date('expires_at')->nullable();
            $table->boolean('is_active')->default(true);

            // Métadonnées
            $table->string('design_template', 100)->nullable();
            $table->text('message')->nullable();

            $table->timestamps();
        });

        // ===== TRANSACTIONS =====

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number', 50)->unique();
            $table->string('transaction_type', 10); // 'ticket', 'invoice', 'return', 'refund'
            $table->boolean('is_wix_release')->default(false);

            // Clients
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();

            // Retours
            $table->unsignedBigInteger('parent_transaction_id')->nullable();
            $table->string('return_reason')->nullable();
            $table->string('return_type', 20)->nullable(); // 'exchange', 'refund', 'credit'

            // Montants détaillés
            $table->decimal('subtotal_ht', 15, 4)->default(0);
            $table->decimal('subtotal_ttc', 15, 4)->default(0);
            $table->decimal('tax_amount', 15, 4)->default(0);
            $table->decimal('discount_amount', 15, 4)->default(0);
            $table->json('discounts_data')->nullable();
            $table->decimal('total_amount', 15, 4);
            $table->integer('items_count')->default(0);

            // Coûts et marges
            $table->decimal('total_cost', 15, 4)->default(0);
            $table->decimal('total_margin', 15, 4)->default(0);
            $table->decimal('margin_percentage', 8, 2)->default(0);

            // Métadonnées
            $table->string('currency', 3)->default('EUR');
            $table->decimal('exchange_rate', 10, 6)->default(1.000000);
            $table->text('notes')->nullable();

            // Statuts
            $table->string('status', 15)->default('completed'); // 'completed', 'cancelled', 'refunded'
            $table->string('payment_status', 15)->default('paid'); // 'paid', 'pending', 'cancelled'

            // Traçabilité
            $table->unsignedBigInteger('cashier_id');
            $table->unsignedBigInteger('cash_register_id')->nullable();
            $table->string('pos_terminal', 50)->nullable();

            // Audit
            $table->unsignedBigInteger('voided_by')->nullable();
            $table->timestamp('voided_at')->nullable();
            $table->text('void_reason')->nullable();

            $table->timestamps();

            $table->comment('CHECK ((customer_id IS NOT NULL AND company_id IS NULL) OR (customer_id IS NULL AND company_id IS NOT NULL) OR (customer_id IS NULL AND company_id IS NULL))');
        });

        Schema::create('transaction_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaction_id');

            // Produit (snapshot au moment de la vente)
            $table->uuid('variant_id')->nullable();
            $table->uuid('stock_id')->nullable();
            $table->string('article_name');
            $table->string('variant_reference', 100)->nullable();
            $table->json('variant_attributes')->nullable(); // {"couleur": "Rouge", "taille": "L"}
            $table->string('barcode')->nullable();

            // Prix et quantités
            $table->decimal('quantity', 8, 3);

            $table->decimal('unit_price_ht', 15, 4)->default(0);
            $table->decimal('unit_price_ttc', 15, 4)->default(0);
            $table->decimal('total_price_ht', 15, 4)->default(0);
            $table->decimal('total_price_ttc', 15, 4)->default(0);
            $table->decimal('tax_amount', 15, 4)->default(0);
            $table->decimal('tax_rate', 5, 2);
            $table->decimal('discount_rate', 5, 2)->default(0);
            $table->decimal('discount_amount', 15, 4)->default(0);
            $table->decimal('total_cost', 15, 4)->default(0);
            $table->decimal('margin', 15, 4)->default(0);


            $table->timestamps();
        });

        Schema::create('transaction_stock_movements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaction_item_id');
            $table->unsignedBigInteger('stock_id');

            $table->decimal('quantity_used', 8, 3); // Peut être négatif pour retours
            $table->decimal('cost_price', 15, 4);
            $table->decimal('total_cost', 15, 4);
            $table->string('lot_reference', 100)->nullable();

            $table->timestamps();
        });

        Schema::create('transaction_discounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaction_id');
            $table->unsignedBigInteger('discount_id')->nullable();

            $table->string('discount_type', 20); // 'percentage', 'fixed', 'loyalty', 'manual'
            $table->string('discount_name');
            $table->string('discount_code', 50)->nullable();
            $table->decimal('discount_value', 10, 2);
            $table->decimal('discount_amount', 15, 4);

            $table->string('applied_to', 20)->default('total'); // 'total', 'item', 'shipping'
            $table->unsignedBigInteger('target_item_id')->nullable();

            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaction_id');
            $table->unsignedBigInteger('payment_method_id');

            $table->decimal('amount', 15, 4);
            $table->string('currency', 3)->default('EUR');
            $table->decimal('exchange_rate', 10, 6)->default(1.000000);

            // Références externes
            $table->string('reference')->nullable();
            $table->string('authorization_code', 100)->nullable();
            $table->string('transaction_id_external')->nullable();

            // Frais
            $table->decimal('processing_fee', 15, 4)->default(0);

            // Statuts et traçabilité
            $table->string('status', 15)->default('completed'); // 'pending', 'completed', 'failed', 'refunded'
            $table->string('failure_reason')->nullable();

            // Timestamps
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->unsignedBigInteger('refunded_by')->nullable();

            $table->timestamps();
        });

        Schema::create('gift_card_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gift_card_id');
            $table->unsignedBigInteger('transaction_id')->nullable();

            $table->string('transaction_type', 20); // 'issued', 'used', 'refunded', 'expired', 'topped_up'
            $table->decimal('amount', 15, 4);
            $table->decimal('balance_before', 15, 4);
            $table->decimal('balance_after', 15, 4);

            $table->string('reference', 100)->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('processed_by');

            $table->timestamps();
        });

        // ===== FOREIGN KEY CONSTRAINTS =====
        // Toutes les contraintes de clés étrangères sont ajoutées après la création de toutes les tables

        // Transaction relations
        Schema::table('transactions', function (Blueprint $table) {
//            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
//            $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');
            $table->foreign('parent_transaction_id')->references('id')->on('transactions')->onDelete('set null');
            $table->foreign('cashier_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('cash_register_id')->references('id')->on('cash_registers')->onDelete('set null');
            $table->foreign('voided_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::table('transaction_items', function (Blueprint $table) {
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
//            $table->foreign('variant_id')->references('id')->on('variants')->onDelete('restrict');
//            $table->foreign('stock_id')->references('id')->on('stocks')->onDelete('restrict');
        });

        Schema::table('transaction_stock_movements', function (Blueprint $table) {
            $table->foreign('transaction_item_id')->references('id')->on('transaction_items')->onDelete('cascade');
            $table->foreign('stock_id')->references('id')->on('stocks')->onDelete('restrict');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
            $table->foreign('payment_method_id')->references('id')->on('payment_methods')->onDelete('restrict');
            $table->foreign('refunded_by')->references('id')->on('users')->onDelete('set null');
        });

        // Discount relations
        Schema::table('discounts', function (Blueprint $table) {
            $table->foreign('target_category_id')->references('id')->on('categories')->onDelete('set null');
            $table->foreign('target_variant_id')->references('id')->on('variants')->onDelete('set null');
        });

        Schema::table('transaction_discounts', function (Blueprint $table) {
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
            $table->foreign('discount_id')->references('id')->on('discounts')->onDelete('set null');
            $table->foreign('target_item_id')->references('id')->on('transaction_items')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict');
        });

        // Gift card relations
        Schema::table('gift_cards', function (Blueprint $table) {
//            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
//            $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');
            $table->foreign('issued_by')->references('id')->on('users')->onDelete('restrict');
        });

        Schema::table('gift_card_transactions', function (Blueprint $table) {
            $table->foreign('gift_card_id')->references('id')->on('gift_cards')->onDelete('cascade');
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('set null');
            $table->foreign('processed_by')->references('id')->on('users')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Supprimer dans l'ordre inverse pour éviter les contraintes de clés étrangères
        Schema::dropIfExists('gift_card_transactions');
        Schema::dropIfExists('gift_cards');
        Schema::dropIfExists('transaction_discounts');
        Schema::dropIfExists('discounts');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('transaction_stock_movements');
        Schema::dropIfExists('transaction_items');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('cash_registers');
        Schema::dropIfExists('payment_methods');
    }
};
