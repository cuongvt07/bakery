<?php

namespace App\Livewire\Admin\Agency;

use App\Models\AgencyNote;
use Livewire\Component;
use Livewire\WithFileUploads;

class NoteForm extends Component
{
    use WithFileUploads;

    public $agencyId;
    public $noteId = null;
    public $loai = 'khac';
    public $tieu_de = '';
    public $noi_dung = '';
    public $muc_do_quan_trong = 'trung_binh';
    public $ngay_nhac_nho = null;
    public $images = [];
    public $existingImages = [];

    // Dynamic metadata fields
    public $metadata = [];

    public function mount($agencyId, $noteId = null)
    {
        $this->agencyId = $agencyId;

        if ($noteId) {
            $note = AgencyNote::find($noteId);
            if ($note && $note->diem_ban_id == $agencyId) {
                $this->noteId = $note->id;
                $this->loai = $note->loai;
                $this->tieu_de = $note->tieu_de;
                $this->noi_dung = $note->noi_dung;
                $this->muc_do_quan_trong = $note->muc_do_quan_trong;
                $this->ngay_nhac_nho = $note->ngay_nhac_nho?->format('Y-m-d');
                $this->metadata = $note->metadata ?? [];
                $this->existingImages = $note->hinh_anh ?? [];
            }
        }
    }

    public function save()
    {
        $this->validate([
            'tieu_de' => 'required|min:3',
            'loai' => 'required',
            'muc_do_quan_trong' => 'required',
            'images.*' => 'nullable|image|max:2048',
        ]);

        // Handle image uploads
        $uploadedPaths = [];
        foreach ($this->images as $image) {
            $path = $image->store('agency-notes', 'public');
            $uploadedPaths[] = $path;
        }

        // Merge with existing images
        $allImages = array_merge($this->existingImages, $uploadedPaths);

        $data = [
            'diem_ban_id' => $this->agencyId,
            'loai' => $this->loai,
            'tieu_de' => $this->tieu_de,
            'noi_dung' => $this->noi_dung,
            'metadata' => $this->metadata,
            'hinh_anh' => $allImages,
            'ngay_nhac_nho' => $this->ngay_nhac_nho,
            'muc_do_quan_trong' => $this->muc_do_quan_trong,
            'nguoi_tao_id' => auth()->id(),
        ];

        if ($this->noteId) {
            AgencyNote::find($this->noteId)->update($data);
            session()->flash('success', 'Cập nhật ghi chú thành công');
        } else {
            AgencyNote::create($data);
            session()->flash('success', 'Thêm ghi chú thành công');
        }

        return redirect()->route('admin.agencies.detail', $this->agencyId);
    }

    public function removeImage($index)
    {
        unset($this->existingImages[$index]);
        $this->existingImages = array_values($this->existingImages);
    }

    public function render()
    {
        return view('livewire.admin.agency.note-form');
    }
}
