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
        Schema::create('user_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('module'); // e.g., 'leave', 'payment', 'work'
            $table->string('permission_key'); // e.g., 'submit', 'approve', 'view_all'
            $table->timestamps();
            
            // Ensure unique combination
            $table->unique(['user_id', 'module', 'permission_key']);
            
            $table->index('user_id');
            $table->index(['module', 'permission_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_permissions');
    }
};
