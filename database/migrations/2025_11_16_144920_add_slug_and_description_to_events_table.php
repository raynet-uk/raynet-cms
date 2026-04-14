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
        Schema::table('events', function (Blueprint $table) {

            // Add slug only if it doesn't already exist
            if (! Schema::hasColumn('events', 'slug')) {
                $table->string('slug')->nullable()->after('title');
            }

            // Add description only if it doesn't already exist
            if (! Schema::hasColumn('events', 'description')) {
                $table->text('description')->nullable()->after('location');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {

            // Drop slug safely
            if (Schema::hasColumn('events', 'slug')) {
                $table->dropColumn('slug');
            }

            // Drop description safely
            if (Schema::hasColumn('events', 'description')) {
                $table->dropColumn('description');
            }
        });
    }
};