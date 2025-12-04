<?php

namespace App\Livewire\Admin\Production;

use App\Models\ProductionBatch;
use App\Models\ProductionBatchDetail;
use App\Models\Recipe;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

#[Layout('components.layouts.app')]
class ProductionBatchForm extends Component
{
    use WithFileUploads;

    public ?ProductionBatch $batch = null;
    public $ma_me = '';
    public $ngay_san_xuat;
    public $buoi = 'sang';
    public $han_su_dung;
    public $trang_thai = 'ke_hoach';

    // Products in this batch
    public $products = []; // [[cong_thuc_id, so_luong_du_kien]]
    
    // QC fields
    public $qcData = []; // [detail_id => failed_qty]
    public $ghi_chu_qc = '';
    public $qcImages = [];
    public $existingImages = [];

    public function mount($id = null)
    {
        $this->ngay_san_xuat = Carbon::today()->format('Y-m-d');
        $this->han_su_dung = Carbon::today()->addDays(3)->format('Y-m-d');

        if ($id) {
            $this->batch = ProductionBatch::with(['details.recipe.product', 'details.product'])->findOrFail($id);
            $this->ma_me = $this->batch->ma_me;
            $this->ngay_san_xuat = $this->batch->ngay_san_xuat->format('Y-m-d');
            $this->buoi = $this->batch->buoi;
            $this->han_su_dung = $this->batch->han_su_dung?->format('Y-m-d');
            $this->trang_thai = $this->batch->trang_thai;
            $this->ghi_chu_qc = $this->batch->ghi_chu_qc ?? '';
            $this->existingImages = $this->batch->anh_qc ?? [];
            
            // Load products from details
            foreach ($this->batch->details as $detail) {
                $this->products[] = [
                    'detail_id' => $detail->id,
                    'cong_thuc_id' => $detail->cong_thuc_id,
                    'so_luong_du_kien' => $detail->so_luong_du_kien,
                    'so_luong_that_bai' => $detail->so_luong_that_bai,
                    'so_luong_thuc_te' => $detail->so_luong_thuc_te,
                ];
                
                $this->qcData[$detail->id] = $detail->so_luong_that_bai;
            }
        } else {
            // Start with one empty product slot
            $this->products = [['cong_thuc_id' => '', 'so_luong_du_kien' => 100]];
            // Auto-generate batch code
            $this->generateBatchCode();
        }
    }

    public function generateBatchCode()
    {
        $date = Carbon::parse($this->ngay_san_xuat);
        $buoiMap = [
            'sang' => 'SANG',
            'trua' => 'TRUA',
            'chieu' => 'CHIEU',
        ];
        
        $prefix = $buoiMap[$this->buoi] ?? 'SANG';
        $dateStr = $date->format('Ymd');
        
        // Count existing batches for this day and session
        $count = ProductionBatch::whereDate('ngay_san_xuat', $this->ngay_san_xuat)
                               ->where('buoi', $this->buoi)
                               ->count();
        
        $this->ma_me = $prefix . '-' . $dateStr . '-' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);
    }

    public function updatedBuoi()
    {
        if (!$this->batch) {
            $this->generateBatchCode();
        }
    }

    public function updatedNgaySanXuat()
    {
        if (!$this->batch) {
            $this->generateBatchCode();
        }
    }

    public function addProduct()
    {
        $this->products[] = ['cong_thuc_id' => '', 'so_luong_du_kien' => 100];
    }

    public function removeProduct($index)
    {
        unset($this->products[$index]);
        $this->products = array_values($this->products); // Re-index
    }

    public function save()
    {
        $this->validate([
            'ma_me' => 'required|unique:me_san_xuat,ma_me,' . ($this->batch->id ?? 'NULL'),
            'ngay_san_xuat' => 'required|date',
            'products' => 'required|array|min:1',
            'products.*.cong_thuc_id' => 'required|exists:cong_thuc_san_xuat,id',
            'products.*.so_luong_du_kien' => 'required|integer|min:1',
        ]);

        DB::transaction(function () {
            $data = [
                'ma_me' => $this->ma_me,
                'nguoi_tao_id' => Auth::id(),
                'ngay_san_xuat' => $this->ngay_san_xuat,
                'buoi' => $this->buoi,
                'han_su_dung' => $this->han_su_dung,
                'trang_thai' => $this->trang_thai,
            ];

            if ($this->batch) {
                $this->batch->update($data);
                // Delete old details
                $this->batch->details()->delete();
            } else {
                $this->batch = ProductionBatch::create($data);
            }

            // Create details for each product
            foreach ($this->products as $product) {
                $recipe = Recipe::find($product['cong_thuc_id']);
                
                ProductionBatchDetail::create([
                    'me_san_xuat_id' => $this->batch->id,
                    'cong_thuc_id' => $product['cong_thuc_id'],
                    'san_pham_id' => $recipe->san_pham_id,
                    'so_luong_du_kien' => $product['so_luong_du_kien'],
                ]);
            }
        });

        session()->flash('message', 'Lưu mẻ sản xuất thành công.');
        return redirect()->route('admin.production-batches.index');
    }

    public function confirmQC()
    {
        $this->validate([
            'qcData.*' => 'required|integer|min:0',
            'qcImages.*' => 'nullable|image|max:2048',
        ]);

        // Upload QC images
        $imagePaths = $this->existingImages;
        if (!empty($this->qcImages)) {
            foreach ($this->qcImages as $image) {
                $path = $image->store('production-qc', 'public');
                $imagePaths[] = $path;
            }
        }

        $this->batch->confirmQC($this->qcData, $imagePaths, $this->ghi_chu_qc);
        $this->batch->nguoi_qc_id = Auth::id();
        $this->batch->save();

        session()->flash('message', 'Xác nhận QC thành công. Mẻ sản xuất đã hoàn thành.');
        return redirect()->route('admin.production-batches.index');
    }

    public function render()
    {
        $recipes = Recipe::where('trang_thai', 'hoat_dong')
                        ->with('product')
                        ->orderBy('ten_cong_thuc')
                        ->get();

        return view('livewire.admin.production.production-batch-form', [
            'recipes' => $recipes,
        ]);
    }
}
