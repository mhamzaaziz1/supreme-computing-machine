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
        Schema::create('geofence_violation_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_id')->unsigned();
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            
            // Seller (user) reference
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Route reference (optional - may be outside any route)
            $table->integer('customer_route_id')->unsigned()->nullable();
            $table->foreign('customer_route_id')->references('id')->on('customer_routes')->onDelete('cascade');
            
            // Contact (outlet) reference (optional - may be outside any outlet)
            $table->integer('contact_id')->unsigned()->nullable();
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
            
            // Violation type: outside_route, outside_outlet, mock_location, accuracy_too_low
            $table->string('violation_type');
            
            // Action attempted: place_order, record_payment, mark_visit_done, check_in
            $table->string('attempted_action');
            
            // Location data
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->decimal('accuracy', 10, 2)->nullable(); // GPS accuracy in meters
            
            // Distance from nearest valid location (in meters)
            $table->decimal('distance_from_valid', 10, 2)->nullable();
            
            // Device information for fraud prevention
            $table->string('device_id')->nullable();
            $table->boolean('is_mock_location')->default(false);
            
            // Additional details about the violation
            $table->text('details')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('geofence_violation_logs');
    }
};