<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('session_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('session_id')->index();
            $table->unsignedBigInteger('variant_id');
            $table->unsignedBigInteger('stock_id');
            $table->string('article_name');
            $table->string('variant_reference')->nullable();
            $table->string('barcode')->nullable();
            $table->decimal('quantity', 10, 3);
            $table->decimal('unit_price', 15, 4);
            $table->decimal('total_price', 15, 4);
            $table->decimal('tax_rate', 5, 2)->default(21);
            $table->decimal('cost_price', 15, 4)->nullable();
            $table->json('attributes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('session_items');
    }
};
