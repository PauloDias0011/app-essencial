<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->text('details');
            $table->foreignId('professor_id')->constrained('users')->onDelete('cascade');
            $table->decimal('total_cost', 10, 2);
            $table->boolean('is_reimbursed')->default(false);
            $table->date('date_expense');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('expenses');
    }
};