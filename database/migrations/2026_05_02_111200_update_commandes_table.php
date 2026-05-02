<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('commandes', function (Blueprint $table) {
            $table->enum('type_livraison', ['livraison', 'retrait'])->default('retrait')->after('prix_total');
            $table->string('adresse_livraison')->nullable()->after('type_livraison');
            $table->string('numero_livraison')->nullable()->after('adresse_livraison');
            $table->integer('frais_livraison')->default(0)->after('numero_livraison');
            $table->string('preuve_paiement')->nullable()->after('statut');
            $table->string('reference_paiement_client')->nullable()->after('preuve_paiement');
            
            // On va étendre les statuts possibles
            // On ne peut pas facilement modifier un enum dans une migration sans raw SQL sur certains drivers, 
            // mais ici on va supposer qu'on peut ou on va utiliser des strings pour plus de flexibilité si besoin.
            // Pour l'instant on garde l'enum et on ajoute les nouveaux cas si possible, 
            // ou on change la colonne en string pour être tranquille.
        });

        // Conversion du statut en string pour plus de flexibilité
        DB::statement("ALTER TABLE commandes MODIFY COLUMN statut VARCHAR(255) DEFAULT 'en_cours'");
    }

    public function down(): void
    {
        Schema::table('commandes', function (Blueprint $table) {
            $table->dropColumn(['type_livraison', 'adresse_livraison', 'numero_livraison', 'frais_livraison', 'preuve_paiement', 'reference_paiement_client']);
        });
    }
};
