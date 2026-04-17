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
            if (!\Illuminate\Support\Facades\Schema::hasColumn('event_assignments', 'shifts')) { if (!\Illuminate\Support\Facades\Schema::hasColumn('event_assignments', 'shifts')) { $table->json('shifts')->nullable(); } }

            // Channel plan — primary already exists (frequency/mode/ctcss_tone)
            // Add label + secondary + fallback tiers
            if (!\Illuminate\Support\Facades\Schema::hasColumn('event_assignments', 'channel_label')) { if (!\Illuminate\Support\Facades\Schema::hasColumn('event_assignments', 'channel_label')) { $table->string('channel_label', 50)->nullable(); } }
            if (!\Illuminate\Support\Facades\Schema::hasColumn('event_assignments', 'secondary_frequency')) { if (!\Illuminate\Support\Facades\Schema::hasColumn('event_assignments', 'secondary_frequency')) { $table->string('secondary_frequency', 20)->nullable(); } }
            if (!\Illuminate\Support\Facades\Schema::hasColumn('event_assignments', 'secondary_mode')) { if (!\Illuminate\Support\Facades\Schema::hasColumn('event_assignments', 'secondary_mode')) { $table->string('secondary_mode', 10)->nullable(); } }
            if (!\Illuminate\Support\Facades\Schema::hasColumn('event_assignments', 'secondary_ctcss')) { if (!\Illuminate\Support\Facades\Schema::hasColumn('event_assignments', 'secondary_ctcss')) { $table->string('secondary_ctcss', 10)->nullable(); } }
            if (!\Illuminate\Support\Facades\Schema::hasColumn('event_assignments', 'fallback_frequency')) { if (!\Illuminate\Support\Facades\Schema::hasColumn('event_assignments', 'fallback_frequency')) { $table->string('fallback_frequency', 20)->nullable(); } }
            if (!\Illuminate\Support\Facades\Schema::hasColumn('event_assignments', 'fallback_mode')) { if (!\Illuminate\Support\Facades\Schema::hasColumn('event_assignments', 'fallback_mode')) { $table->string('fallback_mode', 10)->nullable(); } }
            if (!\Illuminate\Support\Facades\Schema::hasColumn('event_assignments', 'fallback_ctcss')) { if (!\Illuminate\Support\Facades\Schema::hasColumn('event_assignments', 'fallback_ctcss')) { $table->string('fallback_ctcss', 10)->nullable(); } }

            // Coverage radius for map circles
            if (!\Illuminate\Support\Facades\Schema::hasColumn('event_assignments', 'coverage_radius_m')) { if (!\Illuminate\Support\Facades\Schema::hasColumn('event_assignments', 'coverage_radius_m')) { $table->unsignedBigInteger('coverage_radius_m')->default(0); } }

            // Equipment as JSON checklist (supplements free-text equipment column)
            if (!\Illuminate\Support\Facades\Schema::hasColumn('event_assignments', 'equipment_items')) { if (!\Illuminate\Support\Facades\Schema::hasColumn('event_assignments', 'equipment_items')) { $table->json('equipment_items')->nullable(); } }

            // Private notes — not printed on standard briefing sheet
            if (!\Illuminate\Support\Facades\Schema::hasColumn('event_assignments', 'medical_notes')) { if (!\Illuminate\Support\Facades\Schema::hasColumn('event_assignments', 'medical_notes')) { $table->text('medical_notes')->nullable(); } }

            // Emergency contact
            if (!\Illuminate\Support\Facades\Schema::hasColumn('event_assignments', 'emergency_contact_name')) { if (!\Illuminate\Support\Facades\Schema::hasColumn('event_assignments', 'emergency_contact_name')) { $table->string('emergency_contact_name', 100)->nullable(); } }
            if (!\Illuminate\Support\Facades\Schema::hasColumn('event_assignments', 'emergency_contact_phone')) { if (!\Illuminate\Support\Facades\Schema::hasColumn('event_assignments', 'emergency_contact_phone')) { $table->string('emergency_contact_phone', 20)->nullable(); } }
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
