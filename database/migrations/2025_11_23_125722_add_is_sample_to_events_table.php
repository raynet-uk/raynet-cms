<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add an 'is_sample' flag so I can mark demo events.
     */
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // Boolean flag, defaults to false for real events
            $table->boolean('is_sample')
                ->default(false)
                ->after('description'); // adjust position if 'description' doesn’t exist
        });
    }

    /**
     * Rollback: remove the column.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('is_sample');
        });
    }
};