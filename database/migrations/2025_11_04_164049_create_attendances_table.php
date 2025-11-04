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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->dateTime('clock_in_time')->nullable();
            $table->dateTime('clock_out_time')->nullable();
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->string('clock_in_purpose')->nullable();
            $table->string('clock_out_purpose')->nullable();
            $table->decimal('total_hours', 8, 2)->nullable();
            $table->boolean('is_late')->default(false);
            $table->boolean('is_early_departure')->default(false);
            $table->string('status')->default('active'); // active, completed
            $table->date('attendance_date');
            $table->timestamps();
            
            // Ensure one attendance record per user per day
            $table->unique(['user_id', 'attendance_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
