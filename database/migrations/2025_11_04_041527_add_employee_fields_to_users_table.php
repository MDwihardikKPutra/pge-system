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
        Schema::table('users', function (Blueprint $table) {
            // Add columns only if they don't exist
            if (!Schema::hasColumn('users', 'employee_id')) {
                $table->string('employee_id')->unique()->nullable()->after('id');
            }
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'department')) {
                $table->string('department')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'position')) {
                $table->string('position')->nullable()->after('department');
            }
            if (!Schema::hasColumn('users', 'join_date')) {
                $table->date('join_date')->nullable()->after('position');
            }
            if (!Schema::hasColumn('users', 'annual_leave_quota')) {
                $table->integer('annual_leave_quota')->default(12)->after('join_date');
            }
            if (!Schema::hasColumn('users', 'remaining_leave')) {
                $table->integer('remaining_leave')->default(12)->after('annual_leave_quota');
            }
            if (!Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable()->after('remaining_leave');
            }
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('address');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'employee_id',
                'phone',
                'department',
                'position',
                'join_date',
                'annual_leave_quota',
                'remaining_leave',
                'address',
                'is_active',
            ]);
        });
    }
};
