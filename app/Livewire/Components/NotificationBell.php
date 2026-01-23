<?php

namespace App\Livewire\Components;

use Livewire\Component;
use App\Models\TrangThaiThongBao;
use Illuminate\Support\Facades\Auth;

class NotificationBell extends Component
{
    public $notifications = [];
    public $unreadCount = 0;
    public $isOpen = false;

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $userId = Auth::id();
        if (!$userId)
            return;

        // Get unread count
        $this->unreadCount = TrangThaiThongBao::where('nguoi_dung_id', $userId)
            ->where('da_doc', false)
            ->count();

        // Get recent notifications (top 10 to allow scrolling, user asked for "5 per screen", suggesting a scrollable list)
        $this->notifications = TrangThaiThongBao::with('thongBao')
            ->where('nguoi_dung_id', $userId)
            ->orderByDesc('created_at')
            ->take(20) // Fetching a reasonable amount
            ->get();
    }

    public function toggleUserNotifications()
    {
        $this->isOpen = !$this->isOpen;
        if ($this->isOpen) {
            $this->loadNotifications();
        }
    }

    public function markAsRead($notificationId)
    {
        $notificationStatus = TrangThaiThongBao::where('id', $notificationId)
            ->where('nguoi_dung_id', Auth::id())
            ->first();

        if ($notificationStatus && !$notificationStatus->da_doc) {
            $notificationStatus->update([
                'da_doc' => true,
                'ngay_doc' => now(),
            ]);

            // Reload to update count and list status
            $this->loadNotifications();
        }
    }

    public function markAllAsRead()
    {
        TrangThaiThongBao::where('nguoi_dung_id', Auth::id())
            ->where('da_doc', false)
            ->update([
                'da_doc' => true,
                'ngay_doc' => now(),
            ]);

        $this->loadNotifications();
    }

    public function render()
    {
        return view('livewire.components.notification-bell');
    }
}
