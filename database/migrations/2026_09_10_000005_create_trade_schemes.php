<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Trade schemes: the promotions lubricant distribution actually runs on.
 *
 *   free_goods     buy N, get M free (same item or a named one)
 *   slab_discount  % off once an order reaches a quantity slab
 *   target_rebate  period target per outlet with tiered rebates
 *
 * `scope` says which products count (empty = all), `audience` which outlets
 * qualify (empty = all), and `rules` holds the type-specific numbers. JSON
 * rather than columns because each type needs a different shape and the
 * engine is the only reader.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trade_schemes', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('business_id');
            $table->string('name');
            $table->string('type', 20);
            $table->date('starts_on');
            $table->date('ends_on')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('scope')->nullable();
            $table->json('audience')->nullable();
            $table->json('rules');
            $table->unsignedInteger('created_by');
            $table->timestamps();

            $table->index(['business_id', 'is_active']);
        });

        Schema::create('scheme_applications', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('business_id');
            $table->unsignedBigInteger('scheme_id');
            $table->unsignedInteger('transaction_id');
            $table->unsignedInteger('contact_id')->nullable();
            $table->decimal('benefit_value', 22, 4)->default(0);
            $table->json('details')->nullable();
            $table->timestamps();

            $table->index(['business_id', 'scheme_id']);
            $table->index('transaction_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scheme_applications');
        Schema::dropIfExists('trade_schemes');
    }
};
