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
        // Check if assignment_date column exists
        $columnExists = DB::select("SHOW COLUMNS FROM route_seller_assignments LIKE 'assignment_date'");

        if (empty($columnExists)) {
            // Add assignment_date field
            Schema::table('route_seller_assignments', function (Blueprint $table) {
                $table->date('assignment_date')->nullable()->after('is_active');
            });
        }

        // Check if the unique constraint exists
        $indexExists = DB::select("SHOW INDEXES FROM route_seller_assignments WHERE Key_name = 'route_seller_unique_with_date'");

        if (empty($indexExists)) {
            // Add a new unique constraint including assignment_date
            // We'll keep the old unique constraint as it's needed for foreign key constraints
            try {
                Schema::table('route_seller_assignments', function (Blueprint $table) {
                    $table->unique(['user_id', 'customer_route_id', 'assignment_date'], 'route_seller_unique_with_date');
                });
            } catch (\Exception $e) {
                // If there's an error adding the unique constraint, log it but continue
                \Log::error('Error adding unique constraint: ' . $e->getMessage());
            }
        }

        // Mark this migration as run
        DB::table('migrations')->insert([
            'migration' => '2025_08_25_000001_add_assignment_date_to_route_seller_assignments_table',
            'batch' => DB::table('migrations')->max('batch') + 1
        ]);
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

            // Remove the assignment_date field
            $table->dropColumn('assignment_date');

            // Note: We don't need to add back the original unique constraint
            // since we never dropped it in the up method
        });
    }
};
