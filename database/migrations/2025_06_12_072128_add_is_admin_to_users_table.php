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
        Schema::table('users', function (Blueprint $table) {
            // Ajouter username (unique)
            $table->string('username')->unique()->after('id');

            // Ajouter les champs de nom séparés
            $table->string('first_name')->nullable()->after('email_verified_at');
            $table->string('last_name')->nullable()->after('first_name');

            // Ajouter le téléphone
            $table->string('phone', 20)->nullable()->after('last_name');

            // Ajouter les champs d'administration
            $table->boolean('is_active')->default(true)->after('phone');
            $table->integer('is_admin')->default(0)->comment('0=user, 90=admin, 100=super_admin')->after('is_active');

            // Ajouter le timestamp de dernière connexion
            $table->timestamp('last_login_at')->nullable()->after('is_admin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'username',
                'first_name',
                'last_name',
                'phone',
                'is_active',
                'is_admin',
                'last_login_at'
            ]);
        });
    }
};
