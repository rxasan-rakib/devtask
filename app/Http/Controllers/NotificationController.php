<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Services\ReminderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class NotificationController extends Controller
{
    /**
     * Display a listing of all notifications with filters and stats.
     */
    public function index(Request $request)
    {
        abort_if(!Gate::allows('view notifications'), 403, 'Unauthorized access to notifications.');

        $userId = Auth::id();
        $query = AppNotification::where('user_id', $userId);

        // Filter by tab
        $filter = $request->get('filter', 'all');
        if ($filter === 'unread') {
            $query->unread();
        } elseif ($filter === 'reminders') {
            $query->where('type', 'reminder');
        } elseif ($filter === 'tasks') {
            $query->whereIn('type', ['task_deadline', 'task_assigned', 'task_status']);
        } elseif ($filter === 'system') {
            $query->where('type', 'system');
        }

        // Search in title and message
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $notifications = $query->latest()->paginate(20)->withQueryString();

        // Summary counts
        $totalCount     = AppNotification::where('user_id', $userId)->count();
        $unreadCount    = AppNotification::where('user_id', $userId)->unread()->count();
        $remindersCount = AppNotification::where('user_id', $userId)->where('type', 'reminder')->count();
        $tasksCount     = AppNotification::where('user_id', $userId)->whereIn('type', ['task_deadline', 'task_assigned', 'task_status'])->count();

        return view('notifications.index', compact(
            'notifications',
            'totalCount',
            'unreadCount',
            'remindersCount',
            'tasksCount',
            'filter'
        ));
    }

    /**
     * Mark notification as read and redirect to target URL.
     */
    public function readAndRedirect(AppNotification $notification)
    {
        abort_if((int) $notification->user_id !== (int) Auth::id() && !Gate::allows('manage notifications'), 403, 'Unauthorized access to notification.');

        $notification->markAsRead();

        if ($notification->action_url) {
            return redirect($notification->action_url);
        }

        return redirect()->route('notifications.index');
    }

    /**
     * Mark a single notification as read.
     */
    public function markAsRead(Request $request, AppNotification $notification)
    {
        abort_if((int) $notification->user_id !== (int) Auth::id() && !Gate::allows('manage notifications'), 403, 'Unauthorized access to notification.');

        $notification->markAsRead();

        if ($request->wantsJson() || $request->ajax()) {
            $unreadCount = AppNotification::where('user_id', Auth::id())->unread()->count();
            return response()->json([
                'status'       => 'success',
                'message'      => 'Notification marked as read.',
                'unread_count' => $unreadCount,
            ]);
        }

        return back()->with('success', 'Notification marked as read.');
    }

    /**
     * Mark a single notification as unread.
     */
    public function markAsUnread(Request $request, AppNotification $notification)
    {
        abort_if((int) $notification->user_id !== (int) Auth::id() && !Gate::allows('manage notifications'), 403, 'Unauthorized access to notification.');

        $notification->markAsUnread();

        if ($request->wantsJson() || $request->ajax()) {
            $unreadCount = AppNotification::where('user_id', Auth::id())->unread()->count();
            return response()->json([
                'status'       => 'success',
                'message'      => 'Notification marked as unread.',
                'unread_count' => $unreadCount,
            ]);
        }

        return back()->with('success', 'Notification marked as unread.');
    }

    /**
     * Mark all notifications as read for current user.
     */
    public function markAllAsRead(Request $request)
    {
        AppNotification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status'       => 'success',
                'message'      => 'All notifications marked as read.',
                'unread_count' => 0,
            ]);
        }

        return back()->with('success', 'All notifications marked as read.');
    }

    /**
     * Delete a notification.
     */
    public function destroy(Request $request, AppNotification $notification)
    {
        abort_if((int) $notification->user_id !== (int) Auth::id() && !Gate::allows('delete notifications'), 403, 'Unauthorized to delete this notification.');

        $notification->delete();

        if ($request->wantsJson() || $request->ajax()) {
            $unreadCount = AppNotification::where('user_id', Auth::id())->unread()->count();
            return response()->json([
                'status'       => 'success',
                'message'      => 'Notification deleted.',
                'unread_count' => $unreadCount,
            ]);
        }

        return back()->with('success', 'Notification deleted.');
    }

    /**
     * Delete all read notifications.
     */
    public function clearRead(Request $request)
    {
        AppNotification::where('user_id', Auth::id())
            ->whereNotNull('read_at')
            ->delete();

        return back()->with('success', 'All read notifications have been cleared.');
    }

    /**
     * Polling endpoint for real-time due reminders & notifications.
     */
    public function checkDue(Request $request)
    {
        $userId = Auth::id();
        $alerts = ReminderService::processDueReminders($userId);

        $unreadCount = AppNotification::where('user_id', $userId)->unread()->count();
        $recentNotifications = AppNotification::where('user_id', $userId)
            ->latest()
            ->take(8)
            ->get()
            ->map(function (AppNotification $n) {
                return [
                    'id'          => $n->id,
                    'type'        => $n->type,
                    'type_label'  => $n->type_label,
                    'title'       => $n->title,
                    'message'     => $n->message,
                    'action_url'  => route('notifications.read-and-redirect', $n),
                    'icon'        => $n->default_icon,
                    'color'       => $n->type_badge_color,
                    'is_read'     => $n->isRead(),
                    'time_ago'    => $n->created_at->diffForHumans(),
                    'exact_time'  => $n->created_at->format('d M Y, h:i A'),
                ];
            });

        return response()->json([
            'status'        => 'success',
            'unread_count'  => $unreadCount,
            'alerts_count'  => count($alerts),
            'alerts'        => $alerts,
            'notifications' => $recentNotifications,
        ]);
    }
}
