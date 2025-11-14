<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Get all notifications for the authenticated user
     */
    public function index()
    {
        $notifications = auth()->user()
            ->notifications()
            ->latest()
            ->paginate(20);
        
        return view('notifications.index', compact('notifications'));
    }

    /**
     * Get unread notifications (for dropdown/AJAX)
     */
    public function unread()
    {
        $notifications = auth()->user()
            ->unreadNotifications()
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'data' => $notification->data,
                    'created_at' => $notification->created_at->diffForHumans(),
                    'read_at' => $notification->read_at,
                ];
            });
        
        return response()->json([
            'notifications' => $notifications,
            'unread_count' => auth()->user()->unreadNotifications()->count()
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $notification = auth()->user()
            ->notifications()
            ->where('id', $id)
            ->first();
        
        if ($notification) {
            $notification->markAsRead();
            
            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read',
                'unread_count' => auth()->user()->unreadNotifications()->count()
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Notification not found'
        ], 404);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        
        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read',
            'unread_count' => 0
        ]);
    }

    /**
     * Delete notification
     */
    public function destroy($id)
    {
        $notification = auth()->user()
            ->notifications()
            ->where('id', $id)
            ->first();
        
        if ($notification) {
            $notification->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Notification deleted',
                'unread_count' => auth()->user()->unreadNotifications()->count()
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Notification not found'
        ], 404);
    }
}






