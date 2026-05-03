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
            // Rename columns to new requirements
            $table->renameColumn('is_vogue', 'is_livre_du_mois');
            $table->renameColumn('is_selection_mois', 'is_selection_annee');
            
            // Add new column for livre duo
            $table->boolean('is_livre_duo')->default(false);
            
            // Drop column that is no longer needed
            $table->dropColumn('is_selection_mois_precedent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('livres', function (Blueprint $table) {
            $table->renameColumn('is_livre_du_mois', 'is_vogue');
            $table->renameColumn('is_selection_annee', 'is_selection_mois');
            $table->boolean('is_selection_mois_precedent')->default(false);
            $table->dropColumn('is_livre_duo');
        });
    }
};
