<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('devis', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('livre_id')->references('id')->on('livres')->onDelete('cascade');
            $table->string('nom_complet');
            $table->string('telephone');
            $table->string('email')->nullable();
            $table->integer('nombre_exemplaires')->default(1);
            $table->text('message')->nullable();
            $table->enum('statut', ['nouveau', 'traite', 'refuse'])->default('nouveau');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devis');
    }
};
