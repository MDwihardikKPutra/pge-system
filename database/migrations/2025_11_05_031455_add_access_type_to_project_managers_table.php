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
        Schema::table('project_managers', function (Blueprint $table) {
            // Add access_type column: 'pm' (Work Plans & Realizations only), 'finance' (Payments only), 'full' (All)
            $table->enum('access_type', ['pm', 'finance', 'full'])->default('pm')->after('project_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_managers', function (Blueprint $table) {
            $table->dropColumn('access_type');
        });
    }
};
