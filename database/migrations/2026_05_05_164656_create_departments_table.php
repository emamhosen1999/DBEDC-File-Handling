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
        Schema::create('departments', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->char('parent_id', 26)->nullable()->comment('For hierarchical structure');
            $table->char('manager_id', 26)->nullable()->comment('Department manager/head');
            $table->boolean('is_active')->default(true);
            $table->integer('display_order')->default(0);
            $table->timestamps();

            $table->index('parent_id');
            $table->index('manager_id');
            $table->index('is_active');
            $table->index('name');
            $table->index('display_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
