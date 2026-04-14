<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('body')->nullable();
            $table->unsignedTinyInteger('priority')->default(1);
            $table->unsignedBigInteger('sent_by')->nullable();
            $table->boolean('sent_to_all')->default(false);
            $table->timestamps();
        });

        Schema::create('admin_notification_recipients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('notification_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamp('read_at')->nullable();
            $table->timestamp('removed_at')->nullable();
            $table->timestamps();

            $table->unique(['notification_id', 'user_id']);
            $table->index('notification_id');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_notification_recipients');
        Schema::dropIfExists('admin_notifications');
    }
};