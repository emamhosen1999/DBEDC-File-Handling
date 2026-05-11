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
        Schema::create('users', function (Blueprint $table) {
            $table->char('id', 26)->primary()->comment('ULID');
            $table->string('google_id', 100)->nullable()->unique();
            $table->string('email', 255)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('name', 255);
            $table->char('department_id', 26)->nullable();
            $table->enum('role', ['ADMIN', 'MANAGER', 'MEMBER', 'VIEWER'])->default('MEMBER');
            $table->text('avatar_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('email_notifications')->default(true);
            $table->boolean('push_notifications')->default(true);
            $table->string('password')->nullable();
            $table->string('wechat_openid', 100)->nullable();
            $table->string('wechat_unionid', 100)->nullable();
            $table->string('provider', 50)->default('email');
            $table->timestamp('last_login')->nullable();
            $table->timestamps();

            $table->index('email');
            $table->index('google_id');
            $table->index('department_id');
            $table->index('role');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
