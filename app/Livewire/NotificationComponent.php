<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ThongBao;
use App\Models\TrangThaiThongBao;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class NotificationComponent extends Component
{
    public $notifications = [];
    public $unreadCount = 0;

    // UI State
    public $showDropdown = false;
    public $showToast = false;
    public $toastMessage = '';
    public $toastType = 'info'; // info, success, warning, error

    protected $listeners = ['forceRefreshNotification' => 'loadNotifications'];

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        if (!Auth::check()) {
            return;
        }

        $userId = Auth::id();

        // Polling Strategy:
        // Get unread notifications + recent read ones
        // Using "TrangThaiThongBao" as the pivot is better for "da_doc" status

        $query = TrangThaiThongBao::where('nguoi_dung_id', $userId)
            ->with(['thongBao'])
            ->whereHas('thongBao') // Ensure notification still exists
            ->orderBy('da_doc', 'asc') // Unread first
            ->orderByDesc('created_at')
            ->limit(10);

        $items = $query->get();

        // Count TOTAL unread items (not limited by pagination)
        $newUnreadCount = TrangThaiThongBao::where('nguoi_dung_id', $userId)
            ->where('da_doc', false)
            ->count();

        // Detect new unread item to trigger Toast
        if ($newUnreadCount > $this->unreadCount && $this->unreadCount >= 0) {
            // Find the newest unread item
            $newest = $items->first();
            if ($newest && !$newest->da_doc) {
                // Only show toast if user is an employee (or explicitly not admin if that's preferred preference)
                // User requested: "hiển thị bên nhân viên thôi"
                if (Auth::user()->vai_tro === 'nhan_vien') {
                    $this->showToastNotification($newest->thongBao->tieu_de, $newest->thongBao->loai_thong_bao);
                }
            }
        }

        $this->unreadCount = $newUnreadCount;
        $this->notifications = $items;
    }

    // Polling action
    public function poll()
    {
        $this->loadNotifications();
    }

    public $showDetailModal = false;
    public $selectedNotification = null;

    public function toggleDropdown()
    {
        $this->showDropdown = !$this->showDropdown;
    }

    public function markAsRead($statusId)
    {
        $status = TrangThaiThongBao::with('thongBao.nguoiGui')->where('id', $statusId)
            ->where('nguoi_dung_id', Auth::id())
            ->first();

        if ($status) {
            // Mark as read if not already
            if (!$status->da_doc) {
                $status->update([
                    'da_doc' => true,
                    'ngay_doc' => now()
                ]);

                // Refresh list only if status changed
                $this->loadNotifications();
            }

            // Open Detail Modal
            $this->selectedNotification = $status;
            $this->showDetailModal = true;

            // Close dropdown
            $this->showDropdown = false;
        }
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->selectedNotification = null;
    }

    public function markAllAsRead()
    {
        TrangThaiThongBao::where('nguoi_dung_id', Auth::id())
            ->where('da_doc', false)
            ->update([
                'da_doc' => true,
                'ngay_doc' => now()
            ]);

        $this->loadNotifications();
    }

    private function showToastNotification($message, $type = 'thong_tin')
    {
        $this->toastMessage = $message;
        $this->toastType = match ($type) {
            'canh_bao' => 'warning',
            'he_thong' => 'error',
            default => 'info'
        };
        $this->showToast = true;

        // Auto-hide handled by AlpineJS in view
    }

    public function closeToast()
    {
        $this->showToast = false;
    }

    public function render()
    {
        return view('livewire.notification-component');
    }
}
