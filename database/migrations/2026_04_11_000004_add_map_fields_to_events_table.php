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
            $table->decimal('event_lat', 10, 7)->nullable();
            $table->decimal('event_lng', 10, 7)->nullable();

            // GeoJSON polygon representing the site boundary (optional)
            // Stored as the geometry object: {"type":"Polygon","coordinates":[[[lng,lat],...]]}
            $table->json('event_polygon')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['event_lat', 'event_lng', 'event_polygon']);
        });
    }
};
