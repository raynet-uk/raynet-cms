<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_assignments', function (Blueprint $table) {
            // Multi-shift JSON (replaces single start_time/end_time for complex schedules)
            $table->json('shifts')->nullable();

            // Channel plan — primary already exists (frequency/mode/ctcss_tone)
            // Add label + secondary + fallback tiers
            $table->string('channel_label', 50)->nullable();
            $table->string('secondary_frequency', 20)->nullable();
            $table->string('secondary_mode', 10)->nullable();
            $table->string('secondary_ctcss', 10)->nullable();
            $table->string('fallback_frequency', 20)->nullable();
            $table->string('fallback_mode', 10)->nullable();
            $table->string('fallback_ctcss', 10)->nullable();

            // Coverage radius for map circles
            $table->unsignedInteger('coverage_radius_m')->default(0);

            // Equipment as JSON checklist (supplements free-text equipment column)
            $table->json('equipment_items')->nullable();

            // Private notes — not printed on standard briefing sheet
            $table->text('medical_notes')->nullable();

            // Emergency contact
            $table->string('emergency_contact_name', 100)->nullable();
            $table->string('emergency_contact_phone', 20)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('event_assignments', function (Blueprint $table) {
            $table->dropColumn([
                'shifts',
                'channel_label',
                'secondary_frequency', 'secondary_mode', 'secondary_ctcss',
                'fallback_frequency',  'fallback_mode',  'fallback_ctcss',
                'coverage_radius_m',
                'equipment_items',
                'medical_notes',
                'emergency_contact_name',
                'emergency_contact_phone',
            ]);
        });
    }
};
