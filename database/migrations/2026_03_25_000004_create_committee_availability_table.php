<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('committee_availability');
        Schema::create('committee_availability', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique(); // no FK to users
            $table->boolean('is_active_operator')->default(false);
            $table->boolean('deployable_60min')->default(false);
            $table->boolean('deployable_120min')->default(false);
            $table->boolean('is_team_leader')->default(false);
            $table->boolean('induction_current')->default(false);
            $table->boolean('message_handling_current')->default(false);
            $table->boolean('digital_data_competent')->default(false);
            $table->date('induction_date')->nullable();
            $table->date('message_handling_date')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable(); // no FK
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('committee_availability'); }
};
