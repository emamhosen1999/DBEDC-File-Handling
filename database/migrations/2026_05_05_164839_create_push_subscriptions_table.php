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
        Schema::create('push_subscriptions', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('user_id', 26);
            $table->string('endpoint', 500);
            $table->string('public_key', 255);
            $table->string('auth_token', 255);
            $table->timestamp('created_at')->useCurrent();

            $table->index('user_id');
            $table->index('endpoint');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('push_subscriptions');
    }
};
