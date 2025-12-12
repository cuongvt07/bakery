<?php

namespace App\Livewire\Admin\Material;

use App\Models\AgencyNote;
use App\Models\Agency;
use App\Models\AgencyLocation;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class MaterialForm extends Component
{
    public $materialId = null;
    public $diem_ban_id = '';
    public $ten_vat_tu = '';
    public $ma_vat_tu = '';
    public $vi_tri_id = '';
    public $mo_ta_vi_tri = '';
    public $dia_diem = '';
    public $mo_ta = '';
    
    // Bulk add mode
    public $bulkMode = false;
    public $bulk_materials = '';

    public function mount($id = null)
    {
        if ($id) {
            $material = AgencyNote::where('loai', 'vat_dung')->findOrFail($id);
            $this->materialId = $material->id;
            $this->diem_ban_id = $material->diem_ban_id;
            $this->ten_vat_tu = $material->tieu_de;
            $this->ma_vat_tu = $material->metadata['ma_vat_dung'] ?? '';
            $this->vi_tri_id = $material->vi_tri_id;
            $this->mo_ta_vi_tri = $material->metadata['mo_ta_vi_tri'] ?? '';
            $this->dia_diem = $material->metadata['dia_diem'] ?? '';
            $this->mo_ta = $material->noi_dung;
        }
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

    public function updatedTenVatTu($value)
    {
        // Auto-generate ma_vat_tu if creating new
        if (!$this->materialId && $value) {
            $this->ma_vat_tu = $this->generateMaterialCode($value);
        }
    }

    private function generateMaterialCode($text)
    {
        // Get first letters of each word
        $words = explode(' ', $text);
        $code = '';
        foreach ($words as $word) {
            if (!empty($word)) {
                $code .= strtoupper(substr($word, 0, 1));
            }
        }
        
        if (empty($code)) {
            $code = strtoupper(substr($text, 0, 2));
        }
        
        // Add counter for uniqueness
        $counter = 1;
        $finalCode = $code . str_pad($counter, 3, '0', STR_PAD_LEFT);
        
        while (AgencyNote::where('diem_ban_id', $this->diem_ban_id)
                         ->where('loai', 'vat_dung')
                         ->where('metadata->ma_vat_dung', $finalCode)
                         ->when($this->materialId, function ($q) {
                             $q->where('id', '!=', $this->materialId);
                         })
                         ->exists()) {
            $counter++;
            $finalCode = $code . str_pad($counter, 3, '0', STR_PAD_LEFT);
        }
        
        return $finalCode;
    }

    public function save()
    {
        $this->validate([
            'diem_ban_id' => 'required|exists:diem_ban,id',
            'ten_vat_tu' => 'required|string|max:200',
            'ma_vat_tu' => 'required|string|max:50',
            'vi_tri_id' => 'required|exists:vi_tri_diem_ban,id',
        ]);

        $data = [
            'diem_ban_id' => $this->diem_ban_id,
            'loai' => 'vat_dung',
            'tieu_de' => $this->ten_vat_tu,
            'noi_dung' => $this->mo_ta,
            'vi_tri_id' => $this->vi_tri_id,
            'metadata' => [
                'ma_vat_dung' => $this->ma_vat_tu,
                'mo_ta_vi_tri' => $this->mo_ta_vi_tri,
                'dia_diem' => $this->dia_diem,
            ],
            'da_xu_ly' => false,
            'muc_do_quan_trong' => 'trung_binh',
            'nguoi_tao_id' => auth()->id(),
        ];

        if ($this->materialId) {
            AgencyNote::find($this->materialId)->update($data);
            session()->flash('message', 'Cập nhật vật tư thành công');
        } else {
            AgencyNote::create($data);
            session()->flash('message', 'Thêm vật tư thành công');
        }

        return redirect()->route('admin.materials.index');
    }
    
    public function saveBulk()
    {
        $this->validate([
            'diem_ban_id' => 'required|exists:diem_ban,id',
            'vi_tri_id' => 'required|exists:vi_tri_diem_ban,id',
            'bulk_materials' => 'required|string',
        ]);
        
        // Split by newlines and filter empty
        $names = array_filter(array_map('trim', explode("\n", $this->bulk_materials)));
        
        if (empty($names)) {
            session()->flash('error', 'Vui lòng nhập ít nhất một tên vật tư');
            return;
        }
        
        $created = 0;
        foreach ($names as $name) {
            if (empty($name)) continue;
            
            $code = $this->generateMaterialCode($name);
            
            AgencyNote::create([
                'diem_ban_id' => $this->diem_ban_id,
                'loai' => 'vat_dung',
                'tieu_de' => $name,
                'noi_dung' => '',
                'vi_tri_id' => $this->vi_tri_id,
                'metadata' => [
                    'ma_vat_dung' => $code,
                    'mo_ta_vi_tri' => $this->mo_ta_vi_tri,
                    'dia_diem' => $this->dia_diem,
                ],
                'da_xu_ly' => false,
                'muc_do_quan_trong' => 'trung_binh',
                'nguoi_tao_id' => auth()->id(),
            ]);
            
            $created++;
        }
        
        session()->flash('message', "Đã thêm {$created} vật tư thành công");
        return redirect()->route('admin.materials.index');
    }

    public function render()
    {
        $agencies = Agency::orderBy('ten_diem_ban')->get();
        $locations = AgencyLocation::query()
            ->when($this->diem_ban_id, function ($q) {
                $q->where('diem_ban_id', $this->diem_ban_id);
            })
            ->orderBy('ma_vi_tri')
            ->get();

        return view('livewire.admin.material.material-form', [
            'agencies' => $agencies,
            'locations' => $locations,
        ]);
    }
}
