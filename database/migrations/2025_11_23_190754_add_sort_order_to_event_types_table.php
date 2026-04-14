<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSortOrderToEventTypesTable extends Migration
{
    public function up(): void
    {
        Schema::table('event_types', function (Blueprint $table) {
            if (!Schema::hasColumn('event_types', 'sort_order')) {
                $table->unsignedInteger('sort_order')
                      ->default(0)
                      ->after('name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('event_types', function (Blueprint $table) {
            if (Schema::hasColumn('event_types', 'sort_order')) {
                $table->dropColumn('sort_order');
            }
        });
    }
}