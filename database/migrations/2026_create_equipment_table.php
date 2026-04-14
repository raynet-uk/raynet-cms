<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('make');
            $table->string('model');
            $table->string('serial_number')->nullable();
            $table->string('callsign')->nullable();
            $table->string('licence_class')->nullable();
            $table->enum('equipment_type', [
                'handheld', 'mobile', 'base', 'hf', 'repeater', 'digital', 'antenna', 'other'
            ])->default('handheld');
            $table->date('last_tested_date')->nullable();
            $table->date('next_test_due')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('last_tested_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};