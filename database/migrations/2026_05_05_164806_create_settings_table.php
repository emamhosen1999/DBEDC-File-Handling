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
        Schema::create('settings', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->string('setting_key', 100)->unique();
            $table->text('setting_value')->nullable();
            $table->enum('setting_group', ['branding', 'email', 'workflow', 'system', 'auth']);
            $table->enum('data_type', ['string', 'integer', 'boolean', 'float', 'json'])->default('string');
            $table->boolean('is_public')->default(false);
            $table->text('description')->nullable();
            $table->timestamp('updated_at')->useCurrent()->onUpdate('CURRENT_TIMESTAMP');

            $table->index('setting_group');
            $table->index('is_public');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
