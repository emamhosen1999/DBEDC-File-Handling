<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DepartmentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Department::query()->with(['manager', 'parent']);

        if (!$request->all) {
            $query->where('is_active', true);
        }

        $departments = $query->orderBy('display_order')->orderBy('name')->get();

        return response()->json([
            'departments' => $departments,
            'total' => $departments->count(),
        ]);
    }

    public function tree(): JsonResponse
    {
        $departments = Department::with(['manager', 'users'])
            ->where('is_active', true)
            ->whereNull('parent_id')
            ->orderBy('display_order')
            ->orderBy('name')
            ->get()
            ->map(function ($dept) {
                $dept->children = $this->getChildren($dept->id);
                return $dept;
            });

        return response()->json(['tree' => $departments]);
    }

    private function getChildren($parentId)
    {
        return Department::with(['manager', 'users'])
            ->where('is_active', true)
            ->where('parent_id', $parentId)
            ->orderBy('display_order')
            ->orderBy('name')
            ->get()
            ->map(function ($dept) {
                $dept->children = $this->getChildren($dept->id);
                return $dept;
            });
    }

    public function stats(): JsonResponse
    {
        $departments = Department::withCount(['users', 'letters', 'tasks'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return response()->json(['departments' => $departments]);
    }

    public function show(string $id): JsonResponse
    {
        $department = Department::with(['manager', 'parent', 'users', 'children'])
            ->withCount(['users', 'letters', 'tasks'])
            ->findOrFail($id);

        return response()->json($department);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:departments,id',
            'manager_id' => 'nullable|exists:users,id',
            'display_order' => 'nullable|integer',
        ]);

        $department = Department::create($validated);

        return response()->json([
            'success' => true,
            'id' => $department->id,
            'message' => 'Department created successfully',
        ], 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $department = Department::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|nullable|string',
            'parent_id' => 'sometimes|nullable|exists:departments,id',
            'manager_id' => 'sometimes|nullable|exists:users,id',
            'is_active' => 'sometimes|boolean',
            'display_order' => 'sometimes|integer',
        ]);

        $department->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Department updated successfully',
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $department = Department::findOrFail($id);

        if ($department->users()->where('is_active', true)->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete department with active users',
            ], 400);
        }

        if ($department->children()->where('is_active', true)->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete department with child departments',
            ], 400);
        }

        $department->update(['is_active' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Department deleted successfully',
        ]);
    }
}
