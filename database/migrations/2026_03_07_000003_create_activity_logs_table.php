<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();

            // Plain integers — no FK constraints.
            // Shared hosting MySQL (MyISAM or mixed engines) rejects foreign keys
            // when the referenced table uses a different engine or collation.
            // Referential integrity is enforced at the application layer instead.
            $table->unsignedBigInteger('user_id')->index();
            $table->string('event_name')->nullable();
            $table->date('event_date')->index();
            $table->decimal('hours', 5, 1);
            $table->unsignedBigInteger('logged_by')->nullable()->index();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};