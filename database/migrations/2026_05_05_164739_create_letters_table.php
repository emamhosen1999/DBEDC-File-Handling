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
        Schema::create('letters', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->string('reference', 50)->unique();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->string('sender', 255)->nullable();
            $table->string('recipient', 255)->nullable();
            $table->string('subject', 500)->nullable();
            $table->date('letter_date');
            $table->date('due_date')->nullable();
            $table->enum('priority', ['LOW', 'MEDIUM', 'HIGH', 'URGENT'])->default('MEDIUM');
            $table->enum('status', ['DRAFT', 'PENDING', 'IN_PROGRESS', 'REVIEW', 'COMPLETED', 'ARCHIVED'])->default('PENDING');
            $table->char('department_id', 26)->nullable();
            $table->char('assigned_to', 26)->nullable();
            $table->char('stakeholder_id', 26)->nullable();
            $table->string('file_path', 500)->nullable();
            $table->string('file_name', 255)->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->string('file_mime_type', 100)->nullable();
            $table->char('created_by', 26)->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('reference');
            $table->index('status');
            $table->index('priority');
            $table->index('department_id');
            $table->index('assigned_to');
            $table->index('due_date');
            $table->index('letter_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('letters');
    }
};
