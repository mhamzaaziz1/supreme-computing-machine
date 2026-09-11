<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Credit control, cheque tracking and the approvals inbox.
 *
 * - contacts gain an overdue-days ceiling and a manual/automatic hold flag,
 *   alongside the credit_limit column UltimatePOS already had.
 * - transaction_payments gain the fields a post-dated cheque register needs;
 *   cheque_number already existed.
 * - ops_approvals is the one queue every "a manager must decide" exception
 *   goes through (credit overrides, van variances, returns).
 * - bounced_cheques keeps the record of a cheque whose payment rows had to be
 *   deleted to put the invoices back into due.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->unsignedInteger('max_overdue_days')->nullable()->after('credit_limit');
            $table->boolean('credit_hold')->default(false)->after('max_overdue_days');
            $table->string('credit_hold_reason')->nullable()->after('credit_hold');
        });

        Schema::table('transaction_payments', function (Blueprint $table) {
            $table->string('cheque_bank')->nullable()->after('cheque_number');
            $table->date('cheque_date')->nullable()->after('cheque_bank');
            // pending → deposited → cleared, or bounced. Null for non-cheques.
            $table->string('cheque_status', 20)->nullable()->after('cheque_date');
            $table->dateTime('cheque_status_at')->nullable()->after('cheque_status');
            $table->index(['business_id', 'cheque_status']);
        });

        Schema::create('ops_approvals', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('business_id');
            $table->string('type', 40);
            $table->string('status', 20)->default('pending');
            $table->unsignedInteger('contact_id')->nullable();
            $table->string('subject_type', 60)->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->decimal('amount', 22, 4)->nullable();
            $table->string('summary');
            $table->json('payload')->nullable();
            $table->unsignedInteger('requested_by');
            $table->unsignedInteger('decided_by')->nullable();
            $table->dateTime('decided_at')->nullable();
            $table->string('decision_note')->nullable();
            $table->dateTime('consumed_at')->nullable();
            $table->string('consumed_ref')->nullable();
            $table->timestamps();

            $table->index(['business_id', 'status']);
            $table->index(['business_id', 'type']);
        });

        Schema::create('bounced_cheques', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('business_id');
            $table->unsignedInteger('contact_id');
            $table->string('cheque_number')->nullable();
            $table->string('cheque_bank')->nullable();
            $table->date('cheque_date')->nullable();
            $table->decimal('amount', 22, 4);
            $table->string('payment_ref_no')->nullable();
            $table->string('note')->nullable();
            $table->unsignedInteger('recorded_by');
            $table->timestamps();

            $table->index(['business_id', 'contact_id']);
        });

        // Managers approve credit overrides and van variances. Admins bypass
        // permission checks, so this only matters for non-admin roles.
        DB::table('permissions')->insertOrIgnore([
            'name' => 'ops.approve',
            'guard_name' => 'web',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        Schema::dropIfExists('bounced_cheques');
        Schema::dropIfExists('ops_approvals');

        Schema::table('transaction_payments', function (Blueprint $table) {
            $table->dropIndex(['business_id', 'cheque_status']);
            $table->dropColumn(['cheque_bank', 'cheque_date', 'cheque_status', 'cheque_status_at']);
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn(['max_overdue_days', 'credit_hold', 'credit_hold_reason']);
        });

        DB::table('permissions')->where('name', 'ops.approve')->delete();
    }
};
