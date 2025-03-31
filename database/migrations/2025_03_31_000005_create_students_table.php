<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('date_of_birth');
            $table->string('grade_year');
            $table->text('special_observations')->nullable();
            $table->string('gender');
            $table->string('school');
            $table->string('address');
            $table->foreignId('parent_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('professor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('students');
    }
};