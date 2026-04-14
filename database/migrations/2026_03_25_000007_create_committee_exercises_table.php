<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('committee_exercises');
        Schema::create('committee_exercises', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('activity');
            $table->enum('type', ['training_night','tabletop','practical_exercise','real_deployment','partner_exercise','other']);
            $table->string('capability_tested')->nullable();
            $table->string('lead', 120)->nullable();
            $table->text('outcome')->nullable();
            $table->text('lessons_identified')->nullable();
            $table->string('action_owner', 120)->nullable();
            $table->date('due_date')->nullable();
            $table->date('closed_date')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable(); // no FK
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down(): void { Schema::dropIfExists('committee_exercises'); }
};
