<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (!\Illuminate\Support\Facades\Schema::hasColumn('events', 'event_polygon_name')) { $table->string('event_polygon_name', 120)->nullable(); }
            if (!\Illuminate\Support\Facades\Schema::hasColumn('events', 'event_route_name')) { $table->string('event_route_name',   120)->nullable(); }
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['event_polygon_name', 'event_route_name']);
        });
    }
};
