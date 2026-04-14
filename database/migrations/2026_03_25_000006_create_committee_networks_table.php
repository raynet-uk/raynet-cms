<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('committee_networks');
        Schema::create('committee_networks', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->enum('type', ['VHF/UHF','DMR','YSF','VoIP','LoRa','APRS','HF','Other']);
            $table->text('description')->nullable();
            $table->enum('status', ['operational','degraded','offline','unknown'])->default('unknown');
            $table->date('last_tested')->nullable();
            $table->text('test_result')->nullable();
            $table->string('frequency_channel')->nullable();
            $table->string('talkgroup_network_id')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('owner_id')->nullable();  // no FK
            $table->unsignedBigInteger('created_by')->nullable(); // no FK
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down(): void { Schema::dropIfExists('committee_networks'); }
};
