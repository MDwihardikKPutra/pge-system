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
        Schema::create('changelogs', function (Blueprint $table) {
            $table->id();
            $table->string('version', 20); // e.g., "v1.0.0"
            $table->date('release_date');
            $table->string('title'); // e.g., "Initial Release"
            $table->text('changes'); // JSON array of changes
            $table->enum('category', ['feature', 'bugfix', 'improvement', 'security', 'refactor'])->default('feature');
            $table->boolean('is_major')->default(false);
            $table->timestamps();
            
            $table->index('release_date');
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('changelogs');
    }
};
