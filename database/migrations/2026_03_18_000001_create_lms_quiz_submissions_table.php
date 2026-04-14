<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_quiz_submissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('quiz_id');
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('lesson_id');
            $table->integer('attempt_number')->default(1);
            $table->integer('score');
            $table->boolean('passed')->default(false);
            $table->json('answers')->nullable(); // {question_id: answer_id, ...}
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_quiz_submissions');
    }
};