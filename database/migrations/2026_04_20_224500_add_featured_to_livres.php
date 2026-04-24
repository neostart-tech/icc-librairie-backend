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
        Schema::table('livres', function (Blueprint $table) {
            $table->boolean('is_selection_mois')->default(false);
            $table->boolean('is_selection_mois_precedent')->default(false);
            $table->boolean('is_vogue')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('livres', function (Blueprint $table) {
            $table->dropColumn(['is_selection_mois', 'is_selection_mois_precedent', 'is_vogue']);
        });
    }
};
