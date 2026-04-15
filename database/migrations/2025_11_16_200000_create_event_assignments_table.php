<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('event_assignments')) Schema::create('event_assignments', function (Blueprint $table) {
            $table->id();

            // Use unsignedInteger (not unsignedBigInteger) to match existing
            // events and users tables that were created with increments('id').
            // If your tables use bigIncrements, change these to unsignedBigInteger.
            $table->unsignedBigInteger('event_id');
            $table->unsignedBigInteger('user_id');

            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Role & radio
            $table->string('role')->nullable();            // Net Control, Field Operator, Liaison Officer…
            $table->string('callsign')->nullable();        // Member's callsign for this event
            $table->string('frequency')->nullable();       // Primary working frequency
            $table->string('ctcss_tone')->nullable();      // CTCSS / DCS tone if applicable
            $table->enum('mode', ['FM','AM','SSB','DMR','Other'])->default('FM');

            // Position / location
            $table->string('location_name')->nullable();   // "Checkpoint Alpha", "Net Control Room"
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->string('grid_ref')->nullable();        // OS Grid ref e.g. SJ394905
            $table->string('what3words')->nullable();      // what3words address

            // Schedule
            $table->time('report_time')->nullable();       // When to arrive/report
            $table->time('start_time')->nullable();        // When their shift starts
            $table->time('end_time')->nullable();          // When their shift ends
            $table->time('depart_time')->nullable();       // Expected departure time

            // Logistics
            $table->text('equipment')->nullable();         // Equipment they should bring
            $table->text('briefing_notes')->nullable();    // Private admin notes for this operator
            $table->boolean('has_vehicle')->default(false);
            $table->string('vehicle_reg')->nullable();
            $table->boolean('first_aid_trained')->default(false);

            // Status
            $table->enum('status', ['pending','confirmed','declined','standby'])
                  ->default('pending');
            $table->timestamp('status_changed_at')->nullable();
            $table->text('status_note')->nullable();       // Reason for decline / standby note

            // Tracking
            $table->boolean('briefing_sent')->default(false);
            $table->timestamp('briefing_sent_at')->nullable();

            $table->timestamps();

            $table->unique(['event_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_assignments');
    }
};