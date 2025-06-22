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
        Schema::create('modules', function (Blueprint $table) {
            $table->id();

            $table->string('name', 100)->unique()->comment('Nom unique du module');
            $table->string('display_name')->comment('Nom affiché dans l\'interface');
            $table->text('description')->nullable()->comment('Description du module');
            $table->string('icon', 100)->nullable()->comment('Icône FontAwesome');
            $table->string('route_prefix', 100)->nullable()->comment('Préfixe des routes (séparés par virgule)');
            $table->boolean('is_enabled')->default(true)->comment('Module activé/désactivé');
            $table->boolean('is_visible_sidebar')->default(true)->comment('Visible dans la sidebar');
            $table->integer('sort_order')->default(0)->comment('Ordre d\'affichage');
            $table->string('required_permission', 100)->nullable()->comment('Permission requise');
            $table->integer('min_admin_level')->default(0)->comment('0=user, 90=admin, 100=super_admin');
            $table->foreignId('parent_module_id')->nullable()->constrained('modules')->onDelete('set null');

            $table->index(['is_enabled', 'is_visible_sidebar']);
            $table->index(['parent_module_id', 'sort_order']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
