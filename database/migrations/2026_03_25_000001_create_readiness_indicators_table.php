<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('readiness_indicators', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();       // M1, CC1, etc.
            $table->string('category', 80);
            $table->unsignedTinyInteger('category_weight');  // 25, 15, 25, 10, 15, 10
            $table->string('indicator_name');
            $table->text('evidence_examples')->nullable();
            $table->string('anchor_0');                 // 0 anchor text
            $table->string('anchor_3');                 // 3 anchor text
            $table->string('anchor_5');                 // 5 anchor text
            $table->unsignedTinyInteger('indicator_weight');  // individual weighting
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('readiness_indicators');
    }
};
