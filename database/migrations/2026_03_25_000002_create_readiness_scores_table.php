<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('readiness_scores');

        Schema::create('readiness_scores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('indicator_id');
            $table->unsignedTinyInteger('raw_score')->default(0);
            $table->string('evidence_ref')->nullable();
            $table->date('evidence_date')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('scored_by')->nullable();
            $table->timestamps();

            $table->unique('indicator_id');

            $table->foreign('indicator_id')
                  ->references('id')->on('readiness_indicators')
                  ->cascadeOnDelete();

            // No FK on scored_by — avoids type mismatch with existing users table
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('readiness_scores');
    }
};