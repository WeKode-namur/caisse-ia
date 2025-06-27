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
        Schema::create('items_unknown', function (Blueprint $table) {
            $table->id();
            $table->string('article_name');
            $table->text('description')->nullable();
            $table->decimal('price', 15, 4);
            $table->decimal('quantity', 10, 3);
            $table->string('status')->default('new'); // new, lié, fermé, annulé
            $table->uuid('id_variant')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items_unknown');
    }
};
