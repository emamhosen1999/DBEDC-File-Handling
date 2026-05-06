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
        Schema::create('task_updates', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('task_id', 26);
            $table->char('user_id', 26);
            $table->string('old_status', 50)->nullable();
            $table->string('new_status', 50)->nullable();
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->index('task_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_updates');
    }
};
