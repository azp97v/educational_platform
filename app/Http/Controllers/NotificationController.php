<?php

namespace App\Http\Controllers;

use App\Support\TextEncodingNormalizer;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->latest()->paginate(6);
        $unreadCount = $user->unreadNotifications()->count();

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    public function fetch()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->latest()->take(6)->get();
        $unreadCount = $user->unreadNotifications()->count();

        $iconMap = [
            'enrollment' => 'ri-user-add-line',
            'inquiry' => 'ri-question-line',
            'message' => 'ri-message-2-line',
            'chat' => 'ri-message-2-line',
            'question' => 'ri-chat-3-line',
            'answer' => 'ri-chat-3-line',
            'support' => 'ri-headset-line',
            'announcement' => 'ri-notification-3-line',
            'alert' => 'ri-alert-line',
            'system' => 'ri-notification-3-line',
            'course' => 'ri-book-open-line',
            'lesson' => 'ri-book-2-line',
            'exam' => 'ri-survey-line',
            'achievement' => 'ri-medal-line',
            'attendance' => 'ri-checkbox-circle-line',
            'payment' => 'ri-wallet-line',
            'general' => 'ri-notification-3-line',
        ];

        return response()->json([
            'success' => true,
            'unread_count' => $unreadCount,
            'latest_id' => $notifications->first()?->id ?? 0,
            'notifications' => $notifications->map(function (DatabaseNotification $notification) use ($iconMap) {
                $category = strtolower($notification->data['category'] ?? $notification->data['type'] ?? 'general');
                $icon = trim($notification->data['icon'] ?? '');
                if (empty($icon) || !str_starts_with($icon, 'ri-')) {
                    $icon = $iconMap[$category] ?? $iconMap['general'];
                }

                return [
                    'id' => $notification->id,
                    'title' => TextEncodingNormalizer::normalizeString((string) ($notification->data['title'] ?? 'إشعار جديد')) ?? 'إشعار جديد',
                    'message' => TextEncodingNormalizer::normalizeString((string) ($notification->data['message'] ?? '')) ?? '',
                    'url' => route('notifications.goto', $notification->id),
                    'category' => $category,
                    'icon' => $icon,
                    'read_at' => $notification->read_at ? true : false,
                    'created_at' => $notification->created_at->diffForHumans(),
                ];
            })->toArray(),
        ]);
    }

    public function redirect(DatabaseNotification $notification)
    {
        $user = Auth::user();

        if ($notification->notifiable_id !== $user->id || $notification->notifiable_type !== get_class($user)) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا الإشعار');
        }

        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        return redirect($notification->data['url'] ?? route('notifications.index'));
    }

    public function markAllAsRead()
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();

        return back()->with('success', 'تم تمييز جميع الإشعارات كمقروءة بنجاح');
    }

    public function updatePreferences(Request $request)
    {
        $validated = $request->validate([
            'notify_enrollment' => 'nullable|boolean',
            'notify_inquiry' => 'nullable|boolean',
            'notify_message' => 'nullable|boolean',
            'notify_system' => 'nullable|boolean',
        ]);

        $user = Auth::user();
        $user->update([
            'notify_enrollment' => $request->has('notify_enrollment'),
            'notify_inquiry' => $request->has('notify_inquiry'),
            'notify_message' => $request->has('notify_message'),
            'notify_system' => $request->has('notify_system'),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم حفظ تفضيلات الإشعارات بنجاح',
            ]);
        }

        return back()->with('success', 'تم حفظ تفضيلات الإشعارات بنجاح');
    }
}
