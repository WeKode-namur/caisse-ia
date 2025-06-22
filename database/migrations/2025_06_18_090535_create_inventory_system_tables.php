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
        // ===== PHASE 1: Création de toutes les tables sans contraintes =====

        // 1. Categories
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 2. Types
        Schema::create('types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('category_id');
            $table->timestamps();
        });

        // 3. Subtypes
        Schema::create('subtypes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('type_id');
            $table->timestamps();
        });

        // 4. Attributes
        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Ex: "Taille", "Couleur", "Matière"
            $table->string('type'); // Ex: "select", "color", "text"
            $table->string('unit')->nullable(); // Ex: "cm", "kg", etc.
            $table->timestamps();
        });

        // 5. Attribute Values
        Schema::create('attribute_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attribute_id');
            $table->string('value'); // Ex: "S", "Rouge", "Coton"
            $table->string('second_value')->nullable(); // Pour des valeurs composées
            $table->integer('order')->default(0); // Ordre d'affichage
            $table->timestamps();
        });

        // 6. Articles
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('type_id')->nullable();
            $table->unsignedBigInteger('subtype_id')->nullable();
            $table->string('sell_price')->nullable(); // Prix de vente par défaut
            $table->string('buy_price')->nullable();  // Prix d'achat par défaut
            $table->integer('tva')->nullable(); // Taux de TVA en pourcentage
            $table->timestamps();
        });

        // 7. Variants
        Schema::create('variants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('article_id');
            $table->string('barcode')->unique()->nullable();
            $table->string('reference')->nullable();
            $table->string('sell_price')->nullable(); // Prix de vente spécifique à ce variant
            $table->string('buy_price')->nullable();  // Prix d'achat spécifique à ce variant
            $table->timestamps();
        });

        // 8. Variants Attribute Value (table de liaison)
        Schema::create('variants_attribute_value', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('variant_id');
            $table->unsignedBigInteger('attribute_value_id');
            $table->timestamps();

            // Index unique pour éviter les doublons
            $table->unique(['variant_id', 'attribute_value_id'], 'variant_attribute_unique');
        });

        // 9. Stocks
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('variant_id');
            $table->decimal('buy_price', 15, 2); // Prix d'achat réel pour ce lot
            $table->integer('quantity'); // Quantité en stock
            $table->string('lot_reference', 100)->nullable(); // Ex: ACHAT-2024-001
            $table->timestamp('expiry_date')->nullable(); // Pour les produits périssables
            $table->timestamps();
        });

        // 10. Medias
        Schema::create('medias', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('variant_id');
            $table->string('path'); // Chemin vers le fichier
            $table->string('type')->nullable(); // image, video, document, etc.
            $table->timestamps();
        });

        // ===== PHASE 2: Ajout de toutes les contraintes de clés étrangères =====

        Schema::table('types', function (Blueprint $table) {
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });

        Schema::table('subtypes', function (Blueprint $table) {
            $table->foreign('type_id')->references('id')->on('types')->onDelete('cascade');
        });

        Schema::table('attribute_values', function (Blueprint $table) {
            $table->foreign('attribute_id')->references('id')->on('attributes')->onDelete('cascade');
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
            $table->foreign('type_id')->references('id')->on('types')->onDelete('set null');
            $table->foreign('subtype_id')->references('id')->on('subtypes')->onDelete('set null');
        });

        Schema::table('variants', function (Blueprint $table) {
            $table->foreign('article_id')->references('id')->on('articles')->onDelete('cascade');
        });

        Schema::table('variants_attribute_value', function (Blueprint $table) {
            $table->foreign('variant_id')->references('id')->on('variants')->onDelete('cascade');
            $table->foreign('attribute_value_id')->references('id')->on('attribute_values')->onDelete('cascade');
        });

        Schema::table('stocks', function (Blueprint $table) {
            $table->foreign('variant_id')->references('id')->on('variants')->onDelete('cascade');
        });

        Schema::table('medias', function (Blueprint $table) {
            $table->foreign('variant_id')->references('id')->on('variants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Supprimer dans l'ordre inverse pour respecter les contraintes
        Schema::dropIfExists('medias');
        Schema::dropIfExists('stocks');
        Schema::dropIfExists('variants_attribute_value');
        Schema::dropIfExists('variants');
        Schema::dropIfExists('articles');
        Schema::dropIfExists('attribute_values');
        Schema::dropIfExists('attributes');
        Schema::dropIfExists('subtypes');
        Schema::dropIfExists('types');
        Schema::dropIfExists('categories');
    }
};
