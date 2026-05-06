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
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('user_id', 26)->unique();
            $table->json('quick_actions')->nullable()->comment('Customizable quick action shortcuts');
            $table->json('dashboard_layout')->nullable()->comment('Dashboard widget preferences');
            $table->string('theme_preference', 20)->default('system');
            $table->string('default_view', 50)->default('my-tasks');
            $table->integer('items_per_page')->default(25);
            $table->timestamps();

            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_preferences');
    }
};
