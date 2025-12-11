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
        Schema::create('shift_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // "Ca 1", "Ca sáng", "Ca chiều"
            $table->time('start_time'); // "08:00:00"
            $table->time('end_time'); // "12:00:00"
            $table->boolean('is_default')->default(false); // True for seeded defaults
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Manual FK constraint
            $table->foreignId('diem_ban_id')->constrained('diem_ban')->onDelete('cascade');
            
            // Index for faster queries
            $table->index(['diem_ban_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shift_templates');
    }
};
