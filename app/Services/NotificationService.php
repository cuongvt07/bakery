<?php

namespace App\Services;

use App\Models\ThongBao;
use App\Models\TrangThaiThongBao;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NotificationService
{
    /**
     * Send notification to a specific user
     */
    public static function sendToUser($senderId, $receiverId, $title, $content, $type = 'thong_tin', $agencyId = null)
    {
        return DB::transaction(function () use ($senderId, $receiverId, $title, $content, $type, $agencyId) {
            // 1. Create Notification
            $notification = ThongBao::create([
                'tieu_de' => $title,
                'noi_dung' => $content,
                'loai_thong_bao' => $type, // 'he_thong', 'canh_bao', 'thong_tin'
                'gui_toi_tat_ca' => false,
                'diem_ban_id' => $agencyId,
                'nguoi_nhan_id' => $receiverId,
                'nguoi_gui_id' => $senderId,
                'ngay_gui' => now(),
            ]);

            // 2. Create Status Record for Receiver
            TrangThaiThongBao::create([
                'thong_bao_id' => $notification->id,
                'nguoi_dung_id' => $receiverId,
                'da_doc' => false,
            ]);

            return $notification;
        });
    }

    /**
     * Send notification to all users
     */
    public static function sendToAll($senderId, $title, $content, $type = 'thong_tin')
    {
        return DB::transaction(function () use ($senderId, $title, $content, $type) {
            // 1. Create Notification
            $notification = ThongBao::create([
                'tieu_de' => $title,
                'noi_dung' => $content,
                'loai_thong_bao' => $type,
                'gui_toi_tat_ca' => true,
                'nguoi_gui_id' => $senderId,
                'ngay_gui' => now(),
            ]);

            // Note: For "Send to All", we might not create individual records immediately 
            // to save performance, or we can create them via a Job.
            // For now, let's create them for active users to ensure consistency.

            $activeUserIds = User::where('trang_thai', 'hoat_dong')->pluck('id');

            $statusRecords = [];
            foreach ($activeUserIds as $userId) {
                $statusRecords[] = [
                    'thong_bao_id' => $notification->id,
                    'nguoi_dung_id' => $userId,
                    'da_doc' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Bulk insert
            TrangThaiThongBao::insert($statusRecords);

            return $notification;
        });
    }

    /**
     * Send notification to all users in a specific store (Agency)
     */
    public static function sendToStore($agencyId, $title, $content, $type = 'thong_tin')
    {
        // System sender ID or null (if no specific sender)
        $senderId = \Illuminate\Support\Facades\Auth::id();

        return DB::transaction(function () use ($senderId, $agencyId, $title, $content, $type) {
            // 1. Create Notification
            $notification = ThongBao::create([
                'tieu_de' => $title,
                'noi_dung' => $content,
                'loai_thong_bao' => $type,
                'gui_toi_tat_ca' => false, // Not to ALL stores, just one
                'diem_ban_id' => $agencyId,
                'nguoi_gui_id' => $senderId,
                'ngay_gui' => now(),
            ]);

            // 2. Find Users in Store
            $userIds = User::where('diem_ban_id', $agencyId)
                ->where('trang_thai', 'hoat_dong')
                ->pluck('id');

            $statusRecords = [];
            foreach ($userIds as $userId) {
                $statusRecords[] = [
                    'thong_bao_id' => $notification->id,
                    'nguoi_dung_id' => $userId,
                    'da_doc' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (!empty($statusRecords)) {
                TrangThaiThongBao::insert($statusRecords);
            }

            return $notification;
        });
    }
}
