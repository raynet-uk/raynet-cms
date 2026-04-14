<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Whether the member attended at least one event in the current
            // RAYNET year (1 Sep → 31 Aug). Reset to false on 1 September.
            $table->boolean('attended_event_this_year')
                  ->default(false)
                  ->after('notes');

            // Count of individual events attended in the current year.
            $table->unsignedSmallInteger('events_attended_this_year')
                  ->default(0)
                  ->after('attended_event_this_year');

            // Cumulative volunteering hours in the current year.
            // Sourced from the hours value on each attended event.
            $table->decimal('volunteering_hours_this_year', 6, 1)
                  ->default(0)
                  ->after('events_attended_this_year');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'attended_event_this_year',
                'events_attended_this_year',
                'volunteering_hours_this_year',
            ]);
        });
    }
};