<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Oil-change reminders sent to vehicle owners, so the same owner is not
 * chased twice in a week and the bay can see who was reminded before they
 * came back.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_reminders', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('business_id');
            $table->unsignedBigInteger('vehicle_id');
            $table->unsignedInteger('contact_id');
            $table->date('due_on')->nullable();
            $table->string('channel', 20);
            $table->text('message')->nullable();
            $table->dateTime('sent_at');
            $table->unsignedInteger('sent_by');
            $table->timestamps();

            $table->index(['business_id', 'vehicle_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_reminders');
    }
};
