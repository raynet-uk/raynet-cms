<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE lms_lessons MODIFY COLUMN type ENUM('text','video','scorm','quiz','audio','document','presentation','external','checklist') NOT NULL DEFAULT 'text'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE lms_lessons MODIFY COLUMN type ENUM('text','video','scorm','quiz') NOT NULL DEFAULT 'text'");
    }
};