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
        Schema::create('vehicle_mileage_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('business_id');
            $table->unsignedInteger('customer_id');
            $table->unsignedBigInteger('vehicle_id');
            $table->unsignedInteger('invoice_id');
            $table->integer('previous_mileage')->nullable();
            $table->integer('oil_change_mileage')->nullable();
            $table->integer('next_mileage')->nullable();
            $table->timestamps();

            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('contacts')->onDelete('cascade');
            $table->foreign('vehicle_id')->references('id')->on('customer_vehicles')->onDelete('cascade');
            $table->foreign('invoice_id')->references('id')->on('transactions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehicle_mileage_records');
    }
};
