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
        Schema::table('route_seller_assignments', function (Blueprint $table) {
            // Add assignment_date field
            $table->date('assignment_date')->nullable()->after('is_active');
            
            // Drop the existing unique constraint
            $table->dropUnique(['user_id', 'customer_route_id']);
            
            // Add a new unique constraint including assignment_date
            $table->unique(['user_id', 'customer_route_id', 'assignment_date'], 'route_seller_unique_with_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('route_seller_assignments', function (Blueprint $table) {
            // Drop the new unique constraint
            $table->dropUnique('route_seller_unique_with_date');
            
            // Add back the original unique constraint
            $table->unique(['user_id', 'customer_route_id']);
            
            // Remove the assignment_date field
            $table->dropColumn('assignment_date');
        });
    }
};