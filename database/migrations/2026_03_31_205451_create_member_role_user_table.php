<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_role_user', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('member_role_id')->index();
            $table->primary(['user_id', 'member_role_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_role_user');
    }
};
