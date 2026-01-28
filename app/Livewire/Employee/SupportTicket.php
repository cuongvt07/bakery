<?php

namespace App\Livewire\Employee;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use App\Models\Agency;

#[Layout('components.layouts.mobile')]
class SupportTicket extends Component
{
    public $message = '';
    public $selectedAgencyId = null;
    public $agencies = [];

    public function mount()
    {
        $this->agencies = Agency::all();
        $this->selectedAgencyId = Auth::user()->diem_ban_id; // Default to user's agency

        // If user has no agency, default to the first one in the list to avoid null
        if (!$this->selectedAgencyId && $this->agencies->isNotEmpty()) {
            $this->selectedAgencyId = $this->agencies->first()->id;
        }
    }

    public function submit()
    {
        // Validation removed per user request (no requirement on message field)

        $user = Auth::user();
        $agency = Agency::find($this->selectedAgencyId ?? $user->diem_ban_id);

        // Create ticket in yeu_cau_ca_lam table
        $data = [
            'nguoi_dung_id' => $user->id,
            'loai_yeu_cau' => 'ticket',
            'ca_lam_viec_id' => null,
            'diem_ban_id' => $this->selectedAgencyId ?? $user->diem_ban_id,
            'trang_thai' => 'cho_duyet',
        ];

        $lyDoData = [
            'message' => $this->message,
            'agency_id' => $this->selectedAgencyId ?? $user->diem_ban_id,
            'agency_name' => $agency->ten_diem_ban ?? 'N/A',
        ];

        $data['ly_do'] = json_encode($lyDoData);

        $ticket = \App\Models\YeuCauCaLam::create($data);

        // Send Lark notification to alert Admin about new ticket
        $this->sendTicketNotification($ticket, $lyDoData);

        session()->flash('message', '🚨 Đã gửi ticket khẩn cấp! Chúng tôi sẽ phản hồi sớm nhất.');

        $this->reset(['message']);
    }

    private function sendTicketNotification($ticket, $lyDoData)
    {
        try {
            $user = Auth::user();

            $card = [
                'msg_type' => 'interactive',
                'card' => [
                    'header' => [
                        'title' => [
                            'tag' => 'plain_text',
                            'content' => '🚨 TICKET KHẨN CẤP TỪ ĐIỂM BÁN',
                        ],
                        'template' => 'red',
                    ],
                    'elements' => [
                        [
                            'tag' => 'div',
                            'text' => [
                                'tag' => 'lark_md',
                                'content' => sprintf(
                                    "**🏪 Điểm bán:** %s\n**👤 Nhân viên:** %s\n**🆔 Mã NV:** %s\n\n**📢 Yêu cầu giúp đỡ:**\n%s",
                                    $lyDoData['agency_name'],
                                    $user->ho_ten ?? $user->name,
                                    $user->ma_nhan_vien ?? 'N/A',
                                    $lyDoData['message']
                                ),
                            ],
                        ],
                    ],
                ],
            ];

            \Illuminate\Support\Facades\Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post(
                    'https://open.larksuite.com/open-apis/bot/v2/hook/1f6c319b-bfef-4b9d-a4e4-3972fbdcc4ae',
                    $card
                );
        } catch (\Exception $e) {
            \Log::error('Ticket notification failed: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.employee.support-ticket');
    }
}
