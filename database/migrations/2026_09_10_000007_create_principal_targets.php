<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * What the lubricant principal measures a distributor on.
 *
 * - products.pack_litres: the volume of one selling unit (a 4 L can, a
 *   208 L drum, a carton of 12 × 1 L = 12). Principals set targets and pay
 *   incentives in litres, and cost-to-serve is only comparable per litre.
 * - principal_targets: period targets in quantity, litres or value, scoped
 *   to brands, categories or products.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('pack_litres', 10, 3)->nullable()->after('weight');
        });

        Schema::create('principal_targets', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('business_id');
            $table->string('name');
            $table->date('starts_on');
            $table->date('ends_on');
            // qty | litres | value
            $table->string('measure', 10);
            $table->decimal('target', 22, 4);
            $table->json('scope')->nullable();
            $table->unsignedInteger('created_by');
            $table->timestamps();

            $table->index(['business_id', 'ends_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('principal_targets');

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('pack_litres');
        });
    }
};
