<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Notification::query()->where('user_id', auth()->id());

        if ($request->unread_only) {
            $query->where('is_read', false);
        }

        $notifications = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => Notification::where('user_id', auth()->id())->where('is_read', false)->count(),
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return response()->json($notification);
    }

    public function markAsRead(string $id): JsonResponse
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $notification->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
        ]);
    }

    public function markAllAsRead(): JsonResponse
    {
        Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read',
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted successfully',
        ]);
    }
}
