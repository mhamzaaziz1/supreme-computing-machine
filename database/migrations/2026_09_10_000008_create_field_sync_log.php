<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Idempotency for the offline field app.
 *
 * Every action a seller takes offline carries a client-generated id. The
 * phone retries until it hears back, so the same order can arrive twice
 * after a dropped connection; this table makes the second arrival return
 * the first result instead of creating a second invoice.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('field_sync_log', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('business_id');
            $table->unsignedInteger('user_id');
            $table->string('client_id', 64);
            $table->string('kind', 20);
            // done | failed
            $table->string('status', 10);
            $table->json('result')->nullable();
            $table->dateTime('happened_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'client_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('field_sync_log');
    }
};
