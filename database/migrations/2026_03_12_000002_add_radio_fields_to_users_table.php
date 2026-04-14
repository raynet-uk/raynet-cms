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
            $table->string('licence_class', 20)->nullable()->after('dmr_id');   // Foundation, Intermediate, Full
            $table->string('licence_number', 30)->nullable()->after('licence_class');

            // Digital network IDs
            $table->string('echolink_number', 10)->nullable()->after('licence_number');
            $table->string('dstar_callsign', 15)->nullable()->after('echolink_number');
            $table->string('c4fm_callsign', 15)->nullable()->after('dstar_callsign');
            $table->string('aprs_ssid', 10)->nullable()->after('c4fm_callsign');   // e.g. M0XYZ-9

            // Capabilities (JSON array of mode strings)
            $table->json('modes')->nullable()->after('aprs_ssid');

            // Deployment
            $table->boolean('available_for_callout')->default(false)->after('modes');
            $table->boolean('has_vehicle')->default(false)->after('available_for_callout');
            $table->string('vehicle_type', 50)->nullable()->after('has_vehicle');
            $table->unsignedSmallInteger('max_travel_miles')->nullable()->after('vehicle_type');

            // Emergency / next-of-kin
            $table->string('nok_name', 100)->nullable()->after('max_travel_miles');
            $table->string('nok_relationship', 50)->nullable()->after('nok_name');
            $table->string('nok_phone', 20)->nullable()->after('nok_relationship');
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
