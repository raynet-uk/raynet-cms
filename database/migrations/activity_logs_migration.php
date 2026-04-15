<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('activity_logs')) {
            Schema::create('activity_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('event_name');
                $table->date('event_date');
                $table->decimal('hours', 5, 2);
                $table->unsignedBigInteger('logged_by')->nullable();
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('logged_by')->references('id')->on('users')->onDelete('set null');
            });
        } else {
            Schema::table('activity_logs', function (Blueprint $table) {
                // Add any missing columns safely
                if (!Schema::hasColumn('activity_logs', 'logged_by')) {
                    $table->unsignedBigInteger('logged_by')->nullable();
                    $table->foreign('logged_by')->references('id')->on('users')->onDelete('set null');
                }
                if (!Schema::hasColumn('activity_logs', 'updated_at')) {
                    $table->timestamps();
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
