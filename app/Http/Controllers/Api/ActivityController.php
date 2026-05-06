<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ActivityController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Activity::query()->with('user');

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->entity_type) {
            $query->where('entity_type', $request->entity_type);
        }

        if ($request->entity_id) {
            $query->where('entity_id', $request->entity_id);
        }

        if ($request->action) {
            $query->where('action', $request->action);
        }

        $perPage = $request->per_page ?? 50;
        $activities = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'activities' => $activities->items(),
            'pagination' => [
                'page' => $activities->currentPage(),
                'per_page' => $activities->perPage(),
                'total' => $activities->total(),
                'total_pages' => $activities->lastPage(),
            ],
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $activity = Activity::with('user')->findOrFail($id);

        return response()->json($activity);
    }
}
