<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add an is_admin flag to operators.
     *
     * Note to self
     * - This mirrors the behaviour expected by OperatorAdminController
     *   and MemberDashboardController (which orders by is_admin).
     */
    public function up(): void
    {
        Schema::table('operators', function (Blueprint $table) {
            // Put it after status for sanity, but position isn’t critical.
            $table->boolean('is_admin')
                ->default(false)
                ->after('status');
        });
    }

    /**
     * Rollback: drop the is_admin column from operators.
     */
    public function down(): void
    {
        Schema::table('operators', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }
};