<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // Pin position — centre point of the event location
            if (!\Illuminate\Support\Facades\Schema::hasColumn('events', 'event_lat')) { $table->decimal('event_lat', 10, 7)->nullable(); }
            if (!\Illuminate\Support\Facades\Schema::hasColumn('events', 'event_lng')) { $table->decimal('event_lng', 10, 7)->nullable(); }

            // GeoJSON polygon representing the site boundary (optional)
            // Stored as the geometry object: {"type":"Polygon","coordinates":[[[lng,lat],...]]}
            if (!\Illuminate\Support\Facades\Schema::hasColumn('events', 'event_polygon')) { $table->json('event_polygon')->nullable(); }
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['event_lat', 'event_lng', 'event_polygon']);
        });
    }
};
