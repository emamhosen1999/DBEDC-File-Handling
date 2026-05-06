<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Models\Letter;
use App\Models\Notification;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

#[Signature('app:send-deadline-reminders')]
#[Description('Send deadline reminders for tasks and letters due soon')]
class SendDeadlineReminders extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sent = 0;

        // Send reminders for tasks due in 3 days
        $tasksDueSoon = Task::where('status', '!=', 'COMPLETED')
            ->where('status', '!=', 'CANCELLED')
            ->where('due_date', '>=', now())
            ->where('due_date', '<=', now()->addDays(3))
            ->whereDoesntHave('notifications', function ($query) {
                $query->where('type', 'deadline_reminder')
                      ->where('created_at', '>=', now()->subDay());
            })
            ->with('assignedTo')
            ->get();

        foreach ($tasksDueSoon as $task) {
            if ($task->assignedTo) {
                Notification::create([
                    'user_id' => $task->assigned_to,
                    'title' => 'Task Deadline Reminder',
                    'message' => "Task '{$task->title}' is due on {$task->due_date->format('M d, Y')}",
                    'type' => 'deadline_reminder',
                    'link' => route('tasks.index'),
                ]);
                $sent++;
            }
        }

        // Send reminders for letters due in 3 days
        $lettersDueSoon = Letter::where('status', '!=', 'COMPLETED')
            ->where('status', '!=', 'CANCELLED')
            ->where('due_date', '>=', now())
            ->where('due_date', '<=', now()->addDays(3))
            ->whereDoesntHave('notifications', function ($query) {
                $query->where('type', 'deadline_reminder')
                      ->where('created_at', '>=', now()->subDay());
            })
            ->with('assignedTo')
            ->get();

        foreach ($lettersDueSoon as $letter) {
            if ($letter->assignedTo && $letter->due_date) {
                Notification::create([
                    'user_id' => $letter->assigned_to,
                    'title' => 'Letter Deadline Reminder',
                    'message' => "Letter '{$letter->title}' is due on {$letter->due_date->format('M d, Y')}",
                    'type' => 'deadline_reminder',
                    'link' => route('letters.index'),
                ]);
                $sent++;
            }
        }

        $this->info("Sent {$sent} deadline reminders");
        return Command::SUCCESS;
    }
}
