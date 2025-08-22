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
        // This migration is a duplicate of 2023_10_25_000000_add_customer_route_id_to_contacts_table.php
        // The column has already been added, so we don't need to add it again
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // No need to drop the column here as it will be handled by the original migration
    }
};
