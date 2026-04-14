<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->nullable()->after('callsign');        // e.g. "Net Controller", "Operator"
            $table->integer('level')->nullable()->after('role');          // operator training level
            $table->string('status')->nullable()->after('level');         // Active / Inactive / Standby
            $table->string('phone')->nullable()->after('status');
            $table->date('joined_at')->nullable()->after('phone');
            $table->text('notes')->nullable()->after('joined_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'level', 'status', 'phone', 'joined_at', 'notes']);
        });
    }
};
