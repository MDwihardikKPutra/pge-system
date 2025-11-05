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
        Schema::create('work_realizations', function (Blueprint $table) {
            $table->id();
            $table->string('realization_number')->unique(); // Format: RL-YYYYMMDD-XXXX
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('department')->nullable(); // Untuk sementara pakai string
            $table->foreignId('work_plan_id')->nullable()->constrained()->nullOnDelete();
            $table->date('realization_date');
            $table->string('title');
            $table->text('description');
            $table->json('achievements')->nullable();
            $table->text('output_description')->nullable();
            $table->json('output_files')->nullable(); // Array of file paths
            $table->string('work_location')->nullable(); // office/site/wfh/wfa
            $table->decimal('actual_duration_hours', 4, 1)->nullable();
            $table->integer('progress_percentage')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['user_id', 'realization_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_realizations');
    }
};
