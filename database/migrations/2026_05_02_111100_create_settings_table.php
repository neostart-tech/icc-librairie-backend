<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Default settings
        DB::table('settings')->insert([
            [
                'key' => 'payment_message',
                'value' => 'Pour régler cette facture, envoyez $montant_total au +22800000000 sur Moov Money (Flooz) ou au +22800000000 sur T-Money (Mixx) et envoyez la référence de votre paiement ou une capture d\'écran.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'delivery_fee',
                'value' => '1000',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
