<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Letter;
use App\Models\Task;
use App\Models\User;
use App\Models\Department;
use App\Models\Stakeholder;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $reportType = $request->query('type', 'overview');
        $export = $request->query('export');
        $dateFrom = $request->query('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->query('date_to', now()->format('Y-m-d'));

        $data = match ($reportType) {
            'overview' => $this->generateOverviewReport($dateFrom, $dateTo),
            'letters' => $this->generateLettersReport($dateFrom, $dateTo),
            'tasks' => $this->generateTasksReport($dateFrom, $dateTo),
            'users' => $this->generateUsersReport($dateFrom, $dateTo),
            'departments' => $this->generateDepartmentsReport($dateFrom, $dateTo),
            'stakeholders' => $this->generateStakeholdersReport($dateFrom, $dateTo),
            default => response()->json(['error' => 'Unknown report type'], 400),
        };

        if ($export) {
            return $this->exportReport($data, $reportType, $export);
        }

        return response()->json($data);
    }

    protected function generateOverviewReport($dateFrom, $dateTo)
    {
        $user = auth()->user();
        $scope = $this->getUserScope();

        $query = Letter::with(['tasks'])
            ->whereBetween('created_at', [$dateFrom, now()->parse($dateTo)->endOfDay()])
            ->where('status', '!=', 'DELETED');

        if ($scope === 'department') {
            $query->where('department_id', $user->department_id);
        } elseif ($scope === 'own') {
            $query->where('created_by', $user->id);
        }

        $letters = $query->get();

        $summary = [
            'total_letters' => $letters->count(),
            'total_tasks' => $letters->sum(fn($l) => $l->tasks->count()),
            'completed_tasks' => $letters->sum(fn($l) => $l->tasks->where('status', 'COMPLETED')->count()),
            'pending_tasks' => $letters->sum(fn($l) => $l->tasks->where('status', 'PENDING')->count()),
            'in_progress_tasks' => $letters->sum(fn($l) => $l->tasks->where('status', 'IN_PROGRESS')->count()),
            'overdue_tasks' => $letters->sum(fn($l) => $l->tasks->where('due_date', '<', now())->whereNotIn('status', ['COMPLETED', 'CANCELLED'])->count()),
        ];

        $total = $summary['total_tasks'] ?? 0;
        $completed = $summary['completed_tasks'] ?? 0;
        $summary['completion_rate'] = $total > 0 ? round(($completed / $total) * 100, 1) : 0;

        return [
            'period' => ['from' => $dateFrom, 'to' => $dateTo],
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'summary' => $summary,
        ];
    }

    protected function generateLettersReport($dateFrom, $dateTo)
    {
        $user = auth()->user();
        $scope = $this->getUserScope();

        $query = Letter::with(['stakeholder', 'department', 'createdBy'])
            ->whereBetween('created_at', [$dateFrom, now()->parse($dateTo)->endOfDay()])
            ->where('status', '!=', 'DELETED');

        if ($scope === 'department') {
            $query->where('department_id', $user->department_id);
        } elseif ($scope === 'own') {
            $query->where('created_by', $user->id);
        }

        $letters = $query->get();

        return [
            'period' => ['from' => $dateFrom, 'to' => $dateTo],
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'by_stakeholder' => $letters->groupBy('stakeholder_id')->map(fn($group) => [
                'stakeholder' => $group->first()->stakeholder->name ?? 'Unknown',
                'code' => $group->first()->stakeholder->code ?? '',
                'color' => $group->first()->stakeholder->color ?? null,
                'count' => $group->count(),
            ])->values(),
            'by_priority' => $letters->groupBy('priority')->map(fn($group) => [
                'priority' => $group->first()->priority,
                'count' => $group->count(),
            ])->sortByDesc('count')->values(),
            'by_status' => $letters->groupBy('status')->map(fn($group) => [
                'status' => $group->first()->status,
                'count' => $group->count(),
            ])->values(),
            'letters' => $letters->map(fn($l) => [
                'id' => $l->id,
                'reference' => $l->reference,
                'title' => $l->title,
                'letter_date' => $l->letter_date,
                'priority' => $l->priority,
                'status' => $l->status,
                'created_at' => $l->created_at,
                'stakeholder' => $l->stakeholder->name ?? null,
                'department' => $l->department->name ?? null,
                'uploaded_by' => $l->createdBy->name ?? null,
                'task_count' => $l->tasks->count(),
            ]),
        ];
    }

    protected function generateTasksReport($dateFrom, $dateTo)
    {
        $user = auth()->user();
        $scope = $this->getUserScope();

        $query = Task::with(['letter', 'assignedTo', 'department'])
            ->whereBetween('created_at', [$dateFrom, now()->parse($dateTo)->endOfDay()]);

        if ($scope === 'department') {
            $query->where('department_id', $user->department_id);
        } elseif ($scope === 'own') {
            $query->where('assigned_to', $user->id);
        }

        $tasks = $query->get();

        return [
            'period' => ['from' => $dateFrom, 'to' => $dateTo],
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'by_status' => $tasks->groupBy('status')->map(fn($group) => [
                'status' => $group->first()->status,
                'count' => $group->count(),
            ])->values(),
            'by_priority' => $tasks->groupBy('priority')->map(fn($group) => [
                'priority' => $group->first()->priority,
                'count' => $group->count(),
            ])->sortByDesc('count')->values(),
            'tasks' => $tasks->map(fn($t) => [
                'id' => $t->id,
                'title' => $t->title,
                'status' => $t->status,
                'priority' => $t->priority,
                'due_date' => $t->due_date,
                'created_at' => $t->created_at,
                'completed_at' => $t->completed_at,
                'letter_reference' => $t->letter->reference ?? null,
                'assigned_to' => $t->assignedTo->name ?? null,
                'department' => $t->department->name ?? null,
            ]),
        ];
    }

    protected function generateUsersReport($dateFrom, $dateTo)
    {
        if (!$this->isAdmin()) {
            return response()->json(['error' => 'Insufficient permissions'], 403);
        }

        $users = User::with(['department'])
            ->where('is_active', true)
            ->withCount(['activities' => fn($q) => $q->whereBetween('created_at', [$dateFrom, now()->parse($dateTo)->endOfDay()])])
            ->withCount(['tasksAssigned' => fn($q) => $q->whereBetween('created_at', [$dateFrom, now()->parse($dateTo)->endOfDay()])])
            ->get();

        return [
            'period' => ['from' => $dateFrom, 'to' => $dateTo],
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'by_role' => $users->groupBy('role')->map(fn($group) => [
                'role' => $group->first()->role,
                'count' => $group->count(),
            ])->values(),
            'by_department' => $users->groupBy('department_id')->map(fn($group) => [
                'department' => $group->first()->department->name ?? 'No Department',
                'count' => $group->count(),
            ])->sortByDesc('count')->values(),
            'users' => $users->map(fn($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'role' => $u->role,
                'department' => $u->department->name ?? null,
                'last_login' => $u->last_login_at,
                'activity_count' => $u->activities_count,
                'tasks_assigned' => $u->tasks_assigned_count,
            ]),
        ];
    }

    protected function generateDepartmentsReport($dateFrom, $dateTo)
    {
        $user = auth()->user();
        $scope = $this->getUserScope();

        $query = Department::with(['manager', 'users', 'letters', 'tasks'])
            ->where('is_active', true);

        if ($scope === 'department') {
            $query->where('id', $user->department_id);
        }

        $departments = $query->get();

        return [
            'period' => ['from' => $dateFrom, 'to' => $dateTo],
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'departments' => $departments->map(fn($d) => [
                'id' => $d->id,
                'name' => $d->name,
                'description' => $d->description,
                'manager_name' => $d->manager->name ?? null,
                'user_count' => $d->users->where('is_active', true)->count(),
                'letter_count' => $d->letters->whereBetween('created_at', [$dateFrom, now()->parse($dateTo)->endOfDay()])->count(),
                'task_count' => $d->tasks->whereBetween('created_at', [$dateFrom, now()->parse($dateTo)->endOfDay()])->count(),
                'completed_tasks' => $d->tasks->whereBetween('created_at', [$dateFrom, now()->parse($dateTo)->endOfDay()])->where('status', 'COMPLETED')->count(),
                'pending_tasks' => $d->tasks->whereBetween('created_at', [$dateFrom, now()->parse($dateTo)->endOfDay()])->where('status', 'PENDING')->count(),
            ]),
        ];
    }

    protected function generateStakeholdersReport($dateFrom, $dateTo)
    {
        $user = auth()->user();
        $scope = $this->getUserScope();

        $query = Stakeholder::with(['letters' => fn($q) => $q->whereBetween('created_at', [$dateFrom, now()->parse($dateTo)->endOfDay()])])
            ->where('is_active', true);

        if ($scope === 'department') {
            $query->whereHas('letters', fn($q) => $q->where('department_id', $user->department_id));
        } elseif ($scope === 'own') {
            $query->whereHas('letters', fn($q) => $q->where('created_by', $user->id));
        }

        $stakeholders = $query->get();

        return [
            'period' => ['from' => $dateFrom, 'to' => $dateTo],
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'stakeholders' => $stakeholders->map(fn($s) => [
                'id' => $s->id,
                'name' => $s->name,
                'code' => $s->code,
                'color' => $s->color,
                'letter_count' => $s->letters->count(),
                'task_count' => $s->letters->sum(fn($l) => $l->tasks->count()),
            ]),
        ];
    }

    protected function exportReport($data, $reportType, $format)
    {
        if ($format === 'json') {
            return response()->json($data)
                ->header('Content-Disposition', "attachment; filename=\"report_{$reportType}_" . now()->format('Y-m-d') . '.json"');
        }

        if ($format === 'csv') {
            $rows = match ($reportType) {
                'letters' => $data['letters'] ?? [],
                'tasks' => $data['tasks'] ?? [],
                'users' => $data['users'] ?? [],
                'departments' => $data['departments'] ?? [],
                'stakeholders' => $data['stakeholders'] ?? [],
                default => [],
            };

            $csv = fopen('php://temp', 'r+');
            if (!empty($rows)) {
                fputcsv($csv, array_keys((array)$rows->first()));
                foreach ($rows as $row) {
                    fputcsv($csv, (array)$row);
                }
            }
            rewind($csv);
            $content = stream_get_contents($csv);
            fclose($csv);

            return response($content)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', "attachment; filename=\"report_{$reportType}_" . now()->format('Y-m-d') . '.csv"');
        }

        return response()->json(['error' => 'Unknown export format'], 400);
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

    protected function isAdmin()
    {
        return auth()->user()->role === 'ADMIN';
    }
}
