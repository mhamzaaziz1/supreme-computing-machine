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
        Schema::create('route_visit_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_id')->unsigned();
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            
            // Seller (user) reference
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Route reference
            $table->integer('customer_route_id')->unsigned();
            $table->foreign('customer_route_id')->references('id')->on('customer_routes')->onDelete('cascade');
            
            // Contact (outlet) reference - nullable for route check-ins that aren't at a specific outlet
            $table->integer('contact_id')->unsigned()->nullable();
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
            
            // Visit type: check_in, check_out, visit_start, visit_end
            $table->string('visit_type');
            
            // Location data
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->decimal('accuracy', 10, 2)->nullable(); // GPS accuracy in meters
            
            // Visit metadata
            $table->dateTime('visit_time');
            $table->text('notes')->nullable();
            
            // Proof of visit
            $table->string('photo_url')->nullable(); // URL to geo-tagged photo
            $table->string('otp')->nullable(); // OTP from retailer
            
            // Device information for fraud prevention
            $table->string('device_id')->nullable();
            $table->boolean('is_mock_location')->default(false);
            
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
        Schema::dropIfExists('route_visit_logs');
    }
};