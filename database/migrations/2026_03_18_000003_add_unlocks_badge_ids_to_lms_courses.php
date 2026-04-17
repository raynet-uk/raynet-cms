<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lms_courses', function (Blueprint $table) {
            if (!\Illuminate\Support\Facades\Schema::hasColumn('lms_courses', 'unlocks_badge_ids')) { $table->json('unlocks_badge_ids')->nullable(); }
        });
    }
    public function down(): void
    {
        Schema::table('lms_courses', function (Blueprint $table) {
            $table->dropColumn('unlocks_badge_ids');
        });
    }
};