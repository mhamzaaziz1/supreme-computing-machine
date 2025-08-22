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
        // Skip this migration if it's already been run
        // This is a workaround for the "stuck in processing" issue
        // The column already exists, so we don't need to add it again
        return;

        // The original migration code is commented out below for reference
        /*
        Schema::table('contacts', function (Blueprint $table) {
            $table->integer('customer_route_id')->nullable()->after('customer_group_id');
        });
        */
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn('customer_route_id');
        });
    }
};
