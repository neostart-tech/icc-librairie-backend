<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('livres', function (Blueprint $table) {
            $table->boolean('sur_commande')->default(false)->after('prix_promo');
            $table->integer('prix')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('livres', function (Blueprint $table) {
            $table->dropColumn('sur_commande');
            $table->integer('prix')->nullable(false)->change();
        });
    }
};
