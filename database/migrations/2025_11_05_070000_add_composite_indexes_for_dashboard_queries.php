<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add composite indexes for common dashboard queries
     */
    public function up(): void
    {
        // Helper function to check if index exists (for SQLite compatibility)
        $hasIndex = function($table, $indexName) {
            if (DB::getDriverName() === 'sqlite') {
                $indexes = DB::select("SELECT name FROM sqlite_master WHERE type='index' AND tbl_name=? AND name=?", [$table, $indexName]);
                return count($indexes) > 0;
            } else {
                try {
                    $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
                    return count($indexes) > 0;
                } catch (\Exception $e) {
                    return false;
                }
            }
        };

        // Composite index for work_plans: (plan_date, created_at) for dashboard queries
        Schema::table('work_plans', function (Blueprint $table) use ($hasIndex) {
            if (!$hasIndex('work_plans', 'work_plans_plan_date_created_at_index')) {
                $table->index(['plan_date', 'created_at'], 'work_plans_plan_date_created_at_index');
            }
        });

        // Composite index for work_realizations: (realization_date, created_at) for dashboard queries
        Schema::table('work_realizations', function (Blueprint $table) use ($hasIndex) {
            if (!$hasIndex('work_realizations', 'work_realizations_realization_date_created_at_index')) {
                $table->index(['realization_date', 'created_at'], 'work_realizations_realization_date_created_at_index');
            }
        });

        // Composite index for spd: (created_at, status) for dashboard and approval queries
        Schema::table('spd', function (Blueprint $table) use ($hasIndex) {
            if (!$hasIndex('spd', 'spd_created_at_status_index')) {
                $table->index(['created_at', 'status'], 'spd_created_at_status_index');
            }
        });

        // Composite index for purchases: (created_at, status) for dashboard and approval queries
        Schema::table('purchases', function (Blueprint $table) use ($hasIndex) {
            if (!$hasIndex('purchases', 'purchases_created_at_status_index')) {
                $table->index(['created_at', 'status'], 'purchases_created_at_status_index');
            }
        });

        // Composite index for vendor_payments: (created_at, status) for dashboard and approval queries
        Schema::table('vendor_payments', function (Blueprint $table) use ($hasIndex) {
            if (!$hasIndex('vendor_payments', 'vendor_payments_created_at_status_index')) {
                $table->index(['created_at', 'status'], 'vendor_payments_created_at_status_index');
            }
        });

        // Composite index for leave_requests: (created_at, status) for dashboard queries
        Schema::table('leave_requests', function (Blueprint $table) use ($hasIndex) {
            if (!$hasIndex('leave_requests', 'leave_requests_created_at_status_index')) {
                $table->index(['created_at', 'status'], 'leave_requests_created_at_status_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_plans', function (Blueprint $table) {
            try {
                $table->dropIndex('work_plans_plan_date_created_at_index');
            } catch (\Exception $e) {
                // Index might not exist
            }
        });

        Schema::table('work_realizations', function (Blueprint $table) {
            try {
                $table->dropIndex('work_realizations_realization_date_created_at_index');
            } catch (\Exception $e) {
                // Index might not exist
            }
        });

        Schema::table('spd', function (Blueprint $table) {
            try {
                $table->dropIndex('spd_created_at_status_index');
            } catch (\Exception $e) {
                // Index might not exist
            }
        });

        Schema::table('purchases', function (Blueprint $table) {
            try {
                $table->dropIndex('purchases_created_at_status_index');
            } catch (\Exception $e) {
                // Index might not exist
            }
        });

        Schema::table('vendor_payments', function (Blueprint $table) {
            try {
                $table->dropIndex('vendor_payments_created_at_status_index');
            } catch (\Exception $e) {
                // Index might not exist
            }
        });

        Schema::table('leave_requests', function (Blueprint $table) {
            try {
                $table->dropIndex('leave_requests_created_at_status_index');
            } catch (\Exception $e) {
                // Index might not exist
            }
        });
    }
};


