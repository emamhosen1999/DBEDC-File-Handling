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
        // Users table foreign keys
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
        });

        // Departments table foreign keys
        Schema::table('departments', function (Blueprint $table) {
            $table->foreign('parent_id')->references('id')->on('departments')->onDelete('set null');
            $table->foreign('manager_id')->references('id')->on('users')->onDelete('set null');
        });

        // Letters table foreign keys
        Schema::table('letters', function (Blueprint $table) {
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
            $table->foreign('stakeholder_id')->references('id')->on('stakeholders')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        // Tasks table foreign keys
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreign('letter_id')->references('id')->on('letters')->onDelete('set null');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        // Task updates table foreign keys
        Schema::table('task_updates', function (Blueprint $table) {
            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Notifications table foreign keys
        Schema::table('notifications', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Activities table foreign keys
        Schema::table('activities', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // User preferences table foreign keys
        Schema::table('user_preferences', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Push subscriptions table foreign keys
        Schema::table('push_subscriptions', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('push_subscriptions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('user_preferences', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('activities', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('task_updates', function (Blueprint $table) {
            $table->dropForeign(['task_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['letter_id']);
            $table->dropForeign(['assigned_to']);
            $table->dropForeign(['department_id']);
            $table->dropForeign(['created_by']);
        });

        Schema::table('letters', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropForeign(['assigned_to']);
            $table->dropForeign(['stakeholder_id']);
            $table->dropForeign(['created_by']);
        });

        Schema::table('departments', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropForeign(['manager_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
        });
    }
};
