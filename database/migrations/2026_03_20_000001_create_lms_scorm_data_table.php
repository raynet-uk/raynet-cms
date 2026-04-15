<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('lms_scorm_data');

        Schema::create('lms_scorm_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');        // int(10) unsigned to match users.id
            $table->unsignedBigInteger('lesson_id');   // bigint to match lms_lessons.id
            $table->string('key', 255);
            $table->text('value')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'lesson_id', 'key']);
            $table->index(['user_id', 'lesson_id']);

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('lesson_id')->references('id')->on('lms_lessons')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_scorm_data');
    }
};