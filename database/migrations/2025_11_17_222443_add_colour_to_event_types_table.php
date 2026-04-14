<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Only add the column if it doesn't already exist
        if (! Schema::hasColumn('event_types', 'colour')) {
            Schema::table('event_types', function (Blueprint $table) {
                $table->string('colour', 7)->nullable()->after('sort_order');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only drop the column if it exists
        if (Schema::hasColumn('event_types', 'colour')) {
            Schema::table('event_types', function (Blueprint $table) {
                $table->dropColumn('colour');
            });
        }
    }
};