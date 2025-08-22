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
            // Add route_assigned_at date field
            $table->date('route_assigned_at')->nullable()->after('customer_route_id')
                ->comment('Date when the vehicle was assigned to a route');
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
            // Remove route_assigned_at date field
            $table->dropColumn('route_assigned_at');
        });
    }
};