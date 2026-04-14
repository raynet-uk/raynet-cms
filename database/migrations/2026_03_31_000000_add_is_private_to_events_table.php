<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->boolean('is_private')->default(false)->after('is_public');
        });

        // Backfill: rows where is_public = 0 were already "private",
        // so mirror that intent into the new column.
        DB::statement('UPDATE events SET is_private = 1 WHERE is_public = 0');
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('is_private');
        });
    }
};