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
      // Migration: Students Table
Schema::create('students', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->date('date_of_birth');
    $table->string('grade_year');
    $table->text('special_observations')->nullable();
    $table->enum('gender', ['M', 'F', 'Outro']);
    $table->string('school');
    $table->string('address');
    $table->foreignId('parent_id')->constrained('users')->onDelete('cascade');
    $table->foreignId('professor_id')->constrained('users')->onDelete('cascade');
    $table->timestamps();
});

// Migration: Class Plans Table
Schema::create('class_plans', function (Blueprint $table) {
    $table->id();
    $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
    $table->foreignId('professor_id')->constrained('users')->onDelete('cascade');
    $table->string('file_path');
    $table->timestamps();
});

// Migration: Grades Table
Schema::create('grades', function (Blueprint $table) {
    $table->id();
    $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
    $table->foreignId('professor_id')->constrained('users')->onDelete('cascade');
    $table->string('subject');
    $table->integer('semester');
    $table->decimal('grade', 5, 2);
    $table->timestamps();
});

// Migration: Announcements Table
Schema::create('announcements', function (Blueprint $table) {
    $table->id();
    $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
    $table->text('content');
    $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
    $table->timestamps();
});

// Migration: Expenses Table
Schema::create('expenses', function (Blueprint $table) {
    $table->id();
    $table->text('details');
    $table->foreignId('professor_id')->constrained('users')->onDelete('cascade');
    $table->decimal('total_cost', 10, 2);
    $table->boolean('is_reimbursed')->default(false);
    $table->timestamps();
});

// Migration: Schedules Table
Schema::create('schedules', function (Blueprint $table) {
    $table->id();
    $table->foreignId('professor_id')->constrained('users')->onDelete('cascade');
    $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
    $table->dateTime('scheduled_at');
    $table->timestamps();
});

        

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('failed_jobs');
    }
};
