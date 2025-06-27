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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_number', 20)->unique(); // CLI-2024-001
            $table->string('gender', 5)->nullable(); // 'M', 'F', 'Other'
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('email', 320)->unique(); // RFC 5321 limite
            $table->string('phone', 20)->nullable();
            $table->date('birth_date')->nullable();
            $table->integer('loyalty_points')->default(0); // Cache performance
            $table->string('loyalty_tier', 20)->default('bronze'); // 'bronze', 'silver', 'gold', 'platinum'
            $table->decimal('total_purchases', 15, 4)->default(0);
            $table->boolean('marketing_consent')->default(false); // RGPD
            $table->timestamp('last_visit_at')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes('deleted_at', 0);
            $table->timestamps();// Ajoute deleted_at pour SoftDeletes
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
