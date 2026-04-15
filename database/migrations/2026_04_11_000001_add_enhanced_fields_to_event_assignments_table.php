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
            $table->json('shifts')->nullable()->after('depart_time');

            // Channel plan — primary already exists (frequency/mode/ctcss_tone)
            // Add label + secondary + fallback tiers
            $table->string('channel_label', 50)->nullable()->after('ctcss_tone');
            $table->string('secondary_frequency', 20)->nullable()->after('channel_label');
            $table->string('secondary_mode', 10)->nullable()->after('secondary_frequency');
            $table->string('secondary_ctcss', 10)->nullable()->after('secondary_mode');
            $table->string('fallback_frequency', 20)->nullable()->after('secondary_ctcss');
            $table->string('fallback_mode', 10)->nullable()->after('fallback_frequency');
            $table->string('fallback_ctcss', 10)->nullable()->after('fallback_mode');

            // Coverage radius for map circles
            $table->unsignedInteger('coverage_radius_m')->default(0)->after('what3words');

            // Equipment as JSON checklist (supplements free-text equipment column)
            $table->json('equipment_items')->nullable()->after('equipment');

            // Private notes — not printed on standard briefing sheet
            $table->text('medical_notes')->nullable()->after('briefing_notes');

            // Emergency contact
            $table->string('emergency_contact_name', 100)->nullable()->after('medical_notes');
            $table->string('emergency_contact_phone', 20)->nullable()->after('emergency_contact_name');
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
