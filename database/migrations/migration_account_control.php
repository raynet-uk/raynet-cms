<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'force_password_reset')) {
                $table->boolean('force_password_reset')->default(false)->after('password');
            }
            if (! Schema::hasColumn('users', 'suspended_at')) {
                $table->timestamp('suspended_at')->nullable()->after('force_password_reset');
            }
            if (! Schema::hasColumn('users', 'suspension_message')) {
                $table->text('suspension_message')->nullable()->after('suspended_at');
            }
            if (! Schema::hasColumn('users', 'admin_message')) {
                $table->text('admin_message')->nullable()->after('suspension_message');
            }
            if (! Schema::hasColumn('users', 'dismissed_broadcast_id')) {
                $table->unsignedBigInteger('dismissed_broadcast_id')->nullable()->after('admin_message');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'force_password_reset',
                'suspended_at',
                'suspension_message',
                'admin_message',
                'dismissed_broadcast_id',
            ]);
        });
    }
};
