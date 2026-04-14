<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_unavailability', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->date('from_date');
            $table->date('to_date');
            $table->string('reason', 200)->nullable();
            $table->timestamps();

            $table->index(['from_date', 'to_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_unavailability');
    }
};