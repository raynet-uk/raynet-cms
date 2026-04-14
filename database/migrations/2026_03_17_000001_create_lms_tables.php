<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('category')->nullable();
            $table->enum('difficulty', ['beginner','intermediate','advanced'])->default('beginner');
            $table->decimal('estimated_hours', 5, 1)->nullable();
            $table->boolean('is_published')->default(false);
            $table->boolean('is_drip')->default(false);
            $table->integer('drip_interval_days')->default(7);
            $table->integer('pass_mark')->default(80);
            $table->boolean('certificate_enabled')->default(true);
            $table->text('certificate_text')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });

        Schema::create('lms_modules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('lms_lessons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('module_id');
            $table->unsignedBigInteger('course_id');
            $table->string('title');
            $table->enum('type', ['text','video','scorm','quiz'])->default('text');
            $table->longText('content')->nullable();
            $table->string('video_url')->nullable();
            $table->string('scorm_package')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_free_preview')->default(false);
            $table->integer('drip_days')->default(0);
            $table->timestamps();
        });

        Schema::create('lms_quizzes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lesson_id');
            $table->unsignedBigInteger('course_id');
            $table->string('title');
            $table->integer('pass_mark')->default(80);
            $table->integer('attempts_allowed')->default(3);
            $table->integer('time_limit_minutes')->nullable();
            $table->timestamps();
        });

        Schema::create('lms_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quiz_id');
            $table->text('question');
            $table->enum('type', ['multiple_choice','true_false','text'])->default('multiple_choice');
            $table->integer('points')->default(1);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('lms_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('question_id');
            $table->text('answer_text');
            $table->boolean('is_correct')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('lms_enrollments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('assigned_by')->nullable();
            $table->timestamp('enrolled_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->date('due_date')->nullable();
            $table->unique(['user_id','course_id']);
            $table->timestamps();
        });

        Schema::create('lms_progress', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('lesson_id');
            $table->timestamp('completed_at')->nullable();
            $table->integer('quiz_score')->nullable();
            $table->integer('attempts')->default(0);
            $table->unique(['user_id','lesson_id']);
            $table->timestamps();
        });

        Schema::create('lms_certificates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('course_id');
            $table->string('certificate_number')->unique();
            $table->timestamp('issued_at')->useCurrent();
            $table->unique(['user_id','course_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_certificates');
        Schema::dropIfExists('lms_progress');
        Schema::dropIfExists('lms_enrollments');
        Schema::dropIfExists('lms_answers');
        Schema::dropIfExists('lms_questions');
        Schema::dropIfExists('lms_quizzes');
        Schema::dropIfExists('lms_lessons');
        Schema::dropIfExists('lms_modules');
        Schema::dropIfExists('lms_courses');
    }
};