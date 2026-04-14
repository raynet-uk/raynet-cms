<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('committee_risks');
        Schema::dropIfExists('committee_actions');

        Schema::create('committee_actions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('source', ['exercise','deployment','risk','committee','inspection','other'])->default('other');
            $table->string('source_ref')->nullable();
            $table->unsignedBigInteger('owner_id')->nullable();   // no FK
            $table->date('due_date')->nullable();
            $table->enum('priority', ['low','medium','high','critical'])->default('medium');
            $table->enum('status', ['open','in_progress','closed','overdue','cancelled'])->default('open');
            $table->date('closed_date')->nullable();
            $table->text('closure_notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable(); // no FK
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('committee_risks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category', 80)->nullable();
            $table->unsignedTinyInteger('likelihood')->default(1);
            $table->unsignedTinyInteger('impact')->default(1);
            $table->text('mitigation')->nullable();
            $table->enum('status', ['open','mitigated','accepted','closed'])->default('open');
            $table->date('review_date')->nullable();
            $table->unsignedBigInteger('owner_id')->nullable();   // no FK
            $table->unsignedBigInteger('created_by')->nullable(); // no FK
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('committee_risks');
        Schema::dropIfExists('committee_actions');
    }
};
