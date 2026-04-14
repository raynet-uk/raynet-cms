<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    public function up(): void
    {
        Permission::firstOrCreate([
            'name'       => 'view dmr dashboard',
            'guard_name' => 'web',
        ]);
    }

    public function down(): void
    {
        Permission::where('name', 'view dmr dashboard')
            ->where('guard_name', 'web')
            ->delete();
    }
};