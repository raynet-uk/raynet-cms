<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('committee_service_levels');
        Schema::create('committee_service_levels', function (Blueprint $table) {
            $table->id();
            $table->string('key', 80)->unique();
            $table->text('value')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable(); // no FK
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('committee_service_levels'); }
};
