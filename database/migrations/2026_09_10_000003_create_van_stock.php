<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Vans as stock locations, and the nightly settlement.
 *
 * Each supply-chain vehicle gets its own business location, so a van's
 * stock is just stock at a location: loads and unloads are ordinary stock
 * transfers, sales from the van decrement it like any sale, and every
 * existing stock report works on vans without change.
 *
 * van_settlements records the day-end count: expected vs counted stock and
 * cash, and what happened to any difference.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('supply_chain_vehicles', function (Blueprint $table) {
            $table->unsignedInteger('location_id')->nullable()->after('customer_route_id');
        });

        Schema::table('business_locations', function (Blueprint $table) {
            $table->unsignedBigInteger('supply_chain_vehicle_id')->nullable()->after('is_oil_change_point');
        });

        Schema::create('van_settlements', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('business_id');
            $table->unsignedBigInteger('supply_chain_vehicle_id');
            $table->unsignedInteger('location_id');
            $table->date('settlement_date');
            // settled | pending_approval | approved | rejected
            $table->string('status', 20);
            $table->decimal('expected_cash', 22, 4)->default(0);
            $table->decimal('counted_cash', 22, 4)->default(0);
            $table->decimal('expected_cheques', 22, 4)->default(0);
            $table->decimal('counted_cheques', 22, 4)->default(0);
            $table->decimal('stock_short_value', 22, 4)->default(0);
            $table->decimal('stock_over_value', 22, 4)->default(0);
            $table->json('lines');
            $table->json('sellers')->nullable();
            $table->unsignedInteger('unload_transfer_id')->nullable();
            $table->unsignedInteger('adjustment_id')->nullable();
            $table->unsignedBigInteger('approval_id')->nullable();
            $table->unsignedInteger('odometer_end')->nullable();
            $table->string('notes', 500)->nullable();
            $table->unsignedInteger('created_by');
            $table->timestamps();

            $table->index(['business_id', 'settlement_date']);
            $table->index(['supply_chain_vehicle_id', 'settlement_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('van_settlements');

        Schema::table('business_locations', function (Blueprint $table) {
            $table->dropColumn('supply_chain_vehicle_id');
        });

        Schema::table('supply_chain_vehicles', function (Blueprint $table) {
            $table->dropColumn('location_id');
        });
    }
};
