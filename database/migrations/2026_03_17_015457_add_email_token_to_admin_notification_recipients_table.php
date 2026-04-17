<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admin_notification_recipients', function (Blueprint $table) {
            if (!\Illuminate\Support\Facades\Schema::hasColumn('admin_notification_recipients', 'email_token')) { $table->string('email_token', 64)->nullable()->unique(); }
            if (!\Illuminate\Support\Facades\Schema::hasColumn('admin_notification_recipients', 'email_opened_at')) { $table->timestamp('email_opened_at')->nullable(); }
        });
    }

    public function down(): void
    {
        Schema::table('admin_notification_recipients', function (Blueprint $table) {
            $table->dropColumn(['email_token', 'email_opened_at']);
        });
    }
};