<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSupplyChainVehicleIdToTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('supply_chain_vehicle_id')->nullable()->after('expense_for')
                ->comment('Foreign key to supply_chain_vehicles table');
            
            $table->foreign('supply_chain_vehicle_id')
                ->references('id')->on('supply_chain_vehicles')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['supply_chain_vehicle_id']);
            $table->dropColumn('supply_chain_vehicle_id');
        });
    }
}