<?php

namespace App\Livewire\Admin\Agency;

use App\Models\Agency;
use App\Models\AgencyNote;
use App\Models\AgencyLocation;
use Livewire\Component;
use Livewire\WithPagination;

class ItemList extends Component
{
    use WithPagination;
    public $agencyId;
    public Agency $agency;
    
    // Form fields
    public $showModal = false;
    public $editingItem = null;
    public $ma_vat_dung = '';
    public $ten_vat_dung = '';
    public $vi_tri_id = '';
    public $mo_ta_vi_tri = '';
    public $dia_diem = '';

    public function mount($agencyId)
    {
        $this->agencyId = $agencyId;
        $this->agency = Agency::findOrFail($agencyId);
    }

    public function openAddModal()
    {
        $this->reset(['ma_vat_dung', 'ten_vat_dung', 'vi_tri_id', 'mo_ta_vi_tri', 'dia_diem', 'editingItem']);
        $this->showModal = true;
    }

    public function openEditModal($itemId)
    {
        $item = AgencyNote::findOrFail($itemId);
        
        $this->editingItem = $item->id;
        $this->ma_vat_dung = $item->metadata['ma_vat_dung'] ?? '';
        $this->ten_vat_dung = $item->tieu_de;
        $this->vi_tri_id = $item->vi_tri_id;
        $this->mo_ta_vi_tri = $item->metadata['mo_ta_vi_tri'] ?? '';
        $this->dia_diem = $item->metadata['dia_diem'] ?? '';
        
        $this->showModal = true;
    }

    public function updatedViTriId($value)
    {
        if ($value) {
            $location = AgencyLocation::find($value);
            if ($location) {
                $this->mo_ta_vi_tri = $location->mo_ta ?? '';
                $this->dia_diem = $location->dia_chi ?? '';
            }
        }
    }

    public function updatedTenVatDung($value)
    {
        // Auto-generate ma_vat_dung from ten_vat_dung if not editing
        if (!$this->editingItem && $value) {
            $this->ma_vat_dung = $this->generateItemCode($value);
        }
    }

    private function generateItemCode($text)
    {
        // Get first letters of each word
        $words = explode(' ', $text);
        $code = '';
        foreach ($words as $word) {
            if (!empty($word)) {
                $code .= strtoupper(substr($word, 0, 1));
            }
        }
        
        // Fallback if no code generated
        if (empty($code)) {
            $code = strtoupper(substr($text, 0, 2));
        }
        
        // Add counter for uniqueness
        $counter = 1;
        $finalCode = $code . str_pad($counter, 3, '0', STR_PAD_LEFT);
        
        while (AgencyNote::where('diem_ban_id', $this->agencyId)
                         ->where('loai', 'vat_dung')
                         ->where('metadata->ma_vat_dung', $finalCode)
                         ->exists()) {
            $counter++;
            $finalCode = $code . str_pad($counter, 3, '0', STR_PAD_LEFT);
        }
        
        return $finalCode;
    }

    public function save()
    {
        $this->validate([
            'ma_vat_dung' => 'required|string|max:50',
            'ten_vat_dung' => 'required|string|max:200',
            'vi_tri_id' => 'required|exists:vi_tri_diem_ban,id',
        ]);

        $data = [
            'diem_ban_id' => $this->agencyId,
            'loai' => 'vat_dung',
            'tieu_de' => $this->ten_vat_dung,
            'vi_tri_id' => $this->vi_tri_id,
            'metadata' => [
                'ma_vat_dung' => $this->ma_vat_dung,
                'mo_ta_vi_tri' => $this->mo_ta_vi_tri,
                'dia_diem' => $this->dia_diem,
            ],
            'da_xu_ly' => false,
            'muc_do_quan_trong' => 'trung_binh',
        ];

        if ($this->editingItem) {
            AgencyNote::find($this->editingItem)->update($data);
            session()->flash('message', 'Cập nhật vật dụng thành công');
        } else {
            AgencyNote::create($data);
            session()->flash('message', 'Thêm vật dụng thành công');
        }

        $this->showModal = false;
        $this->reset(['ma_vat_dung', 'ten_vat_dung', 'vi_tri_id', 'mo_ta_vi_tri', 'dia_diem', 'editingItem']);
    }

    public function delete($itemId)
    {
        AgencyNote::findOrFail($itemId)->delete();
        session()->flash('message', 'Đã xóa vật dụng');
    }

    public function render()
    {
        $items = AgencyNote::where('diem_ban_id', $this->agencyId)
            ->where('loai', 'vat_dung')
            ->with('location')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $locations = AgencyLocation::where('diem_ban_id', $this->agencyId)
            ->orderBy('ma_vi_tri')
            ->get();

        return view('livewire.admin.agency.item-list', [
            'items' => $items,
            'locations' => $locations,
        ]);
    }
}
