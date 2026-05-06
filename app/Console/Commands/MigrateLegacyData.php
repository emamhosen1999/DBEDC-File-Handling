<?php

namespace App\Console\Commands;

use App\Models\Department;
use App\Models\User;
use App\Models\Letter;
use App\Models\Task;
use App\Models\Stakeholder;
use App\Models\Setting;
use App\Models\Notification;
use App\Models\Activity;
use App\Models\TaskUpdate;
use App\Models\UserPreference;
use App\Traits\HasUlid;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

#[Signature('app:migrate-legacy-data')]
#[Description('Migrate data from legacy PHP application to Laravel')]
class MigrateLegacyData extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting legacy data migration...');

        // Configure legacy database connection
        $legacyDb = $this->configureLegacyDatabase();

        if (!$legacyDb) {
            $this->error('Failed to connect to legacy database. Please check your .env file.');
            return Command::FAILURE;
        }

        try {
            // Migrate in order of dependencies
            $this->migrateDepartments($legacyDb);
            $this->migrateStakeholders($legacyDb);
            $this->migrateUsers($legacyDb);
            $this->migrateLetters($legacyDb);
            $this->migrateTasks($legacyDb);
            $this->migrateTaskUpdates($legacyDb);
            $this->migrateSettings($legacyDb);
            $this->migrateNotifications($legacyDb);
            $this->migrateActivities($legacyDb);
            $this->migrateUserPreferences($legacyDb);

            $this->info('Legacy data migration completed successfully!');
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Migration failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Configure legacy database connection
     */
    protected function configureLegacyDatabase()
    {
        $legacyHost = env('LEGACY_DB_HOST', 'localhost');
        $legacyPort = env('LEGACY_DB_PORT', 3306);
        $legacyDbName = env('LEGACY_DB_NAME', 'file_tracker');
        $legacyUser = env('LEGACY_DB_USER', 'root');
        $legacyPass = env('LEGACY_DB_PASSWORD', '');

        try {
            $pdo = new \PDO(
                "mysql:host={$legacyHost};port={$legacyPort};dbname={$legacyDbName}",
                $legacyUser,
                $legacyPass,
                [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
            );
            $this->info('Connected to legacy database successfully.');
            return $pdo;
        } catch (\PDOException $e) {
            $this->error('Legacy database connection failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Migrate departments
     */
    protected function migrateDepartments($legacyDb)
    {
        $this->info('Migrating departments...');

        $stmt = $legacyDb->query("SELECT * FROM departments WHERE deleted_at IS NULL");
        $departments = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($departments as $dept) {
            Department::updateOrCreate(
                ['id' => $dept['id']],
                [
                    'name' => $dept['name'],
                    'description' => $dept['description'] ?? null,
                    'parent_id' => $dept['parent_id'] ?? null,
                    'manager_id' => $dept['manager_id'] ?? null,
                    'display_order' => $dept['display_order'] ?? 0,
                    'is_active' => ($dept['is_active'] ?? 1) == 1,
                    'created_at' => $dept['created_at'] ?? now(),
                    'updated_at' => $dept['updated_at'] ?? now(),
                ]
            );
        }

        $this->info("Migrated " . count($departments) . " departments.");
    }

    /**
     * Migrate stakeholders
     */
    protected function migrateStakeholders($legacyDb)
    {
        $this->info('Migrating stakeholders...');

        $stmt = $legacyDb->query("SELECT * FROM stakeholders WHERE deleted_at IS NULL");
        $stakeholders = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($stakeholders as $stakeholder) {
            Stakeholder::updateOrCreate(
                ['id' => $stakeholder['id']],
                [
                    'name' => $stakeholder['name'],
                    'code' => $stakeholder['code'],
                    'email' => $stakeholder['email'] ?? null,
                    'phone' => $stakeholder['phone'] ?? null,
                    'address' => $stakeholder['address'] ?? null,
                    'contact_person' => $stakeholder['contact_person'] ?? null,
                    'color' => $stakeholder['color'] ?? null,
                    'description' => $stakeholder['description'] ?? null,
                    'is_active' => ($stakeholder['is_active'] ?? 1) == 1,
                    'created_at' => $stakeholder['created_at'] ?? now(),
                    'updated_at' => $stakeholder['updated_at'] ?? now(),
                ]
            );
        }

        $this->info("Migrated " . count($stakeholders) . " stakeholders.");
    }

    /**
     * Migrate users
     */
    protected function migrateUsers($legacyDb)
    {
        $this->info('Migrating users...');

        $stmt = $legacyDb->query("SELECT * FROM users WHERE deleted_at IS NULL");
        $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($users as $user) {
            User::updateOrCreate(
                ['id' => $user['id']],
                [
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'password' => $user['password'] ?? Hash::make('password'),
                    'department_id' => $user['department_id'] ?? null,
                    'role' => strtoupper($user['role'] ?? 'USER'),
                    'phone' => $user['phone'] ?? null,
                    'avatar_url' => $user['avatar_url'] ?? null,
                    'is_active' => ($user['is_active'] ?? 1) == 1,
                    'email_verified_at' => $user['email_verified_at'] ?? null,
                    'created_at' => $user['created_at'] ?? now(),
                    'updated_at' => $user['updated_at'] ?? now(),
                ]
            );
        }

        $this->info("Migrated " . count($users) . " users.");
    }

    /**
     * Migrate letters
     */
    protected function migrateLetters($legacyDb)
    {
        $this->info('Migrating letters...');

        $stmt = $legacyDb->query("SELECT * FROM letters WHERE deleted_at IS NULL");
        $letters = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($letters as $letter) {
            Letter::updateOrCreate(
                ['id' => $letter['id']],
                [
                    'reference' => $letter['reference_no'] ?? $letter['reference'],
                    'title' => $letter['subject'] ?? $letter['title'],
                    'description' => $letter['description'] ?? null,
                    'sender' => $letter['sender'] ?? null,
                    'recipient' => $letter['recipient'] ?? null,
                    'subject' => $letter['subject'] ?? null,
                    'letter_date' => $letter['received_date'] ?? $letter['letter_date'] ?? null,
                    'due_date' => $letter['due_date'] ?? null,
                    'priority' => strtoupper($letter['priority'] ?? 'MEDIUM'),
                    'status' => strtoupper($letter['status'] ?? 'PENDING'),
                    'department_id' => $letter['department_id'] ?? null,
                    'assigned_to' => $letter['assigned_to'] ?? null,
                    'stakeholder_id' => $letter['stakeholder_id'] ?? null,
                    'created_by' => $letter['uploaded_by'] ?? $letter['created_by'] ?? null,
                    'created_at' => $letter['created_at'] ?? now(),
                    'updated_at' => $letter['updated_at'] ?? now(),
                ]
            );
        }

        $this->info("Migrated " . count($letters) . " letters.");
    }

    /**
     * Migrate tasks
     */
    protected function migrateTasks($legacyDb)
    {
        $this->info('Migrating tasks...');

        $stmt = $legacyDb->query("SELECT * FROM tasks WHERE deleted_at IS NULL");
        $tasks = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($tasks as $task) {
            Task::updateOrCreate(
                ['id' => $task['id']],
                [
                    'letter_id' => $task['letter_id'] ?? null,
                    'title' => $task['title'],
                    'description' => $task['description'] ?? null,
                    'assigned_to' => $task['assigned_to'] ?? null,
                    'department_id' => $task['assigned_department'] ?? $task['department_id'] ?? null,
                    'status' => strtoupper($task['status'] ?? 'PENDING'),
                    'priority' => strtoupper($task['priority'] ?? 'MEDIUM'),
                    'due_date' => $task['due_date'] ?? null,
                    'completed_at' => $task['completed_at'] ?? null,
                    'created_by' => $task['created_by'] ?? null,
                    'created_at' => $task['created_at'] ?? now(),
                    'updated_at' => $task['updated_at'] ?? now(),
                ]
            );
        }

        $this->info("Migrated " . count($tasks) . " tasks.");
    }

    /**
     * Migrate task updates
     */
    protected function migrateTaskUpdates($legacyDb)
    {
        $this->info('Migrating task updates...');

        $stmt = $legacyDb->query("SELECT * FROM task_updates");
        $updates = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($updates as $update) {
            TaskUpdate::updateOrCreate(
                ['id' => $update['id']],
                [
                    'task_id' => $update['task_id'],
                    'user_id' => $update['user_id'] ?? null,
                    'old_status' => $update['old_status'] ?? null,
                    'new_status' => $update['new_status'] ?? null,
                    'comment' => $update['comment'] ?? null,
                    'created_at' => $update['created_at'] ?? now(),
                ]
            );
        }

        $this->info("Migrated " . count($updates) . " task updates.");
    }

    /**
     * Migrate settings
     */
    protected function migrateSettings($legacyDb)
    {
        $this->info('Migrating settings...');

        $stmt = $legacyDb->query("SELECT * FROM settings");
        $settings = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['setting_key' => $setting['setting_key']],
                [
                    'value' => $setting['setting_value'],
                    'description' => $setting['description'] ?? null,
                    'type' => $setting['data_type'] ?? 'string',
                    'created_at' => $setting['created_at'] ?? now(),
                    'updated_at' => $setting['updated_at'] ?? now(),
                ]
            );
        }

        $this->info("Migrated " . count($settings) . " settings.");
    }

    /**
     * Migrate notifications
     */
    protected function migrateNotifications($legacyDb)
    {
        $this->info('Migrating notifications...');

        $stmt = $legacyDb->query("SELECT * FROM notifications");
        $notifications = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($notifications as $notification) {
            Notification::updateOrCreate(
                ['id' => $notification['id']],
                [
                    'user_id' => $notification['user_id'],
                    'title' => $notification['title'],
                    'message' => $notification['message'],
                    'type' => $notification['type'] ?? 'info',
                    'is_read' => ($notification['is_read'] ?? 0) == 1,
                    'link' => $notification['link'] ?? null,
                    'created_at' => $notification['created_at'] ?? now(),
                    'updated_at' => $notification['updated_at'] ?? now(),
                ]
            );
        }

        $this->info("Migrated " . count($notifications) . " notifications.");
    }

    /**
     * Migrate activities
     */
    protected function migrateActivities($legacyDb)
    {
        $this->info('Migrating activities...');

        $stmt = $legacyDb->query("SELECT * FROM activities");
        $activities = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($activities as $activity) {
            Activity::updateOrCreate(
                ['id' => $activity['id']],
                [
                    'user_id' => $activity['user_id'],
                    'action' => $activity['action'],
                    'entity_type' => $activity['entity_type'] ?? null,
                    'entity_id' => $activity['entity_id'] ?? null,
                    'description' => $activity['description'] ?? null,
                    'metadata' => $activity['metadata'] ?? null,
                    'created_at' => $activity['created_at'] ?? now(),
                ]
            );
        }

        $this->info("Migrated " . count($activities) . " activities.");
    }

    /**
     * Migrate user preferences
     */
    protected function migrateUserPreferences($legacyDb)
    {
        $this->info('Migrating user preferences...');

        $stmt = $legacyDb->query("SELECT * FROM user_preferences");
        $preferences = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($preferences as $pref) {
            UserPreference::updateOrCreate(
                ['id' => $pref['id']],
                [
                    'user_id' => $pref['user_id'],
                    'preference_key' => $pref['preference_key'],
                    'preference_value' => $pref['preference_value'],
                    'created_at' => $pref['created_at'] ?? now(),
                    'updated_at' => $pref['updated_at'] ?? now(),
                ]
            );
        }

        $this->info("Migrated " . count($preferences) . " user preferences.");
    }
}
