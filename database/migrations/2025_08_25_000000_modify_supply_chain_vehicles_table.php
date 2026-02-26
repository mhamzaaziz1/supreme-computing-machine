<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Use raw SQL to make customer_route_id nullable
        DB::statement('ALTER TABLE supply_chain_vehicles MODIFY customer_route_id INT UNSIGNED NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Use raw SQL to make customer_route_id non-nullable
        DB::statement('ALTER TABLE supply_chain_vehicles MODIFY customer_route_id INT UNSIGNED NOT NULL');
    }
};
