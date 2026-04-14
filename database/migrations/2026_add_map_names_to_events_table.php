<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('event_polygon_name', 120)->nullable()->after('event_polygon');
            $table->string('event_route_name',   120)->nullable()->after('event_route');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['event_polygon_name', 'event_route_name']);
        });
    }
};
