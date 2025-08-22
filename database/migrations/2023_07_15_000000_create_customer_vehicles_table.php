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
        // Check if the table already exists
        if (Schema::hasTable('customer_vehicles')) {
            return;
        }

        Schema::create('customer_vehicles', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('contact_id')->comment('Foreign key to contacts table');
            $table->string('make')->nullable()->comment('Vehicle make/manufacturer');
            $table->string('model')->nullable()->comment('Vehicle model');
            $table->string('year')->nullable()->comment('Vehicle year');
            $table->string('license_plate')->nullable()->comment('Vehicle license plate number');
            $table->string('color')->nullable()->comment('Vehicle color');
            $table->string('vin')->nullable()->comment('Vehicle identification number');
            $table->text('notes')->nullable()->comment('Additional notes about the vehicle');
            $table->unsignedInteger('business_id');
            $table->unsignedInteger('created_by');
            $table->timestamps();

            $table->foreign('contact_id')
                ->references('id')
                ->on('contacts')
                ->onDelete('cascade');

            $table->foreign('business_id')
                ->references('id')
                ->on('business')
                ->onDelete('cascade');

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_vehicles');
    }
};
