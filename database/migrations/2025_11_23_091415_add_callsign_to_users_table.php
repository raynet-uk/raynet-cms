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
        Schema::table('users', function (Blueprint $table) {
            // Add a nullable callsign after email.
            // Adjust placement if you prefer a different order.
            if (! Schema::hasColumn('users', 'callsign')) {
                $table->string('callsign', 32)->nullable()->after('email');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'callsign')) {
                $table->dropColumn('callsign');
            }
        });
    }
};