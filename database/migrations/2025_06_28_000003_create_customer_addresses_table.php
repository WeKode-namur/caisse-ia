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
        Schema::create('customer_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('type', 20)->default('billing'); // 'billing', 'shipping'
            $table->string('street', 255);
            $table->string('number', 10);
            $table->string('city', 100);
            $table->string('postal_code', 10);
            $table->string('country', 100)->default('Belgium');
            $table->boolean('is_primary')->default(false);
            $table->softDeletes('deleted_at', 0);
            $table->timestamps();

            // Index pour améliorer les performances
            $table->index(['customer_id', 'type']);
            $table->index(['company_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_addresses');
    }
}; 