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
        Schema::create('stakeholders', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->string('name', 100)->unique();
            $table->string('code', 20)->unique()->comment('Short code (e.g., IE, JV, RHD)');
            $table->string('color', 7)->default('#6B7280')->comment('Hex color for badges');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('code');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stakeholders');
    }
};
