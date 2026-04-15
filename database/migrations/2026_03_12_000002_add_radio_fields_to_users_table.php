<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Licence
            $table->string('licence_class', 20)->nullable();   // Foundation, Intermediate, Full
            $table->string('licence_number', 30)->nullable();

            // Digital network IDs
            $table->string('echolink_number', 10)->nullable();
            $table->string('dstar_callsign', 15)->nullable();
            $table->string('c4fm_callsign', 15)->nullable();
            $table->string('aprs_ssid', 10)->nullable();   // e.g. M0XYZ-9

            // Capabilities (JSON array of mode strings)
            $table->json('modes')->nullable();

            // Deployment
            $table->boolean('available_for_callout')->default(false);
            $table->boolean('has_vehicle')->default(false);
            $table->string('vehicle_type', 50)->nullable();
            $table->unsignedSmallInteger('max_travel_miles')->nullable();

            // Emergency / next-of-kin
            $table->string('nok_name', 100)->nullable();
            $table->string('nok_relationship', 50)->nullable();
            $table->string('nok_phone', 20)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'licence_class', 'licence_number',
                'echolink_number', 'dstar_callsign', 'c4fm_callsign', 'aprs_ssid',
                'modes',
                'available_for_callout', 'has_vehicle', 'vehicle_type', 'max_travel_miles',
                'nok_name', 'nok_relationship', 'nok_phone',
            ]);
        });
    }
};
