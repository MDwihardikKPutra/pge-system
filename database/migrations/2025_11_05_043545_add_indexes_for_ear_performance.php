<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add indexes for EAR page performance optimization
     */
    public function up(): void
    {
        Schema::table('work_plans', function (Blueprint $table) {
            // Index for date filtering (whereYear/whereMonth)
            if (!$this->hasIndex('work_plans', 'work_plans_plan_date_index')) {
                $table->index('plan_date', 'work_plans_plan_date_index');
            }
            
            // Index for project filtering
            if (!$this->hasIndex('work_plans', 'work_plans_project_id_index')) {
                $table->index('project_id', 'work_plans_project_id_index');
            }
        });

        Schema::table('work_realizations', function (Blueprint $table) {
            // Index for date filtering (whereYear/whereMonth)
            if (!$this->hasIndex('work_realizations', 'work_realizations_realization_date_index')) {
                $table->index('realization_date', 'work_realizations_realization_date_index');
            }
            
            // Index for project filtering
            if (!$this->hasIndex('work_realizations', 'work_realizations_project_id_index')) {
                $table->index('project_id', 'work_realizations_project_id_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_plans', function (Blueprint $table) {
            $table->dropIndex('work_plans_plan_date_index');
            $table->dropIndex('work_plans_project_id_index');
        });

        Schema::table('work_realizations', function (Blueprint $table) {
            $table->dropIndex('work_realizations_realization_date_index');
            $table->dropIndex('work_realizations_project_id_index');
        });
    }

    /**
     * Check if index exists
     */
    private function hasIndex(string $table, string $indexName): bool
    {
        $connection = Schema::getConnection();
        $database = $connection->getDatabaseName();
        
        if ($connection->getDriverName() === 'sqlite') {
            // SQLite doesn't have information_schema, check differently
            $indexes = $connection->select("PRAGMA index_list({$table})");
            foreach ($indexes as $index) {
                if ($index->name === $indexName) {
                    return true;
                }
            }
            return false;
        }
        
        // MySQL/MariaDB
        $result = $connection->selectOne(
            "SELECT COUNT(*) as count FROM information_schema.statistics 
             WHERE table_schema = ? AND table_name = ? AND index_name = ?",
            [$database, $table, $indexName]
        );
        
        return $result->count > 0;
    }
};
