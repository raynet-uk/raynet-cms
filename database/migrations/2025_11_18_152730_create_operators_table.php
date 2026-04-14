<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // In dev I don't care about nuking test data: drop and recreate.
        Schema::dropIfExists('operators');

        Schema::create('operators', function (Blueprint $table) {
            $table->id();

            // Core identity
            $table->string('name');
            $table->string('callsign')->nullable();
            $table->string('email')->nullable();

            // Roles & levels
            $table->string('role')->default('Member');   // Member / Controller / Admin
            $table->string('level')->nullable();         // Level 0–5 or training grade

            // Status
            $table->string('status')->default('Active'); // Active, Training, Inactive

            // Extra attributes I’ll extend later
            $table->string('phone')->nullable();
            $table->date('joined_at')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operators');
    }
};