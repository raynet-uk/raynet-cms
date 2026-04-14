<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alert_statuses', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('level')->default(5); // 1–5
            $table->string('headline')->nullable();
            $table->text('message')->nullable();
            $table->timestamps();
        });

        // Seed a default "Level 5 – No Incidents"
        DB::table('alert_statuses')->insert([
            'level'      => 5,
            'headline'   => null,
            'message'    => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('alert_statuses');
    }
};