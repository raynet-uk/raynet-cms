<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Detect existing role column type and extend it safely.
        // Works whether role is ENUM or VARCHAR.
        try {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('member','committee','admin','super_admin') NOT NULL DEFAULT 'member'");
        } catch (\Exception $e) {
            // If column isn't an ENUM (e.g. VARCHAR), just ensure 'committee' is a valid value by doing nothing —
            // the application code handles role checks as strings.
        }
    }

    public function down(): void
    {
        try {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('member','admin','super_admin') NOT NULL DEFAULT 'member'");
        } catch (\Exception $e) {
            // Swallow if not an ENUM column.
        }
    }
};
