<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Detect the actual column type of events.id so the FK matches exactly.
        // Older Laravel apps use increments() → unsigned int (4 bytes).
        // Newer apps use id()              → unsigned bigint (8 bytes).
        $eventsIdType = 'bigint';

        try {
            $cols = DB::select("SHOW COLUMNS FROM `events` WHERE Field = 'id'");
            if (!empty($cols)) {
                $rawType = strtolower($cols[0]->Type ?? '');
                // e.g. "int(10) unsigned", "int unsigned", "bigint(20) unsigned"
                if (str_starts_with($rawType, 'int') && !str_starts_with($rawType, 'bigint')) {
                    $eventsIdType = 'int';
                }
            }
        } catch (\Throwable $e) {
            // Fall through — will try bigint
        }

        Schema::create('event_documents', function (Blueprint $table) use ($eventsIdType) {

            $table->id(); // bigint auto-increment — always fine for this table's own PK

            // Match the events.id column type precisely
            if ($eventsIdType === 'int') {
                $table->unsignedInteger('event_id');
            } else {
                $table->unsignedBigInteger('event_id');
            }

            $table->string('filename');               // original filename shown to users
            $table->string('label')->nullable();      // optional human-readable display name
            $table->string('disk')->default('local'); // storage disk (local / s3 etc.)
            $table->string('path');                   // full path on the disk
            $table->unsignedBigInteger('size_bytes')->nullable();
            $table->unsignedInteger('sort_order')->default(0);

            // uploaded_by — nullable FK to users
            // Use the same type detection in case users.id is also int
            $table->unsignedBigInteger('uploaded_by')->nullable();

            $table->timestamps();

            // Foreign keys — added after columns so we can control the types
            $table->foreign('event_id')
                  ->references('id')
                  ->on('events')
                  ->cascadeOnDelete();

            $table->foreign('uploaded_by')
                  ->references('id')
                  ->on('users')
                  ->nullOnDelete();

            // Useful indexes
            $table->index(['event_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_documents');
    }
};