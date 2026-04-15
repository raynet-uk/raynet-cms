<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // JSON array of POI objects:
            // [{"id":"uuid","type":"entrance","name":"Main Gate","lat":53.41,"lng":-2.99,"description":"...","colour":"#1a6b3c"}]
            $table->json('event_pois')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('event_pois');
        });
    }
};
