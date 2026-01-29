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
        Schema::create('gateways', function (Blueprint $table) {
            $table->id();
            $table->integer('semoa_id')->unique(); // id venant de Semoa
            $table->uuid('reference')->unique();
            $table->string('libelle');
            $table->string('psp');
            $table->string('psp_logo')->nullable();
            $table->string('methode')->nullable();
            $table->string('logo_url')->nullable();
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gateways');
    }
};
