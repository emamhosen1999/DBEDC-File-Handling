<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Letter;
use App\Models\Task;
use App\Models\Stakeholder;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = trim($request->query('q', ''));
        $limit = min((int)($request->query('limit', 10)), 20);

        if (empty($query) || strlen($query) < 2) {
            return response()->json(['results' => []]);
        }

        $user = auth()->user();
        $scope = $this->getUserScope();
        $results = [];
        $searchTerm = '%' . $query . '%';

        // Search letters
        $letterQuery = Letter::with(['stakeholder'])
            ->where('status', 'ACTIVE')
            ->where(function ($q) use ($searchTerm) {
                $q->where('reference', 'like', $searchTerm)
                  ->orWhere('title', 'like', $searchTerm)
                  ->orWhere('description', 'like', $searchTerm);
            });

        if ($scope === 'department') {
            $letterQuery->where('department_id', $user->department_id);
        } elseif ($scope === 'own') {
            $letterQuery->where(function ($q) use ($user) {
                $q->where('created_by', $user->id)
                  ->orWhereHas('tasks', fn($tq) => $tq->where('assigned_to', $user->id));
            });
        }

        $letters = $letterQuery->limit($limit)->get();
        foreach ($letters as $letter) {
            $results[] = [
                'type' => 'letter',
                'id' => $letter->id,
                'title' => $letter->reference,
                'meta' => "Subject: {$letter->title} | Stakeholder: " . ($letter->stakeholder->name ?? 'Unknown'),
                'date' => $letter->letter_date,
                'priority' => $letter->priority,
            ];
        }

        // Search tasks
        $taskQuery = Task::with(['letter'])
            ->where('status', '!=', 'CANCELLED')
            ->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', $searchTerm)
                  ->orWhere('description', 'like', $searchTerm);
            });

        if ($scope === 'department') {
            $taskQuery->where(function ($q) use ($user) {
                $q->where('department_id', $user->department_id)
                  ->orWhereHas('letter', fn($lq) => $lq->where('department_id', $user->department_id));
            });
        } elseif ($scope === 'own') {
            $taskQuery->where('assigned_to', $user->id);
        }

        $tasks = $taskQuery->limit($limit)->get();
        foreach ($tasks as $task) {
            $results[] = [
                'type' => 'task',
                'id' => $task->id,
                'title' => $task->title,
                'meta' => "Letter: {$task->letter->reference} | Status: {$task->status}",
                'date' => $task->created_at,
                'priority' => $task->priority,
                'assigned_to' => $task->assigned_to,
            ];
        }

        // Search stakeholders
        $stakeholders = Stakeholder::where('is_active', true)
            ->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('code', 'like', $searchTerm)
                  ->orWhere('description', 'like', $searchTerm);
            })
            ->limit($limit)
            ->get();

        foreach ($stakeholders as $stakeholder) {
            $results[] = [
                'type' => 'stakeholder',
                'id' => $stakeholder->id,
                'title' => $stakeholder->name,
                'meta' => "Code: {$stakeholder->code} | {$stakeholder->description}",
                'date' => $stakeholder->created_at,
            ];
        }

        // Search departments (if user has permission)
        if ($this->canViewDepartments()) {
            $departments = Department::where('is_active', true)
                ->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'like', $searchTerm)
                      ->orWhere('description', 'like', $searchTerm);
                })
                ->limit($limit)
                ->get();

            foreach ($departments as $department) {
                $results[] = [
                    'type' => 'department',
                    'id' => $department->id,
                    'title' => $department->name,
                    'meta' => $department->description,
                    'date' => $department->created_at,
                ];
            }
        }

        // Search users (if user has permission)
        if ($this->canViewUsers()) {
            $users = User::where('is_active', true)
                ->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'like', $searchTerm)
                      ->orWhere('email', 'like', $searchTerm);
                })
                ->limit($limit)
                ->get();

            foreach ($users as $userItem) {
                $results[] = [
                    'type' => 'user',
                    'id' => $userItem->id,
                    'title' => $userItem->name,
                    'meta' => "Email: {$userItem->email} | Role: {$userItem->role}",
                    'date' => $userItem->last_login_at,
                ];
            }
        }

        // Sort results by relevance
        $priority = ['letter' => 1, 'task' => 2, 'stakeholder' => 3, 'department' => 4, 'user' => 5];
        usort($results, function ($a, $b) use ($priority) {
            $aPriority = $priority[$a['type']] ?? 6;
            $bPriority = $priority[$b['type']] ?? 6;

            if ($aPriority !== $bPriority) {
                return $aPriority - $bPriority;
            }

            $aDate = $a['date'] ?? '1970-01-01';
            $bDate = $b['date'] ?? '1970-01-01';
            return strtotime($bDate) - strtotime($aDate);
        });

        // Limit total results
        $results = array_slice($results, 0, $limit);

        return response()->json(['results' => $results]);
    }

    protected function getUserScope()
    {
        $user = auth()->user();
        if ($user->role === 'ADMIN') {
            return 'all';
        }
        if ($user->role === 'MANAGER') {
            return 'department';
        }
        return 'own';
    }

    protected function canViewDepartments()
    {
        $user = auth()->user();
        return in_array($user->role, ['ADMIN', 'MANAGER']);
    }

    protected function canViewUsers()
    {
        $user = auth()->user();
        return in_array($user->role, ['ADMIN', 'MANAGER']);
    }
}
