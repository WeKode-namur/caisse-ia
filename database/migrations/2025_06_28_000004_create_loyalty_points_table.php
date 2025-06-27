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
        Schema::create('loyalty_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('transaction_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('points'); // Peut être négatif
            $table->string('type', 15); // 'earned', 'spent', 'expired', 'bonus', 'adjustment'
            $table->string('description', 500)->nullable();
            $table->date('expires_at')->nullable(); // NULL = pas d'expiration
            $table->softDeletes('deleted_at', 0); // Ajoute deleted_at pour SoftDeletes
            $table->timestamps();

            // Index pour améliorer les performances
            $table->index(['customer_id', 'type']);
            $table->index(['company_id', 'type']);
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_points');
    }
}; 