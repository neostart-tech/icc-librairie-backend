<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('settings')->insertOrIgnore([
            [
                'key' => 'contact_phone_primary',
                'value' => '+228 92 09 02 04',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'contact_phone_secondary_1',
                'value' => '+228 79 76 27 33',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'contact_phone_secondary_2',
                'value' => '+228 90 00 94 62',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'contact_email',
                'value' => 'librairieicclome05@gmail.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'contact_address',
                'value' => 'Librairie ICC Hountigomé, Lomé, Togo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'opening_hours_weekday',
                'value' => 'Lun-Ven 8h-17h',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'opening_hours_sunday',
                'value' => 'Dim 8h-14h',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        DB::table('settings')->whereIn('key', [
            'contact_phone_primary',
            'contact_phone_secondary_1',
            'contact_phone_secondary_2',
            'contact_email',
            'contact_address',
            'opening_hours_weekday',
            'opening_hours_sunday',
        ])->delete();
    }
};
