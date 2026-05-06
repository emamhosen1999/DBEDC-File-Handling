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
        Schema::create('tasks', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->char('letter_id', 26)->nullable();
            $table->char('assigned_to', 26)->nullable();
            $table->char('department_id', 26)->nullable();
            $table->enum('status', ['PENDING', 'IN_PROGRESS', 'REVIEW', 'COMPLETED', 'CANCELLED'])->default('PENDING');
            $table->enum('priority', ['LOW', 'MEDIUM', 'HIGH', 'URGENT'])->default('MEDIUM');
            $table->date('due_date')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->char('created_by', 26)->nullable();
            $table->timestamps();

            $table->index('letter_id');
            $table->index('assigned_to');
            $table->index('department_id');
            $table->index('status');
            $table->index('priority');
            $table->index('due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
