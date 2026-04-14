<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Stored as a string — DMR IDs are numeric but we never do
            // arithmetic on them, and leading-zero edge cases are avoided.
            $table->string('dmr_id', 20)->nullable()->after('callsign');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('dmr_id');
        });
    }
};
