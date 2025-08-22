<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('route_zone_restrictions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_id')->unsigned();
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            
            // Route reference
            $table->integer('customer_route_id')->unsigned();
            $table->foreign('customer_route_id')->references('id')->on('customer_routes')->onDelete('cascade');
            
            // Module restrictions
            $table->boolean('enable_returns')->default(true);
            $table->boolean('enable_collections')->default(true);
            $table->boolean('enable_discounts')->default(true);
            $table->boolean('enable_credit_sales')->default(true);
            
            // Order restrictions
            $table->decimal('minimum_order_value', 15, 4)->nullable();
            $table->decimal('maximum_order_value', 15, 4)->nullable();
            
            // Promotion restrictions
            $table->json('allowed_promotions')->nullable(); // JSON array of promotion IDs
            
            // Time restrictions
            $table->time('allowed_start_time')->nullable(); // Only allow operations during certain hours
            $table->time('allowed_end_time')->nullable();
            
            // Days of week restrictions (JSON array of days: 0=Sunday, 1=Monday, etc.)
            $table->json('allowed_days')->nullable();
            
            $table->timestamps();
            
            // Ensure a route can only have one set of restrictions
            $table->unique(['customer_route_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('route_zone_restrictions');
    }
};