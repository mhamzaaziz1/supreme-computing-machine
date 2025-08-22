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
        Schema::table('contacts', function (Blueprint $table) {
            // Add geofence type (radius or polygon)
            $table->string('geofence_type')->nullable()->after('longitude');
            
            // Add geofence radius (in meters) for radius type
            $table->decimal('geofence_radius', 10, 2)->nullable()->after('geofence_type');
            
            // Add geofence polygon data (JSON array of lat/lng points) for polygon type
            $table->json('geofence_polygon')->nullable()->after('geofence_radius');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn('geofence_type');
            $table->dropColumn('geofence_radius');
            $table->dropColumn('geofence_polygon');
        });
    }
};