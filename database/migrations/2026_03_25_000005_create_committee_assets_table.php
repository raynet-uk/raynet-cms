<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('committee_assets');
        Schema::create('committee_assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_type', 80);
            $table->string('description');
            $table->string('serial_number')->nullable();
            $table->unsignedSmallInteger('quantity')->default(1);
            $table->unsignedSmallInteger('serviceable_qty')->default(0);
            $table->date('last_test_date')->nullable();
            $table->decimal('power_runtime_hours', 5, 1)->nullable();
            $table->string('location', 120)->nullable();
            $table->string('owner', 120)->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable(); // no FK
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down(): void { Schema::dropIfExists('committee_assets'); }
};
