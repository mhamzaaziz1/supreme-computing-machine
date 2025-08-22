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
        if (Schema::hasTable('supply_chain_vehicle_expenses')) {
            return;
        }

        Schema::create('supply_chain_vehicle_expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supply_chain_vehicle_id')->comment('Foreign key to supply_chain_vehicles table');
            $table->unsignedBigInteger('supply_chain_vehicle_mileage_id')->nullable()->comment('Foreign key to supply_chain_vehicle_mileage table');
            $table->date('date')->comment('Date of the expense');
            $table->string('expense_type')->comment('Type of expense (fuel, maintenance, repair, etc.)');
            $table->decimal('amount', 15, 4)->comment('Amount of the expense');
            $table->string('receipt_image')->nullable()->comment('Path to the receipt image');
            $table->text('description')->nullable()->comment('Description of the expense');
            $table->unsignedInteger('business_id');
            $table->unsignedInteger('created_by');
            $table->timestamps();

            $table->foreign('supply_chain_vehicle_id')
                ->references('id')
                ->on('supply_chain_vehicles')
                ->onDelete('cascade');

            $table->foreign('supply_chain_vehicle_mileage_id')
                ->references('id')
                ->on('supply_chain_vehicle_mileage')
                ->onDelete('set null');

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
        Schema::dropIfExists('supply_chain_vehicle_expenses');
    }
};