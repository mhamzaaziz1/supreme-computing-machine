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
        Schema::create('route_seller_assignments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_id')->unsigned();
            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            
            // Seller (user) reference
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Route reference
            $table->integer('customer_route_id')->unsigned();
            $table->foreign('customer_route_id')->references('id')->on('customer_routes')->onDelete('cascade');
            
            // Assignment status
            $table->boolean('is_active')->default(1);
            
            // Assignment metadata
            $table->integer('created_by')->unsigned();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
            
            // Ensure a seller can only be assigned to a route once
            $table->unique(['user_id', 'customer_route_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('route_seller_assignments');
    }
};