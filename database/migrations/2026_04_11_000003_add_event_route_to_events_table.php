<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // GeoJSON LineString geometry for the event route (walk, race course etc.)
            // {"type":"LineString","coordinates":[[lng,lat],[lng,lat],...]}
            if (!\Illuminate\Support\Facades\Schema::hasColumn('events', 'event_route')) { $table->json('event_route')->nullable(); }
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('event_route');
        });
    }
};
