<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // GPS pings from operator briefs
        Schema::create('operator_gps_pings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assignment_id');
            $table->unsignedBigInteger('event_id');
            $table->decimal('lat', 10, 7);
            $table->decimal('lng', 10, 7);
            $table->smallInteger('accuracy_m')->nullable();
            $table->smallInteger('heading')->nullable();      // degrees 0-359
            $table->smallInteger('speed_ms')->nullable();     // m/s * 10 (fixed point)
            $table->tinyInteger('battery_pct')->nullable();   // 0-100
            $table->boolean('is_dead_reckoned')->default(false);
            $table->timestamp('pinged_at');
            $table->index(['assignment_id', 'pinged_at']);
            $table->index(['event_id', 'pinged_at']);
        });

        // Messages from Net Control to operators
        Schema::create('operator_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id');
            $table->unsignedBigInteger('assignment_id')->nullable(); // null = broadcast to all
            $table->unsignedBigInteger('sent_by');
            $table->string('type')->default('info');         // info|warning|urgent|frequency_change
            $table->text('body');
            $table->json('payload')->nullable();             // e.g. new frequency details
            $table->boolean('requires_ack')->default(false);
            $table->timestamps();
            $table->index(['event_id', 'created_at']);
            $table->index(['assignment_id', 'created_at']);
        });

        // Operator acknowledgements of messages
        Schema::create('operator_message_acks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('message_id');
            $table->unsignedBigInteger('assignment_id');
            $table->timestamp('acked_at');
            $table->unique(['message_id', 'assignment_id']);
        });

        // SOS alerts from operators
        Schema::create('operator_sos_alerts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assignment_id');
            $table->unsignedBigInteger('event_id');
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->text('message')->nullable();
            $table->unsignedBigInteger('resolved_by')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            $table->index(['event_id', 'resolved_at']);
        });

        // Welfare check configurations per event
        Schema::create('operator_welfare_checks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id');
            $table->unsignedBigInteger('created_by');
            $table->smallInteger('interval_minutes')->default(30);
            $table->boolean('active')->default(true);
            $table->timestamp('last_sent_at')->nullable();
            $table->timestamps();
        });

        // Individual welfare check responses
        Schema::create('operator_welfare_responses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('welfare_check_id');
            $table->unsignedBigInteger('assignment_id');
            $table->boolean('responded')->default(false);
            $table->timestamp('prompted_at');
            $table->timestamp('responded_at')->nullable();
            $table->index(['welfare_check_id', 'assignment_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operator_welfare_responses');
        Schema::dropIfExists('operator_welfare_checks');
        Schema::dropIfExists('operator_sos_alerts');
        Schema::dropIfExists('operator_message_acks');
        Schema::dropIfExists('operator_messages');
        Schema::dropIfExists('operator_gps_pings');
    }
};
