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
        Schema::create('route_outlet_sequence', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_id')->unsigned();
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            
            // Route reference
            $table->integer('customer_route_id')->unsigned();
            $table->foreign('customer_route_id')->references('id')->on('customer_routes')->onDelete('cascade');
            
            // Contact (outlet) reference
            $table->integer('contact_id')->unsigned();
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
            
            // Sequence number (order in the route)
            $table->integer('sequence_number');
            
            // Optional start/end time for this outlet in the route
            $table->time('expected_start_time')->nullable();
            $table->time('expected_end_time')->nullable();
            
            // Optional minimum visit duration in minutes
            $table->integer('min_visit_duration')->nullable();
            
            // Optional notes for this outlet in the route
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Ensure an outlet can only appear once in a route
            $table->unique(['customer_route_id', 'contact_id']);
            
            // Ensure sequence numbers are unique within a route
            $table->unique(['customer_route_id', 'sequence_number']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('route_outlet_sequence');
    }
};