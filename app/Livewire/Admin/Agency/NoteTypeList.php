<?php

namespace App\Livewire\Admin\Agency;

use App\Models\NoteType;
use App\Models\Agency;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class NoteTypeList extends Component
{
    public Agency $agency;
    public $showModal = false;
    public $editingType = null;

    // Form fields
    public $ma_loai = '';
    public $ten_hien_thi = '';
    public $icon = '📝';
    public $mau_sac = 'gray';
    public $thu_tu = 0;

    protected $icons = ['📄', '💰', '👮', '🪑', '🪧', '📝', '📋', '🔧', '⚠️', '📊', '🎯', '💡'];
    protected $colors = [
        'gray' => 'Xám',
        'blue' => 'Xanh dương', 
        'green' => 'Xanh lá',
        'yellow' => 'Vàng',
        'red' => 'Đỏ',
        'purple' => 'Tím',
        'pink' => 'Hồng',
        'orange' => 'Cam',
    ];

    public function mount($agencyId)
    {
        $this->agency = Agency::findOrFail($agencyId);
    }

    public function openAddModal()
    {
        $this->reset(['ma_loai', 'ten_hien_thi', 'icon', 'mau_sac', 'thu_tu']);
        $this->icon = '📝';
        $this->mau_sac = 'gray';
        $this->editingType = null;
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $type = NoteType::findOrFail($id);
        
        if ($type->la_mac_dinh) {
            session()->flash('error', 'Không thể sửa loại mặc định.');
            return;
        }

        $this->editingType = $type;
        $this->ma_loai = $type->ma_loai;
        $this->ten_hien_thi = $type->ten_hien_thi;
        $this->icon = $type->icon;
        $this->mau_sac = $type->mau_sac;
        $this->thu_tu = $type->thu_tu;
        $this->showModal = true;
    }

    public function updatedTenHienThi($value)
    {
        // Auto-generate ma_loai from ten_hien_thi if not editing
        if (!$this->editingType && $value) {
            $this->ma_loai = $this->generateCode($value);
        }
    }

    private function generateCode($text)
    {
        // Convert Vietnamese to ASCII and create slug
        $text = strtolower($text);
        $text = preg_replace('/[àáạảãâầấậẩẫăằắặẳẵ]/u', 'a', $text);
        $text = preg_replace('/[èéẹẻẽêềếệểễ]/u', 'e', $text);
        $text = preg_replace('/[ìíịỉĩ]/u', 'i', $text);
        $text = preg_replace('/[òóọỏõôồốộổỗơờớợởỡ]/u', 'o', $text);
        $text = preg_replace('/[ùúụủũưừứựửữ]/u', 'u', $text);
        $text = preg_replace('/[ỳýỵỷỹ]/u', 'y', $text);
        $text = preg_replace('/đ/u', 'd', $text);
        $text = preg_replace('/[^a-z0-9]+/', '_', $text);
        $text = trim($text, '_');
        return $text;
    }

    public function save()
    {
        $this->validate([
            'ma_loai' => 'required|string|max:50|regex:/^[a-z_]+$/',
            'ten_hien_thi' => 'required|string|max:100',
            'icon' => 'required|string|max:10',
            'mau_sac' => 'required|string',
        ], [
            'ma_loai.regex' => 'Mã loại chỉ được chứa chữ thường và dấu gạch dưới',
        ]);

        $data = [
            'diem_ban_id' => $this->agency->id,
            'ma_loai' => strtolower(trim($this->ma_loai)),
            'ten_hien_thi' => $this->ten_hien_thi,
            'icon' => $this->icon,
            'mau_sac' => $this->mau_sac,
            'thu_tu' => $this->thu_tu ?: 99,
        ];

        if ($this->editingType) {
            $this->editingType->update($data);
            session()->flash('message', 'Cập nhật loại ghi chú thành công.');
        } else {
            NoteType::create($data);
            session()->flash('message', 'Thêm loại ghi chú mới thành công.');
        }

        $this->showModal = false;
    }

    public function delete($id)
    {
        $type = NoteType::findOrFail($id);
        
        if ($type->la_mac_dinh) {
            session()->flash('error', 'Không thể xóa loại mặc định.');
            return;
        }

        if ($type->notes()->count() > 0) {
            session()->flash('error', 'Không thể xóa loại đang được sử dụng.');
            return;
        }

        $type->delete();
        session()->flash('message', 'Xóa loại ghi chú thành công.');
    }

    public function render()
    {
        $types = NoteType::where('diem_ban_id', $this->agency->id)
            ->orderBy('thu_tu')
            ->get();

        return view('livewire.admin.agency.note-type-list', [
            'types' => $types,
            'icons' => $this->icons,
            'colors' => $this->colors,
        ]);
    }
}
