<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Letter;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class LetterController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Letter::query()
            ->with(['department', 'assignedTo', 'stakeholder', 'createdBy']);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('reference', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->status && $request->status !== 'ALL') {
            $query->where('status', $request->status);
        }

        if ($request->priority && $request->priority !== 'ALL') {
            $query->where('priority', $request->priority);
        }

        if ($request->stakeholder && $request->stakeholder !== 'ALL') {
            $query->where('stakeholder_id', $request->stakeholder);
        }

        if ($request->department && $request->department !== 'ALL') {
            $query->where('department_id', $request->department);
        }

        $perPage = $request->per_page ?? 25;
        $letters = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'letters' => $letters->items(),
            'pagination' => [
                'page' => $letters->currentPage(),
                'per_page' => $letters->perPage(),
                'total' => $letters->total(),
                'total_pages' => $letters->lastPage(),
            ],
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $letter = Letter::with(['department', 'assignedTo', 'stakeholder', 'createdBy', 'tasks'])
            ->findOrFail($id);

        return response()->json($letter);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'reference' => 'required|string|max:100|unique:letters,reference',
            'title' => 'required|string|max:500',
            'description' => 'nullable|string|max:2000',
            'sender' => 'nullable|string|max:255',
            'recipient' => 'nullable|string|max:255',
            'subject' => 'nullable|string|max:500',
            'letter_date' => 'required|date',
            'due_date' => 'nullable|date',
            'priority' => 'required|in:LOW,MEDIUM,HIGH,URGENT',
            'status' => 'nullable|in:PENDING,IN_PROGRESS,COMPLETED,CANCELLED',
            'department_id' => 'nullable|exists:departments,id',
            'assigned_to' => 'nullable|exists:users,id',
            'stakeholder_id' => 'required|exists:stakeholders,id',
        ]);

        $letter = Letter::create([
            ...$validated,
            'created_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Letter created successfully',
            'letter_id' => $letter->id,
        ], 201);
    }

    public function bulkStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'letters' => 'required|array',
            'letters.*.reference' => 'required|string|max:100',
            'letters.*.title' => 'required|string|max:500',
            'letters.*.stakeholder_id' => 'required|exists:stakeholders,id',
            'letters.*.letter_date' => 'required|date',
        ]);

        $created = 0;
        $errors = [];

        foreach ($validated['letters'] as $index => $letterData) {
            try {
                Letter::create([
                    ...$letterData,
                    'created_by' => auth()->id(),
                ]);
                $created++;
            } catch (\Exception $e) {
                $errors[] = "Row " . ($index + 1) . ": " . $e->getMessage();
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Bulk import completed",
            'created' => $created,
            'errors' => $errors,
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $letter = Letter::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:500',
            'description' => 'sometimes|nullable|string|max:2000',
            'priority' => 'sometimes|in:LOW,MEDIUM,HIGH,URGENT',
            'status' => 'sometimes|in:PENDING,IN_PROGRESS,COMPLETED,CANCELLED',
            'department_id' => 'sometimes|nullable|exists:departments,id',
            'assigned_to' => 'sometimes|nullable|exists:users,id',
            'stakeholder_id' => 'sometimes|exists:stakeholders,id',
            'due_date' => 'sometimes|nullable|date',
        ]);

        $letter->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Letter updated successfully',
        ]);
    }

    public function bulkUpdate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:letters,id',
            'updates' => 'required|array',
            'updates.status' => 'sometimes|in:PENDING,IN_PROGRESS,COMPLETED,CANCELLED',
            'updates.priority' => 'sometimes|in:LOW,MEDIUM,HIGH,URGENT',
            'updates.department_id' => 'sometimes|nullable|exists:departments,id',
        ]);

        $updated = Letter::whereIn('id', $validated['ids'])->update($validated['updates']);

        return response()->json([
            'success' => true,
            'message' => "Updated $updated letters",
            'updated' => $updated,
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $letter = Letter::findOrFail($id);
        $letter->delete();

        return response()->json([
            'success' => true,
            'message' => 'Letter deleted successfully',
        ]);
    }

    public function bulkDestroy(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:letters,id',
        ]);

        $deleted = Letter::whereIn('id', $validated['ids'])->delete();

        return response()->json([
            'success' => true,
            'message' => "Deleted $deleted letters",
            'deleted' => $deleted,
        ]);
    }

    public function export(string $format): JsonResponse
    {
        // Export functionality to be implemented
        return response()->json(['message' => 'Export not yet implemented'], 501);
    }

    public function calendar(Request $request): JsonResponse
    {
        $month = $request->month ?? date('Y-m');
        $startDate = $month . '-01';
        $endDate = date('Y-m-t', strtotime($startDate));

        $letters = Letter::with('stakeholder')
            ->whereBetween('letter_date', [$startDate, $endDate])
            ->orderBy('letter_date')
            ->get();

        return response()->json([
            'month' => $month,
            'letters' => $letters,
        ]);
    }
}
