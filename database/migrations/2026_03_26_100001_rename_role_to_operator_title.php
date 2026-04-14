<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Rename users.role → users.operator_title
 *
 * The 'role' column was used for RAYNET operator titles
 * (e.g. Group Controller, Operator) NOT for access control.
 * Renaming prevents confusion with Spatie's roles system.
 *
 * NOTE: The value 'committee' was also stored here to grant
 * committee access. The SpatieRoleSeeder handles migrating
 * those users to the proper Spatie 'committee' role.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('role', 'operator_title');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('operator_title', 'role');
        });
    }
};
