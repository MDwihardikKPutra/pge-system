<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Helper function to check if index exists (for SQLite compatibility)
        $hasIndex = function($table, $indexName) {
            if (DB::getDriverName() === 'sqlite') {
                $indexes = DB::select("SELECT name FROM sqlite_master WHERE type='index' AND tbl_name=? AND name=?", [$table, $indexName]);
                return count($indexes) > 0;
            } else {
                $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
                return count($indexes) > 0;
            }
        };

        // Indexes for work_plans table
        Schema::table('work_plans', function (Blueprint $table) use ($hasIndex) {
            // Index for user_id filtering
            if (!$hasIndex('work_plans', 'work_plans_user_id_index')) {
                $table->index('user_id', 'work_plans_user_id_index');
            }
            
            // Index for created_at sorting
            if (!$hasIndex('work_plans', 'work_plans_created_at_index')) {
                $table->index('created_at', 'work_plans_created_at_index');
            }
            
            // Composite index for common query: user_id + created_at
            if (!$hasIndex('work_plans', 'work_plans_user_id_created_at_index')) {
                $table->index(['user_id', 'created_at'], 'work_plans_user_id_created_at_index');
            }
        });

        // Indexes for work_realizations table
        Schema::table('work_realizations', function (Blueprint $table) use ($hasIndex) {
            // Index for user_id filtering
            if (!$hasIndex('work_realizations', 'work_realizations_user_id_index')) {
                $table->index('user_id', 'work_realizations_user_id_index');
            }
            
            // Index for created_at sorting
            if (!$hasIndex('work_realizations', 'work_realizations_created_at_index')) {
                $table->index('created_at', 'work_realizations_created_at_index');
            }
            
            // Composite index for common query: user_id + created_at
            if (!$hasIndex('work_realizations', 'work_realizations_user_id_created_at_index')) {
                $table->index(['user_id', 'created_at'], 'work_realizations_user_id_created_at_index');
            }
        });

        // Indexes for spd table
        Schema::table('spd', function (Blueprint $table) use ($hasIndex) {
            // Index for user_id filtering
            if (!$hasIndex('spd', 'spd_user_id_index')) {
                $table->index('user_id', 'spd_user_id_index');
            }
            
            // Index for status filtering
            if (!$hasIndex('spd', 'spd_status_index')) {
                $table->index('status', 'spd_status_index');
            }
            
            // Index for created_at sorting
            if (!$hasIndex('spd', 'spd_created_at_index')) {
                $table->index('created_at', 'spd_created_at_index');
            }
            
            // Composite index for common query: project_id + status + created_at
            if (!$hasIndex('spd', 'spd_project_status_created_index')) {
                $table->index(['project_id', 'status', 'created_at'], 'spd_project_status_created_index');
            }
        });

        // Indexes for purchases table
        Schema::table('purchases', function (Blueprint $table) use ($hasIndex) {
            // Index for user_id filtering
            if (!$hasIndex('purchases', 'purchases_user_id_index')) {
                $table->index('user_id', 'purchases_user_id_index');
            }
            
            // Index for status filtering
            if (!$hasIndex('purchases', 'purchases_status_index')) {
                $table->index('status', 'purchases_status_index');
            }
            
            // Index for created_at sorting
            if (!$hasIndex('purchases', 'purchases_created_at_index')) {
                $table->index('created_at', 'purchases_created_at_index');
            }
            
            // Composite index for common query: project_id + status + created_at
            if (!$hasIndex('purchases', 'purchases_project_status_created_index')) {
                $table->index(['project_id', 'status', 'created_at'], 'purchases_project_status_created_index');
            }
        });

        // Indexes for vendor_payments table
        Schema::table('vendor_payments', function (Blueprint $table) use ($hasIndex) {
            // Index for user_id filtering
            if (!$hasIndex('vendor_payments', 'vendor_payments_user_id_index')) {
                $table->index('user_id', 'vendor_payments_user_id_index');
            }
            
            // Index for status filtering
            if (!$hasIndex('vendor_payments', 'vendor_payments_status_index')) {
                $table->index('status', 'vendor_payments_status_index');
            }
            
            // Index for created_at sorting
            if (!$hasIndex('vendor_payments', 'vendor_payments_created_at_index')) {
                $table->index('created_at', 'vendor_payments_created_at_index');
            }
            
            // Composite index for common query: project_id + status + created_at
            if (!$hasIndex('vendor_payments', 'vendor_payments_project_status_created_index')) {
                $table->index(['project_id', 'status', 'created_at'], 'vendor_payments_project_status_created_index');
            }
        });

        // Indexes for leave_requests table
        Schema::table('leave_requests', function (Blueprint $table) use ($hasIndex) {
            // Index for user_id filtering
            if (!$hasIndex('leave_requests', 'leave_requests_user_id_index')) {
                $table->index('user_id', 'leave_requests_user_id_index');
            }
            
            // Index for status filtering
            if (!$hasIndex('leave_requests', 'leave_requests_status_index')) {
                $table->index('status', 'leave_requests_status_index');
            }
            
            // Index for created_at sorting
            if (!$hasIndex('leave_requests', 'leave_requests_created_at_index')) {
                $table->index('created_at', 'leave_requests_created_at_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_plans', function (Blueprint $table) {
            $table->dropIndex('work_plans_user_id_index');
            $table->dropIndex('work_plans_created_at_index');
            $table->dropIndex('work_plans_user_id_created_at_index');
        });

        Schema::table('work_realizations', function (Blueprint $table) {
            $table->dropIndex('work_realizations_user_id_index');
            $table->dropIndex('work_realizations_created_at_index');
            $table->dropIndex('work_realizations_user_id_created_at_index');
        });

        Schema::table('spd', function (Blueprint $table) {
            $table->dropIndex('spd_user_id_index');
            $table->dropIndex('spd_status_index');
            $table->dropIndex('spd_created_at_index');
            $table->dropIndex('spd_project_status_created_index');
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->dropIndex('purchases_user_id_index');
            $table->dropIndex('purchases_status_index');
            $table->dropIndex('purchases_created_at_index');
            $table->dropIndex('purchases_project_status_created_index');
        });

        Schema::table('vendor_payments', function (Blueprint $table) {
            $table->dropIndex('vendor_payments_user_id_index');
            $table->dropIndex('vendor_payments_status_index');
            $table->dropIndex('vendor_payments_created_at_index');
            $table->dropIndex('vendor_payments_project_status_created_index');
        });

        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropIndex('leave_requests_user_id_index');
            $table->dropIndex('leave_requests_status_index');
            $table->dropIndex('leave_requests_created_at_index');
        });
    }
};


