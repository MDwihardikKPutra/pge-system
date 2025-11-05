<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('work_plans', function (Blueprint $table) {
            if (!Schema::hasColumn('work_plans', 'project_id')) {
                $table->foreignId('project_id')->nullable()->after('department')->constrained('projects')->nullOnDelete();
            }
        });

        Schema::table('work_realizations', function (Blueprint $table) {
            if (!Schema::hasColumn('work_realizations', 'project_id')) {
                $table->foreignId('project_id')->nullable()->after('department')->constrained('projects')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_plans', function (Blueprint $table) {
            if (Schema::hasColumn('work_plans', 'project_id')) {
                $table->dropForeign(['project_id']);
                $table->dropColumn('project_id');
            }
        });

        Schema::table('work_realizations', function (Blueprint $table) {
            if (Schema::hasColumn('work_realizations', 'project_id')) {
                $table->dropForeign(['project_id']);
                $table->dropColumn('project_id');
            }
        });
    }
};
