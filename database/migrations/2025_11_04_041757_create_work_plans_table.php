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
        Schema::create('work_plans', function (Blueprint $table) {
            $table->id();
            $table->string('work_plan_number')->unique(); // Format: RK-YYYYMMDD-XXXX
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('department')->nullable(); // Untuk sementara pakai string, bisa diubah ke foreign key nanti
            $table->date('plan_date');
            $table->string('title');
            $table->text('description');
            $table->json('objectives')->nullable();
            $table->text('expected_output')->nullable();
            $table->string('work_location')->nullable(); // office/site/wfh/wfa
            $table->decimal('planned_duration_hours', 4, 1)->default(8);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['user_id', 'plan_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_plans');
    }
};
