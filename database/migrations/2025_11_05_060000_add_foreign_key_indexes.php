<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add indexes for foreign keys that are missing standalone indexes
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
                    // Table might not exist, return false
                    return false;
                }
            }
        };

        // Index for work_plans.project_id (already exists from EAR migration, but add check)
        Schema::table('work_plans', function (Blueprint $table) use ($hasIndex) {
            if (!$hasIndex('work_plans', 'work_plans_project_id_index')) {
                $table->index('project_id', 'work_plans_project_id_index');
            }
        });

        // Index for work_realizations.project_id (already exists from EAR migration, but add check)
        Schema::table('work_realizations', function (Blueprint $table) use ($hasIndex) {
            if (!$hasIndex('work_realizations', 'work_realizations_project_id_index')) {
                $table->index('project_id', 'work_realizations_project_id_index');
            }
        });

        // Index for spd.project_id (standalone index, composite already exists)
        Schema::table('spd', function (Blueprint $table) use ($hasIndex) {
            if (!$hasIndex('spd', 'spd_project_id_index')) {
                $table->index('project_id', 'spd_project_id_index');
            }
        });

        // Index for purchases.project_id (standalone index, composite already exists)
        Schema::table('purchases', function (Blueprint $table) use ($hasIndex) {
            if (!$hasIndex('purchases', 'purchases_project_id_index')) {
                $table->index('project_id', 'purchases_project_id_index');
            }
        });

        // Index for vendor_payments.project_id (standalone index, composite already exists)
        Schema::table('vendor_payments', function (Blueprint $table) use ($hasIndex) {
            if (!$hasIndex('vendor_payments', 'vendor_payments_project_id_index')) {
                $table->index('project_id', 'vendor_payments_project_id_index');
            }
        });

        // Index for leave_requests.user_id (already exists from performance migration, but add check)
        Schema::table('leave_requests', function (Blueprint $table) use ($hasIndex) {
            if (!$hasIndex('leave_requests', 'leave_requests_user_id_index')) {
                $table->index('user_id', 'leave_requests_user_id_index');
            }
        });

        // Index for activity_logs.user_id
        if (Schema::hasTable('activity_logs')) {
            Schema::table('activity_logs', function (Blueprint $table) use ($hasIndex) {
                if (!$hasIndex('activity_logs', 'activity_logs_user_id_index')) {
                    $table->index('user_id', 'activity_logs_user_id_index');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_plans', function (Blueprint $table) {
            try {
                $table->dropIndex('work_plans_project_id_index');
            } catch (\Exception $e) {
                // Index might not exist
            }
        });

        Schema::table('work_realizations', function (Blueprint $table) {
            try {
                $table->dropIndex('work_realizations_project_id_index');
            } catch (\Exception $e) {
                // Index might not exist
            }
        });

        Schema::table('spd', function (Blueprint $table) {
            try {
                $table->dropIndex('spd_project_id_index');
            } catch (\Exception $e) {
                // Index might not exist
            }
        });

        Schema::table('purchases', function (Blueprint $table) {
            try {
                $table->dropIndex('purchases_project_id_index');
            } catch (\Exception $e) {
                // Index might not exist
            }
        });

        Schema::table('vendor_payments', function (Blueprint $table) {
            try {
                $table->dropIndex('vendor_payments_project_id_index');
            } catch (\Exception $e) {
                // Index might not exist
            }
        });

        Schema::table('leave_requests', function (Blueprint $table) {
            try {
                $table->dropIndex('leave_requests_user_id_index');
            } catch (\Exception $e) {
                // Index might not exist
            }
        });

        if (Schema::hasTable('activity_logs')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                try {
                    $table->dropIndex('activity_logs_user_id_index');
                } catch (\Exception $e) {
                    // Index might not exist
                }
            });
        }
    }
};

