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
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // e.g., 'spd', 'leave', 'purchase'
            $table->string('label'); // e.g., 'SPD', 'Cuti & Izin'
            $table->string('icon')->nullable(); // e.g., '🚗', '🏝️'
            $table->text('description')->nullable();
            $table->json('routes')->nullable(); // {index: 'user.spd.index', approval: 'admin.approvals.spd'}
            $table->json('actions')->nullable(); // ['view', 'create', 'update', 'delete', 'approve']
            $table->boolean('assignable_to_user')->default(true); // Bisa di-assign ke user
            $table->boolean('admin_only')->default(false); // Hanya untuk admin (testing phase)
            $table->boolean('is_default')->default(false); // Default module (work-plan, work-realization)
            $table->boolean('is_active')->default(true); // Enable/disable modul
            $table->integer('sort_order')->default(0); // Urutan tampil
            $table->timestamps();
            
            $table->index('key');
            $table->index('is_active');
            $table->index('assignable_to_user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
