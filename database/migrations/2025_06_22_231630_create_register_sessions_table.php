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
        Schema::create('register_sessions', function (Blueprint $table) {
            $table->string('id')->primary(); // session_id
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('cash_register_id')->nullable();
            $table->json('customer_data')->nullable();
            $table->json('discounts_data')->nullable();
            $table->decimal('total_amount', 15, 4)->default(0);
            $table->integer('items_count')->default(0);
            $table->enum('status', ['active', 'pending', 'completed', 'cancelled'])->default('active');
            $table->timestamp('last_activity')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['cash_register_id', 'status']);
            $table->index('last_activity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('register_sessions');
    }
};
