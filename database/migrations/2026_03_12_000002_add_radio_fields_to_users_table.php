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
            if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'licence_class')) { $table->string('licence_class', 20)->nullable(); }   // Foundation, Intermediate, Full
            if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'licence_number')) { $table->string('licence_number', 30)->nullable(); }

            // Digital network IDs
            if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'echolink_number')) { $table->string('echolink_number', 10)->nullable(); }
            if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'dstar_callsign')) { $table->string('dstar_callsign', 15)->nullable(); }
            if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'c4fm_callsign')) { $table->string('c4fm_callsign', 15)->nullable(); }
            if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'aprs_ssid')) { $table->string('aprs_ssid', 10)->nullable(); }   // e.g. M0XYZ-9

            // Capabilities (JSON array of mode strings)
            if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'modes')) { $table->json('modes')->nullable(); }

            // Deployment
            if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'available_for_callout')) { $table->boolean('available_for_callout')->default(false); }
            if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'has_vehicle')) { $table->boolean('has_vehicle')->default(false); }
            if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'vehicle_type')) { $table->string('vehicle_type', 50)->nullable(); }
            $table->unsignedSmallInteger('max_travel_miles')->nullable();

            // Emergency / next-of-kin
            if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'nok_name')) { $table->string('nok_name', 100)->nullable(); }
            if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'nok_relationship')) { $table->string('nok_relationship', 50)->nullable(); }
            if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'nok_phone')) { $table->string('nok_phone', 20)->nullable(); }
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
