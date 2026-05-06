<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Task::query()
            ->with(['letter', 'assignedTo', 'department', 'createdBy']);

        $view = $request->view ?? 'my';
        if ($view === 'my') {
            $query->where(function ($q) {
                $q->where('assigned_to', auth()->id())
                  ->orWhere('department_id', auth()->user()->department_id);
            });
        }

        if ($request->status && $request->status !== 'ALL') {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhereHas('letter', function ($letterQ) use ($request) {
                      $letterQ->where('reference', 'like', '%' . $request->search . '%')
                             ->orWhere('title', 'like', '%' . $request->search . '%');
                  });
            });
        }

        if ($request->assigned_to) {
            $query->where('assigned_to', $request->assigned_to);
        }

        $perPage = $request->per_page ?? 20;
        $tasks = $query->orderByRaw("FIELD(status, 'PENDING', 'IN_PROGRESS', 'COMPLETED')")
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'tasks' => $tasks->items(),
            'pagination' => [
                'page' => $tasks->currentPage(),
                'per_page' => $tasks->perPage(),
                'total' => $tasks->total(),
                'total_pages' => $tasks->lastPage(),
            ],
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $task = Task::with(['letter', 'assignedTo', 'department', 'createdBy', 'updates'])
            ->findOrFail($id);

        return response()->json($task);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'letter_id' => 'required|exists:letters,id',
            'title' => 'required|string|max:500',
            'description' => 'nullable|string|max:2000',
            'assigned_to' => 'nullable|exists:users,id',
            'department_id' => 'nullable|exists:departments,id',
            'status' => 'nullable|in:PENDING,IN_PROGRESS,COMPLETED,CANCELLED',
            'priority' => 'nullable|in:LOW,MEDIUM,HIGH,URGENT',
            'due_date' => 'nullable|date',
        ]);

        $task = Task::create([
            ...$validated,
            'created_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Task created successfully',
            'task_id' => $task->id,
        ], 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $task = Task::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:500',
            'description' => 'sometimes|nullable|string|max:2000',
            'assigned_to' => 'sometimes|nullable|exists:users,id',
            'department_id' => 'sometimes|nullable|exists:departments,id',
            'status' => 'sometimes|in:PENDING,IN_PROGRESS,COMPLETED,CANCELLED',
            'priority' => 'sometimes|in:LOW,MEDIUM,HIGH,URGENT',
            'due_date' => 'sometimes|nullable|date',
        ]);

        $task->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Task updated successfully',
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $task = Task::findOrFail($id);
        $task->delete();

        return response()->json([
            'success' => true,
            'message' => 'Task deleted successfully',
        ]);
    }
}
