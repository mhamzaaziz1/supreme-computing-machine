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
        // Insert a record for the problematic migration into the migrations table
        DB::table('migrations')->insert([
            'migration' => '2025_08_17_024544_add_location_fields_to_users_table',
            'batch' => DB::table('migrations')->max('batch')
        ]);

        // Check if the columns exist before trying to add them
        if (!Schema::hasColumn('users', 'latitude')) {
            Schema::table('users', function (Blueprint $table) {
                $table->decimal('latitude', 10, 7)->nullable()->after('language');
            });
        }

        if (!Schema::hasColumn('users', 'longitude')) {
            Schema::table('users', function (Blueprint $table) {
                $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            });
        }

        if (!Schema::hasColumn('users', 'location_updated_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('location_updated_at')->nullable()->after('longitude');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
