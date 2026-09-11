<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Per-route geofence enforcement.
 *
 *   off     — visits are recorded where GPS is available, nothing is checked
 *   log     — checked; a failure is logged as a violation but not blocked
 *   enforce — a failure blocks the sale until the seller gives a reason
 *
 * Defaults to off so turning this on is a deliberate, per-route decision
 * made once outlet coordinates have been collected.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('route_zone_restrictions', function (Blueprint $table) {
            $table->string('geofence_mode', 10)->default('off')->after('customer_route_id');
        });
    }

    public function down(): void
    {
        Schema::table('route_zone_restrictions', function (Blueprint $table) {
            $table->dropColumn('geofence_mode');
        });
    }
};
