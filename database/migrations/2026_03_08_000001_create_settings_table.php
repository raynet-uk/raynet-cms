<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('label')->nullable();
            $table->timestamps();
        });

        // Default values
        DB::table('settings')->insert([
            ['key' => 'support_request_email', 'value' => '', 'label' => 'Support Request Email', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'registration_approval_required', 'value' => '1', 'label' => 'Registration Approval Required', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
