<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Stakeholder;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class StakeholderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Stakeholder::query();

        if (!$request->all) {
            $query->where('is_active', true);
        }

        $stakeholders = $query->orderBy('name')->get();

        return response()->json([
            'stakeholders' => $stakeholders,
            'total' => $stakeholders->count(),
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $stakeholder = Stakeholder::with('letters')->findOrFail($id);

        return response()->json($stakeholder);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:stakeholders,code',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:7',
            'description' => 'nullable|string',
        ]);

        $stakeholder = Stakeholder::create($validated);

        return response()->json([
            'success' => true,
            'id' => $stakeholder->id,
            'message' => 'Stakeholder created successfully',
        ], 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $stakeholder = Stakeholder::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'code' => 'sometimes|string|max:50|unique:stakeholders,code,' . $id,
            'email' => 'sometimes|nullable|email',
            'phone' => 'sometimes|nullable|string|max:20',
            'address' => 'sometimes|nullable|string',
            'contact_person' => 'sometimes|nullable|string|max:255',
            'color' => 'sometimes|nullable|string|max:7',
            'description' => 'sometimes|nullable|string',
            'is_active' => 'sometimes|boolean',
        ]);

        $stakeholder->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Stakeholder updated successfully',
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $stakeholder = Stakeholder::findOrFail($id);
        $stakeholder->update(['is_active' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Stakeholder deleted successfully',
        ]);
    }
}
