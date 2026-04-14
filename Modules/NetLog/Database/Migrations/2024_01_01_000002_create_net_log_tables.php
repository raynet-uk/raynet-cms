<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('net_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('frequency', 30)->nullable();
            $table->string('net_control', 20)->nullable();
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('opened_by')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('net_checkins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('net_session_id')->constrained()->cascadeOnDelete();
            $table->string('callsign', 20);
            $table->string('name')->nullable();
            $table->string('location')->nullable();
            $table->string('signal_report', 10)->nullable();
            $table->text('message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('net_checkins');
        Schema::dropIfExists('net_sessions');
    }
};
