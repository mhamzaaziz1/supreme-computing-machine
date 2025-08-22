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
        if (Schema::hasTable('supply_chain_vehicle_mileage')) {
            return;
        }

        Schema::create('supply_chain_vehicle_mileage', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supply_chain_vehicle_id')->comment('Foreign key to supply_chain_vehicles table');
            $table->date('date')->comment('Date of the mileage record');
            $table->integer('start_mileage')->comment('Starting mileage');
            $table->integer('end_mileage')->comment('Ending mileage');
            $table->string('start_picture')->nullable()->comment('Path to the start mileage picture');
            $table->string('end_picture')->nullable()->comment('Path to the end mileage picture');
            $table->text('notes')->nullable()->comment('Additional notes');
            $table->unsignedInteger('business_id');
            $table->unsignedInteger('created_by');
            $table->timestamps();

            $table->foreign('supply_chain_vehicle_id')
                ->references('id')
                ->on('supply_chain_vehicles')
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
        Schema::dropIfExists('supply_chain_vehicle_mileage');
    }
};