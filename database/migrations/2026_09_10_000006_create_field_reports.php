<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * What a seller records at an outlet besides an order: damaged or leaking
 * stock (with a photo), and used oil collected for buy-back. Kept apart from
 * route_visit_logs because those require GPS and a route; a report must be
 * possible from the office too.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('field_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('business_id');
            $table->unsignedInteger('contact_id');
            $table->unsignedInteger('user_id');
            // damage | used_oil
            $table->string('kind', 20);
            $table->unsignedInteger('variation_id')->nullable();
            $table->decimal('quantity', 22, 4)->nullable();
            $table->decimal('litres', 12, 3)->nullable();
            $table->decimal('rate', 22, 4)->nullable();
            $table->decimal('amount', 22, 4)->nullable();
            $table->string('photo')->nullable();
            $table->string('notes', 500)->nullable();
            $table->unsignedInteger('route_visit_log_id')->nullable();
            $table->timestamps();

            $table->index(['business_id', 'kind', 'created_at']);
            $table->index(['business_id', 'contact_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('field_reports');
    }
};
