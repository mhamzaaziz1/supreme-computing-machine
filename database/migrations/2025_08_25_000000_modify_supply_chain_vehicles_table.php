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
        Schema::table('supply_chain_vehicles', function (Blueprint $table) {
            // Make customer_route_id nullable
            $table->unsignedInteger('customer_route_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('supply_chain_vehicles', function (Blueprint $table) {
            // Revert back to non-nullable
            $table->unsignedInteger('customer_route_id')->nullable(false)->change();
        });
    }
};