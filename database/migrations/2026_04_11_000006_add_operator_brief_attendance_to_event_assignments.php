<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\EventAssignment;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_assignments', function (Blueprint $table) {
            // Unique token used in the QR code URL — unguessable, no login needed
            $table->string('briefing_token', 64)->nullable()->unique()->after('briefing_sent_at');

            // Current attendance state — derived from log but stored for fast reads
            $table->enum('attendance_status', ['not_arrived', 'checked_in', 'on_break', 'checked_out'])
                  ->default('not_arrived')
                  ->after('briefing_token');

            // Full log of all check-in/out/break events as a JSON array:
            // [{"type":"check_in","time":"2026-06-20T08:45:00","note":""}]
            $table->json('attendance_log')->nullable()->after('attendance_status');
        });

        // Backfill tokens for any existing assignments
        EventAssignment::whereNull('briefing_token')->each(function ($a) {
            $a->update(['briefing_token' => Str::random(48)]);
        });
    }

    public function down(): void
    {
        Schema::table('event_assignments', function (Blueprint $table) {
            $table->dropColumn(['briefing_token', 'attendance_status', 'attendance_log']);
        });
    }
};
