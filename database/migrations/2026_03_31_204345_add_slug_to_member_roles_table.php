<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('member_roles', function (Blueprint $table) {
            $table->string('slug')->unique();
            $table->string('colour', 7)->nullable();
            $table->text('description')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('member_roles', function (Blueprint $table) {
            $table->dropColumn(['slug', 'colour', 'description']);
        });
    }
};
