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
        // Check if the columns exist before trying to add them
        if (!Schema::hasColumn('users', 'latitude') && !Schema::hasColumn('users', 'longitude') && !Schema::hasColumn('users', 'location_updated_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->decimal('latitude', 10, 7)->nullable()->after('language');
                $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('latitude');
            $table->dropColumn('longitude');
            $table->dropColumn('location_updated_at');
        });
    }
};
