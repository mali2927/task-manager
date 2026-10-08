<?php

namespace App\Livewire\Notifications;

use App\Models\AppNotification;
use App\Models\Workspace;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationBell extends Component
{
    public ?Workspace $workspace = null;
    public bool $isOpen = false;

    public function toggle()
    {
        $this->isOpen = !$this->isOpen;
    }

    public function markAsRead($notificationId)
    {
        $notification = AppNotification::where('user_id', Auth::id())
            ->where('id', $notificationId)
            ->first();

        if ($notification) {
            $notification->markAsRead();
        }
    }

    public function markAllAsRead()
    {
        AppNotification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function render()
    {
        $unreadCount = AppNotification::where('user_id', Auth::id())
            ->unread()
            ->count();

        $notifications = AppNotification::where('user_id', Auth::id())
            ->latest()
            ->take(10)
            ->get();

        return view('livewire.notifications.notification-bell', [
            'unreadCount' => $unreadCount,
            'notifications' => $notifications,
        ]);
    }
}
