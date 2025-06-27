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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('company_number', 20)->unique(); // ENT-2024-001
            $table->string('name', 255);
            $table->string('legal_name', 255)->nullable(); // Raison sociale complète
            $table->string('company_number_be', 15)->unique()->nullable(); // 0123.456.789
            $table->string('vat_number', 15)->unique()->nullable(); // BE0123456789
            $table->string('company_type', 20)->nullable(); // 'SRL', 'SA', 'SPRL', 'Indépendant'
            $table->string('legal_representative', 255)->nullable();
            $table->string('email', 320)->nullable();
            $table->string('phone', 20)->nullable();
            $table->integer('payment_terms')->default(30); // Jours de paiement
            $table->decimal('credit_limit', 15, 4)->default(0);
            $table->integer('loyalty_points')->default(0); // Points fidélité B2B
            $table->string('loyalty_tier', 20)->default('bronze');
            $table->decimal('total_purchases', 15, 4)->default(0);
            $table->timestamp('last_order_at')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes('deleted_at', 0); // Ajoute deleted_at pour SoftDeletes
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
