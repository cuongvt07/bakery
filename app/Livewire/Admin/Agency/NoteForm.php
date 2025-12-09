<?php

namespace App\Livewire\Admin\Agency;

use App\Models\AgencyNote;
use App\Models\NoteType;
use App\Models\AgencyLocation;
use Livewire\Component;
use Livewire\WithFileUploads;

class NoteForm extends Component
{
    use WithFileUploads;

    public $agencyId;
    public $noteId = null;
    public $loai = '';
    public $tieu_de = '';
    public $noi_dung = '';
    public $muc_do_quan_trong = 'trung_binh';
    public $ngay_nhac_nho = null;
    public $images = [];
    public $existingImages = [];

    // Dynamic metadata fields
    public $metadata = [];
    
    // Item-specific fields
    public $ma_vat_dung = '';
    public $vi_tri_id = '';
    public $mo_ta_vi_tri = '';
    public $dia_diem = '';

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
                
                // Load item-specific fields
                if ($note->loai === 'vat_dung') {
                    $this->ma_vat_dung = $note->metadata['ma_vat_dung'] ?? '';
                    $this->vi_tri_id = $note->vi_tri_id;
                    $this->mo_ta_vi_tri = $note->metadata['mo_ta_vi_tri'] ?? '';
                    $this->dia_diem = $note->metadata['dia_diem'] ?? '';
                }
            }
        } else {
            // Set default type to first available type
            $firstType = NoteType::where('diem_ban_id', $agencyId)
                ->where('hien_thi', true)
                ->orderBy('thu_tu')
                ->first();
            if ($firstType) {
                $this->loai = $firstType->ma_loai;
            }
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

    public function updatedTieuDe($value)
    {
        // Auto-generate ma_vat_dung from tieu_de if type is vat_dung and not editing
        if ($this->loai === 'vat_dung' && !$this->noteId && $value) {
            $this->ma_vat_dung = $this->generateItemCode($value);
        }
    }

    public function removeNewImage($index)
    {
        // Remove image from new uploads array
        if (isset($this->images[$index])) {
            array_splice($this->images, $index, 1);
        }
    }

    public function removeExistingImage($index)
    {
        // Remove image from existing images array
        if (isset($this->existingImages[$index])) {
            array_splice($this->existingImages, $index, 1);
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
        $rules = [
            'tieu_de' => 'required|min:3',
            'loai' => 'required',
            'muc_do_quan_trong' => 'required',
            'images.*' => 'nullable|image|max:2048',
        ];

        // Add item-specific validation
        if ($this->loai === 'vat_dung') {
            $rules['ma_vat_dung'] = 'required|string|max:50';
            $rules['vi_tri_id'] = 'required|exists:vi_tri_diem_ban,id';
        }

        $this->validate($rules);

        // Handle image uploads with compression
        $uploadedPaths = [];
        if (!empty($this->images)) {
            foreach ($this->images as $image) {
                $uploadedPaths[] = $this->processAndSaveImage($image);
            }
        }

        // Merge with existing images
        $allImages = array_merge($this->existingImages, $uploadedPaths);

        // Prepare metadata
        $metadata = $this->metadata;
        if ($this->loai === 'vat_dung') {
            $metadata['ma_vat_dung'] = $this->ma_vat_dung;
            $metadata['mo_ta_vi_tri'] = $this->mo_ta_vi_tri;
            $metadata['dia_diem'] = $this->dia_diem;
        }

        $data = [
            'diem_ban_id' => $this->agencyId,
            'loai' => $this->loai,
            'tieu_de' => $this->tieu_de,
            'noi_dung' => $this->noi_dung,
            'metadata' => $metadata,
            'hinh_anh' => $allImages,
            'ngay_nhac_nho' => $this->ngay_nhac_nho,
            'muc_do_quan_trong' => $this->muc_do_quan_trong,
            'nguoi_tao_id' => auth()->id(),
        ];

        // Add location for items
        if ($this->loai === 'vat_dung') {
            $data['vi_tri_id'] = $this->vi_tri_id;
        }

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

    /**
     * Process and save image with compression
     * Target: <500KB file size
     */
    private function processAndSaveImage($uploadedFile)
    {
        try {
            $img = \Intervention\Image\Facades\Image::make($uploadedFile->getRealPath());
            
            // Resize if too large (max 1920px)
            $img->resize(1920, 1920, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            
            // Encode as JPEG with quality 85
            $img->encode('jpg', 85);
            
            // Check file size, reduce quality if needed
            $quality = 85;
            while ($img->filesize() > 500 * 1024 && $quality > 20) {
                $quality -= 10;
                $img->encode('jpg', $quality);
            }
            
            // Generate unique filename
            $filename = 'agency-notes/' . uniqid() . '_' . time() . '.jpg';
            
            // Save to storage
            \Storage::disk('public')->put($filename, $img->stream());
            
            return $filename;
            
        } catch (\Exception $e) {
            // Fallback: save original file
            \Log::error('Image compression failed: ' . $e->getMessage());
            return $uploadedFile->store('agency-notes', 'public');
        }
    }

    public function render()
    {
        // Load dynamic note types for this agency
        $noteTypes = NoteType::where('diem_ban_id', $this->agencyId)
            ->where('hien_thi', true)
            ->orderBy('thu_tu')
            ->get();

        // Load locations for item selection
        $locations = AgencyLocation::where('diem_ban_id', $this->agencyId)
            ->orderBy('ma_vi_tri')
            ->get();

        return view('livewire.admin.agency.note-form', [
            'noteTypes' => $noteTypes,
            'locations' => $locations,
        ]);
    }
}
