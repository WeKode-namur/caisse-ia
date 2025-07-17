<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('unknown_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaction_item_id');
            $table->string('nom');
            $table->text('description')->nullable();
            $table->decimal('prix', 15, 4);
            $table->integer('tva');
            $table->text('note_interne')->nullable();
            $table->boolean('est_regularise')->default(false);
            $table->timestamps();

            $table->foreign('transaction_item_id')->references('id')->on('transaction_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unknown_items');
    }
};
