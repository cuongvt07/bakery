<?php

namespace App\Livewire\Admin\Notification;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ThongBao;
use App\Models\User;
use App\Models\Agency;
use App\Models\TrangThaiThongBao;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class NotificationManager extends Component
{
    use WithPagination;

    public $activeTab = 'all'; // all, canh_bao, he_thong
    public $showCreateModal = false;

    // Form fields
    public $tieu_de = '';
    public $noi_dung = '';
    public $loai_thong_bao = 'he_thong'; // he_thong, canh_bao
    public $target_type = 'all'; // all, agency, user
    public $target_agency_id = '';
    public $target_user_id = '';

    public function mount()
    {
        //
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->reset(['tieu_de', 'noi_dung', 'loai_thong_bao', 'target_type', 'target_agency_id', 'target_user_id']);
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
    }

    public function sendNotification()
    {
        $this->validate([
            'tieu_de' => 'required|string|max:255',
            'noi_dung' => 'required|string',
            'loai_thong_bao' => 'required|in:thong_tin,he_thong,canh_bao',
            'target_type' => 'required|in:all,agency,user',
            'target_agency_id' => 'required_if:target_type,agency',
            'target_user_id' => 'required_if:target_type,user',
        ], [
            'target_agency_id.required_if' => 'Vui lòng chọn đại lý',
            'target_user_id.required_if' => 'Vui lòng chọn nhân viên',
        ]);

        try {
            DB::beginTransaction();

            $senderId = Auth::id();

            if ($this->target_type === 'all') {
                app(\App\Services\NotificationService::class)->sendToAll(
                    $senderId,
                    $this->tieu_de,
                    $this->noi_dung,
                    $this->loai_thong_bao
                );
            } elseif ($this->target_type === 'agency') {
                app(\App\Services\NotificationService::class)->sendToStore(
                    $this->target_agency_id,
                    $this->tieu_de,
                    $this->noi_dung,
                    $this->loai_thong_bao
                );
            } elseif ($this->target_type === 'user') {
                app(\App\Services\NotificationService::class)->sendToUser(
                    $senderId,
                    $this->target_user_id,
                    $this->tieu_de,
                    $this->noi_dung,
                    $this->loai_thong_bao
                );
            }

            DB::commit();

            // Send to Lark
            $this->sendToLark($this->tieu_de, $this->noi_dung, $this->loai_thong_bao);

            $this->closeCreateModal();
            session()->flash('success', 'Đã gửi thông báo thành công');
            $this->resetPage(); // Refresh list

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Create notification failed: ' . $e->getMessage());
            session()->flash('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    private function sendToLark($title, $content, $type)
    {
        try {
            $webhookUrl = 'https://open.larksuite.com/open-apis/bot/v2/hook/6ce00d25-5ae9-4bd9-8e74-a45b0773cf3b';

            // Format content with sender info
            $adminName = Auth::user()->ho_ten ?? Auth::user()->name ?? 'Admin';
            $fullContent = "**📢 THÔNG BÁO TỪ ADMIN: {$adminName}**\n\n";
            $fullContent .= $content;

            // Determine header color
            $headerColor = $type === 'canh_bao' ? 'yellow' : 'red';
            $headerIcon = $type === 'canh_bao' ? '⚠️' : '📢';

            $card = [
                'msg_type' => 'interactive',
                'card' => [
                    'header' => [
                        'title' => [
                            'tag' => 'plain_text',
                            'content' => "{$headerIcon} {$title}",
                        ],
                        'template' => $headerColor,
                    ],
                    'elements' => [
                        [
                            'tag' => 'div',
                            'text' => [
                                'tag' => 'lark_md',
                                'content' => $fullContent,
                            ],
                        ],
                    ],
                ],
            ];

            \Illuminate\Support\Facades\Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($webhookUrl, $card);

        } catch (\Exception $e) {
            \Log::error('Sending Lark notification failed: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $query = ThongBao::with(['nguoiGui', 'diemBan'])
            ->orderBy('created_at', 'desc');

        if ($this->activeTab === 'canh_bao') {
            // "Cảnh báo" includes tickets & approvals (mapped to 'canh_bao')
            $query->where('loai_thong_bao', 'canh_bao');
        } elseif ($this->activeTab === 'he_thong') {
            $query->where('loai_thong_bao', 'he_thong');
        }
        // 'all' gets everything

        $notifications = $query->paginate(15);

        $agencies = Agency::all();
        $users = User::where('trang_thai', 'hoat_dong')->get();

        return view('livewire.admin.notification.notification-manager', [
            'notifications' => $notifications,
            'agencies' => $agencies,
            'users' => $users,
            
        ]);
    }
}
