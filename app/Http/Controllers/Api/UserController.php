<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = User::query()->with('department');

        if (!$request->all) {
            $query->where('is_active', true);
        }

        if ($request->department_id) {
            $query->where('department_id', $request->department_id);
        }

        $users = $query->orderBy('name')->get();

        return response()->json([
            'users' => $users,
            'total' => $users->count(),
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $user = User::with(['department', 'preferences'])->findOrFail($id);

        return response()->json($user);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'department_id' => 'nullable|exists:departments,id',
            'role' => 'required|in:USER,MANAGER,ADMIN',
            'phone' => 'nullable|string|max:20',
            'avatar_url' => 'nullable|url',
        ]);

        $user = User::create([
            ...$validated,
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'success' => true,
            'id' => $user->id,
            'message' => 'User created successfully',
        ], 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'password' => 'sometimes|string|min:8',
            'department_id' => 'sometimes|nullable|exists:departments,id',
            'role' => 'sometimes|in:USER,MANAGER,ADMIN',
            'phone' => 'sometimes|nullable|string|max:20',
            'avatar_url' => 'sometimes|nullable|url',
            'is_active' => 'sometimes|boolean',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->update(['is_active' => false]);

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully',
        ]);
    }
}
