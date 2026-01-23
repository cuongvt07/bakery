<?php

namespace App\Services;

use App\Models\ThongBao;
use App\Models\TrangThaiThongBao;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\DB;

class NotificationService
{
    /**
     * Send a notification to a specific user.
     *
     * @param int $userId The ID of the user receiving the notification.
     * @param string $title The title of the notification.
     * @param string $content The content of the notification.
     * @param string $type The type of notification (e.g., 'he_thong', 'canh_bao', 'thong_tin').
     * @return ThongBao
     */
    public function sendToUser($userId, $title, $content, $type = 'thong_tin')
    {
        try {
            return DB::transaction(function () use ($userId, $title, $content, $type) {
                // Determine sender (current user or system/admin placeholder if needed)
                // For now assuming Auth::id() is available, otherwise handling could be improved
                $senderId = Auth::id() ?? 1; // Fallback to ID 1 if system action

                // Create the notification record
                $notification = ThongBao::create([
                    'tieu_de' => $title,
                    'noi_dung' => $content,
                    'loai_thong_bao' => $type,
                    'gui_toi_tat_ca' => false,
                    'nguoi_nhan_id' => $userId,
                    'nguoi_gui_id' => $senderId,
                    'ngay_gui' => now(),
                ]);

                // Create the read status record for the user
                TrangThaiThongBao::create([
                    'thong_bao_id' => $notification->id,
                    'nguoi_dung_id' => $userId,
                    'da_doc' => false,
                ]);

                return $notification;
            });
        } catch (Exception $e) {
            // Log error or rethrow
            \Log::error('Failed to send notification: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Send a notification to all users in a specific store.
     *
     * @param int $storeId The ID of the store (diem_ban).
     * @param string $title The title of the notification.
     * @param string $content The content of the notification.
     * @param string $type The type of notification (e.g., 'he_thong', 'canh_bao', 'thong_tin').
     * @return ThongBao
     */
    public function sendToStore($storeId, $title, $content, $type = 'thong_tin')
    {
        try {
            return DB::transaction(function () use ($storeId, $title, $content, $type) {
                $senderId = Auth::id() ?? 1;

                // Create the notification record
                $notification = ThongBao::create([
                    'tieu_de' => $title,
                    'noi_dung' => $content,
                    'loai_thong_bao' => $type,
                    'gui_toi_tat_ca' => false, // false because it's limited to a store, not GLOBAL
                    'diem_ban_id' => $storeId,
                    'nguoi_gui_id' => $senderId,
                    'ngay_gui' => now(),
                ]);

                // Find all active users in the store
                // Assuming NguoiDung has diem_ban_id
                $users = \App\Models\User::where('diem_ban_id', $storeId)
                    ->where('trang_thai', 'hoat_dong') // Assuming 'active' status check if exists
                    ->get();

                // If model is NguoiDung, use NguoiDung
                if ($users->isEmpty()) {
                    $users = \App\Models\NguoiDung::where('diem_ban_id', $storeId)
                        ->where('trang_thai', 'hoat_dong')
                        ->get();
                }

                // Bulk insert read status
                $statusData = [];
                foreach ($users as $user) {
                    $statusData[] = [
                        'thong_bao_id' => $notification->id,
                        'nguoi_dung_id' => $user->id,
                        'da_doc' => false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                if (!empty($statusData)) {
                    TrangThaiThongBao::insert($statusData);
                }

                return $notification;
            });
        } catch (Exception $e) {
            \Log::error('Failed to send store notification: ' . $e->getMessage());
            throw $e;
        }
    }
}
