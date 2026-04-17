<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('member_roles', function (Blueprint $table) {
            if (!\Illuminate\Support\Facades\Schema::hasColumn('member_roles', 'slug')) { $table->string('slug')->unique(); }
            if (!\Illuminate\Support\Facades\Schema::hasColumn('member_roles', 'colour')) { $table->string('colour', 7)->nullable(); }
            if (!\Illuminate\Support\Facades\Schema::hasColumn('member_roles', 'description')) { $table->text('description')->nullable(); }
        });
    }

    public function down(): void
    {
        Schema::table('member_roles', function (Blueprint $table) {
            $table->dropColumn(['slug', 'colour', 'description']);
        });
    }
};
